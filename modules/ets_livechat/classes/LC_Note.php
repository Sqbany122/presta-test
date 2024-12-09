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
class LC_Note extends ObjectModel
{
    public $id_message;
    public $id_employee;
    public $id_download;
    public $note;
    public $file_name;
    public $date_add;
    public static $definition = array(
		'table' => 'ets_livechat_ticket_form_message_note',
		'primary' => 'id_note',
		'fields' => array(
            'id_message' => array('type' => self::TYPE_INT),
			'id_employee' => array('type' => self::TYPE_INT),
            'id_download' =>	array('type' => self::TYPE_INT),
            'note' =>	array('type' => self::TYPE_HTML),
            'file_name' =>	array('type' => self::TYPE_STRING),
            'date_add' =>	array('type' => self::TYPE_STRING),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public function delete()
    {
        $id_download = Db::getInstance()->getValue('SELECT id_download FROM '._DB_PREFIX_.'ets_livechat_download WHERE id_note ='.(int)$this->id);
        if($id_download)
        {
            $download= new LC_Download($id_download);
            $download->delete();
        }
        return parent::delete();
    }
 }