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
class Product extends ProductCore
{
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public $id_product;
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public $id_shop;
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public $in_facebook_feed;
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public $product_name_facebook_feed;
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public $custom_label_0;
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public $custom_label_1;
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public $custom_label_2;
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public $custom_label_3;
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public $custom_label_4;
    /*
    * module: pdfacebookdynamicadsfeedpro
    * date: 2020-02-07 11:56:00
    * version: 1.1.1
    */
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {
        if (Configuration::get('PD_FDAFP_ASSIGN_ON_ADD')) {
            $this->in_facebook_feed = 1;
        }
        
        self::$definition['fields']['in_facebook_feed'] = array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool');
        self::$definition['fields']['product_name_facebook_feed'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 128);
        self::$definition['fields']['custom_label_0'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 128);
        self::$definition['fields']['custom_label_1'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100);
        self::$definition['fields']['custom_label_2'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100);
        self::$definition['fields']['custom_label_3'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100);
        self::$definition['fields']['custom_label_4'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100);
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}
