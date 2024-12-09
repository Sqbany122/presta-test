<?php
/**
 *    ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      controllers/admin/AdminAmbBoCustomizerAjaxController.php
 *    @subject   Manages Ajax calls from callbacks
 *
 *    Support by mail: support@ambris.com
 */

class AdminAmbBoCustomizerAjaxController extends AdminController
{

    public $bootstrap = true;
    public $module;
    private $css = array();

    public function __construct()
    {
        $this->module = Module::getInstanceByName('ambbocustomizer');
        parent::__construct();
    }

    public static function noResult($items, $msg)
    {
        if (!is_array($items) || count($items) == 0) {
            die(Tools::jsonEncode(self::getTranslation($msg)));
        }

        return true;
    }

    public static function getId($method)
    {
        $id = Tools::getValue('id', 0);
        if ($id == 0) {
            die(Tools::jsonEncode('ID missing in ' . $method));
        } else {
            return $id;
        }
    }

    public static function getTranslation($string)
    {
        return Translate::getModuleTranslation('ambbocustomizer', $string, 'ambbocustomizer');
    }

    public function ajaxProcessFetchData()
    {
        die(Tools::jsonEncode(
            'Standard ajax fetch succeeded.
            <br />If this message appears, it means no specific
            process was found in the data/fields configuration file.'
        ));
    }

    private function fetchProductsFromOrder($id_order, $returned_products_only = false)
    {
        $order = new Order($id_order);
        if (!Validate::isLoadedObject($order)) {
            self::noResult($order, "This order is not available");
        }

        $items = $order->getProducts();
        if ($returned_products_only) {
            $items = array_filter(
                $items,
                function ($x) {
                    return ((int) $x['product_quantity_return'] > 0);
                }
            );
        }

        ksort($items);

        foreach ($items as &$item) {
            $item['image_tag'] = $this->getImageTagForItem($item);
        }
        return $items;
    }

    private function getImage($id_product, $id_product_attribute, $id_lang = null)
    {
        if ($id_lang === null) {
            $id_lang = $this->context->language->id;
        }

        $images = Image::getImages($id_lang, $id_product, $id_product_attribute);
        if (is_array($images) && count($images) > 0) {
            return new Image($images[0]['id_image'], $id_lang);
        }

        return null;
    }

    private function getImageTagForItem($item)
    {
        if (isset($item['id_product']) && !isset($item['product_id'])) {
            $item['product_id'] = $item['id_product'];
        }
        if (isset($item['id_product_attribute']) && !isset($item['product_attribute_id'])) {
            $item['product_attribute_id'] = $item['id_product_attribute'];
        }

        if ($item['image'] != null) {
            $name = 'product_mini_' . (int) $item['product_id'] . (isset($item['product_attribute_id']) ? '_' . (int) $item['product_attribute_id'] : '') . '.jpg';
            // generate image cache, only for back office
            $image_tag = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $item['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
            if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                $image_size = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                $image_tag = str_replace('>', $image_size[3] . ' >', $image_tag);
            }
        } else {
            $image_tag = '--';
        }
        return $image_tag;
    }

    public function ajaxProcessFetchProductsFromOrderId()
    {
        $id = self::getId(__METHOD__);

        $items = $this->fetchProductsFromOrder($id);
        self::noResult($items, "No products in this order");
        $items_list = array();
        $title = '';
        foreach ($items as $item) {
            $items_list[] = array(
                $item['product_id'],
                $item['image_tag'],
                $item['reference'],
                htmlentities(Product::getProductName($item['product_id'], $item['product_attribute_id'])),
                Tools::displayPrice($item['unit_price_tax_incl']),
                $item['product_quantity'],
                Tools::displayPrice($item['total_price_tax_incl']),
            );
        }

        $headers = array(
            '<ID',
            '=' . Translate::getAdminTranslation('Image', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Reference', 'AdminOrders'),
            '<' . Translate::getAdminTranslation('Product', 'AdminOrders'),
            '>' . Translate::getAdminTranslation('Unit price', 'AdminOrders') . ' <small class="text-muted">' . Translate::getAdminTranslation('tax incl.', 'AdminProducts') . '</small>',
            '>' . Translate::getAdminTranslation('Quantity', 'AdminOrders'),
            '>' . Translate::getAdminTranslation('Total', 'AdminOrders') . ' <small class="text-muted">' . Translate::getAdminTranslation('tax incl.', 'AdminProducts') . '</small>',
        );
        $css = array('td' => array_fill(0, count($headers), 'line-height:45px;'));
        $css['td'][2] .= 'white-space:nowrap;';
        $css['td'][3] .= 'white-space:nowrap;';
        $css['td'][4] .= 'white-space:nowrap;';
        $css['td'][6] .= 'white-space:nowrap;';
        $html = array('th' => true, 1 => true);

        die(Tools::jsonEncode(array('html' => $this->renderTable($headers, $items_list, $title, $html, $css))));
    }

    public function ajaxProcessFetchReturnedProductsFromOrderId()
    {
        $id = self::getId(__METHOD__);

        $items = $this->fetchProductsFromOrder($id, true);
        self::noResult($items, "No returned products in this order");
        $items_list = array();
        $title = '';
        foreach ($items as $item) {
            $items_list[] = array(
                $item['product_id'],
                $item['image_tag'],
                $item['reference'],
                htmlentities(Product::getProductName($item['product_id'], $item['product_attribute_id'])),
                Tools::displayPrice($item['unit_price_tax_incl']),
                $item['product_quantity_return'],
                Tools::displayPrice($item['total_price_tax_incl']),
            );
        }

        $headers = array('<ID', 'Image', '<Reference', '<Product', '>Unit price tax incl.', '>Returned quantity', '>Total price tax incl.');
        $headers = array(
            '<ID',
            '=' . Translate::getAdminTranslation('Image', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Reference', 'AdminOrders'),
            '<' . Translate::getAdminTranslation('Product', 'AdminOrders'),
            '>' . Translate::getAdminTranslation('Unit price', 'AdminOrders') . ' <small class="text-muted">' . Translate::getAdminTranslation('tax incl.', 'AdminProducts') . '</small>',
            '>' . Translate::getAdminTranslation('Returned', 'AdminOrders'),
            '>' . Translate::getAdminTranslation('Total', 'AdminOrders') . ' <small class="text-muted">' . Translate::getAdminTranslation('tax incl.', 'AdminProducts') . '</small>',
        );
        $css = array('td' => array_fill(0, count($headers), 'line-height:45px;'));
        $css['td'][2] .= 'white-space:nowrap;';
        $css['td'][3] .= 'white-space:nowrap;';
        $css['td'][4] .= 'white-space:nowrap;';
        $css['td'][6] .= 'white-space:nowrap;';
        $html = array('th' => true, 1 => true);

        die(Tools::jsonEncode(array('html' => $this->renderTable($headers, $items_list, $title, $html, $css))));
    }

    public function ajaxProcessFetchReturnedProductsFromReturnOrderId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                    SELECT * FROM ' . _DB_PREFIX_ . 'order_return_detail ord
                    LEFT JOIN ' . _DB_PREFIX_ . 'order_detail od ON od.id_order_detail=ord.id_order_detail
                    LEFT JOIN ' . _DB_PREFIX_ . 'product p ON od.product_id=p.id_product
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON od.product_id=pl.id_product
                    AND pl.id_lang=' . (int) $this->context->language->id . '
                    WHERE ord.id_order_return=' . (int) $id . '
                    AND ord.product_quantity > 0
                    AND pl.id_shop=' . (int) $this->context->shop->id;

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        self::noResult($items, "No returned products");

        $title = '';
        $items_list = array();
        foreach ($items as $item) {
            $item['image'] = $this->getImage($item['product_id'], $item['product_attribute_id']);
            $items_list[] = array(
                $item['product_id'],
                $this->getImageTagForItem($item),
                $item['reference'],
                htmlentities(Product::getProductName($item['product_id'], $item['product_attribute_id'])),
                Tools::displayPrice($item['unit_price_tax_incl']),
                $item['product_quantity_return'],
                Tools::displayPrice($item['total_price_tax_incl']),
            );
        }

        $headers = array('<ID', 'Image', '<Reference', '<Product', '>Unit price tax incl.', '>Returned quantity', '>Total price tax incl.');
        $css = array('td' => array_fill(0, count($headers), 'line-height:45px;'));
        $css['td'][2] .= 'white-space:nowrap;';
        $css['td'][3] .= 'white-space:nowrap;';
        $css['td'][4] .= 'white-space:nowrap;';
        $css['td'][6] .= 'white-space:nowrap;';
        $html = array(1 => true);

        die(Tools::jsonEncode(array('html' => $this->renderTable($headers, $items_list, $title, $html, $css))));
    }

    public function ajaxProcessFetchPaimentsFromOrderId()
    {
        $id = self::getId(__METHOD__);

        $query = '
					SELECT * FROM ' . _DB_PREFIX_ . 'order_invoice_payment oip
					LEFT JOIN ' . _DB_PREFIX_ . 'order_payment op ON op.id_order_payment=oip.id_order_payment
					WHERE oip.id_order=' . (int) $id;

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No payments in this order");

        $title = '';
        $items_list = array();
        foreach ($items as $item) {
            $items_list[] = array(
                $item['date_add'],
                $item['payment_method'],
                $item['transaction_id'],
                Tools::displayPrice($item['amount'], (int) $item['id_currency']),
            );
        }

        $headers = array(
            '<' . Translate::getAdminTranslation('Date', 'AdminOrders'),
            '<' . Translate::getAdminTranslation('Payment method', 'AdminOrders'),
            '<' . Translate::getAdminTranslation('Transaction ID', 'AdminOrders'),
            '>' . Translate::getAdminTranslation('Amount', 'AdminOrders'),
        );
        $css = array('td' => 'white-space:nowrap;');
        $html = array();

        die(Tools::jsonEncode(array('html' => $this->renderTable($headers, $items_list, $title, $html, $css))));
    }

    public function ajaxProcessFetchVouchersFromOrderId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                    SELECT * FROM ' . _DB_PREFIX_ . 'order_cart_rule ocr
                    WHERE ocr.id_order=' . (int) $id;

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No vouchers available for this order");

        $title = '';
        $items_list = array();
        foreach ($items as $item) {
            $items_list[] = array(
                $item['name'],
                Tools::displayPrice($item['value']),
            );
        }

        $headers = array(
            '<' . Translate::getAdminTranslation('Discount name', 'AdminOrders'),
            '>' . Translate::getAdminTranslation('Value', 'AdminOrders'),
        );
        $css = array();
        $html = array();

        die(Tools::jsonEncode(array('html' => $this->renderTable($headers, $items_list, $title, $html, $css))));
    }

    ////////////////////////////////////////////////AdminProducts///////////////////////////////////////////////////////

    public function ajaxProcessFetchSpecificPricesFromProductId()
    {
        $id = self::getId(__METHOD__);

        $query = 'SELECT sp.*, spr.name as rule_name, sh.name as shop_name, shg.name as shop_group_name,
        cu.name as currency_name, col.name as country_name, grl.name as group_name, CONCAT(cust.firstname, \' \', cust.lastname) as customer_name
        FROM ' . _DB_PREFIX_ . 'specific_price sp
        LEFT JOIN ' . _DB_PREFIX_ . 'specific_price_rule spr ON spr.id_specific_price_rule=sp.id_specific_price_rule
        LEFT JOIN ' . _DB_PREFIX_ . 'shop sh ON sp.id_shop = sh.id_shop
        LEFT JOIN ' . _DB_PREFIX_ . 'shop_group shg ON sp.id_shop_group = shg.id_shop_group
        LEFT JOIN ' . _DB_PREFIX_ . 'currency cu ON sp.id_currency = cu.id_currency
        LEFT JOIN ' . _DB_PREFIX_ . 'country_lang col ON sp.id_country = col.id_country AND col.id_lang = ' . (int) $this->context->language->id . '
        LEFT JOIN ' . _DB_PREFIX_ . 'group_lang grl ON sp.id_group = grl.id_group AND grl.id_lang = ' . (int) $this->context->language->id . '
        LEFT JOIN ' . _DB_PREFIX_ . 'customer cust ON sp.id_customer = cust.id_customer
        WHERE sp.id_product=' . $id . ' AND NOW() BETWEEN (IF(sp.`from` = 0, NOW(), sp.`from`)) AND (IF(sp.`to` = 0, NOW(), sp.`to`))';
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No specific prices for this products");

        $title = '';
        $items_list = array();
        foreach ($items as $item) {
            $id_currency = $item['id_currency'] ? $item['id_currency'] : $this->context->currency->id;

            if ($item['from'] == 0 && $item['to'] == 0) {
                $date_period = self::getTranslation('unlimited');
            } else {
                if ($item['from'] == 0) {
                    $item['from'] = self::getTranslation('undefined');
                }

                if ($item['to'] == 0) {
                    $item['to'] = self::getTranslation('undefined');
                }

                $date_period = Translate::getAdminTranslation('From', 'AdminProducts') . ' ' . Tools::displayDate($item['from']) . '<br/>' . Translate::getAdminTranslation('To', 'AdminProducts') . ' ' . Tools::displayDate($item['to']);
            }
            error_log(print_r($item, true));
            $price = Tools::ps_round($item['price'], 2);
            $fixed_price = (($item['price'] == -1) ? '--' : Tools::displayPrice($price, (int) $id_currency));

            if ($item['reduction_type'] == 'percentage') {
                $impact = '- ' . ($item['reduction'] * 100) . ' %';
            } elseif ($item['reduction'] > 0) {
                $impact = '- ' . Tools::displayPrice(Tools::ps_round($item['reduction'], 2), (int) $id_currency) . ' ';
                if ($item['reduction_tax']) {
                    $impact .= '(' . Translate::getAdminTranslation('Tax incl.', 'AdminProducts') . ')';
                } else {
                    $impact .= '(' . Translate::getAdminTranslation('Tax excl.', 'AdminProducts') . ')';
                }
            } else {
                $impact = '--';
            }

            $items_list[] = array(
                $item['rule_name'],
                ((int) $item['id_product_attribute'] == 0 ? Translate::getAdminTranslation('All combinations', 'AdminProducts') : htmlentities(Product::getProductName($item['product_id'], $item['product_attribute_id']))),
                (empty($item['shop_name']) ? Translate::getAdminTranslation('All shops', 'AdminProducts') : $item['shop_name']),
                (empty($item['currency_name']) ? Translate::getAdminTranslation('All currencies', 'AdminProducts') : $item['currency_name']),
                (empty($item['country_name']) ? Translate::getAdminTranslation('All countries', 'AdminProducts') : $item['country_name']),
                (empty($item['group_name']) ? Translate::getAdminTranslation('All groups', 'AdminProducts') : $item['group_name']),
                (empty($item['customer_name']) ? Translate::getAdminTranslation('All customers', 'AdminProducts') : $item['customer_name']),
                $fixed_price,
                $impact,
                $date_period,
                $item['from_quantity'],
            );
            if (!Shop::isFeatureActive()) {
                array_splice($items_list[count($items_list) - 1], 2, 1);
            }
        }

        $headers = array(
            '<' . Translate::getAdminTranslation('Rule', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Combination', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Shop', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Currency', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Country', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Group', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Customer', 'AdminProducts'),
            '>' . Translate::getAdminTranslation(($this->context->country->display_tax_label ? 'Fixed price (tax excl.)' : 'Fixed price'), 'AdminProducts'),
            '>' . Translate::getAdminTranslation('Impact', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Period', 'AdminProducts'),
            '>' . Translate::getAdminTranslation('From (quantity)', 'AdminProducts'),
        );
        $css = array();
        $html = array(9 => true);

        if (!Shop::isFeatureActive()) {
            array_splice($headers, 2, 1);
            array_splice($css, 2, 1);
            $html = array(8 => true);
        }

        die(Tools::jsonEncode(array('html' => $this->renderTable($headers, $items_list, $title, $html, $css))));
    }

    public function ajaxProcessFetchCombinationsFromProductId()
    {
        $id = self::getId(__METHOD__);

        $product = new Product($id);
        $items = $product->getAttributesResume($this->context->language->id);

        self::noResult($items, "No combinations for this products");

        $title = '';
        $items_list = array();
        foreach ($items as $item) {
            $image = Product::getCombinationImageById($item['id_product_attribute'], $this->context->language->id);
            if (is_array($image)) {
                $item['image'] = new Image($image['id_image']);
            }
            $items_list[] = array(
                $item['attribute_designation'],
                (isset($item['image']) ? $this->getImageTagForItem($item) : '--'),
                Tools::displayPrice($item['price']),
                $item['weight'] . Configuration::get('PS_WEIGHT_UNIT'),
                $item['reference'],
                $item['ean13'],
                $item['upc'],
                Tools::displayPrice($item['wholesale_price']),
                $this->displayEnabled($item['default_on']),
                $item['quantity'],
            );
        }

        $headers = array(
            '<' . Translate::getAdminTranslation('Attribute - value pair', 'AdminProducts'),
            '-' . Translate::getAdminTranslation('Image', 'AdminProducts'),
            '>' . Translate::getAdminTranslation('Impact on price', 'AdminProducts'),
            '>' . Translate::getAdminTranslation('Impact on weight', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Reference', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('EAN-13', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('UPC', 'AdminProducts'),
            '>' . Translate::getAdminTranslation('Wholesale price', 'AdminProducts'),
            '<' . Translate::getAdminTranslation('Default', 'AdminProducts'),
            '>' . Translate::getAdminTranslation('Quantity', 'AdminProducts'),
        );
        $css = array();
        $html = array('th' => true, 1 => true, 8 => true);

        die(Tools::jsonEncode(array('html' => $this->renderTable($headers, $items_list, $title, $html, $css))));
    }

    public function ajaxProcessFetchFeaturesFromProductId()
    {
        $id = self::getId(__METHOD__);

        $items = Product::getFrontFeaturesStatic($this->context->language->id, $id);

        self::noResult($items, "No features for this product");

        $items_list = '';
        $done = array();
        foreach ($items as $item) {
            if (!isset($done[$item['id_feature']])) {
                $done[$item['id_feature']] = array("name" => $item["name"], "values" => array());
            }
            $done[$item['id_feature']]["values"][] = $item['value'];
        }

        foreach ($done as $feature) {
            $values = '';
            foreach ($feature['values'] as $value) {
                $values .= (Tools::strlen($values) > 0 ? ', ' : '') . $value;
            }
            if (Tools::strlen($values) > 0) {
                $items_list .= '<strong>' . $feature['name'] . ' :</strong> ' . $values . '<br />';
            }
        }

        die(Tools::jsonEncode($items_list));
    }

    public function ajaxProcessFetchSuppliersFromProductId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT * FROM ' . _DB_PREFIX_ . 'product_supplier
                LEFT JOIN ' . _DB_PREFIX_ . 'supplier USING(id_supplier)
                WHERE id_product=' . (int) $id . '
                GROUP BY id_supplier;';

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No suppliers for this product");

        $items_list = '';
        foreach ($items as $item) {
            $items_list .= $item['name'] . '<br />';
        }

        die(Tools::jsonEncode($items_list));
    }

    public function ajaxProcessFetchWarehousesFromProductId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT * FROM ' . _DB_PREFIX_ . 'warehouse_product_location
                LEFT JOIN ' . _DB_PREFIX_ . 'warehouse USING(id_warehouse)
                WHERE id_product=' . (int) $id . '
                GROUP BY id_warehouse;';

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "This product is not attached to any warehouse");

        $items_list = '';
        foreach ($items as $item) {
            $items_list .= $item['name'] . '<br />';
        }

        die(Tools::jsonEncode($items_list));
    }

    public function ajaxProcessFetchOrdersFromCustomerId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT id_order, reference, date_add, total_paid_tax_incl FROM ' . _DB_PREFIX_ . 'orders o
                INNER JOIN ' . _DB_PREFIX_ . 'order_state os ON o.current_state = os.id_order_state
                WHERE os.logable=1
                AND o.id_customer=' . (int) $id;

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No orders for this customer");

        /*
        $items_list = '';
        foreach ($items as $item) {
        $items_list .= Tools::displayDate($item['date_add']) . ' : #' . $item['id_order'] . ', '
        . $item['reference'] . ' (' . Tools::displayPrice($item['total_paid_tax_incl']) . ')<br />';
        }

        die(Tools::jsonEncode($items_list));
         */
        $items_list = array();
        foreach ($items as $item) {
            $items_list[] = array(Tools::displayDate($item['date_add']), '#' . $item['id_order'], $item['reference'], Tools::displayPrice($item['total_paid_tax_incl']));
        }

        $headers = array('<Date', '-Id', 'Reference', '>Total paid tax incl.');

        die(Tools::jsonEncode(array('html' => $this->renderTable($headers, $items_list))));
    }

    public function ajaxProcessFetchCartsFromCustomerId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT c.id_cart, c.date_add,
                IF (
                    IFNULL(
                        o.id_order,
                        \'' . $this->l('Non ordered', 'AdminCarts')
        . '\') = \'' . $this->l('Non ordered', 'AdminCarts') . '\',
                        IF(TIME_TO_SEC(TIMEDIFF(\'' . pSQL(date('Y-m-d H:i:00', time()))
        . '\', c.`date_add`)) > 86400, \''
        . $this->l('Abandoned cart', 'AdminCarts') . '\', \'' . $this->l('Non ordered', 'AdminCarts')
        . '\'), CONCAT("' . $this->l('Order', 'AdminOrders') . '", " #", o.id_order)) AS status
                FROM ' . _DB_PREFIX_ . 'cart c
                LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_cart=c.id_cart
                WHERE c.id_customer=' . (int) $id;

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No carts for this customer");

        $items_list = '';
        foreach ($items as $item) {
            $items_list .= Tools::displayDate($item['date_add']) . ': #' . $item['id_cart']
                . ' (' . $item['status'] . ')<br />';
        }

        die(Tools::jsonEncode($items_list));
    }
    public function ajaxProcessFetchOrderedCartsFromCustomerId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT c.id_cart, o.id_order, c.date_add
                FROM ' . _DB_PREFIX_ . 'cart c
                LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_cart=c.id_cart
                WHERE c.id_customer=' . (int) $id . ' AND o.id_order IS NOT NULL';

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No ordered carts for this customer");

        $items_list = '';
        foreach ($items as $item) {
            $items_list .= Tools::displayDate($item['date_add']) . ': #' . $item['id_cart']
            . ' (' . $this->l('Order', 'AdminOrders') . ' #' . $item['id_order'] . ')<br />';
        }

        die(Tools::jsonEncode($items_list));
    }
    public function ajaxProcessFetchAbandonedCartsFromCustomerId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT c.id_cart,
                c.date_add
                FROM ' . _DB_PREFIX_ . 'cart c
                LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_cart=c.id_cart
                WHERE c.id_customer=' . (int) $id . ' AND o.id_order IS NULL';

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No abandoned carts for this customer");

        $items_list = '';
        foreach ($items as $item) {
            $items_list .= Tools::displayDate($item['date_add']) . ': #' . $item['id_cart'] . '<br />';
        }

        die(Tools::jsonEncode($items_list));
    }

    public function ajaxProcessFetchGroupsFromCustomerId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT gl.name FROM ' . _DB_PREFIX_ . 'customer_group cg
                INNER JOIN ' . _DB_PREFIX_ . 'group_lang gl ON gl.id_group=cg.id_group
                WHERE cg.id_customer=' . (int) $id . '
                AND gl.id_lang=' . (int) $this->context->language->id;

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "This customer is not attached to any group");

        $items_list = '';
        foreach ($items as $item) {
            $items_list .= $item['name'] . '<br />';
        }

        die(Tools::jsonEncode($items_list));
    }

    public function ajaxProcessFetchCartRulesFromCustomerId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT * FROM ' . _DB_PREFIX_ . 'cart_rule cr
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_lang crl ON cr.id_cart_rule=crl.id_cart_rule
                WHERE cr.id_customer=' . (int) $id . '
                AND crl.id_lang=' . (int) $this->context->language->id;

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No vouchers for this customer");

        $items_list = '';
        foreach ($items as $item) {
            $items_list .= $item['name'] . '<br />';
        }

        die(Tools::jsonEncode($items_list));
    }

    public function ajaxProcessFetchProductsFromCartId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT * FROM ' . _DB_PREFIX_ . 'cart_product cp
                LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=cp.id_product
                WHERE cp.id_cart=' . (int) $id . '
                AND pl.id_lang=' . (int) $this->context->language->id . '
                AND pl.id_shop=' . (int) $this->context->shop->id;

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No products in this cart");

        $items_list = '';
        foreach ($items as $item) {
            $items_list .= $item['name'] . ' (x' . $item['quantity'] . ')<br />';
        }

        die(Tools::jsonEncode($items_list));
    }

    public function ajaxProcessFetchCategoryGroupsFromCategoryId()
    {
        $id = self::getId(__METHOD__);

        $query = '
                SELECT * FROM ' . _DB_PREFIX_ . 'category_group cg
                LEFT JOIN ' . _DB_PREFIX_ . 'group_lang gl ON gl.id_group=cg.id_group
                WHERE cg.id_category=' . (int) $id . '
                AND gl.id_lang=' . (int) $this->context->language->id;

        $this->module->log($query, __FILE__, __METHOD__, __LINE__);
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        self::noResult($items, "No groups attached to this category");

        $items_list = '';
        foreach ($items as $item) {
            $items_list .= $item['name'] . '<br />';
        }

        die(Tools::jsonEncode($items_list));
    }

    public function getCSS($element, $index = 0)
    {
        $css = $this->css;
        $localCSS = '';

        if (isset($css[$element])) {
            if (is_array($css[$element])) {
                $localCSS = (isset($css[$element][$index]) ? $css[$element][$index] : '');
            } else {
                $localCSS = $css[$element];
            }
        }
        return (!empty($localCSS) ? ' style="' . $localCSS . '" ' : '');
    }

    public function displayEnabled($value)
    {

        $enable = '<span class="icon-check"></span>';
        $disable = '';
        return (($value != null && $value) ? $enable : $disable);
    }

    public function renderTable($headers, $rows, $title = "", $html = array(), $css = array())
    {
        $this->css = $css;

        $columns = array();
        if (count($headers) > 0) {
            $i = 0;
            foreach ($headers as $header) {
                $columns[$i] = array();
                $start = 1;
                $style = Tools::substr($header, 0, 1);
                if ($style == '<') {
                    $columns[$i]['align'] = 'text-left';
                } elseif ($style == '>') {
                    $columns[$i]['align'] = 'text-right';
                } elseif ($style == '-') {
                    $columns[$i]['align'] = 'text-center';
                } elseif ($style == '=') {
                    $columns[$i]['align'] = 'text-justify';
                } else {
                    $columns[$i]['align'] = '';
                    $start = 0;
                }

                $columns[$i]['header'] .= (isset($html['th']) && $html['th'] === true ? Tools::substr($header, $start) : Tools::safeOutput(Tools::substr($header, $start)));
                $i++;
            }
        }

        $this->context->smarty->assign(array(
            'builder' => $this,
            'columns' => $columns,
            'title' => $title,
            'html_columns' => $html,
            'rows' => $rows,
        ));

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ambbocustomizer/views/templates/admin/render_table.tpl');
    }

    /*public function renderTable__($headers, $rows, $title = "", $html = array(), $css = array())
{
$output = '<div class="bootstrap"><div class="table-responsive">';
if (Tools::strlen($title) > 0) {
$output .= '<div class="title" ' . $this->getCSS('title') . ' >';
$output .= $title;
$output .= '<span class="badge" ' . $this->getCSS('badge') . '>';
$output .= count($rows);
$output .= '</span></div>';
}
$output .= '<table class="table" ' . $this->getCSS('table', $css) . ' >';
// output headers
$styles = array();
if (count($headers) > 0) {
$output .= '<thead ' . $this->getCSS('thead', $css) . '><tr ' . $this->getCSS('thead > tr', $css) . ' >';
$i = 0;
foreach ($headers as $header) {
$start = 1;
$style = Tools::substr($header, 0, 1);
if ($style == '<') {
$styles[$i] = 'text-left';
} elseif ($style == '>') {
$styles[$i] = 'text-right';
} elseif ($style == '-') {
$styles[$i] = 'text-center';
} elseif ($style == '=') {
$styles[$i] = 'text-justify';
} else {
$styles[$i] = '';
$start = 0;
}

$output .= '<th class="' . $styles[$i] . '" ' . $this->getCSS('th', $css, $i) . ' >';
$output .= (isset($html['th']) && $html['th'] === true ? Tools::substr($header, $start) : Tools::safeOutput(Tools::substr($header, $start)));
$output .= '</th>';

$i++;
}
$output .= '</tr></thead>';
}
if (count($rows) > 0) {
$output .= '<tbody ' . $this->getCSS('tbody', $css) . '>';

$odd = 0;
foreach ($rows as $row) {
$output .= '<tr class="' . (($odd++ % 2) == 0 ? 'odd' : '') . '" ' . $this->getCSS('tbody > tr', $css) . '>';

$i = 0;
foreach ($row as $value) {
$output .= '<td class="' . (isset($styles[$i]) ? $styles[$i] : '') . '" ' . $this->getCSS('td', $css, $i) . ' >';
$output .= (isset($html[$i]) && $html[$i] === true ? $value : Tools::safeOutput($value));
$output .= '</td>';
$i++;
}
$output .= '</tr>';
}
$output .= '</tbody>';
}

$output .= '</table></div></div>';
return $output;
}*/
}
