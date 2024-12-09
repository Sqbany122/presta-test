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

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'prestacafenewpayu_data`;';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'prestacafenewpayu_payment`;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        PrestaCafeNewPayu::addLog(
            'MySQL error '.Db::getInstance()->getMsgError().' for query '.$sql,
            3
        );
        return false;
    }
}
