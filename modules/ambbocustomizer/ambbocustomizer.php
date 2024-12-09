<?php
/**
 *   ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      ambbocustomizer.php
 *    @subject   main module file (install/config/hook)
 *
 *            Support by mail: support@ambris.com
 */

if (!class_exists('AmbModule')) {
    require_once 'classes/AmbModule.php';
}

require 'classes/AmbBackCaller.php';
require 'classes/AmbBackCallerExport.php';

if (file_exists(_PS_MODULE_DIR_ . 'ambbocustomizer/classes/AmbBackCallerCustom.php')) {
    require 'classes/AmbBackCallerCustom.php';
}

require 'classes/AmbLists.php';
require 'classes/AmbData.php';

class AmbBoCustomizer extends AmbModule
{
    private $hookList;
    public static $counter = 0;

    public function __construct()
    {
        $this->name = 'ambbocustomizer';

        $this->tab = 'administration';
        $this->version = '1.3.5';
        $this->author = 'Ambris Informatique';
        $this->module_key = '1e080e54834257797c28d0e3c9d25fae';
        //AmbData::generateTranslatables(); //A COMMENTER POUR LA LIVRAISON

        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        parent::__construct();

        $this->displayName = $this->l('BO Customizer : Customize your back-office');
        $this->description = $this->l('Customize the prestashop back-office lists');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->data_dirs['fields'] = _PS_MODULE_DIR_ . $this->name . '/data/fields';
    }

    public function install()
    {
        //$array_lang = array(1 => $this->l('Customize lists'), 2 => $this->l('Customize lists'));

        $languages = Language::getLanguages();
        $array_lang1 = array();
        $array_lang2 = array();

        foreach ($languages as $language) {
            if (isset($language['id'])) {
                $id = $language['id'];
            } elseif (isset($language['id_lang'])) {
                $id = $language['id_lang'];
            }

            $array_lang1[$id] = $this->l('Customize lists');
            $array_lang2[$id] = 'AmbBoCustomizerAjax';
        };

        if (!parent::install()
            || !$this->registerListingHooks()
            || !$this->registerHook('displayAdminListBefore')
            || !$this->installModuleTab(
                'AdminAmbBoCustomizerAjax',
                $array_lang2,
                -1
            )
            || !$this->installModuleTab(
                'AdminAmbBoCustomizerParams',
                $array_lang1,
                Tab::getIdFromClassName('AdminAdmin')
            )
        ) {
            return false;
        }

        return true;
    }

    public function installOverrides()
    {
        if (version_compare(_PS_VERSION_, 1.6, '<')) {
            return parent::installOverrides();
        } else {
            return true;
        }
    }

    public function getContent()
    {
        parent::getContent();
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminAmbBoCustomizerParams'));
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallModuleTab('AdminAmbBoCustomizerAjax')
            || !$this->uninstallModuleTab('AdminAmbBoCustomizerParams')
        ) {
            return false;
        }

        return true;
    }

    private function registerListingHooks()
    {
        if ($this->id != null) {
            $files = scandir(_PS_MODULE_DIR_ . $this->name . '/data/fields');
            foreach ($files as $file) {
                if (preg_match('/(.+)\.json/', $file, $matches)) {
                    $controller_name = $matches[1];
                    $hook_name = 'Action' . $controller_name . 'ListingFieldsModifier';
                    $id_hook = Hook::getIdByName($hook_name);

                    new AmbData($controller_name);

                    if (!$id_hook || count(Hook::getModulesFromHook($id_hook, $this->id)) == 0) {
                        $this->registerHook($hook_name);
                        $id_hook = Hook::getIdByName($hook_name);
                    }

                    $this->updatePosition($id_hook, 0, 1);

                    //Add hooks on ListingResults for logging
                    $hook_name = 'Action' . $controller_name . 'ListingResultsModifier';
                    $id_hook = Hook::getIdByName($hook_name);
                    if (!$id_hook || count(Hook::getModulesFromHook($id_hook, $this->id)) == 0) {
                        $this->registerHook($hook_name);
                    }
                }
            }
        }

        return true;
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if (method_exists($this->context->controller, 'addCSS')) {
            $this->context->controller->addCSS($this->_path . 'views/css/backoffice.css');
            if ($this->compat) {
                $this->context->controller->addCSS($this->_path . 'views/css/compat.css');
            }
        }

        if ($this->compat && method_exists($this->context->controller, 'addJS')) {
            $this->context->controller->addJS($this->_path . 'views/js/jquery/jquery-1.11.2.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/bootstrap.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/jquery/jquery-fix-compatibility.js');
            self::$jquery_loaded = true;
        }

        parent::hookDisplayBackOfficeHeader($params);
    }

    public function hookDisplayBackOfficeFooter($params)
    {
        $controller = Tools::getValue('controller', null);
        $loadJS = false;
        if ($controller != null) {
            $fields = new AmbData(Tools::getValue('controller'));
            $loadJS = ($fields->isActive() || in_array(Tools::strtolower($controller), array('adminambbocustomizerparams')));
        }

        $content = "";
        if ($loadJS) {
            $content .= '<script type="text/javascript" src="' . $this->_path . 'views/js/ajax_calls.js"></script>';
            $content .= '<script type="text/javascript" src="' . $this->_path . 'views/js/params.js"></script>';
            $content .= '<script type="text/javascript" src="' . _PS_JS_DIR_
                . 'jquery/plugins/jquery.tablednd.js"></script>';
            $content .= '<script type="text/javascript">
                            var come_from = "fields";
                            var alternate = "0";
                            currentIndex += "&name=' . Tools::getValue('name') . '";
                        </script>';
            if (!$this->compat) {
                $content .= '<script type="text/javascript" src="' . _PS_JS_DIR_ . 'admin/dnd.js"></script>';
            } else {
                $content .= '<script type="text/javascript" src="' . $this->_path . 'views/js/dnd.js"></script>';
            }
        }

        $content .= parent::hookDisplayBackofficeFooter($params);
        return $content;
    }

    //Initialization of required hooks. Should match all the filenames of data/fields
    //All hooks call the hookCommon method
    public function hookActionAdminCartsListingFieldsModifier($params)
    {
        $this->hookCommon($params, __METHOD__);
    }
    public function hookActionAdminCategoriesListingFieldsModifier($params)
    {
        $this->hookCommon($params, __METHOD__);
    }
    public function hookActionAdminCustomersListingFieldsModifier($params)
    {
        $this->hookCommon($params, __METHOD__);
    }
    public function hookActionAdminCustomerThreadsListingFieldsModifier($params)
    {
        $this->hookCommon($params, __METHOD__);
    }
    public function hookActionAdminOrdersListingFieldsModifier($params)
    {
        $this->hookCommon($params, __METHOD__);
    }
    public function hookActionAdminProductsListingFieldsModifier($params)
    {
        $this->hookCommon($params, __METHOD__);
    }
    public function hookActionAdminReturnListingFieldsModifier($params)
    {
        $this->hookCommon($params, __METHOD__);
    }

    //Initialization of hooks used for logging the mysql request
    public function hookActionAdminCartsListingResultsModifier($params)
    {
        $this->hookLogger($params);
    }
    public function hookActionAdminCategoriesListingResultsModifier($params)
    {
        $this->hookLogger($params);
    }
    public function hookActionAdminCustomersListingResultsModifier($params)
    {
        $this->hookLogger($params);
    }
    public function hookActionAdminCustomerThreadsListingResultsModifier($params)
    {
        $this->hookLogger($params);
    }
    public function hookActionAdminOrdersListingResultsModifier($params)
    {
        $this->hookLogger($params);
    }
    public function hookActionAdminProductsListingResultsModifier($params)
    {
        $this->hookLogger($params);
    }
    public function hookActionAdminReturnListingResultsModifier($params)
    {
        $this->hookLogger($params);
    }

    public function hookDisplayAdminListBefore($params)
    {
        if (method_exists($this, 'hookAction' . Tools::getValue('controller') . 'ListingFieldsModifier')) {
            $fields = new AmbData(Tools::getValue('controller'));

            if ($fields->isActive()) {
                $this->context->smarty->assign('current_view_name', $fields->getViewName());
                $this->context->smarty->assign('view_names', $fields->getViewNames());
                $this->context->smarty->assign(
                    'url',
                    $this->context->link->getAdminLink($fields->controller_name)
                );

                $this->context->smarty->assign('compat', $this->compat);
                if ($this->compat) {
                    $this->context->smarty->assign('edit_list_url', $this->context->link->getAdminLink('AdminAmbBoCustomizerParams') . '&name=' . $fields->controller_name);
                }

                return $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . 'ambbocustomizer/views/templates/admin/_view_selector.tpl'
                );
            }
        }
    }

    private function hookLogger($params)
    {
        if ($this->debug) {
            $reflectedController = new ReflectionClass('AdminController');
            $reflectedValue = $reflectedController->getProperty('_listsql');
            $reflectedValue->setAccessible(true);
            $listsql = $reflectedValue->getValue($this->context->controller);

            $this->log($listsql, __FILE__, __METHOD__, __LINE__, "Logs SQL request generated by AdminController");
        }
    }

    private function hookCommon($params, $caller)
    {

        if (!array_key_exists('select', $params)) {
            return false;
        }

        $reflectedController = new ReflectionClass('AdminController');
        $reflectedValue = $reflectedController->getProperty('toolbar_btn');
        $reflectedValue->setAccessible(true);
        $toolbar_btns = $reflectedValue->getValue($this->context->controller);

        $toolbar_btns['settings'] = array(
            'href' => $this->context->link->getAdminLink('AdminAmbBoCustomizerParams')
            . '&name=' . Tools::getValue('controller'),
            'desc' => $this->l('Customize list'),
            'imgclass' => 'configure',
        );

        $reflectedValue->setValue($this->context->controller, $toolbar_btns);

        //Does not work in PS 1.5
        if (!$this->compat) {
            $this->context->controller->initPageHeaderToolbar();
        }

        //error_log(print_r($reflectedController, true));

        $reflectedValue = $reflectedController->getProperty('action');
        $reflectedValue->setAccessible(true);
        $action = $reflectedValue->getValue($this->context->controller);

        $is_export = $action == 'export' ? true : false;

        //Set use_found_rows to true in adminController
        if ($reflectedController->hasProperty('_use_found_rows')) {
            $reflectedValue = $reflectedController->getProperty('_use_found_rows');
            $reflectedValue->setAccessible(true);
            $reflectedValue->setValue($this->context->controller, true);
        }

        //Checking the method caller in the backtrace...
        //... in order to extract the admin_controller we are using
        // RS : WHAT !!!!!
        // RS : NO NO NO ! Use the magic constant __METHOD__ as a parameter from the caller and it works nicer :-)
        $admin_controller_name = $this->getAdminControllerNameFromMethod($caller);
        if (!$admin_controller_name) {
            return false;
        }

        $fields = new AmbData($admin_controller_name);
        $fields_from_data = $fields->getConstrainedFields();

        //die(print_r($this->context->cookie));

        if (($this->compat && $fields->viewChanged()) || $this->context->cookie->{'resetFilterFor' . $fields->getName()}) {
            $this->context->controller->processResetFilters();
            unset($this->context->cookie->{'resetFilterFor' . $fields->getName()});
        }

        if (count($fields_from_data) > 0 && $fields->isActive()) {
            $to_replace = array(
                '$PREFIX_',
                '$LANGUAGE',
                '$SHARE_ORDER_SHOP',
                '$SHARE_CUSTOMER_SHOP',
                '$SHARE_STOCK_SHOP',
                '$SHOP_CONTEXT',
            );
            $replacements = array(
                _DB_PREFIX_,
                $this->context->language->id,
                $this->getShopRestriction(Shop::SHARE_ORDER),
                $this->getShopRestriction(Shop::SHARE_CUSTOMER),
                $this->getShopRestriction(Shop::SHARE_STOCK),
                $this->getShopRestriction(false),
            );

            //Generate and replace the fields for the current controller
            $list = $this->buildList($fields_from_data, $params['fields'], $is_export);
            $params['fields'] = $list['fields'];
            $params['join'] .= implode(' ', str_replace($to_replace, $replacements, $list['join']));

            $params['select'] .= (Tools::substr(trim($params['select']), -1) != ',' && count($list['select']) > 0 ? ', ' : ' ')
            . implode(',', str_replace($to_replace, $replacements, $list['select']));

            if (count($list['group_by']) > 0) {
                $params['group_by'] = 'GROUP BY '
                . implode(',', str_replace($to_replace, $replacements, $list['group_by']));
            }
            //die(print_r($this->context->cookie));
        } else {
            return false;
        }

        $reflectedValue = $reflectedController->getProperty('filter');
        $reflectedValue->setAccessible(true);
        $filter_on = $reflectedValue->getValue($this->context->controller);
        if ($filter_on) {
            $this->context->controller->processFilter();
        }

        if ($fields->isActive()) {
            $list_id = $reflectedController->getProperty('list_id')->getValue($this->context->controller);
            $prefix = str_replace(array('admin', 'controller'), '', Tools::strtolower(get_class($this->context->controller)));
            if ($this->context->cookie->{$prefix . $list_id . 'Orderby'}) {
                if (!in_array($this->context->cookie->{$prefix . $list_id . 'Orderby'}, array_keys($fields_from_data))) {
                    $this->context->cookie->{$prefix . $list_id . 'Orderby'} = false;
                }
            }
        }
        return true;
    }

    private function getShopRestriction($share)
    {
        return 'id_shop IN (' . implode(', ', Shop::getContextListShopID($share)) . ')';
    }

    private function getAdminControllerNameFromMethod($method)
    {
        return preg_match('/hookAction(.+)ListingFieldsModifier/', $method, $matches) ? $matches[1] : false;
    }

    private function buildList($fields, $fields_from_params, $is_export = false)
    {

        $list = array(
            'fields' => array(),
            'join' => array(),
            'select' => array(),
            'where' => array(),
            'group_by' => array(),
        );

        $joined_tables = array();

        $possible_options = array(
            array('name' => 'title', 'translate' => true),
            array('name' => 'align'),
            array('name' => 'type'),
            array('name' => 'class'),
            array('name' => 'tmpTableFilter'),
            array('name' => 'orderby'),
            array('name' => 'havingFilter'),
            array('name' => 'badge_success'),
            array('name' => 'currency'),
            array('name' => 'callback'),
            array('name' => 'callback_object'),
            array('name' => 'type'),
            array('name' => 'active'),
            array('name' => 'color'),
            array('name' => 'list'),
            array('name' => 'icon'),
            array('name' => 'filter_key'),
            array('name' => 'filter_type'),
            array('name' => 'search'),
            array('name' => 'remove_onclick'),
        );

        //die(print_r($renamed_fields));

        foreach ($fields as $key => $field) {
            $is_core = property_exists($field, 'is_core') ? $field->is_core : false;

            if (property_exists($field, 'query')) {
                if (property_exists($field->query, 'select')) {
                    $select = is_array($field->query->select) ?
                    implode(', ', $field->query->select) : $field->query->select;
                    $list['select'][] = $select;
                }

                if (property_exists($field->query, 'joins')) {
                    if (!is_array($field->query->joins)) {
                        $field->query->joins = array($field->query->joins);
                    }

                    foreach ($field->query->joins as $join) {
                        if (!in_array($join->alias, $joined_tables)) {
                            $joined_tables[] = $join->alias; //Prevents multiple joins on the same table

                            if (property_exists($join, 'table')) {
                                $list['join'][] = $join->type . ' JOIN ' . _DB_PREFIX_ . $join->table
                                . ' ' . $join->alias . ' ON ' . $join->on;
                            } elseif (property_exists($join, 'select')) {
                                $list['join'][] = $join->type . ' JOIN ' . $join->select
                                . ' ' . $join->alias . ' ON ' . $join->on;
                            }
                        }
                    }
                }
                if (property_exists($field->query, 'group_by')) {
                    $group_by = is_array($field->query->group_by) ?
                    implode(', ', $field->query->group_by) : $field->query->group_by;
                    $list['group_by'][] = $group_by;
                }
            }

            if (property_exists($field, 'amb_callback')) {
                if (property_exists($field->amb_callback, 'object') && class_exists($field->amb_callback->object)) {
                    $field->callback_object = $field->amb_callback->object;
                    $field->callback = $field->amb_callback->method;
                } else {
                    if ($is_export) {
                        if (method_exists("AmbBackCallerExport", $field->amb_callback->method)) {
                            $field->callback_object = "AmbBackCallerExport";
                            $field->callback = $field->amb_callback->method;
                        } elseif (property_exists($field->amb_callback, 'export')) {
                            $field->callback_object = "AmbBackCallerExport";
                            if ($field->amb_callback->export == "value") {
                                $field->callback = "simpleValue";
                            } elseif ($field->amb_callback->export == "skip") {
                                continue;
                            } else {
                                $field->callback = "errorUnknownExportParam";
                            }
                        } else {
                            $field->callback_object = "AmbBackCaller";
                            $field->callback = $field->amb_callback->method;
                        }
                    } else {
                        $field->callback_object = "AmbBackCaller";
                        $field->callback = $field->amb_callback->method;
                    }
                }

                $has_value = false;
                if (property_exists($field->amb_callback, 'args')) {
                    foreach ($field->amb_callback->args as $arg_name => $arg) {
                        if ($arg_name == 'value') {
                            $has_value = true;
                            $list['select'][] = "CONCAT('" . $key . "', '" . AmbBackCaller::$separator . "', "
                                . $arg . ") as " . $key;
                        } else {
                            $list['select'][] = $arg . " as " . $key . "_" . $arg_name;
                        }
                    }
                }

                if (property_exists($field, 'filter_value')) {
                    $list['select'][] = "CONCAT('" . $key . "', '" . AmbBackCaller::$separator . "', "
                    . $field->filter_value . ") as " . $key;
                } elseif (!property_exists($field, 'quick_select') && !$has_value) {
                    $list['select'][] = "'" . $key . "' as " . $key;
                }

                /*elseif ($has_value === true) {
            $list['select'][] = "CONCAT('" . $key . "', '" . AmbBackCaller::$separator . "', "
            . $field->amb_callback->args->value . ") as " . $key;
            }*/
            }

            if (property_exists($field, 'quick_select')) {
                $list['select'][] = $field->quick_select . ' as ' . $key;
            }

            if (property_exists($field, 'list')) {
                $field->list = AmbLists::getList($field->list);
            }

            if (property_exists($field, 'icon')) {
                $field->icon = AmbLists::getList($field->icon);
            }

            if ($is_core) {
                if (array_key_exists($key, $fields_from_params)) {
                    $list['fields'][$key] = $fields_from_params[$key];
                }
            } else {
                $list['fields'][$key] = array();
            }

            if ($is_export) {
                if (isset($list['fields'][$key]['type']) && $list['fields'][$key]['type'] == 'price') {
                    $list['fields'][$key]['type'] = '';
                    $list['fields'][$key]['currency'] = false;
                    unset($list['fields'][$key]['callback']);
                }
            }

            if (array_key_exists($key, $list['fields'])) {
                foreach ($possible_options as $option) {
                    if (property_exists($field, $option['name']) && !($option['name'] == 'title' && $is_core)) {
                        if (isset($option['translate']) && $option['translate']) {
                            if (property_exists($field, 'translator')) {
                                $list['fields'][$key][$option['name']] = Translate::getAdminTranslation(
                                    $field->{$option['name']},
                                    $field->translator
                                );
                            } else {
                                $list['fields'][$key][$option['name']] = $this->l($field->{$option['name']});
                            }
                        } else {
                            $list['fields'][$key][$option['name']] = $field->{$option['name']};
                        }
                    }
                }
            }
        }

        //Return list = array('fields' => array(...), 'join' => array(...))
        //error_log(print_r($list, true));
        return $list;
    }

    public function declareTranslatables()
    {
        #MANUAL DECLARATIONS

        //AmbCustomizerAjaxControllers.php
        $this->l('No products in this order');
        $this->l('No returned products in this order');
        $this->l('No payments in this order');
        $this->l('No vouchers available for this order');
        $this->l('No combinations for this products');
        $this->l('No features for this product');
        $this->l('No suppliers for this product');
        $this->l('This product is not attached to any warehouse');
        $this->l('No orders for this customer');
        $this->l('No carts for this customer');
        $this->l('This customer is not attached to any group');
        $this->l('No products in this cart');
        $this->l('No groups attached to this category');
        $this->l('No returned products');
        $this->l('No orders for this customer');
        $this->l('No ordered carts for this customer');
        $this->l('No abandoned carts for this customer');
        $this->l('No vouchers for this customer');
        $this->l('Returned products');
        $this->l('No specific prices for this products');
        $this->l('unlimited');
        $this->l('undefined');

        #GENERATED DECLARATIONS

        //AdminCarts.json
        $this->l('Cart identifier');
        $this->l('ID of the related order');
        $this->l('Customer name');
        $this->l('Total tax included of the cart');
        $this->l('Carrier for this cart');
        $this->l('Date of creation of the cart');
        $this->l('Indicates whether the customer is still online or not');
        $this->l('List of the products contained in the cart');
        $this->l('Link to the related order');
        $this->l('Link to create an order from the cart');
        $this->l('Link to the customer profile');

        //AdminCategories.json
        $this->l('Category identifier');
        $this->l('Category name');
        $this->l('Category description');
        $this->l('Category position');
        $this->l('Indicates if the category is displayed on the front-office');
        $this->l('Category image');
        $this->l('Meta title of the category');
        $this->l('Meta description of the category');
        $this->l('Meta tags of the category');
        $this->l('Friendly URL of the category');
        $this->l('Indicates the different groups this category is displayed for');
        $this->l('Indicates the number of products attached to this category');
        $this->l('Link to products list filtered on the category');

        //AdminCustomerThreads.json
        $this->l('Customer thread identifier');
        $this->l('Customer name');
        $this->l('Email address of the customer');
        $this->l('Type of support');
        $this->l('Language of the customer');
        $this->l('Current status of the thread');
        $this->l('Employee responding to the thread');
        $this->l('Extract of the last message of the thread');
        $this->l('Date of the last message');
        $this->l('First message');
        $this->l('Date of the first message');
        $this->l('Customer name with link to the customer page');
        $this->l('Order ID with link to the order page');
        $this->l('Number of messages');
        $this->l('The number of messages in the customer service thread');
        $this->l('Show last');
        $this->l('Show the last message of the customer');

        //AdminCustomers.json
        $this->l('Customer identifier');
        $this->l('Social title of the customer');
        $this->l('First name of the customer');
        $this->l('Last name of the customer');
        $this->l('Email address of the customer');
        $this->l('Company of the customer (only if B2B is activated)');
        $this->l('Total sales tax included for this customer');
        $this->l('Indicates if the customer is active');
        $this->l('Indicates if the customer subscribed to the newsletter');
        $this->l('Indicates if the customer has allowed the transmission of its email to third parties');
        $this->l('Date of registration of the customer');
        $this->l('Date of last visit of the customer');
        $this->l('Show the default billing address for the customer');
        $this->l('Shows a list of the orders made by the customer');
        $this->l('List of all the carts of the customer');
        $this->l('Ordered cart');
        $this->l('List of the carts that were ordered by the customer');
        $this->l('List of the carts that were abandoned by the customer');
        $this->l('Customer birthday');
        $this->l('Customer age');
        $this->l('Main language of the customer');
        $this->l('List of the groups the customer belongs to');
        $this->l('Shows the private customer note');
        $this->l('Last order');
        $this->l('Show the date of the last order of the customer');
        $this->l('Products (Tax excl.)');
        $this->l('Shows the amount of products purchased by the customer');
        $this->l('List of discounts the customer has been granted');
        $this->l('Current thread');
        $this->l('Link to the current customer service thread');
        $this->l('Orders score');
        $this->l('Score based on the number of orders the customer made compared to the average number of orders');
        $this->l('Frequency score');
        $this->l('Score based on the purchase frequency of the customer compared to the average frequency');
        $this->l('Amount score');
        $this->l('Score based on the amounts purchased by the customer compared to the average amount');
        $this->l('Global score');
        $this->l('Score based on a blend of the three previous scores');
        $this->l('Phone(s)');
        $this->l('Display phone number(s) of the customer');

        //AdminOrders.json
        $this->l('Order identifier');
        $this->l('Order reference');
        $this->l('Indicates if the order has been made by a new client');
        $this->l('Company of the customer (only with B2B mode activated)');
        $this->l('Country where the order will be delivered');
        $this->l('Name of the customer');
        $this->l('Total of the order');
        $this->l('Total tax excluded of the order');
        $this->l('Total taxes for the order');
        $this->l('Products (tax excl.)');
        $this->l('Total for the products tax excluded');
        $this->l('Products (tax incl.)');
        $this->l('Total for the products tax included');
        $this->l('Total vouchers tax excluded');
        $this->l('Total vouchers (Tax incl.)');
        $this->l('Total vouches tax included');
        $this->l('Total shipping tax excluded');
        $this->l('Total shipping tax included');
        $this->l('The payment mode used for the order');
        $this->l('The current status of the order');
        $this->l('The date of creation of the order');
        $this->l('Buttons to print the invoice and/or the delivery slip');
        $this->l('Customer name with a link to their profile');
        $this->l('Email of the customer');
        $this->l('Delivery address for the order');
        $this->l('Delivery phone');
        $this->l('Phone number related to the delivery');
        $this->l('Invoice address for the order');
        $this->l('Invoice phone');
        $this->l('Phone number related to the invoice');
        $this->l('Invoice country');
        $this->l('Country where the invoice will be sent to');
        $this->l('Invoice city');
        $this->l('City where the invoice will be sent to');
        $this->l('Messages');
        $this->l('Number of messages exchanged with the customer regarding this order');
        $this->l('Thread status');
        $this->l('Current status of the customer service thread');
        $this->l('Shows the private customer note');
        $this->l('List of the products that have been ordered');
        $this->l('Payments');
        $this->l('List of payments for the order');
        $this->l('Returned products');
        $this->l('List of products that have been returned');
        $this->l('List of clickable tracking numbers related to the order');
        $this->l('List of the carriers in charge of the order');
        $this->l('Link to the cart that created the order');
        $this->l('List of the vouchers used in the order');
        $this->l('Order slip');
        $this->l('Links to the order slips related to the order');
        $this->l('Profit');
        $this->l('Profit made on the order, based on the difference between the tax excluded price and the wholesale price');
        $this->l('Profit margin');
        $this->l('Profit expressed in percentage compared to the total amount of products tax excluded');
        $this->l('Payment id');
        $this->l('Payment id for the order');
        $this->l('Transaction id');
        $this->l('Transaction id for the order');
        $this->l('Invoices');
        $this->l('List of invoice numbers related to this order');
        $this->l('Order slip');
        $this->l('List of order slips related to this order');
        $this->l('Delivery Number');
        $this->l('Delivery number related to this order');

        //AdminProducts.json
        $this->l('Product identifier');
        $this->l('Product image');
        $this->l('Product name');
        $this->l('Product reference');
        $this->l('Default shop the product belongs to');
        $this->l('Default category for the product');
        $this->l('Price of the product tax excluded');
        $this->l('Price of the product tax included');
        $this->l('List of the specific prices currently active for this product');
        $this->l('Number of products available');
        $this->l('Indicates if the product is active or not');
        $this->l('Manages the position of the product');
        $this->l('EAN-13 or JAN barcode for the product');
        $this->l('Product UPC');
        $this->l('Additionnal shipping costs for the product');
        $this->l('Wholesale price of the product');
        $this->l('Profit');
        $this->l('Profit per product sold, based on the difference between the price tax excluded and the wholesale price');
        $this->l('Profit %');
        $this->l('Profit expressed in percentage compared to the wholesale price');
        $this->l('Profit margin');
        $this->l('Profit expressed in percentage compared to the price tax excluded');
        $this->l('Multiplying factor');
        $this->l('Multiplying factor between the price and the wholesale price');
        $this->l('Indicates if the product is available for sale');
        $this->l('Online only');
        $this->l('Indicates if the product is available online only');
        $this->l('Customizable');
        $this->l('Indicates if the product has customizable fields');
        $this->l('On sale');
        $this->l('Indicates if the product has the "on sale" label on its front-office page');
        $this->l('Indicates if product uses advanced stock management');
        $this->l('Short description for the product');
        $this->l('Product description');
        $this->l('Friendly URL of the product');
        $this->l('Meta description of the product');
        $this->l('Meta title of the product');
        $this->l('List of the tax rules groups the product belongs to');
        $this->l('Link to the manufacturer of the product');
        $this->l('Default supplier');
        $this->l('Link to the default supplier of the product');
        $this->l('Number of attachments for the product');
        $this->l('List of the different combinations of the product and their available quantities');
        $this->l('List of the features that exist for the product');
        $this->l('List of suppliers for the product');
        $this->l('List of warehouses this product is stored in (only with advanced stock management activated)');
        $this->l('Show the product type');
        $this->l('Indicates the visibility of the product');
        $this->l('Indicates the current status of the product');
        $this->l('Product tags');
        $this->l('Sales this month');
        $this->l('Shows the sales of the current month');
        $this->l('Sales last 30 days');
        $this->l('Show the sales of the last 30 days');
        $this->l('Sales this year');
        $this->l('Show the sales of the current year');
        $this->l('Qty sold this month');
        $this->l('Shows the quantities sold during the current month');
        $this->l('Qty sold last 30 days');
        $this->l('Shows the quantities sold for the last 30 days');
        $this->l('Qty sold this year');
        $this->l('Shows the quantities sold during the current year');
        $this->l('Sales last 12 months');
        $this->l('Show the sales of the last 12 months');
        $this->l('Qty sold last 12 months');
        $this->l('Shows the quantities sold for the last 12 months');
        $this->l('Date add');
        $this->l('Date the product was created');
        $this->l('Date update');
        $this->l('Date the product was last updated');

        //AdminReturn.json
        $this->l('Return identifier');
        $this->l('ID of the related order');
        $this->l('Current status of the return');
        $this->l('Date the return has been issued');
        $this->l('Order ID with link to the order page');
        $this->l('Customer name with link to the customer page');
        $this->l('Returned products');
        $this->l('List of products that have been returned');
    }
}
