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
if(!class_exists('LC_Note') && file_exists(dirname(__FILE__).'/../../classes/LC_Note.php'))
    require_once(dirname(__FILE__).'/../../classes/LC_Note.php');
class LC_Ticket extends ObjectModel
{
    public $id_form;
    public $id_shop;
    public $id_departments;
    public $status;
    public $priority;
    public $rate;
    public $readed;
    public $customer_readed;
    public $id_customer;
    public $subject;
    public $date_add;
    public $date_customer_update;
    public $date_admin_update;
    public $id_employee;
    public static $definition = array(
		'table' => 'ets_livechat_ticket_form_message',
		'primary' => 'id_message',
		'fields' => array(
            'id_form' => array('type' => self::TYPE_INT),
            'id_shop' => array('type'=>self::TYPE_INT),
			'id_departments' => array('type' => self::TYPE_INT),
            'status' =>	array('type' => self::TYPE_STRING),
            'priority' =>	array('type' => self::TYPE_INT),
            'rate' =>	array('type' => self::TYPE_INT),
            'readed' =>	array('type' => self::TYPE_INT),
            'id_employee' =>	array('type' => self::TYPE_INT),
            'customer_readed' =>	array('type' => self::TYPE_INT),
            'id_customer' =>	array('type' => self::TYPE_INT),
            'subject' =>	array('type' => self::TYPE_STRING),
            'date_add' =>	array('type' => self::TYPE_STRING),
            'date_customer_update' =>	array('type' => self::TYPE_STRING),
            'date_admin_update' =>	array('type' => self::TYPE_STRING),
        )
	);
    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
 }