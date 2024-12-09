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
function upgrade_module_2_0_1($object)
{
    $sqls=array();
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_message` ADD COLUMN `type_attachment` VARCHAR(222) AFTER `message`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_message` ADD COLUMN `name_attachment` VARCHAR(222) AFTER `message`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_message` ADD COLUMN `id_product` INT(10) AFTER `id_employee`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_pre_made_message` CHANGE `short_code` `short_code` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `id_departments` INT(11) NOT NULL AFTER `browser_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `id_departments_wait` INT(11) NOT NULL AFTER `browser_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `http_referer` varchar(1000) NOT NULL AFTER `browser_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `current_url` varchar(1000) NOT NULL AFTER `browser_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `id_employee` INT(11) NOT NULL AFTER `browser_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `id_employee_wait` INT(11) NOT NULL AFTER `browser_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `note` text NOT NULL AFTER `browser_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `id_tranfer` INT(11) NOT NULL AFTER `browser_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_conversation` ADD `date_accept` datetime NOT NULL AFTER `browser_name`';
    $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_livechat_message` CHANGE `id_employee` `id_employee` INT(10) NOT NULL';
    $sqls[]="CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_livechat_download` (
          `id_download` int(11) AUTO_INCREMENT PRIMARY KEY ,
          `id_message` int(11) unsigned NOT NULL,
          `id_ticket` int(11),
          `id_field` int(11),
          `id_note` int(11),
          `id_conversation` int(11) unsigned NOT NULL,
          `filename` VARCHAR(222) CHARACTER SET utf8 COLLATE utf8_bin
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8";
    $sqls[]='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_departments` ( 
            `id_departments` INT(11) NOT NULL AUTO_INCREMENT , 
            `name` VARCHAR(222) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL , 
            `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, 
            `all_employees` INT(11) NOT NULL,
            `status` INT(1) NOT NULL , PRIMARY KEY (`id_departments`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[]='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_departments_employee` ( 
            `id_departments` INT(11) NOT NULL , 
            `id_employee` INT(11) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[]='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_employee_online` ( 
            `id_employee` INT(11) NOT NULL ,
            `id_shop` INT(11) NOT NULL ,
            `date_online` DATETIME NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[]='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_employee_status` ( 
            `id_employee` INT(11) NOT NULL ,
            `id_shop` INT(11) NOT NULL ,
            `status` VARCHAR (222) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_url` (
        `id_conversation` INT(11),
        `url` VARCHAR(1000) NOT NULL , 
        `date_add` DATETIME NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[]= 'CREATE TABLE `'._DB_PREFIX_.'ets_livechat_staff` ( 
        `id_employee` INT(11) NOT NULL , 
        `name` VARCHAR(222) NOT NULL , 
        `avata` VARCHAR(222) NOT NULL , 
        `status` INT(1) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[]= 'CREATE TABLE `'._DB_PREFIX_.'ets_livechat_customer_info` ( 
        `id_customer` INT(11) NOT NULL , 
        `avata` VARCHAR(222) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_staff_decline` ( 
        `id_employee` INT(11) NOT NULL , 
        `id_conversation` INT(11) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[]='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form` ( 
        `id_form` INT(11) NOT NULL AUTO_INCREMENT ,
        `active` INT(1),
        `id_shop` INT(11),
        `mail_new_ticket` VARCHAR(222),
        `custom_mail` VARCHAR(222),
        `send_mail_to_customer` INT(1),
        `send_mail_reply_customer` INT(1),
        `send_mail_reply_admin` INT(1),
        `customer_reply_upload_file` INT(1),
        `allow_user_submit` INT(1),
        `save_customer_file` INT(1),
        `save_staff_file` INT(1),
        `require_select_department` INT(1),
        `departments` VARCHAR(222),
        `allow_captcha` INT(11),
        `deleted` INT(11),
        `sort_order` INT(11),
        `default_priority` INT(2),
        PRIMARY KEY (`id_form`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[]='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_field` ( 
        `id_field` INT(11) NOT NULL AUTO_INCREMENT ,
        `id_form` INT(11), 
        `type` VARCHAR(222),
        `is_contact_mail` INT(1),
        `is_contact_name` INT (1),  
        `is_subject` INT(1),
        `required` INT (1),
        `deleted` INT (1),
        `position` INT (11),     
        PRIMARY KEY (`id_field`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_lang` ( 
        `id_form` INT(11),
        `id_lang` INT (11),
        `title` VARCHAR(222) NOT NULL , 
        `button_submit_label` VARCHAR(222) NOT NULL , 
        `description` TEXT NOT NULL , 
        `friendly_url` VARCHAR(222) NOT NULL , 
        `meta_title` VARCHAR(222) NOT NULL , 
        `meta_description` TEXT NOT NULL , 
        `meta_keywords` TEXT NOT NULL
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_field_lang` ( 
        `id_field` INT(11),
        `id_lang` INT (11),
        `label` VARCHAR (222),
        `placeholder` TEXT NOT NULL,
        `description` TEXT NOT NULL,
        `options` VARCHAR(222) NOT NULL) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_message` ( 
        `id_message` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_form` INT(11) NOT NULL , 
        `id_shop` INT (11) NOT NULL,
        `id_departments` INT(11),
        `status` VARCHAR(22),
        `priority` INT(2),
        `rate` INT(11),
        `readed` INT(1),
        `id_employee` INT(11),
        `customer_readed` INT(1),
        `id_customer` INT(11) NOT NULL , 
        `subject` text,
        `date_add` DATETIME NOT NULL ,
        `date_customer_update` DATETIME NOT NULL ,
        `date_admin_update` DATETIME NOT NULL,
         PRIMARY KEY (`id_message`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_message_field` ( 
        `id_message` INT(11) NOT NULL , 
        `id_field` INT(11) NOT NULL , 
        `id_download` INT(11),
        `value` TEXT NOT NULL ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_ticket_form_message_note` ( 
        `id_note` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_message` INT(11) NOT NULL , 
        `id_employee` INT(11) NOT NULL,
        `id_download` INT(11),
        `note` text,
        `file_name` text,
        `date_add` DATETIME NOT NULL , PRIMARY KEY (`id_note`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_livechat_social_login` ( 
        `id_social_login` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_customer` INT(11) NOT NULL , 
        `social` VARCHAR(22) NOT NULL,
        `date_login` DATETIME NOT NULL , PRIMARY KEY (`id_social_login`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';
    $sqls[] ="ALTER TABLE `"._DB_PREFIX_."ets_livechat_conversation` ADD `chatref` INT(11) NOT NULL AFTER `id_employee_wait`";
    if($sqls)
        foreach($sqls as $sql)
        {
            Db::getInstance()->execute($sql);
        }
    $object->_installTabs();
    $object->registerHook('displayStaffs');
    $object->registerHook('displaySystemTicket');
    $object->registerHook('customerAccount');
    $object->registerHook('displayMyAccountBlock');
    $object->registerHook('moduleRoutes');
    $object->registerHook('displayLeftColumn');
    $object->registerHook('displayFooter');
    $object->registerHook('displayRightColumn');
    $object->registerHook('displayNav');
    $object->registerHook('displayNav2');
    $object->registerHook('customBlockSupport');
    return true;
    
}