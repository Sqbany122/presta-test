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
if(!class_exists('LC_Conversation') && file_exists(dirname(__FILE__).'/../../classes/LC_Conversation.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Conversation.php');
if(!class_exists('LC_Message') && file_exists(dirname(__FILE__).'/../../classes/LC_Message.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Message.php');
if(!class_exists('LC_Download') && file_exists(dirname(__FILE__).'/../../classes/LC_Download.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Download.php');
if(!class_exists('LC_Departments') && file_exists(dirname(__FILE__).'/../../classes/LC_Departments.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Departments.php');
if(!class_exists('LC_Ticket_form') && file_exists(dirname(__FILE__).'/../../classes/LC_Ticket_form.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Ticket_form.php');
if(!class_exists('LC_Ticket_field') && file_exists(dirname(__FILE__).'/../../classes/LC_Ticket_field.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Ticket_field.php');
if(!class_exists('LC_Ticket') && file_exists(dirname(__FILE__).'/../../classes/LC_Ticket.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Ticket.php');
class Ets_livechatFormModuleFrontController extends ModuleFrontController
{
    public $_errors;
    public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
	}
	public function init()
	{
		parent::init();
        //Sorry, you do not have permission');
	}
	public function initContent()
	{
	    parent::initContent();
        if(($id_form= (int)Tools::getValue('id_form')) && $this->module->_checkEixtForm($id_form))
        {
            $form_class= new LC_Ticket_form($id_form,$this->context->language->id);
            if(!$this->context->customer->logged && !$form_class->allow_user_submit)
                Tools::redirectLink($this->context->link->getPageLink('authentication',null,null,array('back'=>urldecode($this->module->getFormLink($id_form)))));
            if($this->module->is17)
            {
                $body_classes = array(
                    'lang-'.$this->context->language->iso_code => true,
                    'lang-rtl' => (bool) $this->context->language->is_rtl,
                    'country-'.$this->context->country->iso_code => true,                                   
                );
                $page = array(
                    'title' => '',
                    'canonical' => '',
                    'meta' => array(
                        'title' => $form_class->meta_title ? $form_class->meta_title : $form_class->title,
                        'description' => $form_class->meta_description,
                        'keywords' => $form_class->meta_keywords,
                        'robots' => 'index',
                    ),
                    'page_name' => 'lc_form_page',
                    'body_classes' => $body_classes,
                    'admin_notifications' => array(),
                ); 
                $this->context->smarty->assign(array('page' => $page)); 
            }    
            else
            {
                $this->context->smarty->assign(
                    array(
                        'meta_title'=> $form_class->meta_title ? $form_class->meta_title : $form_class->title,
                        'meta_description'=> $form_class->meta_description,
                        'meta_keywords' =>$form_class->meta_keywords,
                    )
                );
            }
            if(Tools::isSubmit('submit_send_ticket'))
            {
                if($this->checkSubmitForm($form_class))
                {
                    $customer_name='';
                    $customer_email ='';
                    $attachments =array();
                    $ticket= new LC_Ticket();
                    $ticket->id_form = Tools::getValue('id_form');
                    $ticket->id_shop = $this->context->shop->id;
                    $ticket->id_customer = (int)$this->context->customer->id;
                    $ticket->id_departments = Tools::getValue('id_departments');
                    $ticket->status= 'open';
                    $ticket->priority = (int)$form_class->default_priority;
                    $ticket->date_admin_update = date('Y-m-d H:i:s');
                    $ticket->date_customer_update = date('Y-m-d H:i:s');
                    $ticket->date_add=date('Y-m-d H:i:s');
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
                                    if($this->context->customer->logged)
                                    {
                                        $field= $this->context->customer->firstname.' '.$this->context->customer->lastname;
                                    }
                                    $is_contact_name=true;
                                    $customer_name=$field;
                                }
                                if($field && $field_class->type=='email' && $field_class->is_contact_mail && !$is_contact_email)
                                {
                                    if($this->context->customer->logged)
                                    {
                                        $field= $this->context->customer->email;
                                    }
                                    $is_contact_email=true;
                                    $customer_email=$field;
                                }
                                if($field_class->type=='phone_number' && $field_class->is_customer_phone_number && !$is_customer_phone_number)
                                {
                                    if($this->context->customer->logged)
                                    {
                                        $addresses = $this->context->customer->getAddresses($this->context->language->id);
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
                                            $this->_errors[] = $this->module->l('Can not upload the file','form').' '.$field_class->label;
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
                        $this->_errors[] = $this->module->l('Add ticket error');
                    if(!$this->_errors)
                    {
                        if($form_class->mail_new_ticket)
                        {
                            $email_info= $form_class->getEmailAdminInfo(Tools::getValue('id_departments'));
                            if($email_info && $mails_to=$email_info['mails_to'])
                            {
                                $names_to= $email_info['names_to'];
                                $template_vars=array(
                                    '{mail_content}' => $this->module->displayMessageTicket($id_message,true),
                                );
                                if(!Mail::Send(
                        			$this->context->language->id,
                        			'new_ticket_admin',
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
                                $this->_errors[]=$this->module->l('An error occurred while sending the message, please try again.','form');
                            }
                        }
                        if(!$customer_email && $this->context->customer->logged)
                            $customer_email = $this->context->customer->email;
                        if($form_class->send_mail_to_customer && $customer_email)
                        {
                            $template_vars=array(
                                '{mail_content}' => $this->module->displayMessageTicket($id_message),
                                '{customer_name}' => $customer_name ? $customer_name : $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                '{subject}' => $ticket->subject,
                                '{link_ticket}' => $this->context->link->getModuleLink($this->module->name,'ticket',array('viewticket'=>1,'id_ticket'=>$ticket->id)),
                            );
                            Mail::Send(
                    			$this->context->language->id,
                    			'new_ticket_customer',
                                $this->module->l('Your ticket has been submitted','form'),
                    			$template_vars,
            			        $customer_email,
                    			$customer_email? $customer_email : $this->context->customer->firstname.' '.$this->context->customer->lastname,
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
                    
                }
                $this->context->smarty->assign(
                    array(
                        'errors' => $this->_errors ? $this->module->displayError($this->_errors):'',
                        'success'=> $this->_errors ? false : $this->module->l('Message sent successfully','form'),
                        
                    )
                );
            }
            $this->context->smarty->assign(
                array(
                    'render_html_form' => $this->module->renderHtmlForm($id_form),
                    'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
                    'path' => $this->module->getBreadCrumb(),
                )
            );
            if($this->module->is17)
                $this->setTemplate('module:ets_livechat/views/templates/front/form.tpl');      
            else         
                $this->setTemplate('form16.tpl');
        }
        else
            die($this->module->l('Form not exists','form'));
          
    }
    public function checkSubmitForm($form_class)
    {
        if($form_class->getDepartments() && !Tools::getValue('id_departments'))
            $this->_errors[]= $this->module->l('Department is required','form');
        if($fields = Tools::getValue('fields'))
        {
            foreach($fields as $id_filed=> $field)
            {
                $field_class= new LC_Ticket_field($id_filed,$this->context->language->id);
                if($field_class->required && !$field)
                    $this->_errors[]= $field_class->label.' '.$this->module->l('is required','form');
                if(((($field_class->type=='text' || $field_class->type=='text_editor') && !Validate::isCleanHtml($field)) || ($field_class->type=='email' && !Validate::isEmail($field)) || ($field_class->type=='phone_number' && !Validate::isPhoneNumber($field))) && $field)
                    $this->_errors[] = $field_class->label.' '.$this->module->l('is invalid','form');
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
                        $this->_errors[]= $field_class->label.' '.$this->module->l('is required','form'); 
                   if($name_file)
                   {
                        $fileType = Tools::strtolower(pathinfo($name_file,PATHINFO_EXTENSION));
                        if(!in_array($fileType,$this->module->file_types))
                        {
                            $this->_errors[] = $field_class->label.' '.$this->module->l('is invalid','form');
                        }
                   }
                   
              }
        }
        if($form_class->allow_captcha && (!$this->context->customer->logged || !$form_class->customer_no_captcha))
        {
            if(!Tools::getValue('field_captcha'))
                $this->_errors[]= $this->module->l('Captcha code is required','form');
            else
            {
                $ets_lc_ticket_captcha_code= Tools::jsonDecode($this->context->cookie->ets_lc_ticket_captcha_code,true);
                if(isset($ets_lc_ticket_captcha_code[$form_class->id]) && $ets_lc_ticket_captcha_code[$form_class->id]!=Tools::getValue('field_captcha'))
                    $this->_errors[]= $this->module->l('Captcha code is not valid','form');
            }
        }
        if($this->_errors)
            return false;
        else
            return true;
    }
    
}