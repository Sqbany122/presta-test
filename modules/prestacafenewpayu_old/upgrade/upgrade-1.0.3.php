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

function upgrade_module_1_0_3($module)
{
    $return = true;

    $query = 'ALTER TABLE `'._DB_PREFIX_.'prestacafenewpayu_payment` ADD `payu_payment_id` VARCHAR(255)';
    $return &= Db::getInstance()->execute($query);

    $query = 'ALTER TABLE `'._DB_PREFIX_.'prestacafenewpayu_payment` ADD `payu_currency_code` VARCHAR(10)';
    $return &= Db::getInstance()->execute($query);

    $query = 'ALTER TABLE `'._DB_PREFIX_.'prestacafenewpayu_payment` ADD `payu_total_amount` INT(11)';
    $return &= Db::getInstance()->execute($query);

    if (version_compare(_PS_VERSION_, '1.6', '<')) {
        $return &= $module->registerHook('displayAdminOrder');
    } else {
        $return &= $module->registerHook('displayAdminOrderTabOrder');
        $return &= $module->registerHook('displayAdminOrderContentOrder');
    }

    return $return;
}
