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
function upgrade_module_2_5_0($module)
{
    $module->registerHook('backOfficeFooter');

    $queries = array(
        'UPDATE `'._DB_PREFIX_.'prestacafenewpayu_data` SET `name` = \'pos_id_PLN\' WHERE `name` = \'pos_id\'',
        'UPDATE `'._DB_PREFIX_.'prestacafenewpayu_data` SET `name` = \'key_PLN\' WHERE `name` = \'key\'',
        'UPDATE `'._DB_PREFIX_.'prestacafenewpayu_data` SET `name` = \'second_key_PLN\' WHERE `name` = \'second_key\'',
        'UPDATE `'._DB_PREFIX_.'prestacafenewpayu_data` SET `name` = \'pos_auth_key_PLN\' '
            .'WHERE `name` = \'pos_auth_key\'',
        'UPDATE `'._DB_PREFIX_.'prestacafenewpayu_data` SET `name` = \'direct_card_PLN\' '
            .'WHERE `name` = \'direct_card\'',
        'UPDATE `'._DB_PREFIX_.'prestacafenewpayu_data` SET `name` = \'basic_payment_PLN\' '
            .'WHERE `name` = \'basic_payment\'',
        'UPDATE `'._DB_PREFIX_.'prestacafenewpayu_data` SET `name` = \'display_payment_methods_PLN\' '
            .'WHERE `name` = \'display_payment_methods\'',
        'UPDATE `'._DB_PREFIX_.'prestacafenewpayu_data` SET `name` = \'send_payment_email_PLN\' '
            .'WHERE `name` = \'send_payment_email\'',
        'INSERT INTO `'._DB_PREFIX_.'prestacafenewpayu_data`
            (id_shop_group, id_shop, id_lang, `name`, `value`, `date_add`, `date_upd`)
            SELECT id_shop_group, id_shop, id_lang, \'enable_pos_PLN\', \'1\', `date_add`, `date_upd`
            FROM `'._DB_PREFIX_.'prestacafenewpayu_data` WHERE `name` = \'pos_id_PLN\'',
        'ALTER TABLE `'._DB_PREFIX_.'prestacafenewpayu_payment` ADD iso_currency VARCHAR(10) NOT NULL',
        'UPDATE `'._DB_PREFIX_.'prestacafenewpayu_payment` SET iso_currency = payu_currency_code',
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
