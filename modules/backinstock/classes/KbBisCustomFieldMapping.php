<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

class KbBisCustomFieldMapping extends ObjectModel
{
    public $id_mapping;
    public $id_prouct_customer;
    public $id_field;
    public $value;
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'kb_bis_custom_field_mapping',
        'primary' => 'id_mapping',
        'fields' => array(
            'id_mapping' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'id_prouct_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'id_field' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'value' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false
            ),
        ),
    );
    
    public function __construct($id_mapping = null)
    {
         parent::__construct($id_mapping);
    }
    
    /*
     * Function to fetch customer inputs for custom fields
     */
    public static function getValueByProductCustomerID($id_prouct_customer = null)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM '._DB_PREFIX_.'kb_bis_custom_field_mapping'
            . ' WHERE id_prouct_customer='.(int) $id_prouct_customer
        );
    }
}
