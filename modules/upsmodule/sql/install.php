<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ups_shipment` (
    `id` int unsigned NOT null AUTO_INCREMENT,
    id_ups_shipment VARCHAR(50) NOT null,
    tracking_number varchar(50) NOT null,
    delivery_status varchar(255) NOT null,
    id_order int(10) NOT null,
    customer_name varchar(255) NOT null,
    product varchar(255) NOT null,
    ap_id varchar(128),
    ap_name varchar(500),
    shipping_address1 varchar(128),
    shipping_address2 varchar(128),
    postcode varchar(10),
    city varchar(128),
    state varchar(128),
    country varchar(128),
    phone varchar(32),
    email varchar(126),
    shipping_service varchar(255) NOT null,
    package_detail varchar(255),
    accessorials_service varchar(1000) NOT null,
    create_date datetime NOT null,
    status int(10) NOT null,
    cod TINYINT(1) NOT null,
    order_value decimal(20,6) NOT null,
    shipping_fee decimal(20,6),
    PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ups_openorder` (
    id_ups_openorder int(10) unsigned NOT null AUTO_INCREMENT,
    id_order int(10) null,
    ap_id varchar(128),
    ap_name varchar(255),
    ap_address1 varchar(128),
    ap_address2 varchar(128),
    ap_state varchar(12),
    ap_postcode varchar(12),
    ap_city varchar(64),
    shipping_service varchar(255) NOT null,
    accessorials_service varchar(1000),
    created_at timestamp default CURRENT_TIMESTAMP,
    archived_at timestamp,
    status TINYINT(3) DEFAULT 1,
    PRIMARY KEY (`id_ups_openorder`)
);';

$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ups_data` (
    `id` int(10) unsigned NOT null AUTO_INCREMENT,
    `created_at` timestamp default CURRENT_TIMESTAMP,
    `key_name` varchar(100) not null,
    `ups_value` longtext,
    `status` TINYINT(2) DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE (`key_name`)
);";

$sql[] = 'CREATE TABLE IF NOT EXISTS `log_data` (
    `type` varchar(20),
    `script` varchar(200),
    `function` varchar(100),
    `created_at` timestamp default CURRENT_TIMESTAMP,
    `content` longtext,
    `call_back` longtext
);
';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
