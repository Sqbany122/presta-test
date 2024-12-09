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
class LC_Ticket_form extends ObjectModel
{
    public $active;
    public $id_shop;
    public $mail_new_ticket;
    public $custom_mail;
    public $send_mail_to_customer;
    public $send_mail_reply_customer;
    public $send_mail_reply_admin;
    public $customer_reply_upload_file;
    public $allow_user_submit;
    public $save_customer_file;
    public $save_staff_file;
    public $require_select_department;
    public $departments;
    public $allow_captcha;
    public $customer_no_captcha;
    public $default_priority;
    public $title;
    public $description;
    public $friendly_url;
    public $meta_title;
    public $meta_description;
	public $meta_keywords;
    public $deleted;
    public $sort_order;
    public $button_submit_label;
    public static $definition = array(
		'table' => 'ets_livechat_ticket_form',
		'primary' => 'id_form',
		'multilang' => true,
		'fields' => array(
            'active' => array('type' => self::TYPE_INT),
			'id_shop' => array('type' => self::TYPE_INT),
            'mail_new_ticket' =>	array('type' => self::TYPE_STRING,'validate' => 'isCleanHtml', 'size' => 500),
            'custom_mail' =>	array('type' => self::TYPE_STRING,'validate' => 'isCleanHtml', 'size' => 500),
            'send_mail_to_customer' =>	array('type' => self::TYPE_INT),
            'send_mail_reply_customer' =>	array('type' => self::TYPE_INT),
            'send_mail_reply_admin' =>	array('type' => self::TYPE_INT),
            'customer_reply_upload_file' =>	array('type' => self::TYPE_INT),
            'allow_user_submit' =>	array('type' => self::TYPE_INT),
            'save_customer_file' =>	array('type' => self::TYPE_INT),
            'save_staff_file' =>	array('type' => self::TYPE_INT),
            'require_select_department' =>	array('type' => self::TYPE_INT),
            'departments' =>	array('type' => self::TYPE_STRING,'validate' => 'isCleanHtml', 'size' => 500),
            'allow_captcha' =>	array('type' => self::TYPE_INT),
            'customer_no_captcha' =>	array('type' => self::TYPE_INT),
            'default_priority' => array('type' => self::TYPE_INT),
            'deleted' =>	array('type' => self::TYPE_INT),
            'sort_order' =>	array('type' => self::TYPE_INT),
            // Lang fields
            'title' =>	array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 500),
            'button_submit_label' =>array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 500),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 7000),
            'friendly_url' => array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 7000),   
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 700),         
			'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml','size' => 700),			
            'meta_keywords' =>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 900000),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public function add($autodate = true, $null_values = false)
	{
		$context = Context::getContext();
		$this->sort_order =1+ Db::getInstance()->getValue('SELECT MAX(sort_order) FROM '._DB_PREFIX_.'ets_livechat_ticket_form WHERE id_shop='.(int)$context->shop->id);
		return parent::add($autodate, $null_values);
	}
    public function delete()
    {
        $fileds = Db::getInstance()->executeS('SELECT id_field FROM '._DB_PREFIX_.'ets_livechat_ticket_form_field WHERE id_form='.(int)$this->id);
        if($fileds)
        {
            foreach($fileds as $filed)
            {
                $field_class= new LC_Ticket_field($filed['id_field']);
                $field_class->delete();
            }
        }
        return parent::delete();
    }
    public function duplicate()
    {
        $this->id = null; 
        if($this->add())
        {
            return $this->id;
        }
        return false;        
    }
    public function getEmailAdminInfo($id_departments=0)
    {
        if($id_departments)
            $id_departments=Tools::getValue('id_departments');
        if($form_mails=explode(',',$this->mail_new_ticket))
        {
            $mails_to=array();
            $names_to=array();
            if($form_mails)
            {
                foreach($form_mails as $form_mail)
                {
                    
                    $employees=array();
                    if($form_mail=='all_employees')
                    {
                        $employees = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'employee WHERE active=1');
                         
                    }
                    elseif($form_mail=='supper_admins' && !in_array('all_employee',$form_mails))
                    {
                        $employees = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'employee WHERE active=1 AND id_profile=1');
                    }
                    elseif($form_mail=='department' && $id_departments && !in_array('all_employee',$form_mails))
                    {
                        $department= new LC_Departments($id_departments);
                        if($department->all_employees)
                        {
                            $employees = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'employee WHERE active=1');
                        }
                        $employees = Db::getInstance()->executeS('
                            SELECT e.* FROM '._DB_PREFIX_.'employee e
                            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments_employee de ON (e.id_employee AND de.id_employee)
                            LEFT JOIN '._DB_PREFIX_.'ets_livechat_departments d ON (d.id_departments=de.id_departments)
                            WHERE e.active=1 AND (d.id_departments ="'.(int)$id_departments.'" OR e.id_profile=1)
                        ');
                    }
                    elseif($form_mail=='custom_emails' && $this->custom_mail && $custom_mails= explode(',',$this->custom_mail))
                    {
                        foreach($custom_mails as $custom_mail)
                        {
                            if(!in_array($custom_mail,$mails_to))
                            {
                                $mails_to[]= $this->custom_mail;
                                $names_to[] = Configuration::get('PS_SHOP_NAME');
                            }
                        }
                    }
                    if($employees)
                    {
                        foreach($employees as $employee)
                        {
                            if(!in_array($employee['email'],$mails_to))
                            {
                                $mails_to[]=$employee['email'];
                                $names_to[]=$employee['firstname'].' '.$employee['lastname'];
                            }
                            
                        }
                    }
                }
            }
            if($mails_to)
            {
                return array(
                    'mails_to'=>$mails_to,
                    'names_to' =>$names_to
                );
            }
            
        }
        return false;   
    }
    public function getDepartments()
    {
        if($this->require_select_department && $this->departments && $form_departments = explode(',',$this->departments))
        {
            if(in_array('all',$form_departments))
            {
                $departments= Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments WHERE status=1');
            }
            else
                $departments = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ets_livechat_departments WHERE id_departments IN ('.implode(',',array_map('intval',$form_departments)).') AND status=1');
            return $departments;
        }
        return false;
    }
}