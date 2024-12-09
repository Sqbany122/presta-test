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

if (!defined('_PS_VERSION_'))
    exit;

/**
 * @param $module Module
 * @return bool
 */
function upgrade_module_2_6_6($module)
{
    $queries = array(
        'DROP TABLE IF EXISTS `'._DB_PREFIX_.'prestacafenewpayu_debug`',
        'CREATE TABLE `'._DB_PREFIX_.'prestacafenewpayu_debug` (
            `id_prestacafenewpayu_debug`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `message`                           TEXT NOT NULL,
            `data`                              BLOB NULL,
            `tag`                               VARCHAR(32) NOT NULL,
            `severity`                          TINYINT(1) NOT NULL DEFAULT 1,
            `date_add`                          TIMESTAMP NOT NULL,
            PRIMARY KEY (`id_prestacafenewpayu_debug`)
        )'
    );

    $result = true;

    foreach ($queries as $query) {
        $result &= Db::getInstance()->execute($query);
        if (!$result) {
            break;
        }
    }

    return $result;
}
