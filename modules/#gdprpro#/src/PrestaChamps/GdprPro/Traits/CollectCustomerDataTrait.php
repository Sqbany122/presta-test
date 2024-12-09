<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

// @codingStandardsIgnoreFile

namespace PrestaChamps\GdprPro\Traits;

/**
 * Trait CollectCustomerDataTrait
 *
 * @package PrestaChamps\GdprPro\Traits
 */
trait CollectCustomerDataTrait
{
    /**
     * The customer
     *
     * @var $customer \Customer
     */
    public $customer;

    /**
     * @var $addresses \Address[]
     */
    public $addresses = array();

    /**
     * @var $orders \Order[]
     */
    public $orders = array();

    /**
     * @var $carts \Cart[]
     */
    public $carts = array();

    /**
     * @var $customerMessages \CustomerMessage[]
     */
    public $customerMessages = array();

    /**
     * @var $customerThreads \CustomerThread[]
     */
    public $customerThreads = array();

    /**
     * @var \Connection[]
     */
    public $connections = array();

    public $numberOfInvoices = array();

    /**
     * @var array
     */
    public $activities = array();


    /**
     * Collect the required AR objects to ensure easy data handling (delete, export and stuff like this)
     *
     * @see \Customer::delete()
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function collectData()
    {
        $addresses = $this->getSimpleAddresses();
        // Addresses
        foreach ($addresses as $address) {
            $this->addresses[] = new \Address($address['id']);
        }
        // Orders: the Order::getCustomerOrders returns an array only
        $ordersArray = \Order::getCustomerOrders($this->customer->id, true, $this->context);
        foreach ($ordersArray as $orderItem) {
            $this->orders[] = new \Order($orderItem['id_order']);
        }
        // Carts
        $cartsArray = \Cart::getCustomerCarts($this->customer->id);
        foreach ($cartsArray as $cartItem) {
            $this->carts[] = new \Cart($cartItem['id_cart']);
        }

        // Threads & messages
        $customerMessages = \CustomerThread::getCustomerMessages($this->customer->id);
        $threads = array();
        foreach ($customerMessages as $customerMessage) {
            $this->customerMessages[] = new \CustomerMessage($customerMessage['id_customer_message']);
            $threads[] = $customerMessage['id_customer_thread'];
        }
        $threads = array_unique($threads);
        foreach ($threads as $threadId) {
            $this->customerThreads[] = new \CustomerThread($threadId);
        }

        // Connection
        $connections = $this->customer->getLastConnections();
        foreach ($connections as $connection) {
            $this->connections[] = new \Connection($connection['id_connections']);
        }

        // Activity logs
        $this->activities = \GdprActivityLog::activitiesByCustomer($this->customer->id);
    }


    /**
     * @return array
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function formatData()
    {
        $data = array();

        // Customer
        $customerArray = get_object_vars($this->customer);
        unset($customerArray['id']);
        unset($customerArray['note']);
        unset($customerArray['id_gender']);
        unset($customerArray['id_lang']);
        unset($customerArray['outstanding_allow_amount']);
        unset($customerArray['show_public_prices']);
        unset($customerArray['force_id']);
        unset($customerArray['id_guest']);
        unset($customerArray['id_default_group']);
        $customerArray['group'] = (
        new \Group(
            $this->customer->id_default_group,
            \Context::getContext()->language->id
        )
        )->name;
        $customerArray['gender'] = (
        new \Gender(
            $this->customer->id_gender,
            \Context::getContext()->language->id
        )
        )->name;
        $customerArray['language'] = (
        new \Language(
            $this->customer->id_lang,
            \Context::getContext()->language->id
        )
        )->name;
        unset($customerArray['id_shop']);
        unset($customerArray['id_shop_group']);
        unset($customerArray['secure_key']);
        unset($customerArray['passwd']);
        unset($customerArray['reset_password_token']);
        unset($customerArray['reset_password_validity']);
        unset($customerArray['webserviceParameters']);
        unset($customerArray['logged']);
        unset($customerArray['id_risk']);
        unset($customerArray['max_payment_days']);
        unset($customerArray['']);
        $data['customer'] = $customerArray;

        // Addresses
        $data['addresses'] = array();
        foreach ($this->addresses as $address) {
            $data['addresses'][] = get_object_vars($address);
        }

        // Orders
        $data['orders'] = array();
        foreach ($this->orders as $order) {
            $orderArray = get_object_vars($order);

            $orderArray['address_delivery'] = $this->addressDataFormatter(
                new \Address(
                    $order->id_address_delivery,
                    \Context::getContext()->language->id
                )
            );
            $orderArray['address_invoice'] = $this->addressDataFormatter(
                new \Address(
                    $order->id_address_invoice,
                    \Context::getContext()->language->id
                )
            );

            unset($orderArray['id_shop_group']);
            unset($orderArray['id_shop']);
            unset($orderArray['id_cart']);
            unset($orderArray['id_currency']);
            unset($orderArray['id_lang']);
            unset($orderArray['id_customer']);
            unset($orderArray['id_carrier']);
            unset($orderArray['current_state']);
            unset($orderArray['secure_key']);
            unset($orderArray['module']);
            unset($orderArray['conversion_rate']);
            unset($orderArray['recyclable']);
            unset($orderArray['mobile_theme']);
            unset($orderArray['round_mode']);
            unset($orderArray['round_type']);
            unset($orderArray['id']);
            unset($orderArray['id_shop_list']);
            unset($orderArray['force_id']);
            unset($orderArray['id_address_invoice']);
            unset($orderArray['id_address_delivery']);
            $data['orders'][] = $orderArray;
        }

        // Connections
        $data['connections'] = $this->customer->getLastConnections();

        // Number of invoices
        $data['numberOfInvoices'] = $this->numberOfInvoices;

        // Activities
        $data['activities'] = $this->activities;

        return $data;
    }

    /**
     * Format an \Address object to a human readable string
     *
     * @param \Address $address
     *
     * @return string
     */
    public function addressDataFormatter(\Address $address)
    {
        return "{$address->alias} - {$address->address1} {$address->postcode}" .
            " {$address->city}, {$address->country}";
    }

    /**
     * Get simplified Addresses arrays.
     *
     * @param int|null $idLang Language ID
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public function getSimpleAddresses($idLang = null)
    {
        if (is_null($idLang)) {
            $idLang = \Context::getContext()->language->id;
        }

        $sql = $this->getSimpleAddressSql(null, $idLang);
        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $addresses = array();
        foreach ($result as $addr) {
            $addresses[$addr['id']] = $addr;
        }

        return $addresses;
    }

    /**
     * Get SQL query to retrieve Address in an array.
     *
     * @param int|null $idAddress Address ID
     * @param int|null $idLang    Language ID
     *
     * @return string
     */
    public function getSimpleAddressSql($idAddress = null, $idLang = null)
    {
        if (is_null($idLang)) {
            $idLang = \Context::getContext()->language->id;
        }
        $shareOrder = (bool)\Context::getContext()->shop->getGroup()->share_order;

        $sql = 'SELECT DISTINCT
                      a.`id_address` AS `id`,
                      a.`alias`,
                      a.`firstname`,
                      a.`lastname`,
                      a.`company`,
                      a.`address1`,
                      a.`address2`,
                      a.`postcode`,
                      a.`city`,
                      a.`id_state`,
                      s.name AS state,
                      s.`iso_code` AS state_iso,
                      a.`id_country`,
                      cl.`name` AS country,
                      co.`iso_code` AS country_iso,
                      a.`other`,
                      a.`phone`,
                      a.`phone_mobile`,
                      a.`vat_number`,
                      a.`dni`
                    FROM `' . _DB_PREFIX_ . 'address` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'country` co ON (a.`id_country` = co.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (co.`id_country` = cl.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON (s.`id_state` = a.`id_state`)
                    ' . ($shareOrder ? '' : \Shop::addSqlAssociation('country', 'co')) . '
                    WHERE
                        `id_lang` = ' . (int)$idLang . '
                        AND `id_customer` = ' . (int)$this->customer->id . '
                        AND a.`deleted` = 0
                        AND a.`active` = 1';

        if (!is_null($idAddress)) {
            $sql .= ' AND a.`id_address` = ' . (int)$idAddress;
        }

        return $sql;
    }
}
