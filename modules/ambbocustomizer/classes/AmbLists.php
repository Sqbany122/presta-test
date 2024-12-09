<?php
/**
 *    ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      classes/AmbLists.php
 *    @subject   Handles lists
 *
 *    Support by mail: support@ambris.com
 */

class AmbLists
{
    public static $counter = 0;

    public static function getList($for)
    {
        if (method_exists('AmbLists', $for)) {
            return self::$for();
        } elseif (is_string($for)) {
            return explode(',', $for);
        }
    }

    public static function getOrderStatuses()
    {
        $statuses = OrderState::getOrderStates((int) Context::getContext()->language->id);
        $statuses_array = array();
        foreach ($statuses as $status) {
            $statuses_array[$status['id_order_state']] = $status['name'];
        }

        return $statuses_array;
    }

    public static function getCountries()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
            'SELECT DISTINCT c.id_country, cl.`name`
		FROM `' . _DB_PREFIX_ . 'orders` o
		' . Shop::addSqlAssociation('orders', 'o') . '
		INNER JOIN `' . _DB_PREFIX_ . 'address` a ON a.id_address = o.id_address_delivery
		INNER JOIN `' . _DB_PREFIX_ . 'country` c ON a.id_country = c.id_country
		INNER JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '
            . (int) Context::getContext()->language->id . ')
		ORDER BY cl.name ASC'
        );

        $country_array = array();
        foreach ($result as $row) {
            $country_array[$row['id_country']] = $row['name'];
        }

        return $country_array;
    }

    public static function getCustomerThreadStatusesIcons()
    {
        $icon_array = array(
            'open' => array(
                'class' => 'icon-circle text-success',
                'alt' => Translate::getAdminTranslation('Open', 'AdminCustomerThreads'),
            ),
            'closed' => array(
                'class' => 'icon-circle text-danger',
                'alt' => Translate::getAdminTranslation('Closed', 'AdminCustomerThreads'),
            ),
            'pending1' => array(
                'class' => 'icon-circle text-warning',
                'alt' => Translate::getAdminTranslation('Pending 1', 'AdminCustomerThreads'),
            ),
            'pending2' => array(
                'class' => 'icon-circle text-warning',
                'alt' => Translate::getAdminTranslation('Pending 2', 'AdminCustomerThreads'),
            ),
        );

        return $icon_array;
    }

    public static function getCustomerThreadStatuses()
    {
        $status_array = array();
        foreach (self::getCustomerThreadStatusesIcons() as $k => $v) {
            $status_array[$k] = $v['alt'];
        }

        return $status_array;
    }

    public static function getProductVisibilities()
    {

        return array(
            'both' => Translate::getAdminTranslation('Everywhere', 'AdminProducts'),
            'catalog' => Translate::getAdminTranslation('Catalog only', 'AdminProducts'),
            'search' => Translate::getAdminTranslation('Search only', 'AdminProducts'),
            'none' => Translate::getAdminTranslation('Nowhere', 'AdminProducts'),
        );
    }

    public static function getProductConditions()
    {
        return array(
            'new' => Translate::getAdminTranslation('New', 'AdminProducts'),
            'used' => Translate::getAdminTranslation('Used', 'AdminProducts'),
            'refurbished' => Translate::getAdminTranslation('Refurbished', 'AdminProducts'),
        );
    }
}
