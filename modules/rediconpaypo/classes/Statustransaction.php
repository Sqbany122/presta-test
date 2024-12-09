<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

class Statustransaction extends ObjectModel
{
    public $id_status = null;
    public $id_transaction = null;
    public $referenceId = null;
    public $transactionId = null;
    public $transactionStatus = null;
    public $amount = null;
    public $message = null;
    public $json = null;
    public $created_at;

    public static $definition = array(
        'table' => 'paypo_status_transaction',
        'primary' => 'id_status',
        'fields' => array(
            'id_status' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
            'id_transaction' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
            'referenceId' =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 40),
            'transactionId' =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 36),
            'transactionStatus' =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 20),
            'amount' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'message' =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 500),
            'json' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true),
            'created_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }
    /**
     * Undocumented function
     *
     * @param integer $cartId
     * @return void
     */
    public static function getByTransactionId($transactionId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT * FROM `" . _DB_PREFIX_ . "paypo_status_transaction` WHERE transactionId LIKE '" . pSQL($transactionId)."'");
    }

    public static function getByTransactionIdAndStatus($transactionId, string $status)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            "SELECT * FROM `" . _DB_PREFIX_ . "paypo_status_transaction` 
            WHERE 
            transactionId LIKE '" . pSQL($transactionId)."' AND transactionStatus LIKE '" . pSQL($status) . "'"
        );
    }

    /**
     * Undocumented function
     *
     * @param integer $id_transaction
     * @param MerchantTransactionValueObject $transactionData
     * @param string $json
     * @return void
     */
    public function create(int $id_transaction, $transactionData, string $json = '')
    {
        $this->id_transaction = $id_transaction;
        $this->referenceId = $transactionData->getReferenceId();
        $this->transactionId = $transactionData->getTransactionId();
        $this->transactionStatus = $transactionData->getTransactionStatus();
        $this->message = $transactionData->getMessage();
        $this->amount = $transactionData->getAmount();
        $this->json = $json;
        $this->created_at = date('Y-m-d H:i:s');
        $this->add();

        return $this;
    }
}
