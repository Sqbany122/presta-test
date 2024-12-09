<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

class PaypoTransaction extends ObjectModel
{
    public $id_transaction = null;
    public $referenceId = null;
    public $redirectUrl = null;
    public $id_cart = null;
    public $id_order = null;
    public $total = null;
    public $transactionId = null;
    public $json = null;
    public $completed = null;
    public $created_at;
    public $updated_at;

    public static $definition = array(
        'table' => 'paypo_transactions',
        'primary' => 'id_transaction',
        'fields' => array(
            'id_transaction' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'referenceId' =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 40),
            'transactionId' =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 36),
            'redirectUrl' =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 500),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'total' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'json' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true),
            'completed' =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'created_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'updated_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * Undocumented function
     *
     * @param string $referenceId
     * @return void
     */
    public static function getByReferenceId(string $referenceId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT * FROM `' . _DB_PREFIX_ . 'paypo_transactions`
        WHERE referenceId LIKE ' . pSQL($referenceId));
    }

    /**
     * Undocumented function
     *
     * @param integer $cartId
     * @return void
     */
    public static function getByCartId(int $cartId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'paypo_transactions` WHERE id_cart=' . (int) pSQL($cartId));
    }

    
    public static function getByOrderId(int $orderId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'paypo_transactions` WHERE id_order=' . (int) pSQL($orderId));
    }
    
    /**
     * Undocumented function
     *
     * @param integer $cartId
     * @return void
     */
    public static function getTransactionIdByCartId(int $cartId)
    {
        $row = self::getByCartId($cartId);

        return $row['transactionId']??false;
    }

    /**
     * Undocumented function
     *
     * @param integer $cartId
     * @return void
     */
    public static function getTotalByCartId(int $cartId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT total FROM `' . _DB_PREFIX_ . 'paypo_transactions` WHERE id_cart=' . (int) pSQL($cartId));
    }

    /**
     * Undocumented function
     *
     * @param integer $orderId
     * @return void
     */
    public static function getTotalByOrderId(int $orderId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT total FROM `' . _DB_PREFIX_ . 'paypo_transactions` WHERE id_order=' . (int) pSQL($orderId));
    }

    /**
     * Undocumented function
     *
     * @param integer $cartId
     * @return void
     */
    public static function getRedirectUrlCartId(int $cartId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT redirectUrl FROM `' . _DB_PREFIX_ . 'paypo_transactions` WHERE id_cart=' . (int) pSQL($cartId));
    }

    /**
     * Undocumented function
     *
     * @param integer $cartId
     * @return void
     */
    public static function getReferenceIdByCartId(int $cartId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT referenceId FROM `' . _DB_PREFIX_ . 'paypo_transactions` WHERE id_cart=' . (int) pSQL($cartId));
    }

    /**
     * Undocumented function
     *
     * @param string $transactionId
     * @return void
     */
    public static function getTransactionByTransactionid(string $transactionId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'paypo_transactions` WHERE transactionId LIKE \'' . pSQL($transactionId).'\'');
    }

    public static function deleteByTransactionid(string $transactionId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('SELECT * FROM `' . _DB_PREFIX_ . 'paypo_transactions` WHERE transactionId LIKE \'' . pSQL($transactionId).'\'');
    }

    /**
     * Undocumented function
     *
     * @param integer $cartId
     * @param array $transactionData
     * @return void
     */
    public function create(int $cartId, int $orderId, array $transactionData)
    {
        $json = $transactionData['json'];
        $response = $transactionData['response'];
        $transaction = $transactionData['payload'];
      
        $obj = new PaypoTransaction();
        $obj->referenceId = $transaction->getOrder()->getReferenceId();
        $obj->transactionId = $response->getTransactionId();
        $obj->redirectUrl = $response->getRedirectUrl();
        $obj->id_cart = $cartId;
        $obj->id_order = $orderId;
        $obj->total = $transaction->getOrder()->getAmount();
        $obj->json = $json;
        $obj->created_at = date('Y-m-d H:i:s');
        $obj->add();

        return $obj;
    }
    /**
     * Undocumented function
     *
     * @param Cart $cart
     * @return void
     */
    public static function equal(Cart $cart)
    {
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        $totalGr = round($total*100);
        $totalSave = self::getTotalByCartId((int)$cart->id);

        return $totalGr == $totalSave;
    }
}
