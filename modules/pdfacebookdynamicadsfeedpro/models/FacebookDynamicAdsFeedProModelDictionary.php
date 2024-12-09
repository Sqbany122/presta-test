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

class FacebookDynamicAdsFeedProModelDictionary extends ObjectModel
{
    public $active = 1;
    public $source_word;
    public $destination_word;
    public $date_add = '0000-00-00 00:00:00';
    public $date_upd = '0000-00-00 00:00:00';
                
    public static $definition = array(
        'table' => 'pdfacebookdynamicadsfeedpro_dictionary',
        'primary' => 'id_pdfacebookdynamicadsfeedpro_dictionary',
        'multilang_shop' => false,
        'fields' => array(
            'active' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'source_word' =>        array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'destination_word' =>   array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'date_add' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
     );

    public function add($autodate = false, $null_values = false)
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

    public function update($null_values = false)
    {
        if ((int)$this->id === 0) {
            return false;
        }

        return parent::update($null_values);
    }


    /**
    * Creates tables
    */
    public static function createTables()
    {
        return Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_dictionary` (
                `id_pdfacebookdynamicadsfeedpro_dictionary` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `source_word` text,
                `destination_word` text,
                `active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                `date_add` datetime,
                `date_upd` datetime,
                PRIMARY KEY (`id_pdfacebookdynamicadsfeedpro_dictionary`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');
    }

    public static function dropTables()
    {
        $sql = 'DROP TABLE IF EXISTS
                `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_dictionary`';

        return Db::getInstance()->execute($sql);
    }
}
