<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

include_once(_PS_MODULE_DIR_ . 'backinstock/classes/KbBisCustomFields.php');

class BackInStock extends Module
{

    private $alert_settings = array();
    protected $product_data;
    public $submit_action = 'submit';
    const PARENT_TAB_CLASS = 'KBBackStockMainTab';
    const SELL_CLASS_NAME = 'SELL';

    public function __construct()
    {
        $this->name = 'backinstock';
        $this->tab = $this->l('front_office_features');
        $this->version = '3.0.1';
        $this->module_key = '55e61391361847c2b8ce395961c5770d';
        $this->author_address = '0x2C366b113bd378672D4Ee91B75dC727E857A54A6';
        $this->author = 'Knowband';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();
        /*
         * @author- Rishabh Jain
         * DOC - 03/02/20
         * To include the sendinblue and mailchimp libarary files for email marketing integration
         */
        /**
         * Checked if the class is already defined or not if not then include the file else not
         * @date 28-03-2023
         * @author Prvind Panday
         */
        if (!class_exists('KbSpinMailin')) {
            include_once(dirname(__FILE__) . '/libraries/sendinBlue/KbSpinMailin.php');
        }
        if (!class_exists('MailChimp')) {
            include_once(dirname(__FILE__) . '/libraries/drewm/mailchimp-api/src/MailChimp.php');
        }
        /* changes over */
        $this->displayName = $this->l('Back In Stock');
        $this->description = $this->l('Attracts the customer by subscribing Selon product');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.999.999');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    /*
     * @author- Rishabh Jain
     * DOC - 03/02/20
     * Function to add the admin controller tabs
     */
    public function installBackInStockTabs()
    {
        $parentTab = new Tab();
        $parentTab->name = array();

        $parent_tab = 'Back In Stock';
        $this->l('Back In Stock');

        foreach (Language::getLanguages(true) as $lang) {
            if ($this->getModuleTranslationByLanguage('backinstock', $parent_tab, 'backinstock', $lang['iso_code']) != '') {
                $parentTab->name[$lang['id_lang']] = $this->getModuleTranslationByLanguage('backinstock', $parent_tab, 'backinstock', $lang['iso_code']);
            } else {
                $parentTab->name[$lang['id_lang']] = $parent_tab;
            }
        }

        $parentTab->class_name = self::PARENT_TAB_CLASS;
        $parentTab->module = $this->name;
        $parentTab->active = 1;
        $parentTab->icon = 'notifications';
        $parentTab->id_parent = Tab::getIdFromClassName(self::SELL_CLASS_NAME);
        $parentTab->add();

        $id_parent_tab = (int) Tab::getIdFromClassName(self::PARENT_TAB_CLASS);

        $admin_menus = $this->getAdminMenus();

        foreach ($admin_menus as $menu) {
            $tab = new Tab();
            foreach (Language::getLanguages(true) as $lang) {
                if ($this->getModuleTranslationByLanguage('backinstock', $menu['name'], 'backinstock', $lang['iso_code']) != '') {
                    $tab->name[$lang['id_lang']] = $this->getModuleTranslationByLanguage('backinstock', $menu['name'], 'backinstock', $lang['iso_code']);
                } else {
                    $tab->name[$lang['id_lang']] = $menu['name'];
                }
            }
            $tab->class_name = $menu['class_name'];
            $tab->module = $this->name;
            $tab->active = $menu['active'];
            $tab->id_parent = $id_parent_tab;
            $tab->add($this->id);
        }
        return true;
    }
    /*
     * @author- Rishabh Jain
     * DOC - 03/02/20
     *  funtion to fetch the translations directy from the translation file for the admin tabs
     */
    public function getModuleTranslationByLanguage($module, $string, $source, $language, $sprintf = null, $js = false)
    {
        $modules = array();
        $langadm = array();
        $translations_merged = array();
        $name = $module instanceof Module ? $module->name : $module;

        if (!isset($translations_merged[$name]) && isset(Context::getContext()->language)) {
            $files_by_priority = array(
                _PS_MODULE_DIR_ . $name . '/translations/' . $language . '.php'
            );
            foreach ($files_by_priority as $file) {
                if (file_exists($file)) {
                    include($file);
                    /* No need to define $_MODULE as it is defined in the above included file. */
                    $modules = $_MODULE;
                    $translations_merged[$name] = true;
                }
            }
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);
        if ($modules == null) {
            if ($sprintf !== null) {
                $string = Translate::checkAndReplaceArgs($string, $sprintf);
            }

            return str_replace('"', '&quot;', $string);
        }
        $current_key = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $source) . '_' . $key;
        $default_key = Tools::strtolower('<{' . $name . '}prestashop>' . $source) . '_' . $key;
        if ('controller' == Tools::substr($source, -10, 10)) {
            $file = Tools::substr($source, 0, -10);
            $current_key_file = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $file) . '_' . $key;
            $default_key_file = Tools::strtolower('<{' . $name . '}prestashop>' . $file) . '_' . $key;
        }

        if (isset($current_key_file) && !empty($modules[$current_key_file])) {
            $ret = Tools::stripslashes($modules[$current_key_file]);
        } elseif (isset($default_key_file) && !empty($modules[$default_key_file])) {
            $ret = Tools::stripslashes($modules[$default_key_file]);
        } elseif (!empty($modules[$current_key])) {
            $ret = Tools::stripslashes($modules[$current_key]);
        } elseif (!empty($modules[$default_key])) {
            $ret = Tools::stripslashes($modules[$default_key]);
            // if translation was not found in module, look for it in AdminController or Helpers
        } elseif (!empty($langadm)) {
            $ret = Tools::stripslashes(Translate::getGenericAdminTranslation($string, $key, $langadm));
        } else {
            $ret = Tools::stripslashes($string);
        }

        if ($sprintf !== null) {
            $ret = Translate::checkAndReplaceArgs($ret, $sprintf);
        }

        if ($js) {
            $ret = addslashes($ret);
        } else {
            $ret = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
        }
        return $ret;
    }

    /*
     * @author- Rishabh Jain
     * DOC - 03/02/20
     * Function to get the admin tab details
     */
    private function getAdminMenus()
    {
        $this->l('Module Configuration');
        $this->l('Subscriber List');
        return array(
            array(
                'class_name' => 'AdminKbBackInStockSetting',
                'active' => 1,
                'name' => $this->l('Module Configuration')
            ),
            array(
                'class_name' => 'AdminKbBisFields',
                'active' => 1,
                'name' => $this->l('Back In Stock Custom fields')
            ),
            /* start by dharmanshu 20-08-2021
            array(
            'class_name' => 'AdminKbBackRecaptcha',
            'active' => 1,
            'name' => 'V3 Recaptcha Settings'
            ),
            * end by dharmanshu 20-08-2021
            */
            array(
                'class_name' => 'AdminKbBackSubscriberList',
                'active' => 1,
                'name' => $this->l('Subscriber List')
            ),
        );
    }

    /*
     * Function to remove the admin controller tabs
     */
    public function unInstallBackInStockTabs()
    {
        $parentTab = new Tab(Tab::getIdFromClassName(self::PARENT_TAB_CLASS));
        $parentTab->delete();

        $admin_menus = $this->getAdminMenus();

        foreach ($admin_menus as $menu) {
            $sql = 'SELECT id_tab FROM `' . _DB_PREFIX_ . 'tab` Where class_name = "' . pSQL($menu['class_name']) . '" 
				AND module = "' . pSQL($this->name) . '"';
            $id_tab = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        return true;
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (
            !parent::install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('displayProductButtons') ||
            !$this->registerHook('displayProductAdditionalInfo') ||
            !$this->registerHook('displayCustomAlertBlockAnywhere') ||
            !$this->registerHook('actionUpdateQuantity') ||
            !$this->registerHook('displayCustomerAccount') ||
            !$this->registerHook('actionProductSave') ||
            !$this->registerHook('actionValidateOrder') ||
            !$this->registerHook('displayLeftColumnProduct') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('actionExportGDPRData') ||
            !$this->registerHook('actionDeleteGDPRCustomer') ||
            !$this->registerHook('actionProductUpdate')
        ) {
            return false;
        }
        $this->installBackInStockTabs();
        /*
         * @author- Rishabh Jain
         * DOC- 31/01/20
         * To add the order and low stock mail column to keep
         * the data if the low stock alert notification is send
         * and has customer placed the order or ot
         */
        $create_product_table = 'create table if not exists `' . _DB_PREFIX_ . 'product_update_product_detail`
					(
					`id` int(100) NOT NULL AUTO_INCREMENT,
					`email` varchar(100) NOT NULL,
					`customer_id` int(11) NOT NULL,
					`product_id` int(11) NOT NULL,
					`product_name` varchar(100) NOT NULL,
					`category_id` varchar(50) NOT NULL,
					`skv` varchar(20) NOT NULL,
					`product_attribute_id` int(11) NOT NULL,
					`current_price` varchar(20) NOT NULL,
					`subscribe_type` varchar(10) NOT NULL,
					`currency_code` varchar(50) NOT NULL,
					`store_id` int(11) NOT NULL,
					`order` int(11) NOT NULL DEFAULT "0",
					`low_stock_mail` int(11) NOT NULL DEFAULT "0",
					`active` int(11) NOT NULL DEFAULT "1",
					`key` varchar(100) NOT NULL,
					`send` enum("0","1"),
					`date_added` datetime NOT NULL,
					`date_updated` datetime NOT NULL,
					`mail_send_date` datetime NOT NULL,
					PRIMARY KEY (`id`)) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
        /* changes over */
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_product_table);
        $check_col_sql = 'SELECT count(*) FROM information_schema.COLUMNS
                              WHERE COLUMN_NAME = "lang_iso"
                              AND TABLE_NAME = "' . _DB_PREFIX_ . 'product_update_product_detail"
                              AND TABLE_SCHEMA = "' . _DB_NAME_ . '"';
        $check_col = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_col_sql);
        if ((int) $check_col == 0) {
            $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_update_product_detail` ADD `lang_iso` varchar(100) NULL AFTER `store_id`';
            Db::getInstance()->execute($query);
        }

        $check_col_sql = 'SELECT count(*) FROM information_schema.COLUMNS
                              WHERE COLUMN_NAME = "low_stock_mail"
                              AND TABLE_NAME = "' . _DB_PREFIX_ . 'product_update_product_detail"
                              AND TABLE_SCHEMA = "' . _DB_NAME_ . '"';
        $check_col = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_col_sql);
        if ((int) $check_col == 0) {
            $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_update_product_detail` ADD `low_stock_mail` int(11) NOT NULL DEFAULT "0" AFTER `store_id`';
            Db::getInstance()->execute($query);
            $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_update_product_detail` ADD `order` int(11) NOT NULL DEFAULT "0" AFTER `store_id`';
            Db::getInstance()->execute($query);
        }
        /*
         * @author- Prvind Panday
         * DOC- 17/06/22
         * To add the req_quantityt column to keep
         * the data if the user need a large quantity of the product
         * and has admin can be notified of the product demand.
         */
        $check_quan_col_sql = 'SELECT count(*) FROM information_schema.COLUMNS
                              WHERE COLUMN_NAME = "req_quan"
                              AND TABLE_NAME = "' . _DB_PREFIX_ . 'product_update_product_detail"
                              AND TABLE_SCHEMA = "' . _DB_NAME_ . '"';
        $check_quan_col = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_quan_col_sql);
        if ((int) $check_quan_col == 0) {
            $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_update_product_detail` ADD `req_quan` int(11) NOT NULL DEFAULT "0" AFTER `store_id`';
            Db::getInstance()->execute($query);
        }
        $check_col_sql = 'SELECT count(*) FROM information_schema.COLUMNS
                              WHERE COLUMN_NAME = "allowed_order"
                              AND TABLE_NAME = "' . _DB_PREFIX_ . 'product_update_product_detail"
                              AND TABLE_SCHEMA = "' . _DB_NAME_ . '"';
        $check_col = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_col_sql);
        if ((int) $check_col == 0) {
            $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_update_product_detail` ADD `allowed_order` int(10) NULL AFTER `store_id`';
            Db::getInstance()->execute($query);
        }
        $check_col_sql = 'SELECT count(*) FROM information_schema.COLUMNS
                              WHERE COLUMN_NAME = "update_email"
                              AND TABLE_NAME = "' . _DB_PREFIX_ . 'product_update_product_detail"
                              AND TABLE_SCHEMA = "' . _DB_NAME_ . '"';
        $check_col = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_col_sql);
        if ((int) $check_col == 0) {
            $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_update_product_detail` ADD `update_email` int(10) NULL AFTER `store_id`';
            Db::getInstance()->execute($query);
        }
        $create_stat_table = 'create table if not exists `' . _DB_PREFIX_ . 'product_update_product_stats`
					(
					`id` int(100) NOT NULL AUTO_INCREMENT,
					`total_sent` int(11) NOT NULL,
					`total_opened` int(11) NOT NULL,
					`total_buy_now_clicks` varchar(100) NOT NULL,
					`total_view_clicks` varchar(50) NOT NULL,
					`date_added` datetime NOT NULL,
					`date_updated` datetime NOT NULL,
					PRIMARY KEY (`id`)) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_stat_table);
        /*Changes Over*/
        //Create Email templates table
        $create_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'product_update_email_templates` (
			`id_template` int(10) NOT NULL auto_increment,
			`template_no` enum("1", "2", "3"),
			`id_lang` int(10) NOT NULL,
			`shop_id` INT( 11 ) NOT NULL DEFAULT  "0",
			`iso_code` char(4) NOT NULL,
			`subject` varchar(255) NOT NULL,
			`body` Text NULL,
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_template`),
			INDEX (  `id_lang` )
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
        Db::getInstance()->execute($create_table);

        $alter_table = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_update_email_templates` CHANGE `template_no` `template_no` ENUM("1","2", "3") CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL';
        Db::getInstance()->execute($alter_table);

        //chanegs by vishal for truncate the table to add the related product functionality
        if (!Configuration::get('VELSOF_PRODUCT_UPDATE_RELATED_CHECK')) {
            $sql = 'TRUNCATE TABLE ' . _DB_PREFIX_ . 'product_update_email_templates';
            Db::getInstance()->execute($sql);
        }
        //changes end
        //changes by gopi for custom feilds
        $create_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_bis_custom_field_mapping` (
                        `id_mapping` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `id_prouct_customer` int(11) UNSIGNED NOT NULL DEFAULT "0",
                        `id_field` int(11) UNSIGNED NOT NULL DEFAULT "0",
                        `value` text,
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime DEFAULT NULL,
                        PRIMARY KEY (`id_mapping`)
                    )ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
        Db::getInstance()->execute($create_table);
        $create_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_bis_fields` (
                            `id_field` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                            `field_name` varchar(255) DEFAULT NULL,
                            `type` varchar(255) NOT NULL,
                            `validation` varchar(255) DEFAULT NULL,
                            `html_id` varchar(255) DEFAULT NULL,
                            `html_class` varchar(255) DEFAULT NULL,
                            `max_length` int(11) UNSIGNED DEFAULT NULL,
                            `min_length` int(11) UNSIGNED DEFAULT NULL,
                            `required` int(2) UNSIGNED NOT NULL DEFAULT "0",
                            `position` int(11) UNSIGNED NOT NULL DEFAULT "0",
                            `active` tinyint(1) UNSIGNED NOT NULL DEFAULT "0",
                            `date_add` datetime NOT NULL,
                            `date_upd` datetime DEFAULT NULL,
                            PRIMARY KEY (`id_field`)
                          ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
        Db::getInstance()->execute($create_table);
        $create_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_bis_fields_lang` (
                            `id_field` int(11) UNSIGNED NOT NULL,
                            `id_lang` int(11) UNSIGNED NOT NULL,
                            `id_shop` int(11) UNSIGNED NOT NULL,
                            `label` varchar(255) DEFAULT NULL,
                            `description` text,
                            `value` text,
                            `placeholder` varchar(255) DEFAULT NULL,
                            `error_msg` varchar(255) DEFAULT NULL,
                            PRIMARY KEY (`id_field`,`id_lang`,`id_shop`)
                          ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
        Db::getInstance()->execute($create_table);

        $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . _DB_NAME_ . '" AND TABLE_NAME="' . _DB_PREFIX_ . 'kb_bis_fields_lang" AND column_name="id_shop"';
        $data_type = Db::getInstance()->getValue($select_datatype);
        if (!empty($data_type)) {
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'kb_bis_fields_lang DROP COLUMN id_shop');
        }

        $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . _DB_NAME_ . '" AND TABLE_NAME="' . _DB_PREFIX_ . 'kb_bis_fields" AND column_name="id_shop"';
        $data_type = Db::getInstance()->getValue($select_datatype);
        if (empty($data_type)) {
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'kb_bis_fields ADD COLUMN `id_shop` text');
        }
        //changes by gopi end here
        $this->context->smarty->assign(
            'thanks', $this->context->link->getMediaLink(
                __PS_BASE_URI__ . 'modules/backinstock/views/img/thank-you-banners.jpg'
            )
        );
        $this->context->smarty->assign(
            'shop_logo', $this->context->link->getMediaLink(
                __PS_BASE_URI__ . 'img/logo.jpg'
            )
        );
        $default_body = $this->display(__FILE__, 'views/templates/admin/initial_mail.tpl');
        $default_body = str_replace('[', '{', $default_body);
        $default_body = str_replace(']', '}', $default_body);
        $default_subject = 'Product Update';
        $default_subject = $this->l('Product alert added successfully');
        if ((!Configuration::get('VELSOF_PRODUCT_UPDATE_MAIL_CHECK')) && (!Configuration::get('VELSOF_PRODUCT_UPDATE_LOW_STOCK_MAIL_CHECK')) || (!Configuration::get('VELSOF_PRODUCT_UPDATE_RELATED_CHECK'))) {
            foreach (Language::getLanguages(false) as $lang) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'product_update_email_templates SET id_lang = '
                    . (int) $lang['id_lang'] . ',
		template_no="1",
		iso_code="' . pSQL($lang['iso_code']) . '",subject = "'
                    . pSQL(Tools::htmlentitiesUTF8($default_subject)) . '",body = "'
                    . pSQL(Tools::htmlentitiesUTF8($default_body)) . '",shop_id = ' . (int) $this->context->shop->id . ',
		date_add = now(), date_upd = now()';
                Db::getInstance()->execute($sql);
            }
        }
        //        $this->context->smarty->assign('new_price_alert', $this->context->link->getMediaLink(
//                    __PS_BASE_URI__ . 'modules/backinstock/views/img/new-price-alert.jpg'
//            ));
        $default_body = $this->display(__FILE__, 'views/templates/admin/final_mail.tpl');
        $default_body = str_replace('[', '{', $default_body);
        $default_body = str_replace(']', '}', $default_body);
        $default_subject = 'Product Update';
        $default_subject = $this->l('Good news!!! Product is back in stock');
        /*
         * @author - Rishabh Jain
         * DOC - 31/01/20
         * Added (!Configuration::get('VELSOF_PRODUCT_UPDATE_LOW_STOCK_MAIL_CHECK') condition
         * as added a new template in the table for low stock alert mails
         */
        if ((!Configuration::get('VELSOF_PRODUCT_UPDATE_MAIL_CHECK')) && (!Configuration::get('VELSOF_PRODUCT_UPDATE_LOW_STOCK_MAIL_CHECK')) || (!Configuration::get('VELSOF_PRODUCT_UPDATE_RELATED_CHECK'))) {
            foreach (Language::getLanguages(false) as $lang) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'product_update_email_templates
                        SET id_lang = ' . (int) $lang['id_lang'] . ',
			template_no="2",
                        iso_code="' . pSQL($lang['iso_code']) . '", 
                        subject = "' . pSQL(Tools::htmlentitiesUTF8($default_subject)) . '", 
                        body = "' . pSQL(Tools::htmlentitiesUTF8($default_body)) . '",
			shop_id = ' . (int) $this->context->shop->id . ',
                        date_add = now(), date_upd = now()';
                Db::getInstance()->execute($sql);
            }
        }

        /*
         * @author - Rishab Jain
         * To add low stock alert mail template content
         */
        $default_body = $this->display(__FILE__, 'views/templates/admin/low_stock_mail.tpl');
        $default_body = str_replace('[', '{', $default_body);
        $default_body = str_replace(']', '}', $default_body);
        $default_subject = $this->l('Hurry!!! Subscribed product is low in stock');
        if ((!Configuration::get('VELSOF_PRODUCT_UPDATE_MAIL_CHECK')) && (!Configuration::get('VELSOF_PRODUCT_UPDATE_LOW_STOCK_MAIL_CHECK')) || (!Configuration::get('VELSOF_PRODUCT_UPDATE_RELATED_CHECK'))) {
            foreach (Language::getLanguages(false) as $lang) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'product_update_email_templates
                        SET id_lang = ' . (int) $lang['id_lang'] . ',
			template_no="3",
                        iso_code="' . pSQL($lang['iso_code']) . '", 
                        subject = "' . pSQL(Tools::htmlentitiesUTF8($default_subject)) . '", 
                        body = "' . pSQL(Tools::htmlentitiesUTF8($default_body)) . '",
			shop_id = ' . (int) $this->context->shop->id . ',
                        date_add = now(), date_upd = now()';
                Db::getInstance()->execute($sql);
            }
        }
        // changes over
        if ((!Configuration::get('VELSOF_PRODUCT_UPDATE_MAIL_CHECK')) && (!Configuration::get('VELSOF_PRODUCT_UPDATE_LOW_STOCK_MAIL_CHECK')) || (!Configuration::get('VELSOF_PRODUCT_UPDATE_RELATED_CHECK'))) {
            $mail_dir = dirname(__FILE__) . '/mails/en';
            $languages = Language::getLanguages(false);
            $language_count = count($languages);
            for ($i = 0; $i < $language_count; $i++) {
                if ($languages[$i]['iso_code'] != 'en') {
                    $new_dir = dirname(__FILE__) . '/mails/' . $languages[$i]['iso_code'];
                    $this->copyfolder($mail_dir, $new_dir);
                }
            }
            Configuration::updateGlobalValue('VELSOF_PRODUCT_UPDATE_MAIL_CHECK', 1);
            Configuration::updateGlobalValue('VELSOF_PRODUCT_UPDATE_LOW_STOCK_MAIL_CHECK', 1);
            Configuration::updateGlobalValue('VELSOF_PRODUCT_UPDATE_RELATED_CHECK', 1);
        }

        if (Configuration::get('VELOCITY_PRODUCT_UPDATE')) {
            Configuration::deleteByName('VELOCITY_PRODUCT_UPDATE');
        }
        $default_data = $this->getDefaultSettings();
        $default_aval_data = $this->getDefaultAvalSettings();
        Configuration::updateValue('VELOCITY_PRODUCT_UPDATE', serialize($default_data), true);
        Configuration::updateValue('VELOCITY_AVAILABILITY_SETTINGS', serialize($default_aval_data), true);

        return true;
    }

    public function uninstall()
    {
        if (
            !parent::uninstall() || !Configuration::deleteByName('VELOCITY_PRODUCT_UPDATE') ||
            !$this->unregisterHook('displayLeftColumnProduct') ||
            !$this->unregisterHook('displayCustomerAccount') || // hook to add the subscription list option in menu
            !$this->unregisterHook('actionProductSave') ||
            !$this->unregisterHook('actionUpdateQuantity') ||
            !$this->unregisterHook('actionExportGDPRData') ||
            !$this->unregisterHook('displayBackOfficeHeader') ||
            !$this->unregisterHook('actionDeleteGDPRCustomer') ||
            !$this->unregisterHook('actionValidateOrder') || // hook to sed low stock mail alert and keep track of orders of subscribers
            !$this->unregisterHook('actionProductUpdate')
        ) {
            return false;
        }
        $this->unInstallBackInStockTabs();
        return true;
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            if (Module::isInstalled('backinstock')) {
                $config = Tools::unserialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
                if (($config['enable'] == 1) && ($config['enable_gdpr_delete'] == 1)) {
                    $checkCustomerExistSql = "Select * from " . _DB_PREFIX_ . "product_update_product_detail where email ='" . pSQL($customer['email']) . "'";
                    $checkCustomerExistData = Db::getInstance()->ExecuteS($checkCustomerExistSql);
                    if (count($checkCustomerExistData)) {
                        $sql = "DELETE FROM " . _DB_PREFIX_ . "product_update_product_detail WHERE email = '" . pSQL($customer['email']) . "'";
                        Db::getInstance()->execute($sql);
                        return json_encode(true);
                    } else {
                        return json_encode($this->l('Back In Stock : No user found with this email.'));
                    }
                }
            }
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if (Module::isInstalled('backinstock')) {
            //To display tab icon at back-office
            $this->context->controller->addCSS(($this->_path) . 'views/css/admin/menuTabIcon.css');
        }
    }

    public function hookActionExportGDPRData($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            if (Module::isInstalled('backinstock')) {
                $config = Tools::unserialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
                if (($config['enable'] == 1)) {
                    $checkCustomerExistSql = "Select * from " . _DB_PREFIX_ . "product_update_product_detail where email ='" . pSQL($customer['email']) . "'";
                    $checkCustomerExistData = Db::getInstance()->ExecuteS($checkCustomerExistSql);
                    if (count($checkCustomerExistData)) {
                        $resArray = array();
                        foreach ($checkCustomerExistData as $key => $val) {
                            $resArray[] = array(
                                $this->l('Email') => $val['email'],
                                $this->l('Product Name') => $val['product_name'],
                                $this->l('Reference') => $val['skv'],
                                $this->l('Current Price') => $val['current_price'],
                                $this->l('Currency') => $val['currency_code'],
                                $this->l('Date Created') => $val['date_added']
                            );
                        }
                        return json_encode($resArray);
                    }
                    return json_encode($this->l('Back In Stock : No user found with this email.'));
                }
            }
        }
    }

    public function getContent()
    {
        $this->addBackOfficeMedia();
        include_once dirname(__FILE__) . '/classes/productupdate_tracker.php';
        $output = null;
        $languages = Language::getLanguages(false);
        $shop_id = Context::getContext()->shop->id;
        $lang_id = $this->context->cookie->id_lang;
        /*
         * @author - Rishabh Jain
         * DOC - 30/01/20
         * To fetch the email marketing lists as per api
         */
        if (Tools::isSubmit('ajax')) {
            $this->ajaxProcess(Tools::getValue('method'));
        }
        if (Tools::isSubmit('ajax_rend')) {
            echo $this->ajaxHandler($_POST);
            die;
        }
        if (Tools::isSubmit('pop_up')) {
            $popup_data = Tools::getValue('attribute');
            echo $this->displayPopup($popup_data);
            die;
        }
        // Changes by prvind for adding product auto complete field
        if (Tools::getvalue('ajaxproductaction')) {
            echo $this->ajaxproductlist();
            die;
        }
        // Changes Over
        $filter_data = array();
        if (Tools::isSubmit('graph')) {
            $filter_data['from'] = Tools::getValue('from');
            $filter_data['to'] = Tools::getValue('to');

            echo $this->getGraphData($filter_data);
            die;
        }
        if (Tools::isSubmit('statsgraph')) {
            echo $this->geStatshData();
            die;
        }
        if (Tools::isSubmit('velocity_email_template')) {
            $temp_status = 0;
            $temp_data = Tools::getValue('velocity_email_template');
            if (isset($temp_data['subject'])) {
                if ($temp_data['subject'] == '') {
                    $this->context->controller->errors[] = $this->l('Initial template subject can not be empty');
                    $temp_status = 1;
                }
                if ($temp_data['content'] == '') {
                    $this->context->controller->errors[] = $this->l('Initial template content can not be empty');
                    $temp_status = 1;
                }
            }
            if (isset($temp_data['subject_final'])) {
                if ($temp_data['subject_final'] == '') {
                    $this->context->controller->errors[] = $this->l('Final template subject can not be empty');
                    $temp_status = 1;
                }
                if ($temp_data['content_drop'] == '') {
                    $this->context->controller->errors[] = $this->l('Final template content can not be empty');
                    $temp_status = 1;
                }
            }
            if ($temp_status == 0) {
                $output .= $this->displayConfirmation($this->l('Email template updated succesfully'));
            }
        }
        /*
         * @author - Rishabh Jain
         * DOC - 03/02/20
         * To check if the low stock alert mail content is empty or not
         */
        if (Tools::isSubmit('velocity_low_stock_alert_setting')) {
            $temp_status = 0;
            $temp_data = Tools::getValue('velocity_low_stock_alert_setting');
            if (isset($temp_data['subject'])) {
                if ($temp_data['subject'] == '') {
                    $this->context->controller->errors[] = $this->l('Low Stock Alert Email template subject can not be empty');
                    $temp_status = 1;
                }
                if ($temp_data['content'] == '') {
                    $this->context->controller->errors[] = $this->l('Low Stock Alert Email template content can not be empty');
                    $temp_status = 1;
                }
            }
            if ($temp_status == 0) {
                $output .= $this->displayConfirmation($this->l('Email template updated succesfully'));
            }
        }

        /*
         * @author Rishabhb Jain
         * DOC - 31st Jan 2020
         * to save the email marketing configuration
         */
        if (Tools::isSubmit('back_stock_email')) {
            $temp_default = $this->getDefaultEmailMarketingSettings();
            $form_value = Tools::getValue('back_stock_email');
            $email_marketing_values = array();
            $email_marketing_values['mailchimp_api'] = $form_value['mailchimp_api'];
            $email_marketing_values['mailchimp_status'] = $form_value['mailchimp_status'];
            $email_marketing_values['mailchimp_list'] = isset($form_value['mailchimp_list']) ? $form_value['mailchimp_list'] : '';
            $email_marketing_values['klaviyo_status'] = isset($form_value['klaviyo_status']) ? $form_value['klaviyo_status'] : '';
            $email_marketing_values['klaviyo_api'] = isset($form_value['klaviyo_api']) ? $form_value['klaviyo_api'] : '';
            $email_marketing_values['klaviyo_list'] = isset($form_value['klaviyo_list']) ? $form_value['klaviyo_list'] : '';
            $email_marketing_values['SendinBlue_status'] = isset($form_value['SendinBlue_status']) ? $form_value['SendinBlue_status'] : '';
            $email_marketing_values['SendinBlue_api'] = isset($form_value['SendinBlue_api']) ? $form_value['SendinBlue_api'] : '';
            $email_marketing_values['SendinBlue_list'] = isset($form_value['SendinBlue_list']) ? $form_value['SendinBlue_list'] : '';
            Configuration::updateValue('VELOCITY_BACK_STOCK_EMAIL_MARKETING', serialize($email_marketing_values), true);
            $output .= $this->displayConfirmation($this->l('Settings has been updated successfully'));
        }
        // changes over
        //start by dharmanshu for the recaptcha 19-08-2021
        if (Tools::isSubmit('KB_BACKINSTOCK_RECAPTCHA_ENABLE')) {
            Configuration::updateGlobalValue('KB_BACKINSTOCK_RECAPTCHA_ENABLE', Tools::getValue('KB_BACKINSTOCK_RECAPTCHA_ENABLE'));
            Configuration::updateGlobalValue('KB_BACKINSTOCK_RECAPTCHA_SITE_KEY', Tools::getValue('KB_BACKINSTOCK_RECAPTCHA_SITE_KEY'));
            Configuration::updateGlobalValue('KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY', Tools::getValue('KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY'));
            $output .= $this->displayConfirmation($this->l('Recaptcha Settings has been updated successfully'));
        }
        //end by dharmanshu for the recaptcha 19-08-2021
        //Changes Start by Prvind Panday for the availability settings
        if (Tools::isSubmit('availability_form')) {
            $aval_post_data = Tools::getValue('availability_form');
            $aval_post_data['prestashop_category'] = Tools::getvalue('prestashop_category');
            Configuration::updateValue('VELOCITY_AVAILABILITY_SETTINGS', serialize($aval_post_data), true);
            $output .= $this->displayConfirmation($this->l('Settings has been updated successfully'));
        }
        // Changes Over
        if (Tools::isSubmit('product_update')) {
            $temp_default = $this->getDefaultSettings();
            $post_data = Tools::getValue('product_update');
            /* Knowband validation start */
            if (empty($post_data['background']) || !Validate::isColor($post_data['background']) || !$post_data['background']) {
                $output .= $this->displayError(
                    $this->l('Error: Background color value is invalid.')
                );
            } elseif (empty($post_data['border']) || !Validate::isColor($post_data['border']) || !$post_data['border']) {
                $output .= $this->displayError(
                    $this->l('Error:  Border color value is invalid.')
                );
            } elseif (empty($post_data['text']) || !Validate::isColor($post_data['text']) || !$post_data['text']) {
                $output .= $this->displayError(
                    $this->l('Error: Text color value is invalid.')
                );
            } else {
                /* Knowband validation start */
                $my_module_lang = Tools::getValue('velsof_translator');
                $lang_iso = $my_module_lang['selected_language'];
                $post_data['plugin_id'] = $temp_default['plugin_id'];
                $post_data['version'] = $temp_default['version'];
                foreach ($languages as $lang) {
                    $post_data['product_update_gdpr_policy_text'][$lang['id_lang']] = Tools::getValue(
                        'product_update_gdpr_policy_text_' . $lang['id_lang']
                    );
                }
                //changes by vishal
                foreach ($languages as $lang) {
                    $post_data['initial_related_title'][$lang['id_lang']] = Tools::getValue(
                        'initial_related_title_' . $lang['id_lang']
                    );
                }
                foreach ($languages as $lang) {
                    $post_data['final_related_title'][$lang['id_lang']] = Tools::getValue(
                        'final_related_title_' . $lang['id_lang']
                    );
                }
                foreach ($languages as $lang) {
                    $post_data['low_stock_related_title'][$lang['id_lang']] = Tools::getValue(
                        'low_stock_related_title_' . $lang['id_lang']
                    );
                }
                //changes end

                foreach ($languages as $lang) {
                    $post_data['product_update_gdpr_policy_url'][$lang['id_lang']] = Tools::getValue(
                        'product_update_gdpr_policy_url_' . $lang['id_lang']
                    );
                }
                $flag = 0;
                Configuration::updateValue('VELOCITY_PRODUCT_UPDATE', serialize($post_data), true);
                Configuration::updateValue('KB_BACKINSTOCK_CSS', Tools::getValue('kb_backinstock_css'), true);
                Configuration::updateValue('KB_BACKINSTOCK_JS', Tools::getValue('kb_backinstock_js'), true);
                Configuration::updateValue('VELOCITY_PRODUCT_UPDATE_LANG_' . $lang_iso, serialize($my_module_lang), true);
                $output .= $this->displayConfirmation($this->l('Settings has been updated successfully'));
            }
        }
        $languages = Language::getLanguages(false);
        $this->smarty->assign('languages', $languages);
        $this->available_tabs_lang = array(
            'General Settings' => $this->l('General Settings'),
            'Initial Email Settings' => $this->l('Initial Email Settings'),
            'Final Email Settings' => $this->l('Final Email Settings'),
            'Low_Stock_Email_Settings' => $this->l('Low Stock Email Settings'),
            // to add the low stock mail alert tab
            'Email_Marketing_Setting' => $this->l('Email Marketing Setting'),
            // to add the Email marketing tab
//            'Subscriber_List' => $this->l('Subscriber List'),
            'Analysis' => $this->l('Analysis'),
            'V3 Recaptcha Settings' => $this->l('V3 Recaptcha Settings'),
            // v3 recaptcha settings by dharmanshu 19-08-2021
            'Availability Settings' => $this->l('Availability Settings'),
            'Statistics' => $this->l('Statistics'),
        );
        $this->available_tabs = array(
            'General Settings',
            'Initial Email Settings',
            'Final Email Settings',
            'Low_Stock_Email_Settings',
            'Email_Marketing_Setting',
            //            'Subscriber_List',
            'Analysis',
            'V3 Recaptcha Settings',
            // v3 recaptcha settings by dharmanshu 19-08-2021
            'Availability Settings',
            'Statistics',
        );
        $this->tab_display = 'General Settings';
        $product_tabs = array();
        foreach ($this->available_tabs as $key => $product_tab) {
            $product_tabs[$product_tab] = array(
                'data' => $key,
                'id' => $product_tab,
                'selected' => (Tools::strtolower($product_tab) == Tools::strtolower($this->tab_display) || (isset($this->tab_display_module) && 'module' .
                    $this->tab_display_module == Tools::strtolower($product_tab))),
                'name' => $this->available_tabs_lang[$product_tab],
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            );
        }
        $settings = Configuration::get('VELOCITY_PRODUCT_UPDATE');
        $customcss = Configuration::get('KB_BACKINSTOCK_CSS');
        $customjs = Configuration::get('KB_BACKINSTOCK_JS');
        $aval_settings = Configuration::get('VELOCITY_AVAILABILITY_SETTINGS');
        $this->product_update_settings = Tools::unSerialize($settings);
        $this->product_update_css['css'] = $customcss;
        $this->product_update_js['js'] = $customjs;
        $this->product_aval_settings = Tools::unSerialize($aval_settings);
        if (!Configuration::get('VELOCITY_PRODUCT_UPDATE') || Configuration::get('VELOCITY_PRODUCT_UPDATE') == '') {
            $this->demo_settings = $this->getDefaultSettings();
        } else {
            $this->demo_settings = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        }
        $get_product = 'select price.*,cat.name from ' . _DB_PREFIX_ . 'product_update_product_detail '
            . 'price,' . _DB_PREFIX_ . 'product_lang cat 
                        where price.product_id=cat.id_product and price.active=1 and
                        cat.id_lang=' . (int) $this->context->cookie->id_lang . ' and cat.id_shop='
            . (int) $shop_id . ' and price.store_id=' . (int) $shop_id;
        $product_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_product);
        $product_arr = array();
        $product_list = array();
        $flag = 0;
        foreach ($product_data as $product) {
            $date_added = new DateTime($product['date_added']);
            $today = new DateTime(date('Y-m-d', time()));
            $interval = $date_added->diff($today);

            if ($interval->days >= 0) {
                $product_obj = new Product($product['product_id'], false, $lang_id, $shop_id);
                $attributes = $product_obj->getAttributeCombinationsById($product['product_attribute_id'], $lang_id);
                if (count($attributes) > 0) {
                    $get_attribute_reference = 'SELECT reference FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = ' . $product['product_id'] . ' AND id_product_attribute = ' . $product['product_attribute_id'];
                    $attribute_reference_data = Db::getInstance()->getRow($get_attribute_reference);
                    $product['model'] = $attribute_reference_data['reference'];
                    $product['attributes'] = '';
                    foreach ($attributes as $attribute) {
                        $product['attributes'] .= $attribute['group_name'] . ': ' . $attribute['attribute_name'] . ', ';
                    }
                    $product['attributes'] = Tools::substr($product['attributes'], 0, -2);
                } else {
                    $product['model'] = $product_obj->reference;
                    $product['attributes'] = '';
                }
                //                if (count($attributes) > 0) {
//                    $product['attributes'] = '';
//                    foreach ($attributes as $attribute) {
//                        $product['attributes'] .= $attribute['group_name'] . ': ' . $attribute['attribute_name'] . ', ';
//                    }
//                    $product['attributes'] = Tools::substr($product['attributes'], 0, -2);
//                } else {
//                    $product['attributes'] = '';
//                }
//
//                $product['model'] = $product_obj->reference;

                $check_query = 'select odr.product_id from ' . _DB_PREFIX_ . 'order_detail odr, '
                    . _DB_PREFIX_ . 'orders od
                    where od.id_order in (select id_order from ' . _DB_PREFIX_ . 'orders where 
                    id_customer in (select id_customer from ' . _DB_PREFIX_ . "customer where email='"
                    . pSQL($product['email']) . "') and 
                    od.date_add > '" . pSQL($product['date_updated']) . "') and odr.id_order = od.id_order and
                    odr.product_attribute_id=" . (int) $product['product_attribute_id'] . ' and
                    odr.product_id=' . (int) $product['product_id'];

                $pro_found = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($check_query);
                if (count($pro_found) >= 1) {
                    $product['status'] = 'Yes';
                } else {
                    $product['status'] = 'No';
                }

                $product['current_price'] = Tools::displayPrice($product['current_price']);
                $product_arr[$flag] = $product;
                $flag++;
                unset($product_obj);
            }
        }
        $flag = 0;
        $get_product = 'select price.*,cat.name,count(*),product_attribute_id from '
            . _DB_PREFIX_ . 'product_update_product_detail
            price,' . _DB_PREFIX_ . 'product_lang cat where price.product_id=cat.id_product and price.active=1 and
                        cat.id_lang=' . (int) $this->context->cookie->id_lang . ' and cat.id_shop=' . (int) $shop_id . '
                        and price.store_id=' . (int) $shop_id . ' group by price.product_id,price.product_attribute_id';
        $product_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_product);
        foreach ($product_data as $product) {
            $date_added = new DateTime($product['date_added']);
            $today = new DateTime(date('Y-m-d', time()));
            $interval = $date_added->diff($today);

            if ($interval->days >= 0) {
                $product_obj = new Product($product['product_id'], false, $lang_id, $shop_id);
                $attributes = $product_obj->getAttributeCombinationsById($product['product_attribute_id'], $lang_id);

                if (count($attributes) > 0) {
                    $get_attribute_reference = 'SELECT reference FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = ' . $product['product_id'] . ' AND id_product_attribute = ' . $product['product_attribute_id'];
                    $attribute_reference_data = Db::getInstance()->getRow($get_attribute_reference);
                    $product['model'] = $attribute_reference_data['reference'];
                    $product['attributes'] = '';
                    foreach ($attributes as $attribute) {
                        $product['attributes'] .= $attribute['group_name'] . ': ' . $attribute['attribute_name'] . ', ';
                    }
                    $product['attributes'] = Tools::substr($product['attributes'], 0, -2);
                } else {
                    $product['model'] = $product_obj->reference;
                    $product['attributes'] = '';
                }
                //                    $product['attributes'] = '';
//                    foreach ($attributes as $attribute) {
//                        $product['attributes'] .= $attribute['group_name'] . ': ' . $attribute['attribute_name'] . ', ';
//                    }
//                    $product['attributes'] = Tools::substr($product['attributes'], 0, -2);
//                } else {
//                    $product['attributes'] = '';
//                }
//                $product['model'] = $product_obj->reference;

                $product['count'] = $product['count(*)'];
                $product['product_attribute_id'] = $product['product_attribute_id'];
                $product['current_price'] = Tools::displayPrice($product['current_price']);
                $product_list[$flag] = $product;
                $flag++;
                unset($product_obj);
            }
        }
        $category_query = 'select id_category,name from ' . _DB_PREFIX_ . 'category_lang
                        where id_category>2 and id_shop=' . (int) $shop_id . ' and id_lang=' . (int) $lang_id;
        $category_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($category_query);
        $flag = 0;
        $category = array();
        foreach ($category_data as $cat) {
            $category[$flag] = $cat;
            $flag++;
        }
        $this->smarty->assign('pal_default_lang', $this->context->language->iso_code);
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $version = 5;
        } else {
            $version = 6;
        }
        $languages = Language::getLanguages(false);
        $lang = array(
            0 =>
            array(
                'id_lang' => 0,
                'name' => $this->l('Please Select Language')
            )
        );
        //changes by vishal
        $display_methods = array(
            0 => array(
                'id' => '1',
                'name' => $this->l('Best Seller'),
            ),
            1 => array(
                'id' => '2',
                'name' => $this->l('Products of same category'),
            ),
            2 => array(
                'id' => '3',
                'name' => $this->l('Specific Products'),
            )
        );
        // Changes by prvind
        $hooks = array(
            0 => array(
                'id' => '1',
                'name' => $this->l('Pre Defined Hook'),
            ),
            1 => array(
                'id' => '2',
                'name' => $this->l('Custom Hook'),
            )
        );
        // Changes end here
        $included_products_arry = array();
        $included_products = Product::getProducts($this->context->language->id, 0, 0, 'name', 'asc', false, true);
        if (!empty($included_products)) {
            foreach ($included_products as $included_product) {
                if (isset($this->context->cookie->kb_id_product_cookie)) {
                    if ($included_product['id_product'] != $this->context->cookie->kb_id_product_cookie) {
                        $included_products_arry[] = $included_product;
                    }
                }
            }
        }

        if (empty($included_products_arry)) {
            $included_products_arry = $included_products;
        }
        //changes end
        $lang = array_merge($lang, $languages);
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('General Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable/Disable'),
                            'name' => 'product_update[enable]',
                            'class' => 't',
                            'required' => true,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'value' => 1,
                                    'id' => 'enable_radio',
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'value' => 0,
                                    'id' => 'disable_radio',
                                    'label' => $this->l('Disabled')
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('Enable/Disable Cron'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[enable_cron]',
                            'values' => array(
                                array(
                                    'id' => 'product_update[enable_cron]_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id' => 'product_update[enable_cron]_off',
                                    'value' => 0,
                                ),
                            ),
                            'hint' => $this->l('Enable/Disable the plugin')
                        ),
                        array(
                            'label' => $this->l('Enable/Disable Quantity Field On Subscription Box'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[enable_quantity]',
                            'values' => array(
                                array(
                                    'id' => 'product_update[enable_quantity]_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id' => 'product_update[enable_quantity]_off',
                                    'value' => 0,
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('Update Subscribers If the subscribed product is set to allow order.'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[update_subscribers]',
                            'values' => array(
                                array(
                                    'id' => 'product_update[update_subscribers]_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id' => 'product_update[update_subscribers]_off',
                                    'value' => 0,
                                ),
                            ),
                        ),
                        array(
                            'type' => 'select',
                            'class' => 'general_tab',
                            'label' => $this->l('Select Hook'),
                            'required' => true,
                            'name' => 'product_update[display_hook]',
                            'options' => array(
                                'query' => $hooks,
                                'id' => 'id',
                                'name' => 'name'
                            ),
                            'desc' => $this->l('In case of custom hook, Paste the code anywhere in template file of product page to display the block at your desired position. Code: {hook h="displayCustomAlertBlockAnywhere"}')
                        ),
                        array(
                            'label' => $this->l('Delete Customer Data On Delete Request'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[enable_gdpr_delete]',
                            'values' => array(
                                array(
                                    'value' => 1,
                                ),
                                array(
                                    'value' => 0,
                                ),
                            ),
                            'hint' => $this->l('Enable/Disable to delete customer data on GDPR module delete request.'),
                            'desc' => $this->l('Enable/Disable to delete customer data on GDPR module delete request.')
                        ),
                        array(
                            'label' => $this->l('Enable/Disable Privacy Policy'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[enable_gdpr_policy]',
                            'values' => array(
                                array(
                                    'value' => 1,
                                ),
                                array(
                                    'value' => 0,
                                ),
                            ),
                            'hint' => $this->l('Enable/Disable to show/hide consent checkbox from frontend.'),
                            'desc' => $this->l('Enable/Disable to show/hide consent checkbox from frontend.')
                        ),
                        array(
                            'label' => $this->l('Privacy Policy Text'),
                            'required' => true,
                            'type' => 'text',
                            'lang' => true,
                            'class' => '',
                            'class' => '',
                            'name' => 'product_update_gdpr_policy_text',
                            'hint' => $this->l('Enter the Privacy policy text which will be displayed on frontend product update notification popup.'),
                            'desc' => $this->l('Enter the Privacy policy text which will be displayed on frontend product update notification popup.')
                        ),
                        array(
                            'label' => $this->l('Privacy Policy Page URL'),
                            'required' => true,
                            'type' => 'text',
                            'lang' => true,
                            'class' => '',
                            'name' => 'product_update_gdpr_policy_url',
                            'hint' => $this->l('Enter the Privacy policy page URL where you have define privacy policy.'),
                            'desc' => $this->l('Enter the Privacy policy page URL where you have define privacy policy.')
                        ),
                        /*
                         * @author - Rishabh Jain
                         * DOC - 29/01/20
                         * To add the enable/disbale subscription list,remove button and listing page size
                         */
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable/Disable Subscription Listing Page'),
                            'name' => 'product_update[enable_subscription_list]',
                            'class' => 't',
                            'required' => true,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'value' => 1,
                                    'id' => 'enable_radio',
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'value' => 0,
                                    'id' => 'disable_radio',
                                    'label' => $this->l('Disabled')
                                ),
                            ),
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable Remove Subscription Button'),
                            'name' => 'product_update[enable_remove_subscription]',
                            'class' => 't',
                            'required' => true,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'value' => 1,
                                    'id' => 'enable_radio',
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'value' => 0,
                                    'id' => 'disable_radio',
                                    'label' => $this->l('Disabled')
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('Subscription Per Page'),
                            'required' => true,
                            'type' => 'text',
                            'class' => '',
                            'name' => 'product_update[subscription_per_page]',
                            'hint' => $this->l('Enter the number of subscription sto be shown on single page.'),
                            'desc' => $this->l('Enter the number of subscription sto be shown on single page.')
                        ),
                        /* changes over */
                        array(
                            'label' => $this->l('Background Color'),
                            'type' => 'color',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[background]',
                            'hint' => $this->l('Choose the background color for the Notify box.')
                        ),
                        array(
                            'label' => $this->l('Border Color'),
                            'type' => 'color',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[border]',
                            'hint' => $this->l('Choose the border color for the Notify box.')
                        ),
                        array(
                            'label' => $this->l('Text Color'),
                            'type' => 'color',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[text]',
                            'hint' => $this->l('Choose the text color for the Notify box.')
                        ),
                        array(
                            'label' => $this->l('Custom CSS'),
                            'type' => 'textarea',
                            'id' => 'quantity_min',
                            'class' => 'search_tab',
                            'name' => 'kb_backinstock_css',
                            'hint' => $this->l('Customize the css as per requirement')
                        ),
                        array(
                            'label' => $this->l('Custom JS'),
                            'type' => 'textarea',
                            'id' => 'quantity_min',
                            'class' => 'search_tab',
                            'name' => 'kb_backinstock_js',
                            'hint' => $this->l('Customize the JS as per requirement')
                        ),
                        array(
                            'type' => 'html',
                            'class' => 'col-lg-10 col-md-10',
                            'name' => 'cron_links',
                            'id' => 'cron_links',
                            'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'backinstock/views/templates/admin/crondetails.tpl'),
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right form_general'
                    ),
                ),
            );
        } else {
            $this->fields_form = array(
                'form' => array(
                    'name' => 'backinstock_general_form',
                    'id_form' => 'general_form',
                    'legend' => array(
                        'title' => $this->l('General Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable/Disable'),
                            'name' => 'product_update[enable]',
                            'class' => 't',
                            'required' => true,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'value' => 1,
                                    'id' => 'enable_radio',
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'value' => 0,
                                    'id' => 'disable_radio',
                                    'label' => $this->l('Disabled')
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('Enable/Disable Cron'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[enable_cron]',
                            'values' => array(
                                array(
                                    'id' => 'product_update[enable_cron]_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id' => 'product_update[enable_cron]_off',
                                    'value' => 0,
                                ),
                            ),
                            'hint' => $this->l('Enable/Disable the plugin')
                        ),
                        array(
                            'label' => $this->l('Enable/Disable Quantity Field On Subscription Box'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[enable_quantity]',
                            'values' => array(
                                array(
                                    'id' => 'product_update[enable_quantity]_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id' => 'product_update[enable_quantity]_off',
                                    'value' => 0,
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('Update Subscribers If the subscribed product is set to allow order.'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[update_subscribers]',
                            'values' => array(
                                array(
                                    'id' => 'product_update[update_subscribers]_on',
                                    'value' => 1,
                                ),
                                array(
                                    'id' => 'product_update[update_subscribers]_off',
                                    'value' => 0,
                                ),
                            ),
                        ),
                        array(
                            'type' => 'select',
                            'class' => 'general_tab',
                            'label' => $this->l('Select Hook'),
                            'required' => true,
                            'name' => 'product_update[display_hook]',
                            'options' => array(
                                'query' => $hooks,
                                'id' => 'id',
                                'name' => 'name'
                            ),
                            'desc' => $this->l('In case of custom hook, Paste the code anywhere in template file of product page to display the block at your desired position. Code: {hook h="displayCustomAlertBlockAnywhere"}')
                        ),
                        array(
                            'label' => $this->l('Delete Customer Data On Delete Request'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[enable_gdpr_delete]',
                            'values' => array(
                                array(
                                    'value' => 1,
                                ),
                                array(
                                    'value' => 0,
                                ),
                            ),
                            'hint' => $this->l('Enable/Disable to delete customer data on GDPR module delete request.'),
                            'desc' => $this->l('Enable/Disable to delete customer data on GDPR module delete request.')
                        ),
                        array(
                            'label' => $this->l('Enable/Disable Privacy Policy'),
                            'type' => 'switch',
                            'class' => 'general_tab',
                            'name' => 'product_update[enable_gdpr_policy]',
                            'values' => array(
                                array(
                                    'value' => 1,
                                ),
                                array(
                                    'value' => 0,
                                ),
                            ),
                            'hint' => $this->l('Enable/Disable to show/hide consent checkbox from frontend.'),
                            'desc' => $this->l('Enable/Disable to show/hide consent checkbox from frontend.')
                        ),
                        array(
                            'label' => $this->l('Privacy Policy Text'),
                            'required' => true,
                            'type' => 'text',
                            'lang' => true,
                            'class' => '',
                            'class' => '',
                            'name' => 'product_update_gdpr_policy_text',
                            'hint' => $this->l('Enter the Privacy policy text which will be displayed on frontend product update notification popup.'),
                            'desc' => $this->l('Enter the Privacy policy text which will be displayed on frontend product update notification popup.')
                        ),
                        array(
                            'label' => $this->l('Privacy Policy Page URL'),
                            'required' => true,
                            'type' => 'text',
                            'lang' => true,
                            'class' => '',
                            'name' => 'product_update_gdpr_policy_url',
                            'hint' => $this->l('Enter the Privacy policy page URL where you have define privacy policy.'),
                            'desc' => $this->l('Enter the Privacy policy page URL where you have define privacy policy.')
                        ),
                        /*
                         * @author - Rishabh Jain
                         * DOC - 29/01/20
                         * To add the enable/disbale subscription list,remove button and listing page size
                         */
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable/Disable Subscription Listing Page'),
                            'name' => 'product_update[enable_subscription_list]',
                            'class' => 'general_tab',
                            'required' => true,
                            'is_bool' => true,
                            'hint' => $this->l('If enabled then out of subscription list button will be displayed in customer account menu where customer can see all the subscriptions.'),
                            'values' => array(
                                array(
                                    'value' => 1,
                                    'id' => 'enable_radio',
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'value' => 0,
                                    'id' => 'disable_radio',
                                    'label' => $this->l('Disabled')
                                ),
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Remove Subscription Button'),
                            'name' => 'product_update[enable_remove_subscription]',
                            'class' => 'general_tab',
                            'required' => true,
                            'hint' => $this->l('If enabled then customer will have the option to opt out of any subscriptions.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'value' => 1,
                                    'id' => 'enable_radio',
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'value' => 0,
                                    'id' => 'disable_radio',
                                    'label' => $this->l('Disabled')
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('Subscription Per Page'),
                            'required' => true,
                            'type' => 'text',
                            'class' => 'general_tab',
                            'col' => 4,
                            'suffix' => $this->l('Subscriptions Per Page'),
                            'name' => 'product_update[subscription_per_page]',
                            'hint' => $this->l('Enter the number of subscriptions to be shown on single page.'),
                            'desc' => $this->l('Enter the number of subscriptions to be shown on single page.')
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Low Stock Alert Notification'),
                            'hint' => $this->l('Enable Low Stock Alert Notification i.e the notification will be sent to all subscribed customers when the quantity of the product goes below the low stock alert quantity.'),
                            'name' => 'product_update[enable_low_stock_alert]',
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('Low Stock Alert Quantity'),
                            'required' => true,
                            'type' => 'text',
                            'class' => 'general_tab',
                            'col' => 4,
                            'suffix' => $this->l('Quantity'),
                            'name' => 'product_update[low_stock_alert_quantity]',
                            'hint' => $this->l('Enter the quantities after which the low stock notification will be sent to subscribed customer.'),
                            'desc' => $this->l('Enter the quantities after which the low stock notification will be sent to subscribed customer.')
                        ),
                        //changes by vishal for adding related product functionality
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Related products on Initial Email'),
                            'hint' => $this->l('If Enabled, then  Related product is displayed on Initial Email.'),
                            'name' => 'product_update[enable_related_product_initial]',
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('Inital Email related product Tittle'),
                            'type' => 'text',
                            'lang' => true,
                            'class' => '',
                            'class' => '',
                            'name' => 'initial_related_title',
                            'hint' => $this->l('Enter the Tittle for the related product block in initial email.'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select related product method for Initial Email'),
                            'required' => true,
                            'hint' => $this->l('Related product will be displayed on the basis of selected method'),
                            'name' => 'product_update[related_product_method_initial]',
                            'options' => array(
                                'query' => $display_methods,
                                'id' => 'id',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Choose Specific products for Initial Email'),
                            'name' => 'product_update[specific_products_initial][]',
                            'required' => true,
                            'multiple' => 'true',
                            'id' => 'multiple-select-specific_products_initial',
                            'hint' => $this->l('Select the products to display on email'),
                            'options' => array(
                                'query' => $included_products_arry,
                                'id' => 'id_product',
                                'name' => 'name'
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Related products on Final Email'),
                            'hint' => $this->l('If Enabled, then  Related product is displayed on Final Email'),
                            'name' => 'product_update[enable_related_product_final]',
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('FInal Email related product Tittle'),
                            'type' => 'text',
                            'lang' => true,
                            'class' => '',
                            'class' => '',
                            'name' => 'final_related_title',
                            'hint' => $this->l('Enter the Tittle for the related product block in Final email.'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select related product method for Final Email'),
                            'required' => true,
                            'hint' => $this->l('Related product will be displayed on the basis of selected method'),
                            'name' => 'product_update[related_product_method_final]',
                            'options' => array(
                                'query' => $display_methods,
                                'id' => 'id',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Choose Specific products for Final Email'),
                            'name' => 'product_update[specific_products_final][]',
                            'required' => true,
                            'multiple' => 'true',
                            'id' => 'multiple-select-specific_products_final',
                            'hint' => $this->l('Select the products to display on email'),
                            'options' => array(
                                'query' => $included_products_arry,
                                'id' => 'id_product',
                                'name' => 'name'
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Related products on Low Stock Email'),
                            'hint' => $this->l('If Enabled, then  Related product is displayed on Low Stock Email.'),
                            'name' => 'product_update[enable_related_product_low_stock]',
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('Low stock Email related product Tittle'),
                            'type' => 'text',
                            'lang' => true,
                            'class' => '',
                            'class' => '',
                            'name' => 'low_stock_related_title',
                            'hint' => $this->l('Enter the Tittle for the related product block in Low stock email.'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select related product method for Low Stock Email'),
                            'required' => true,
                            'hint' => $this->l('Related product will be displayed on the basis of selected method'),
                            'name' => 'product_update[related_product_method_low_stock]',
                            'options' => array(
                                'query' => $display_methods,
                                'id' => 'id',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Choose Specific products for Low Stock Email'),
                            'name' => 'product_update[specific_products_low_stock][]',
                            'required' => true,
                            'multiple' => 'true',
                            'id' => 'multiple-select-specific_products_low_stock',
                            'hint' => $this->l('Select the products to display on email'),
                            'options' => array(
                                'query' => $included_products_arry,
                                'id' => 'id_product',
                                'name' => 'name'
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable UTM Setting'),
                            'hint' => $this->l('Enable UTM.'),
                            'name' => 'product_update[enable_utm]',
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'label' => $this->l('UTM Source'),
                            'required' => true,
                            'type' => 'text',
                            'class' => '',
                            'class' => '',
                            'name' => 'product_update[product_update_utm_source]',
                            'hint' => $this->l('Enter the UTM Source.'),
                        ),
                        array(
                            'label' => $this->l('UTM Medium'),
                            'required' => true,
                            'type' => 'text',
                            'class' => '',
                            'class' => '',
                            'name' => 'product_update[product_update_utm_medium]',
                            'hint' => $this->l('Enter the UTM Medium.'),
                        ),
                        array(
                            'label' => $this->l('UTM Campaign'),
                            'required' => true,
                            'type' => 'text',
                            'class' => '',
                            'class' => '',
                            'name' => 'product_update[product_update_utm_campaign]',
                            'hint' => $this->l('Enter the UTM Campaign.'),
                        ),
                        //changes end
                        array(
                            'label' => $this->l('Heading Background Color'),
                            'type' => 'color',
                            'id' => 'minimum_letters',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[background_heading]',
                            'hint' => $this->l('Choose the heading background color for the Notify box')
                        ),
                        array(
                            'label' => $this->l('Notify Me Button Boder Color'),
                            'type' => 'color',
                            'id' => 'minimum_letters',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[notify_border]',
                            'hint' => $this->l('Choose the border color for the Notify button')
                        ),
                        array(
                            'label' => $this->l('Notify Me Button background Color'),
                            'type' => 'color',
                            'id' => 'minimum_letters',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[notify_background]',
                            'hint' => $this->l('Choose the background color for the Notify button')
                        ),
                        array(
                            'label' => $this->l('Notify Me Button text Color'),
                            'type' => 'color',
                            'id' => 'minimum_letters',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[notify_text]',
                            'hint' => $this->l('Choose the tect color for the Notify button')
                        ),
                        //changes end
                        array(
                            'label' => $this->l('Background Color'),
                            'type' => 'color',
                            'id' => 'minimum_letters',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[background]',
                            'hint' => $this->l('Choose the background color for the Notify box')
                        ),
                        array(
                            'label' => $this->l('Border Color'),
                            'type' => 'color',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[border]',
                            'hint' => $this->l('Choose the border color for the Notify box.')
                        ),
                        array(
                            'label' => $this->l('Text Color'),
                            'type' => 'color',
                            'class' => 'general_tab',
                            'required' => true,
                            'name' => 'product_update[text]',
                            'hint' => $this->l('Choose the text color for the Notify box.')
                        ),
                        array(
                            'label' => $this->l('Custom CSS'),
                            'type' => 'textarea',
                            'id' => 'quantity_min',
                            'class' => 'search_tab',
                            'name' => 'kb_backinstock_css',
                            'hint' => $this->l('Customize the css as per requirement')
                        ),
                        array(
                            'label' => $this->l('Custom JS'),
                            'type' => 'textarea',
                            'id' => 'quantity_min',
                            'class' => 'search_tab',
                            'name' => 'kb_backinstock_js',
                            'hint' => $this->l('Customize the JS as per requirement')
                        ),
                        array(
                            'type' => 'html',
                            'class' => 'col-lg-10 col-md-10',
                            'name' => 'cron_links',
                            'id' => 'cron_links',
                            'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'backinstock/views/templates/admin/crondetails.tpl'),
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right form_general'
                    ),
                ),
            );
        }
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->fields_form1 = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Initial Email Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select Language'),
                            'name' => 'velocity_email_template[language]',
                            'class' => 'initial_lang',
                            'is_bool' => true,
                            'options' => array(
                                'query' => $lang,
                                'id' => 'id_lang',
                                'name' => 'name',
                            ),
                            'hint' => $this->l('Select Language to edit template')
                        ),
                        array(
                            'label' => $this->l('Template Subject'),
                            'type' => 'text',
                            'required' => true,
                            'id' => 'velsof_template_subject',
                            'class' => 'general_tab',
                            'name' => 'velocity_email_template[subject]',
                        ),
                        array(
                            'label' => $this->l(''),
                            'type' => 'text',
                            'id' => 'hidden_template_id',
                            'class' => 'general_tab',
                            'name' => 'velocity_email_template[template_id]',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Template Content:'),
                            'name' => 'velocity_email_template[content]',
                            'id' => 'velsof_template_content',
                            'required' => true,
                            'cols' => '9',
                            'rows' => '5',
                            'class' => 'col-lg-9',
                            'autoload_rte' => true
                        ),
                        array(
                            'label' => 'sss',
                            'type' => 'hidden',
                            'id' => 'velsof_hidden_id',
                            'class' => 'general_tab1',
                            'name' => 'velsof_hidden_id',
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('   Save   '),
                        'class' => 'btn btn-default pull-right form_initial'
                    ),
                ),
            );
        } else {
            $this->fields_form1 = array(
                'form' => array(
                    'id_form' => 'initial_form',
                    'legend' => array(
                        'title' => $this->l('Initial Email Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select Language'),
                            'name' => 'velocity_email_template[language]',
                            'class' => 'initial_lang',
                            'is_bool' => true,
                            'options' => array(
                                'query' => $lang,
                                'id' => 'id_lang',
                                'name' => 'name',
                            ),
                            'hint' => $this->l('Select Language to edit template')
                        ),
                        array(
                            'label' => $this->l('Template Subject'),
                            'type' => 'text',
                            'required' => true,
                            'id' => 'velsof_template_subject',
                            'class' => 'general_tab',
                            'name' => 'velocity_email_template[subject]',
                        ),
                        array(
                            'label' => $this->l(''),
                            'type' => 'text',
                            'id' => 'hidden_template_id',
                            'class' => 'general_tab',
                            'name' => 'velocity_email_template[template_id]',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Template Content'),
                            'name' => 'velocity_email_template[content]',
                            'id' => 'velsof_template_content',
                            'required' => true,
                            'col' => '9',
                            'class' => 'col-lg-9',
                            'autoload_rte' => true
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right form_initial'
                    ),
                ),
            );
        }
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->fields_form2 = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Final Email Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select Language:'),
                            'name' => 'velocity_email_template[language_final]',
                            'class' => 'final_lang',
                            'is_bool' => true,
                            'options' => array(
                                'query' => $lang,
                                'id' => 'id_lang',
                                'name' => 'name',
                            ),
                            'hint' => $this->l('Select Language to edit template')
                        ),
                        array(
                            'label' => $this->l('Template Subject:'),
                            'type' => 'text',
                            'required' => true,
                            'id' => 'velsof_template_subject_final',
                            'class' => 'general_tab',
                            'name' => 'velocity_email_template[subject_final]',
                        ),
                        array(
                            'label' => $this->l(''),
                            'type' => 'text',
                            'id' => 'hidden_template_id_final',
                            'class' => 'general_tab',
                            'name' => 'velocity_email_template[template_id_final]',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Template Content:'),
                            'name' => 'velocity_email_template[content_drop]',
                            'id' => 'velsof_template_content_final',
                            'required' => true,
                            'cols' => '9',
                            'rows' => '5',
                            'class' => 'col-lg-9',
                            'autoload_rte' => true
                        ),
                        array(
                            'label' => 'sss',
                            'type' => 'hidden',
                            'id' => 'velsof_hidden_id_final',
                            'class' => 'general_tab1',
                            'name' => 'velsof_hidden_id_final',
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('   Save   '),
                        'class' => 'btn btn-default pull-right form_final'
                    ),
                ),
            );
        } else {
            $this->fields_form2 = array(
                'form' => array(
                    'id_form' => 'final_form',
                    'legend' => array(
                        'title' => $this->l('Final Email Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select Language'),
                            'name' => 'velocity_email_template[language_final]',
                            'class' => 'final_lang',
                            'is_bool' => true,
                            'options' => array(
                                'query' => $lang,
                                'id' => 'id_lang',
                                'name' => 'name',
                            ),
                            'hint' => $this->l('Select Language to edit template')
                        ),
                        array(
                            'label' => $this->l('Template Subject'),
                            'type' => 'text',
                            'required' => true,
                            'id' => 'velsof_template_subject_final',
                            'class' => 'general_tab',
                            'name' => 'velocity_email_template[subject_final]',
                        ),
                        array(
                            'label' => $this->l(''),
                            'type' => 'text',
                            'id' => 'hidden_template_id_final',
                            'class' => 'general_tab',
                            'name' => 'velocity_email_template[template_id_final]',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Template Content'),
                            'name' => 'velocity_email_template[content_drop]',
                            'id' => 'velsof_template_content_final',
                            'required' => true,
                            'col' => '9',
                            'class' => 'col-lg-9',
                            'autoload_rte' => true
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right form_final'
                    ),
                ),
            );
        }
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->fields_form3 = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Analysis'),
                    ),
                    'input' => array(
                    ),
                ),
            );
        } else {
            $this->fields_form3 = array(
                'form' => array(
                    'id_form' => 'analysis_form',
                    'legend' => array(
                        'title' => $this->l('Analysis'),
                    ),
                    'input' => array(
                    ),
                ),
            );
        }
        /*
         * @author Risahbh Jain
         * DOC - 31/01/20
         * to fetch the low stock alert form and email marketing form
         */
        $this->fields_form4 = $this->getLowStockAlertForm();

        $this->fields_form5 = $this->getEmailMarketingForm();

        //start by dharmanshu for racpatcha module
        /*
        $this->fields_form6 =  array(
        'form' => array(
        'id_form' => 'recaptcha_form',
        'legend' => array(
        'title' => $this->l('V3 Recaptcha Settings'),
        ),
        'input' => array(
        ),
        ),
        );
        */
        $this->fields_form6 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('V3 Recaptcha Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enabled'),
                        'name' => 'KB_BACKINSTOCK_RECAPTCHA_ENABLE',
                        'desc' => $this->l('Toggle to enable or disable v3 Recaptcha'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'switch_value_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'switch_value_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('V3 Site Key'),
                        'desc' => $this->l('Enter V3 Site Key'),
                        'name' => 'KB_BACKINSTOCK_RECAPTCHA_SITE_KEY',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('V3 Secret Key'),
                        'desc' => $this->l('Enter V3 Secret Key'),
                        'name' => 'KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY',
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right validation_google_recaptcha'
                )
            )
        );
        //end by dharmanshu for racpatcha module

        // Changes by prvind for new tab Availability Settings & statistics tab
        $this->fields_form7 = $this->getAvailabilitySettingForm();
        $this->fields_form8 = $this->getStatisticsTabForm();

        // Changes Over

        $form_value = array();

        if (Configuration::get('VELOCITY_BACK_STOCK_EMAIL_MARKETING')) {
            $form_value = Tools::unSerialize(Configuration::get('VELOCITY_BACK_STOCK_EMAIL_MARKETING'));
        }
        //        print_R($this->product_update_settings);
//        die('sd');
        /* changes over */
        $field_value = array(
            'product_update[enable]' => $this->product_update_settings['enable'],
            'product_update[enable_cron]' => $this->product_update_settings['enable_cron'],
            'product_update[enable_quantity]' => $this->product_update_settings['enable_quantity'],
            'product_update[update_subscribers]' => $this->product_update_settings['update_subscribers'],
            'product_update[display_hook]' => $this->product_update_settings['display_hook'],
            'product_update[enable_gdpr_delete]' => $this->product_update_settings['enable_gdpr_delete'],
            'product_update[enable_gdpr_policy]' => $this->product_update_settings['enable_gdpr_policy'],
            'product_update[background]' => $this->product_update_settings['background'],
            'product_update[border]' => $this->product_update_settings['border'],
            'product_update[enable_subscription_list]' => $this->product_update_settings['enable_subscription_list'],
            'product_update[enable_remove_subscription]' => $this->product_update_settings['enable_remove_subscription'],
            'product_update[enable_low_stock_alert]' => $this->product_update_settings['enable_low_stock_alert'],
            //changes by vishal
            'product_update[related_product_method_initial]' => $this->product_update_settings['related_product_method_initial'],
            'product_update[enable_related_product_initial]' => $this->product_update_settings['enable_related_product_initial'],
            'product_update[specific_products_initial][]' => isset($this->product_update_settings['specific_products_initial']) ? $this->product_update_settings['specific_products_initial'] : array(),
            'product_update[related_product_method_final]' => $this->product_update_settings['related_product_method_final'],
            'product_update[enable_related_product_final]' => $this->product_update_settings['enable_related_product_final'],
            'product_update[specific_products_final][]' => isset($this->product_update_settings['specific_products_final']) ? $this->product_update_settings['specific_products_final'] : array(),
            'product_update[related_product_method_low_stock]' => $this->product_update_settings['related_product_method_low_stock'],
            'product_update[enable_related_product_low_stock]' => $this->product_update_settings['enable_related_product_low_stock'],
            'product_update[specific_products_low_stock][]' => isset($this->product_update_settings['specific_products_low_stock']) ? $this->product_update_settings['specific_products_low_stock'] : array(),
            'product_update[product_update_utm_source]' => $this->product_update_settings['product_update_utm_source'],
            'product_update[product_update_utm_medium]' => $this->product_update_settings['product_update_utm_medium'],
            'product_update[product_update_utm_campaign]' => $this->product_update_settings['product_update_utm_campaign'],
            'product_update[enable_utm]' => $this->product_update_settings['enable_utm'],
            'product_update[notify_text]' => $this->product_update_settings['notify_text'],
            'product_update[notify_background]' => $this->product_update_settings['notify_background'],
            'product_update[notify_border]' => $this->product_update_settings['notify_border'],
            'product_update[background_heading]' => $this->product_update_settings['background_heading'],
            //changes end
            'product_update[low_stock_alert_quantity]' => $this->product_update_settings['low_stock_alert_quantity'],
            'product_update[subscription_per_page]' => $this->product_update_settings['subscription_per_page'],
            'product_update[text]' => $this->product_update_settings['text'],
            'kb_backinstock_css' => $this->product_update_css['css'],
            'kb_backinstock_js' => $this->product_update_js['js'],
            'velocity_email_template[language]' => 0,
            'velocity_email_template[subject]' => '',
            'velocity_email_template[template_id]' => '',
            'velocity_email_template[content]' => '',
            'velocity_email_template[language_final]' => 0,
            'velocity_email_template[subject_final]' => '',
            'velocity_email_template[template_id_final]' => '',
            'velocity_email_template[content_drop]' => '',
            'velsof_hidden_id_final' => '',
            'velsof_hidden_id' => '',
            // low stock alert setting
            'velocity_low_stock_alert_setting[language]' => 0,
            'velocity_low_stock_alert_setting[subject]' => '',
            'velocity_low_stock_alert_setting[template_id]' => '',
            'velocity_low_stock_alert_setting[content]' => '',
            'velsof_hidden_id_final' => '',
            // email marketing setting
            'back_stock_email[SendinBlue_list]' => isset($form_value['SendinBlue_list']) ? $form_value['SendinBlue_list'] : '',
            'back_stock_email[SendinBlue_status]' => isset($form_value['SendinBlue_status']) ? $form_value['SendinBlue_status'] : 0,
            'back_stock_email[SendinBlue_api]' => isset($form_value['SendinBlue_api']) ? $form_value['SendinBlue_api'] : '',
            'back_stock_email[mailchimp_list]' => isset($form_value['mailchimp_list']) ? $form_value['mailchimp_list'] : '',
            'back_stock_email[klaviyo_list]' => isset($form_value['klaviyo_list']) ? $form_value['klaviyo_list'] : '',
            'back_stock_email[mailchimp_status]' => isset($form_value['mailchimp_status']) ? $form_value['mailchimp_status'] : 0,
            'back_stock_email[mailchimp_api]' => isset($form_value['mailchimp_api']) ? $form_value['mailchimp_api'] : '',
            'back_stock_email[klaviyo_status]' => isset($form_value['klaviyo_status']) ? $form_value['klaviyo_status'] : '',
            'back_stock_email[klaviyo_api]' => isset($form_value['klaviyo_api']) ? $form_value['klaviyo_api'] : '',
            // recaptcha settings
            'KB_BACKINSTOCK_RECAPTCHA_ENABLE' => Configuration::get('KB_BACKINSTOCK_RECAPTCHA_ENABLE'),
            'KB_BACKINSTOCK_RECAPTCHA_SITE_KEY' => Configuration::get('KB_BACKINSTOCK_RECAPTCHA_SITE_KEY'),
            'KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY' => Configuration::get('KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY')
        );
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            if (!empty($this->product_update_settings['product_update_gdpr_policy_text'][$lang['id_lang']])) {
                $field_value['product_update_gdpr_policy_text'][$lang['id_lang']] = $this->product_update_settings['product_update_gdpr_policy_text'][$lang['id_lang']];
            } else {
                $field_value['product_update_gdpr_policy_text'][$lang['id_lang']] = '';
            }
        }

        //changes by vishal for adding related product functionality
        foreach ($languages as $lang) {
            if (!empty($this->product_update_settings['low_stock_related_title'][$lang['id_lang']])) {
                $field_value['low_stock_related_title'][$lang['id_lang']] = $this->product_update_settings['low_stock_related_title'][$lang['id_lang']];
            } else {
                $field_value['low_stock_related_title'][$lang['id_lang']] = '';
            }
        }
        foreach ($languages as $lang) {
            if (!empty($this->product_update_settings['initial_related_title'][$lang['id_lang']])) {
                $field_value['initial_related_title'][$lang['id_lang']] = $this->product_update_settings['initial_related_title'][$lang['id_lang']];
            } else {
                $field_value['initial_related_title'][$lang['id_lang']] = '';
            }
        }
        foreach ($languages as $lang) {
            if (!empty($this->product_update_settings['final_related_title'][$lang['id_lang']])) {
                $field_value['final_related_title'][$lang['id_lang']] = $this->product_update_settings['final_related_title'][$lang['id_lang']];
            } else {
                $field_value['final_related_title'][$lang['id_lang']] = '';
            }
        }
        //changes end

        foreach ($languages as $lang) {
            if (!empty($this->product_update_settings['product_update_gdpr_policy_url'][$lang['id_lang']])) {
                $field_value['product_update_gdpr_policy_url'][$lang['id_lang']] = $this->product_update_settings['product_update_gdpr_policy_url'][$lang['id_lang']];
            } else {
                $field_value['product_update_gdpr_policy_url'][$lang['id_lang']] = '';
            }
        }
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->context->smarty->assign('show_toolbar', false);
        }
        $action = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules');
        $form = $this->getform($this->fields_form, $languages, $field_value, 'general', $action);
        $form1 = $this->getform($this->fields_form1, $languages, $field_value, 'initial', $action);
        $form2 = $this->getform($this->fields_form2, $languages, $field_value, 'final', $action);
        $form3 = $this->getform($this->fields_form3, $languages, $field_value, 'analysis', $action);
        /*
         * @author Risahbh Jain
         * DOC - 31/01/20
         * to generate the low stock alert form and email marketing form
         */
        $form4 = $this->getform($this->fields_form4, $languages, $field_value, 'low_stock_alert', $action);
        $form5 = $this->getform($this->fields_form5, $languages, $field_value, 'email_marketing', $action);
        //start by dharmanshu for the recaptcha 19-08-21
        $field_value['availability_form[enable_availability_settings]'] = isset($this->product_aval_settings['enable_availability_settings']) ? $this->product_aval_settings['enable_availability_settings'] : 0;
        $field_value['availability_form[product_name]'] = isset($this->product_aval_settings['product_name']) ? $this->product_aval_settings['product_name'] : '';
        $field_value['availability_form[excluded_products_hidden]'] = isset($this->product_aval_settings['excluded_products_hidden']) ? $this->product_aval_settings['excluded_products_hidden'] : array();

        $form6 = $this->getform($this->fields_form6, $languages, $field_value, 'recaptcha', $action);
        $form7 = $this->getform($this->fields_form7, $languages, $field_value, 'availability', $action);
        $form8 = $this->getform($this->fields_form8, $languages, $field_value, 'statistics', $action);
        // chanegs over
        if (!version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin/pageviewer_16.css');
            $version = 1.6;
        } else {
            $this->context->controller->addCSS($this->_path . 'views/css/admin/pageviewer_15.css');
            $version = 1.5;
        }
        $cron_link = $this->context->link->getModuleLink('backinstock', 'cron');
        $dot_found = 0;
        $needle = 'index.php';
        $dot_found = strpos($cron_link, $needle);
        if ($dot_found !== false) {
            $ch = '&';
        } else {
            $ch = '?';
        }

        $module_path = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $this->name;
        $this->context->smarty->assign('path_fold', $module_path . '&ajaxproductaction=true&');
        $this->context->smarty->assign('front_cron_url', $cron_link . $ch);
        $this->context->smarty->assign('product_tabs', $product_tabs);
        $this->context->smarty->assign('form', $form);
        $this->context->smarty->assign('form1', $form1);
        $this->context->smarty->assign('form2', $form2);
        $this->context->smarty->assign('form3', $form3);
        $this->context->smarty->assign('form4', $form4);
        $this->context->smarty->assign('form5', $form5);
        $this->context->smarty->assign('form6', $form6); //by dharmanshu for recaptcha form 21-08-2021
        $this->context->smarty->assign('form7', $form7);
        $this->context->smarty->assign('form8', $form8);
        /*
         * @author - Rishabh Jain
         * DOC - 29th Jan 2020
         * To assign the email marketing tab values and to fetch the selected marketing list
         * on page load
         */
        $email_marketing_values = array();
        $email_marketing_values['mailchimp_api'] = isset($form_value['mailchimp_api']) ? $form_value['mailchimp_api'] : '';
        $email_marketing_values['mailchimp_status'] = isset($form_value['mailchimp_status']) ? $form_value['mailchimp_status'] : '';
        $email_marketing_values['mailchimp_list'] = isset($form_value['mailchimp_list']) ? $form_value['mailchimp_list'] : '';
        $email_marketing_values['klaviyo_status'] = isset($form_value['klaviyo_status']) ? $form_value['klaviyo_status'] : '';
        $email_marketing_values['klaviyo_api'] = isset($form_value['klaviyo_api']) ? $form_value['klaviyo_api'] : '';
        $email_marketing_values['klaviyo_list'] = isset($form_value['klaviyo_list']) ? $form_value['klaviyo_list'] : '';
        $email_marketing_values['SendinBlue_status'] = isset($form_value['SendinBlue_status']) ? $form_value['SendinBlue_status'] : '';
        $email_marketing_values['SendinBlue_api'] = isset($form_value['SendinBlue_api']) ? $form_value['SendinBlue_api'] : '';
        $email_marketing_values['SendinBlue_list'] = isset($form_value['SendinBlue_list']) ? $form_value['SendinBlue_list'] : '';

        $this->context->smarty->assign('email_marketing_values', Tools::jsonEncode($email_marketing_values));
        $module_path = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules') .
            '&configure=' . $this->name;
        $this->context->smarty->assign('module_path', $module_path); //module path
        $path = $this->_path;
        $this->context->smarty->assign(
            'kb_image_path', $this->context->link->getMediaLink(
                __PS_BASE_URI__ . 'modules/backinstock/views/img/'
            )
        ); //module path
        $this->context->smarty->assign('path', $path);
        //        $subscriber_list = $this->getSubscriberList();
//        $this->context->smarty->assign('subscriber_list', $subscriber_list);
        /* changes over */

        $check_sql = 'select * from ' . _DB_PREFIX_ . 'product_update_product_stats where id = 1';
        $stats = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($check_sql);

        $this->context->smarty->assign('firstCall', false);
        $this->context->smarty->assign('general_settings', $this->l('General Settings'));
        $this->context->smarty->assign('mod_dir', _MODULE_DIR_);
        $this->context->smarty->assign('version', $version);
        $this->context->smarty->assign('action_product_update', $action . '&configure=backinstock');
        $helper = new HelperView();
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->current = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->override_folder = 'helpers/';
        $helper->base_folder = 'view/';
        $helper->base_tpl = 'page_custom.tpl';
        $this->context->smarty->assign('present', $product_list);
        $this->context->smarty->assign('stats', $stats);
        $view = $helper->generateView();
        $this->context->smarty->assign('view', $view);
        $tpl = 'Form_custom.tpl';
        $helper = new Helper();
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->override_folder = 'helpers/';
        $helper->base_folder = 'form/';
        $helper->setTpl($tpl);
        $tpl = $helper->generate();

        $output = $output . $tpl;
        return $output;
    }

    /*
     * @author Risahbh Jain
     * DOC - 31/01/20
     * to fetch the low stock alert form fields
     */
    public function getLowStockAlertForm()
    {
        $languages = Language::getLanguages(false);
        $lang = array(
            0 => array(
                'id_lang' => 0,
                'name' => $this->l('Please Select Language')
            )
        );
        $lang = array_merge($lang, $languages);

        return array(
            'form' => array(
                'id_form' => 'low_stock_alert_form',
                'legend' => array(
                    'title' => $this->l('Low Stock Alert Email Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Language'),
                        'name' => 'velocity_low_stock_alert_setting[language]',
                        'class' => 'low_stock_lang',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $lang,
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                        'hint' => $this->l('Select Language to edit template')
                    ),
                    array(
                        'label' => $this->l('Template Subject'),
                        'type' => 'text',
                        'required' => true,
                        'id' => 'velocity_low_stock_alert_setting_subject',
                        'class' => 'general_tab',
                        'name' => 'velocity_low_stock_alert_setting[subject]',
                    ),
                    array(
                        'label' => $this->l(''),
                        'type' => 'text',
                        'id' => 'hidden_velocity_low_stock_alert_setting_template_id',
                        'class' => 'general_tab',
                        'name' => 'velocity_low_stock_alert_setting[template_id]',
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Template Content'),
                        'name' => 'velocity_low_stock_alert_setting[content]',
                        'id' => 'velocity_low_stock_alert_setting_template_content',
                        'required' => true,
                        'col' => '9',
                        'class' => 'col-lg-9',
                        'autoload_rte' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right form_low_stock'
                ),
            ),
        );
    }

    /*
     * @author Risahbh Jain
     * DOC - 31/01/20
     * function added for generating subscriber list but removed as it was difficult to add filters in same
     */
    public function getSubscriberList()
    {
        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('Id'),
                'type' => 'text',
                'search' => true,
                'orderby' => false
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'type' => 'text',
                'search' => true,
                'orderby' => false
            ),
            'customer_id' => array(
                'title' => $this->l('Customer Id'),
                'type' => 'text',
                'search' => true,
                'orderby' => false
            ),
            'product_id' => array(
                'title' => $this->l('Product Id'),
                'type' => 'text',
                'search' => true,
                'orderby' => false
            ),
            'product_name' => array(
                'title' => $this->l('Product Name'),
                'type' => 'text',
                'search' => true,
                'orderby' => false
            ),
            'product_attribute_id' => array(
                'title' => $this->l('Combination Id'),
                'type' => 'text',
                'search' => true,
                'orderby' => false
            ),
            'send' => array(
                'title' => $this->l('Mail Sent'),
                'search' => true,
                'type' => 'select',
                //                'list' => array(
//                    0 => $this->l('Pending'),
//                    1 => $this->l('Sent'),
//                ),

                'callback' => 'showMailSentStatus',
                'orderby' => false
            ),
            'date_added' => array(
                'title' => $this->l('Date Added'),
                'type' => 'datetime',
                'search' => true,
                'orderby' => false
            ),
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->table = 'product_update_product_detail';
        $helper->identifier = 'id';
        $helper->show_toolbar = true;
        $helper->title = $this->l('Subscriber List');
        $start = Tools::getValue('submitFilter' . $helper->table, 0);
        if (version_compare(_PS_VERSION_, '1.6.1.2', '<')) {
            $default_pagination = 50;
        } else {
            $default_pagination = $helper->_default_pagination;
        }
        $limit = $default_pagination;
        if ((int) Tools::getValue($helper->table . '_pagination') > 0) {
            $limit = (int) Tools::getValue($helper->table . '_pagination');
        }
        if ($start > 0) {
            $start = ($start - 1) * $limit;
        }
        if (Tools::getValue('submitFilter' . $helper->table) == 1) {
            $start = 0;
        }
        $data = array();
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        if (Tools::isSubmit('submitResetproduct') || Tools::isSubmit('submitFilterproduct')) {
            $this->context->smarty->assign('default_tab', 'product');
            if (Tools::isSubmit('submitResetproduct')) {
                $this->context->smarty->assign('reset', 'product');
            }
        }
        if (Tools::getValue('submitFilter') == 'Product') {
            $lang = $this->context->language->id;
            $data = $this->getMappedProducts($start, $limit, true, $lang);
            $this->context->smarty->assign('default_tab', 'product');
            $pro_data = $data['data'];
            $count = $data['count'];
        } else {
            $subscribers = $this->getSubscribers($start, $limit, false);
        }

        $helper->listTotal = count($subscribers);
        return $helper->generateList($subscribers, $this->fields_list);
    }

    /*
     * @author Prvind Panday
     * DOC - 19/06/22
     * function added for getting product list
     */

    public function ajaxproductlist()
    {
        $query = Tools::getValue('q', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', false);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', false);

        $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = '
            . 'p.id_product AND pl.id_lang = '
            . '' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE p.active = 1 and (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . pSQL($excludeIds) . ') ' : ' ') .
            (pSQL($excludeVirtuals) ? 'AND p.id_product NOT IN (SELECT pd.id_product FROM '
                . '`' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            (pSQL($exclude_packs) ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '');

        $items = Db::getInstance()->executeS($sql);
        if ($items) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['reference']) ?
                    ' (ref: ' . $item['reference'] . ')' : '') .
                    '|' . (int) ($item['id_product']) . "\n";
            }
        }
    }

    public function getAvailabilitySettingForm()
    {
        $settings = Configuration::get('VELOCITY_AVAILABILITY_SETTINGS');
        $this->product_aval_settings = Tools::unSerialize($settings);
        $categoryTreeSelection = array();
        if (!empty($this->product_aval_settings['prestashop_category'])) {
            $categoryTreeSelection = $this->product_aval_settings['prestashop_category'];
        }

        $root = Category::getRootCategory();
        //Generating the tree for the first column
        $tree = new HelperTreeCategories('prestashop_category'); //The string in param is the ID used by the generated tree
        $tree->setUseCheckBox(true)
            ->setAttribute('is_category_filter', $root->id)
            ->setRootCategory($root->id)
            ->setSelectedCategories($categoryTreeSelection)
            ->setInputName('prestashop_category')
            ->setUseSearch(true)
            //            ->setDisabledCategories($categoryListDisabled)
            ->setFullTree(true); //Set the name of input. The option "name" of $fields_form doesn't seem to work with "categories_select" type

        $categoryTreePresta = $tree->render();

        $selectedproducts = array();
        if (isset($this->product_aval_settings['excluded_products_hidden']) && (!Tools::isEmpty($this->product_aval_settings['excluded_products_hidden']))) {
            $selectedProductIds = explode(',', $this->product_aval_settings['excluded_products_hidden']);
            foreach ($selectedProductIds as $productId) {
                $productDetails = new Product($productId);
                $selectedproducts[] = array(
                    'product_id' => $productId,
                    'title' => $productDetails->name[$this->context->language->id],
                    'reference' => $productDetails->reference
                );
            }
        }
        $this->context->smarty->assign('selectedproducts', $selectedproducts);

        return array(
            'form' => array(
                'id_form' => 'availability_form',
                'class' => 'col-lg-10 col-md-9',
                'legend' => array(
                    'title' => $this->l('Availability Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enabled'),
                        'name' => 'availability_form[enable_availability_settings]',
                        'desc' => $this->l('Toggle to enable or disable availability settings'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'availability_form[enable_availability_settings]_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'availability_form[enable_availability_settings]_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        ),
                    ),
                    array(
                        'type' => 'categories_select',
                        'label' => $this->l('Categories Allowed'),
                        'category_tree' => $categoryTreePresta,
                        'name' => 'prestashop_category',
                        'hint' => array(
                            $this->l('Categories to be allowed to seller in which he/she can map his/her products.'),
                            $this->l('If no category is selected that will mean that all the categories are allowed.')
                        ),
                        'desc' => $this->l('If no category is selected that will mean that all the categories are allowed. In order to enable a category you will have to check all the parent categories otherwise the category will not be activated. Example- To enable `T-shirts` category, you will have to check all the parent categories i.e. Home, Women, Tops and ofcourse T-shirts.')
                    ),
                    array(
                        'label' => $this->l('Choose products'),
                        'type' => 'text',
                        'hint' => $this->l('Start typing the products name to choose.Select Product From List on Which You Want To Exclude Back In Stock.'),
                        'desc' => $this->l('Start typing the products name to choose.Select Product From List on Which You Want To Exclude Back In Stock.'),
                        'class' => 'ac_input',
                        'name' => 'availability_form[product_name]',
                        'autocomplete' => false,
                    ),
                    array(
                        'type' => 'html',
                        'name' => '',
                        'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'backinstock/views/templates/admin/showSelectedProducts.tpl'),
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'availability_form[excluded_products_hidden]',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right form_low_stock'
                ),
            ),
        );
    }

    public function getStatisticsTabForm()
    {
        return array(
            'form' => array(
                'id_form' => 'statistics_form',
                'class' => 'col-lg-10 col-md-9',
                'legend' => array(
                    'title' => $this->l('Statistics'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                ),
            ),
        );
    }

    /*
     * @author Risahbh Jain
     * DOC - 31/01/20
     * Function to get email marketing form fields
     */
    public function getEmailMarketingForm()
    {
        $chimp_options = array();
        $chimp_options[] = array(
            'id_hide' => '',
            'name' => ''
        );
        $klav_options = array();
        $klav_options[] = array(
            'id_hide' => '',
            'name' => ''
        );
        $sendin_options = array();

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Email Marketing Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable MailChimp'),
                        'hint' => $this->l('Enable MailChimp Settings'),
                        'name' => 'back_stock_email[mailchimp_status]',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('MailChimp API key'),
                        'name' => 'back_stock_email[mailchimp_api]',
                        'hint' => $this->l('Enter MailChimp API key'),
                        'required' => true,
                    ),
                    array(
                        'label' => $this->l('MailChimp List'),
                        'type' => 'select',
                        'name' => 'back_stock_email[mailchimp_list]',
                        'hint' => $this->l('List of subscribed users.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $chimp_options,
                            'id' => 'id_hide',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Klaviyo'),
                        'hint' => $this->l('Enable Klaviyo  Settings'),
                        'name' => 'back_stock_email[klaviyo_status]',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Klaviyo API key'),
                        'name' => 'back_stock_email[klaviyo_api]',
                        'hint' => $this->l('Enter Klaviyo API key'),
                        'required' => true,
                    ),
                    array(
                        'label' => $this->l('Klaviyo List'),
                        'type' => 'select',
                        'name' => 'back_stock_email[klaviyo_list]',
                        'hint' => $this->l('List of subscribed users.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $klav_options,
                            'id' => 'id_hide',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable SendinBlue'),
                        'hint' => $this->l('Enable SendinBlue Settings'),
                        'name' => 'back_stock_email[SendinBlue_status]',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('SendinBlue API key'),
                        'name' => 'back_stock_email[SendinBlue_api]',
                        'hint' => $this->l('Enter SendinBlue API key'),
                        'required' => true,
                    ),
                    array(
                        'label' => $this->l('SendinBlue List'),
                        'type' => 'select',
                        'name' => 'back_stock_email[SendinBlue_list]',
                        'hint' => $this->l('List of subscribed users.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $sendin_options,
                            'id' => 'id_hide',
                            'name' => 'name',
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right form_email_marketing'
                ),
            ),
        );
    }

    public static function getSubscribers($start, $limit, $filter = false, $lang = null)
    {
        if ($filter) {
            $product_name = Tools::getValue('productFilter_name');
            $product_status = Tools::getValue('productFilter_status');
            $where_string = '';
            if (trim($product_name) != '') {
                $where_string .= "pl.name LIKE '%" . pSQL(trim($product_name)) . "%' AND pl.id_lang = " . (int) $lang;
                $query = "SELECT COUNT(*) as count FROM " . _DB_PREFIX_ . "product_lang WHERE name LIKE '%" . pSQL(trim($product_name)) . "%' AND id_lang = " . (int) $lang;
                $count = Db::getInstance()->getValue($query);
                $query = "SELECT * FROM " . _DB_PREFIX_ . "product_lang pl INNER JOIN " . _DB_PREFIX_ . "products_shipping_timer pst ON pl.id_product=pst.product_id"
                    . " WHERE " . $where_string
                    . " LIMIT " . pSQL($start) . ',' . pSQL($limit);
                $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                if (empty($data)) {
                    $query = "SELECT * FROM " . _DB_PREFIX_ . "product_lang WHERE name LIKE '%" . pSQL(trim($product_name)) . "%' AND id_lang = " . (int) $lang;
                    $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                    foreach ($data as &$product) {
                        $product['status'] = 0;
                    }
                } else {
                    foreach ($data as &$product) {
                        $product['status'] = 1;
                    }
                }
            }
            if (trim($product_name) == '' && trim($product_status) == '') {
                $product = new Product();
                $data = $product->getProducts($lang, $start, $limit, 'id_product', 'ASC');
                $query = "SELECT * FROM " . _DB_PREFIX_ . "products_shipping_timer";
                $res = Db::getInstance()->executeS($query);
                $product_id = array();
                foreach ($res as $product) {
                    $product_id[] = $product['product_id'];
                }
                foreach ($data as &$product) {
                    if (in_array($product['id_product'], $product_id)) {
                        $product['status'] = 1;
                    } else {
                        $product['status'] = 0;
                    }
                }
                $query = "SELECT COUNT(*) as count FROM " . _DB_PREFIX_ . "product";
                $count = Db::getInstance()->getValue($query);
            }
            if (trim($product_status) != '') {
                if ($product_status == 1) {
                    $query = "SELECT COUNT(*) FROM " . _DB_PREFIX_ . "product_lang pl INNER JOIN " . _DB_PREFIX_ . "products_shipping_timer pst ON pl.id_product=pst.product_id AND pl.id_lang='" . (int) $lang . "'";
                    $count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                    $query = "SELECT * FROM " . _DB_PREFIX_ . "product_lang pl INNER JOIN " . _DB_PREFIX_ . "products_shipping_timer pst ON pl.id_product=pst.product_id AND pl.id_lang='" . (int) $lang . "'"
                        . " LIMIT " . pSQL($start) . ',' . pSQL($limit);
                    $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                    foreach ($data as &$product) {
                        $product['status'] = 1;
                    }
                } elseif ($product_status == 0) {
                    //                    $query = "SELECT * FROM " . _DB_PREFIX_ . "product_lang pl OUTER JOIN " . _DB_PREFIX_ . "products_shipping_timer pst ON pl.id_product=pst.product_id AND pl.id_lang='$lang'"
//                        . " LIMIT " . pSQL($start) . ',' . pSQL($limit);
                    $query = "SELECT COUNT(*) FROM " . _DB_PREFIX_ . "product_lang WHERE id_lang = '" . (int) $lang . "' AND id_product NOT IN (SELECT DISTINCT(product_id) FROM " . _DB_PREFIX_ . "products_shipping_timer)";
                    $count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                    $query = "SELECT * FROM " . _DB_PREFIX_ . "product_lang WHERE id_lang = '" . (int) $lang . "' AND id_product NOT IN (SELECT DISTINCT(product_id) FROM " . _DB_PREFIX_ . "products_shipping_timer)"
                        . " LIMIT " . pSQL($start) . ',' . pSQL($limit);
                    $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                    foreach ($data as &$product) {
                        $product['status'] = 0;
                    }
                }
            }
            if (trim($product_name) != '' && trim($product_status) != '') {
                if ($product_status == 1) {
                    $query = "SELECT COUNT(*) FROM " . _DB_PREFIX_ . "product_lang pl INNER JOIN " . _DB_PREFIX_ . "products_shipping_timer pst ON pl.id_product=pst.product_id AND pl.id_lang='" . (int) $lang . "' AND pl.name LIKE '%" . pSQL(trim($product_name)) . "%'";
                    $count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                    $query = "SELECT * FROM " . _DB_PREFIX_ . "product_lang pl INNER JOIN " . _DB_PREFIX_ . "products_shipping_timer pst ON pl.id_product=pst.product_id AND pl.id_lang='" . (int) $lang . "' AND pl.name LIKE '%" . pSQL(trim($product_name)) . "%'"
                        . " LIMIT " . pSQL($start) . ',' . pSQL($limit);
                    $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                    foreach ($data as &$product) {
                        $product['status'] = 1;
                    }
                } elseif ($product_status == 0) {
                    //                    $query = "SELECT * FROM " . _DB_PREFIX_ . "product_lang pl OUTER JOIN " . _DB_PREFIX_ . "products_shipping_timer pst ON pl.id_product=pst.product_id AND pl.id_lang='$lang'"
//                        . " LIMIT " . pSQL($start) . ',' . pSQL($limit);
                    $query = "SELECT COUNT(*) FROM " . _DB_PREFIX_ . "product_lang WHERE id_lang = '" . (int) $lang . "' AND name LIKE '%" . pSQL(trim($product_name)) . "%' AND id_product NOT IN (SELECT DISTINCT(product_id) FROM " . _DB_PREFIX_ . "products_shipping_timer)";
                    $count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                    $query = "SELECT * FROM " . _DB_PREFIX_ . "product_lang WHERE id_lang = '" . (int) $lang . "' AND name LIKE '%" . pSQL(trim($product_name)) . "%' AND id_product NOT IN (SELECT DISTINCT(product_id) FROM " . _DB_PREFIX_ . "products_shipping_timer)"
                        . " LIMIT " . pSQL($start) . ',' . pSQL($limit);
                    $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                    foreach ($data as &$product) {
                        $product['status'] = 0;
                    }
                }
            }
        } else {
            $query = "SELECT * FROM " . _DB_PREFIX_ . "product_update_product_detail";
            $result = Db::getInstance()->executeS($query);
        }
        return $result;
    }

    protected function addBackOfficeMedia()
    {
        //CSS files
        $this->context->controller->addCSS($this->_path . 'views/css/admin/productupdate_admin.css');

        $this->context->controller->addJs($this->_path . 'views/js/productupdate.js');
        $this->context->controller->addJs($this->_path . 'views/js/velovalidation.js');
        //$this->context->controller->addJs($this->_path.'views/js/bootstrap.min.js');
        $this->context->controller->addJs($this->_path . 'views/js/flot/jquery.flot.min.js');
        //Charts
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            if (version_compare(_PS_VERSION_, '1.5.1.0', '<')) {
            } else {
                $this->context->controller->addCSS($this->_path . 'views/css/admin/productupdate_15.css');
            }
        } else {
            $this->context->controller->addJqueryPlugin('flot');
        }
        $this->context->controller->addJs($this->_path . 'views/js/flot/jquery.flot.axislabels.js');
    }

    public function productCategory($category_id)
    {
        $product_query = 'select distinct cat.id_product,pro.name from ' . _DB_PREFIX_ . 'category_product
cat,' . _DB_PREFIX_ . 'product_lang pro where cat.id_category=' . (int) $category_id
            . ' and cat.id_product=pro.id_product';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($product_query);
    }

    public function isProductHavingPriceAlert($product_id)
    {
        $product_query = 'select product_id from ' . _DB_PREFIX_ .
            'product_update_product_detail where Active=1 AND product_id=' . (int) $product_id;
        if (count(Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($product_query)) > 0) {
            return true;
        } else {
            return false;
        }
    }


    /*
     * Function for ajax process
     *
     *  @param    string method  Name of method which is to be called for ajax process
     */

    private function ajaxProcess($method)
    {
        $this->json = array();

        if ($method == 'mailchimpgetlist') {
            $api_key = Tools::getValue('api_key');
            $list = $this->mailChimpGetLists($api_key);
            $this->json = $list;
        }

        if ($method == 'getSendinblueList') {
            $api_key = Tools::getValue('api_key');
            $list = $this->getSendinblueList($api_key);
            $this->json = $list;
        }

        if ($method == 'klaviyogetlist') {
            $api_key = Tools::getValue('api_key');


            $list = $this->klaviyoGetLists($api_key);
            $this->json = $list;
        }


        header('Content-Type: application/json', true);
        echo Tools::jsonEncode($this->json);
        die;
    }

    public function ajaxHandler($params)
    {
        if (isset($params['template_action'])) {
            $id_shop = $this->context->shop->id;
            $json = array();
            if (isset($params['fetch_template'])) {
                $template_id = $params['selected_temp'];
                if ($template_id == 1) {
                    $lang_id = $params['selected_lang'];
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_update_email_templates where id_lang = '
                        . (int) $lang_id . ' and shop_id = '
                        . (int) $id_shop . ' and template_no="' . (int) $params['selected_temp'] . '"';
                    $json = Db::getInstance()->getRow($sql);
                    if (count($json) < 2) {
                        return Tools::jsonEncode($this->getDefaultMailTemplate($template_id));
                    } else {
                        $json['subject'] = html_entity_decode($json['subject']);
                        $json['body'] = html_entity_decode($json['body']);
                        return Tools::jsonEncode($json);
                    }
                } elseif ($template_id == 2) {
                    $lang_id = $params['selected_lang'];
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_update_email_templates where id_lang = '
                        . (int) $lang_id . ' and shop_id = '
                        . (int) $id_shop . ' and template_no="' . (int) $params['selected_temp'] . '"';
                    $json = Db::getInstance()->getRow($sql);
                    if (count($json) < 2) {
                        return Tools::jsonEncode($this->getDefaultMailTemplate($template_id));
                    } else {
                        $json['subject'] = html_entity_decode($json['subject']);
                        $json['body'] = html_entity_decode($json['body']);
                        return Tools::jsonEncode($json);
                    }
                } elseif ($template_id == 3) {
                    $lang_id = $params['selected_lang'];
                    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_update_email_templates where id_lang = '
                        . (int) $lang_id . ' and shop_id = '
                        . (int) $id_shop . ' and template_no="' . (int) $params['selected_temp'] . '"';
                    $json = Db::getInstance()->getRow($sql);
                    if (count($json) < 2) {
                        return Tools::jsonEncode($this->getDefaultMailTemplate($template_id));
                    } else {
                        $json['subject'] = html_entity_decode($json['subject']);
                        $json['body'] = html_entity_decode($json['body']);
                        return Tools::jsonEncode($json);
                    }
                }
            }
            if (isset($params['save_template'])) {
                if ($params['selected_temp'] == 1) {
                    if (isset($json['error'])) {
                        return Tools::jsonEncode($json);
                    }
                    if ($params['template_id'] != 0) {
                        $sql = 'UPDATE `' . _DB_PREFIX_ . 'product_update_email_templates` SET
					`subject`="' . pSQL(Tools::htmlentitiesUTF8($params['subject'])) . '",
					`body`="' . pSQL(Tools::htmlentitiesUTF8($params['content'])) . '" where
					`id_template`=' . (int) $params['template_id'];
                    } else {
                        $sql = 'INSERT into `' . _DB_PREFIX_ . 'product_update_email_templates` SET
					`template_no`="' . (int) $params['selected_temp'] . '",
					`id_lang`=' . (int) $params['id_lang'] . ', `shop_id`=' . (int) $id_shop . ',
					`iso_code`="' . pSQL(Language::getIsoById($params['id_lang'])) . '",
					`subject`="' . pSQL(Tools::htmlentitiesUTF8($params['subject'])) . '",
					`body`="' . pSQL(Tools::htmlentitiesUTF8($params['content'])) . '",
					date_add=now(), date_upd=now()';
                    }
                } elseif ($params['selected_temp'] == 2) {
                    if (isset($json['error'])) {
                        return Tools::jsonEncode($json);
                    }
                    if ($params['template_id'] != 0) {
                        $sql = 'UPDATE `' . _DB_PREFIX_ . 'product_update_email_templates` SET
					`subject`="' . pSQL(Tools::htmlentitiesUTF8($params['subject'])) . '",
					`body`="' . pSQL(Tools::htmlentitiesUTF8($params['content'])) . '" where
					`id_template`=' . (int) $params['template_id'];
                    } else {
                        $sql = 'INSERT into `' . _DB_PREFIX_ . 'product_update_email_templates` SET
					`template_no`="' . (int) $params['selected_temp'] . '",
					`id_lang`=' . (int) $params['id_lang'] . ', `shop_id`=' . (int) $id_shop . ',
					`iso_code`="' . pSQL(Language::getIsoById($params['id_lang'])) . '",
					`subject`="' . pSQL(Tools::htmlentitiesUTF8($params['subject'])) . '",
					`body`="' . pSQL(Tools::htmlentitiesUTF8($params['content'])) . '",
					date_add=now(), date_upd=now()';
                    }
                } elseif ($params['selected_temp'] == 3) {
                    if (isset($json['error'])) {
                        return Tools::jsonEncode($json);
                    }
                    if ($params['template_id'] != 0) {
                        $sql = 'UPDATE `' . _DB_PREFIX_ . 'product_update_email_templates` SET
					`subject`="' . pSQL(Tools::htmlentitiesUTF8($params['subject'])) . '",
					`body`="' . pSQL(Tools::htmlentitiesUTF8($params['content'])) . '" where
					`id_template`=' . (int) $params['template_id'];
                    } else {
                        $sql = 'INSERT into `' . _DB_PREFIX_ . 'product_update_email_templates` SET
					`template_no`="' . (int) $params['selected_temp'] . '",
					`id_lang`=' . (int) $params['id_lang'] . ', `shop_id`=' . (int) $id_shop . ',
					`iso_code`="' . pSQL(Language::getIsoById($params['id_lang'])) . '",
					`subject`="' . pSQL(Tools::htmlentitiesUTF8($params['subject'])) . '",
					`body`="' . pSQL(Tools::htmlentitiesUTF8($params['content'])) . '",
					date_add=now(), date_upd=now()';
                    }
                }
                if (Db::getInstance()->execute($sql)) {
                    if ($params['selected_temp'] == 1) {
                        $html_content = $params['content'];
                        $text_content = $params['text_content'];
                        $iso_code = Language::getIsoById($params['id_lang']);
                        if ($this->generateTemplateFiles($html_content, $text_content, $iso_code, $params['selected_temp'])) {
                            $json['success'] = true;
                            $json['msg'] = $this->l('Email template updated successfully.');
                        } else {
                            $json['success'] = false;
                            $json['error'] = $this->l(
                                'Permission Error on this Module\'s Mail Directory. '
                                . 'Please save the template after altering the permissions.'
                            );
                        }
                    } elseif ($params['selected_temp'] == 2) {
                        $html_content = $params['content'];
                        $text_content = $params['text_content'];
                        $iso_code = Language::getIsoById($params['id_lang']);
                        if ($this->generateTemplateFiles($html_content, $text_content, $iso_code, $params['selected_temp'])) {
                            $json['success'] = true;
                            $json['msg'] = $this->l('Email template updated successfully.');
                        } else {
                            $json['success'] = false;
                            $json['error'] = $this->l(
                                'Permission Error on this Module\'s Mail Directory. '
                                . 'Please save the template after altering the permissions.'
                            );
                        }
                    } elseif ($params['selected_temp'] == 3) {
                        $html_content = $params['content'];
                        $text_content = $params['text_content'];
                        $iso_code = Language::getIsoById($params['id_lang']);
                        if ($this->generateTemplateFiles($html_content, $text_content, $iso_code, $params['selected_temp'])) {
                            $json['success'] = true;
                            $json['msg'] = $this->l('Email template updated successfully.');
                        } else {
                            $json['success'] = false;
                            $json['error'] = $this->l(
                                'Permission Error on this Module\'s Mail Directory. '
                                . 'Please save the template after altering the permissions.'
                            );
                        }
                    }
                } else {
                    $json['success'] = false;
                    $json['error'] = $this->l('Unable to update email template.');
                }
                return Tools::jsonEncode($json);
            }
        }
        return Tools::jsonEncode(
            array(
                2
            )
        );
    }

    public function hookDisplayLeftColumnProduct()
    {
        /**
         * checked whether the module is active or not and if active displayhook method is set to 1 or not. 1 means display block at default location
         * @date 28-03-2023
         * @commenter Prvind Panday
         */
        $module_data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        if (Tools::getValue('controller') == 'product' && isset($module_data['enable']) && $module_data['enable'] == 1 && $module_data['display_hook'] == 1) {

            $customer = array();
            if ($this->context->cookie->logged) {
                $customer['id'] = $this->context->cookie->id_customer;
                $customer['email'] = $this->context->cookie->email;
            } else {
                $customer['id'] = 0;
                $customer['email'] = '';
            }

            $currency_code = $this->context->currency->iso_code;
            $currency_id = $this->context->currency->id;

            $shop_id = $this->context->shop->id;

            if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
                $this->context->controller->addCSS($this->_path . 'views/css/hook/productupdate_15.css');
            }
            $this->mobile_detect = new Mobile_Detect();
            $mobile_class = 'desktop';
            if (isset($_SERVER['HTTP_REFERER'])) {
                $referer_url = $_SERVER['HTTP_REFERER'];
            } else {
                $referer_url = '';
            }

            $demo_frame_type = 'desktop';
            if ((strpos($referer_url, 'ipad') !== false) || (strpos($referer_url, 'iphone') !== false)) {
                $demo_frame_type = 'mobile-tablet';
            }
            if ($this->mobile_detect->isMobile() || $demo_frame_type == 'mobile-tablet') {
                $mobile_class = 'mobile';
            }
            if ($this->mobile_detect->isTablet() || $demo_frame_type == 'mobile-tablet') {
                $mobile_class = 'tablet';
            }
            $id_product = Tools::getValue('id_product');

            $image_query = 'select id_image from ' . _DB_PREFIX_
                . 'image where id_product=' . (int) $id_product . ' and cover=1';

            $image = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($image_query);
            $image_id = $image['id_image'];

            $image_link_query = 'select link_rewrite from ' . _DB_PREFIX_
                . 'product_lang where id_product=' . (int) $id_product;

            $link_rewrite = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($image_link_query);
            $img_link_rewrite = $link_rewrite['link_rewrite'];

            $desc_query = 'select description_short,name from ' . _DB_PREFIX_
                . 'product_lang where id_product=' . (int) $id_product;

            $short = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($desc_query);
            $short_desc = $short['description_short'];
            $product_name = $short['name'];

            $product = new product((int) $id_product);
            $actual_price = $product->getPrice(true, null, 6);
            $product_price = Tools::displayPrice($actual_price);

            $module_data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
            $style = urldecode(Configuration::get('KB_BACKINSTOCK_CSS'));
            $script = urldecode(Configuration::get('KB_BACKINSTOCK_JS'));
            $customcss = $style;
            $customjs = $script;
            $short_desc = strip_tags($short_desc);
            if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
                $version = 5;
            } else {
                $version = 6;
            }
            $gdpr_policy_text = '';
            $gdpr_policy_url = '';
            if (isset($module_data['product_update_gdpr_policy_text']) && isset($module_data['product_update_gdpr_policy_text'][$this->context->language->id])) {
                $gdpr_policy_text = trim($module_data['product_update_gdpr_policy_text'][$this->context->language->id]);
            }
            if (isset($module_data['product_update_gdpr_policy_url']) && isset($module_data['product_update_gdpr_policy_url'][$this->context->language->id])) {
                $gdpr_policy_url = trim($module_data['product_update_gdpr_policy_url'][$this->context->language->id]);
            }
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
            $this->smarty->assign(
                array(
                    'module_dir' => $module_dir,
                    'background' => $module_data['background'],
                    //changes by vishal
                    'background_heading' => $module_data['background_heading'],
                    'notify_text' => $module_data['notify_text'],
                    'notify_background' => $module_data['notify_background'],
                    'notify_border' => $module_data['notify_border'],
                    //changes end
                    'border' => $module_data['border'],
                    'text' => $module_data['text'],
                    'image_id' => $image_id,
                    'img_link_rewrite' => $img_link_rewrite,
                    'short_desc' => $short_desc,
                    'product_name' => $product_name,
                    'action_product_front_back' => $this->context->link->getModuleLink('backinstock', 'success', array('render' => 'add'), (bool) Configuration::get('PS_SSL_ENABLED')),
                    'product_price' => $product_price,
                    'product_id' => $id_product,
                    'actual_price' => $actual_price,
                    'customer' => $customer,
                    'shop_id' => $shop_id,
                    'currency_code' => $currency_code,
                    'currency_id' => $currency_id,
                    'customcss' => $customcss,
                    'customjs' => $customjs,
                    'version' => $version,
                    'device' => $mobile_class,
                    'enable_gdpr_policy' => $module_data['enable_gdpr_policy'],
                    'gdpr_policy_text' => $gdpr_policy_text,
                    'gdpr_policy_url' => $gdpr_policy_url,
                )
            );
            /* Code added by rishabh Jain on 16th July to hide email alert box if the out of stock order is allowed */
            $product_id = Tools::getValue('id_product');
            $query = 'Select out_of_stock from ' . _DB_PREFIX_ . 'stock_available where id_product = ' . (int) $product_id;
            $stock = Db::getInstance()->getValue($query);
            $show_box = 1;
            if ((int) $stock == 1) {
                $show_box = 0;
            } else if ((int) $stock == 2) {
                $out_of_stock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
                if ($out_of_stock == 1) {
                    $show_box = 0;
                }
            }
            $settings = Configuration::get('VELOCITY_AVAILABILITY_SETTINGS');
            $this->product_aval_settings = Tools::unSerialize($settings);

            if (isset($this->product_aval_settings['enable_availability_settings']) && $this->product_aval_settings['enable_availability_settings'] == 1) {
                if (isset($this->product_aval_settings['excluded_products_hidden']) && (!Tools::isEmpty($this->product_aval_settings['excluded_products_hidden']))) {
                    $selectedProductIds = explode(',', $this->product_aval_settings['excluded_products_hidden']);
                    foreach ($selectedProductIds as $productId) {
                        $selectedproducts[] = $productId;
                    }
                }

                if (!empty($this->product_aval_settings['prestashop_category'])) {
                    $categoryTreeSelection = $this->product_aval_settings['prestashop_category'];
                }

                if (!empty($selectedproducts)) {
                    if (in_array($id_product, $selectedproducts)) {
                        $show_box = 0;
                    }
                }

                if (!empty($categoryTreeSelection)) {
                    if (!in_array($product->id_category_default, $categoryTreeSelection)) {
                        $show_box = 0;
                    }
                }
            }
            $this->context->controller->addJS($this->_path . 'views/js/front/backinstock.js');
            if (isset($customer['id']) && $customer['id'] > 0) {
                $this->context->smarty->assign('disabled', 1);
            } else {
                $this->context->smarty->assign('disabled', 0);
            }
            /* Changes over */
            //changes by gopi fr integratio of v3 start
            $this->context->smarty->assign('grb_check_protocol', Tools::getShopProtocol());
            $this->context->smarty->assign('kb_site_key', Configuration::get('KB_BACKINSTOCK_RECAPTCHA_SITE_KEY'));
            $this->context->smarty->assign('kb_grb_enable', Configuration::get('KB_BACKINSTOCK_RECAPTCHA_ENABLE'));
            //changes by gopi for integration of v3 end here
            //changes by gopi for custom feild
            $kb_final_field = $this->getBisCustomFeild();
            $this->context->smarty->assign('kb_available_field', $kb_final_field);
            //changes by gopi end here
            if (isset($module_data['enable']) && $module_data['enable'] == 1 && $module_data['enable_quantity'] == 1) {
                $this->context->smarty->assign('show_quantity', 1);
            } else {
                $this->context->smarty->assign('show_quantity', 0);
            }

            $this->context->smarty->assign('show_box', $show_box);
            /**
             * Removed the module enable disable condition as it is already check at the start of the function
             * @date 28-03-2023
             * @commenter Prvind Panday
             */
            if ($show_box == 1) {
                $this->context->controller->addCSS($this->_path . 'views/css/hook/productupdate.css');
                $this->context->controller->addJS($this->_path . 'views/js/velovalidation.js');
                return $this->display(__FILE__, 'alert_block.tpl');
            }
        }
    }

    // changes by prvind panday to display the block anywhere on product page on 17-06-22
    public function hookDisplayCustomAlertBlockAnywhere()
    {
        /**
         * checked whether the module is active or not and if active displayhook method is set to 2 or not. 2 means display block anywhere
         * @date 28-03-2023
         * @commenter Prvind Panday
         */
        $module_data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        if (Tools::getValue('controller') == 'product' && isset($module_data['enable']) && $module_data['enable'] == 1 && $module_data['display_hook'] == 2) {
            $customer = array();
            if ($this->context->cookie->logged) {
                $customer['id'] = $this->context->cookie->id_customer;
                $customer['email'] = $this->context->cookie->email;
            } else {
                $customer['id'] = 0;
                $customer['email'] = '';
            }

            $currency_code = $this->context->currency->iso_code;
            $currency_id = $this->context->currency->id;

            $shop_id = $this->context->shop->id;


            $this->mobile_detect = new Mobile_Detect();
            $mobile_class = 'desktop';
            if (isset($_SERVER['HTTP_REFERER'])) {
                $referer_url = $_SERVER['HTTP_REFERER'];
            } else {
                $referer_url = '';
            }

            $demo_frame_type = 'desktop';
            if ((strpos($referer_url, 'ipad') !== false) || (strpos($referer_url, 'iphone') !== false)) {
                $demo_frame_type = 'mobile-tablet';
            }
            if ($this->mobile_detect->isMobile() || $demo_frame_type == 'mobile-tablet') {
                $mobile_class = 'mobile';
            }
            if ($this->mobile_detect->isTablet() || $demo_frame_type == 'mobile-tablet') {
                $mobile_class = 'tablet';
            }
            $id_product = Tools::getValue('id_product');

            $image_query = 'select id_image from ' . _DB_PREFIX_
                . 'image where id_product=' . (int) $id_product . ' and cover=1';

            $image = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($image_query);
            $image_id = $image['id_image'];

            $image_link_query = 'select link_rewrite from ' . _DB_PREFIX_
                . 'product_lang where id_product=' . (int) $id_product;

            $link_rewrite = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($image_link_query);
            $img_link_rewrite = $link_rewrite['link_rewrite'];

            $desc_query = 'select description_short,name from ' . _DB_PREFIX_
                . 'product_lang where id_product=' . (int) $id_product;

            $short = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($desc_query);
            $short_desc = $short['description_short'];
            $product_name = $short['name'];

            $actual_price = Product::getPriceStatic($id_product, true, null, 6);
            $product_price = Tools::displayPrice($actual_price);

            $module_data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
            $style = urldecode(Configuration::get('KB_BACKINSTOCK_CSS'));
            $script = urldecode(Configuration::get('KB_BACKINSTOCK_JS'));
            $customcss = $style;
            $customjs = $script;
            $short_desc = strip_tags($short_desc);
            if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
                $version = 5;
            } else {
                $version = 6;
            }
            $gdpr_policy_text = '';
            $gdpr_policy_url = '';
            if (isset($module_data['product_update_gdpr_policy_text']) && isset($module_data['product_update_gdpr_policy_text'][$this->context->language->id])) {
                $gdpr_policy_text = trim($module_data['product_update_gdpr_policy_text'][$this->context->language->id]);
            }
            if (isset($module_data['product_update_gdpr_policy_url']) && isset($module_data['product_update_gdpr_policy_url'][$this->context->language->id])) {
                $gdpr_policy_url = trim($module_data['product_update_gdpr_policy_url'][$this->context->language->id]);
            }
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
            $this->smarty->assign(
                array(
                    'module_dir' => $module_dir,
                    'background' => $module_data['background'],
                    //changes by vishal
                    'background_heading' => $module_data['background_heading'],
                    'notify_text' => $module_data['notify_text'],
                    'notify_background' => $module_data['notify_background'],
                    'notify_border' => $module_data['notify_border'],
                    //changes end
                    'border' => $module_data['border'],
                    'text' => $module_data['text'],
                    'image_id' => $image_id,
                    'img_link_rewrite' => $img_link_rewrite,
                    'short_desc' => $short_desc,
                    'product_name' => $product_name,
                    'action_product_front_back' => $this->context->link->getModuleLink('backinstock', 'success', array('render' => 'add'), (bool) Configuration::get('PS_SSL_ENABLED')),
                    'product_price' => $product_price,
                    'product_id' => $id_product,
                    'actual_price' => $actual_price,
                    'customer' => $customer,
                    'shop_id' => $shop_id,
                    'currency_code' => $currency_code,
                    'currency_id' => $currency_id,
                    'customcss' => $customcss,
                    'customjs' => $customjs,
                    'version' => $version,
                    'device' => $mobile_class,
                    'enable_gdpr_policy' => $module_data['enable_gdpr_policy'],
                    'gdpr_policy_text' => $gdpr_policy_text,
                    'gdpr_policy_url' => $gdpr_policy_url,
                )
            );
            /* Code added by rishabh Jain on 16th July to hide email alert box if the out of stock order is allowed */
            $product_id = Tools::getValue('id_product');
            $prod_obj = new Product($product_id);
            $attributes = $prod_obj->getAttributesResume($this->context->language->id);
            $product_available_stock = StockAvailable::getQuantityAvailableByProduct($product_id);
            $show_box = 1;
            if (!$product_available_stock) {
                $query = 'Select out_of_stock from ' . _DB_PREFIX_ . 'stock_available where id_product = ' . (int) $product_id . ' and id_shop=' . (int) $this->context->shop->id;
                $stock = Db::getInstance()->getValue($query);
                if ((int) $stock == 1) {
                    $show_box = 0;
                } else if ((int) $stock == 2) {
                    $out_of_stock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
                    if ($out_of_stock == 1) {
                        $show_box = 0;
                    }
                }
            } else {
                if (is_array($attributes) && count($attributes) > 0) {
                    $show_box = 1;
                } else {
                    if ($product_available_stock < 0) {
                        $show_box = 1;
                    } else {
                        $show_box = 0;
                    }
                }
            }
            if (isset($customer['id']) && $customer['id'] > 0) {
                $this->context->smarty->assign('disabled', 1);
            } else {
                $this->context->smarty->assign('disabled', 0);
            }
            /* Changes over */
            //changes by gopi fr integratio of v3 start
            $this->context->smarty->assign('grb_check_protocol', Tools::getShopProtocol());
            $this->context->smarty->assign('kb_site_key', Configuration::get('KB_BACKINSTOCK_RECAPTCHA_SITE_KEY'));
            $this->context->smarty->assign('kb_grb_enable', Configuration::get('KB_BACKINSTOCK_RECAPTCHA_ENABLE'));
            //changes by gopi for integration of v3 end here
            //changes by gopi for custom feild
            $kb_final_field = $this->getBisCustomFeild();
            $this->context->smarty->assign('kb_available_field', $kb_final_field);
            //changes by gopi end here

            $settings = Configuration::get('VELOCITY_AVAILABILITY_SETTINGS');
            $this->product_aval_settings = Tools::unSerialize($settings);

            if (isset($this->product_aval_settings['enable_availability_settings']) && $this->product_aval_settings['enable_availability_settings'] == 1) {
                if (isset($this->product_aval_settings['excluded_products_hidden']) && (!Tools::isEmpty($this->product_aval_settings['excluded_products_hidden']))) {
                    $selectedProductIds = explode(',', $this->product_aval_settings['excluded_products_hidden']);
                    foreach ($selectedProductIds as $productId) {
                        $selectedproducts[] = $productId;
                    }
                }

                if (!empty($this->product_aval_settings['prestashop_category'])) {
                    $categoryTreeSelection = $this->product_aval_settings['prestashop_category'];
                }

                if (!empty($selectedproducts)) {
                    if (in_array($id_product, $selectedproducts)) {
                        $show_box = 0;
                    }
                }

                if (!empty($categoryTreeSelection)) {
                    if (!in_array($prod_obj->id_category_default, $categoryTreeSelection)) {
                        $show_box = 0;
                    }
                }
            }

            if (isset($module_data['enable']) && $module_data['enable'] == 1 && $module_data['enable_quantity'] == 1) {
                $this->context->smarty->assign('show_quantity', 1);
            } else {
                $this->context->smarty->assign('show_quantity', 0);
            }
            /**
             * Removed the module enable disable condition as it is already check at the start of the function
             * @date 28-03-2023
             * @commenter Prvind Panday
             */
            if ($show_box == 1) {
                if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
                    $this->context->controller->addCSS($this->_path . 'views/css/hook/productupdate_15.css');
                }
                $this->context->controller->addCSS($this->_path . 'views/css/hook/productupdate.css');
                return $this->display(__FILE__, 'views/templates/hook/alert_block_anywhere.tpl');
            }
        }
    }
    // Changes over

    public function getBisCustomFeild()
    {
        $field_data = KbBisCustomFields::getAvailableBisCustomFields();
        $field_final = array();
        foreach ($field_data as $key => $field) {
            $shop_ids = explode(",", $field['id_shop']);
            foreach ($shop_ids as $value) {
                if ($value == (int) Context::getContext()->shop->id) {
                    $field_final[] = $field;
                }
            }
        }
        return $field_final;
    }
    public function hookActionProductSave()
    {
        $id_product = Tools::getValue('id_product');
        $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        if ($data['enable'] == 1) {
            /*
             * @author - Rishabh Jain
             * DOC - 24/02/20
             * To send the low stock alert emails on product update
             */
            $is_low_stock_alert_enabled = $data['enable_low_stock_alert'];
            $low_stock_alert_quantity = $data['low_stock_alert_quantity'];
            if ($is_low_stock_alert_enabled) {
                $get_data = 'select distinct(product_attribute_id) from ' . _DB_PREFIX_ . 'product_update_product_detail a 
                    where active=1 and send="1" and low_stock_mail=0 and `order`=0 and product_id=' . (int) $id_product . ' and store_id=' . (int) $this->context->shop->id;
                $user_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_data);
                foreach ($user_data as $user_data_key => $subscriber_data) {
                    $available_quantity = StockAvailable::getQuantityAvailableByProduct(
                        $id_product,
                        $subscriber_data['product_attribute_id'],
                        $this->context->shop->id
                    );
                    if ($available_quantity <= $low_stock_alert_quantity && $available_quantity != 0) {
                        $this->sendLowStockAlertEmail($id_product, $subscriber_data['product_attribute_id']);
                    }
                }
            }
            /*
             * Changes over
             */
            $this->sendEmails($id_product);
        }
    }

    public function hookActionProductUpdate()
    {

        $id_product = Tools::getValue('id_product');
        $pro_obj = new Product($id_product);
        $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        if ($data['enable'] == 1) {
            /*
             * @author - Rishabh Jain
             * DOC - 24/02/20
             * To send the low stock alert emails on product update
             */
            $is_low_stock_alert_enabled = $data['enable_low_stock_alert'];
            $low_stock_alert_quantity = $data['low_stock_alert_quantity'];
            $cron = $data['enable_cron'];
            if ($cron == 1) {
                $stock = Tools::getValue('out_of_stock');
                if ((int) $stock == 1) {
                    $query = 'Update ' . _DB_PREFIX_ . 'product_update_product_detail set allowed_order = 1 where active=1 and send="0" and `order`=0 and product_id=' . (int) $id_product . ' and store_id=' . (int) $this->context->shop->id;
                    $res = Db::getInstance()->execute($query);
                }
            }
            if ($is_low_stock_alert_enabled) {
                $get_data = 'select distinct(product_attribute_id) from ' . _DB_PREFIX_ . 'product_update_product_detail a 
                    where active=1 and send="1" and low_stock_mail=0 and `order`=0 and product_id=' . (int) $id_product . ' and store_id=' . (int) $this->context->shop->id;
                $user_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_data);
                foreach ($user_data as $user_data_key => $subscriber_data) {
                    $available_quantity = StockAvailable::getQuantityAvailableByProduct(
                        $id_product,
                        $subscriber_data['product_attribute_id'],
                        $this->context->shop->id
                    );
                    if ($available_quantity <= $low_stock_alert_quantity && $available_quantity != 0) {
                        $this->sendLowStockAlertEmail($id_product, $subscriber_data['product_attribute_id']);
                    }
                }
            }
            /*
             * Changes over
             */
            $this->sendEmails($id_product);
        }
    }

    public function hookDisplayHeader()
    {
        $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        if ($data['enable'] == 1) {
            $via = Tools::getValue('via');
            if (isset($via) && $via == 'email') {
                $check_sql = 'select * from ' . _DB_PREFIX_ . 'product_update_product_stats where id = 1';
                $res_sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_sql);
                if (!empty($res_sql)) {
                    $total_view_clicks = (int) $res_sql['total_view_clicks'] + 1;
                    $update_stats = 'update `' . _DB_PREFIX_ . 'product_update_product_stats` SET total_view_clicks = ' . (int) $total_view_clicks . ', date_updated = now()';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_stats);
                }
            }
            //            return $this->display(__FILE__, 'views/templates/hooks/header_display.tpl');
        }
    }

    public function sendEmails($id_product)
    {
        /**
         * To delete the theme mails folder if exists
         * @date 06-03-2023
         * @author Kanishka Kannoujia
         * @commenter Prvind Panday
         */
        if (file_exists(_PS_THEME_DIR_ . 'modules/backinstock/mails')) {
            $this->deleteDir(_PS_THEME_DIR_ . 'modules/backinstock/mails');
        }
        $get_data = 'select * from ' . _DB_PREFIX_ . 'product_update_product_detail a 
        where active=1 and send="0" and product_id=' . (int) $id_product . ' and store_id=' . (int) $this->context->shop->id;
        $user_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_data);
        foreach ($user_data as $user) {
            $quantity_query = 'select quantity from ' . _DB_PREFIX_
                . 'stock_available where id_product_attribute='
                . (int) $user['product_attribute_id'] . ' and id_product=' . (int) $user['product_id'] . ' and id_shop=' . (int) $this->context->shop->id;
            $quantity_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($quantity_query);
            if ($quantity_data[0]['quantity'] > 0) {
                $id_image = Product::getCover($user['product_id']);
                $current = Product::getPriceStatic($user['product_id'], true, null, 6);
                /*
                 * Added is_array check before checking count of the array of the images of the product
                 * @author 
                 * @date 31-01-2023
                 * @commenter Prvind Panday
                 */
                if (is_array($id_image) && count($id_image) > 0) {
                    $image = new Image($id_image['id_image']);
                    $img_path = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
                }
                /*
                 * Front Controller - Delete Url Generated to delete the subscriber from the database
                 * @date 30-01-2023
                 * @commenter Prvind Panday
                 */
                $link = $this->context->link->getModuleLink('backinstock', 'delete');
                $url = $this->context->link->getProductLink($user['product_id']);
                $dot_found = 0;
                $needle = '.php';
                $dot_found = strpos($link, $needle);
                if ($dot_found !== false) {
                    $ch = '&';
                } else {
                    $ch = '?';
                }

                $shop_id = Context::getContext()->shop->id;
                $lang_iso = $user['lang_iso'];
                if (empty($lang_iso)) {
                    $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                } else {
                    $id_lang = Language::getIdByIso($lang_iso);
                }
                $cid = $user['product_attribute_id'];
                $id = $user['product_id'];
                $cemail = urlencode($user['email']);
                $delete_url = $link . $ch . 'email=' . $cemail . '&id=' . $id .
                    '&attribute_id=' . $cid . '&shop_id=' . $shop_id;
                $product_obj = new Product($user['product_id'], false, $id_lang, $shop_id);
                $attributes = $product_obj->getAttributeCombinationsById($user['product_attribute_id'], $id_lang);


                $product_name = $product_obj->name;
                $product_description = $product_obj->description_short;
                if (count($attributes) > 0) {
                    $attr = '';
                    foreach ($attributes as $attribute) {
                        $attr .= $attribute['group_name'] . ': ' . $attribute['attribute_name'] . ', ';
                    }
                    $attr = Tools::substr($attr, 0, -2);
                } else {
                    $attr = '';
                }

                if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                    $ps_base_url = _PS_BASE_URL_SSL_;
                } else {
                    $ps_base_url = _PS_BASE_URL_;
                }
                $getsubject = 'select subject,body from ' . _DB_PREFIX_ . 'product_update_email_templates where id_lang='
                    . (int) $id_lang . ' and template_no="2"';
                $data_subject = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($getsubject);
                //changes by vishal for adding related products functioanlity
                $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
                if ($data['enable_related_product_final'] == 1) {
                    if ($data['related_product_method_final'] == 1) {
                        $results = ProductSale::getBestSalesLight((int) $this->context->language->id, 0, 10);
                        if (!empty($results)) {
                            $kb_products = array();
                            foreach ($results as $key => $value) {
                                $kb_array = array();
                                $kb_product_obj = new Product($value['id_product']);
                                $kb_array['id_product'] = $value['id_product'];
                                $kb_array['description_short'] = $kb_product_obj->description_short[$this->context->language->id];
                                $kb_array['name'] = $kb_product_obj->name[$this->context->language->id];
                                $kb_array['link_rewrite'] = $kb_product_obj->link_rewrite[$this->context->language->id];
                                $kb_products[] = $kb_array;
                            }
                        } else {
                            $kb_products = array();
                        }
                    } else if ($data['related_product_method_final'] == 2) {
                        $kb_prod_obj = new Product($id_product);

                        $kb_products = Product::getProducts((int) $this->context->language->id, 0, 4, 'id_product', 'ASC', $kb_prod_obj->id_category_default);
                    } else if ($data['related_product_method_final'] == 3) {
                        if (!empty($data['specific_products_final'])) {
                            $kb_products = array();
                            $kb_array = array();
                            foreach ($data['specific_products_final'] as $key => $value) {
                                $kb_product_obj = new Product($value);
                                /*
                                 * Added a condition to filter out the products which are not active and not in stock
                                 * @author Prvind Panday
                                 * @date 26-01-2023
                                 * @commenter Prvind Panday
                                 */
                                if ($kb_product_obj->active == 0 || $kb_product_obj->quantity == 0) {
                                    continue;
                                }
                                $kb_array['id_product'] = $value;
                                $kb_array['description_short'] = $kb_product_obj->description_short[$this->context->language->id];
                                $kb_array['name'] = $kb_product_obj->name[$this->context->language->id];
                                $kb_array['link_rewrite'] = $kb_product_obj->link_rewrite[$this->context->language->id];
                                $kb_products[] = $kb_array;
                            }
                        } else {
                            $kb_products = array();
                        }
                    }
                    if (!empty($kb_products)) {
                        $link = new Link();
                        $cart_html = "";
                        if (isset($data['initial_related_title'][$this->context->language->id]) && !empty($data['initial_related_title'][$this->context->language->id])) {
                            $heading = $data['final_related_title'][$this->context->language->id];
                        } else {
                            $heading = "RELATED PRODUCTS";
                        }
                        $kb_final_data = array();
                        foreach ($kb_products as $products) {
                            $kb_temp = array();
                            $kb_product_obj = new Product($products['id_product']);
                            $kb_id_image = $kb_product_obj->getImages((int) $this->context->language->id);
                            if (empty($kb_id_image)) {
                                continue;
                            }
                            if (!isset($products['attributes'])) {
                                $products['attributes'] = ' ';
                            }
                            if (!isset($products['name'])) {
                                $products['name'] = ' ';
                            }
                            if (!isset($products['description_short'])) {
                                $products['description_short'] = ' ';
                            }
                            if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                                $kb_img_path = 'https://' . $link->getImageLink($products['link_rewrite'], $kb_id_image[0]['id_image']);
                            } else {
                                $kb_img_path = 'http://' . $link->getImageLink($products['link_rewrite'], $kb_id_image[0]['id_image']);
                            }
                            $id_lang = $this->context->language->id;
                            $kb_temp['name'] = $products['name'];
                            $kb_temp['image'] = $kb_img_path;
                            if (isset($data['enable_utm']) && $data['enable_utm'] == 1) {
                                $utm_paramters = 'utm_source=' . $data['product_update_utm_source'] . '&utm_medium=' . $data['product_update_utm_medium'] . '&utm_campaign=' . $data['product_update_utm_campaign'];
                                $kb_product_link_new = $this->context->link->getProductLink($kb_product_obj, null, null, null, $id_lang, $shop_id);
                                if (strpos($kb_product_link_new, '?') !== false) {
                                    $kb_product_link_new .= '&' . $utm_paramters;
                                } else {
                                    $kb_product_link_new .= '?' . $utm_paramters;
                                }
                            } else {
                                $kb_product_link_new = $this->context->link->getProductLink($kb_product_obj, null, null, null, $id_lang, $shop_id);
                            }
                            $kb_price = Tools::displayPrice(Product::getPriceStatic($products['id_product'], true));
                            $kb_temp['kb_product_link_new'] = $kb_product_link_new;
                            $kb_temp['price'] = $kb_price;
                            $kb_final_data[] = $kb_temp;
                        }
                    }
                }
                $this->context->smarty->assign('kb_heading', $heading);
                $this->context->smarty->assign('kb_product', $kb_final_data);
                $cart_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'backinstock/views/templates/front/final_mail_content.tpl');
                if (!empty($kb_products)) {
                    $kb_cart_html = $cart_html;
                } else {
                    $kb_cart_html = "";
                }
                $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
                $id_lang = $this->context->language->id;
                if (isset($data['enable_utm']) && $data['enable_utm'] == 1) {
                    $utm_paramters = 'utm_source=' . $data['product_update_utm_source'] . '&utm_medium=' . $data['product_update_utm_medium'] . '&utm_campaign=' . $data['product_update_utm_campaign'];
                    $kb_product_link = $this->context->link->getProductLink($product_obj, null, null, null, $id_lang, $shop_id);
                    if (strpos($kb_product_link, '?') !== false) {
                        $kb_product_link .= '&' . $utm_paramters;
                    } else {
                        $kb_product_link .= '?' . $utm_paramters;
                    }
                } else {
                    $kb_product_link = $this->context->link->getProductLink($product_obj, null, null, null, $id_lang, $shop_id);
                }
                if (strpos($kb_product_link, '?') !== false) {
                    $kb_product_link .= '&via=email';
                } else {
                    $kb_product_link .= '?via=email';
                } /*
                  * Modified the current_price to show correct price with tax for the customer group 
                  * @date 27-01-2023
                  * @author Prvind Panday
                  */
                // get customer session
                if (isset($user['customer_id']) && $user['customer_id'] != 0) {
                    $customer = new Customer($user['customer_id']);
                    $id_group = $customer->id_default_group;
                } else {
                    $id_group = (int) Configuration::get('PS_GUEST_GROUP');
                }
                $group = new Group($id_group);
                $current_price = Tools::displayPrice(
                    $product_obj->getPriceStatic(
                        (int) $user['product_id'],
                        $group->price_display_method ? false : true,
                        (int) $user['product_attribute_id'],
                        6,
                        null,
                        false,
                        true
                    )
                );
                /*
                 * Checked whether the product has specific price or not, if yes then it will show the specific price with tax or without tax for the customer group
                 * @date 30-01-2023
                 * @commenter Prvind Panday
                 */
                if (!empty($product_obj->specific_prices)) {
                    $current_price = Tools::displayPrice(
                        $product_obj->getPriceStatic(
                            (int) $user['product_id'],
                            $group->price_display_method ? false : true,
                            (int) $user['product_attribute_id'],
                            6,
                            null,
                            false,
                            false
                        )
                    );
                }
                //changes end
                /*
                 * Template variables created for the email content, the variables are replaced in the email content
                 * @date 30-01-2023
                 * @commenter Prvind Panday
                 */
                /*
                 * Checked the image path is not empty and if empty then set the default image path else blank
                 * @author Prvind Panday
                 * @date 31-01-2023
                 * @commenter Prvind Panday
                 */
                //changes end
                $template_vars = array(
                    '{template}' => $data_subject['body'],
                    '{related_product_content}' => $kb_cart_html,
                    '{minimal_image}' => $this->context->link->getMediaLink(
                        __PS_BASE_URI__ . 'modules/backinstock/views/img/minimal6.png'
                    ),
                    '{product_description}' => $product_description,
                    '{product_link}' => $kb_product_link,
                    '{product_image}' => isset($img_path) ? $img_path : '',
                    '{product_name}' => $product_name,
                    '{current_price}' => $current_price,
                    '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_url}' => _PS_BASE_URL_ . __PS_BASE_URI__,
                    'ps_root_path' => $ps_base_url
                    . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', ''),
                    '{url}' => $url
                );
                unset($product_obj);

                $subject = html_entity_decode($data_subject['subject']);
                $email = $user['email'];
                $update_time = 'update ' . _DB_PREFIX_ . 'product_update_product_detail'
                    . ' set mail_send_date=now(),send="1" where id=' . (int) $user['id'];
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_time);
                if (Mail::Send($id_lang, 'quantity_drop', $subject, $template_vars, $email, null, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), null, null, dirname(__FILE__) . '/mails/', false, $this->context->shop->id)) {
                    $update_time = 'update ' . _DB_PREFIX_ . 'product_update_product_detail'
                        . ' set mail_send_date=now(),send="1" where id=' . (int) $user['id'];
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_time);
                    $check_sql = 'select * from ' . _DB_PREFIX_ . 'product_update_product_stats where id = 1';
                    $res_sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_sql);
                    if (!empty($res_sql)) {
                        $total_sent = (int) $res_sql['total_sent'] + 1;
                        $update_stats = 'update `' . _DB_PREFIX_ . 'product_update_product_stats` SET total_sent = ' . (int) $total_sent . ' date_updated = now()';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_stats);
                    } else {
                        $insert_stats = 'INSERT into `' . _DB_PREFIX_ . 'product_update_product_stats` SET total_sent = 1, total_opened = 0, total_buy_now_clicks = 0, total_view_clicks = 0, date_added = now(), date_updated = now()';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insert_stats);
                    }
                }
                $directory = _PS_MODULE_DIR_ . 'backinstock/mails/' . $lang_iso . '/';
                if (is_writable($directory)) {
                    $html_template = 'quantity_drop.html';
                    $txt_template = 'quantity_drop.txt';

                    $base_html = Tools::file_get_contents(_PS_MODULE_DIR_ . "backinstock/views/templates/admin/html_content_final.html");
                    $template_html = str_replace('[template_content]', html_entity_decode($data_subject['body']), $base_html);
                    $file = fopen($directory . $html_template, 'w+');
                    fwrite($file, $template_html);
                    fclose($file);

                    $file = fopen($directory . $txt_template, 'w+');
                    fwrite($file, $template_html);
                    fclose($file);
                }
            }
        }
    }

    /*
     * Function to send the low stock alert mails to customer
     * who have subscribed and have recieved the back in stock mails
     */

    public function sendLowStockAlertEmail($id_product, $id_product_attribute)
    {
        /**
         * To delete the theme mails folder if exists
         * @date 06-03-2023
         * @author Kanishka Kannoujia
         * @commenter Prvind Panday
         */
        if (file_exists(_PS_THEME_DIR_ . 'modules/backinstock/mails')) {
            $this->deleteDir(_PS_THEME_DIR_ . 'modules/backinstock/mails');
        }
        $get_data = 'select * from ' . _DB_PREFIX_ . 'product_update_product_detail a 
            where active=1 and send="1" and low_stock_mail=0 and `order`=0 and product_id=' . (int) $id_product . ' and product_attribute_id = ' . (int) $id_product_attribute . ' and store_id=' . (int) $this->context->shop->id;
        $user_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_data);
        foreach ($user_data as $user) {
            $id_image = Product::getCover($user['product_id']);
            $current = Product::getPriceStatic($user['product_id'], true, null, 6);
            if (count($id_image) > 0) {
                $image = new Image($id_image['id_image']);
                $img_path = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
            }

            $link = $this->context->link->getModuleLink('backinstock', 'delete');

            $url = $this->context->link->getProductLink($user['product_id']);

            $dot_found = 0;
            $needle = '.php';
            $dot_found = strpos($link, $needle);
            if ($dot_found !== false) {
                $ch = '&';
            } else {
                $ch = '?';
            }

            $shop_id = Context::getContext()->shop->id;
            $lang_iso = $user['lang_iso'];
            if (empty($lang_iso)) {
                $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
            } else {
                $id_lang = Language::getIdByIso($lang_iso);
            }
            $cid = $user['product_attribute_id'];
            $id = $user['product_id'];
            $cemail = urlencode($user['email']);
            $delete_url = $link . $ch . 'email=' . $cemail . '&id=' . $id .
                '&attribute_id=' . $cid . '&shop_id=' . $shop_id;
            $product_obj = new Product($user['product_id'], false, $id_lang, $shop_id);
            $attributes = $product_obj->getAttributeCombinationsById($user['product_attribute_id'], $id_lang);

            $product_name = $product_obj->name;
            $product_description = $product_obj->description_short;
            if (count($attributes) > 0) {
                $attr = '';
                foreach ($attributes as $attribute) {
                    $attr .= $attribute['group_name'] . ': ' . $attribute['attribute_name'] . ', ';
                }
                $attr = Tools::substr($attr, 0, -2);
            } else {
                $attr = '';
            }

            if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                $ps_base_url = _PS_BASE_URL_SSL_;
            } else {
                $ps_base_url = _PS_BASE_URL_;
            }
            $getsubject = 'select subject,body from ' . _DB_PREFIX_ . 'product_update_email_templates where id_lang='
                . (int) $id_lang . ' and template_no="3"';
            $data_subject = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($getsubject);
            //changes by vishal for adding related products functioanlity
            $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
            if ($data['enable_related_product_low_stock'] == 1) {
                if ($data['related_product_method_low_stock'] == 1) {
                    $results = ProductSale::getBestSalesLight((int) $this->context->language->id, 0, 10);
                    if (!empty($results)) {
                        $kb_products = array();
                        foreach ($results as $key => $value) {
                            $kb_array = array();
                            $kb_product_obj = new Product($value['id_product']);
                            $kb_array['id_product'] = $value['id_product'];
                            $kb_array['description_short'] = $kb_product_obj->description_short[$this->context->language->id];
                            $kb_array['name'] = $kb_product_obj->name[$this->context->language->id];
                            $kb_array['link_rewrite'] = $kb_product_obj->link_rewrite[$this->context->language->id];
                            $kb_products[] = $kb_array;
                        }
                    } else {
                        $kb_products = array();
                    }
                } else if ($data['related_product_method_low_stock'] == 2) {
                    $kb_prod_obj = new Product($user['product_id']);

                    $kb_products = Product::getProducts((int) $this->context->language->id, 0, 4, 'id_product', 'ASC', $kb_prod_obj->id_category_default);
                } else if ($data['related_product_method_low_stock'] == 3) {
                    if (!empty($data['specific_products_low_stock'])) {
                        $kb_products = array();
                        $kb_array = array();
                        foreach ($data['specific_products_low_stock'] as $key => $value) {
                            $kb_product_obj = new Product($value);
                            $kb_array['id_product'] = $value;
                            $kb_array['description_short'] = $kb_product_obj->description_short[$this->context->language->id];
                            $kb_array['name'] = $kb_product_obj->name[$this->context->language->id];
                            $kb_array['link_rewrite'] = $kb_product_obj->link_rewrite[$this->context->language->id];
                            $kb_products[] = $kb_array;
                        }
                    } else {
                        $kb_products = array();
                    }
                }
                if (!empty($kb_products)) {
                    $link = new Link();
                    $cart_html = "";
                    if (isset($data['initial_related_title'][$this->context->language->id]) && !empty($data['initial_related_title'][$this->context->language->id])) {
                        $heading = $data['low_stock_related_title'][$this->context->language->id];
                    } else {
                        $heading = "RELATED PRODUCTS";
                    }
                    $kb_final_data = array();
                    foreach ($kb_products as $products) {
                        $kb_temp = array();
                        $kb_product_obj = new Product($products['id_product']);
                        $kb_id_image = $kb_product_obj->getImages((int) $this->context->language->id);
                        if (empty($kb_id_image)) {
                            continue;
                        }
                        if (!isset($products['attributes'])) {
                            $products['attributes'] = ' ';
                        }
                        if (!isset($products['name'])) {
                            $products['name'] = ' ';
                        }
                        if (!isset($products['description_short'])) {
                            $products['description_short'] = ' ';
                        }
                        if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                            $kb_img_path = 'https://' . $link->getImageLink($products['link_rewrite'], $kb_id_image[0]['id_image']);
                        } else {
                            $kb_img_path = 'http://' . $link->getImageLink($products['link_rewrite'], $kb_id_image[0]['id_image']);
                        }
                        $id_lang = $this->context->language->id;
                        $kb_temp['name'] = $products['name'];
                        $kb_temp['image'] = $kb_img_path;
                        if (isset($data['enable_utm']) && $data['enable_utm'] == 1) {
                            $utm_paramters = 'utm_source=' . $data['product_update_utm_source'] . '&utm_medium=' . $data['product_update_utm_medium'] . '&utm_campaign=' . $data['product_update_utm_campaign'];
                            $kb_product_link_new = $this->context->link->getProductLink($kb_product_obj, null, null, null, $id_lang, $shop_id);
                            if (strpos($kb_product_link_new, '?') !== false) {
                                $kb_product_link_new .= '&' . $utm_paramters;
                            } else {
                                $kb_product_link_new .= '?' . $utm_paramters;
                            }
                        } else {
                            $kb_product_link_new = $this->context->link->getProductLink($kb_product_obj, null, null, null, $id_lang, $shop_id);
                        }
                        $kb_price = Tools::displayPrice(Product::getPriceStatic($products['id_product'], true));
                        $kb_temp['kb_product_link_new'] = $kb_product_link_new;
                        $kb_temp['price'] = $kb_price;
                        $kb_final_data[] = $kb_temp;
                    }
                }
            }
            $this->context->smarty->assign('kb_heading', $heading);
            $this->context->smarty->assign('kb_product', $kb_final_data);
            $cart_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'backinstock/views/templates/front/low_mail_content.tpl');
            if (!empty($kb_products)) {
                $kb_cart_html = $cart_html;
            } else {
                $kb_cart_html = "";
            }
            $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
            $id_lang = $this->context->language->id;
            if (isset($data['enable_utm']) && $data['enable_utm'] == 1) {
                $utm_paramters = 'utm_source=' . $data['product_update_utm_source'] . '&utm_medium=' . $data['product_update_utm_medium'] . '&utm_campaign=' . $data['product_update_utm_campaign'];
                $kb_product_link = $this->context->link->getProductLink($product_obj, null, null, null, $id_lang, $shop_id);
                if (strpos($kb_product_link, '?') !== false) {
                    $kb_product_link .= '&' . $utm_paramters;
                } else {
                    $kb_product_link .= '?' . $utm_paramters;
                }
            } else {
                $kb_product_link = $this->context->link->getProductLink($product_obj, null, null, null, $id_lang, $shop_id);
            }
            if (strpos($kb_product_link, '?') !== false) {
                $kb_product_link .= '&via=email';
            } else {
                $kb_product_link .= '?via=email';
            }
            /*
             * Modified the current_price to show correct price with tax for the customer group 
             * @date 27-01-2023
             * @author Prvind Panday
             */
            // get customer session
            if (isset($user['customer_id']) && $user['customer_id'] != 0) {
                $customer = new Customer($user['customer_id']);
                $id_group = $customer->id_default_group;
            } else {
                $id_group = (int) Configuration::get('PS_GUEST_GROUP');
            }
            $group = new Group($id_group);
            $current_price = Tools::displayPrice(
                $product_obj->getPriceStatic(
                    (int) $user['product_id'],
                    $group->price_display_method ? false : true,
                    (int) $user['product_attribute_id'],
                    6,
                    null,
                    false,
                    true
                )
            );
            /*
             * Checked whether the product has specific price or not, if yes then it will show the specific price with tax or without tax for the customer group
             * @date 30-01-2023
             * @commenter Prvind Panday
             */
            if (!empty($product_obj->specific_prices)) {
                $current_price = Tools::displayPrice(
                    $product_obj->getPriceStatic(
                        (int) $user['product_id'],
                        $group->price_display_method ? false : true,
                        (int) $user['product_attribute_id'],
                        6,
                        null,
                        false,
                        false
                    )
                );
            }
            //changes end
            /*
             * Template variables created for the email content, the variables are replaced in the email content
             * @date 30-01-2023
             * @commenter Prvind Panday
             */
            /*
             * Checked the image path is not empty and if empty then set the default image path else blank
             * @author Prvind Panday
             * @date 31-01-2023
             * @commenter Prvind Panday
             */
            //changes end
            $template_vars = array(
                '{template}' => $data_subject['body'],
                '{related_product_content}' => $kb_cart_html,
                '{minimal_image}' => $this->context->link->getMediaLink(
                    __PS_BASE_URI__ . 'modules/backinstock/views/img/minimal6.png'
                ),
                '{product_description}' => $product_description,
                '{product_link}' => $kb_product_link,
                '{product_image}' => isset($img_path) ? $img_path : '',
                '{product_name}' => $product_name,
                '{current_price}' => $current_price,
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{shop_url}' => _PS_BASE_URL_ . __PS_BASE_URI__,
                'ps_root_path' => $ps_base_url
                . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', ''),
                '{url}' => $url
            );
            unset($product_obj);
            $subject = html_entity_decode($data_subject['subject']);
            $email = $user['email'];
            //            $id_lang = Language::getIdByIso($lang_iso);
            $update_time = 'update ' . _DB_PREFIX_ . 'product_update_product_detail'
                . ' set low_stock_mail="1" where id=' . (int) $user['id'];
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_time);
            if (Mail::Send($id_lang, 'low_stock', $subject, $template_vars, $email, null, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), null, null, dirname(__FILE__) . '/mails/', false, $this->context->shop->id)) {

                $update_time = 'update ' . _DB_PREFIX_ . 'product_update_product_detail'
                    . ' set low_stock_mail="1" where id=' . (int) $user['id'];
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_time);
                $check_sql = 'select * from ' . _DB_PREFIX_ . 'product_update_product_stats where id = 1';
                $res_sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_sql);
                if (!empty($res_sql)) {
                    $total_sent = (int) $res_sql['total_sent'] + 1;
                    $update_stats = 'update `' . _DB_PREFIX_ . 'product_update_product_stats` SET total_sent = ' . (int) $total_sent . ', date_updated = now()';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_stats);
                } else {
                    $insert_stats = 'INSERT into `' . _DB_PREFIX_ . 'product_update_product_stats` SET total_sent = 1, total_opened = 0, total_buy_now_clicks = 0, total_view_clicks = 0, date_added = now(), date_updated = now()';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insert_stats);
                }
            }
            $directory = _PS_MODULE_DIR_ . 'backinstock/mails/' . $lang_iso . '/';
            if (is_writable($directory)) {
                $html_template = 'low_stock.html';
                $txt_template = 'low_stock.txt';

                $base_html = Tools::file_get_contents(_PS_MODULE_DIR_ . "backinstock/views/templates/admin/html_content_final.html");
                $template_html = str_replace('[template_content]', html_entity_decode($data_subject['body']), $base_html);
                $file = fopen($directory . $html_template, 'w+');
                fwrite($file, $template_html);
                fclose($file);

                $file = fopen($directory . $txt_template, 'w+');
                fwrite($file, $template_html);
                fclose($file);
            }
        }
    }

    public function getGraphData($data)
    {
        $json_data = array();
        $data['from'] = date('Y-m-d', strtotime($data['from']));
        $data['to'] = date('Y-m-d', strtotime($data['to']));
        $graph_data = array();
        $combination = array();
        $lang_id = $this->context->cookie->id_lang;
        $shop_id = Context::getContext()->shop->id;
        $filter_query = 'Select product_id, product_attribute_id, count(*) as count from '
            . _DB_PREFIX_ . 'product_update_product_detail '
            . 'where active = 1 AND store_id=' . $shop_id . ' group by product_id, product_attribute_id order by product_attribute_id ASC limit 10';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($filter_query);
        $d = 0;
        $products = array();
        $product_name = array();
        foreach ($result as $product) {
            $product_obj = new Product($product['product_id'], false, $lang_id, $shop_id);
            $attributes = $product_obj->getAttributeCombinationsById($product['product_attribute_id'], $lang_id);

            $products[$d]['model'] = $product_obj->reference;
            $product_name[$d]['name'] = sprintf($this->l('%s'), $product_obj->name);
            $d++;
        }
        $temp_attr = array();
        foreach ($result as $row) {
            if ($row['product_attribute_id'] == '' || $row['product_attribute_id'] == null) {
                $temp_attr = $row['count'];
            } else {
                $product_obj = new Product($row['product_id'], false, $this->context->language->id, $this->context->shop->id);
                $attributes = $product_obj->getAttributeCombinationsById($row['product_attribute_id'], $this->context->language->id);
                $temp = array();
                foreach ($attributes as $attribute) {
                    $temp[] = sprintf($this->l('%s'), $attribute['group_name']) . ': ' . sprintf($this->l('%s'), $attribute['attribute_name']);
                }
                $temp_str = implode('<br>', $temp);
                if ($row['product_attribute_id'] != 0) {
                    $temp_attr[$row['product_attribute_id']] = array(
                        'label' => $temp_str,
                        'count' => $row['count']
                    );
                } else {
                    $temp_attr[$row['product_attribute_id'] . '_' . $row['product_id']] = array(
                        'label' => $temp_str,
                        'count' => $row['count']
                    );
                }

                if (!$this->hasProductAttr($row['product_attribute_id'], $combination)) {
                    $combination[] = array(
                        'key' => $row['product_attribute_id'],
                        'label' => $temp_str
                    );
                } else {
                    $combination[] = array(
                        'key' => $row['product_attribute_id'] . '_' . $row['product_id'],
                        'label' => $temp_str
                    );
                }
            }
        }
        $graph_data[] = $temp_attr;
        $c = 0;
        $count_each = array();
        foreach ($temp_attr as $count_x) {
            $count_each[$c] = $count_x['count'];
            $c++;
        }
        $json_data = array(
            'data' => $graph_data,
            'combination' => $combination,
            'model' => $products,
            'count' => $count_each,
            'name' => $product_name
        );
        return Tools::jsonEncode($json_data);
    }

    public function geStatshData()
    {
        $json_data = array();

        $check_sql = 'select * from ' . _DB_PREFIX_ . 'product_update_product_stats where id = 1';
        $res_sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_sql);
        if (!empty($res_sql)) {
            $json_data = array(
                'data' => $res_sql
            );
        } else {
            $json_data = array(
                'data' => $res_sql
            );
        }
        return Tools::jsonEncode($json_data);
    }

    private function hasProductAttr($key, $arr)
    {
        foreach ($arr as $val) {
            if ($val['key'] == $key) {
                return true;
            }
        }
        return false;
    }

    public function getAlertData($data)
    {
        $filtered_data = array();
        $i = 0;
        $shop_id = Context::getContext()->shop->id;
        $lang_id = $this->context->cookie->id_lang;
        if ($data['category'] != 'nothing' && $data['skv'] == '') {
            $data_array = explode(',', $data['category']);
            $search_query = 'select product_id,email,subscribe_type,date_added,product_attribute_id,
                current_price, count(product_attribute_id) as user_count,
                        currency_code from ' . _DB_PREFIX_ .
                'product_update_product_detail where store_id=' . (int) $shop_id . ' and (';
            $count_array = count($data_array);
            for ($cat = 0; $cat < $count_array; $cat++) {
                if ($cat == count($data_array) - 1) {
                    $search_query .= 'category_id like("%' . (int) $data_array[$cat] . '%"))';
                } else {
                    $search_query .= 'category_id like("%' . (int) $data_array[$cat] . '%") or ';
                }
            }
        } elseif ($data['category'] != 'nothing' && $data['skv'] != '') {
            $data_array = explode(',', $data['category']);
            $search_query = 'select product_id,email,subscribe_type,date_added,
                product_attribute_id,current_price, count(product_attribute_id) as user_count,
                        currency_code from ' . _DB_PREFIX_ .
                'product_update_product_detail where store_id=' . (int) $shop_id . ' and ( ';
            $count_array_x = count($data_array);
            for ($cat = 0; $cat < $count_array_x; $cat++) {
                if ($cat == count($data_array) - 1) {
                    $search_query .= 'category_id like("%' . (int) $data_array[$cat] . '%"))';
                } else {
                    $search_query .= 'category_id like("%' . (int) $data_array[$cat] . '%") or ';
                }
            }
            $search_query .= ' or skv like "%' . pSQL($data['skv'])
                . '%" or product_name like "%' . pSQL($data['skv']) . '%"';
        } elseif ($data['category'] == 'nothing' && $data['skv'] != '') {
            $search_query = 'select product_id,email,subscribe_type,
                date_added,product_attribute_id,current_price, count(product_attribute_id) as user_count,
                        currency_code from ' . _DB_PREFIX_ . 'product_update_product_detail where store_id='
                . (int) $shop_id . ' and skv like "%' . pSQL($data['skv']) .
                '%" or product_name like "%' . pSQL($data['skv']) . '%"';
        } elseif ($data['category'] == 'nothing' && $data['skv'] == '') {
            $search_query = 'select product_id,email,subscribe_type,date_added,
                product_attribute_id,current_price, count(product_attribute_id) as user_count,
                        currency_code from ' . _DB_PREFIX_ .
                'product_update_product_detail where store_id=' . (int) $shop_id;
        }
        $search_query .= ' group by 
                              product_id,product_attribute_id,
                        current_price,currency_code';
        $product_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($search_query);
        foreach ($product_data as $product) {
            $product_obj = new Product($product['product_id'], false, $lang_id, $shop_id);
            $attributes = $product_obj->getAttributeCombinationsById($product['product_attribute_id'], $lang_id);

            $product['model'] = $product_obj->reference;
            $product['name'] = $product_obj->name;
            //$product_name = $product_obj->name;
            if (count($attributes) > 0) {
                $product['attributes'] = '';
                foreach ($attributes as $attribute) {
                    $product['attributes'] .= sprintf($this->l('%s'), $attribute['group_name']) . ': ' . sprintf($this->l('%s'), $attribute['attribute_name']) . ', ';
                }
                $product['attributes'] = Tools::substr($product['attributes'], 0, -2);
            } else {
                $product['attributes'] = '';
            }
            $filtered_data[$i]['count'] = $product['user_count'];
            $filtered_data[$i]['product_attribute_id'] = $product['product_attribute_id'];
            $filtered_data[$i]['email'] = $product['email'];
            $filtered_data[$i]['date_added'] = $product['date_added'];
            $filtered_data[$i] = $product;
            $filtered_data[$i]['current_price'] = Tools::displayPrice($filtered_data[$i]['current_price']);

            $i++;
            unset($product_obj);
        }

        $val = 0;
        foreach ($filtered_data as $data) {
            $val++;
        }
        if ($val > 0) {
            $this->smarty->assign('product_data', $filtered_data);
        } else {
            $this->smarty->assign('product_data', 0);
        }

        return $this->display(__FILE__, 'views/templates/admin/filter.tpl');
    }

    public function displayPopup($popup_data)
    {
        $search_query = 'select email,subscribe_type,skv,product_name,date_added from '
            . _DB_PREFIX_ . 'product_update_product_detail where product_attribute_id=' . (int) $popup_data;
        $product_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($search_query);
        $h = 0;
        $popupdata = array();
        foreach ($product_data as $product) {
            $popupdata[$h]['email'] = $product['email'];
            $popupdata[$h]['product_name'] = $product['product_name'];
            $popupdata[$h]['subscribe_type'] = $product['subscribe_type'];
            $popupdata[$h]['date_added'] = $product['date_added'];
            $popupdata[$h]['skv'] = $product['skv'];
            $h++;
        }
        //d($popupdata);
        $this->smarty->assign('popupdata', $popupdata);
        return $this->display(__FILE__, 'views/templates/admin/product_popup.tpl');
    }

    public function deleteProduct($data)
    {
        $check_query = 'select count(*) as if_exist from ' . _DB_PREFIX_ .
            'product_update_product_detail where email="' . pSQL($data['email'], true) . '"
                        and product_id=' . (int) $data['product_id'] .
            ' and product_attribute_id=' . (int) $data['attr'] . ' and store_id='
            . (int) $data['shop_id'];
        $check_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_query);
        if ($check_data['if_exist'] == 0) {
            return 0;
        } else {
            $delete_query = 'delete from ' . _DB_PREFIX_ . 'product_update_product_detail where email="'
                . pSQL($data['email']) . '"
                                and product_id=' . (int) $data['product_id'] . ' and product_attribute_id='
                . (int) $data['attr'] . ' and store_id=' . (int) $data['shop_id'];

            return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($delete_query);
        }
    }

    private function generateTemplateFiles($html, $text, $iso, $id)
    {
        $directory = _PS_MODULE_DIR_ . 'backinstock/mails/' . $iso;
        if (!file_exists($directory)) {
            $mail_dir = dirname(__FILE__) . '/mails/en';
            $new_dir = dirname(__FILE__) . '/mails/' . $iso;
            $this->copyfolder($mail_dir, $new_dir);
        }
        if (is_writable($directory)) {
            if ($id == 1) {
                if (is_writable($directory . '/quantity.txt') && is_writable($directory . '/quantity.html')) {
                    $f = fopen($directory . '/quantity.txt', 'w');
                    fwrite($f, $text);
                    fwrite($f, PHP_EOL);
                    fclose($f);
                    $f = fopen($directory . '/quantity.html', 'w');
                    $base_html = $this->getTemplateBaseHtml($id);
                    $final_html = str_replace('{template_content}', $html, $base_html);
                    fwrite($f, $final_html);
                    fwrite($f, PHP_EOL);
                    fclose($f);
                    return true;
                } else {
                    return false;
                }
            } elseif ($id == 2) {
                if (is_writable($directory . '/quantity_drop.txt') && is_writable($directory . '/quantity_drop.html')) {
                    $f = fopen($directory . '/quantity_drop.txt', 'w');
                    fwrite($f, $text);
                    fwrite($f, PHP_EOL);
                    fclose($f);
                    $f = fopen($directory . '/quantity_drop.html', 'w');
                    $base_html = $this->getTemplateBaseHtml($id);
                    $final_html = str_replace('{template_content}', $html, $base_html);
                    fwrite($f, $final_html);
                    fwrite($f, PHP_EOL);
                    fclose($f);
                    return true;
                } else {
                    return false;
                }
            } elseif ($id == 3) {
                if (is_writable($directory . '/low_stock.txt') && is_writable($directory . '/low_stock.html')) {
                    $f = fopen($directory . '/low_stock.txt', 'w');
                    fwrite($f, $text);
                    fwrite($f, PHP_EOL);
                    fclose($f);
                    $f = fopen($directory . '/low_stock.html', 'w');
                    $base_html = $this->getTemplateBaseHtml($id);
                    $final_html = str_replace('{template_content}', $html, $base_html);
                    fwrite($f, $final_html);
                    fwrite($f, PHP_EOL);
                    fclose($f);
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    private function getTemplateBaseHtml($id)
    {
        if ($id == 1) {
            $template_html = Tools::file_get_contents(dirname(__FILE__) . "/views/templates/admin/html_content_initial.html");
            //            $template_html = $this->display(__FILE__, 'views/templates/admin/html_content_initial.tpl');
            $template_html = str_replace('[', '{', $template_html);
            $template_html = str_replace(']', '}', $template_html);
        } else {
            $template_html = Tools::file_get_contents(dirname(__FILE__) . "/views/templates/admin/html_content_final.html");
            //            $template_html = $this->display(__FILE__, 'views/templates/admin/html_content_final.tpl');
            $template_html = str_replace('[', '{', $template_html);
            $template_html = str_replace(']', '}', $template_html);
        }
        return $template_html;
    }

    public function copyfolder($source, $destination)
    {
        $directory = opendir($source);
        if (!Tools::file_exists_no_cache($destination)) {
            mkdir($destination);
        }
        while (($file = readdir($directory)) != false) {
            if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
                copy($source . '/' . $file, $destination . '/' . $file);
            } else {
                Tools::copy($source . '/' . $file, $destination . '/' . $file);
            }
        }
        closedir($directory);
    }

    protected function getDefaultEmailMarketingSettings()
    {
        $settings = array();
        $settings = array(
            'mailchimp_status' => 0,
            'mailchimp_api' => '',
            'mailchimp_list' => '',
            'klaviyo_status' => 0,
            'klaviyo_api' => '',
            'klaviyo_list' => '',
            'SendinBlue_status' => 0,
            'SendinBlue_list' => '',
            'SendinBlue_api' => '',
        );
        return $settings;
    }

    protected function getDefaultSettings()
    {
        $settings = array();
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $settings['product_update_gdpr_policy_text'][$lang['id_lang']] = '';
        }
        foreach ($languages as $lang) {
            $settings['product_update_gdpr_policy_url'][$lang['id_lang']] = '';
        }
        //changes by vishal for adding related product functionality
        foreach ($languages as $lang) {
            $settings['initial_related_title'][$lang['id_lang']] = '';
        }
        foreach ($languages as $lang) {
            $settings['final_related_title'][$lang['id_lang']] = '';
        }
        foreach ($languages as $lang) {
            $settings['low_stock_related_title'][$lang['id_lang']] = '';
        }
        //changes end
        $settings = array(
            'adv_id' => 0,
            'plugin_id' => 'PS0009',
            'version' => '0.1',
            'enable' => 0,
            'enable_cron' => 0,
            'enable_quantity' => 0,
            'update_subscribers' => 0,
            'display_hook' => 0,
            'enable_related_product_initial' => 0,
            'enable_related_product_final' => 0,
            'enable_related_product_low_stock' => 0,
            'related_product_method_initial' => 1,
            'related_product_method_final' => 1,
            'related_product_method_low_stock' => 1,
            'enable_utm' => 0,
            'specific_products_final' => "",
            'specific_products_initial' => "",
            'specific_products_low_stock' => "",
            'enable_subscription_list' => 0,
            'enable_low_stock_alert' => 0,
            'enable_remove_subscription' => 0,
            'subscription_per_page' => 10,
            'low_stock_alert_quantity' => 5,
            'enable_gdpr_policy' => 0,
            'enable_gdpr_delete' => 0,
            'delay' => '1',
            'css' => '',
            'js' => '',
            'text' => '#000000',
            //by dharmanshu for default color black
            'border' => '#dfdede',
            'background' => '#ffffff',
            'notify_text' => '#000',
            'notify_background' => '#ffd630',
            'notify_border' => '#f5be18',
            'background_heading' => '#3a99d7',
            'product_update_utm_source' => '',
            'product_update_utm_medium' => '',
            'product_update_utm_campaign' => '',
            'KB_BACKINSTOCK_RECAPTCHA_ENABLE' => 0,
            'KB_BACKINSTOCK_RECAPTCHA_SITE_KEY' => '',
            'KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY' => ''
        );
        return $settings;
    }

    protected function getDefaultAvalSettings()
    {
        $settings = array();
        $settings = array(
            'product_name' => '',
            'excluded_products_hidden' => '',
            'enable_availability_settings' => 0,
            'prestashop_category' => array(),
        );
        return $settings;
    }

    private function getDefaultMailTemplate($id)
    {
        $email_template = array();
        $email_template['id_template'] = 0;
        $email_template['id_lang'] = $this->context->language->iso_code;
        $email_template['shop_id'] = $this->context->shop->id;
        $email_template['iso_code'] = $this->context->language->id;
        if ($id == 1) {
            $email_template['subject'] = $this->l('Product alert added successfully');
            $email_template['body'] = $this->display(__FILE__, 'views/templates/admin/initial_mail.tpl');
            $email_template['body'] = str_replace('[', '{', $email_template['body']);
            $email_template['body'] = str_replace(']', '}', $email_template['body']);
        } else if ($id == 3) {
            $email_template['subject'] = $this->l('Hurry!!! Subscribed product is low in stock');
            $email_template['body'] = $this->display(__FILE__, 'views/templates/admin/low_stock_mail.tpl');
            $email_template['body'] = str_replace('[', '{', $email_template['body']);
            $email_template['body'] = str_replace(']', '}', $email_template['body']);
        } else {
            $email_template['subject'] = $this->l('Good news!!! Product is back in stock');
            $email_template['body'] = $this->display(__FILE__, 'views/templates/admin/final_mail.tpl');
            $email_template['body'] = str_replace('[', '{', $email_template['body']);
            $email_template['body'] = str_replace(']', '}', $email_template['body']);
        }
        return $email_template;
    }

    public function getform($field_form, $languages, $field_value, $id, $action)
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $field_value;
        $helper->name_controller = $this->name;
        $helper->languages = $languages;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->default_form_language = $this->context->language->id;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $this->displayName;
        if ($id == 'general') {
            $helper->show_toolbar = true;
        } else {
            $helper->show_toolbar = false;
        }
        $helper->table = $id;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = $action;
        return $helper->generateForm(
            array(
                'form' => $field_form
            )
        );
    }

    public function getBestSeller($product_id)
    {
        $bestseller = ProductSale::getBestSales($this->context->language->id, 0, 10, null, null);
        if ($bestseller) {
            $seller = array();
            if (is_array($bestseller)) {
                foreach ($bestseller as $best) {
                    if ($best['id_product'] != $product_id) {
                        //changes by vishal for adding UTM functionality
                        $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
                        if (isset($data['enable_utm']) && $data['enable_utm'] == 1) {
                            $utm_paramters = 'utm_source=' . $data['product_update_utm_source'] . '&utm_medium=' . $data['product_update_utm_medium'] . '&utm_campaign=' . $data['product_update_utm_campaign'];
                            if (strpos($best['link'], '?') !== false) {
                                $best['link'] .= '&' . $utm_paramters;
                            } else {
                                $best['link'] .= '?' . $utm_paramters;
                            }
                        }
                        //chanegs end
                        $seller[] = $best;
                    }
                }
            }
            $bestseller1 = array_slice($seller, 0, 2);
            $bestseller2 = array_slice($seller, 2, 2);
            $currency = new Currency($this->context->currency->id);

            $this->context->smarty->assign('id_product', $product_id);
            $this->context->smarty->assign('best_seller_1', $bestseller1);
            $this->context->smarty->assign('link', $this->context->link);
            $this->context->smarty->assign('best_seller_2', $bestseller2);
            $this->context->smarty->assign('currency_sign', $currency->sign);
            if (count($bestseller1) > 0) {
                return $this->display(__FILE__, 'views/templates/admin/most_viewed_product.tpl');
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /*
     * @author - Rishabh Jain
     * DOC - 30/02/20
     * Function to add the subscription list menu item in customer account menu in front
     */
    public function hookDisplayCustomerAccount($params)
    {
        $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        if ($data['enable'] == 1) {
            $subscription_list_link = $this->context->link->getModuleLink(
                'backinstock',
                'subscription',
                array(),
                (bool) Configuration::get('PS_SSL_ENABLED')
            );
            $is_subscription_page = 0;
            if (isset($data['enable_subscription_list']) && $data['enable_subscription_list'] == 1) {
                $is_subscription_page = 1;
            }
            $query = "SELECT * FROM " . _DB_PREFIX_ . "product_update_product_detail where customer_id = " . (int) $this->context->customer->id . " or email = '" . $this->context->customer->email . "'";
            $result = Db::getInstance()->executeS($query);
            if (count($result) > 0 && $is_subscription_page == 1) {
                $this->context->smarty->assign('subscription_list_link', $subscription_list_link);
                $this->context->smarty->assign('is_subscription_page', $is_subscription_page);
                return $this->display(__FILE__, 'views/templates/hook/account_menu.tpl');
            }
        }
    }

    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;

        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }

    /*
     * Get Mailchimp List from subscribed user list
     *
     * @param    string start   API key for Mailchimp Integration
     */

    public function mailchimpGetLists($api_key = null)
    {
        if (trim($api_key) != '' && $api_key !== null) {
            try {
                $Mailchimp = new Mailchimp($api_key);
                $lists = $Mailchimp->get('lists');
                if ($lists !== false) {
                    $options = array();
                    foreach ($lists["lists"] as $list) {
                        $options["success"][] = array(
                            'value' => $list["id"],
                            'label' => $list["name"]
                        );
                    }
                } else {
                    $options["error"][] = array(
                        'value' => "0",
                        'label' => $this->l("No list found. (Verify Credentials)")
                    );
                }
            } catch (\Exception $e) {
                $options["error"][] = array(
                    'value' => "0",
                    'label' => $e->getMessage()
                );
            }
        } else {
            $options["error"][] = array(
                'value' => "0",
                'label' => $this->l("No list found. (Verify Credentials)")
            );
        }

        return $options;
    }

    /*
     * Get SendinBlue List from subscribed user list
     * @date 04-02-2023
     * @author
     * @commenter Kanishka Kannoujia
     *
     * @param    string start   API key for Mailchimp Integration
     * @return array
     */
    public function getSendinblueList($apikey = null)
    {
        $response = array(); //defining array to store response
        if (trim($apikey) != '' && $apikey !== null) {
            $mailin = new KbSpinMailin('https://api.sendinblue.com/v2.0', $apikey);

            $folder = $mailin->get_folder(1)['data']; // it'll be modified later as get_lists() is not working to get all list

            foreach ($folder as $value) {
                $response[] = $value['lists'];
            }

            if (empty($response)) {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/lists?limit=10&offset=0&sort=desc",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "accept: application/json",
                        "api-key: " . $apikey . "",
                    ),
                )
                );

                $result_data = curl_exec($curl);
                $result_data = json_decode($result_data, true);
                if (isset($result_data['lists'])) {
                    unset($result_data['count']);
                    $folder = $result_data;

                    foreach ($folder as $k => $value) {
                        $response[] = $value;
                    }
                }
                curl_close($curl);
            }
        }
        return $response;
    }

    /*
     * Get Klaviyo List from subscribed user list
     *
     * @param    string $api_key   API key for Klaviyo API
     */

    public function klaviyoGetLists($api_key = null)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://a.klaviyo.com/api/v1/lists?api_key=' . $api_key);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = json_decode(curl_exec($ch));
        curl_close($ch);

        if (property_exists($output, 'status')) {
            $status = $output->status;
            if ($status === 403) {
                $reason = $this->l('The Private Klaviyo API Key you have set is invalid.');
            } elseif ($status === 401) {
                $reason = $this->l('The Private Klaviyo API key you have set is no longer valid.');
            } else {
                $reason = $this->l('Unable to verify Klaviyo Private API Key.');
            }

            $result = array(
                'success' => false,
                'reason' => $reason
            );
        } else {
            $static_groups = array_filter($output->data, function ($list) {
                return $list->list_type === 'list';
            });

            usort($static_groups, function ($a, $b) {
                return Tools::strtolower($a->name) > Tools::strtolower($b->name) ? 1 : -1;
            });

            $result = array(
                'success' => true,
                'lists' => $static_groups
            );
        }
        $options = array();
        if (!$result["success"]) {
            $options["error"][] = array(
                'value' => "0",
                'label' => $result["reason"]
            );
        } else {
            if (!empty($result["lists"])) {
                foreach ($result["lists"] as $list) {
                    $options["success"][] = array(
                        'value' => $list->id,
                        'label' => $list->name
                    );
                }
            } else {
                $options["error"][] = array(
                    'value' => "0",
                    'label' => $this->l("No list found. (Verify Credentials)")
                );
            }
        }

        return $options;
    }

    /*
     * @author - Rishabh Jain
     * DOC - 30/02/20
     * Function to set the order value if any subscribed customer has placed the orderr
     * and to send the low stock alert mails
     */
    public function hookActionValidateOrder($params)
    {
        $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));
        if ($data['enable'] == 1) {
            $is_low_stock_alert_enabled = $data['enable_low_stock_alert'];
            $low_stock_alert_quantity = $data['low_stock_alert_quantity'];
            $order_obj = $params['order'];
            $id_customer = $order_obj->id_customer;
            $customer_obj = new Customer($id_customer);
            $customer_email = $customer_obj->email;
            $order_product_detail = $order_obj->getProducts();
            if ($order_product_detail && is_array($order_product_detail) && count($order_product_detail) > 0) {
                foreach ($order_product_detail as $detail) {
                    $id_product = $detail['product_id'];
                    $id_product_attribute = $detail['product_attribute_id'];

                    /*
                     * Below sql is to set the order column to 1 if any subscribed customer has ordered
                     */
                    $is_any_subscribed_customer = 'Select id '
                        . 'from ' . _DB_PREFIX_ . 'product_update_product_detail'
                        . ' WHERE email = "' . psql($customer_email) . '"'
                        . ' and product_id = ' . (int) $id_product
                        . ' and product_attribute_id = ' . (int) $id_product_attribute
                        . ' and send = "1" and `order`=0';
                    $id_subscribed_customer = Db::getInstance()->getValue($is_any_subscribed_customer);
                    if ($id_subscribed_customer) {
                        $is_ordered = 1;
                        $sql_mark_ordered = 'UPDATE `' . _DB_PREFIX_ . 'product_update_product_detail`
                            SET `order`=' . (int) $order_obj->id
                            . ' where `id`=' . (int) $id_subscribed_customer;
                        Db::getInstance()->execute($sql_mark_ordered);
                    }
                    /*
                     * changes over
                     */
                    /*
                     * Below code is to send the low stock alert if enabled
                     */
                    if ($is_low_stock_alert_enabled) {
                        $available_quantity = StockAvailable::getQuantityAvailableByProduct(
                            $id_product,
                            $id_product_attribute,
                            $this->context->shop->id
                        );
                        if ($available_quantity <= $low_stock_alert_quantity) {
                            $this->sendLowStockAlertEmail($id_product, $id_product_attribute);
                        }
                    }
                }
            }
        }
    }

    public function hookActionUpdateQuantity($params = array())
    {
        $id_product = $params['id_product'];

        $data = Tools::unSerialize(Configuration::get('VELOCITY_PRODUCT_UPDATE'));


        if ($data['enable'] == 1) {
            /*
             * @author - Rishabh Jain
             * DOC - 24/02/20
             * To send the low stock alert emails on product update
             */
            $is_low_stock_alert_enabled = $data['enable_low_stock_alert'];
            $low_stock_alert_quantity = $data['low_stock_alert_quantity'];
            if ($is_low_stock_alert_enabled) {
                $get_data = 'select distinct(product_attribute_id) from ' . _DB_PREFIX_ . 'product_update_product_detail a 
                    where active=1 and send="1" and low_stock_mail=0 and `order`=0 and product_id=' . (int) $id_product . ' and store_id=' . (int) $this->context->shop->id;
                $user_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_data);
                foreach ($user_data as $user_data_key => $subscriber_data) {
                    $available_quantity = StockAvailable::getQuantityAvailableByProduct(
                        $id_product,
                        $subscriber_data['product_attribute_id'],
                        $this->context->shop->id
                    );
                    if ($available_quantity <= $low_stock_alert_quantity && $available_quantity != 0) {
                        $this->sendLowStockAlertEmail($id_product, $subscriber_data['product_attribute_id']);
                    }
                }
            }
            /*
             * Changes over
             */

            $this->sendEmails($id_product);
        }
    }

    /*
     * Function defined by kanishka on 01-03-2023 to remove mails folder from the themes
     * KKFeb2023 FunctionDeleteMail Directory
     * @date 01-03-2023
     * @author Kanishka Kannoujia
     * @commenter Kanishka Kannoujia
     */
    public static function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
        rmdir(_PS_THEME_DIR_ . 'modules/backinstock');
    }
}