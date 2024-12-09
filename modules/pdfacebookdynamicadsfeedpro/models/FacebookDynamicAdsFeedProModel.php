<?php
/**
* 2012-2016 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Facebook Dynamic Ads Feed Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2016 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Facebook Dynamic Ads Feed Pro - PrestaShop 1.5.x and 1.6.x Module
* @version   1.0.1
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      27-11-2016
*/

class FacebookDynamicAdsFeedProModel extends ObjectModel
{
    public $id_shop;
    public $id_lang;
    public $id_country;
    public $id_currency;
    public $id_carrier = 0;
    public $id_pdfacebookdynamicadsfeedpro_taxonomy;

    public $active = 1;
   
    public $only_available = 1;
    public $only_active = 1;
    public $selected_categories;
    public $exclude_products;
    public $exclude_manufacturers;
    public $exclude_suppliers;
    public $products_attributes = 0;
    public $include_shipping_cost = 1;
    public $description = 0;
    public $rewrite_url = 1;


    public $min_product_price;
    public $gtin;
    public $mpn = 0;
    public $manu_name;
    public $mpn_prefix;

    public $date_add = '0000-00-00 00:00:00';
    public $date_upd = '0000-00-00 00:00:00';
    public $date_gen = '0000-00-00 00:00:00';
    public $generating = 0;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'pdfacebookdynamicadsfeedpro',
        'primary' => 'id_pdfacebookdynamicadsfeedpro',
        'multilang' => false,
        'fields' => array(
            'id_shop' =>                                 array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'id_lang' =>                                 array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'id_country' =>                              array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'id_currency' =>                             array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'id_carrier' =>                              array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'id_pdfacebookdynamicadsfeedpro_taxonomy' =>   array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'active' =>                                  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'only_available' =>                          array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'only_active' =>                             array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'selected_categories' =>                     array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false),
            'exclude_products' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false),
            'exclude_manufacturers' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false),
            'exclude_suppliers' =>                       array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false),
            'products_attributes' =>                     array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'include_shipping_cost' =>                   array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'description' =>                             array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'rewrite_url' =>                             array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'min_product_price' =>                       array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'required' => false),

            'gtin' =>                                    array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'mpn' =>                                     array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'manu_name' =>                               array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'mpn_prefix' =>                              array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false),
            'date_add' =>                                array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>                                array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_gen' =>                                array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'generating' =>                              array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),

        )
    );

    public function __construct($id_pdfacebookdynamicadsfeedpro = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_pdfacebookdynamicadsfeedpro, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        return parent::add($autodate, $null_values);
    }

    public function delete()
    {
        if ((int)$this->id === 0) {
            return false;
        }

        return parent::delete();
    }

    public function update($autodate = false, $null_values = false)
    {
        if ((int)$this->id === 0) {
            return false;
        }

        return parent::update($autodate, $null_values);
    }

    /**
     * Creates tables
     */
    public static function createTables()
    {
        return Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro` (
                `id_pdfacebookdynamicadsfeedpro` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_pdfacebookdynamicadsfeedpro_taxonomy` int(10) unsigned NOT NULL,
                `id_shop` int(11) unsigned NOT NULL DEFAULT \'1\',
                `id_lang` int(11) unsigned NOT NULL DEFAULT \'1\',
                `id_country` int(11) unsigned NOT NULL DEFAULT \'1\',
                `id_currency` int(11) unsigned NOT NULL DEFAULT \'1\',
                `id_carrier` int(11) unsigned NOT NULL DEFAULT \'1\',
                
                `active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',

                `only_available` tinyint(1) NOT NULL DEFAULT \'1\',
                `only_active` tinyint(1) NOT NULL DEFAULT \'1\',
                `selected_categories` text,
                `exclude_products` text,
                `exclude_manufacturers` text,
                `exclude_suppliers` text,
                `products_attributes` tinyint(1) NOT NULL DEFAULT \'0\',
                
                `include_shipping_cost` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                `description` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `rewrite_url` tinyint(1) unsigned NOT NULL DEFAULT \'1\',

                `min_product_price` decimal(20,6) NOT NULL DEFAULT \'0.000000\',

                `gtin` int(11) unsigned NOT NULL DEFAULT \'1\',
                `mpn` int(11) unsigned NOT NULL DEFAULT \'0\',

                `manu_name` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                `mpn_prefix` text,

                `date_add` datetime,
                `date_upd` datetime,
                `date_gen` datetime,
                `generating` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                PRIMARY KEY (`id_pdfacebookdynamicadsfeedpro`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');
    }

    public static function dropTables()
    {
        $sql = 'DROP TABLE IF EXISTS
                `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro`';

        return Db::getInstance()->execute($sql);
    }
}
