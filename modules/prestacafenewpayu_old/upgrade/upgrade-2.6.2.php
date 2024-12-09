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
function upgrade_module_2_6_2($module)
{
    $queries = array(
        'DELETE FROM `'._DB_PREFIX_.'prestacafenewpayu_data` WHERE `name` LIKE \'pos_auth_key%\''
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
