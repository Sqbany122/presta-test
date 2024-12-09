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
function upgrade_module_2_0_2($object)
{
    $sqls=array();
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_ticket_form` ADD `customer_no_captcha` INT(1) NOT NULL AFTER `allow_captcha`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_departments` ADD `sort_order` INT(11) NOT NULL AFTER `status`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_download` ADD `file_type` VARCHAR(222) NOT NULL AFTER `filename`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_download` ADD `file_size` FLOAT(11,2) NOT NULL AFTER `filename`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_staff` ADD `signature` VARCHAR(222) NOT NULL AFTER `avata`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_ticket_form_field` ADD `is_customer_phone_number` INT(1) NOT NULL AFTER `is_contact_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `id_ticket` INT(11) NOT NULL AFTER `id_customer`';
    $sqls[]='CREATE TABLE IF NOT EXISTS`'._DB_PREFIX_.'ets_livechat_social_customer`( 
        `identifier` VARCHAR(222) NOT NULL , 
        `email` VARCHAR(222) NOT NULL ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    if($sqls)
    {
        foreach($sqls as $sql)
        {
            Db::getInstance()->execute($sql);
        }
    }
    $object->createFormDefault();
    $object->updateDefaultConfig();
    return true;
}