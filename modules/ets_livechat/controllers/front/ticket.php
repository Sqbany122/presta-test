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
class Ets_livechatTicketModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public $_errors= array();
    public $_sussecfull;
    public function __construct()
    {
    	parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
    	$this->context = Context::getContext();
    }
    public function init()
    {
    	parent::init();
    }
    public function initContent()
	{
        parent::initContent();
        if (!$this->context->customer->isLogged())   
            Tools::redirect('index.php?controller=authentication');
        $this->module->setMeta();
        if(Tools::isSubmit('set_rating_ticket') && $id_ticket=Tools::getValue('id_ticket'))
        {
            $ticket= new LC_Ticket($id_ticket);
            $ticket->rate= Tools::getValue('rating');
            $ticket->update();
            die(
                Tools::jsonEncode(
                    array(
                        'success' => $this->module->l('Rating sumitted','ticket'),
                    )
                )
            );
        }
        if(Tools::isSubmit('lc_send_message_ticket') && $id_ticket=Tools::getValue('id_ticket'))
        {
            if(!$this->module->checkAccesTicketFrontEnd($id_ticket))
                $this->_errors[]= $this->l('You do you send message','ticket');
            $note= trim(Tools::getValue('ticket_note')) ;
            if(!Validate::isCleanHtml($note))
                $this->_errors[]= $this->module->l('Message is invalid','ticket');
            $id_form= Db::getInstance()->getValue('SELECT id_form FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE id_message='.(int)$id_ticket);
            $form_class= new LC_Ticket_form($id_form,$this->context->language->id);
            $ticket_file='';
            $name_file='';
            $attachments=array();
            if($form_class->customer_reply_upload_file && isset($_FILES['ticket_file']['tmp_name']) && isset($_FILES['ticket_file']['name']) && $_FILES['ticket_file']['name'])
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
                if($form_class->save_customer_file)
                {
                    $name_file=$ticket_file;
                    $fileName = dirname(__FILE__).'/../../downloads/'.$name_file;   
                    if(!in_array($type,$this->module->file_types))
                    {
                        $this->_errors[] = $this->module->l('File upload is invalid');
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
                        $this->_errors[] = $this->module->l('Avata already exists. Try to rename the file then reupload');
                    }
                    else
                    { 				
    				    if (!$fileName || !move_uploaded_file($_FILES['ticket_file']['tmp_name'], $fileName))
        					$this->_errors[] = $this->module->l('Can not upload the file');
                    } 
                }
                   
            }
            elseif($note!='' && trim(strip_tags($note))=='' && !$this->_errors)
                $this->_errors[]= $this->module->l('Message is invalid','ticket');
            if($this->_errors)
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'error' => $this->module->displayError($this->_errors),
                        )
                    )
                );
            }
            else
            {
                $note_class= new LC_Note();
                $note_class->id_message = $id_ticket;
                $note_class->id_employee=0;
                $note_class->note = Tools::nl2br(trim(strip_tags($note)));
                $note_class->file_name = $ticket_file;
                if($note_class->add())
                {
                    $ticket = new LC_Ticket($id_ticket);
                    $ticket->date_customer_update= date('Y-m-d H:i:s');
                    $ticket->update();
                    if($note_class->file_name)
                    {
                        if($form_class->save_customer_file)
                        {
                            $download= new LC_Download();
                            $download->id_note= $note_class->id;
                            $download->filename=$name_file;
                            $download->file_type= $_FILES['ticket_file']['type'];
                            $download->size = $_FILES['ticket_file']['size']/1048576;
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
                    if($form_class->send_mail_reply_admin)
                    {
                        $template_vars=array(
                            '{mail_content}' => $note,
                            '{customer_name}' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
                        );
                        $email_info= $form_class->getEmailAdminInfo(Tools::getValue('id_departments'));
                        if($email_info && $mails_to=$email_info['mails_to'])
                        {
                            $names_to = $email_info['names_to'];
                            Mail::Send(
                    			$this->context->language->id,
                    			'reply_ticket_to_admin',
                                $this->module->l('Customer has just replied to a ticket'),
                    			$template_vars,
            			        $mails_to,
                    			$names_to? $names_to : null,
                    			Configuration::get('PS_SHOP_EMAIL'),
                    			$this->context->customer->firstname.' '.$this->context->customer->lastname,
                    			$attachments,
                    			null,
                    			dirname(__FILE__).'/../../mails/',
                    			false,
                    			$this->context->shop->id,
                                null,
                                null,
                                null
                            );
                       }
                    }
                    die(
                        Tools::jsonEncode(
                            array(
                                'error'=>false,
                                'id_note' => $note_class->id,
                                'messages' => $this->module->getMessagesTicket($id_ticket,'DESC',2),
                                'success'=> $this->module->displaySuccessMessage($this->module->l('Message sent')),
                            )
                        )
                    );
                }
            }
        }
        $this->context->smarty->assign(
            array(
                'errors_html'=>$this->_errors ? $this->module->displayError($this->_errors) : false,
                'sucsecfull_html' => $this->_sussecfull ? $this->module->displaySuccessMessage($this->_sussecfull):'',
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
                'path' => $this->module->getBreadCrumb(),
            )
        );
        if(Tools::isSubmit('viewticket') && $id_ticket=Tools::getValue('id_ticket'))
        {
            $this->context->smarty->assign(
                array(
                    'detail_ticket'=>$this->module->displayDetailTicket($id_ticket),
                )
            );
            if($this->module->is17)
                $this->setTemplate('module:ets_livechat/views/templates/front/ticket.tpl');      
            else         
                $this->setTemplate('ticket16.tpl');
        }
        else
        {
            $this->context->smarty->assign(
                array(
                    'list_ticket' => $this->module->getListTickets(),
                )
            );
            if($this->module->is17)
                $this->setTemplate('module:ets_livechat/views/templates/front/tickets.tpl');      
            else         
                $this->setTemplate('tickets16.tpl'); 
        }
    }
 }