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
* @version   1.0.2
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      27-11-2016
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(dirname(__FILE__).'/models/FacebookDynamicAdsFeedProModel.php');
include_once(dirname(__FILE__).'/models/FacebookDynamicAdsFeedProModelDictionary.php');
include_once(dirname(__FILE__).'/models/FacebookDynamicAdsFeedProModelTaxonomy.php');


class PdFacebookDynamicAdsFeedPro extends Module
{
    const GOOGLE_TAXONOMY_DATA_URL = 'http://www.google.com/basepages/producttype/';

    private $html = '';
    public $secure_key;

    public $limit = 1000;

    public $ps_ver_15;
    public $ps_ver_16;
    public $ps_ver_17;

    public $order_out_of_stock;
    public $stock_management;
    public $advanced_stock_management;

    private $source_dictionary_array = array();
    private $destination_dictionary_array = array();

    public $languagesIsoTranslation;
    public $countriesIsoTranslation;

    // Google taxonomies curently available and their associations
    public $googleTaxonomiesCorelations = array(
        'pl-PL' => array('languages' => 'pl',       'countries' => 'PL',                'currencies' => 'PLN'),
        'en-US' => array('languages' => 'en',       'countries' => 'US, CA',            'currencies' => 'USD, CAD'),
        'en-GB' => array('languages' => 'en, gb',   'countries' => 'GB, AU, IN, CH',    'currencies' => 'GBP, AUD, INR, CHF'),
        'fr-FR' => array('languages' => 'fr',       'countries' => 'FR, CH, CA, BE',    'currencies' => 'EUR, CHF, CAD'),
        'de-DE' => array('languages' => 'de',       'countries' => 'DE, CH, AT',        'currencies' => 'EUR, CHF'),
        'it-IT' => array('languages' => 'it',       'countries' => 'IT, CH',            'currencies' => 'EUR, CHF'),
        'nl-NL' => array('languages' => 'nl',       'countries' => 'NL, BE',            'currencies' => 'EUR'),
        'es-ES' => array('languages' => 'es',       'countries' => 'ES, MX',            'currencies' => 'EUR, MXN'),
        'zh-CN' => array('languages' => 'zh',       'countries' => 'CN',                'currencies' => 'CNY'),
        'ja-JP' => array('languages' => 'ja',       'countries' => 'JP',                'currencies' => 'JPY'),
        'pt-BR' => array('languages' => 'br',       'countries' => 'BR',                'currencies' => 'BRL'),
        'cs-CZ' => array('languages' => 'cs',       'countries' => 'CZ',                'currencies' => 'CSK'),
        'ru-RU' => array('languages' => 'ru',       'countries' => 'RU',                'currencies' => 'RUB'),
        'sv-SE' => array('languages' => 'sv',       'countries' => 'SE',                'currencies' => 'SEK'),
        'da-DK' => array('languages' => 'da',       'countries' => 'DK',                'currencies' => 'DKK'),
        'no-NO' => array('languages' => 'no',       'countries' => 'NO',                'currencies' => 'NOK'),
        'tr-TR' => array('languages' => 'tr',       'countries' => 'TR',                'currencies' => 'TRY')
    );

    public function __construct()
    {
        $this->name = 'pdfacebookdynamicadsfeedpro';
        $this->author = 'PrestaDev.pl';
        $this->tab = 'seo';
        $this->version = '1.1.1';
        $this->bootstrap = true;
        $this->module_key = '9dc4b5105bfdb2784bdabd2bbb5ca9fg';
        $this->secure_key = Tools::encrypt(_COOKIE_KEY_);

        parent::__construct();

        $this->displayName = $this->l('Facebook Ads Feed Pro');
        $this->description = $this->l('Module generating XML feeds for Facebook Dynamic Ads, per country, language, currency, shop');

        $this->prefix = 'PD_FDAFP_';
        $this->ps_ver_15 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.5', '=')) ? true : false;
        $this->ps_ver_16 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')) ? true : false;
        $this->ps_ver_17 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '=')) ? true : false;

        // Stock options and allow buy when ouf off stock
        $this->order_out_of_stock = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
        $this->stock_management = (int)Configuration::get('PS_STOCK_MANAGEMENT');
        $this->advanced_stock_management = (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');

        $this->assignLangTranslationsArr();
        $this->assignCountriesTranslationsArr();
    }


    public function assignLangTranslationsArr()
    {
        $this->languagesIsoTranslation = array(
            'pl' => $this->l('Polish'),
            'en' => $this->l('English (US)'),
            'gb' => $this->l('English (GB)'),
            'fr' => $this->l('French'),
            'de' => $this->l('Deutsch'),
            'it' => $this->l('Italian'),
            'nl' => $this->l('Dutch'),
            'es' => $this->l('Spanish'),
            'zh' => $this->l('Chinese'),
            'ja' => $this->l('Japanese'),
            'br' => $this->l('Breton'),
            'cs' => $this->l('Czech'),
            'ru' => $this->l('Russian'),
            'sv' => $this->l('Swedish'),
            'da' => $this->l('Danish'),
            'no' => $this->l('Norwegian'),
            'tr' => $this->l('Turkish'),
        );
    }

    public function assignCountriesTranslationsArr()
    {
        $this->countriesIsoTranslation = array(
            'PL' => $this->l('Poland'),
            'US' => $this->l('United States'),
            'CA' => $this->l('Canada'),
            'GB' => $this->l('United Kingdom'),
            'AU' => $this->l('Australia'),
            'IN' => $this->l('India'),
            'CH' => $this->l('Switzerland'),
            'FR' => $this->l('France'),
            'BE' => $this->l('Belgium'),
            'DE' => $this->l('Germany'),
            'AT' => $this->l('Austria'),
            'IT' => $this->l('Italy'),
            'NL' => $this->l('Netherlands'),
            'ES' => $this->l('Spain'),
            'MX' => $this->l('Mexico'),
            'CN' => $this->l('China'),
            'JP' => $this->l('Japan'),
            'BR' => $this->l('Brazil'),
            'RU' => $this->l('Russian Federation'),
            'CZ' => $this->l('Czech Republic'),
            'SE' => $this->l('Sweden'),
            'DK' => $this->l('Denmark'),
            'NO' => $this->l('Norway'),
            'TR' => $this->l('Turkey'),
        );
    }


    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('updateCarrier')
            || !$this->registerHook('displayAdminProductsExtra')
            || !$this->registerHook('addProduct')
            || !$this->alterProductTable()
            || !FacebookDynamicAdsFeedProModel::createTables()
            || !FacebookDynamicAdsFeedProModelDictionary::createTables()
            || !FacebookDynamicAdsFeedProModelTaxonomy::createTables()
            || !FacebookDynamicAdsFeedProModelTaxonomy::createTablesTaxonomyData()
            || !FacebookDynamicAdsFeedProModelTaxonomy::createTablesTaxonomyCategory()
            || !FacebookDynamicAdsFeedProModelTaxonomy::addTaxonomyCorelations()
            || !$this->installModuleTabs()) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->unAlterProductTable()
            || !$this->uninstallModuleTab('AdminFacebookDynamicAdsFeedPro')
            || !$this->uninstallModuleTab('AdminFacebookDynamicAdsFeedProNew')
            || !$this->uninstallModuleTab('AdminFacebookDynamicAdsFeedProDictionary')
            || !$this->uninstallModuleTab('AdminFacebookDynamicAdsFeedProTaxonomy')
            || !FacebookDynamicAdsFeedProModel::dropTables()
            || !FacebookDynamicAdsFeedProModelDictionary::dropTables()
            || !FacebookDynamicAdsFeedProModelTaxonomy::dropTables()
            || !FacebookDynamicAdsFeedProModelTaxonomy::dropTablesTaxonomyCategory()
            || !FacebookDynamicAdsFeedProModelTaxonomy::dropTablesTaxonomyData()) {
            return false;
        }
        return true;
    }

    public function alterProductTable()
    {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `product_name_facebook_feed` varchar(256) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_0` varchar(100) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_1` varchar(100) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_2` varchar(100) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_3` varchar(100) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` ADD `custom_label_4` varchar(100) NOT NULL');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product` ADD `in_facebook_feed` tinyint(1) NOT NULL default 1');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_shop` ADD `in_facebook_feed` tinyint(1) NOT NULL default 1');
        return true;
    }

    public function unAlterProductTable()
    {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `product_name_facebook_feed`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_0`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_1`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_2`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_3`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_lang` DROP `custom_label_4`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product` DROP `in_facebook_feed`');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('ALTER TABLE `'._DB_PREFIX_.'product_shop` DROP `in_facebook_feed`');
        return true;
    }


    private function installModuleTabs()
    {
        $languages = Language::getLanguages();

        $tabs = array(
                'AdminFacebookDynamicAdsFeedProNew' => array(
                    'en' => 'Add new / View list',
                    'pl' => 'Dodaj nowy / przeglądaj'),
                'AdminFacebookDynamicAdsFeedProTaxonomy' => array(
                    'en' => 'Category mapping / import',
                    'pl' => 'Mapowanie kategorii'),
                'AdminFacebookDynamicAdsFeedProDictionary' => array(
                    'en' => 'Dictionary management',
                    'pl' => 'Zarządzanie słownikiem'),
                );

        $main_tab_lang = array(
                'en' => 'Facebook Ads Feed Pro',
                'pl' => 'Facebook Ads Feed Pro');

        $main_tab_names_array = array();
        foreach ($main_tab_lang as $tab_iso => $main_tab_name) {
            foreach ($languages as $language) {
                if ($language['iso_code'] == $tab_iso) {
                    $main_tab_names_array[$language['id_lang']] = $main_tab_name;
                } else {
                    $main_tab_names_array[$language['id_lang']] = $this->l('Facebook Ads Feed Pro');
                }
            }
        }

        $main_tab_id = $this->installModuleTab('AdminFacebookDynamicAdsFeedPro', $main_tab_names_array, 0);

        if ($main_tab_id) {
            foreach ($tabs as $class => $tab) {
                // tabs names as array where key is an a id_language
                $tab_names_array = array();
                foreach ($tab as $tab_iso => $tab_name) {
                    foreach ($languages as $language) {
                        if ($language['iso_code'] == $tab_iso) {
                            $tab_names_array[$language['id_lang']] = $tab_name;
                        } else {
                            if ($class == 'AdminFacebookDynamicAdsFeedProNew') {
                                $tab_names_array[$language['id_lang']] = $this->l('Add new / View list');
                            } elseif ($class == 'AdminFacebookDynamicAdsFeedProTaxonomy') {
                                $tab_names_array[$language['id_lang']] = $this->l('Category mapping / import');
                            } else {
                                $tab_names_array[$language['id_lang']] = $this->l('Dictionary management');
                            }
                        }
                    }
                }

                $this->installModuleTab($class, $tab_names_array, $main_tab_id);
            }
        }
        return true;
    }

    private function installModuleTab($tabClass, $tab_name, $id_tab_parent)
    {
        file_put_contents('../img/t/'.$tabClass.'.gif', Tools::file_get_contents('logo.gif'));

        $tab = new Tab();
        $tab->name = $tab_name;
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $id_tab_parent;

        if (!$tab->save()) {
            return false;
        }

        return $tab->id;
    }

    private function uninstallModuleTab($tabClass)
    {
        $id_tab = Tab::getIdFromClassName($tabClass);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }
        return false;
    }

    public function getContent()
    {
        if (Tools::isSubmit('save_auto_assign')) {
            $this->_postProcess();
        } else {
            $this->html .= '<br />';
        }

        $this->html .= '<h2>'.$this->displayName.' (v'.$this->version.')</h2><p>'.$this->description.'</p>';
        $this->html .= $this->renderForm();

        return $this->html;
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('save_auto_assign')) {
            Configuration::updateValue($this->prefix.'ASSIGN_ON_ADD', Tools::getValue($this->prefix.'ASSIGN_ON_ADD'));
            $this->html .= $this->displayConfirmation($this->l('Setting was updated'));
        }
    }


    public function renderForm()
    {
        $switch = version_compare(_PS_VERSION_, '1.6.0', '>=') ? 'switch' : 'radio';

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Automatic option assigment "In Facebook feed" for new products'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => $switch,
                        'label' => $this->l('Active'),
                        'name' => $this->prefix.'ASSIGN_ON_ADD',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l('Set if every new product added should get assigned in generated feed for Facebook Dynamic Ads automaticly'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),

                'submit' => array(
                    'name' => 'save_auto_assign',
                    'title' => $this->l('Save auto assign'),
                )
            ),
        );


        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function getConfigFieldsValues()
    {
        $return = array();
        $return[$this->prefix.'ASSIGN_ON_ADD'] = Configuration::get($this->prefix.'ASSIGN_ON_ADD');

        return $return;
    }

    public function getServicesList()
    {
        $sql = 'SELECT pdgmcp.*FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro` pdgmcp';
        return Db::getInstance()->executeS($sql);
    }


    public function getCategories($id_lang, $id_shop, $active = true, $sql_filter = '')
    {
        $sql = 'SELECT c.`id_category`, c.`id_parent`, cl.`name`, cl.`id_shop`, cl.`id_lang`
                FROM `'._DB_PREFIX_.'category` c
                INNER JOIN `'._DB_PREFIX_.'category_shop` category_shop ON (category_shop.id_category = c.id_category AND category_shop.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category` AND cl.id_shop = '.(int)$id_shop.'
                WHERE 1 '.$sql_filter.' 
                AND `id_lang` = '.(int)$id_lang.'
                '.($active ? 'AND `active` = 1' : '').'
                '.(!$id_lang ? 'GROUP BY c.id_category' : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function generateCategoryPath($id_lang, $id_shop, $taxonomy_lang = false)
    {
        //Get all categories and add FILTER clausule for PS 1.5 for greater that 1 because 1 is root
        $c_tmp = $this->getCategories($id_lang, $id_shop, false, 'AND c.`id_category` > 1');

        //d($c_tmp);
        $c_arr = array();

        foreach ($c_tmp as $c) {
            $c_arr[$c['id_category']] = $c;
        }

        //Add root category to categories array
        $shop = new Shop($id_shop);
        $root_c = Category::getRootCategory($id_lang, $shop);
        $c_arr[$root_c->id] = array('id_category' => $root_c->id, 'id_parent' => $root_c->id_parent, 'name' =>  $root_c->name);
        //END

        foreach ($c_arr as $c) {
            if($this->ps_ver_17) {
                $c_arr[$c['id_category']]['path'] = $this->getCategoryPathPs17($c['id_category'], $id_lang, $id_shop);
            } else {
                $c_arr[$c['id_category']]['path'] = $this->getCategoryPath($c['id_category'], $id_lang, $id_shop);
            }

            if ($taxonomy_lang) {
                $path_google_taxonomy = $this->getGoogleTaxonomyCategoryValue($c['id_category'], $taxonomy_lang);
                $c_arr[$c['id_category']]['path_google_taxonomy'] = $path_google_taxonomy['txt_taxonomy'];
            } else {
                $c_arr[$c['id_category']]['path_google_taxonomy'] = '';
            }
        }

        return $c_arr;
    }



    public function getCategoryPathPs17($id_category, $id_lang, $id_shop, $home = false)
    {
        $shop = new Shop($id_shop);
        $root_category = Category::getRootCategory($id_lang, $shop);
        $id_root_category = $root_category->id;
        $pipe = ' > ';

        $context = Context::getContext();
       
        $category = Db::getInstance()->getRow('
            SELECT id_category, level_depth, nleft, nright
            FROM '._DB_PREFIX_.'category
            WHERE id_category = '.(int)$id_category
        );


        if (isset($category['id_category'])) {
            $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
                    FROM '._DB_PREFIX_.'category c
                    LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category'.Shop::addSqlRestrictionOnLang('cl').')
                    WHERE c.nleft <= '.(int)$category['nleft'].'
                        AND c.nright >= '.(int)$category['nright'].'
                        AND cl.id_lang = '.(int)$context->language->id.
                       ($home ? ' AND c.id_category='.(int)$id_category : '').'
                        AND c.id_category != '.(int)Category::getTopCategory()->id.'
                        AND c.active = 1
                    GROUP BY c.id_category
                    ORDER BY c.level_depth ASC
                    LIMIT '.(!$home ? (int)$category['level_depth'] + 1 : 1);

            $categories = Db::getInstance()->executeS($sql);
            $path = '';
            $return = '';
            $full_path = '';
            $n = 1;
            $n_categories = (int)count($categories);

            foreach ($categories as $category) {
                 $full_path .= self::removeAccents($category['name']).(($n++ != $n_categories || !empty($path)) ? $pipe : '');
            }

            $return = $full_path.$path;
        
            // Sort out products with category default assign to home
            if (!empty($return)) {
                return $full_path.$path;
            } else {
                return $root_category->name;
            }
        } else {
            return $root_category->name;
        }
    }


    public function getCategoryPath($id_category, $id_lang, $id_shop)
    {
        $interval = Category::getInterval($id_category);
        $shop = new Shop($id_shop);
        $root_category = Category::getRootCategory($id_lang, $shop);
        $id_root_category = $root_category->id;
        $interval_root = Category::getInterval($id_root_category);
        $pipe = ' > ';
        
        if ($interval) {
            $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
                    FROM '._DB_PREFIX_.'category c
                    INNER JOIN `'._DB_PREFIX_.'category_shop` category_shop ON (category_shop.id_category = c.id_category AND category_shop.id_shop = '.(int)$id_shop.')
                    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category` AND cl.id_shop = '.(int)$id_shop.'
                    WHERE c.nleft <= '.$interval['nleft'].'
                        AND c.nright >= '.$interval['nright'].'
                        AND c.nleft >= '.$interval_root['nleft'].'
                        AND c.nright <= '.$interval_root['nright'].'
                        AND cl.id_lang = '.(int)$id_lang.'
                        AND c.active = 1
                        AND c.level_depth > '.(int)$interval_root['level_depth'].'
                    ORDER BY c.level_depth ASC';
                
            $categories = Db::getInstance()->executeS($sql);
            $n = 1;
            $n_categories = count($categories);
            $full_path = '';
            $path = '';
            $return = '';

            foreach ($categories as $category) {
                $full_path .= self::removeAccents($category['name']).(($n++ != $n_categories || !empty($path)) ? $pipe : '');
            }
            
            $return = $full_path.$path;
            
            // Sort out products with category default assign to home
            if (!empty($return)) {
                return $full_path.$path;
            } else {
                return $root_category->name;
            }
        } else {
            return $root_category->name;
        }
    }


    public function generateFeedFromConfig($all = false, $id_pdfacebookdynamicadsfeedpro = false)
    {
        $generated = false;
        $feeds = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro`
            WHERE `active` = 1'. (!$all ? ' AND `id_pdfacebookdynamicadsfeedpro` = '.(int)$id_pdfacebookdynamicadsfeedpro.'' : ''));

        foreach ($feeds as $feed) {
            $id_pdfacebookdynamicadsfeedpro = $feed['id_pdfacebookdynamicadsfeedpro'];
            $generated = $this->generateFile($id_pdfacebookdynamicadsfeedpro);

            // Last generating date set in db per id_pdfacebookdynamicadsfeedpro
            if ($generated) {
                $this->updateGeneratingTime($id_pdfacebookdynamicadsfeedpro);
            }
        }

        return $generated;
    }

    public function setSourceAndDestinationDictionaryArrays()
    {
        $this->source_dictionary_array = Db::getInstance()->executeS('
            SELECT source_word
            FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_dictionary`
            WHERE `active` = 1');

        $this->destination_dictionary_array = Db::getInstance()->executeS('
            SELECT destination_word
            FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_dictionary`
            WHERE `active` = 1');
    }


    public function useDictionaryForString($string)
    {
        if (count($this->source_dictionary_array) && count($this->destination_dictionary_array)) {
            foreach ($this->source_dictionary_array as $key => $source) {
                $string = str_replace($source, $this->destination_dictionary_array[$key]['destination_word'], $string);
            }
        }

        return  $string;
    }



    public function updateGeneratingTime($id_pdfacebookdynamicadsfeedpro)
    {
        Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro` 
                SET `date_gen`= "'.date('Y-m-d H:i:s').'"
                WHERE `id_pdfacebookdynamicadsfeedpro`= '.(int)$id_pdfacebookdynamicadsfeedpro);
    }

    private function generateFile($id_pdfacebookdynamicadsfeedpro)
    {
        // Set as cache source and destination arrays to avoid db queries
        $this->setSourceAndDestinationDictionaryArrays();

        $obj = new FacebookDynamicAdsFeedProModel($id_pdfacebookdynamicadsfeedpro);

        $id_lang = $obj->id_lang;
        $id_shop = $obj->id_shop;
        $id_currency = $obj->id_currency;
        $description_conf = $obj->description;
        $gtin_conf = $obj->gtin;
        $mpn_conf = $obj->mpn;
        $mpn_prefix = $obj->mpn_prefix;
        $include_shipping_cost_conf = $obj->include_shipping_cost;
        $rewrite_url = $obj->rewrite_url;
        $id_pdfacebookdynamicadsfeedpro_taxonomy = $obj->id_pdfacebookdynamicadsfeedpro_taxonomy;

        // Show memory usage for testing
        // p('Memory usage at the begining:');
        // self::echoMemoryUsage();

        $currency = Currency::getCurrencyInstance($id_currency);

        $path_parts = pathinfo(__FILE__);

        $xml_writer = new XMLWriter();
        $xml_writer->openMemory();
        $xml_writer->setIndent(true);

        $generate_file_path = $path_parts['dirname'].'/../../facebook-products-feed_id-'.$id_pdfacebookdynamicadsfeedpro.'.xml';

        $xml_writer->startDocument('1.0', 'UTF-8');
        $xml_writer->text('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">');
        $xml_writer->startElement('channel');

        $xml_writer->writeElement('title', Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
        $xml_writer->writeElement('link', $this->getShopDomain($id_shop, true));

        $description =  'Shop id: '.$id_shop.' | Language id: '.$id_lang.' | Currency id: '.$id_currency;
   

        $xml_writer->writeElement('description', $description);

        // get google etaxonomy iso lang form taxonomy id
        $object = new FacebookDynamicAdsFeedProModelTaxonomy($id_pdfacebookdynamicadsfeedpro_taxonomy);
        $taxonomy_lang = $object->taxonomy_lang;

        $cat_path_arr = $this->generateCategoryPath($id_lang, $id_shop, $taxonomy_lang);
        $feed_counter = 0;

        // count products to for loop
        $count_products = self::getProductsDBLightCount($obj);
        //d($count_products);

        // get products from sql by 100
        for ($offset = 0; $offset < $count_products; $offset += $this->limit) {
            $products = $this->getProducts($obj, $offset);
            foreach ($products as $p) {
                $feed_counter++;

                $product_description = '';
                if ($description_conf == 1) {
                    $product_description = trim($p['description_short']);
                    $product_description = $this->useDictionaryForString($product_description);
                    $product_description = self::html2txt($product_description);
                    $product_description = self::splitWords($product_description, 4995, '...');
                } elseif ($description_conf == 2) {
                    $product_description = trim($p['description']);
                    $product_description = $this->useDictionaryForString($product_description);
                    $product_description = self::html2txt($product_description);
                    $product_description = self::splitWords($product_description, 4995, '...');
                } elseif ($description_conf == 3) {
                    $product_description = $p['meta_description'];
                    $product_description = $this->useDictionaryForString($product_description);
                    $product_description = self::html2txt($product_description);
                    $product_description = self::splitWords($product_description, 4995, '...');
                }

                // Open new item element
                $xml_writer->startElement('item');

                // ID
                $xml_writer->writeElement('g:id', $p['id_product'].(isset($p['id_product_attribute']) ? '-'.$p['id_product_attribute'] : ''));

                // Title
                $product_name = $this->useDictionaryForString($p['product_name']);
                $xml_writer->writeElement('title', htmlspecialchars(Tools::ucfirst(mb_strtolower(Tools::substr($product_name, 0, 150))), ENT_COMPAT, 'UTF-8'));

                // Link
                $xml_writer->writeElement('link', self::getProductLink($p['id_product'], $id_lang, $id_shop, isset($p['id_product_attribute']) ? $p['id_product_attribute'] : 0, $rewrite_url, $p['link_rewrite']));

                // Price
                $xml_writer->writeElement('g:price', $p['price'].' '.$currency->iso_code);

                // Sale price
                $xml_writer->writeElement('g:sale_price', $p['price_sale'].' '.$currency->iso_code);

                // Unit price
                if (!empty($p['unit_pricing_measure'])) {
                    $xml_writer->writeElement('g:unit_pricing_measure', $p['unit_pricing_measure']);
                }

                // Description
                $xml_writer->writeElement('g:description', Tools::stripslashes($product_description));

                // Condition
                $xml_writer->writeElement('g:condition', $p['condition']);

                // Custom labels 0 - 4
                $xml_writer->writeElement('g:custom_label_0', $p['custom_label_0']);
                $xml_writer->writeElement('g:custom_label_1', $p['custom_label_1']);
                $xml_writer->writeElement('g:custom_label_2', $p['custom_label_2']);
                $xml_writer->writeElement('g:custom_label_3', $p['custom_label_3']);
                $xml_writer->writeElement('g:custom_label_4', $p['custom_label_4']);

          
                // item_group_id
                if (isset($p['id_product_attribute'])) {
                    $xml_writer->writeElement('g:item_group_id', $p['id_product']);
                }

                // Image link and additional image link
                if (count($p['images'])) {
                    $img_counter = 0;
                    foreach ($p['images'] as $i) {
                        $img_counter++;

                        if ($i['cover'] == 1) {
                            $xml_writer->writeElement('g:image_link', self::getImageLinkUrl($p['link_rewrite'], $p['id_product'].'-'.$i['id_image'], $id_shop));
                        } elseif (!isset($i['cover'])) {
                            $xml_writer->writeElement('g:image_link', self::getImageLinkUrl($p['link_rewrite'], $p['id_product'].'-'.$i['id_image'], $id_shop));
                        }

                        if ($i['cover'] == 0) {
                            $xml_writer->writeElement('g:additional_image_link', self::getImageLinkUrl($p['link_rewrite'], $p['id_product'].'-'.$i['id_image'], $id_shop));
                        }

                        if ($img_counter == 10) {
                            break;
                        }  // only 10 additional images are possible to add
                    }
                }

                // Availability and available_date
                if ($p['quantity'] > 0 && $p['available_date'] == '0000-00-00') {
                    $xml_writer->writeElement('g:availability', 'in stock');
                } elseif ($p['quantity'] == 0 && $p['available_date'] == '0000-00-00') {
                    $xml_writer->writeElement('g:availability', 'out of stock');
                } elseif ($p['available_date'] !== '0000-00-00') {
                    $xml_writer->writeElement('g:availability', 'preorder');
                    $xml_writer->writeElement('g:availability_date', $p['available_date'].'T00:00:00');
                } else {
                    $xml_writer->writeElement('g:availability', 'in stock');
                }

                // Brand
                $brand_exists = false;
                if (!empty($p['manufacturer_name'])) {
                    $xml_writer->writeElement('g:brand', htmlspecialchars($p['manufacturer_name'], ENT_COMPAT, 'UTF-8'));
                    $brand_exists = true;
                }

                // Gtin
                $gtin_exists = false;

                if ($gtin_conf == 1 && !empty($p['ean13'])) {
                    $gtin = $p['ean13'];
                    $gtin_exists = true;
                } elseif ($gtin_conf == 2 && !empty($p['upc'])) {
                    $gtin = $p['upc'];
                    $gtin_exists = true;
                } elseif ($gtin_conf == 3 && !empty($p['reference'])) {
                    $gtin = $p['reference'];
                    $gtin_exists = true;
                } elseif ($gtin_conf == 0) {
                    $gtin_exists = false;
                }

                if ($gtin_exists) {
                    $xml_writer->writeElement('g:gtin', $gtin);
                }

                // Mpn
                $mpn_exists = false;

                if ($mpn_conf == 1 && !empty($p['supplier_reference'])) {
                    $mpn = $mpn_prefix.$p['supplier_reference'];
                    $mpn_exists = true;
                } elseif ($mpn_conf == 2 && !empty($p['reference'])) {
                    $mpn = $mpn_prefix.$p['reference'];
                    $mpn_exists = true;
                } elseif ($mpn_conf == 3) {
                    $mpn = $mpn_prefix.$p['id_product'];
                    $mpn_exists = true;
                } elseif ($mpn_conf == 'disabled') {
                    $mpn_exists = false;
                }

                if ($mpn_exists) {
                    $xml_writer->writeElement('g:mpn', $mpn);
                }

                // If there is no GTIN and MPN and BRAND create element identifier not exist
                if (!$gtin_exists && !$mpn_exists) {
                    $xml_writer->writeElement('g:identifier_exists', 'false');
                }


                // Product type
                $categories_path = $cat_path_arr[$p['id_category_default']]['path'];
                $categories_path = strip_tags($categories_path);
                $xml_writer->writeElement('g:product_type', $categories_path);


                // Google category / taxonomy
                $path_google_taxonomy = $cat_path_arr[$p['id_category_default']]['path_google_taxonomy'];
                $path_google_taxonomy = strip_tags($path_google_taxonomy);
                $xml_writer->writeElement('g:google_product_category', $path_google_taxonomy);

                if ($include_shipping_cost_conf) {
                    foreach ($p['shipping'] as $key => $val) {
                        $xml_writer->startElement('g:shipping');
                        $xml_writer->writeElement('g:country', $key);
                        $xml_writer->writeElement('g:service', $val['name']);
                        $xml_writer->writeElement('g:price', $val['price']);
                        $xml_writer->endElement();
                    }
                }
                // Shipping weight
                if ($p['weight'] != '0') {
                    $xml_writer->writeElement('g:shipping_weight', $p['weight']);
                }

                $xml_writer->endElement(); // Close item

                file_put_contents($generate_file_path.'_temp', $xml_writer->flush(true), FILE_APPEND);
            }
            unset($products);
        }
        
        $xml_writer->endElement(); // close channel
        $xml_writer->text('</rss>'); // Close rss

        if (file_put_contents($generate_file_path.'_temp', $xml_writer->flush(true), FILE_APPEND)) { // Flush rest of data

            // remove old file
            @unlink($generate_file_path);
            @chmod($generate_file_path.'_temp', 0777);
            // rename just generated _temp to destionation name
            rename($generate_file_path.'_temp', $generate_file_path);
            @chmod($generate_file_path, 0777);
        }

        return true;
    }



    // Count results
    private function getProductsDBLightCount($obj)
    {
        $id_lang = $obj->id_lang;
        $id_shop = $obj->id_shop;
      
        $only_active = (int)$obj->only_active;
        $selected_categories = array_filter(explode(',', $obj->selected_categories), 'is_numeric');
        $exclude_manufacturers = array_filter(explode(',', $obj->exclude_manufacturers), 'is_numeric');
        $exclude_suppliers = array_filter(explode(',', $obj->exclude_suppliers), 'is_numeric');
        $exclude_products = array_filter(explode(',', $obj->exclude_products), 'is_numeric');

        $sql = 'SELECT COUNT(DISTINCT p.`id_product`)
                FROM `'._DB_PREFIX_.'product` p
                INNER JOIN `'._DB_PREFIX_.'product_shop` product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = p.`id_product` AND ps.`id_product_attribute` = 0)'.
                (count($selected_categories) ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
                WHERE pl.`id_lang` = '.(int)$id_lang.
                (count($selected_categories) ? ' AND c.`id_category` IN ('.implode(',', $selected_categories).')' : '').
                (count($exclude_manufacturers) ? ' AND p.`id_manufacturer` NOT IN ('.implode(',', $exclude_manufacturers).')' : '').
                (count($exclude_suppliers) ? ' AND p.`id_supplier` NOT IN ('.implode(',', $exclude_suppliers).')' : '').
                (count($exclude_products) ? ' AND p.`id_product`  NOT IN ('.implode(',', $exclude_products).')' : '').
                ($only_active ? ' AND product_shop.`active` = 1' : '').'
                AND p.`in_facebook_feed` = 1
                ORDER BY p.`id_product`';

            //d(Db::getInstance()->executeS($sql));

        return Db::getInstance()->getValue($sql);
    }


    /**
    * Ligh Function to get all available products for comparision engines
    * For saving memory we get only necesary data
    */

    private function getProductsDBLight($obj, $offset = 0)
    {
        $id_lang = $obj->id_lang;
        $id_shop = $obj->id_shop;

        $only_active = (int)$obj->only_active;
        $selected_categories = array_filter(explode(',', $obj->selected_categories), 'is_numeric');
        $exclude_manufacturers = array_filter(explode(',', $obj->exclude_manufacturers), 'is_numeric');
        $exclude_suppliers = array_filter(explode(',', $obj->exclude_suppliers), 'is_numeric');
        $exclude_products = array_filter(explode(',', $obj->exclude_products), 'is_numeric');

        $sql = 'SELECT DISTINCT p.`id_product`, p.`weight`, p.`id_category_default`, p.`reference`, p.`ean13`, p.`upc`,
        		p.`id_tax_rules_group`, p.`supplier_reference`, p.`condition`, p.`available_date`,
                pl.`name`, pl.`product_name_facebook_feed`, pl.`description_short`, pl.`description`, pl.`meta_description`, pl.`link_rewrite`,
                pl.`custom_label_0`, pl.`custom_label_1`, pl.`custom_label_2`, pl.`custom_label_3`, pl.`custom_label_4`,
                m.`name` AS manufacturer_name,
                s.`name` AS supplier_name, 
                ps.`product_supplier_reference` AS supplier_reference,
                sav.`out_of_stock`
                FROM `'._DB_PREFIX_.'product` p
                INNER JOIN `'._DB_PREFIX_.'product_shop` product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = p.`id_product` AND ps.`id_product_attribute` = 0)'.
                (count($selected_categories) ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
                LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 AND sav.id_shop = '.(int)$id_shop.')
                WHERE pl.`id_lang` = '.(int)$id_lang.
                (count($selected_categories) ? ' AND c.`id_category` IN ('.implode(',', $selected_categories).')' : '').
                (count($exclude_manufacturers) ? ' AND p.`id_manufacturer` NOT IN ('.implode(',', $exclude_manufacturers).')' : '').
                (count($exclude_suppliers) ? ' AND p.`id_supplier` NOT IN ('.implode(',', $exclude_suppliers).')' : '').
                (count($exclude_products) ? ' AND p.`id_product`  NOT IN ('.implode(',', $exclude_products).')' : '').
                ($only_active ? ' AND product_shop.`active` = 1' : '').'
                AND p.`in_facebook_feed` = 1
                ORDER BY p.`id_product`
                LIMIT '.(int)$offset.','.(int)$this->limit;

            //d(Db::getInstance()->executeS($sql));

        return Db::getInstance()->executeS($sql);
    }


    private function getProducts($obj, $offset)
    {
        $return = array();

        $id_lang = $obj->id_lang;
        $id_shop = $obj->id_shop;
        $id_country = $obj->id_country;
        $id_carrier = $obj->id_carrier;
        $id_currency = $obj->id_currency;

        $only_available = $obj->only_available;

        $min_product_price = $obj->min_product_price;
        $include_shipping_cost = $obj->include_shipping_cost;
        $products_attributes = $obj->products_attributes;
            
        $products = self::getProductsDBLight($obj, $offset);
        //p(count($products));
        //d($products);
        $weight_unit = Configuration::get('PS_WEIGHT_UNIT');

        // initiate context for shop we generating
        $context = Context::getContext()->cloneContext();

        if ($id_shop !== null && $context->shop->id != (int)$id_shop) {
            $context->shop = new Shop((int)$id_shop);
        }
        // END


        foreach ($products as $k => $p) {
            if ($products_attributes) {
                $combinations = self::getAttributeCombinations($p['id_product'], $id_lang, $id_shop);
            } else {
                $combinations = array();
            }

            if (!$products_attributes || !count($combinations)) {
                $price = self::getPrice((int)$p['id_product'], false, $id_shop, $id_currency, $id_country, false, true);
                $price_sale = self::getPrice((int)$p['id_product'], false, $id_shop, $id_currency, $id_country, true, true);

                $quantity = self::getQuantity((int)$p['id_product'], $id_shop, null, null);
                $p['quantity']  = $quantity;
                // STOCK MANAGMENT AND OUT_OF_STOCK
                // Allow to order the product when out of stock?
                $product_out_of_stock = (int)$p['out_of_stock'];

                // if use only avaiable and stock managment is on then skip that product if is not in stock
                if ($only_available && $this->stock_management) {
                    if ($quantity <= 0) {
                        continue;
                    }
                // else if only avaliable is off and we use stock managment then we do normal prestashop logick regarding allow to buy or not
                } elseif ($only_available == false && $this->stock_management) {
                    if ($quantity > 0) {
                        $p['quantity'] = $quantity;
                    } elseif ($quantity <= 0 && $product_out_of_stock == 0) {
                        continue;
                    } elseif ($quantity <= 0 && $product_out_of_stock == 1) {
                        $p['quantity'] = 99;
                    } elseif ($quantity <= 0 && $this->order_out_of_stock == 0 && $product_out_of_stock == 2) {
                        continue;
                    } elseif ($quantity <= 0 && $this->order_out_of_stock == 1 && $product_out_of_stock == 2) {
                        $p['quantity'] = 99;
                    }
                } elseif ($this->stock_management == false) {
                    $p['quantity'] = 99;
                }
                // END STOCK MANAGMENT


                $p['images'] = $this->getProductImges($id_lang, (int)$p['id_product'], null, $p['link_rewrite']);
                $p['price'] = Tools::ps_round($price);
                $p['price_sale'] = Tools::ps_round($price_sale);

                if (isset($p['product_name_facebook_feed']) && !empty($p['product_name_facebook_feed'])) {
                    $p['product_name'] = $p['product_name_facebook_feed'];
                } else {
                    $p['product_name'] = $p['name'];
                }
                    
                if ($include_shipping_cost) {
                    $p['shipping'] = $this->getShippingCostByParams($p['id_product'], $id_shop, $id_country, $id_carrier, $id_currency, $price);
                }

                $p['weight'] = Tools::ps_round($p['weight']).' '.$weight_unit;

                if (isset($min_product_price) && $min_product_price > 0) {
                    if ($price > $min_product_price) {
                        $return[] = $p;
                    }
                } else {
                    $return[] = $p;
                }
            } else {
                foreach ($combinations as $ca => $a) {
                    $price_attr = self::getPrice((int)$p['id_product'], (int)$a['id_product_attribute'], $id_shop, $id_currency, $id_country, false, true);
                    $price_attr_sale = self::getPrice((int)$p['id_product'], (int)$a['id_product_attribute'], $id_shop, $id_currency, $id_country, true, true);

                    $quantity = self::getQuantity((int)$p['id_product'], $id_shop, (int)$a['id_product_attribute'], null);
                    $p['quantity']  = $quantity;
                    // STOCK MANAGMENT AND OUT_OF_STOCK
                    // Allow to order the product when out of stock?
                    $product_out_of_stock = (int)$p['out_of_stock'];
                    
                    // if use only avaiable and stock managment is on then skip that product if is not in stock
                    if ($only_available && $this->stock_management) {
                        if ($quantity <= 0) {
                            continue;
                        }

                    // else if only avaliable is off and we use stock managment then we do normal prestashop logick regarding allow to buy or not
                    } elseif ($only_available == false && $this->stock_management) {
                        if ($quantity > 0) {
                            $p['quantity'] = $quantity;
                        } elseif ($quantity <= 0 && $product_out_of_stock == 0) {
                            continue;
                        } elseif ($quantity <= 0 && $product_out_of_stock == 1) {
                            $p['quantity'] = 99;
                        } elseif ($quantity <= 0 && $this->order_out_of_stock == 0 && $product_out_of_stock == 2) {
                            continue;
                        } elseif ($quantity <= 0 && $this->order_out_of_stock == 1 && $product_out_of_stock == 2) {
                            $p['quantity'] = 99;
                        }
                    } elseif ($this->stock_management == false) {
                        $p['quantity'] = 99;
                    }
                    // END STOCK MANAGMENT

                        
                    $p['images'] = $this->getProductImges($id_lang, (int)$p['id_product'], (int)$a['id_product_attribute'], $p['link_rewrite']);
                        
                    // if images for attributes don't exist get main ones
                    if (!count($p['images'])) {
                        $p['images'] = $this->getProductImges($id_lang, (int)$p['id_product'], null, $p['link_rewrite']);
                    }

                    $p['price'] = Tools::ps_round($price_attr);
                    $p['price_sale'] = Tools::ps_round($price_attr_sale);
                    $p['id_product_attribute'] = $a['id_product_attribute'];

                    if (isset($p['product_name_facebook_feed']) && !empty($p['product_name_facebook_feed'])) {
                        $p['product_name'] = $p['product_name_facebook_feed'].' '.$a['attribute_name'];
                    } else {
                        $p['product_name'] = $p['name'].' '.$a['attribute_name'];
                    }
                    
                    $p['reference'] = $a['reference'];
                    $p['supplier_reference'] = $a['supplier_reference'];
                    if ($include_shipping_cost) {
                        $p['shipping'] = $this->getShippingCostByParams($p['id_product'], $id_shop, $id_country, $id_carrier, $id_currency, $price_attr);
                    }
                    $p['ean13'] = $a['ean13'];

                    $p['weight'] = Tools::ps_round($p['weight']).' '.$weight_unit;

                    if (isset($min_product_price) && $min_product_price > 0) {
                        if ($price_attr > $min_product_price) {
                            $return[] = $p;
                        }
                    } else {
                        $return[] = $p;
                    }
                }
            }
        }

        //d($return);
        //d(count($return));
        //unset($products);
        return $return;
    }


    public static function getQuantity($id_product, $id_shop, $id_product_attribute = null, $cache_is_pack = null)
    {
        if ((int)$cache_is_pack || ($cache_is_pack === null && Pack::isPack((int)$id_product))) {
            if (!Pack::isInStock((int)$id_product)) {
                return 0;
            }
        }

        return (StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, $id_shop));
    }

    public static function getPrice($id_product, $id_product_attribute, $id_shop, $id_currency, $id_country, $usereduc, $usetax)
    {
        $id_state = 0;
        $zipcode = 0;
        $decimals = 2;
        $quantity = 1;
        $cart_quantity = 1;
        $only_reduc = false;
        $specific_price_output = null;
        $with_ecotax = true;
        $use_group_reduction = true;
        $use_customer_price = true;
        $id_cart = 0;
        $id_group = 0;
        $id_customer = 0;

        return Product::priceCalculation(
            $id_shop,
            $id_product,
            $id_product_attribute,
            $id_country,
            $id_state,
            $zipcode,
            $id_currency,
            $id_group,
            $quantity,
            $usetax,
            $decimals,
            $only_reduc,
            $usereduc,
            $with_ecotax,
            $specific_price_output,
            $use_group_reduction,
            $id_customer,
            $use_customer_price,
            $id_cart,
            $cart_quantity
        );
    }

    private static function getAttributeCombinations($id_product, $id_lang, $id_shop)
    {
        $sql = 'SELECT pa.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`public_name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`, ps.`product_supplier_reference` AS supplier_reference
                FROM `'._DB_PREFIX_.'product_attribute` pa
                    INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop = '.(int)$id_shop.')
                    LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                    LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                    LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
                    LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = '.(int)$id_product.')
                    LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = '.(int)$id_product.' AND ps.`id_product_attribute` = pa.`id_product_attribute`)
                WHERE pa.`id_product` = '.(int)$id_product.'
                ORDER BY pa.`id_product_attribute`';

        $results = Db::getInstance()->ExecuteS($sql);
        $return = array();

        foreach ($results as $k => $r) {
            if (!isset($return[$r['id_product_attribute']]['attribute_name'])) {
                $return[$r['id_product_attribute']]['attribute_name'] = '';
            }

            $return[$r['id_product_attribute']]['price'] = $r['price'];
            $return[$r['id_product_attribute']]['id_product_attribute'] = $r['id_product_attribute'];
            $return[$r['id_product_attribute']]['attribute_name'] .= ', '.$r['group_name'].' - '.$r['attribute_name'].'';
            $return[$r['id_product_attribute']]['quantity'] = $r['quantity'];
            $return[$r['id_product_attribute']]['reference'] = $r['reference'];
            $return[$r['id_product_attribute']]['supplier_reference'] = $r['supplier_reference'];
            $return[$r['id_product_attribute']]['ean13'] = $r['ean13'];
        }

        unset($results);
        return $return;
    }

    public function getProductImges($id_lang, $id_product, $id_product_attribute)
    {
        return Image::getImages($id_lang, $id_product, $id_product_attribute);
    }

    public static function getImageLinkUrl($link_rewrite, $ids, $id_shop)
    {
        $use_ssl = Configuration::get('PS_SSL_ENABLED', null, null, $id_shop);
        $protocol_content = ($use_ssl) ? 'https://' : 'http://';
        $uri_path = self::getImageLink($link_rewrite, $ids, null);
        $domain = ShopUrl::getMainShopDomain($id_shop);
        return $protocol_content.$domain.$uri_path;
    }

    public static function getImageLink($name, $ids, $type = '')
    {
        if(empty($type)) {
            $type = ImageType::getFormatedName('large');
        }
        
        $not_default = false;
        $allow = (int)Configuration::get('PS_REWRITING_SETTINGS');
        $ps_ver_17 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '=')) ? true : false;
        // legacy mode or default image
        if (!$ps_ver_17) {
            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
        } else {
            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'-'.Context::getContext()->shop->theme_name.'.jpg')) ? '-'.Context::getContext()->shop->theme_name : '');
        }
        if ((Configuration::get('PS_LEGACY_IMAGES')
            && (file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg')))
            || ($not_default = strpos($ids, 'default') !== false)) {
            if ($allow == 1 && !$not_default) {
                $uri_path = __PS_BASE_URI__.$ids.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg';
            }
        } else {
            // if ids if of the form id_product-id_image, we want to extract the id_image part
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            
            if (!$ps_ver_17) {
                $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
            } else {
                $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($id_image). $id_image.($type ? '-'.$type : '').'-'.(int) Context::getContext()->shop->theme_name.'.jpg')) ? '-'.Context::getContext()->shop->theme_name : '');
            }

            if ($allow == 1) {
                $uri_path = __PS_BASE_URI__.$id_image.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').$theme.'.jpg';
            }
        }

        return $uri_path;
    }

    private static function getProductLink($id_product, $id_lang, $id_shop, $ipa, $rewrite_url = false, $alias = null)
    {
        if ($rewrite_url) {
            $link = new Link();
            return htmlspecialchars($link->getProductLink($id_product, $alias, null, null, $id_lang, $id_shop, $ipa, false), ENT_COMPAT, 'UTF-8', false);
        } else {
            return self::getShopDomain($id_shop).'index.php?controller=product&id_product='.$id_product.'&id_lang='.$id_lang;
        }
    }


    public static function getShopDomain($id_shop, $only_domain = false)
    {
        $shop = new Shop($id_shop);
        $ssl_enable = Configuration::get('PS_SSL_ENABLED', null, null, $id_shop);

        if ($ssl_enable) {
            $domain = $shop->domain_ssl;
        } else {
            $domain = $shop->domain;
        }

        if ($only_domain) {
            return ($ssl_enable ? 'https://' : 'http://').$domain;
        } else {
            return ($ssl_enable ? 'https://' : 'http://').$domain.$shop->physical_uri.$shop->virtual_uri;
        }
    }

    /**
    * Reduce string lenght and add separator and keep whole words not cuted ones :-)
    *
    * @param string $str
    * @param int number of characters
    * @param str ending separator
    * @return string
    *
    */
    public function splitWords($string, $nb_caracs, $separator)
    {
        if (Tools::strlen($string) <= $nb_caracs) {
            $final_string = $string;
        } else {
            $final_string = '';
            $words = explode(' ', $string);
            foreach ($words as $value) {
                if (Tools::strlen($final_string.' '.$value) < $nb_caracs) {
                    if (!empty($final_string)) {
                        $final_string .= ' ';
                        $final_string .= $value;
                    }
                } else {
                    break;
                }
            }

            $final_string .= $separator;
        }
        return $final_string;
    }


    /**
    * Frankenstein patter for strip out html, css, js, iframe, non breaking space, BOM, from string etc.
    *
    * @param string $str
    * @return string
    *
    */
    public static function html2txt($str)
    {
        $search = array('@<script[^>]*?>.*?</script>@si',               // Strip out javascript
                        '@<[\/\!]*?[^<>]*?>@si',                        // Strip out HTML tags
                        '@<style[^>]*?>.*?</style>@siU',                // Strip style tags properly
                        '@<![\s\S]*?--[ \t\n\r]*>@',                    // Strip multi-line comments including CDATA
                        '@<iframe.*?\/iframe>@i');                      // Strip iframe

        $str = preg_replace($search, '', $str);                         // Do all abowe patterns matching
        $str = preg_replace('/\s\s+/', ' ', $str);                      // Strip multiple whitespaces
        $str = str_replace('&nbsp;', '', $str);                         // Strip non breaking space

        if (Tools::substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) { // Remove BOM Bytes Order Mark from begining of string
            $str = Tools::substr($str, 3);
        }

        return $str;
    }

    public static function getCountriesByZoneId($zones_ids, $id_shop)
    {
        $sql = ' SELECT DISTINCT c.`iso_code`, c.`id_zone`
                FROM `'._DB_PREFIX_.'country` c
                LEFT JOIN `'._DB_PREFIX_.'country_shop` country_shop ON (country_shop.id_country = c.id_country AND country_shop.id_shop = '.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_country` = c.`id_country`)
                WHERE c.`id_zone` IN ('.implode(',', $zones_ids).')
                AND c.`active` = 1';

        return Db::getInstance()->executeS($sql);
    }

    public static function getZonesByIdCarrier($id_carrier)
    {
        $zones = array();
        $result = Db::getInstance()->executeS('
			SELECT 	z.`id_zone`
			FROM `'._DB_PREFIX_.'carrier_zone` cz
			LEFT JOIN `'._DB_PREFIX_.'zone` z ON cz.`id_zone` = z.`id_zone`
			WHERE cz.`id_carrier` = '.(int)$id_carrier);
        
        if (count($result)) {
            foreach ($result as $r) {
                $zones[] = $r['id_zone'];
            }
        }

        return $zones;
    }

    public function getShippingCostByParams($id_product, $id_shop, $id_country, $id_carrier, $id_currency, $price)
    {
        $return = array();
        $id_zone = Country::getIdZone($id_country);

        $carrier_zones_ids = self::getZonesByIdCarrier($id_carrier);
        $zone_countries = self::getCountriesByZoneId($carrier_zones_ids, $id_shop);

        //d($zone_countries);

        foreach ($zone_countries as $c) {
            $return[$c['iso_code']] = self::getShippingData($id_product, $id_shop, $id_country, $c['id_zone'], $id_carrier, $id_currency, $price);
        }

          //d($return);
        return $return;
    }

    public static function getShippingData($id_product, $id_shop, $id_country, $id_zone, $id_carrier, $id_currency, $price)
    {
        $product = new Product($id_product);
        $carrier = new Carrier((int)$id_carrier);
        $currency = Currency::getCurrencyInstance($id_currency);
        $out = array();

        $out['name'] = trim($carrier->name);
        $out['price'] = self::getProductShippingCost($id_shop, $id_zone, $id_country, $product, $id_currency, $price, (int)$id_carrier, true).' '.$currency->iso_code;
        $out['price_tax_exc'] = self::getProductShippingCost($id_shop, $id_zone, $id_country, $product, $id_currency, $price, (int)$id_carrier, false).' '.$currency->iso_code;

        unset($product);
        unset($carrier);
        return $out;
    }

    public static function getIdTaxRulesGroupByIdCarrier($id_carrier, $id_shop)
    {
        $key = 'carrier_id_tax_rules_group_'.(int)$id_carrier.'_'.(int)$id_shop;
        if (!Cache::isStored($key)) {
            Cache::store(
                $key,
                Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                    SELECT `id_tax_rules_group`
                    FROM `'._DB_PREFIX_.'carrier_tax_rules_group_shop`
                    WHERE `id_carrier` = '.(int)$id_carrier.' AND id_shop='.(int)$id_shop)
            );
        }

        return Cache::retrieve($key);
    }


    public static function getTaxRateByIdCountry($id_tax_rules_group, $id_country)
    {
        $postcode = 0;
        $id_state = 0;

        $rows = Db::getInstance()->executeS('
                SELECT tr.*
                FROM `'._DB_PREFIX_.'tax_rule` tr
                JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (tr.`id_tax_rules_group` = trg.`id_tax_rules_group`)
                WHERE trg.`active` = 1
                AND tr.`id_country` = '.(int)$id_country.'
                AND tr.`id_tax_rules_group` = '.(int)$id_tax_rules_group.'
                AND tr.`id_state` IN (0, '.(int)$id_state.')
                AND (\''.pSQL($postcode).'\' BETWEEN tr.`zipcode_from` AND tr.`zipcode_to`
                    OR (tr.`zipcode_to` = 0 AND tr.`zipcode_from` IN(0, \''.pSQL($postcode).'\')))
                ORDER BY tr.`zipcode_from` DESC, tr.`zipcode_to` DESC, tr.`id_state` DESC, tr.`id_country` DESC');

        $first_row = true;
        $taxes = array();

        foreach ($rows as $row) {
            $tax = new Tax((int)$row['id_tax']);
            $taxes[] = $tax;

            // the applied behavior correspond to the most specific rules
            if ($first_row) {
                $first_row = false;
            }

            if ($row['behavior'] == 0) {
                break;
            }
        }

        unset($rows);

        if (isset($row) && count($row)) {
            $tax = new Tax($row['id_tax']);
            return $tax->rate;
        } else {
            return 0;
        }
    }


    public static function getProductShippingCost($id_shop, $id_zone, $id_country, $product, $id_currency, $price, $id_carrier = null, $use_tax = true)
    {
        $carrier = new Carrier((int)$id_carrier);

        $order_total = $price;
        $orderTotalwithDiscounts = $price;
        $total_package_without_shipping_tax_inc = $price;

        // Start with shipping cost at 0
        $shipping_cost = 0;

        if (!Validate::isLoadedObject($carrier)) {
            die(Tools::displayError('Fatal error: "no carrier"'));
        }
        if (!$carrier->active) {
            return $shipping_cost;
        }

        // Free fees if free carrier
        if ($carrier->is_free == 1) {
            return 0;
        }

        // Select carrier tax
        if ($use_tax && !Tax::excludeTaxeOption()) {
            $id_tax_rules_group = self::getIdTaxRulesGroupByIdCarrier($id_carrier, $id_shop);
            $carrier_tax = self::getTaxRateByIdCountry($id_tax_rules_group, $id_country);
        }

        $configuration = Configuration::getMultiple(array(
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HANDLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT'
        ), null, null, $id_shop);

        // Free fees
        $free_fees_price = 0;
        if (isset($configuration['PS_SHIPPING_FREE_PRICE'])) {
            $free_fees_price = Tools::convertPrice((float)$configuration['PS_SHIPPING_FREE_PRICE'], Currency::getCurrencyInstance($id_currency));
        }

        if ($orderTotalwithDiscounts >= (float)($free_fees_price) && (float)($free_fees_price) > 0) {
            return $shipping_cost;
        }

        if (isset($configuration['PS_SHIPPING_FREE_WEIGHT']) && $product->weight >= (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] && (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] > 0) {
            return $shipping_cost;
        }

        // Get shipping cost using correct method
        if ($carrier->range_behavior) {
            if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT && !Carrier::checkDeliveryPriceByWeight($carrier->id, $product->weight, $id_zone))
            || ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE && !Carrier::checkDeliveryPriceByPrice($carrier->id, $total_package_without_shipping_tax_inc, $id_zone, $id_currency)
            )) {
                $shipping_cost += 0;
            } else {
                if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping_cost += $carrier->getDeliveryPriceByWeight($product->weight, $id_zone);
                } else { // by price
                    $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, $id_currency);
                }
            }
        } else {
            if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) {
                $shipping_cost += $carrier->getDeliveryPriceByWeight($product->weight, $id_zone);
            } else {
                $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, $id_currency);
            }
        }
        // Adding handling charges
        if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier->shipping_handling) {
            $shipping_cost += (float)$configuration['PS_SHIPPING_HANDLING'];
        }

        // Additional Shipping Cost per product
        if (!$product->is_virtual) {
            $shipping_cost += $product->additional_shipping_cost;
        }

        $shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance($id_currency));

        // Apply tax
        if ($use_tax && isset($carrier_tax)) {
            $shipping_cost *= 1 + ($carrier_tax / 100);
        }

        $shipping_cost = (float)Tools::ps_round((float)$shipping_cost, 2);
        unset($carrier);
        return $shipping_cost;
    }


    public function checkConfigExist($id_feed)
    {
        $exist = false;
        $exist = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro`
            WHERE `id_pdfacebookdynamicadsfeedpro` = '.(int)$id_feed);

        if (count($exist)) {
            $exist = true;
        }

        return $exist;
    }


    /**
    * Hooks used in module
    */

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS(($this->_path).'views/css/admin.css');
    }

    /*
    ** Hook update carrier when we change somthing in carrier id is changing to
    ** And reflect that change in db
    */

    public function hookupdateCarrier($params)
    {
        //p($params);
        $old_id_carrier = $params['id_carrier'];
        $new_id_carrier = $params['carrier']->id;

        Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro`
            SET `id_carrier` = '.(int)$new_id_carrier.'
            WHERE `id_carrier` = '.(int)$old_id_carrier);
    }

    public function updateMapGoogleCategories2ShopCategories($catsmappingarr, $taxonomy_lang)
    {
        Db::getInstance()->Execute('
            DELETE FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_taxonomy_category`
            WHERE lang = "'.$taxonomy_lang.'"
        ');

        foreach ($catsmappingarr as $shop_category => $taxonomy_category) {
            if ($taxonomy_category && !empty($taxonomy_category)) {
                Db::getInstance()->Execute('
                    INSERT INTO `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_taxonomy_category`
                    VALUES (\''.(int)$shop_category.'\', \''.pSQL($taxonomy_category).'\', \''.pSQL($taxonomy_lang).'\')');
            }
        }
        return true;
    }

    public function getGoogleTaxonomyCategory($id_category, $taxonomy_lang)
    {
        return Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_taxonomy_category`
            WHERE `id_category` = '.(int)($id_category).'
            AND `lang` = \''.pSQL($taxonomy_lang).'\'
        ');
    }

    public function getGoogleTaxonomyCategoryValue($id_category, $taxonomy_lang)
    {
        return Db::getInstance()->getRow('
            SELECT txt_taxonomy
            FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_taxonomy_category`
            WHERE `id_category` = '.(int)($id_category).'
            AND `lang` = \''.pSQL($taxonomy_lang).'\'
        ');
    }



    public function getSelectTaxonomiesOptions()
    {
        return Db::getInstance()->ExecuteS('
            SELECT `id_pdfacebookdynamicadsfeedpro_taxonomy`, taxonomy_lang
            FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_taxonomy`
        ');
    }



    public function downloadTaxonomyFile($url)
    {
        $content = false;
        
        // Try with file_get_contents
        if (ini_get('allow_url_fopen')) {
            $content = Tools::file_get_contents($url);
        }
        
        // If returns false > try with CURL if available
        if ($content === false && function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_VERBOSE => true
            ));

            $content = @curl_exec($ch);
            curl_close($ch);
        }
        
        return $content;
    }

    public function importTaxonomyData($taxonomy_lang)
    {
        $return = false;
        // Build URL to fetch from Google
        $url = self::GOOGLE_TAXONOMY_DATA_URL.'taxonomy.'.$taxonomy_lang.'.txt';

        // Get and check content is here
        $content = $this->downloadTaxonomyFile($url);

        if (!$content || Tools::strlen($content) == 0) {
            die('0');
        }
        
        // Convert to array and check all is still OK
        $lines = explode("\n", trim($content));
        if (!$lines || !is_array($lines)) {
            die('0');
        }
            
        Db::getInstance()->Execute('
            DELETE FROM `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_taxonomy_data` 
            WHERE `lang` = \''.pSQL($taxonomy_lang).'\'
        ');
            
        foreach ($lines as $index => $line) {
            // First skip is a version number
            if ($index > 0) {
                $return = Db::getInstance()->Execute('
                    INSERT INTO `'._DB_PREFIX_.'pdfacebookdynamicadsfeedpro_taxonomy_data` (`value`, `lang`) 
                    VALUES (\''.pSQL($line).'\', \''.pSQL($taxonomy_lang).'\'
                )');
            }
        }
        return $return;
    }

    public static function echoMemoryUsage()
    {
        $mem_usage = memory_get_usage(true);

        if ($mem_usage < 1024) {
            echo $mem_usage.' bytes';
        } elseif ($mem_usage < 1048576) {
            echo round($mem_usage / 1024, 2).' kilobytes';
        } else {
            echo round($mem_usage / 1048576, 2).' megabytes';
        }
    }

    // Wordpress function to remove acents
    private static function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }

    public function hookAddProduct($params)
    {
        if (Configuration::get($this->prefix.'ASSIGN_ON_ADD') && $id_product = $params['id_product']) {
            $product = new Product((int)$id_product);
            $product->in_facebook_feed = 1;
            $product->update();
        }
    }


    public function hookDisplayAdminProductsExtra($params)
    {
        //d($params);
        $id_product = $params['id_product'];

        if (!isset($id_product)) {
            $id_product = Tools::getValue('id_product');
        }

        if (Validate::isLoadedObject($product = new Product((int)$id_product))) {
            
            $this->context->smarty->assign(array(
                'product' => $product,
                'path_tpl' =>  dirname(__FILE__).'/views/templates/admin',
                'languages' => $this->context->controller->getLanguages(),
                'default_form_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
            ));

            if ($this->ps_ver_15) {
                return $this->display(__FILE__, 'extraproducttab_15.tpl');
            } else if ($this->ps_ver_16) {
                return $this->display(__FILE__, 'extraproducttab_16.tpl');
            } else {
                return $this->display(__FILE__, 'extraproducttab_17.tpl');
            }
        } else {
            return $this->displayError($this->l('You must save this product before save settings'));
        }
    }
}
