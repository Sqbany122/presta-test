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

class KbBisCustomFields extends ObjectModel
{
    public $id_field;
    public $field_name;
    public $label;
    public $description;
    public $value;
    public $type;
    public $validation;
    public $error_msg;
    public $placeholder;
    public $html_id;
    public $html_class;
    public $max_length;
    public $min_length;
    public $required;
    public $position;
    public $active;
    public $date_add;
    public $date_upd;
    public $id_shop;
    
    const TABLE_NAME = 'kb_bis_fields';
    
    public static $definition = array(
        'table' => 'kb_bis_fields',
        'primary' => 'id_field',
        'multilang' => true,
        'fields' => array(
            'id_field' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'field_name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName'
            ),
            'type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName'
            ),
            'validation' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName'
            ),
            'html_id' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
//                'default' => 'fd'
            ),
            'html_class' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName'
            ),
            'max_length' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId'
            ),
            'min_length' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId'
            ),
            'required' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId'
            ),
            'position' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
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
            //lang
            'label' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'lang' => true,
            ),
            'description' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'lang' => true,
            ),
            'error_msg' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'lang' => true,
            ),
            'placeholder' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'lang' => true,
            ),
            'value' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
                'lang' => true,
            ),
            'id_shop' => array(
                'type' => self::TYPE_STRING,
            ),
        ),
    );
    public function __construct($id_field = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
         parent::__construct($id_field, $id_lang, $id_shop);
    }
    /*
     * Function to fetch custom field ID by Field Name
     */
    public static function getCustomFieldIDbyName($field_name = null)
    {
        if ($field_name == null) {
            return;
        }
        $data = Db::getInstance()->getRow(
            'SELECT id_field FROM '._DB_PREFIX_.'kb_bis_fields'
            . ' WHERE field_name="'. pSQL($field_name) .'"'
        );
        
        return $data['id_field'];
    }
    
    public static function getGlobalField()
    {
        $data = Db::getInstance()->executeS('SELECT id_field FROM '._DB_PREFIX_.'kb_bis_fields');
        return $data;
    }
    /*
     * Function to fetch available custom fields
     */
    public static function getAvailableBisCustomFields()
    {
        return Db::getInstance()->executeS(
            'SELECT c.*,cl.* FROM ' . _DB_PREFIX_ . 'kb_bis_fields c'
            . ' INNER JOIN ' . _DB_PREFIX_ . 'kb_bis_fields_lang cl'
            . ' on (cl.id_field=c.id_field AND cl.id_lang=' . (int) Context::getcontext()->language->id.') WHERE c.active=1 ORDER BY position ASC'
        );
    }

    public function updateRulePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('SELECT `id_field`, `position` FROM `'._DB_PREFIX_.'kb_bis_fields` ORDER BY `position` ASC')) {
            return false;
        }
        foreach ($res as $plans) {
            if ((int)$plans['id_field'] == (int)$this->id) {
                $moved_plans = $plans;
            }
        }

        if (!isset($moved_plans) || !isset($position)) {
            return false;
        }

        return (Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'kb_bis_fields` SET `position` = `position` '.($way ? '- 1' : '+ 1').' WHERE `position` '.($way ? '> '.(int)$moved_plans['position'].' AND `position` <= '.(int)$position : '< '.(int)$moved_plans['position'].' AND `position` >= '.(int)$position)) && Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'kb_bis_fields` SET `position` = '.(int)$position.' WHERE `id_field` = '.(int)$moved_plans['id_field']));
    }
}
