<?php
/**
 * 2007-2019 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2019 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if(!class_exists('LC_Conversation') && file_exists(dirname(__FILE__).'/../../classes/LC_Conversation.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Conversation.php');
if(!class_exists('LC_Message') && file_exists(dirname(__FILE__).'/../../classes/LC_Message.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Message.php');
if(!class_exists('LC_Download') && file_exists(dirname(__FILE__).'/../../classes/LC_Download.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Download.php');
class Ets_livechatAjaxModuleFrontController extends ModuleFrontController
{
    /**
    * @see FrontController::initContent()
    */
    public function initContent()
    {
        parent::initContent();
        $errors = array();
        if(Tools::getValue('token')!=md5($this->module->id))
            return '';  
        if($conversation = LC_Conversation::getCustomerConversation())
            $this->module->checkAutoEndChat($conversation);
        if(Tools::isSubmit('set_chatbox_position'))
        {
            $this->context->cookie->lc_chatbox_top = Tools::getValue('top');
            $this->context->cookie->lc_chatbox_left = Tools::getValue('left');
            $this->context->cookie->write();
            die('updated');
        }
        if(Tools::isSubmit('customer_edit_message')&& $id_message=(int)Tools::getValue('id_message'))
        {
            $message= new LC_Message($id_message);
            if($message->id_employee|| !$message->id|| !(int)Configuration::get('ETS_LC_ENABLE_EDIT_MESSAGE'))
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'error'=> $this->module->l('error','ajax'),
                        ) 
                    )
                );
            }
            else
            {
                $conversation = new LC_Conversation($message->id_conversation);
                $conversation->latest_online = date('Y-m-d H:i:s');
                if($conversation->end_chat && !$errors)
                {
                    if(!$this->module->duplicateConversation($conversation))
                        $errors[] = $this->module->l('Sorry! We are not able to start the conversation at this time','ajax');
                }
                else
                    $conversation->update();
                die(
                    Tools::jsonEncode(
                        array(
                            'error'=> 0,
                            'message'=>$message->message,
                        )
                    )
                    
                );
            }
        }
        if(Tools::isSubmit('customer_delete_message')&& $id_message=(int)Tools::getValue('id_message'))
        {
            $message= new LC_Message($id_message);
            if($message->id_employee|| !$message->id || !(int)Configuration::get('ETS_LC_ENABLE_DELETE_MESSAGE'))
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'error'=> $this->module->l('error','ajax'),
                        )
                    )
                );
            }
            else
            {
                $conversation = new LC_Conversation($message->id_conversation);
                if($conversation->message_deleted)
                    $conversation->message_deleted .=','.$id_message;
                else
                    $conversation->message_deleted=$id_message;
                $conversation->latest_online = date('Y-m-d H:i:s');
                if($conversation->end_chat && !$errors)
                {
                    if(!$this->module->duplicateConversation($conversation))
                        $errors[] = $this->module->l('Sorry! We are not able to start the conversation at this time','ajax');
                }
                else
                    $conversation->update();
                $message->delete();
                die(
                    Tools::jsonEncode(
                        array(
                            'error'=> 0,
                        )
                    )
                );
            }
        }
        if(Tools::isSubmit('change_sound_conversation'))
        {
            if($conversation = LC_Conversation::getCustomerConversation())
            {
                $conversation->enable_sound=(int)Tools::getValue('status');
                $conversation->update();
            }
            Context::getContext()->cookie->enable_sound =(int)Tools::getValue('status');
            Context::getContext()->cookie->write();
            die('1');
        }
        if(Tools::isSubmit('load_chat_box'))
        {
            $conversation = LC_Conversation::getCustomerConversation();
            die(Tools::jsonEncode(array(
                'has_conversation' =>(int)Tools::getValue('refresh')?0:1,
                'html' => $this->module->displayChatBoxCustomer((int)Tools::getValue('refresh')),
                'isAdminBusy' => Ets_livechat::isAdminBusy(),   
                'wait_support' => $conversation && $this->module->checkWaitSupport($conversation->id) ? Ets_livechat::getTimeWait():false,
            )));
        }
        if(Tools::isSubmit('send_message'))
        {
            $extraID=0;
            //if(!$this->context->cookie->lc_siteloaded)
             //   $errors[]= $this->module->l('Authority failed','ajax');   
            $openInfo = false;
            $ignoreCaptcha = false;
            if(!Ets_livechat::needCaptcha())
                $ignoreCaptcha = true;
            $isAdminOnline = Ets_livechat::isAdminOnline(); 
            $message = trim(Tools::getValue('message')); 
            Configuration::updateValue('ETS_LC_DATE_ACTION_LAST',date('Y-m-d H:i:s'));        
            if(!($conversation = LC_Conversation::getCustomerConversation()) || (int)Tools::getValue('updateCustomerInfo',0) && $conversation && (int)Configuration::get('ETS_LC_UPDATE_CONTACT_INFO'))
            {
                if(!$conversation)
                    $conversation = new LC_Conversation();
                if(!$conversation->chatref)
                    $conversation->chatref =1+(int)Db::getInstance()->getValue('SELECT MAX(id_conversation) FROM '._DB_PREFIX_.'ets_livechat_conversation');
                if($this->module->checkChangeDepartment($conversation))
                {
                    if($conversation->id_departments!=Tools::getValue('id_departments'))
                    {
                        $conversation->id_employee=0;
                        $conversation->id_employee_wait=0;
                        $conversation->id_departments_wait=0;
                    }
                    $conversation->id_departments = (int)Tools::getValue('id_departments');
                    if(!$conversation->id_departments && LC_Conversation::isRequiredField('departments'))
                        $errors[] = $this->module->l('Department is required','ajax');
                }
                if(isset($this->context->customer->id) && (int)$this->context->customer->id)
                {
                    $conversation->customer_name = trim(Tools::ucfirst($this->context->customer->firstname).' '.Tools::ucfirst($this->context->customer->lastname));
                    $conversation->customer_email = $this->context->customer->email;
                    $conversation->customer_phone = ($addresses = $this->context->customer->getAddresses($this->context->language->id)) ? ($addresses[0]['phone'] ? $addresses[0]['phone'] : $addresses[0]['phone_mobile']) : '';
                    
                    if(!$conversation->customer_phone && LC_Conversation::isUsedField('name') && ($phone = trim(Tools::getValue('phone'))) && Validate::isPhoneNumber($phone))
                        $conversation->customer_phone = $phone;
                    if(!$conversation->customer_phone && LC_Conversation::isRequiredField('phone'))
                        $errors[] = $this->module->l('Please enter a valid phone number','ajax');
                    
                    $conversation->id_customer = (int)$this->context->customer->id;
                }
                else
                {
                    if(LC_Conversation::isUsedField('name'))
                    {
                        $name = trim(Tools::getValue('name'));
                        if(LC_Conversation::isRequiredField('name') && !$name)
                            $errors[] = $this->module->l('Name is required','ajax');
                        elseif(!Validate::isName($name))
                            $errors[] = $this->module->l('Name is not valid','ajax');
                        else
                            $conversation->customer_name = $name;
                    }
                    if(LC_Conversation::isUsedField('email') || !$isAdminOnline)
                    {
                        $email = trim(Tools::getValue('email'));
                        if(!$email && (!$isAdminOnline || LC_Conversation::isRequiredField('email')))
                            $errors[] = $this->module->l('Email is required');
                        elseif($email && !Validate::isEmail($email))
                            $errors[] = $this->module->l('Email is not valid');
                        elseif(LC_Conversation::isUsedField('email') || !$isAdminOnline)    
                            $conversation->customer_email = $email;
                    }                    
                    if(LC_Conversation::isUsedField('phone'))
                    {
                        $phone = trim(Tools::getValue('phone'));
                        if(LC_Conversation::isRequiredField('phone') && !$phone)
                            $errors[] = $this->module->l('Phone is required','ajax');
                        elseif(!Validate::isPhoneNumber($phone))
                            $errors[] = $this->module->l('Phone is not valid','ajax');
                        else
                            $conversation->customer_phone = $phone;
                    }
                    $conversation->id_customer = 0;
                }
                if(!$ignoreCaptcha && !Ets_livechat::validCaptcha())
                {
                    if(Tools::getValue('captcha')!='')
                        $errors[] = $this->module->l('Security code is not valid','ajax');
                    else
                        $errors[]=$this->module->l('Please enter security code','ajax');
                    $ignoreCaptcha = true;
                }                                   
                if(!$errors)
                {
                    $conversation->blocked = 0;
                    $conversation->customer_writing = 0;
                    $conversation->employee_writing = 0;
                    $conversation->latest_online = date('Y-m-d H:i:s');
                    $conversation->latest_ip = Tools::getRemoteAddr();
                    $conversation->browser_name = Tools::getValue('browser_name');
                    $current_url = Tools::getValue('current_url');
                    if (strpos($current_url, '#') !== FALSE) {
                        $current_url = Tools::substr($current_url, 0, strpos($current_url, '#'));
                    }
                    $conversation->current_url = $current_url;
                    $conversation->datetime_added = $conversation->latest_online;
                    $conversation->id_shop = Context::getContext()->shop->id;
                    if($conversation->end_chat)
                    {
                        $conversation->end_chat=0;
                        $conversation->id_employee=0;
                        $conversation->id_employee_wait=0;
                        $conversation->id_departments_wait=0;
                        $conversation->id=0;
                    }
                    if(isset(Context::getContext()->cookie->enable_sound) && !Context::getContext()->cookie->enable_sound)
                        $conversation->enable_sound=0;
                    if(!$conversation || ($conversation && !$conversation->id))
                    {
                        $this->context->cookie->ets_lc_chatbox_status='open';
                        $this->context->cookie->write();
                        if(!$conversation->add())
                            $errors[] = $this->module->l('Sorry! We are not able to start the conversation at this time','ajax');
                        else
                        {
                            $this->context->cookie->lc_id_conversation = $conversation->id;
                            $this->context->cookie->write();
                        }
                    }
                    elseif(!$conversation->update())
                        $errors[] = $this->module->l('Sorry! We are not able to update our contact infomation at this time','ajax');
                }                                    
            }
            elseif($conversation = LC_Conversation::getCustomerConversation())
            {
                if(!$conversation->chatref)
                    $conversation->chatref =1+(int)Db::getInstance()->getValue('SELECT MAX(id_conversation) FROM '._DB_PREFIX_.'ets_livechat_conversation');
                if($this->module->checkChangeDepartment($conversation))
                {
                    if($conversation->id_departments!=Tools::getValue('id_departments'))
                    {
                        $conversation->id_employee=0;
                        $conversation->id_employee_wait=0;
                        $conversation->id_departments_wait=0;
                    }
                    $conversation->id_departments = (int)Tools::getValue('id_departments');
                    if(!$conversation->id_departments && LC_Conversation::isRequiredField('departments'))
                        $errors[] = $this->module->l('Department is required','ajax');
                    else
                    {
                        if($conversation->end_chat&& !$errors)
                        {
                            if(!$this->module->duplicateConversation($conversation))
                                $errors[] = $this->module->l('Sorry! We are not able to start the conversation at this time','ajax');
                            
                        }
                        else
                            $conversation->update();
                    }
                        
                }
            }
            if(!Configuration::get('ETS_LC_UPDATE_CONTACT_INFO') && (int)Tools::getValue('updateCustomerInfo',0))
                $errors[] = $this->module->l('You are not allow to update your contact information','ajax');
            if(!$errors && (!$conversation || $conversation && !$conversation->id))
            {
                $this->context->cookie->lc_id_conversation = 0;
                $this->context->cookie->write();
                $errors[] = $this->module->l('This conversation does not exist','ajax');
            }
            if($conversation->end_chat && !$errors)
            {
                if(!$this->module->duplicateConversation($conversation))
                    $errors[] = $this->module->l('Sorry! We are not able to start the conversation at this time','ajax');
            }
            if($errors)
                $openInfo = true;
            if(!(int)Tools::getValue('updateCustomerInfo',0) && !($message = trim(Tools::getValue('message'))) && (!isset($_FILES['message_file']) || !$_FILES['message_file']))
                $errors[] = $this->module->l('Message can not be empty','ajax');
            elseif(!(int)Tools::getValue('updateCustomerInfo',0) && $message && !Validate::isCleanHtml($message,false))
                $errors[] = $this->module->l('Message is not valid','ajax');
            elseif(Tools::strlen($message)> (int)Configuration::get('ETS_LC_MSG_LENGTH'))
                $errors[]= $this->module->l('Message is invalid','ajax');
            if(!(isset($_FILES['message_file']['tmp_name'])&& isset($_FILES['message_file']['name']) && $_FILES['message_file']['name']) && trim(strip_tags($message))=='' && $message!='' && !$errors)
            {
                $errors[]= $this->module->l('Message is invalid','ajax');
            }
            if(!$errors && !(int)Tools::getValue('updateCustomerInfo',0))
            {
                if(!$ignoreCaptcha && !Ets_livechat::validCaptcha())
                {
                    if(Tools::getValue('captcha')!='')
                        $errors[] = $this->module->l('Security code is not valid','ajax');
                    else
                        $errors[]=$this->module->l('Please enter security code','ajax');
                }
                if($conversation->blocked && !$conversation->end_chat)
                    $errors[] = $this->module->l('You are temporarily blocked by administrator','ajax');
                if(!$errors)
                {
                    if($id_message=(int)Tools::getValue('id_message'))
                    {
                        $msg = new LC_Message($id_message);
                        $msg->datetime_edited = date('Y-m-d H:i:s');
                    }  
                    else
                    {
                        $msg = new LC_Message();
                        $msg->datetime_added = date('Y-m-d H:i:s');
                        $msg->datetime_edited = $msg->datetime_added;
                    }
                    $msg->id_employee = 0;                    
                    $msg->id_conversation = $conversation->id;
                    $msg->message = Tools::nl2br(trim(strip_tags($message)));
                    $msg->delivered = 0;
                    $msg->id_product=(int)Tools::getValue('send_product_id');
                    $attachments=array();
                    if(isset($_FILES['message_file']['tmp_name'])&& isset($_FILES['message_file']['name']) && $_FILES['message_file']['name'])
                    {
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['message_file']['name'], '.'), 1));
                        if(!in_array($type,$this->module->file_types))
                        {
                            $errors[] = $this->module->l('File upload is invalid');
                        }
                        else
                        {
                            $fileupload_name = sha1(microtime()).'-'.$_FILES['message_file']['name'];
                            if (!move_uploaded_file($_FILES['message_file']['tmp_name'], dirname(__FILE__).'/../../downloads/'.$fileupload_name))
                                $errors[] = $this->module->l('Can not upload the file','ajax');
                            else
                            {
                                $msg->name_attachment=$_FILES['message_file']['name'];
                            }
                            $attachment=array(
                                'rename' =>  uniqid().Tools::strtolower(Tools::substr($_FILES['message_file']['name'], -5)),
                                'content' => Tools::file_get_contents($_FILES['message_file']['tmp_name']),
                                'tmp_name' => $_FILES['message_file']['tmp_name'],
                                'name' =>$_FILES['message_file']['name'],
                                'mime' => $_FILES['message_file']['type'],
                                'error' => $_FILES['message_file']['error'],
                                'size' => $_FILES['message_file']['size'],
                            );
                            $attachments[]=$attachment;
                        }
                        
                    }
                    if(!$errors)
                    {
                        if($id_message=(int)Tools::getValue('id_message'))
                        {
                            if(!$msg->update())
                                $errors[] = $this->module->l('OPPS! We are not able to send the message at the moment. Please contact webmaster for more infomation','ajax');
                            else
                            {
                                if(isset($fileupload_name) && $fileupload_name)
                                {
                                    $download= new LC_Download();
                                    $download->filename=$fileupload_name;
                                    $download->id_message= $msg->id;
                                    $download->id_conversation = $msg->id_conversation;
                                    $download->file_type=$_FILES['message_file']['type'];
                                    $download->file_size=$_FILES['message_file']['size']/1048576;
                                    if($download->add())
                                    {
                                        $msg->type_attachment=$download->id;
                                        $msg->update();
                                    }
                                    
                                }
                                if($conversation->message_edited)
                                    $conversation->message_edited .=','.$id_message;
                                else
                                    $conversation->message_edited=$id_message;
                                $conversation->latest_online = date('Y-m-d H:i:s');
                                $conversation->date_message_last = date('Y-m-d H:i:s');
                                $conversation->date_message_last_customer =date('Y-m-d H:i:s');
                                $conversation->browser_name = Tools::getValue('browser_name');
                                $current_url = Tools::getValue('current_url');
                                if (strpos($current_url, '#') !== FALSE) {
                                    $current_url = Tools::substr($current_url, 0, strpos($current_url, '#'));
                                }
                                $conversation->current_url = $current_url;
                                $conversation->datetime_added = $conversation->latest_online;
                                LC_Conversation::sendEmail($conversation->id);
                                $conversation->customer_writing=0;
                                if($conversation->end_chat && !$errors)
                                {
                                    if(!$this->module->duplicateConversation($conversation))
                                        $errors[] = $this->module->l('Sorry! We are not able to start the conversation at this time','ajax');
                                }
                                else
                                    $conversation->update();
                            }
                        }
                        else
                        {               
                            if(!$msg->add())
                                $errors[] = $this->module->l('OPPS! We are not able to send the message at the moment. Please contact webmaster for more infomation','ajax');
                            else
                            {
                                if(isset($fileupload_name) && $fileupload_name)
                                {
                                    $download= new LC_Download();
                                    $download->filename=$fileupload_name;
                                    $download->file_type=$_FILES['message_file']['type'];
                                    $download->file_size=$_FILES['message_file']['size']/1048576;
                                    $download->id_message= $msg->id;
                                    $download->id_conversation = $msg->id_conversation;
                                    if($download->add())
                                    {
                                        $msg->type_attachment=$download->id;
                                        $msg->update();
                                    }
                                    
                                }
                                if((int)Configuration::get('ETS_ENABLE_AUTO_REPLY') && !(int)Configuration::get('ETS_LC_STAFF_ACCEPT') && $isAdminOnline)
                                {
                                    
                                    if(!Configuration::get('ETS_FORCE_ONLINE_AUTO_REPLY') || !Ets_livechat::isAdminOnlineNoForce())
                                    {
                                        
                                        if(!(int)Configuration::get('ETS_STOP_AUTO_REPLY') || (Configuration::get('ETS_STOP_AUTO_REPLY') && $conversation->replied==0))
                                        {
                                           
                                            $totalMesageCustomer=Db::getInstance()->getValue('SELECT count(*) FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_employee=0 AND id_conversation='.(int)$conversation->id);
                                            if($auto_message = Db::getInstance()->getValue('SELECT auto_content FROM '._DB_PREFIX_.'ets_livechat_auto_msg WHERE message_order='.(int)$totalMesageCustomer))
                                            {
                                                $msg = new LC_Message();
                                                $msg->id_employee = -1;                    
                                                $msg->id_conversation = $conversation->id;
                                                $msg->message = $auto_message;
                                                $msg->delivered = 0;
                                                $msg->datetime_added = date('Y-m-d H:i:s'); 
                                                $msg->datetime_edited = $msg->datetime_added;
                                                $msg->add();
                                                $extraID = $msg->id;
                                            }
                                        }
                                    }
                                }
                                $conversation->latest_online = date('Y-m-d H:i:s');
                                $conversation->date_message_last = date('Y-m-d H:i:s');
                                $conversation->date_message_last_customer =date('Y-m-d H:i:s');
                                $conversation->browser_name = Tools::getValue('browser_name');
                                $current_url = Tools::getValue('current_url');
                                if (strpos($current_url, '#') !== FALSE) {
                                    $current_url = Tools::substr($current_url, 0, strpos($current_url, '#'));
                                }
                                $conversation->current_url = $current_url;
                                $conversation->datetime_added = $conversation->latest_online;
                                LC_Conversation::sendEmail($conversation->id,isset($attachments)?$attachments:array());
                                $conversation->archive=0;
                                $conversation->customer_writing=0;
                                if($conversation->end_chat && !$errors)
                                {
                                    if(!$this->module->duplicateConversation($conversation))
                                        $errors[] = $this->module->l('Sorry! We are not able to start the conversation at this time','ajax');
                                }
                                else
                                    $conversation->update();
                            }
                        }
                    }
                        
                }
            } // dau dau
            $isEmployeeSeen = $conversation? LC_Conversation::isEmployeeSeen($conversation->id):0;
            $isEmployeeDelivered = $conversation? LC_Conversation::isEmployeeDelivered($conversation->id):0;
            $isEmployeeWriting = $conversation? LC_Conversation::isEmployeeWriting($conversation->id):0;   
            $isEmployeeSent=$conversation? LC_Conversation::isEmployeeSent($conversation->id):0;                
            die(Tools::jsonEncode(array(
                    'error' => $errors ? $this->module->displayError($errors) : false,
                    'isAdminBusy' => Ets_livechat::isAdminBusy(),   
                    'wait_support' => $conversation && $this->module->checkWaitSupport($conversation->id) ? Ets_livechat::getTimeWait():false,
                    'messages' => !$errors && $conversation ? $conversation->getMessages((int)Tools::getValue('latestID'),0,'DESC',$extraID) : array(),
                    'openInfo' => $openInfo,
                    'message_edited' => Tools::getValue('id_message')?LC_Message::getMessage(Tools::getValue('id_message')):'',
                    'id_conversation' => $conversation ? $conversation->id : 0,
                    'isAdminOnline' => $isAdminOnline,
                    'lastMessageOfEmployee' => LC_Conversation::getLastMessageOfEmployee($conversation ? $conversation->id : 0),
                    'isEmployeeSeen'=>$isEmployeeSeen&& in_array('seen',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                    'isEmployeeDelivered'=>$isEmployeeDelivered && in_array('delevered',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                    'isEmployeeWriting'=>$isEmployeeWriting && in_array('writing',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                    'isEmployeeSent'=> $isEmployeeSent && in_array('sent',explode(',',Configuration::get('ETS_LC_DISPLAY_MESSAGE_STATUSES'))),
                    'updateContactInfo' => (int)Tools::getValue('updateCustomerInfo',0) ? true : false,
                    'thankyouMsg' => !$isAdminOnline && ($thankyou = Configuration::get('ETS_LC_TEXT_OFFLINE_THANKYOU')) ? $thankyou : false,
                    'captcha' => Ets_livechat::needCaptcha() ? $this->context->link->getModuleLink('ets_livechat','captcha',array('rand' => Tools::substr(sha1(mt_rand()), 17, 6))) : false,                    
            )));             
        }
        if(Tools::isSubmit('set_chatbox_status'))
        {
            $this->context->cookie->ets_lc_chatbox_status = (Tools::getValue('status')=='open' ? 'open' : 'closed');
            $this->context->cookie->lc_chatbox_top = '';
            $this->context->cookie->lc_chatbox_left ='';
            $this->context->cookie->write();
            die(Tools::jsonEncode(array('success' => $this->module->l('Chat box status updated','ajax').': '.$this->context->cookie->ets_lc_chatbox_status)));
        }
        if(Tools::isSubmit('set_rating') && $rating=(int)Tools::getValue('rating'))
        {
            if($rating<1)
                $rating=1;
            if($rating>5)
                $rating=5;
            $conversation = LC_Conversation::getCustomerConversation();
            if($conversation && Configuration::get('ETS_LC_DISPLAY_RATING'))
            {
                $conversation->latest_online = date('Y-m-d H:i:s');
                $conversation->latest_ip = Tools::getRemoteAddr();
                $conversation->browser_name = Tools::getValue('browser_name');
                $current_url = Tools::getValue('current_url');
                if (strpos($current_url, '#') !== FALSE) {
                    $current_url = Tools::substr($current_url, 0, strpos($current_url, '#'));
                }
                $conversation->current_url = $current_url;
                $conversation->rating = $rating;
                $conversation->update();
                die($this->module->l('Rated'));
            }
        }
        if(Tools::isSubmit('customer_end_chat'))
        {
            $conversation = LC_Conversation::getCustomerConversation();
            if($conversation)
            {
                $conversation->latest_online = date('Y-m-d H:i:s');
                $conversation->latest_ip = Tools::getRemoteAddr();
                $conversation->browser_name = Tools::getValue('browser_name');
                $conversation->end_chat=-1;
                $conversation->update();
                die(Tools::jsonEncode(
                    array(
                        'suss'=>$this->module->l('end_chat'),
                    )
                ));
            }
        }
        if(Tools::isSubmit('getOldMessages'))
        {
            $conversation = LC_Conversation::getCustomerConversation();
            $firstId =Tools::getValue('firstId');
            $messages=LC_Message::getOldMessages($conversation->id,$firstId);
            if($conversation)
            {
                die(Tools::jsonEncode(
                   array(
                        'messages' => $messages,
                        'loaded'=> count($messages)<(int)Configuration::get('ETS_LC_MSG_COUNT')?1:0,
                        'firstId' => $messages?$messages[0]['id_message']:0,
                   ) 
                ));
            }
        }
        die($this->module->l('Access denied'));
    }
}