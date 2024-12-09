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
class LC_Ticket_field extends ObjectModel
{
    public $id_form;
    public $type;
    public $is_contact_mail;
    public $is_contact_name;
    public $is_subject;
    public $required;
    public $position;
    public $label;
    public $placeholder;
    public $description;
    public $options;
    public $deleted;
    public $is_customer_phone_number;
    public static $definition = array(
		'table' => 'ets_livechat_ticket_form_field',
		'primary' => 'id_field',
		'multilang' => true,
		'fields' => array(
            'id_form' => array('type' => self::TYPE_INT),
            'type' => array('type' => self::TYPE_STRING,'validate' => 'isCleanHtml', 'size' => 500),
			'is_subject' => array('type' => self::TYPE_INT),
            'is_contact_mail' => array('type' => self::TYPE_INT),
            'is_contact_name' => array('type' => self::TYPE_INT),
            'is_customer_phone_number' => array('type' => self::TYPE_INT),
            'position' => array('type' => self::TYPE_INT),
            'required' => array('type' => self::TYPE_INT),
            'deleted' => array('type' => self::TYPE_INT),
            // Lang fields
            'label' =>	array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 500),
            'placeholder' =>	array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 500),
            'description' =>	array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 500),
            'options' => array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 7000),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
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
    public function delete()
    {
        $this->deleted=1;
        $this->update();
        return true;
    }
}