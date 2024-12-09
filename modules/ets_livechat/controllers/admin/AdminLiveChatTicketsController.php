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

if (!defined('_PS_VERSION_'))
    exit;
class AdminLiveChatTicketsController extends ModuleAdminController
{
    public $errors= array();
    public $_module;
    public function __construct()
    {
       parent::__construct();
       $this->bootstrap = true;
       $this->_module = new Ets_livechat();
       $this->context = Context::getContext();
       $this->context->controller->_conf[444]= $this->module->l('Transfer successfull');
    }
    public function initContent()
    {
        if(Tools::isSubmit('deleteticket') && $id_ticket=Tools::getValue('id_ticket'))
        {
            if($this->checkAccesTicket($id_ticket))
            {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE id_message='.(int)$id_ticket);
                Db::getInstance()->execute('DELETE  FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_field WHERE id_message='.(int)$id_ticket);
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets').'&conf=2');
            }
            else
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets'));
            
        }
        if(Tools::isSubmit('changestatus') && in_array(Tools::getValue('changestatus'),array('open','close','cancel')) &&  $id_ticket=Tools::getValue('id_ticket'))
        {
            if($this->checkAccesTicket($id_ticket))
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_message SET status="'.pSQL(Tools::getValue('changestatus')).'" WHERE id_message='.(int)$id_ticket);
                if(Tools::isSubmit('viewticket'))
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets').'&viewticket&id_ticket='.(int)$id_ticket.'&conf=4');
                else
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets').'&conf=4');
            }
            else
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets'));
            
        }
        if(Tools::isSubmit('change_priority')  && $id_ticket=Tools::getValue('id_ticket'))
        {
            if($this->checkAccesTicket($id_ticket))
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_message SET priority="'.(int)Tools::getValue('ticket_priority').'" WHERE id_message='.(int)$id_ticket);
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets').'&viewticket&id_ticket='.(int)$id_ticket.'&conf=4');
            }
            else
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets'));
        }
        if(Tools::isSubmit('transfer_ticket') && $id_ticket=Tools::getValue('id_ticket'))
        {
            if($this->checkAccesTicket($id_ticket))
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_message SET id_departments="'.(int)Tools::getValue('id_departments_ticket').'",id_employee="'.(int)Tools::getValue('id_employee_ticket').'",readed="0" WHERE id_message='.(int)$id_ticket);
                if($this->checkAccesTicket($id_ticket))
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets').'&viewticket&id_ticket='.(int)$id_ticket.'&conf=444');
                else
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets').'&conf=444');
            }
            else
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets'));
        }
        if(Tools::isSubmit('submitBulkActionTicket') && Tools::getValue('bulk_action_ticket') && $ticket_readed = array_keys(Tools::getValue('ticket_readed')))
        {
            if(Tools::getValue('bulk_action_ticket')=='mark_as_read')
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_message SET readed="1" WHERE id_message IN ('.implode(',',array_map('intval',$ticket_readed)).')');
                die(
                    Tools::jsonEncode(
                        array(
                            'url_reload' => $this->context->link->getAdminLink('AdminLiveChatTickets').'&conf=4',
                        )
                    )
                );  
            }
            elseif(Tools::getValue('bulk_action_ticket')=='mark_as_unread')
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_message SET readed="0" WHERE id_message IN ('.implode(',',array_map('intval',$ticket_readed)).')');
                die(
                    Tools::jsonEncode(
                        array(
                            'url_reload' => $this->context->link->getAdminLink('AdminLiveChatTickets').'&conf=4',
                        )
                    )
                );
            }
            elseif(Tools::getValue('bulk_action_ticket')=='delete_selected')
            {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE id_message IN ('.implode(',',array_map('intval',$ticket_readed)).')');
                //die('DELETE FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE id_message IN ('.implode(',',array_map('intval',$ticket_readed)).')');
                die(
                    Tools::jsonEncode(
                        array(
                            'url_reload' => $this->context->link->getAdminLink('AdminLiveChatTickets').'&conf=2',
                        )
                    )
                );
            }
            
        }
        if(Tools::isSubmit('lc_send_message_ticket') && $id_ticket=Tools::getValue('id_ticket'))
        {
            $note= Tools::getValue('ticket_note');
            if(!Validate::isCleanHtml($note))
                $this->errors[] = $this->l('Message is invalid');
            $attachments=array();
            $ticket_file='';
            $name_file='';
            $id_form= Db::getInstance()->getValue('SELECT id_form FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE id_message='.(int)$id_ticket);
            $form_class= new LC_Ticket_form($id_form,$this->context->language->id);
            if(isset($_FILES['ticket_file']['tmp_name']) && isset($_FILES['ticket_file']['name']) && $_FILES['ticket_file']['name'])
            {
                $attachment=array(
                    'rename' =>  uniqid().Tools::strtolower(Tools::substr($_FILES['ticket_file']['name'], -5)),
                    'content' => Tools::file_get_contents($_FILES['ticket_file']['tmp_name']),
                    'tmp_name' => $_FILES['ticket_file']['tmp_name'],
                    'name' =>$_FILES['ticket_file']['name'],
                    'mime' => $_FILES['ticket_file']['type'],
                    'error' => $_FILES['ticket_file']['error'],
                    'size' => $_FILES['ticket_file']['size'],
                );
                $attachments[]=$attachment;
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['ticket_file']['name'], '.'), 1));
                $ticket_file = $_FILES['ticket_file']['name'];
                if($form_class->save_staff_file)
                {
                    $name_file=$ticket_file;
                    $fileName = dirname(__FILE__).'/../../downloads/'.$name_file;   
                    if(!in_array($type,$this->module->file_types))
                    {
                        $this->errors[] = $this->module->l('File upload is invalid');
                    }
                    if(file_exists($fileName))
                    {
                        $time=md5(time());
                        for($i=0;$i<6;$i++)
                        {
                            $index =rand(0,Tools::strlen($time)-1);
                            $name_file =$time[$index].$name_file;
                        }
                        $fileName = dirname(__FILE__).'/../../downloads/'.$name_file;
                    }              
                    if(file_exists($fileName))
                    {
                        $this->errors[] = $this->module->l('Avata already exists. Try to rename the file then reupload');
                    }
                    else
                    { 				
            				if (!$fileName || !move_uploaded_file($_FILES['ticket_file']['tmp_name'], $fileName))
            					$this->errors[] = $this->module->l('Can not upload the file');
                    }
                }
                
            }
            elseif(trim(strip_tags($note))=='' && trim($note)!='')
            {
                $this->errors[] = $this->module->l('Message is valid');
            }
            if($this->errors)
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'error' => $this->module->displayError($this->errors),
                        )
                    )
                );
            }
            else
            {
                $note_class= new LC_Note();
                $note_class->id_message = $id_ticket;
                $note_class->id_employee=(int)$this->context->employee->id;
                $note_class->note = Tools::nl2br(trim(strip_tags($note)));
                $note_class->file_name = $ticket_file;
                if($note_class->add())
                {
                    $ticket = new LC_Ticket($id_ticket);
                    $ticket->date_admin_update= date('Y-m-d h:i:s');
                    $ticket->update();
                    if($note_class->file_name)
                    {
                        if($form_class->save_staff_file)
                        {
                            $download= new LC_Download();
                            $download->id_note= $note_class->id;
                            $download->filename=$name_file;
                            $download->file_type=$_FILES['ticket_file']['type'];
                            $download->file_size = $_FILES['ticket_file']['size']/1048576;
                            if($download->add())
                            {
                                $note_class->id_download= $download->id;
                                $note_class->update();
                            } 
                        }
                        else
                        {
                            $note_class->id_download= -1;
                            $note_class->update();
                        }   
                    }
                    $send_mail = false;
                    if($form_class->send_mail_reply_customer && $customer= $this->module->getEmailCustomer($id_ticket))
                    {
                        $signature= Db::getInstance()->getValue('SELECT signature FROM '._DB_PREFIX_.'ets_livechat_staff WHERE id_employee='.(int)$this->context->employee->id);
                        $template_vars=array(
                            '{mail_content}' => $note .($signature ? '<br/>-----<br/><span class="employee_signature">'.$signature.'</span>':''),
                            '{customer_name}' => $customer['name'],
                            '{employee_name}' => $this->context->employee->firstname.' '.$this->context->employee->lastname,
                            '{link_ticket}' => $this->context->link->getModuleLink($this->module->name,'ticket',array('viewticket'=>1,'id_ticket'=>$ticket->id)),
                        );
                        if(Mail::Send(
                			$this->context->language->id,
                			'reply_ticket_to_customer',
                            Configuration::get('PS_SHOP_NAME').$this->module->l(' just replied to your ticket '),
                			$template_vars,
        			        $customer['email'],
                			$customer['name']? $customer['name'] : null,
                			Configuration::get('PS_SHOP_EMAIL'),
                			Configuration::get('PS_SHOP_NAME'),
                			$attachments,
                			null,
                			dirname(__FILE__).'/../../mails/',
                			false,
                			$this->context->shop->id,
                            null,
                            null,
                            null
                        ))
                        $send_mail=true;
                    }
                    die(
                        Tools::jsonEncode(
                            array(
                                'error'=>false,
                                'id_note' => $note_class->id,
                                'messages' => $this->module->getMessagesTicket($id_ticket,'DESC',2),
                                'success'=> ($send_mail ? $this->l('Message sent. An email notification was successfully sent to') : $this->l('Message sent')).($send_mail ? ' '.$customer['email'] :''),
                            )
                        )
                    );
                }
            }
        }
        parent::initContent();
    }
    public function renderList() 
    {
        $this->context->smarty->assign(
            array(
                'menu_top' => $this->module->displayMenuTop(),
                'link'=> $this->context->link,
            )
        );
        $ets_livechat= Module::getInstanceByName('ets_livechat');
        if(Tools::isSubmit('viewticket') && $id_ticket=Tools::getValue('id_ticket'))
        {
            if($ticket= $this->checkAccesTicket($id_ticket))
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_message SET readed=1 WHERE id_message='.(int)$id_ticket);
                $messages = $this->module->getMessagesTicket($ticket);
                $fields = Db::getInstance()->executeS('
                SELECT mf.value,fl.label,f.type,f.is_contact_name,mf.id_download FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_field mf
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field f ON (f.id_field=mf.id_field)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field_lang fl ON (mf.id_field=fl.id_field)
                WHERE mf.id_message='.(int)$id_ticket.' AND fl.id_lang='.(int)$this->context->language->id);
                if($fields)
                {
                    foreach($fields as &$field)
                    {
                        if($field['type']=='file' && $field['value'])
                        {
                            if($field['id_download'] >0)
                            {
                                $download = new LC_Download($field['id_download']);
                                $field['file_size'] = $download->file_size;
                            }
                            $field['link_download'] =$ets_livechat->getBaseLink().'/modules/ets_livechat/download.php?downloadfile='.md5(_COOKIE_KEY_.$field['id_download']);
                        }
                    }
                }
                $employees=  Db::getInstance()->executeS(
                'SELECT e.*,d.id_departments,s.name,s.avata,IFNULL(s.status,1) as status FROM '._DB_PREFIX_.'employee e 
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (de.id_employee=e.id_employee)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.id_departments = de.id_departments)
                LEFT JOIN '._DB_PREFIX_.'ets_livechat_staff s ON (s.id_employee=e.id_employee) 
                WHERE e.active=1 GROUP BY e.id_employee');
                if($employees)
                {
                    foreach($employees as &$employee)
                    {
                        $employe_departments= Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments_employee WHERE id_employee='.(int)$employee['id_employee']);
                        $employee['departments']=$employe_departments;
                    }
                }
                $this->context->smarty->assign(
                     array(
                        'ticket' => $ticket,
                        'messages' => $messages,
                        'fields' => $fields,
                        'employees'=>$employees,
                        'departments' => $this->module->getDepartments(),
                        'form_class' => new LC_Ticket_form($ticket['id_form']),
                        'reply_customer'=> $this->module->getEmailCustomer($ticket),
                        'ETS_LC_AVATAR_IMAGE_TYPE'=> Configuration::get('ETS_LC_AVATAR_IMAGE_TYPE'),
                        'link_basic' => $this->module->getBaseLink(),
                     ) 
                );
                return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'ticket.tpl');
            }
            else
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets'));
        }
        if(Tools::isSubmit('addticket') && $id_form=Tools::getValue('id_form'))
        {
            if(Tools::isSubmit('submit_send_ticket'))
            {
                $form_class = new LC_Ticket_form($id_form);
                if($this->checkSubmitForm($form_class))
                {
                    $customer_name='';
                    $customer_email ='';
                    $attachments =array();
                    $ticket= new LC_Ticket();
                    $ticket->id_form = Tools::getValue('id_form');
                    $ticket->id_shop = $this->context->shop->id;
                    $ticket->id_customer = (int)Tools::getValue('id_customer_ticket');
                    $ticket->id_departments = Tools::getValue('id_departments');
                    $ticket->id_employee = Tools::getValue('ticket_id_employee');
                    if(Tools::getValue('id_customer_ticket'))
                        $customer_class= new Customer(Tools::getValue('id_customer_ticket'));
                    $ticket->status= 'open';
                    $ticket->priority = (int)$form_class->default_priority;
                    $ticket->date_admin_update = date('Y-m-d h:i:s');
                    $ticket->date_customer_update = date('Y-m-d h:i:s');
                    $ticket->date_add=date('Y-m-d h:i:s');
                    $ticket->add();
                    if($id_message = $ticket->id)
                    {
                        if($fields = Tools::getValue('fields'))
                        {
                            $is_contact_name=false;
                            $is_contact_email=false;
                            $is_customer_phone_number=false;
                            foreach($fields as $id_field=> $field)
                            {
                                $field_class= new LC_Ticket_field($id_field,$this->context->language->id);
                                
                                if($field && $field_class->type=='text' && $field_class->is_contact_name && !$is_contact_name)
                                {
                                    if(isset($customer_class) && $customer_class->id)
                                    {
                                        $field= $customer_class->firstname.' '.$customer_class->lastname;
                                    }
                                    $is_contact_name=true;
                                    $customer_name=$field;
                                }
                                if($field && $field_class->type=='email' && $field_class->is_contact_mail && !$is_contact_email)
                                {
                                    if(isset($customer_class) && $customer_class->id)
                                    {
                                        $field= $customer_class->email;
                                    }
                                    $is_contact_email=true;
                                    $customer_email=$field;
                                }
                                if($field_class->type=='phone_number' && $field_class->is_customer_phone_number && !$is_customer_phone_number)
                                {
                                    if(isset($customer_class) && $customer_class->id)
                                    {
                                        $addresses = $customer_class->getAddresses($this->context->language->id);
                                        if($addresses)
                                        {
                                            $field = $addresses[0]['phone'] ? $addresses[0]['phone'] : $addresses[0]['phone_mobile'];
                                            $is_customer_phone_number=true;
                                        }
                                        
                                    }
                                }
                                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_ticket_form_message_field(id_message,id_field,value) VALUES("'.(int)$id_message.'","'.(int)$id_field.'","'.pSQL(strip_tags($field)).'")');
                            }
                        }
                        if(isset($_FILES['fields']) && $_FILES['fields'])
                        {
                              $name_files= $_FILES['fields']['name'];
                              $type_files =$_FILES['fields']['type'];
                              $tmp_name_files = $_FILES['fields']['tmp_name'];
                              $size_files= $_FILES['fields']['size'];
                              $error_files=$_FILES['fields']['error'];
                              foreach($name_files as $id_field=> $name_file)
                              {
                                $field_class= new LC_Ticket_field($id_field,$this->context->language->id);
                                if($name_files[$id_field])
                                {
                                    $attachment=array(
                                        'rename' =>  uniqid().Tools::strtolower(Tools::substr($name_files[$id_field], -5)),
                                        'content' => Tools::file_get_contents($tmp_name_files[$id_field]),
                                        'tmp_name' => $tmp_name_files[$id_field],
                                        'name' =>$name_file,
                                        'mime' => $type_files[$id_field],
                                        'error' => $error_files[$id_field],
                                        'size' => $size_files[$id_field],
                                    );
                                    $attachments[]=$attachment;
                                    $file_name= $name_files[$id_field];
                                    if($form_class->save_customer_file)
                                    {
                                        $fileupload_name = sha1(microtime()).'-'.$name_files[$id_field];
                                        if (!move_uploaded_file($tmp_name_files[$id_field], dirname(__FILE__).'/../../downloads/'.$fileupload_name))
                                            $this->errors[] = $this->module->l('Can not upload the file').' '.$field_class->label;
                                        else
                                        {
                                            $download = new LC_Download();
                                            $download->id_ticket = $id_message;
                                            $download->filename = $fileupload_name;
                                            $download->file_type= $type_files[$id_field];
                                            $download->file_size = $size_files[$id_field]/1048576;
                                            $download->id_field= $id_field;
                                            $download->add();
                                            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_ticket_form_message_field(id_message,id_field,id_download,value) VALUES("'.(int)$id_message.'","'.(int)$id_field.'","'.(int)$download->id.'","'.pSQL($file_name).'")');
                                        }
                                    }
                                    else
                                       Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_livechat_ticket_form_message_field(id_message,id_field,id_download,value) VALUES("'.(int)$id_message.'","'.(int)$id_field.'","-1","'.pSQL($file_name).'")'); 
                                }
                              }
                        }
                        $ticket->subject = $this->module->getSubjectMessageTicket($ticket->id);
                        $ticket->update();
                    }
                    else
                        $this->errors[] = $this->module->l('Add ticket error');
                    if(!$this->errors)
                    {
                        if(!$customer_name && isset($customer_class))
                            $customer_email= $customer_class->email;
                        if($form_class->mail_new_ticket)
                        {
                            $email_info= $form_class->getEmailAdminInfo(Tools::getValue('id_departments'));
                            if($email_info && $mails_to=$email_info['mails_to'])
                            {
                                $names_to= $email_info['names_to'];
                                $template_vars=array(
                                    '{mail_content}' => $this->module->displayMessageTicket($id_message,true),
                                    '{customer}' => $customer_name ? $customer_name : ($customer_email ? $customer_email: Configuration::get('PS_SHOP_NAME')),
                                );
                                if(!Mail::Send(
                        			$this->context->language->id,
                        			'admin_new_ticket_admin',
                                    $this->module->l('A new ticket has been submitted','form'),
                        			$template_vars,
                			        $mails_to,
                        			$names_to? $names_to : null,
                        			$customer_email ? $customer_email:null,
                        			$customer_name ? $customer_name :Configuration::get('PS_SHOP_NAME'),
                        			$attachments,
                        			null,
                        			dirname(__FILE__).'/../../mails/',
                        			false,
                        			$this->context->shop->id,
                                    null,
                                    $customer_email? $customer_email :null,
                                    $customer_name ? $customer_name :null
                                ))
                                $this->errors[]=$this->module->l('An error occurred while sending the message, please try again.');
                            }
                        }
                        if($form_class->send_mail_to_customer && $customer_email)
                        {
                            $template_vars=array(
                                '{mail_content}' => $this->module->displayMessageTicket($id_message),
                                '{staff}' => $this->context->employee->firstname.' '.$this->context->employee->lastname,
                                '{subject}' => $ticket->subject,
                            );
                            Mail::Send(
                    			$this->context->language->id,
                    			'admin_new_ticket_customer',
                                $this->module->l('A new ticket has been submitted'),
                    			$template_vars,
            			        $customer_email,
                    			$customer_email? $customer_email : (isset($customer_class) ? $customer_class->firstname.' '.$customer_class->lastname:''),
                    			Configuration::get('PS_SHOP_EMAIl'),
                    			Configuration::get('PS_SHOP_NAME'),
                    			$attachments,
                    			null,
                    			dirname(__FILE__).'/../../mails/',
                    			false,
                    			$this->context->shop->id,
                                null
                            );
                        }
                    }
                    if(!$this->errors && $ticket->id)
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets').'&viewticket=1&id_ticket='.$ticket->id);
                }
                $this->context->smarty->assign(
                    array(
                        'errors' => $this->errors ? $this->module->displayError($this->errors):'',
                        'success'=> $this->errors ? false : $this->module->l('Tiket created successfully'),
                        
                    )
                );
            }
            $this->context->smarty->assign(
                array(
                    'form_html' => $this->module->renderHtmlForm($id_form,0,true),
                )
            );
            return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'add_ticket.tpl');
        }
        $filter ='';
        $post_value=array();
        if(Tools::getValue('id_ticket'))
        {
            $filter .=' AND fm.id_message='.(int)Tools::getValue('id_ticket');
            $post_value['id_ticket']=Tools::getValue('id_ticket');
        }
        if(Tools::getValue('customer_name'))
        {
            $filter .=' AND (CONCAT(c.firstname," ",c.lastname) LIKE "'.pSQL(Tools::getValue('customer_name')).'%" OR fmf2.value LIKE "'.pSQL(Tools::getValue('customer_name')).'%") ';
            $post_value['customer_name']=Tools::getValue('customer_name');
        }
        if(Tools::getValue('customer_email'))
        {
            $filter .=' AND (c.email LIKE "'.pSQl(Tools::getValue('customer_email')).'%" OR fmf1.value LIKE "'.pSQl(Tools::getValue('customer_email')).'%")';
            $post_value['customer_email'] = Tools::getValue('customer_email');
        }
        if(Tools::getValue('form_title'))
        {
            $filter .=' AND fl.title LIKE "%'.pSQL(Tools::getValue('form_title')).'%"';
            $post_value['form_title'] = Tools::getValue('form_title');
        }
        if(Tools::getValue('priority'))
        {
            $filter .= ' AND fm.priority="'.(int)Tools::getValue('priority').'" ';
            $post_value['priority'] = (int)Tools::getValue('priority');
        }
        if(Tools::getValue('status'))
        {
            $filter .= ' AND fm.status="'.pSQL(Tools::getValue('status')).'"';
            $post_value['status'] = Tools::getValue('status');
        }
        if(Tools::getValue('date_add_from'))
        {
            $filter .=' AND fm.date_customer_update >= "'.pSQL(Tools::getValue('date_add_from')).' 00:00:00"';
            $post_value['date_add_from'] = Tools::getValue('date_add_from');
        }
        if(Tools::getValue('date_add_to'))
        {
            $filter .=' AND fm.date_customer_update <= "'.pSQL(Tools::getValue('date_add_to')).' 00:00:00"';
            $post_value['date_add_to'] = Tools::getValue('date_add_to');
        }
        if(Tools::getValue('subject'))
        {
            $filter .=' AND fm.subject LIKE "'.pSQL(Tools::getValue('subject')).'%"';
            $post_value['subject'] = Tools::getValue('subject');
        }
        if(Tools::getValue('replied')!='')
        {
            $filter .=' AND fm.replied ='.(int)Tools::getValue('replied');
            $post_value['replied'] = Tools::getValue('replied');
        }
        $sort =Tools::getValue('sort','date_customer_update');
        $sort_type = Tools::getValue('sort_type','desc');
        $page = (int)Tools::getValue('page') && (int)Tools::getValue('page') > 0 ? (int)Tools::getValue('page') : 1;
        $totalRecords = (int)$this->getListTickets(true,$filter);
        $paggination = new LC_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getAdminLink('AdminLiveChatTickets').'&page=_page_'.$this->getUrlExtra($post_value);
        $paggination->limit =  20;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->module->l('links');
        $paggination->style_results = $this->module->l('results');
        $tickets= $this->getListTickets(false,$filter,$sort,$sort_type,$paggination->limit,$start);
        $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form f
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form =fl.id_form)
        WHERE f.id_shop= "'.(int)$this->context->shop->id.'" AND fl.id_lang = "'.(int)$this->context->language->id.'" AND active=1 AND f.id_form!=1 AND deleted=0 ORDER BY f.sort_order ASC';
        $forms= Db::getInstance()->executeS($sql);
        if($forms)
        {
            foreach($forms as &$form)
            {
                $form['link']= $this->context->link->getAdminLink('AdminLiveChatTickets').'&addticket&id_form='.$form['id_form'];
            }
        }
        $this->context->smarty->assign(
            array(
                'tickets' => $tickets,
                'post_value' => $post_value,
                'sort'=>$sort,
                'sort_type' => $sort_type,
                'pagination_text' => $paggination->render(),
                'forms' => $forms,
                'new_ticket_link' => count($forms)==1 ? $forms[0]['link'] :false,
                'ps16' =>version_compare(_PS_VERSION_, '1.6', '>='),
                'totalRecords' => $totalRecords,
            )
        );
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'tickets.tpl');
    }
    public function getListTickets($count=false,$filter=false,$sort=false,$sort_type=false,$limit=false,$start=0)
    {
        if($this->context->employee->id_profile==1)
           $sql= 'SELECT fm.*,fl.title,c.firstname,c.lastname,c.email,f.default_priority ,fmf1.value as email_customer, fmf2.value as name_customer FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message fm
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form f ON (fm.id_form=f.id_form)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON (fm.id_customer=c.id_customer)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field ff1 ON (ff1.id_form =f.id_form AND ff1.is_contact_mail=1)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field ff2 ON (ff2.id_form =f.id_form AND ff2.is_contact_name=1)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_message_field fmf1 ON (fmf1.id_message= fm.id_message AND fmf1.id_field=ff1.id_field)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_message_field fmf2 ON (fmf2.id_message= fm.id_message AND fmf2.id_field=ff2.id_field)
            WHERE 1 '.(!$this->module->all_shop ? ' AND fm.id_shop="'.(int)$this->context->shop->id.'"':'').($filter ? $filter :'').'
            GROUP BY fm.id_message'
            .($sort ? ' ORDER BY '.pSQL($sort):'').' '.($sort_type && $sort ? pSQL($sort_type) :'')
            . ($limit ? ' LIMIT '.(int)$start.','.(int)$limit :''); 
        else
            $sql = 'SELECT fm.*,fl.title,c.firstname,c.lastname,c.email,f.default_priority,fmf1.value as email_customer, fmf2.value as name_customer FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message fm
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form f ON (fm.id_form=f.id_form)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON (fm.id_customer=c.id_customer)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (fm.id_departments = d.id_departments)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (d.id_departments=de.id_departments)
             LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field ff1 ON (ff1.id_form =f.id_form AND ff1.is_contact_mail=1)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_field ff2 ON (ff2.id_form =f.id_form AND ff2.is_contact_name=1)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_message_field fmf1 ON (fmf1.id_message= fm.id_message AND fmf1.id_field=ff1.id_field)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_message_field fmf2 ON (fmf2.id_message= fm.id_message AND fmf2.id_field=ff2.id_field)
            WHERE (fm.id_departments <=0  OR de.id_employee="'.(int)$this->context->employee->id.'" OR d.all_employees=1) AND (fm.id_employee<=0 OR fm.id_employee="'.(int)$this->context->employee->id.'") '.(!$this->module->all_shop ? ' AND fm.id_shop="'.(int)$this->context->shop->id.'"':'').($filter ? $filter :'').'
            GROUP BY fm.id_message'
            .($sort ? ' ORDER BY '.pSQL($sort):'').' '.($sort_type && $sort ? pSQL($sort_type) :'')
            .($limit ? ' LIMIT '.(int)$start.','.(int)$limit :'');
        $tickets= Db::getInstance()->executeS($sql);
        if($tickets)
        {
            foreach($tickets as &$ticket)
            {
                $ticket['replied'] = $this->checkTicketReplied($ticket['id_message']);
            }
        }
        if($count)
            return Count($tickets);
        return $tickets;
    }
    public function checkAccesTicket($id_ticket)
    {
         if($this->context->employee->id_profile==1)
            $sql= 'SELECT fm.*,fl.title,c.firstname,c.lastname,c.email,f.default_priority FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message fm
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form f ON (fm.id_form=f.id_form)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON (fm.id_customer=c.id_customer)
            WHERE fm.id_message="'.(int)$id_ticket.'"'.(!$this->module->all_shop ? ' AND fm.id_shop="'.(int)$this->context->shop->id.'"':'').'
            GROUP BY fm.id_message'; 
         else
            $sql = 'SELECT fm.*,fl.title,c.firstname,c.lastname,c.email,f.default_priority FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message fm
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form f ON (fm.id_form=f.id_form)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_lang fl ON (f.id_form=fl.id_form AND fl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON (fm.id_customer=c.id_customer)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (fm.id_departments = d.id_departments)
            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (d.id_departments=de.id_departments)
            WHERE fm.id_message="'.(int)$id_ticket.'"'.(!$this->module->all_shop ? ' AND fm.id_shop="'.(int)$this->context->shop->id.'"':'').' AND (fm.id_departments<=0 OR de.id_employee="'.(int)$this->context->employee->id.'" OR d.all_employees=1) AND (fm.id_employee<=0 OR fm.id_employee="'.(int)$this->context->employee->id.'") GROUP BY fm.id_message';
         $ticket = Db::getInstance()->getRow($sql);
		if($ticket)
		{
			 if($ticket['id_departments']>0)
			 {
					$department  = new LC_Departments($ticket['id_departments']);
					$ticket['dertpartment_name'] = $department->name;
			 }
			 else
				$ticket['dertpartment_name'] = $this->l('All departments');
			 if($ticket['id_employee']>0)
			 {
				$employee = new Employee($ticket['id_employee']);
				$ticket['employee_name'] = $employee->firstname.' '.$employee->lastname;
			 }
			 return $ticket;
		}
		return false;
    }
    public function getUrlExtra($post_value)
    {
        if($post_value)
        {
            $url ='';
            foreach($post_value as $key=>$value)
            {
                $url .='&'.$key.'='.$value;
            }
            return $url;
        }
        return '';
    }
    public function checkSubmitForm($form_class)
    {
        if($form_class->getDepartments() && !Tools::getValue('id_departments'))
            $this->errors[]= $this->module->l('Department is required');
        if(!$form_class->allow_user_submit && !Tools::getValue('id_customer_ticket'))
            $this->errors[]= $this->module->l('Customer is required');
        if($fields = Tools::getValue('fields'))
        {
            foreach($fields as $id_filed=> $field)
            {
                $field_class= new LC_Ticket_field($id_filed,$this->context->language->id);
                if($field_class->required && !$field)
                    $this->errors[]= $field_class->label.' '.$this->module->l('is required');
                if(((($field_class->type=='text' || $field_class->type=='text_editor') && !Validate::isCleanHtml($field)) || ($field_class->type=='email' && !Validate::isEmail($field)) || ($field_class->type=='phone_number' && !Validate::isPhoneNumber($field))) && $field)
                    $this->errors[] = $field_class->label.' '.$this->module->l('is invalid');
            }
        }
        if(isset($_FILES['fields']) && $_FILES['fields'])
        {
              $name_files= $_FILES['fields']['name'];
              //$size_files= $_FILES['fields']['size'];
              foreach($name_files as $id_filed=> $name_file)
              {
                   $field_class= new LC_Ticket_field($id_filed,$this->context->language->id);
                   if($field_class->required && !$name_file)
                        $this->errors[]= $field_class->label.' '.$this->module->l('is required'); 
                   if($name_file)
                   {
                        $fileType = Tools::strtolower(pathinfo($name_file,PATHINFO_EXTENSION));
                        if(!in_array($fileType,$this->module->file_types))
                        {
                            $this->errors[] = $field_class->label.' '.$this->module->l('is invalid');
                        }
                   }
                   
              }
        }
        if($this->errors)
            return false;
        else
            return true;
    }
    public function checkTicketReplied($id_message)
    {
        $note = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_note WHERE id_message='.(int)$id_message.' ORDER BY id_note DESC');
        if($note && $note['id_employee']==1)
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_message SET replied=1 WHERE id_message='.(int)$id_message);
            return true;
        }
        else
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_livechat_ticket_form_message SET replied=0 WHERE id_message='.(int)$id_message);
            return false;
        }
    }
}