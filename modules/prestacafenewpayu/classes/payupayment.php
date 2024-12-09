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

class PayUPayment extends ObjectModel
{
    const STATUS_NEW = 'NEW';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_PENDING = 'PENDING';
    const STATUS_WAITING_FOR_CONFIRMATION = 'WAITING_FOR_CONFIRMATION';
    const STATUS_REJECTED = 'REJECTED';

    /**
     * Cart id, set before saving object for the first time.
     * @var int
     */
    public $id_cart;
    /**
     * Currency ISO code filled out upon creating the payment.
     * @var string
     */
    public $iso_currency;
    /**
     * PayU POS ID, set before saving object for the first time.
     * @var int
     */
    public $payu_pos_id;
    public $payu_second_key;
    /**
     * External order id (passed to PayU, unique for a single POS). Set after calling the create order PayU API.
     * @var string
     */
    public $payu_external_order_id;
    /**
     * Order ID assigned by PayU as of order creation.
     * @var string
     */
    public $payu_order_id;
    /**
     * Last seen PayU order status.
     * @var string
     */
    public $payu_order_status;
    public $payu_payment_id;
    /**
     * Currency ISO code filled out upon receiving a PayU notification.
     * @var string
     */
    public $payu_currency_code;
    public $payu_total_amount;
    public $payu_surcharge;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'prestacafenewpayu_payment',
        'primary' => 'id_prestacafenewpayu_payment',
        'fields' => array(
            'id_cart'                   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId',
                                                 'required' => true),
            'iso_currency'              => array('type' => self::TYPE_STRING, 'required' => true),
            'payu_pos_id'               => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt',
                                                 'required' => true),
            'payu_second_key'           => array('type' => self::TYPE_STRING, 'required' => true),
            'payu_external_order_id'    => array('type' => self::TYPE_STRING),
            'payu_order_id'             => array('type' => self::TYPE_STRING),
            'payu_order_status'         => array('type' => self::TYPE_STRING),
            'payu_payment_id'           => array('type' => self::TYPE_STRING),
            'payu_currency_code'        => array('type' => self::TYPE_STRING),
            'payu_total_amount'         => array('type' => self::TYPE_INT),
            'payu_surcharge'            => array('type' => self::TYPE_INT),
            'date_add'                  => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd'                  => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * @param $payu_order_id
     * @return bool|PayUPayment
     * @throws PrestaShopDatabaseException
     */
    public static function findByPayuOrderId($payu_order_id)
    {
        $query = new DbQuery;
        $query->select('p.`id_prestacafenewpayu_payment`');
        $query->from('prestacafenewpayu_payment', 'p');
        $query->where('p.`payu_order_id`=\''.pSQL($payu_order_id).'\'');
        $rows = Db::getInstance()->executeS($query->build());
        if (!$rows) {
            return false;
        }
        $payment = new PayUPayment($rows[0]['id_prestacafenewpayu_payment']);
        if (!Validate::isLoadedObject($payment)) {
            return false;
        }
        return $payment;
    }

    /**
     * Return all PayU payments for the given cart id ordered by id_prestacafenewpayu_payment
     * in descending order (ie. newest first). We should not rely on creation date here.
     * The system clock can be skewed or misleading but the payment ordering must be flawless.
     * @param $id_cart
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getPaymentsByCartId($id_cart)
    {
        $query = new DbQuery;
        $query->select('p.*');
        $query->from('prestacafenewpayu_payment', 'p');
        $query->where('p.`id_cart`='.(int)$id_cart);
        $query->orderBy('p.`id_prestacafenewpayu_payment` DESC');
        // Really should not use SQL Slave in here even though it is only a select query.
        // The race condition danger is real regarding everything PayU callback related.
        return Db::getInstance()->executeS($query->build());
    }

    /**
     * @param $id_cart
     * @return null|PayUPayment
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getLastPaymentByCartId($id_cart)
    {
        $query = new DbQuery;
        $query->select('p.*');
        $query->from('prestacafenewpayu_payment', 'p');
        $query->where('p.`id_cart`='.(int)$id_cart);
        $query->orderBy('p.`id_prestacafenewpayu_payment` DESC');
        $query->limit(1);
        // Really should not use SQL Slave in here even though it is only a select query.
        // The race condition danger is real regarding everything PayU callback related.
        $row = Db::getInstance()->executeS($query->build());
        if ($row) {
            return new PayUPayment($row[0]['id_prestacafenewpayu_payment']);
        }
        return null;
    }
}
