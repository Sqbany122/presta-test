<?php
/**
 * 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
 *
 * NOTICE OF LICENSE
 *
 * Lukasz Kowalczyk is the owner of the copyright of this module.
 * All rights of any kind, which are not expressly granted in this
 * License, are entirely and exclusively reserved to and by Lukasz
 * Kowalczyk.
 *
 * You may not rent, lease, transfer, modify or create derivative
 * works based on this module.
 *
 * @author    Lukasz Kowalczyk <lkowalczyk@prestacafe.pl>
 * @copyright Lukasz Kowalczyk
 * @license   LICENSE.txt
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'prestacafenewpayu_payment` (
    `id_prestacafenewpayu_payment`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_cart`                               INT(11) UNSIGNED NOT NULL,
    `iso_currency`                          VARCHAR(10) NOT NULL,
    `payu_pos_id`                           INT(11) UNSIGNED NOT NULL,
    `payu_second_key`                       VARCHAR(255) NOT NULL,
    `payu_external_order_id`                VARCHAR(255),
    `payu_order_id`                         VARCHAR(255),
    `payu_order_status`                     VARCHAR(255),
    `payu_payment_id`                       VARCHAR(255),
    `payu_currency_code`                    VARCHAR(10),
    `payu_total_amount`                     INT(11),
    `payu_surcharge`                        INT(11) NOT NULL DEFAULT 0,
    `date_add`                              DATETIME NOT NULL,
    `date_upd`                              DATETIME NOT NULL,
    PRIMARY KEY  (`id_prestacafenewpayu_payment`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'prestacafenewpayu_data` (
    `id_prestacafenewpayu_data`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_shop_group`                         INT(11) UNSIGNED DEFAULT NULL,
    `id_shop`                               INT(11) UNSIGNED DEFAULT NULL,
    `id_lang`                               INT(10) UNSIGNED NOT NULL,
    `name`                                  VARCHAR(254) NOT NULL,
    `value`                                 TEXT,
    `date_add`                              DATETIME NOT NULL,
    `date_upd`                              DATETIME NOT NULL,
    PRIMARY KEY (`id_prestacafenewpayu_data`),
    KEY `name` (`name`),
    KEY `id_shop` (`id_shop`),
    KEY `id_shop_group` (`id_shop_group`),
    KEY `id_lang` (`id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'prestacafenewpayu_debug` (
    `id_prestacafenewpayu_debug`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `message`                           TEXT NOT NULL,
    `data`                              BLOB NULL,
    `tag`                               VARCHAR(32) NOT NULL,
    `severity`                          TINYINT(1) NOT NULL DEFAULT 1,
    `date_add`                          TIMESTAMP NOT NULL,
    PRIMARY KEY (`id_prestacafenewpayu_debug`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        PrestaCafeNewPayu::addLog(
            'MySQL error '.Db::getInstance()->getMsgError().' for query '.$sql,
            3
        );
        return false;
    }
}
