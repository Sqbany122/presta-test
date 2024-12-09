<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';
require_once dirname(__FILE__) . '/../../common/OrderRetention.php';

class AdminUpsArchivedOrdersController extends CommonController
{
    public function __construct()
    {
        $this->bootstrap   = true;
        $this->table       = 'order';
        $this->lang        = false;
        $this->addRowAction(' ');

        parent::__construct();

        $orders       = _DB_PREFIX_ . 'orders';
        $order_detail = _DB_PREFIX_ . 'order_detail';
        $product_lang = _DB_PREFIX_ . 'product_lang';
        $address      = _DB_PREFIX_ . 'address';
        $openorder    = _DB_PREFIX_ . 'ups_openorder';

        $id_lang = (int) $this->context->language->id;

        $this->_join = "
            LEFT JOIN  $openorder    op ON op.id_order      = a.id_order
            LEFT JOIN  $order_detail od ON od.id_order      = a.id_order
            INNER JOIN $product_lang pl ON (pl.id_product   = od.product_id AND pl.id_lang = $id_lang)
            LEFT JOIN  $address      ad ON ad.id_address    = a.id_address_delivery
        ";

        $sub_query = "
            SELECT GROUP_CONCAT(
                CONCAT(
                    od.product_quantity,
                    ' x ',
                    IF (CHARACTER_LENGTH(pl.name) > '30', CONCAT(left(pl.name,27) , '...'), pl.name),
                    '<br>')
                SEPARATOR '')
            FROM $orders o
            LEFT JOIN $order_detail od ON od.id_order = o.id_order
            INNER JOIN $product_lang pl ON pl.id_product = od.product_id AND pl.id_lang = 1
            WHERE (od.id_order = a.id_order) GROUP BY 'all'
        ";

        $this->_select = "
            a.id_order as id_order,
            DATE(a.date_add) as order_date,
            TIME(a.date_add) as order_time,
            ($sub_query) as product_name,

            CONCAT_WS(
                '<br>',
                IF (CHARACTER_LENGTH(ad.address1) > '35', CONCAT(left(ad.address1,32) , '...'), ad.address1),
                IF (CHARACTER_LENGTH(ad.address2) > '35', CONCAT(left(ad.address2,32) , '...'), ad.address2),
                IF (CHARACTER_LENGTH(ad.city) > '35', CONCAT(left(ad.city,32) , '...'), ad.city)
            ) as address1,

            CONCAT_WS(
                '<br>',
                IF (CHARACTER_LENGTH(op.ap_address1) > '35', CONCAT(left(op.ap_address1,32) , '...'), op.ap_address1),
                IF (CHARACTER_LENGTH(op.ap_address2) > '35', CONCAT(left(op.ap_address2,32) , '...'), op.ap_address2),
                IF (CHARACTER_LENGTH(op.ap_city) > '35', CONCAT(left(op.ap_city,32) , '...'), op.ap_city)
            ) as ap_address1,

            op.shipping_service as shipping_service,
            op.accessorials_service as accessorials_service,
            a.current_state as current_state,
            a.module as cod
        ";

        $idCarriers = CommonFunction::getIdsCarrierByReference(Configuration::get('UPS_SHIPING_METHOD_REFERENCE_ID'));
        $this->strIdCarriers = (!empty($idCarriers)) ? implode(",", $idCarriers) : -1;

        $this->_group = 'GROUP BY a.id_order';
        $this->_where = "AND op.status = " . Constants::STATUS_ARCHIVED_ORDERS
                        . " AND a.current_state IN ( "
                            . Constants::STATUS_AWAITING_CHECK_PAYMENT . ','
                            . Constants::STATUS_PAYMENT_ACCEPTED . ','
                            . Constants::STATUS_PROCESSING_IN_PROGRESS . ','
                            . Constants::STATUS_ORDER_PAID . ','
                            . Constants::STATUS_PAYMENT_ERROR . ','
                            . Constants::STATUS_AWAITING_BANK_WIRE_PAYMENT . ','
                            . Constants::STATUS_REMOTE_PAYMENT_ACCEPTED . ','
                            . Constants::STATUS_ORDER_NOT_PAID . ','
                            . Constants::STATUS_COD_VALIDATION . ')'
                        . " AND a.id_carrier " . " IN (" . $this->strIdCarriers . ")";
        
        // Button Bulk Action
        $this->bulk_actions = array(
            'noname' => array(
                'text' => '',
                'callback' => '',
            ),
        );

        $this->fields_list   = $this->createFieldsList();
        $this->tpl_list_vars = array(
            'sql'          => false,
            'show_filters' => true,
        );
    }

    public function setMedia($isNewTheme = false)
    {
        $sql = new DbQuery();
        $sql->select('o.id_order as id');
        $sql->leftJoin(Constants::DB_TABLE_OPENORDER, 'op', 'op.id_order = o.id_order');
        $sql->from('orders', 'o');

        $sql->where("op.status = '0'");

        $orderId  = Db::getInstance()->executeS($sql);
        $countRow = 0;

        if ($orderId) {
            $countRow = count($orderId);
            Media::addJsDef(array(
                'orderID'  => $orderId[0]['id'],
                'countRow' => $countRow,
            ));
        } else {
            Media::addJsDef(array(
                'countRow' => $countRow,
            ));
        }

        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upsarchivedorder.js');
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/upsmodule.css');
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initContent()
    {
        $retention = new OrderRetention();
        $retention->deleletOrderMoreThan90Days();

        parent::initContent();
    }

    public function sqlSelectOrder($id_order)
    {
        // Get id
        $id_lang = (int) $this->context->language->id;

        $sql = new DbQuery();

        // Order
        $sql->select('o.id_order as id_order');
        $sql->select('DATE(o.date_add) as order_date');
        $sql->select('TIME(o.date_add) as order_time');
        $sql->select('o.module as cod');
        $sql->select('o.current_state as current_state');
        $sql->select('o.total_paid as total_paid');
        $sql->select('o.total_products as total_products');
        $sql->select('cr.iso_code as currency');

        // OpenOrder
        $sql->select('op.shipping_service as shipping_service');
        $sql->select('op.ap_name as ap_name');
        $sql->select('op.ap_address1 as ap_address1');
        $sql->select('op.ap_address2 as ap_address2');
        $sql->select('op.ap_state as ap_state');
        $sql->select('op.ap_postcode as ap_postcode');
        $sql->select('op.ap_city as ap_city');
        $sql->select('op.accessorials_service as accessorials_service');

        // Order detail
        $sql->select('od.product_quantity as product_quantity');

        // Product lang
        $sql->select('pl.name as product_name');

        // Customer
        $sql->select('c.lastname as lastname');
        $sql->select('c.firstname as firstname');
        $sql->select('c.email as email');

        // Address
        $sql->select('ad.address1 as address_delivery1');
        $sql->select('ad.address2 as address_delivery2');
        $sql->select('ad.postcode as postcode');
        $sql->select('ad.city as city');
        $sql->select('ct.iso_code as country_code');
        $sql->select('ad.phone as phone');
        $sql->select('ad.phone_mobile as phone_mobile');

        // Country lang
        $sql->select('cl.name as country_name');

        $sql->from('orders', 'o');
        $sql->leftJoin(Constants::DB_TABLE_OPENORDER, 'op', 'op.id_order = o.id_order');
        $sql->leftJoin('order_detail', 'od', 'od.id_order = o.id_order');
        $sql->innerJoin('product_lang', 'pl', 'pl.id_product = od.product_id AND pl.id_lang = ' . (int) $id_lang);
        $sql->innerJoin('currency', 'cr', 'cr.id_currency = o.id_currency');
        $sql->innerJoin('address', 'ad', 'ad.id_address = o.id_address_delivery');
        $sql->innerJoin('country', 'ct', 'ct.id_country = ad.id_country');
        $sql->leftJoin('country_lang', 'cl', 'ad.id_country = cl.id_country AND cl.id_lang = ' . (int) $id_lang);
        $sql->innerJoin('customer', 'c', 'c.id_customer = ad.id_customer');
        $sql->where('o.id_order = ' . (int) $id_order);

        return Db::getInstance()->executeS($sql);
    }

    private function createFieldsList()
    {
        $fieldsList = array(
            'id_order'         => array(
                'title'          => $this->sdk->t('colname', 'txtOrderId'),
                'align'          => 'text-center',
                'class'          => 'fixed-width-xs',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'unArchivedOrderID'
            ),
            'order_date'       => array(
                'title'          => $this->sdk->t('colname', 'txtOrderDate'),
                'align'          => 'text-center',
                'search'         => false,
                'type'           => 'date',
                'callback'       => 'showShipmentModal',
                'remove_onclick' => true,
                'onclick'        => 'showShipmentModal',
            ),
            'order_time'       => array(
                'title'          => $this->sdk->t('colname', 'txtOrderTime'),
                'align'          => 'text-center',
                'search'         => false,
                'callback'       => 'showShipmentModal',
                'remove_onclick' => true,
            ),
            'product_name'     => array(
                'title'          => $this->sdk->t('colname', 'txtProduct'),
                'search'         => false,
                'callback'       => 'showShipmentModal',
                'remove_onclick' => true,
            ),
            'address1'         => array(
                'title'          => $this->sdk->t('colname', 'txtDeliveryAdd'),
                'search'         => false,
                'callback'       => 'showDeliveryAddress',
                'remove_onclick' => true,
            ),
            'shipping_service' => array(
                'title'          => $this->sdk->t('colname', 'txtShippingService'),
                'align'          => 'text-center',
                'search'         => false,
                'callback'       => 'displayShipingServiceName',
                'remove_onclick' => true,
            ),
            'cod'              => array(
                'title'          => $this->sdk->t('colname', 'txtCod'),
                'align'          => 'text-center',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'printCODIcon',
            ),
            ''                 => array(
                'title'  => 'hidden',
                'search' => false,
                'class'  => 'hidden',
                'id'     => 'hidden',
            ),
        );

        return $fieldsList;
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if ($this->display != 'list') {
            $this->page_header_toolbar_btn['un-archive_orders'] = array(
                'desc' => $this->sdk->t('button', 'un_archive_orders'),
                'icon' => 'process-icon-cancel',
                'js'   => 'unArchiveOrders();',
            );
        }
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsArchivedOrders');
    }

    public function unArchivedOrderID($value)
    {
        return $value;
    }

    public function printCODIcon($value, $order)
    {
        if ($value == Constants::PS_COD_MODULE) {
            return '<p class="action-disabled"
                        onclick="showOrderArchivedModal(' . $order["id_order"] . ');">
                        <i class="icon-check"></i>
                    </p>';
        } else {
            return '<p class="action-disabled"
                        onclick="showOrderArchivedModal(' . $order["id_order"] . ');">
                        <i class="icon-remove"></i>
                    </p>';
        }
    }

    public function displayShipingServiceName($key, $order)
    {
        $serviceName        = $this->module::$shippingServices->getServiceNameByKey($key);

        $serviceType        = $this->module->checkServiceType($key);
        $serviceTypeDisplay = ($serviceType == 'AP') ? "To AP" : "To Address";

        return '<p class="action-disabled"
                onclick="showOrderArchivedModal(' . $order["id_order"] . ');">
                ' . $serviceTypeDisplay . ' - ' . $serviceName . '
            </p>';
    }

    public function initModal()
    {
        $texts = array(
            'txtArcOrder'              => $this->sdk->t('colname', 'txtOrderId') . ':',
            'txtCustomer'              => $this->sdk->t('colname', 'txtCustomer') . ':',
            'txtArcProduct'            => $this->sdk->t('colname', 'txtProduct') . ':',
            'txtOpenAccessPoint'       => $this->sdk->t('colname', 'txtAccessPoint') . ':',
            'txtArcAddress'            => $this->sdk->t('address', 'txtAddress') . ':',
            'txtAccPhoneNumber'        => $this->sdk->t('colname', 'txtPhoneNumber') . ':',
            'txtAccEmail'              => $this->sdk->t('colname', 'txtEmail') . ':',
            'txtArcShippingService'    => $this->sdk->t('colname', 'txtShippingService') . ':',
            'txtArcAccessorialService' => $this->sdk->t('colname', 'txtAccessorialService') . ':',
            'txtArcOrderValue'         => $this->sdk->t('colname', 'txtOrderValue') . ':',
            'txtArcPaymentStatus'      => $this->sdk->t('colname', 'txtPaymentStatus') . ':',
            'txtArcOk'                 => $this->sdk->t('button', 'txtOk'),
        );
        $this->context->smarty->assign(array(
            'arrtext' => $texts,
        ));

        $this->modals[] = array(
            'modal_id'      => 'modalOrderArchited',
            'modal_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name .
                '/views/templates/admin/ups_archived_orders/viewDetailArchitedOrder.tpl'),
        );
    }

    public function ajaxProcessGetOrderById()
    {
        $id_order = Tools::getValue('orderID');
        $order    = $this->sqlSelectOrder($id_order);

        if (!empty($order[0]['shipping_service'])) {
            $order[0]['shipping_service'] = $this->module::$shippingServices->getServiceNameByKey(
                $order[0]['shipping_service']
            );
        } else {
            $order[0]['shipping_service'] = '';
        }

        if (isset($order[0]['accessorials_service']) && !empty($order[0]['accessorials_service'])) {
            $arrayAccessorial = unserialize($order[0]['accessorials_service']);

            if (!empty($arrayAccessorial)) {
                $order[0]['accessorials_service'] = $this->module->getListNamesAccessorial($arrayAccessorial);
            } else {
                $order[0]['accessorials_service'] = '';
            }
        } else {
            $order[0]['accessorials_service'] = '';
        }

        $order[0]['current_state'] = 'Canceled';

        $this->ajaxDie(json_encode($order));
    }

    public function showShipmentModal($value, $order)
    {
        return '<p class="' . ('action-disabled') . '"
                    onclick="showOrderArchivedModal(' . $order["id_order"] . ');">
                    ' . $value .
                '</p>';
    }

    public function showDeliveryAddress($value, $order)
    {
        $displayValue = '';
        if (strpos($order['shipping_service'], '_AP_') !== false) {
            $displayValue = $order['ap_address1'];
        } else {
            $displayValue = $value;
        }
        return '<p class="action-disabled"
                    onclick="showOrderArchivedModal(' . $order["id_order"] . ');">
                    ' . nl2br($displayValue) .
            '</p>';
    }

    public function ajaxProcessGetText()
    {
        $textWarning = $this->sdk->t('openorder', 'txtWarningUnarchive');
        $this->ajaxDie(json_encode($textWarning));
    }

    public function ajaxProcessUnarchiveOrders()
    {
        $orderID = trim(Tools::getValue('orderID'));
        $unarchive = $this->unArchivedOrder($orderID);
        $this->ajaxDie(json_encode($unarchive));
    }

    public function unArchivedOrder($orderID)
    {
        if (empty($orderID)) {
            return false;
        }
        $retention = new OrderRetention();
        $retention->unArchivedOrders($orderID);
    }
}
