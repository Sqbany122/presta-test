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
 
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
include(_PS_ADMIN_DIR_.'/../../config/config.inc.php');
include(dirname(__FILE__).'/ajax.init.php');
$context = Context::getContext();
$ets_livechat = Module::getInstanceByName('ets_livechat');
if($context->employee->id && Tools::getValue('token')==Tools::getAdminTokenLite('AdminModules'))
{
    if($ets_livechat->all_shop && $ets_livechat->shops)
    {
        foreach($ets_livechat->shops as $shop)
        {
            Ets_livechat::updateAdminOnline($shop['id_shop']);
        }
    }
    Ets_livechat::updateAdminOnline();
    if(Tools::isSubmit('change_conversation_to_ticket') && $id_conversation=Tools::getValue('id_conversation'))
    {
        $errors= array();
        $form_class= new LC_Ticket_form(1,$context->language->id);
        $conversation= new LC_Conversation($id_conversation);
        if($fields = Tools::getValue('fields'))
        {
            foreach($fields as $id_filed=> $field)
            {
                $field_class= new LC_Ticket_field($id_filed,$context->language->id);
                if($field_class->required && !$field)
                    $errors[]= $field_class->label.' '.$ets_livechat->l('is required','form');
                if(((($field_class->type=='text' || $field_class->type=='text_editor') && !Validate::isCleanHtml($field)) || ($field_class->type=='email' && !Validate::isEmail($field)) || ($field_class->type=='phone_number' && !Validate::isPhoneNumber($field))) && $field)
                    $errors[] = $field_class->label.' '.$ets_livechat->l('is invalid','form');
            }
        }
        if(!$errors)
        {
            $customer_name='';
            $customer_email ='';
            $ticket= new LC_Ticket();
            $ticket->id_form = 1;
            $ticket->id_shop = $context->shop->id;
            $ticket->id_customer = Tools::getValue('display_customer') ? $conversation->id_customer :0;
            $ticket->id_departments = Tools::getValue('ticket_id_departments');
            $ticket->status= Tools::getValue('ticket_status');
            $ticket->priority = Tools::getValue('ticket_priority');
            $ticket->date_admin_update = date('Y-m-d H:i:s');
            $ticket->date_customer_update = date('Y-m-d H:i:s');
            $ticket->date_add=date('Y-m-d H:i:s');
            $ticket->add();
            if($id_message = $ticket->id)
            {
                $conversation->id_ticket= $ticket->id;
                $conversation->update();
                if($fields = Tools::getValue('fields'))
                {
                    foreach($fields as $id_field=> $field)
                    {
                        $field_class= new LC_Ticket_field($id_field,$context->language->id);
                        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_ticket_form_message_field(id_message,id_field,value) VALUES("'.(int)$id_message.'","'.(int)$id_field.'","'.pSQL($field).'")');
                    }
                }
                $ticket->subject = $ets_livechat->getSubjectMessageTicket($ticket->id);
                $ticket->update();
            }
            else
                $errors[] = $ets_livechat->l('Add ticket error');
        }
        if(!$errors)
        {
            if($form_class->mail_new_ticket)
            {
                $email_info= $form_class->getEmailAdminInfo($ticket->id_departments);
                if($email_info && $mails_to=$email_info['mails_to'])
                {
                    $names_to= $email_info['names_to'];
                    $template_vars=array(
                        '{mail_content}' => $ets_livechat->displayMessageTicket($id_message,true),
                        '{subject}' => $ticket->subject,
                    );
                    if(!Mail::Send(
            			$context->language->id,
            			'chat_ticket_admin',
                        $ets_livechat->l('A new ticket was created from chat'),
            			$template_vars,
    			        $mails_to,
            			$names_to? $names_to : null,
            			$customer_email ? $customer_email:null,
            			Configuration::get('PS_SHOP_NAME'),
            			null,
            			null,
            			dirname(__FILE__).'/mails/',
            			false,
            			$context->shop->id,
                        null,
                        null,
                        null
                    ))
                    $errors[]=$ets_livechat->l('An error occurred while sending the message, please try again.');
                }
            }
            if($conversation->id_customer)
            {
                $customer= new Customer($conversation->id_customer);
                $customer_email = $customer->email;
                $customer_name = $customer->lastname.' '.$customer->lastname;
            }
            else
            {
                $customer_email = $conversation->customer_email;
                $customer_name = $conversation->customer_name;
            }
            if($form_class->send_mail_to_customer && $customer_email)
            {
                $template_vars=array(
                    '{mail_content}' => $ets_livechat->displayMessageTicket($id_message),
                    'customer_name' => $customer_name,
                    '{subject}' => $ticket->subject,
                    '{staff}' => $context->employee->firstname.' '.$context->employee->lastname,
                );
                Mail::Send(
        			$context->language->id,
        			'chat_ticket_customer',
                    $ets_livechat->l('A new ticket was created from your chat'),
        			$template_vars,
    		        $customer_email,
        			$customer_email,
        			Configuration::get('PS_SHOP_EMAIl'),
        			Configuration::get('PS_SHOP_NAME'),
        			null,
        			null,
        			dirname(__FILE__).'/mails/',
        			false,
        			$context->shop->id,
                    null
                );
            }
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation='.(int)$id_conversation);
            if($messages)
            {
                foreach($messages as $message)
                {
                    $note = new LC_Note();
                    $note->id_message= $ticket->id;
                    $note->id_employee= $message['id_employee'];
                    $note->note = $message['message'];
                    $note->file_name = $message['name_attachment'];
                    $note->date_add = $message['datetime_added'];
                    $note->add();
                    if($message['type_attachment'] && $note->id && $download = new LC_Download($message['type_attachment']))
                    {
                        unset($download->id);
                        $download->id_message=0;
                        $download->id_note= $note->id;
                        Tools::copy(dirname(__FILE).'/downloads/'.$download->filename,dirname(__FILE).'/downloads/ticket-'.$download->filename);
                        $download->filename = 'ticket-'.$download->filename;
                        $download->add();
                        $note->id_download= $download->id;
                        $note->update();
                    }
                }
            }
        }
        die(
            Tools::jsonEncode(
                array(
                    'errors' => $errors ? $ets_livechat->displayError($errors):'',
                    'success'=> $errors ? false : $ets_livechat->l('Ticket created successfully'),
                    'id_ticket' => !$errors  ? $ticket->id:'',
                    'subject_ticket' =>!$errors ? $ticket->subject:'',
                    'link_ticket'=>!$errors ? $ets_livechat->getAdminLink('AdminLiveChatTickets').'&viewticket&id_ticket='.$ticket->id :'',
                )
            )
        );
    }
    if(Tools::getValue('action')=='updatePositionForm')
    {
        $ticket_form = Tools::getValue('ticket_form');
        if($ticket_form)
        {
            foreach($ticket_form as $key=> $id_form)
            {
                $position = $key+1;
                Db::getInstance()->execute('Update '._DB_PREFIX_.'ets_livechat_ticket_form set sort_order="'.(int)$position.'" WHERE id_form='.(int)$id_form);
            }
            $ets_livechat->updateLastAction();
            die(
                Tools::jsonEncode(
                    array(
                        'success'=>$ets_livechat->l('Sort order updated')
                    )
                )
            );
        }
    }
    if(Tools::getValue('action')=='updatePositionDepartments')
    {
        $departments= Tools::getValue('departments');
        if($departments)
        {
            foreach($departments as $key=>$id_departments)
            {
                $position=$key+1;
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_departments SET sort_order="'.(int)$position.'" WHERE id_departments="'.(int)$id_departments.'"');
            }
            $ets_livechat->updateLastAction();
            die(
                Tools::jsonEncode(
                    array(
                        'success'=>$ets_livechat->l('Sort order updated')
                    )
                )
            );
        }
    }
    if(Tools::isSubmit('getTicketNoReaded'))
    {
        $count = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE readed=0');
        die(Tools::jsonEncode(
            array(
                'count_ticket' => $count,
            )
        ));
    }
    if(Tools::getValue('getmessage'))
    {
        if($query = Tools::getValue('message'))
        {
            $messages = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_pre_made_message WHERE LOWER(short_code) like "'.pSQL($query).'%"');
            if($messages)
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'messages'=>$messages,
                        )
                    )  
                );
            }
        }
        die(
                Tools::jsonEncode(
                    array(
                        'error'=>'null',
                    )
                )  
        );

    }
    
    if(Tools::isSubmit('set_chatbox_position'))
    {
        $context->cookie->lc_chatbox_top = Tools::getValue('top');
        $context->cookie->lc_chatbox_left = Tools::getValue('left');
        $context->cookie->write();   
        Ets_livechat::updateAdminOnline();
        die('updated'); 
    }
    if(Tools::isSubmit('employee_edit_message')&& $id_message=(int)Tools::getValue('id_message'))
    {
        $ets_livechat->updateLastAction();
        $message= new LC_Message($id_message);
        if(!$message->id_employee|| !$message->id || !(int)Configuration::get('ETS_LC_ENABLE_EDIT_MESSAGE'))
        {
            die(
                Tools::jsonEncode(
                    array(
                        'error'=> $ets_livechat->l('error'),
                    )
                )
            );
        }
        else
        {
            $conversation = new LC_Conversation($message->id_conversation);
            $conversation->latest_online = date('Y-m-d H:i:s');
            //$conversation->end_chat=0;
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
    if(Tools::isSubmit('employee_delete_message')&& $id_message=(int)Tools::getValue('id_message'))
    {
        $ets_livechat->updateLastAction();
        $message= new LC_Message($id_message);
        if(!$message->id_employee|| !$message->id || !(int)Configuration::get('ETS_LC_ENABLE_DELETE_MESSAGE'))
        {
            die(
                Tools::jsonEncode(
                    array(
                        'error'=> $ets_livechat->l('error'),
                    )
                )
            );
        }
        else
        {
            $conversation = new LC_Conversation($message->id_conversation);
            if($conversation->employee_message_deleted)
                $conversation->employee_message_deleted .=','.$id_message;
            else
                $conversation->employee_message_deleted=$id_message;
            $conversation->latest_online = date('Y-m-d H:i:s');
            //$conversation->end_chat=0;
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
   if(Tools::getValue('close_conversation_chatbox') && $id_conversation =(int)Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        $conversation_opened = Tools::jsonDecode($context->cookie->converation_opened,true);
        if (($key = array_search($id_conversation, $conversation_opened)) !== false) {
            unset($conversation_opened[$key]);
            $context->cookie->converation_opened = Tools::jsonEncode($conversation_opened);
            $context->cookie->write();
        } 
        $conversation_hided = Tools::jsonDecode($context->cookie->converation_hided,true);
        if ($conversation_hided && ($key = array_search($id_conversation, $conversation_hided)) !== false) {
            unset($conversation_hided[$key]);
            $context->cookie->converation_hided = Tools::jsonEncode($conversation_hided);
            $context->cookie->write();
        } 
   }
   if(Tools::getValue('hide_conversation_chatbox') && $id_conversation =(int)Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        $conversation_hided = Tools::jsonDecode($context->cookie->converation_hided,true);
        if(Tools::getValue('status')=='open')
        {
            if (($key = array_search($id_conversation, $conversation_hided)) !== false) {
                unset($conversation_hided[$key]);
                $context->cookie->converation_hided = Tools::jsonEncode($conversation_hided);
                $context->cookie->write();
            } 
        }
        else
        {
            if (!$conversation_hided || !in_array($id_conversation, $conversation_hided)){
                $conversation_hided[]=$id_conversation;
                $context->cookie->converation_hided = Tools::jsonEncode($conversation_hided);
                $context->cookie->write();
            }
        }
   }
   if(Tools::isSubmit('submit_clear_message') && Tools::getValue('ETS_CLEAR_MESSAGE'))
   {
        $where ='';
        if(!$ets_livechat->all_shop || !$ets_livechat->shops)
        {
            $where .=' AND id_conversation IN (SELECT id_conversation FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_shop ="'.(int)Context::getContext()->shop->id.'")';
        }
        $ets_livechat->updateLastAction();   
        switch (Tools::getValue('ETS_CLEAR_MESSAGE')) {
            case '1_week':
                $sql ='DELETE  FROM '._DB_PREFIX_.'ets_livechat_message WHERE datetime_added <"'.pSQL(date('Y-m-d',strtotime('-1 WEEK'))).'"'.$where;
                Db::getInstance()->execute($sql);
                break;
            case '1_month_ago':
                $sql = 'DELETE  FROM '._DB_PREFIX_.'ets_livechat_message WHERE datetime_added <"'.pSQL(date('Y-m-d',strtotime('-1 MONTH'))).'"'.$where;
                Db::getInstance()->execute($sql);
                break;
            case '6_month_ago':
                $sql = 'DELETE * FROM '._DB_PREFIX_.'ets_livechat_message WHERE datetime_added <"'.pSQL(date('Y-m-d',strtotime('-6 MONTH'))).'"'.$where;
                Db::getInstance()->execute($sql);
                break;
            case '1_year_ago':
                $sql = 'DELETE * FROM '._DB_PREFIX_.'ets_livechat_message WHERE datetime_added <"'.pSQL(date('Y-m-d',strtotime('-1 YEAR'))).'"'.$where;
                Db::getInstance()->execute($sql);
                break;
            case 'everything':
                $sql = 'DELETE  FROM '._DB_PREFIX_.'ets_livechat_message WHERE 1'.$where;
                Db::getInstance()->execute($sql);
                break;
        }
        $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation NOT IN (SELECT id_conversation FROM '._DB_PREFIX_.'ets_livechat_message)';
        $conversations = Db::getInstance()->executeS($sql);
        $ids_conversation ='';
        if($conversations)
        {
            foreach($conversations as $conversation)
            {
                $ids_conversation .= ','.$conversation['id_conversation']; 
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation='.(int)$conversation['id_conversation']);
            }
        }
        die(Tools::jsonEncode(
            array(
                'ids_conversation'=> trim($ids_conversation,','),
            )
        ));
   }
   if(Tools::isSubmit('end_chat_conversation') &&$ets_livechat->checkExistConversation((int)Tools::getValue('id_conversation'))&& $id_conversation=(int)Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        if(!$ets_livechat->checkConversationEmployee($id_conversation,$context->employee->id) || Ets_livechat::checkWaitAccept($id_conversation))
        {
            die(Tools::jsonEncode(array(
                'id_conversation' => $id_conversation,
                'checkDepartment' => $ets_livechat->l('You\'re not allowed to access this conversation. It has been changed to another department'),
            )));   
        }
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation SET end_chat="'.(int)$context->employee->id.'" WHERE id_conversation="'.(int)$id_conversation.'"');
        die('1');
        //die('UPDATE '._DB_PREFIX_.'ets_livechat_conversation SET end_chat="'.(int)$context->employee->id.'" WHERE id_conversation="'.(int)$id_conversation.'"');
   }
   if(Tools::isSubmit('delete_pre_made_message') && $id_pre_made_message =(int)Tools::getValue('id_pre_made_message'))
   {
        $ets_livechat->updateLastAction();
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_pre_made_message WHERE id_pre_made_message='.(int)$id_pre_made_message);
        die(
            Tools::jsonEncode(
                array(
                    'success' => $ets_livechat->l('Deleted pre-made message successfully')
                )
            )
        );
   }
   if(Tools::isSubmit('delete_departments') && $id_departments= Tools::getValue('id_departments'))
   {
        $ets_livechat->updateLastAction();
        $department= new LC_Departments($id_departments);
        $department->delete();
        die(
            Tools::jsonEncode(  
                array(
                    'success' => $ets_livechat->l('Deleted successfully')
                )
            )
        );
   }
   if(Tools::isSubmit('load_made_messages'))
   {
        die(
            Tools::jsonEncode(
                array(
                    'html'=> $ets_livechat->displayListPreMadeMessages(),
                )
            )  
        );
   }
   if(Tools::isSubmit('get_pre_made_message') && $id_pre_made_message=(int)Tools::getValue('id_pre_made_message'))
   {
        $message = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_pre_made_message WHERE id_pre_made_message='.(int)$id_pre_made_message);
        die(Tools::jsonEncode($message));
   }
   if(Tools::isSubmit('get_departments') && $id_departments= Tools::getValue('id_departments'))
   {

        $ets_livechat->_getFromDepartments($id_departments);
   }
   if(Tools::isSubmit('submit_pre_made_message'))
   {
        $ets_livechat->updateLastAction();
        $ets_livechat->_postPreMadeMessage();
   }
   if(Tools::isSubmit('submit_departments'))
   {
        $ets_livechat->updateLastAction();
        $ets_livechat->_postDepartments();
   }
   if(Tools::isSubmit('get_auto_reply_info') && $id_auto_msg=(int)Tools::getValue('id_auto_msg'))
   {
        $message = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_auto_msg WHERE id_auto_msg ="'.(int)$id_auto_msg.'"');
        die(Tools::jsonEncode($message));
   }
   if(Tools::isSubmit('delete_auto_reply') && $id_auto_msg =(int)Tools::getValue('id_auto_msg'))
   {
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_auto_msg WHERE id_auto_msg="'.(int)$id_auto_msg.'"');
        die(
            Tools::jsonEncode(
                array(
                    'success' => $ets_livechat->l('Deleted auto message successfully'),
                )  
            )
        );
   }
   if(Tools::isSubmit('get_extra_form'))
   {
        die(
          Tools::jsonEncode(
                array(
                    'formhtml' => $ets_livechat->renderExtraForm(),
                )
          )  
        );
   }
   if(Tools::isSubmit('submit_auto_reply'))
   {
        $ets_livechat->updateLastAction();
        $ets_livechat->_postAutoReply();
   }    
   if(Tools::isSubmit('getOldMessages')&& $ets_livechat->checkExistConversation((int)Tools::getValue('id_conversation'))&&$id_conversation = (int)Tools::getValue('id_conversation'))
   {
       $firstId =Tools::getValue('firstId');
       $messages=LC_Message::getOldMessages($id_conversation,$firstId);
       die(Tools::jsonEncode(
           array(
                'messages' => $messages,
                'loaded'=> count($messages)<(int)Configuration::get('ETS_LC_MSG_COUNT')?1:0,
                'firstId' => $messages?$messages[0]['id_message']:0,
           ) 
       ));    
   } 
   if(Tools::isSubmit('change_status_display_admin'))
   {
        $ets_livechat->updateLastAction();
        Configuration::updateValue('ETS_CONVERSATION_DISPLAY_ADMIN',(int)Tools::getValue('status'));
   }
   if(Tools::isSubmit('change_status_employee') && $change_status_employee=Tools::getValue('change_status_employee'))
   {
    
        $ets_livechat->updateLastAction();
        if($change_status_employee=='foce_online')
        {
            if($context->employee->id_profile==1)
            {
                Configuration::updateValue('ETS_LC_FORCE_ONLINE',1);
                $employees = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'employee');
                if($employees)
                {
                    foreach($employees as $employee)
                    {
                        if(!Ets_livechat::getStatusEmployee($employee['id_employee']))
                        {
                            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_employee_status(id_employee,id_shop,status) VALUES("'.(int)$employee['id_employee'].'","'.(int)$context->shop->id.'","online")');
                        }
                        else
                            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_employee_status SET status="online" WHERE id_employee="'.(int)$employee['id_employee'].'" AND id_shop='.(int)$context->shop->id);
                    }
                }
            }
            
        }
        else
        {
            if(!Configuration::get('ETS_LC_FORCE_ONLINE') || $context->employee->id_profile==1)
            {
                Configuration::updateValue('ETS_LC_FORCE_ONLINE',0);
                if(!Ets_livechat::getStatusEmployee($context->employee->id))
                {
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_employee_status(id_employee,id_shop,status) VALUES("'.(int)$context->employee->id.'","'.(int)$context->shop->id.'","'.pSQL($change_status_employee).'")');
                }
                else
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_employee_status SET status="'.pSQL($change_status_employee).'" WHERE id_employee="'.(int)$context->employee->id.'" AND id_shop='.(int)$context->shop->id);
            }
            
        }
        die('1');
   }
   if(Tools::isSubmit('delete_conversation') &&$ets_livechat->checkExistConversation((int)Tools::getValue('id_conversation'))&& $id_conversation=(int)Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        if(!$ets_livechat->checkConversationEmployee($id_conversation,$context->employee->id))
        {
            die(Tools::jsonEncode(array(
                'id_conversation' => $id_conversation,
                'checkDepartment' => $ets_livechat->l('You\'re not allowed to access this conversation. It has been changed to another department'),
            )));   
        }
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_conversation WHERE id_conversation='.(int)$id_conversation);
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation='.(int)$id_conversation); 
        $conversation_opened = Tools::jsonDecode($context->cookie->converation_opened,true);
        if (($key = array_search($id_conversation, $conversation_opened)) !== false) {
            unset($conversation_opened[$key]);
            $context->cookie->converation_opened = Tools::jsonEncode($conversation_opened);
            $context->cookie->write();
        }
        die('1');
   }
   if(Tools::isSubmit('load_more_customer_chat'))
   {
        $ets_livechat->_getMoreCustomer();
   }
   if(Tools::isSubmit('load_list_customer_chat') && Tools::getValue('load_list_customer_chat'))
   {
        $status= Ets_livechat::getStatusEmployee($context->employee->id);
        if(Tools::getValue('auto'))
        {
            if($reload_list=$ets_livechat->checkNewMessage())
            {
                die(Tools::jsonEncode(array(
                    'status_employee' => Configuration::get('ETS_LC_FORCE_ONLINE') ? 'foce_online' : $status ,
                    'html' => $ets_livechat->displayListCustomerChat(),
                    'totalMessageNoSeen'=>LC_Conversation::getTotalMessageNoSeen(),
                    'reload_list'=>$reload_list,
                    'last_message'=> $reload_list && $reload_list==1? LC_Message::getMessage():array(),
                    'conversations' =>$reload_list==1 ? LC_Conversation::getListConversations(Tools::getValue('customer_all'),Tools::getValue('customer_archive'),Tools::getValue('customer_search')):'',
                    'level_request'=> LC_Conversation::getLevelRequestAdmin(),
                )));    
            }
            else{
                die(Tools::jsonEncode(array(
                    'reload_list'=>0,
                    'status_employee' =>Configuration::get('ETS_LC_FORCE_ONLINE') ? 'foce_online' : $status,
                    'level_request'=> LC_Conversation::getLevelRequestAdmin(),
                    'totalMessageNoSeen'=>LC_Conversation::getTotalMessageNoSeen(),
                    'conversations' =>LC_Conversation::getListConversations(Tools::getValue('customer_all'),Tools::getValue('customer_archive'),Tools::getValue('customer_search')),
                )));
            }
        }
        else{
            die(Tools::jsonEncode(array(
                'html' => $ets_livechat->displayListCustomerChat(),
                'totalMessageNoSeen'=>LC_Conversation::getTotalMessageNoSeen(),
                'reload_list'=>2,
                'status_employee' =>Configuration::get('ETS_LC_FORCE_ONLINE') ? 'foce_online' : $status,
                'level_request'=> LC_Conversation::getLevelRequestAdmin(),
            )));
        }
        
   }
   if(Tools::isSubmit('load_chat_box')&& $id_conversation=(int)Tools::getValue('id_conversation') )
   {    
        if($ets_livechat->checkExistConversation((int)Tools::getValue('id_conversation')))
        {
            $conversation_class = new LC_Conversation($id_conversation);
            if($ets_livechat->checkConversationEmployee($conversation_class,$context->employee->id))
            {
                $message_delivered =(int)Tools::getValue('message_delivered');
                $message_seen =(int)Tools::getValue('message_seen');
                $message_writing =(int)Tools::getValue('message_writing');
                LC_Conversation::updateMessageStattus($id_conversation,$message_delivered,$message_seen,$message_writing,'employee');
                $conversation_opened = Tools::jsonDecode($context->cookie->converation_opened,true);
                if(!$conversation_opened || !in_array($id_conversation,$conversation_opened))
                {
                    $conversation_opened[]=$id_conversation;
                    $context->cookie->converation_opened = Tools::jsonEncode($conversation_opened);
                    $context->cookie->write();
                }
                if((int)Tools::getValue('refresh'))
                {
                    $ets_livechat->displayChatBoxEmployee($id_conversation,(int)Tools::getValue('refresh'));
                }
                else
                {
                    $ets_livechat->updateLastAction();
                    die(Tools::jsonEncode(array(
                        'html' => $ets_livechat->displayChatBoxEmployee($id_conversation,(int)Tools::getValue('refresh')),
                        'totalMessageNoSeen'=>LC_Conversation::getTotalMessageNoSeen(),
                    )));
                }
            }
            else
            {
                die(Tools::jsonEncode(array(
                    'checkDepartment' => $ets_livechat->l('You\'re not allowed to access this conversation. It has been changed to another department'),
                )));
            }
            
        }
        else
        {
            die(Tools::jsonEncode(array(
                    'html' => '',
                    'totalMessageNoSeen'=>LC_Conversation::getTotalMessageNoSeen(),
            )));
        }
   }
   if(Tools::isSubmit('load_chat_boxs')&& $ids_conversation= explode(',',Tools::getValue('ids_conversation')))
   {
        $conversations=array();
        $messages_seen= explode(',',Tools::getValue('messages_seen'));
        $messages_writing= explode(',',Tools::getValue('messages_writing'));
        foreach($ids_conversation as $key=> $id_conversation)
        {
            $conversation_class= new LC_Conversation($id_conversation);
            if($ets_livechat->checkExistConversation($id_conversation) && $id_conversation && $ets_livechat->checkConversationEmployee($conversation_class,$context->employee->id))
            {
                $message_seen = (int)$messages_seen[$key];
                $message_writing = $messages_writing[$key];
                LC_Conversation::updateMessageStattus($id_conversation,false,$message_seen,$message_writing,'employee');
                $conversations[]=array(
                    'conversation'=> $ets_livechat->displayChatBoxEmployee($id_conversation,true,true),
                    'id_conversation' =>$id_conversation,
                );
            }
        }
        $reload_list= $ets_livechat->checkNewMessage();
        die(Tools::jsonEncode(array(
            'conversations'=>$conversations,
            'reload_list'=>$reload_list,
            'status_employee' => ($status=Ets_livechat::getStatusEmployee($context->employee->id)) ? $status: Configuration::get('ETS_LC_STATUS_EMPLOYEE'),
            'list_customer_html'=> $reload_list? $ets_livechat->displayListCustomerChat():'',
            'last_message'=> $reload_list && $reload_list==1? LC_Message::getMessage():array(),
            'listconversations' =>$reload_list!=2? LC_Conversation::getListConversations(Tools::getValue('customer_all'),Tools::getValue('customer_archive'),Tools::getValue('customer_search')):'',
            'totalMessageNoSeen' => LC_Conversation::getTotalMessageNoSeen(),
            'level_request'=> LC_Conversation::getLevelRequestAdmin(),
        )));
   }
   if(Tools::isSubmit('send_message') && $ets_livechat->checkExistConversation((int)Tools::getValue('id_conversation')) && (int)Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        $message=trim(Tools::getValue('message'));
        $id_conversation=(int)Tools::getValue('id_conversation');
        $conversation= new LC_Conversation($id_conversation);
        if(!$ets_livechat->checkConversationEmployee($conversation,$context->employee->id))
        {
            die(Tools::jsonEncode(array(
                'id_conversation' => $id_conversation,
                'checkDepartment' => $ets_livechat->l('You\'re not allowed to access this conversation. It has been changed to another department'),
            )));   
        }
        if($conversation->end_chat)
        {
            $ets_livechat->errors[]=$ets_livechat->l('Chat has ended.');
        }
        elseif((Configuration::get('ETS_LC_STAFF_ACCEPT') && $conversation->id_employee==0 && !Ets_livechat::checkSupperAdminDecline($conversation) ))
        {
            $ets_livechat->errors[]=$ets_livechat->l('You have to accept this chat before start chatting.');
        }
        if(Tools::strlen($message)>(int)Configuration::get('ETS_LC_MSG_LENGTH'))
            $ets_livechat->errors[]=$ets_livechat->l('Message is invalid');
        elseif($message && !Validate::isCleanHtml($message,false))
            $ets_livechat->errors[]=$ets_livechat->l('Message is invalid');
        if($id_message=(int)Tools::getValue('id_message'))
        {
            $msg = new LC_Message($id_message);
            $msg->datetime_edited= date('Y-m-d H:i:s');
        }    
        else
        {
            $msg = new LC_Message();
            $msg->datetime_added = date('Y-m-d H:i:s');
            $msg->datetime_edited = $msg->datetime_added;
        } 
        $msg->id_employee = $context->employee->id;                    
        $msg->id_conversation = $id_conversation;
        $msg->message =trim(Tools::nl2br(strip_tags($message)));
        $msg->delivered = 0;
        $attachments=array();
        if(!$ets_livechat->errors && isset($_FILES['message_file']['tmp_name'])&& isset($_FILES['message_file']['name']) && $_FILES['message_file']['name'])
        {
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES['message_file']['name'], '.'), 1));
            if(!in_array($type,$ets_livechat->file_types))
            {
                $ets_livechat->errors[] = $ets_livechat->l('File upload is invalid');
            }
            else
            {
                $fileupload_name = sha1(microtime()).'-'.$_FILES['message_file']['name'];
                if (!move_uploaded_file($_FILES['message_file']['tmp_name'], dirname(__FILE__).'/downloads/'.$fileupload_name))
                    $ets_livechat->errors[] = $ets_livechat->l('Can not upload the file');
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
        elseif($msg->message=='' && $message!='' && !$ets_livechat->errors)
        {
            $ets_livechat->errors[]=$ets_livechat->l('Message is invalid');
        }
        if(!$ets_livechat->errors)
        {
            if($id_message=(int)Tools::getValue('id_message'))
            {
                if($msg->update())
                {
                    if(isset($fileupload_name) && $fileupload_name)
                    {
                        $download= new LC_Download();
                        $download->filename=$fileupload_name;
                        $download->id_message= $msg->id;
                        $download->id_conversation = $msg->id_conversation;
                        $download->file_size = $_FILES['message_file']['size']/1048576;
                        $download->file_type=$_FILES['message_file']['type'];
                        if($download->add())
                        {
                            $msg->type_attachment=$download->id;
                            $msg->update();
                        }
                        
                    }
                    if($conversation->employee_message_edited)
                        $conversation->employee_message_edited .=','.$id_message;
                    else
                        $conversation->employee_message_edited=$id_message;
                    $conversation->date_message_last = date('Y-m-d H:i:s');
                   // $conversation->end_chat=0;
                    $conversation->replied=1;
                    $conversation->employee_writing=0;
                    $conversation->update();
                    
                    
                }
                else
                {
                    $ets_livechat->errors[] = $ets_livechat->l('Send message error'); 
                }
            }
            else
            {
                
                if($msg->add())
                {
                    if(isset($fileupload_name) && $fileupload_name)
                    {
                        $download= new LC_Download();
                        $download->filename=$fileupload_name;
                        $download->id_message= $msg->id;
                        $download->id_conversation = $msg->id_conversation;
                        $download->file_size = $_FILES['message_file']['size']/1048576;
                        $download->file_type=$_FILES['message_file']['type'];
                        if($download->add())
                        {
                            $msg->type_attachment=$download->id;
                            $msg->update();
                        }
                    }
                    $conversation->date_message_last = date('Y-m-d H:i:s');
                    //$conversation->end_chat=0;
                    if($conversation->id_departments_wait)
                    {
                        $conversation->id_departments = $conversation->id_departments_wait==-1 ? 0 : $conversation->id_departments_wait;
                        $conversation->id_departments_wait=0;
                    }
                    $conversation->replied=1;
                    $conversation->employee_writing=0;
                    $conversation->update();
                }
                else
                {
                    $ets_livechat->errors[] = $ets_livechat->l('Send message error');
                }
            }
            if(!$ets_livechat->errors)
            {
                if(Tools::getValue('send_message_to_mail'))
                {
                    if($msg->name_attachment)
                    {
                        $linkdownload = $context->link->getModuleLink('ets_livechat','download',array('downloadfile'=>md5(_COOKIE_KEY_.$msg->type_attachment)));
                        $attachment_file='<p><a class="file_sent" href="'.$linkdownload.'" target="_blank">'.$msg->name_attachment.'</a></p>';
                    }
                    $template_vars =array(
                        '{message}'=>$msg->message.(isset($attachment_file) ? $attachment_file :''),
                        '{year}' =>Date('Y'),
                    );
                    if($conversation->id_customer)
                    {
                        $customer= new Customer($conversation->id_customer);
                        $email = $customer->email;
                    }
                    else
                        $email= $conversation->customer_email;
                    if($email && file_exists(dirname(__FILE__).'/mails/'.$context->language->iso_code.'/send_message.html'))
                    {
                        Mail::Send(
        					Context::getContext()->language->id,
        					'send_message',
        					sprintf(Mail::l('Message from %s', Context::getContext()->language->id),$context->shop->name),
        					$template_vars,
        					$email,
        					null,
        					null,
        					null,
        					$attachments,
        					null,
        					dirname(__FILE__).'/mails/',
        					null,
        					Context::getContext()->shop->id
        				);
                    }
                }
                $ets_livechat->displayChatBoxEmployee($id_conversation,true);
            }
            die(Tools::jsonEncode(array(
                'html' => '',
                'error' => $ets_livechat->errors?$ets_livechat->displayError($ets_livechat->errors):false,
            )));
        }
        else
        {
            die(Tools::jsonEncode(array(
                'html' => '',
                'error' => $ets_livechat->errors?$ets_livechat->displayError($ets_livechat->errors):false,
            )));  
        }
   }
   if(Tools::isSubmit('changed_satatusblock') && $ets_livechat->checkExistConversation((int)Tools::getValue('id_conversation')) && $id_conversation =(int)Tools::getValue('id_conversation'))
   {
        if(!$ets_livechat->checkConversationEmployee(Tools::getValue('id_conversation'),$context->employee->id))
        {
            die(Tools::jsonEncode(array(
                'checkDepartment' => $ets_livechat->l('You\'re not allowed to access this conversation. It has been changed to another department'),
            )));   
        }
        $ets_livechat->updateLastAction();
        $status= Tools::getValue('status');
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation set blocked="'.(int)$status.'" WHERE id_conversation='.(int)$id_conversation);
        die(
            Tools::jsonEncode(
                array(
                    'status' => $status,
                    'text_status' => $status? $ets_livechat->l('Blocked'):$ets_livechat->l('Block'),
                )
            )
        );
   }
   if(Tools::getValue('changed_satatuscaptcha') && $ets_livechat->checkExistConversation((int)Tools::getValue('id_conversation'))&& $id_conversation=(int)Tools::getValue('id_conversation') )
   {
        if(!$ets_livechat->checkConversationEmployee($id_conversation,$context->employee->id))
        {
            die(Tools::jsonEncode(array(
                'id_conversation' => $id_conversation,
                'checkDepartment' => $ets_livechat->l('You\'re not allowed to access this conversation. It has been changed to another department'),
            )));   
        }
        $ets_livechat->updateLastAction();
        $status= Tools::getValue('status');
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation set captcha_enabled="'.(int)$status.'" WHERE id_conversation='.(int)$id_conversation);
        die(
            Tools::jsonEncode(
                array(
                    'status' => $status,
                    'text_status' => $status? $ets_livechat->l('Captcha'):$ets_livechat->l('Captcha'),
                )
            )
        );
   }
   if(Tools::isSubmit('add_active_customer_chat')&& $ets_livechat->checkExistConversation((int)Tools::getValue('id_conversation'))&& $id_conversation=(int)Tools::getValue('id_conversation'))
   {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation SET archive=0 WHERE id_conversation='.(int)$id_conversation);
        die(Tools::jsonEncode(array(
            'html' => $ets_livechat->displayListCustomerChat(),
            'totalMessageNoSeen'=>LC_Conversation::getTotalMessageNoSeen(),
        )));
   }
   if(Tools::isSubmit('add_archive_customer_chat')&& $ets_livechat->checkExistConversation((int)Tools::getValue('id_conversation')) && $id_conversation=(int)Tools::getValue('id_conversation'))
   {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation SET archive=1 WHERE id_conversation='.(int)$id_conversation);
        die(Tools::jsonEncode(array(
            'html' => $ets_livechat->displayListCustomerChat(),
            'totalMessageNoSeen'=>LC_Conversation::getTotalMessageNoSeen(),
        )));
   }
   if(Tools::isSubmit('change_department') && $id_conversation=Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        if($ets_livechat->checkExistConversation($id_conversation))
        {
            $error='';
            $id_departments = (int)Tools::getValue('id_departments');
            $id_employee =(int)Tools::getValue('id_employee');
            if($id_departments &&  $id_employee > 0 && !$ets_livechat->checkDepartmentsExitsEmployee($id_departments,$id_employee))
                $error = $ets_livechat->l('Departments and employee are invalid');
            $conversation = new LC_Conversation($id_conversation);
            if($conversation->id_employee!= $context->employee->id && $context->employee->id_profile!=1)
                $error = $ets_livechat->l('You do not have access permission');
            if(!$error)
            {
                $conversation->id_departments_wait = $id_departments;
                $conversation->id_employee_wait =(int)Tools::getValue('id_employee');
                $conversation->id_tranfer= $context->employee->id;
                if($conversation->update())
                {
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_staff_decline WHERE id_conversation="'.(int)$conversation->id.'" AND id_employee='.(int)$conversation->id_employee_wait);
                }
                
                die(
                    Tools::jsonEncode(
                        array(
                            'id_conversation' =>$id_conversation,
                            'succ' => $ets_livechat->l('Changed successfully'),
                            'waiting_acceptance' => $ets_livechat->checkWaitingAcceptance($conversation),
                        )
                    )
                );
            }
            else
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'error'=>$error,
                        )
                    )
                );
            }
        }
   }
   if(Tools::isSubmit('change_status_form') && $id_form=Tools::getValue('id_form'))
   {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form SET active="'.(int)Tools::getValue('active').'" WHERE id_form='.(int)$id_form);
        die(
            Tools::jsonEncode(
                array(
                    'active' =>(int)Tools::getValue('active'),
                    'id_form' => $id_form,
                    'title' => Tools::getValue('active') ? $ets_livechat->l('Click to disabled'): $ets_livechat->l('Click to enabled'),
                    'success' => $ets_livechat->l('Status changed'),
                )
            )
        );
   }
   if(Tools::isSubmit('change_status_departments') && $id_departments=Tools::getValue('id_departments'))
   {
        $ets_livechat->updateLastAction();
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_departments SET status="'.(int)Tools::getValue('status').'" WHERE id_departments='.(int)$id_departments);
        die(
            Tools::jsonEncode(
                array(
                    'status' =>(int)Tools::getValue('status'),
                    'id_departments' => $id_departments,
                    'success' => $ets_livechat->l('Status changed'),
                    'title' => Tools::getValue('status') ? $ets_livechat->l('Click to disabled'): $ets_livechat->l('Click to enabled')
                )
            )
        );
   }
   if(Tools::isSubmit('change_status_staff') && $id_employee=Tools::getValue('id_employee'))
   {
        $ets_livechat->updateLastAction();
        if($staff = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_staff WHERE id_employee='.(int)$id_employee))
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_staff SET status="'.(int)Tools::getValue('status').'" WHERE id_employee='.(int)$id_employee);
        else
        {
            $employee = new Employee($id_employee);
            $name = $employee->firstname.' '.$employee->lastname;
            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_staff (id_employee,name,status) VALUES ("'.(int)$id_employee.'","'.pSQL($name).'","'.(int)Tools::getValue('status').'")');
        }
        die(
            Tools::jsonEncode(
                array(
                    'status' =>(int)Tools::getValue('status'),
                    'id_employee' => $id_employee,
                    'title' => Tools::getValue('status') ? $ets_livechat->l('Click to disabled'): $ets_livechat->l('Click to enabled'),
                    'success' => $ets_livechat->l('Status changed'),
                )
            )
        );
   }
   if(Tools::isSubmit('get_staff') && $id_employee=(int)Tools::getValue('id_employee'))
   {
        die(
            Tools::jsonEncode(
                array(
                    'html' =>$ets_livechat->getFormStaff($id_employee),
                )
            )
        );
   }
   if(Tools::getValue('submit_save_staff') && $id_employee=Tools::getValue('id_employee'))
   {
        $ets_livechat->updateLastAction();
        $ets_livechat->_postStaff($id_employee);
   }
   if(Tools::isSubmit('delete_avata_staff') && $id_employee=Tools::getValue('id_staff'))
   {
        $ets_livechat->updateLastAction();
        $avatar = Db::getInstance()->getValue('SELECT avata FROM '._DB_PREFIX_.'ets_livechat_staff WHERE id_employee='.(int)$id_employee);
        if($avatar)
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_staff SET avata="" WHERE id_employee='.(int)$id_employee);
            @unlink(dirname(__FILE__).'/views/img/config/'.$avatar);
        }
        die(Tools::jsonEncode(
            array(
                'success' => $ets_livechat->l('Deleted avatar successfully'),
                'image' => $ets_livechat->url_module.'/views/img/config/adminavatar.jpg'
            )
        )
        );
   }
   if(Tools::isSubmit('change_company_info') && $value=Tools::getValue('value'))
   {
        die(
            Tools::jsonEncode($ets_livechat->_getCompanyInfo(Context::getContext()->employee->id,$value))
        );
   }
   if(Tools::isSubmit('accept_submit') && $id_conversation=Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        $ets_livechat->_accpectConversation($id_conversation);
   }
   if(Tools::isSubmit('decline_submit') && $id_conversation=Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        $ets_livechat->_declineConversation($id_conversation);
   }
   if(Tools::isSubmit('cancel_acceptance') && $id_conversation=Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        $ets_livechat->_cancelAcceptance($id_conversation);
   }
   if(Tools::isSubmit('conversation_note') && $id_conversation=Tools::getValue('id_conversation'))
   {
        $ets_livechat->updateLastAction();
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_conversation SET NOTE ="'.pSQL(Tools::getValue('conversation_note')).'" WHERE id_conversation="'.(int)$id_conversation.'"');
        die(
            Tools::jsonEncode(
                array(
                    'error'=>false,
                )
            )  
        );
   }
   if(Tools::isSubmit('view_conversation') && $id_conversation=Tools::getValue('id_conversation'))
   {
        die(
            Tools::jsonEncode(
                array(
                    'converation_messages' => $ets_livechat->_displayConversationDetail($id_conversation),
                )
            )
        );
   }
   if(Tools::isSubmit('gethistory') && $id_conversation=(int)Tools::getValue('id_conversation'))
   {
        $conversation = new LC_Conversation($id_conversation);
        if($conversation->chatref)
        {
            die(
                Tools::jsonEncode(
                    array(
                        'conversation_history' => $ets_livechat->_displayHistoryChatCustomer($conversation->chatref),
                    )
                )
            );
        }
        else
        {
            die(
                Tools::jsonEncode(
                    array(
                        'error' => $ets_livechat->l('Conversation does not exist'),
                    )
                )
            );
        }
   }
   if(Tools::isSubmit('add_new_field_in_form'))
   {
        die(
            Tools::jsonEncode(
                array(
                    'html_form_filed' => $ets_livechat->GetFormField(0,Tools::getValue('max_position')+1),
                )
            )  
        );
   }
   if(Tools::getValue('action')=='updatePositionField')
   {
        $ets_livechat->updateLastAction();
        if($ticket_form_field = Tools::getValue('ticket_form_field'))
        {
            foreach($ticket_form_field as $key=> $field)
            {
                if($field)
                {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_field SET position ="'.(int)$key.'" WHERE id_field='.(int)$field);
                }
            }
        }
        die(
            Tools::jsonEncode(
                array(
                    'success' => $ets_livechat->l('Position updated succesfully'),
                )
            )
        );
   }
   if(Tools::isSubmit('delete_form_field') && $id_field=Tools::getValue('id_field'))
   {
        $ets_livechat->updateLastAction();
        $field_class= new LC_Ticket_field($id_field);
        if($field_class->id_form!=1)
        {
            $field_class->delete();
            die(
                Tools::jsonEncode(
                    array(
                        'success' => $ets_livechat->l('Field deleted succesfully'),
                    )
                )
            ); 
        }
   }
   if(Tools::isSubmit('delete_form_obj') && $id_form=Tools::getValue('id_form'))
   {
        if($id_form!=1)
        {
            $ets_livechat->updateLastAction();
            $form= new LC_Ticket_form($id_form);
            $form->delete();
            die(
                Tools::jsonEncode(
                    array(
                        'success' => $ets_livechat->l('Form deleted succesfully'),
                    )
                )
            ); 
        }
        
   }
   if(Tools::isSubmit('submit_clear_attachments') && Tools::getValue('ETS_CLEAR_ATTACHMENT'))
   {
    
        $ets_livechat->updateLastAction();  
        switch (Tools::getValue('ETS_CLEAR_ATTACHMENT')) {
            case '1_week':
                $date= date('Y-m-d',strtotime('-1 WEEK'));
                break;
            case '1_month_ago':
                $date = date('Y-m-d',strtotime('-1 MONTH'));
                break;
            case '6_month_ago':
                $date = date('Y-m-d',strtotime('-6 MONTH'));
                break;
            case '1_year_ago':
                $date = date('Y-m-d',strtotime('-1 YEAR'));
                break;
            case 'everything':
                $ets_livechat->getAttachmentsMessage();
                $ets_livechat->getAttachmentsNote();
                $ets_livechat->getAttachmentsTickets();
                break;
        
        }
        if(isset($date))
        {
            $ets_livechat->getAttachmentsMessage(false,' AND datetime_added <"'.pSQL($date).'"');
            $ets_livechat->getAttachmentsNote(false,' AND date_add <"'.pSQL($date).'"');
            $ets_livechat->getAttachmentsTickets(false,' AND t.date_add <"'.pSQL($date).'"');
        }
        $message_week= $ets_livechat->getAttachmentsMessage(true,' AND datetime_added <"'.pSQL(date('Y-m-d',strtotime('-1 WEEK'))).'"');
        $note_week= $ets_livechat->getAttachmentsNote(true,' AND date_add <"'.pSQL(date('Y-m-d',strtotime('-1 WEEK'))).'"');
        $attachment_week= $ets_livechat->getAttachmentsTickets(true,' AND t.date_add <"'.pSQL(date('Y-m-d',strtotime('-1 WEEK'))).'"');
        $messages_1_month_ago= $ets_livechat->getAttachmentsMessage(true,' AND datetime_added <"'.pSQL(date('Y-m-d',strtotime('-1 MONTH'))).'"');
        $note_1_month_ago= $ets_livechat->getAttachmentsNote(true,' AND date_add <"'.pSQL(date('Y-m-d',strtotime('-1 MONTH'))).'"');
        $attachment_1_month_ago = $ets_livechat->getAttachmentsTickets(true,' AND t.date_add <"'.pSQL(date('Y-m-d',strtotime('-1 MONTH'))).'"');
        $messages_6_month_ago = $ets_livechat->getAttachmentsMessage(true,' AND datetime_added <"'.pSQL(date('Y-m-d',strtotime('-6 MONTH'))).'"');
        $notes_6_month_ago = $ets_livechat->getAttachmentsNote(true,' AND date_add <"'.pSQL(date('Y-m-d',strtotime('-6 MONTH'))).'"');
        $attachments_6_month_ago = $ets_livechat->getAttachmentsTickets(true,' AND t.date_add <"'.pSQL(date('Y-m-d',strtotime('-6 MONTH'))).'"');
        $messages_year_ago = $ets_livechat->getAttachmentsMessage(true,' AND datetime_added <"'.pSQL(date('Y-m-d',strtotime('-1 YEAR'))).'"');
        $notes_year_ago = $ets_livechat->getAttachmentsNote(true,' AND date_add <"'.pSQL(date('Y-m-d',strtotime('-1 YEAR'))).'"');
        $attachments_year_ago = $ets_livechat->getAttachmentsTickets(true,' AND t.date_add <"'.pSQL(date('Y-m-d',strtotime('-1 YEAR'))).'"');
        $messages_everything = $ets_livechat->getAttachmentsMessage(true);
        $notes_everything = $ets_livechat->getAttachmentsNote(true);
        $attachments_everything = $ets_livechat->getAttachmentsTickets(true);
        die(
            Tools::jsonEncode(
                array(
                    'success' => $ets_livechat->l('Attachment deleted succesfully'),
                    'attachments_1_week' => $message_week['count']+$note_week['count']+$attachment_week['count'],
                    'attachments_1_week_size' => $message_week['size']+$note_week['size']+$attachment_week['size'],
                    'attachments_1_month_ago' => $messages_1_month_ago['count'] + $note_1_month_ago['count']+$attachment_1_month_ago['count'],
                    'attachments_1_month_ago_size' => $messages_1_month_ago['size'] + $note_1_month_ago['size']+$attachment_1_month_ago['size'],
                    'attachments_6_month_ago' => $messages_6_month_ago['count']+$notes_6_month_ago['count']+$attachments_6_month_ago['count'],
                    'attachments_6_month_ago_size' => $messages_6_month_ago['size']+$notes_6_month_ago['size']+$attachments_6_month_ago['size'],
                    'attachments_year_ago' =>$messages_year_ago['count']+$notes_year_ago['count']+$attachments_year_ago['count'] ,
                    'attachments_year_size' =>$messages_year_ago['size']+$notes_year_ago['size']+$attachments_year_ago['size'] ,
                    'attachments_everything' =>  $messages_everything['count']+$notes_everything['count']+$attachments_everything['count'],
                    'attachments_everything_size' =>  $messages_everything['size']+$notes_everything['size']+$attachments_everything['size'],
                )
            )
        );
   }
   if(Tools::isSubmit('submitSendMail') && $mail_conversation_id=Tools::getValue('mail_conversation_id'))
   {
        $ets_livechat->updateLastAction();
        $ets_livechat->_submitSendMail($mail_conversation_id);
   }
}
else
{
    die(Tools::jsonEncode(array(
        'html' => '',
        'error' => $ets_livechat->displayError('You has been logged out'),
    )));  
}
?>