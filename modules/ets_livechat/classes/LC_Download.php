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
class LC_Download extends ObjectModel
{
    public $id_message;
    public $id_ticket;
    public $id_field;
    public $id_note;
    public $id_conversation;
    public $filename;
    public $file_type;
    public $file_size;
    public static $definition = array(
		'table' => 'ets_livechat_download',
		'primary' => 'id_download',
		'fields' => array(
			'id_message' => array('type' => self::TYPE_INT),
            'id_ticket' => array('type' => self::TYPE_INT),
            'id_field' => array('type' => self::TYPE_INT),
            'id_note' => array('type' => self::TYPE_INT),
            'id_conversation' => array('type' => self::TYPE_INT),
            'filename' => array('type' => self::TYPE_STRING), 
            'file_type'=> array('type' => self::TYPE_STRING), 
            'file_size'=> array('type' => self::TYPE_FLOAT),    
        )
	);
    public function delete()
    {
        @unlink(dirname(__FILE__).'/../downloads/'.$this->filename);
        return parent::delete();
    }
}