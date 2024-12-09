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

function upgrade_module_1_0_7($module)
{
    $query = 'DELETE FROM `' . _DB_PREFIX_ . 'prestacafenewpayu_data` WHERE `name`=\'trial_license\'';
    Db::getInstance()->execute($query);

    $tab = new Tab();
    $tab->active = 1;
    $tab->name = array();
    $tab->class_name = 'AdminPrestaCafeNewPayu';

    foreach (Language::getLanguages(true) as $lang) {
        $tab->name[$lang['id_lang']] = 'PayU';
    }

    $tab->id_parent = -1;   // invisible tab
    $tab->module = $module->name;

    return $tab->add();
}
