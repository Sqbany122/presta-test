<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 19.12.13
 * Time: 11:48
 */

class productStatus extends Module
{
    public $settings;

    public function __construct()
    {
        $this->name = 'productstatus';
        $this->tab = 'others';
        $this->version = '2.8.0';
        $this->author = 'Andreika';
        $this->module_key = '83f9247e7b68d9fd751144e4d9b42eb5';
        $this->ps_versions_compliancy['min'] = '1.5.0';
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('Product Status');
        $this->description = $this->l('Show ordered products and statuses of products');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
    }

    public function install()
    {

        if (!parent::install()
            OR !$this->registerHook('backOfficeTop')
            OR !$this->registerHook('updateOrderStatus')
            OR !$this->registerHook('postUpdateOrderStatus')
            OR !$this->registerHook('cancelProduct')
            OR !$this->registerHook('AdminOrder')
            OR !$this->registerHook('newOrder')
            OR !$this->registerHook('displayOrderDetail')
            OR !$this->registerHook('displayBackOfficeHeader'))
            return false;
        $db = Db::getInstance();
        $db->Execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_status` (
                                  `id_history` int(64) NOT NULL AUTO_INCREMENT,
                                  `id_employee` int(64) DEFAULT NULL,
                                  `id_order_detail` int(64) DEFAULT NULL,
                                  `id_product_state` int(64) DEFAULT NULL,
                                  `added` datetime DEFAULT NULL,
                                  PRIMARY KEY (`id_history`),
                                  KEY `id_employee` (`id_employee`,`id_order_detail`,`id_product_state`),
                                  KEY `id_order_detail` (`id_order_detail`),
                                  KEY `id_product_state` (`id_product_state`)
                                ) ENGINE=InnoDB AUTO_INCREMENT=9623 DEFAULT CHARSET=utf8");
        $db->Execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_status_dates` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `id_order_detail` int(11) DEFAULT NULL,
                                  `supplier_delivery` date DEFAULT NULL,
                                  `scheduled_shipping` date DEFAULT NULL,
                                  `tracking_url` varchar(255) DEFAULT NULL,
                                  KEY `id` (`id`)
                                ) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8");
        $this->installTab();
        $this->exportStatuses();
        Configuration::updateValue('PRODUCTSTATUS_OOS_STATUS', _PS_OS_OUTOFSTOCK_);
        Configuration::updateValue('PRODUCTSTATUS_INSTOCK_STATUS', _PS_OS_PREPARATION_);
        return true;
    }

    public function uninstall()
    {

        if (!parent::uninstall())
            return false;
        $this->uninstallTab();
        return true;
    }

    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminProductStatus";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
        {
            $tab->name[$lang['id_lang']] = "Ordered products";
            if ($lang['iso_code'] == 'ru') $tab->name[$lang['id_lang']] = "Заказанные товары";
        }

        $tab->id_parent = (int)Tab::getIdFromClassName('AdminOrders');
        $tab->module = $this->name;
        return $tab->add();
    }

    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminProductStatus');
        if ($id_tab)
        {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        else
            return false;
    }

    public function initSettings()
    {
        $this->settings['out_of_stock_status'] = Configuration::get('PRODUCTSTATUS_OOS_STATUS');
        $this->settings['in_stock_status'] = Configuration::get('PRODUCTSTATUS_INSTOCK_STATUS');
        $this->settings['productstatus_fields_'] = unserialize(Configuration::get('PRODUCTSTATUS_FIELDS_SHOW'));
    }

    public function postConfigProcess()
    {
        //tools::dieObject($_REQUEST);
        if (tools::isSubmit('configureSave')) {
            Configuration::updateValue('PRODUCTSTATUS_OOS_STATUS', tools::getValue('outofstock_status') );
            Configuration::updateValue('PRODUCTSTATUS_INSTOCK_STATUS', tools::getValue('instock_status') );
            Configuration::updateValue('PRODUCTSTATUS_FIELDS_SHOW', serialize(tools::getValue('productstatus_fields_')) );
        }

    }

    public function _getContent()
    {
        $this->postConfigProcess();
        $this->initSettings();

        $statuses = OrderState::getOrderStates($this->context->cookie->id_lang);
        //tools::dieObject($statuses);
        $this->context->smarty->assign('statuses', $statuses);
        $this->context->smarty->assign('settings', $this->settings);
        return $this->display(__FILE__, '/views/templates/admin/config.tpl');
    }

    public function getContent()
    {
        $this->postConfigProcess();
        $this->initSettings();
        //tools::dieObject($this);
        $this->displaySelectForm();
        return $this->_html;
    }

    private function displaySelectForm()
    {
        $this->getFieldsValues();
        $this->_html = $this->renderForm();
    }

    public function getFieldsValues()
    {
        $this->fields_value['out_of_stock_status'] = $this->settings['out_of_stock_status'];
        $this->fields_value['in_stock_status'] = $this->settings['in_stock_status'];
        $wh = array();
        $settings = array();
        foreach ($this->settings['productstatus_fields_'] as $key => $value)
        {
            $wh['productstatus_fields_['.$key.']'] = $value;
        }
        $this->fields_value['productstatus_fields_'] = $this->settings['productstatus_fields_'];

        $this->fields_value = array_merge($this->fields_value, $wh, $settings);
    }

    public function renderForm()
    {
        $wh_option = $this->getFieldsOptions();

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'image' => _PS_ADMIN_IMG_.'information.png'
            ),
            'input' => array(

                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Display fields'),
                    'desc' => $this->l('Check fields'),
                    'name' => 'productstatus_fields',
                    'is_bool' => false,
                    'values' => array(
                        'id' => 'id',
                        'name' => 'label',
                        'query' => $wh_option

                    )
                )
            ),
            'submit' => array(
                'name' => 'configureSave',
                'title' => $this->l('Select')
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_hv';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value = $this->fields_value;
        $helper->tpl_vars = array(
            //'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm($this->fields_form);
    }

    public function getArraySelectedFields()
    {
        $this->initSettings();
        $full_list = $this->getArrayFieldsList();
        $selected = array();
        foreach ($this->settings['productstatus_fields_'] as $field_name => $value)
        {
            $selected[$field_name] = $full_list[$field_name];
        }
        return $selected;
    }

    public function getArrayFieldsList()
    {
        $statusArray = array();
        $statuses = OrderState::getOrderStates((int)($this->context->cookie->__get('id_lang')));
        foreach ($statuses as $state)
        {
            $statusArray[$state['id_order_state']] = $state['name'];
        }
        $list = array(
            'product_id' => array(
                'title' => $this->l('ID product'),
                'align' => 'left',
                'width' => 25),
            'reference' => array(
                'title' => $this->l('Reference'),
                'align' => 'left',
                'width' => 100),
            'id_order' => array(
                'title' => $this->l('ID order'),
                'align' => 'left',
                'width' => 25),
            'customer' => array(
                'title' => $this->l('Client'),
                'width' => 100,
                'tmpTableFilter' => true),
            'product_reference' => array(
                'title' => $this->l('Product reference'),
                'width' => 100),
            'product_name' => array(
                'title' => $this->l('Product'),
                'width' => 200),
            'full_price' => array(
                'title' => $this->l('Price per item'),
                'width' => 50,
                'price' => true,
                'align' => 'right',
                'tmpTableFilter' => true),
            'product_quantity' => array(
                'title' => $this->l('quantity'),
                'width' => 25,
                'align' => 'center'),
            'total_price' => array(
                'title' => $this->l('Total price'),
                'width' => 50,
                'price' => true,
                'align' => 'right',
                'tmpTableFilter' => true,
                'prefix' => '<b>',
                'suffix' => '</b>'),
            'status_name' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'list' => $statusArray,
                'width' => 200,
                'align' => 'left',
                'filter_key' => 'ps!id_product_state',
                'filter_type' => 'int',
                'remove_onclick' => false),

            'date_add' => array(
                'title' => $this->l('Date'),
                'width' => 35,
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'o!date_add'),

            'supplier_delivery' => array(
                'title' => $this->l('Supplier delivery'),
                'type' => 'date',
                'type2' => 'editable',
                'width' => 75,
                'align' => 'right',
                'tmpTableFilter' => true,
                'remove_onclick' => false),

            'scheduled_shipping' => array(
                'title' => $this->l('Scheduled shipping'),
                'type' => 'date',
                'type2' => 'editable',
                'width' => 75,
                'align' => 'right',
                'tmpTableFilter' => true,
                'remove_onclick' => false),

            'tracking_url' => array(
                'title' => $this->l('Tracking URL'),
                'type' => 'editabletext',
                'type2' => 'editable',
                'width' => 75,
                'align' => 'right',
                'tmpTableFilter' => true,
                'remove_onclick' => false),

        );
        return $list;
    }

    public function getFieldsOptions()
    {
        /*
        $wh = array(
            array('id_field' => 'product_id', 'name' => 'ID product'),
            array('id_field' => 'reference', 'name' => 'Reference'),
            array('id_field' => 'id_order', 'name' => 'ID order'),
            array('id_field' => 'customer', 'name' => 'Client'),
            array('id_field' => 'product_reference', 'name' => 'Product reference'),
            array('id_field' => 'product_name', 'name' => 'Product'),
            array('id_field' => 'full_price', 'name' => 'Price per item'),
            array('id_field' => 'product_id', 'name' => 'ID product'),
            array('id_field' => 'product_id', 'name' => 'ID product'),
            array('id_field' => 'product_id', 'name' => 'ID product'),
            array('id_field' => 'product_id', 'name' => 'ID product'),
            array('id_field' => 'product_id', 'name' => 'ID product'),
            array('id_field' => 'product_id', 'name' => 'ID product'),
        );
        */
        $list = $this->getArrayFieldsList();
        $opt = array();
        foreach ($list as $key => $item)
        {
            $opt[] = array(
                'id' => '['.$key.']',
                'val' => $key,
                'label' => $item['title']
            );
        }
        return $opt;
    }

    public function hookdisplayOrderDetail($params)
    {
        $smarty = $this->smarty->smarty;
        $smarty->compile_check = true;
        $products = self::getOrderProductsStatus($params['order']->id, $params['cookie']->__get('id_lang'), true);

        $smarty->assign(array(
            'cookie' => $params['cookie'],
            'products' => $products));

        if (version_compare(_PS_VERSION_, '1.6.0', '>='))
        {
            return $smarty->fetch(_PS_MODULE_DIR_.'/productstatus/views/templates/front/productstatus-order_detail.tpl');
        }
        else
        {
            return $smarty->fetch(_PS_MODULE_DIR_.'/productstatus/views/templates/front/productstatus-order_detail.tpl');
        }


    }

    public function hookDisplayBackOfficeHeader()
    {
        $cookie = $this->context->cookie;
        $ret = '<script src="'.__PS_BASE_URI__.'modules/productstatus/views/js/rgbcolor.js"></script>';
        $ret .= '<script src="'.__PS_BASE_URI__.'modules/productstatus/views/js/productstatus.js"></script>';
        $ret .= '<link rel="stylesheet" href="'.__PS_BASE_URI__.'modules/productstatus/views/css/productstatus.css"/>';
        $ret .= '<span id="user_data" data-lang_id="'.$cookie->__get('id_lang').'" data-id_employee="'.$cookie->__get('id_employee').'"></span>';
        return $ret;
    }

    public function hookPostUpdateOrderStatus($params)
    {
        if ($this->isFirstStatus($params['id_order'])) return true;
        $newIdStatus = $params['newOrderStatus']->id;
        $Order = new Order($params['id_order']);
        $Order->products = $Order->getProducts();
        /*foreach($Order->products as $product)
        {
            self::insertProductStatus($product['id_order_detail'], $newIdStatus, $params['cookie']->id_employee);
        }*/
    }

    public function hooknewOrder($params)
    {
        if (isset($params['orderStatus'])){
            self::setOneStatusForAllProducts($params['orderStatus']->id, $params['order']->id);
        } else {
            $Order = new Order($params['order']->id);
            $this->setFirstStatuses($Order);
        }

        return true;
    }

    public function hookCancelProduct($params)
    {

    }

    public function hookAdminOrder($params)
    {
        $smarty = $this->smarty->smarty;
        $smarty->compile_check = true;
        $products = self::getOrderProductsStatus($params['id_order'], $params['cookie']->__get('id_lang'));
        $smarty->assign(array(
            'cookie' => $params['cookie'],
            'products' => $products,
            'statuses' => self::getStatuses($params['cookie']->__get('id_lang'))));

        if (version_compare(_PS_VERSION_, '1.6.0', '>='))
        {
            return $smarty->fetch(_PS_MODULE_DIR_.'/productstatus/views/templates/admin/productstatus-order.tpl');
        }
        else
        {
            return $smarty->fetch(_PS_MODULE_DIR_.'/productstatus/views/templates/admin/productstatus-order_ps15.tpl');
        }
    }

    /*
    public function hookNewOrder($params)
    {

       self::hookPostUpdateOrderStatus($params);
    }
    */

    public function isFirstStatus($id_order)
    {
        $order = new Order($id_order);
        $history = $order->getHistory($this->context->cookie->id_lang, false, false, 0);
        if (count($history) == 0) return true;
        return false;
    }

    public static function exportStatuses()
    {
        $db = Db::getInstance();
        $result = $db->ExecuteS("SELECT SQL_CALC_FOUND_ROWS
                        a.`id_order_detail`, a.`product_id`, oh.`id_order_state`, oh.`id_employee`, oh.`date_add`
                        FROM `"._DB_PREFIX_."order_detail` a
                        LEFT JOIN "._DB_PREFIX_."order_history AS oh ON (oh.`id_order` = a.`id_order`)
                        LEFT JOIN "._DB_PREFIX_."order_state_lang AS osl ON (oh.`id_order_state` = osl.`id_order_state`)
                        WHERE 1 AND osl.`id_lang` = 1 AND oh.`id_order_history` = (SELECT MAX(`id_order_history`) FROM `"._DB_PREFIX_."order_history` moh WHERE moh.`id_order` = a.`id_order` GROUP BY moh.`id_order`)
                        ");

        foreach ($result as $item)
        {
            if (!self::getLastState($item['id_order_detail'], 1))
            {
                $db->Execute("INSERT INTO `"._DB_PREFIX_."product_status` (id_employee, id_order_detail, id_product_state, added)
                        VALUES (
                        '".pSQL($item['id_employee'])."',
                        '".pSQL($item['id_order_detail'])."',
                        '".pSQL($item['id_order_state'])."',
                        '".pSQL($item['date_add'])."'
                        )");
            }

        }
    }

    public static function getOrderProductsStatus($id_order, $id_lang = 1, $forCustomer = false)
    {
        $Order = new Order($id_order);
        $Order->products = $Order->getProducts();
        foreach ($Order->products as $id_order_detail => $product)
        {
            if ($forCustomer)
            {
                $statusResult = self::getLastStateForCustomer($id_order_detail, $id_lang);
            }
            else
            {
                $statusResult = self::getLastState($id_order_detail, $id_lang);
            }
            $dates = self::getDates($id_order_detail);

            $Order->products[$id_order_detail]['id_product_state'] = $statusResult['id_order_state'];
            $Order->products[$id_order_detail]['name_state'] = $statusResult['name'];
            $Order->products[$id_order_detail]['color_state'] = $statusResult['color'];
            if (!empty($dates)) {
                $Order->products[$id_order_detail]['dates'] = $dates;
                //$Order->products[$id_order_detail]['dates']['supplier_delivery'] = ($dates['supplier_delivery'] == null ? '0000-00-00' : $dates['supplier_delivery']);
                //$Order->products[$id_order_detail]['dates']['scheduled_shipping'] = ($dates['scheduled_shipping'] == null ? '0000-00-00' : $dates['scheduled_shipping']);
            }
            else {
                $Order->products[$id_order_detail]['dates']['supplier_delivery'] = '';
                $Order->products[$id_order_detail]['dates']['scheduled_shipping'] = '';
            }
        }

        return $Order->products;
    }

    public static function getStatuses($id_lang)
    {
        $statuses = OrderState::getOrderStates((int)($id_lang));
        return $statuses;
    }

    public static function insertProductStatus($id_details, $id_state, $id_employee = 0)
    {
        $lastState = self::getLastState($id_details);
        if ($lastState['id_order_state'] == $id_state) return false;
        $db = Db::getInstance();
        $db->Execute("INSERT INTO `"._DB_PREFIX_."product_status` (id_employee, id_order_detail, id_product_state, added)
                        VALUES (
                        '".pSQL($id_employee)."',
                        '".pSQL($id_details)."',
                        '".pSQL($id_state)."',
                        '".date("Y-m-d H:i:s")."'
                        )");
    }


    public static function setStatus($id_details, $id_state, $id_employee = 0, $id_lang = 1)
    {
        self::insertProductStatus($id_details, $id_state, $id_employee, $id_lang);
        self::compliteOrderProductsStatuses($id_details);
        return self::getStatus($id_state, $id_lang);
    }

    public static function getStatus($id_state, $id_lang = 1)
    {
        $result = self::getStatuses($id_lang);

        foreach ($result as $item)
        {
            if ($item['id_order_state'] == $id_state) break;
        }
        return $item;
    }

    public static function getLastState($id_order_detail, $id_lang = 1)
    {
       $state =  Db::getInstance()->getValue('SELECT `id_product_state`
                                    FROM `'._DB_PREFIX_.'product_status`
                                    WHERE `id_order_detail` = '.$id_order_detail.'
                                    ORDER BY `added` DESC, `id_history` DESC');
        if (!$state)
            return false;

        return self::getStatus($state, $id_lang);
    }

    public static function getLastStateForCustomer($id_order_detail, $id_lang = 1)
    {
        $state =  Db::getInstance()->executeS('SELECT `id_product_state`
                                    FROM `'._DB_PREFIX_.'product_status`
                                    WHERE `id_order_detail` = '.$id_order_detail.'
                                    ORDER BY `added` DESC, `id_history` DESC');
        if (!$state)
            return false;
        foreach ($state as $status)
        {
            $currentState = new OrderState($status['id_product_state']);
            if ($currentState->hidden == 1) continue;
            return self::getStatus($currentState->id, $id_lang);
        }

    }

    public static function compliteOrderProductsStatuses($id_order_detail)
    {
        $order_id =  Db::getInstance()->getValue('SELECT `id_order`
                                    FROM `'._DB_PREFIX_.'order_detail`
                                    WHERE `id_order_detail` = '.$id_order_detail.'
                                    ');
        $Order = new Order($order_id);
        $Order->products = $Order->getProducts();
        $Order->id_order_state = OrderHistory::getLastOrderState($Order->id)->id;
        /*foreach ($Order->products as $key => $product)
        {
            $id_product_state = self::getLastState($product['id_order_detail']);
            if (!$id_product_state)
            {
                $Order->products[$key]['id_product_state'] = $Order->id_order_state;
            }
            else
            {
                $Order->products[$key]['id_product_state'] = $id_product_state['id_order_state'];
            }
        }*/

        $result = self::isOrderStatusChange($Order->products);

        if ($result === false)
        {
            // echo "СТАТУСЫ РАЗНЫЕ";
        }
        else
        {
            self::setOrderStatus($result, $Order->id);
        }
    }

    public static function setOrderStatus($id_order_state, $id_order, $id_employee = 0)
    {
        //$Order = new Order($id_order);
        //$Order->setCurrentState($id_order_state, $id_employee);
    }

    public static function isOrderStatusChange($products)
    {
        /*$i = 1;
        foreach ($products as $product)
        {
            if ($i == 1)
            {
               $checkValue = $product['id_product_state'];
               $i++;
            }
            if ($product['id_product_state'] != $checkValue) return false;
        }
        return $checkValue;*/
    }

    public static function setProductStatusesFromOrderStatus($id_order)
    {
        $Order = new Order($id_order);
        $Order->products = $Order->getProducts();
        //$Order->id_order_state = OrderHistory::getLastOrderState($Order->id)->id;
        $Order->id_order_state = $Order->current_state;
        /*foreach ($Order->products as $id_order_detail => $product)
        {
            self::insertProductStatus($id_order_detail, $Order->id_order_state);
        }*/
    }

    public static function getProductStatusHistory($id_order_detail, $id_lang = 1)
    {

        $db = Db::getInstance();
        $result = $db->ExecuteS("SELECT * FROM "._DB_PREFIX_."product_status WHERE id_order_detail = ".$id_order_detail." ORDER BY `id_history` DESC");
        $return = array();
        foreach ($result as $history)
        {
            $state = new OrderState($history['id_product_state'], $id_lang);
            $return[$history['id_history']]['name'] = $state->name;
            $return[$history['id_history']]['employee'] = self::getEmployeeNameById($history['id_employee']);
            $return[$history['id_history']]['added'] = self::displayDateTime($history['added']);

        }
        return $return;
    }

    public static function getEmployeeNameById($id_employee)
    {

        $db = Db::getInstance();
        $result = $db->ExecuteS("SELECT * FROM `"._DB_PREFIX_."employee` WHERE `id_employee` = '".$id_employee."'");
        if (count($result) == 1)
        {
            return trim($result[0]['firstname']).' '.trim($result[0]['lastname']);
        }
        return false;
    }

    public static function displayDateTime($datetime)
    {
        return date('d/m/y H:i', strtotime($datetime));
    }

    public static function getDateTime($mask, $value)
    {
        $date = DateTime::createFromFormat($mask, $value);
        return $date->format('Y-m-d');
    }

    public static function setDate($field, $id_order_detail, $value)
    {

        $db = Db::getInstance();
        $result = $db->ExecuteS("SELECT * FROM `"._DB_PREFIX_."product_status_dates` WHERE `id_order_detail` = ".$id_order_detail."");
        if (empty($value)) {
            $value = "NULL";
        } else {
            $value = "'".$value."'";
        }
        if (count($result) == 0) {
            $sql = "INSERT INTO `" . _DB_PREFIX_ . "product_status_dates` SET `".$field."` = ".$value.", `id_order_detail` = ".$id_order_detail;
        }
        else
        {
            $sql = "UPDATE `" . _DB_PREFIX_ . "product_status_dates` SET `".$field."` = ".$value." WHERE `id_order_detail` = ".$id_order_detail;
        }
        $db->Execute($sql);
    }

    public static function getDates($id_order_detail)
    {
        $db = Db::getInstance();
        $dates = $db->ExecuteS("SELECT `supplier_delivery`, `scheduled_shipping`, `tracking_url`  FROM `"._DB_PREFIX_."product_status_dates` WHERE `id_order_detail` = ".$id_order_detail."");
        if (count($dates) == 0) return array();
        return $dates[0];
    }

    public function setFirstStatuses(Order $order)
    {
        $order->products = $order->getProducts();
        //tools::dieObject($order);
        $this->initSettings();
        foreach ($order->products as $product)
        {
            if ($product['current_stock'] < 0) {
                $this->insertProductStatus($product['id_order_detail'], $this->settings['out_of_stock_status']);
            } else {
                $this->insertProductStatus($product['id_order_detail'], $this->settings['in_stock_status']);
            }
        }
    }

    public static function setOneStatusForAllProducts($id_order_state, $id_order)
    {
        $Order = new Order($id_order);
        $Order->products = $Order->getProducts();
        foreach ($Order->products as $id_order_detail => $product)
        {
            self::insertProductStatus($id_order_detail, $id_order_state);
        }
    }
}
