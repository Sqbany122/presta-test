<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

class ReturnTransaction extends ObjectModel
{
    public $id_return = null;
    public $id_transaction = null;
    public $before_amount = null;
    public $amount = null;
    public $id_employee = null;
    public $created_at;

    public static $definition = array(
        'table' => 'paypo_returns_transaction',
        'primary' => 'id_return',
        'fields' => array(
            'id_return' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_transaction' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'before_amount' =>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'amount' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_employee' =>  array('type' => self::TYPE_INT,'validate' => 'isUnsignedId', 'required' => true),
            'created_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function getReturns($id_transaction)
    {
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            "SELECT prt.*, CONCAT(e.firstname,' ',e.lastname) as employee FROM `" . _DB_PREFIX_ . "paypo_returns_transaction` prt
            LEFT JOIN  `" . _DB_PREFIX_ . "employee` e ON e.id_employee = prt.id_employee 
            WHERE id_transaction = '" . (int) $id_transaction . "'"
        );

        $data = [];
        
        if ($rows) {
            foreach ($rows as $row) {
                $row['before_amount'] = $row['before_amount'] ? $row['before_amount'] / 100 : 0;
                $row['amount'] = $row['amount'] ? $row['amount'] / 100: 0;
                $data[] = $row;
            }
        }

        return $data;
    }

   
    public function create(int $id_transaction, $before_amount, $amount, $id_employee = 0)
    {
        $this->id_transaction = $id_transaction;
        $this->before_amount = $before_amount;
        $this->amount = $amount;
        $this->id_employee = $id_employee;
        $this->created_at = date('Y-m-d H:i:s');
        $this->add();

        return $this;
    }
}
