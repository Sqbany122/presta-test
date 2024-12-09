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

class AdminUpsOpenOrdersController extends CommonController
{
    const DID_NOT_EDIT        = '';
    protected $statuses_array = array();

    private $primaryInfo = array();
    private $accountInfo = array();

    private $tblOrders      = _DB_PREFIX_ . 'orders';
    private $tblOrderDetail = _DB_PREFIX_ . 'order_detail';
    private $tblProductLang = _DB_PREFIX_ . 'product_lang';
    private $tblAddress     = _DB_PREFIX_ . 'address';
    private $tblOpenOrder   = _DB_PREFIX_ . Constants::DB_TABLE_OPENORDER;

    private $strIdCarriers = '';
    public $arrayYesNo     = array();

    public function __construct()
    {
        $this->bootstrap   = true;
        $this->table       = 'order';
        $this->lang        = false;
        $this->addRowAction(' ');

        parent::__construct();
        // retry all API fail in ps_ups_retry_api table

        $id_lang = (int) $this->context->language->id;

        $this->_join = "
            LEFT JOIN  " . pSQL($this->tblOpenOrder) . " op ON op.id_order = a.id_order
            LEFT JOIN  " . pSQL($this->tblOrderDetail) . " od ON od.id_order = a.id_order
            INNER JOIN " . pSQL($this->tblProductLang) . " pl ON pl.id_product = od.product_id AND
            pl.id_lang = " . (int) $id_lang . "
            LEFT JOIN  " . pSQL($this->tblAddress) . "      ad ON ad.id_address = a.id_address_delivery
        ";

        $sub_query = "
            SELECT GROUP_CONCAT(
                CONCAT(
                    od.product_quantity,
                    ' x ',
                    IF (CHARACTER_LENGTH(pl.name) > '35', CONCAT(left(pl.name,32) , '...'), pl.name),
                    '<br>')
                SEPARATOR '')
            FROM " . pSQL($this->tblOrders) . " o
            LEFT JOIN " . pSQL($this->tblOrderDetail) . " od ON od.id_order = o.id_order
            INNER JOIN " . pSQL($this->tblProductLang) . " pl ON pl.id_product = od.product_id AND
            pl.id_lang = " . (int) $id_lang . "
            WHERE (od.id_order = a.id_order) GROUP BY 'all'
        ";

        $this->_select = "
            a.id_order as id_order,
            DATE(a.date_add) as date_add,
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

        /**
         * CONDITION-UPDATING
         * Still UPDATING on the condition
         * UPS
         * #22-08-2018
         */
        $this->_where = "AND op.status = " . Constants::STATUS_OPEN_ORDER . " AND a.current_state IN ( "
                            . Constants::STATUS_AWAITING_CHECK_PAYMENT . ','
                            . Constants::STATUS_PAYMENT_ACCEPTED . ','
                            . Constants::STATUS_PROCESSING_IN_PROGRESS . ','
                            . Constants::STATUS_ORDER_PAID . ','
                            . Constants::STATUS_PAYMENT_ERROR . ','
                            . Constants::STATUS_AWAITING_BANK_WIRE_PAYMENT . ','
                            . Constants::STATUS_REMOTE_PAYMENT_ACCEPTED . ','
                            . Constants::STATUS_ORDER_NOT_PAID . ','
                            . Constants::STATUS_COD_VALIDATION .
                        ") AND a.id_carrier " . " IN (" . $this->strIdCarriers . ")";

        // Button Bulk Action
        $this->bulk_actions = array(
            'noname' => array(
                'text' => '',
            ),
        );

        $this->arrayYesNo = array(
            '0' => $this->sdk->t('shipment', 'txtNo'),
            '1' => $this->sdk->t('shipment', 'txtYes'),
        );

        $this->fields_list = $this->createFieldsList();

        $this->tpl_list_vars = array(
            'sql'          => false,
            'show_filters' => true,
        );

        $this->primaryInfo = $this->getPrimaryInfo();
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsOpenOrders');
    }

    public function initContent()
    {
        $retention = new OrderRetention();
        $retention->archivedOrderMoreThan90Days();

        parent::initContent();
    }

    public function initProcess()
    {
        if (Tools::getIsset('ExportAllOrders')) {
            $this->action = 'ExportAllOrders';
        }

        parent::initProcess();
    }

    public function postProcess()
    {
        parent::postProcess();
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
            ),
            'date_add'         => array(
                'title'          => $this->sdk->t('colname', 'txtOrderDate'),
                'type'           => 'date',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'showOpenOrderModal',
            ),
            'order_time'       => array(
                'title'          => $this->sdk->t('colname', 'txtOrderTime'),
                'type'           => 'time',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'showOpenOrderModal',
            ),
            'product_name'     => array(
                'title'          => $this->sdk->t('colname', 'txtProduct'),
                'class'          => 'fixed-width-xxl',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'showOpenOrderModal',
            ),
            'address1'         => array(
                'title'          => $this->sdk->t('colname', 'txtDeliveryAdd'),
                'class'          => 'fixed-width-xxl',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'showDeliveryDress',
            ),
            'shipping_service' => array(
                'title'          => $this->sdk->t('colname', 'txtShippingService'),
                'align'          => 'text-center',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'displayShipingServiceName',
            ),
            'cod'              => array(
                'title'          => $this->sdk->t('colname', 'txtCod'),
                'align'          => 'text-center',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'printCODIcon',
            ),
        );

        return $fieldsList;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if ($this->display != 'list') {
            $this->page_header_toolbar_btn['new_single_shipments'] = array(
                'desc' => $this->sdk->t('button', 'create_single_shipments'),
                'icon' => 'process-icon-new',
                'js'   => 'showCreateShipmentModal();',
            );

            $this->page_header_toolbar_btn['new_batch_shipments'] = array(
                'desc'  => $this->sdk->t('button', 'create_batch_shipments'),
                'icon'  => 'process-icon-new',
                'js'    => 'showBatchShipmentModal();',
                'class' => 'process-icon-partialreturn',
            );

            $this->page_header_toolbar_btn['export_all_orders'] = array(
                'desc' => $this->sdk->t('button', 'export_all_orders'),
                'href' => self::$currentIndex . '&ExportAllOrders&token=' . $this->token,
                'icon' => 'process-icon-download',
            );

            $this->page_header_toolbar_btn['export_open_orders'] = array(
                'desc' => $this->sdk->t('button', 'export_open_orders'),
                'icon' => 'process-icon-save-date',
            );

            $this->page_header_toolbar_btn['archive_orders'] = array(
                'desc' => $this->sdk->t('button', 'archive_orders'),
                'icon' => 'process-icon-cancel',
                'js'   => 'archiveOrders();',
            );
        }
    }

    public function ajaxProcessArchiveOrders()
    {
        $orderID = Tools::getValue('orderID');
        $archive = $this->archiveOrders($orderID);
        $this->ajaxDie(json_encode($archive));
    }

    public function archiveOrders($orderBox)
    {
        if (!isset($orderBox)) {
            return false;
        }

        $arrOrderBox = explode(',', $orderBox);

        foreach ($arrOrderBox as $orderId) {
            $this->sqlArchiveOrder($orderId);
        }
    }

    public function ajaxProcessExportOpenOrder()
    {
        $orderIDs = Tools::getValue('orderID');

        if ($orderIDs) {
            $this->context->cookie->__set('orderID', $orderIDs);
        }

        $this->ajaxDie(json_encode($orderIDs));
    }

    public function processExport($text_delimiter = '"')
    {
        $orderBox = $this->context->cookie->orderID;

        if (!$orderBox) {
            return;
        }

        $this->exportOrder($orderBox, $text_delimiter);
    }

    public function processExportAllOrders()
    {
        $arrOrderIds = $this->getAllOpenOrderIds();

        if (count($arrOrderIds) < 1) {
            $this->informations[] = $this->sdk->t('err-msg', 'no_record_export');
            return;
        }

        $arrIds = array();

        foreach ($arrOrderIds as $field) {
            $arrIds[] = $field['id_order'];
        }

        $orderIds = implode(',', $arrIds);

        $this->exportOrder($orderIds);
    }

    private function exportOrder($ids, $text_delimiter = '"')
    {
        $orders = $this->sqlExportOpenOrder($ids);

        if (empty($orders)) {
            return;
        }

        $headers  = $this->createHeaderCsv();
        $contents = $this->alterDataExport($orders);
        $this->module->putContents($headers, $contents, $text_delimiter, Constants::PREFIX_CSV_OPEN_ORDER);
        die; // DO NOT DELETE [IMPORTANT]
    }

    public function setMedia($isNewTheme = false)
    {
        $sql = new DbQuery();
        $sql->select('o.id_order as id');
        $sql->leftJoin(Constants::DB_TABLE_OPENORDER, 'op', 'op.id_order = o.id_order');
        $sql->from('orders', 'o');

        /**
         * CONDITION-UPDATING
         * Still UPDATING on the condition
         * UPS
         * #22-08-2018
         */
        $sql->where("op.status = '1' AND o.current_state IN ("
            . Constants::STATUS_AWAITING_CHECK_PAYMENT . ","
            . Constants::STATUS_PAYMENT_ACCEPTED . ","
            . Constants::STATUS_PROCESSING_IN_PROGRESS . ","
            . Constants::STATUS_ORDER_PAID . ","
            . Constants::STATUS_PAYMENT_ERROR . ","
            . Constants::STATUS_AWAITING_BANK_WIRE_PAYMENT . ","
            . Constants::STATUS_REMOTE_PAYMENT_ACCEPTED . ","
            . Constants::STATUS_ORDER_NOT_PAID . ","
            . Constants::STATUS_COD_VALIDATION .
            ") AND o.id_carrier " . " IN (" . $this->strIdCarriers . ")");

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
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/showModal.js');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upsopenorder.js');
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/upsopenorder.css');
    }

    /**
     * Refactor function GetOrderById
     * Updated at 27-09-2018
     */
    public function ajaxProcessGetOrdersByIds()
    {
        $returnOrders = array();
        $orderIds     = Tools::getValue('orderIds');
        $isBatch      = false;
        $isBatch      = Tools::getValue('createBatch');
        $returnOrders['errors'] = false;

        if (!empty($orderIds)) {
            $proccessingOrders = $this->getOrders($orderIds);

            if (!empty($proccessingOrders)) {
                if ($this->hasSameShippingMethods($proccessingOrders) || $isBatch) {
                    $firstOrder = $proccessingOrders[0];

                    $shippingService = '';
                    $shippingMethod  = '';
                    $accountDefault  = '';
                    $codAccessorial  = '';
                    $serviceTypeName = '';

                    $this->setServicesRelatedWith(
                        $firstOrder,
                        $shippingService,
                        $shippingMethod,
                        $accountDefault,
                        $codAccessorial,
                        $serviceTypeName
                    );

                    $accessorials      = $this->getAccessorialsIn($firstOrder, $codAccessorial);
                    $receiverAddresses = $this->getReceiverAddressesIn($proccessingOrders);
                    $account           = $this->getAccountByNumber($accountDefault);
                    $shipFromAddress   = $this->getShipFromAddressIn($account);

                    $returnOrders['defaultPackage']  = $this->getDefaultPackage();
                    $returnOrders['accounts']        = $this->formatAccountName();
                    $returnOrders['accountDefault']  = $accountDefault;
                    $returnOrders['shipFromName']    = isset($account['AccountName']) ?
                        $account['AccountName'] : '';
                    $returnOrders['shipFromAddress'] = $shipFromAddress;
                    $returnOrders['shipTo']          = $receiverAddresses;
                    $returnOrders['serviceType']     = $shippingMethod; // AP / ADD
                    $returnOrders['serviceTypeName'] = $serviceTypeName;
                    $returnOrders['accessorials']    = $accessorials;
                    $returnOrders['shippingService'] = $shippingService;
                    $returnOrders['firstOrder']      = $firstOrder;
                    $returnOrders['currency']        = $firstOrder['currency'];
                    $returnOrders['currentState']    = $this->getCurrentState($firstOrder['current_state']);
                    $returnOrders['txtCustomPackage']      = $this->sdk->t('openorder', 'custom_package');
                    $returnOrders['tipAdditionalHandling'] = $this->sdk->t('openorder', 'tipAdditionalHandling');
                    
                    if ($this->module->usa()) {//Chi ap dung cho US
                        $returnOrders['tipResidentialAddress'] = $this->sdk->t('openorder', 'tipResidentialAddressUS');
                    } else {
                        $returnOrders['tipResidentialAddress'] = $this->sdk->t('openorder', 'tipResidentialAddress');
                    }
                    $returnOrders['txtTooltipToHomeCOD']   = $this->sdk->t('openorder', 'txtTooltipToHomeCOD');

                    // In case have address selected when edit
                    if (Tools::getValue('wannaEdit')) {
                        $orderIdAdressSelected = Tools::getValue('selectedAddress');
                        if ($orderIdAdressSelected != '') {
                            $returnOrders['addOrderSelected'] = $this->getAddressSelected($orderIdAdressSelected);
                        }
                        $returnOrders['listCountry'] = $this->getCountries();
                        $returnOrders['listShippingService'] = $this->module::$shippingServices->getServicePairs(
                            $shippingMethod
                        );
                        $returnOrders['serviceAmong'] = count($returnOrders['listShippingService']);
                        $returnOrders['listAccessorialService'] =
                            $this->module::$accessorials->getAccessorialsWithoutCod();
                        $returnOrders['isUSA'] = $this->module->usa();

                        if ($shippingMethod == 'AP') {
                            $returnOrders['ttlAddress'] = $firstOrder['ap_name'];
                        } else {
                            $returnOrders['ttlAddress'] = $firstOrder['firstname'] . ' ' . $firstOrder['lastname'];
                        }

                        $acc = $this->getCODAccessorial($firstOrder['shipping_service']);
                        $returnOrders['listAccessorialService'][$acc] = $this->module->getNameAccessorialByKey($acc);

                        if ($this->hasCOD($firstOrder)) {
                            $returnOrders['default_accessorials_service'][] = $acc;
                        } else {
                            $returnOrders['default_accessorials_service'][] = '';
                        }
                    }
                } else {
                    $returnOrders['errors'] = "Multiple orders with different Shipping methods (send to Access Point and to Address) can not be merged";
                }
            }
        } else {
            $returnOrders['errors'] = $this->sdk->t('err-msg', 'selectRequest');
        }

        $this->ajaxDie(json_encode($returnOrders));
    }

    private function getCountries()
    {
        $countries = Country::getCountries($this->context->language->id);
        $result = array();
        foreach ($countries as $country) {
            $result[$country['id_country']] = $country['name'];
        }
        return $result;
    }

    public function getAddressSelected($orderId)
    {
        $addSelected = array();
        $orderAddressSelected = $this->sqlSelectOrder($orderId);

        $addSelected['address_title']     = $orderAddressSelected[0]['firstname'] . ' ' .
            $orderAddressSelected[0]['lastname'];
        $addSelected['address_delivery1'] = $orderAddressSelected[0]['address_delivery1'];
        $addSelected['address_delivery2'] = $orderAddressSelected[0]['address_delivery2'];
        $addSelected['address_delivery3'] = '';
        $addSelected['postcode']          = $orderAddressSelected[0]['postcode'];
        $addSelected['city']              = $orderAddressSelected[0]['city'];
        $addSelected['phone']             = $orderAddressSelected[0]['phone'];
        $addSelected['email']             = $orderAddressSelected[0]['email'];
        $addSelected['country']           = $orderAddressSelected[0]['country_name'];
        $addSelected['state']             = $orderAddressSelected[0]['state_name'];

        return $addSelected;
    }

    public function getShipFromAddressIn($account)
    {
        $shipFromAddress = '';
        if (!empty($account)) {
            $shipFromAddress = $account['AddressLine1'];
            if (isset($account['AddressLine2']) && !empty($account['AddressLine2'])) {
                $shipFromAddress .= ' <br /> ' . $account['AddressLine2'];
            }
            if (isset($account['AddressLine3']) && !empty($account['AddressLine3'])) {
                $shipFromAddress .= ' <br />' . $account['AddressLine3'];
            }

            if (isset($account['City']) && !empty($account['City'])) {
                $shipFromAddress .= ' <br />' . $account['City'];
            }

            if (isset($account['PostalCode']) && !empty($account['PostalCode'])) {
                $shipFromAddress .= ' <br />' . $account['PostalCode'];
            }
            //StateProvinceName
            if (isset($account['StateProvinceName']) && !empty($account['StateProvinceName'])) {
                $shipFromAddress .= ' <br />' . $account['StateProvinceName'];
            }

            if (isset($account['Country']) && !empty($account['Country'])) {
                $shipFromAddress .= ' <br />' . $account['Country'];
            }
            if (isset($account['PhoneNumber']) && !empty($account['PhoneNumber'])) {
                $shipFromAddress .= '<br />' . $account['PhoneNumber'];
            }
        }
        return $shipFromAddress;
    }

    public function showOpenOrderModal($value, $order)
    {
        return '<p class="action-disabled"
                    onclick="showViewOpenOrderModal(' . $order["id_order"] . ');">
                    ' . nl2br($value) .
            '</p>';
    }

    public function showDeliveryDress($value, $order)
    {
        $displayValue = '';
        if (strpos($order['shipping_service'], '_AP_') !== false) {
            $displayValue = $order['ap_address1'];
        } else {
            $displayValue = $value;
        }
        return '<p class="action-disabled"
                    onclick="showViewOpenOrderModal(' . $order["id_order"] . ');">
                    ' . nl2br($displayValue) .
            '</p>';
    }

    public function printCODIcon($value, $order)
    {
        if ($value == Constants::PS_COD_MODULE) {
            return '<p class="action-disabled"
                onclick="showViewOpenOrderModal(' . $order["id_order"] . ');">
                <i class="icon-check"></i>
            </p>';
        } else {
            return '<p class="action-disabled"
                onclick="showViewOpenOrderModal(' . $order["id_order"] . ');">
                <i class="icon-remove"></i>
                </p>';
        }
    }

    public function displayShipingServiceName($value, $order)
    {
        $serviceName        = $this->module::$shippingServices->getServiceNameByKey($value);
        $serviceType        = $this->module->checkServiceType($value);
        $serviceTypeDisplay = ($serviceType == 'AP') ? "To AP" : "To Address";

        return '<p class="action-disabled"
                onclick="showViewOpenOrderModal(' . $order["id_order"] . ');">
                ' . $serviceTypeDisplay . ' - ' . $serviceName . '
            </p>';
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initModal()
    {
        $sdk = $this->sdk;
        $email = $sdk->t('colname', 'txtEmail');
        $addr = $sdk->t('address', 'txtAddress');
        $phone = $sdk->t('colname', 'txtPhoneNumber');
        $packageNo = $sdk->t('openorder', 'txtOpenPackage');

        $txtE_ShippingMethod = $sdk->t('ups', 'txtE_ShippingMethod');
        if ($this->module->usa()) {
            $txtE_ShippingMethod = $sdk->t('ups', 'txtE_ShippingMethodUPS');
        }

        $texts = array(
            'txtOpenOrder'                   => $sdk->t('colname', 'txtOpenOrder') . ' #',
            'txtCustomer'                    => $sdk->t('colname', 'txtCustomer') . ':',
            'txtArcProduct'                  => $sdk->t('colname', 'txtProduct') . ':',
            'txtArcAddress'                  => $addr . ':',
            'txtAccPhoneNumber'              => $phone . ':',
            'txtAccEmail'                    => $email . ':',
            'txtArcShippingService'          => $sdk->t('colname', 'txtShippingService') . ':',
            'txtOpenAccessPoint'             => $sdk->t('colname', 'txtAccessPoint') . ':',
            'txtArcAccessorialService'       => $sdk->t('colname', 'txtAccessorialService') . ':',
            'txtArcOrderValue'               => $sdk->t('colname', 'txtOrderValue') . ':',
            'txtOpenPaymentStatus'           => $sdk->t('colname', 'txtPaymentStatus') . ':',
            'txtOpenCOD'                     => $sdk->t('colname', 'txtCod'),
            'txtArcOk'                       => $sdk->t('button', 'txtOk'),
            'txtOpenPackage'                 => $packageNo,
            'txtOpenBrussels'                => $sdk->t('openorder', 'txtOpenBrussels'),
            'txtOpenPhoneNumber'             => $phone,
            'txtOpenEmail'                   => $email,
            'txtShipmentsAccessorialService' => $sdk->t('shipment', 'txtShipmentsAccessorialService'),
            'txtOpenPackaging'               => $sdk->t('openorder', 'txtOpenPackaging'),
            'txtOpenPackage1'                => $packageNo . '1',
            'txtPkgAddPackage'               => $sdk->t('button', 'txtAddPackage'),
            'txtOpenViewEstimated'           => $sdk->t('openorder', 'txtOpenViewEstimated'),
            'txtOpenLoading'                 => $sdk->t('openorder', 'txtOpenLoading'),
            'txtOpenEdit'                    => $sdk->t('button', 'txtEdit'),
            'txtOpenCreateShipment'          => $sdk->t('openorder', 'txtOpenCreateShipment'),
            'txtOpenAccountNumber'           => $sdk->t('openorder', 'txtOpenAccountNumber'),
            'txtOpenEstimatedShippingFee'    => $sdk->t('openorder', 'txtOpenEstimatedShippingFee') . ':',
            'txtOpenView'                    => $sdk->t('openorder', 'txtOpenView'),
            'txtOpenName'                    => $sdk->t('openorder', 'txtOpenName'),
            'txtOpenAddress'                 => $addr,
            'txtOpenMail'                    => $email,
            'txtOpenPhone'                   => $phone,
            'txtOpenPostalCode'              => $sdk->t('address', 'txtPostalCode'),
            'txtOpenCountry'                 => $sdk->t('address', 'txtCountry'),
            'txtOpenState'                   => $sdk->t('address', 'txtState'),
            'txtOpenCity'                    => $sdk->t('address', 'txtCity'),
            'txtOpenNote'                    => $sdk->t('openorder', 'txtOpenNote'),
            'txtOpenCancelEditing'           => $sdk->t('button', 'txtCancel'),
            'txtOpenEditShipment'            => $sdk->t('button', 'txtEdit'),
            'txtOpenAccountNumber'           => $sdk->t('openorder', 'txtOpenAccountNumber'),
            'txtOpenShipFrom'                => $sdk->t('openorder', 'txtOpenShipFrom'),
            'txtOpenShipTo'                  => $sdk->t('openorder', 'txtOpenShipTo'),
            'txtE_ShippingMethod'            => $txtE_ShippingMethod,
            'country_empty'                  => $sdk->t('openorder', 'country_empty'),
            'format_phone_not_empty'         => $sdk->t('openorder', 'format_phone_not_empty'),
            'postal_empty_invalid'           => $sdk->t('openorder', 'postal_empty_invalid'),
            'txtE_AddressRequired'           => $sdk->t('ups', 'txtE_AddressRequired'),
            'email_empty'                    => $sdk->t('openorder', 'email_empty'),
            'city_empty'                     => $sdk->t('openorder', 'city_empty'),
            'name_empty'                     => $sdk->t('openorder', 'name_empty'),
            'txtToBeProcess'                 => $sdk->t('openorder', 'txtToBeProcess'),
        );

        if ($this->module->usa()) {
            $texts['txtShipmentsAccessorialServiceUS'] = $sdk->t('shipment', 'txtShipmentsAccessorialServiceUS');
            $texts['txtAfterAccessorialService'] = $sdk->t('shipment', 'txtAfterAccessorialService');
        }

        $packagesOptions = $this->displayPackageInfo();

        $arrCustomer      = $this->formatAccountName();
        $firstCustomerKey = 0;
        if ($arrCustomer) {
            $arrCustomerKey = array_keys($arrCustomer);
            if (isset($arrCustomerKey[0])) {
                $firstCustomerKey = $arrCustomerKey[0];
            }
        }

        $this->context->smarty->assign(array(
            'customersOptions'  => $arrCustomer,
            'customerID'        => 1,
            'defaultPackage'    => 'UPS_PKG_1_DIMENSION',
            'firstAccount'      => $firstCustomerKey,
            'packagesOptions'   => $packagesOptions,
            'tokenPackage'      => Tools::getAdminTokenLite('AdminUpsAddPackage'),
            'tokenBatchPackage' => Tools::getAdminTokenLite('AdminUpsAddPackageBatch'),
            'arrtext'           => $texts,
            'js_dir'            => _PS_JS_DIR_,
            'iconLoading'       => _MODULE_DIR_ . $this->module->name . '/views/img/wait.gif',
            'isUSA' => $this->module->usa(),
        ));

        $this->modals[] = array(
            'modal_id'      => 'modalViewDetail',
            'modal_class'   => 'modal-md',
            'modal_content' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/ups_open_orders/modalViewDetail.tpl'
            ),
        );

        $this->modals[] = array(
            'modal_id'      => 'modalSingleShipment',
            'modal_class'   => 'modal-md',
            'modal_title'   => $sdk->t('openorder', 'txtOpenProcessShipment'),
            'modal_content' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/ups_shipments/single_shipment.tpl'
            ),
        );

        $this->modals[] = array(
            'modal_id' => 'modalBatchShipment',
            'modal_class' => 'modal-md',
            'modal_title' => $sdk->t('openorder', 'txtCreateBatchShipment'),
            'modal_content' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/ups_shipments/batch_shipment.tpl'
            )
        );
    }

    /**
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function renderModal()
    {
        $modal_render = '';

        if (is_array($this->modals) && count($this->modals)) {
            foreach ($this->modals as $modal) {
                $this->context->smarty->assign($modal);
                $modal_render .= $this->context->smarty->fetch('modal.tpl');
            }
        }

        return $modal_render;
    }

    public function ajaxProcessOnChangeCustomer()
    {
        $shipperAccount = Tools::getValue('accountNumberShipment');
        $accountInfo    = array();
        if (!empty($shipperAccount)) {
            $accountInfo = $this->getAccountByNumber($shipperAccount);
        }

        $accountName     = '';
        $shipFromAddress = '';
        $accountName     = '';
        $accountName     = '';
        if (!empty($accountInfo)) {
            $accountName     = isset($accountInfo['AccountName']) ? $accountInfo['AccountName'] : '';
            $shipFromAddress = isset($accountInfo['AddressLine1']) ? $accountInfo['AddressLine1'] : '';

            if (isset($accountInfo['AddressLine2']) && !empty($accountInfo['AddressLine2'])) {
                $shipFromAddress .= '<br />' . $accountInfo['AddressLine2'];
            }
            if (isset($accountInfo['AddressLine3']) && !empty($accountInfo['AddressLine3'])) {
                $shipFromAddress .= '<br />' . $accountInfo['AddressLine3'];
            }

            if (isset($accountInfo['City']) && !empty($accountInfo['City'])) {
                $shipFromAddress .= '<br />' . $accountInfo['City'];
            }

            if (isset($accountInfo['PostalCode']) && !empty($accountInfo['PostalCode'])) {
                $shipFromAddress .= '<br />' . $accountInfo['PostalCode'];
            }

            if (isset($accountInfo['Country']) && !empty($accountInfo['Country'])) {
                $shipFromAddress .= '<br />' . $accountInfo['Country'];
            }

            if (isset($accountInfo['PhoneNumber']) && !empty($accountInfo['PhoneNumber'])) {
                $shipFromAddress .= '<br />' . $accountInfo['PhoneNumber'];
            }
        }
        // Default Account
        if (empty($accountName) && $this->module->checkDefaultAccount($accountInfo['isDefaultAccount'])) {
            // if default Account then get account name from merchant infor
            $this->primaryInfo = $this->getPrimaryInfo();
            if (isset($this->primaryInfo['CustomerName'])) {
                $accountName = $this->primaryInfo['CustomerName'];
            }
        }

        $accountInfo['AccountName']     = $accountName;
        $accountInfo['shipFromAddress'] = $shipFromAddress;
        $this->ajaxDie(json_encode($accountInfo));
    }

    public function ajaxProcessBatchShipment()
    {
        $pathImg = Media::getMediaPath(_PS_MODULE_DIR_ . $this->module->name);
        $checkMark = $pathImg . '/views/img/check.jpg';
        $crossMark = $pathImg . '/views/img/cross.png';

        $arrReturn = array();
        $arrReturn['error'] = '';
        $orderIds = Tools::getValue('batchOrderIds');
        $orderIds = explode(',', $orderIds);
        if ($orderIds) {
            $accountNumber = Tools::getValue('batchShipmentAccount');
            $orders = $this->getOrders($orderIds);

            if (!empty($orders)) {
                $jsonDefaultPackage = Configuration::get('UPS_PKG_1_DIMENSION');
                $defaultPackage = $jsonDefaultPackage ? unserialize($jsonDefaultPackage) : array();
                foreach ($orders as $order) {
                    $orderAccessorial = $order["accessorials_service"];
                    $tmpAccessorials = !empty($orderAccessorial) ? unserialize($orderAccessorial) : array();

                    if (isset($order['cod']) && $order['cod'] === Constants::PS_COD_MODULE) {
                        if ($this->module::$shippingServices->isShippingToHome($order['shipping_service'])) {
                            $tmpAccessorials[] = $this->module::$accessorials->getToHomeCod();
                        } else {
                            $tmpAccessorials[] = $this->module::$accessorials->getAccessPointCod();
                        }
                    }

                    $shippingMethod = $this->module->checkServiceType($order['shipping_service']);

                    $this->setDummyPhoneAndMail($shippingMethod, $order);

                    $result = $this->createShipment(
                        array(
                            'tmpOrderId'          => $order['id_order'],
                            'orders'              => array($order),
                            'firstOrder'          => $order,
                            'accountNumber'       => $accountNumber,
                            'packages'            => array($defaultPackage),
                            'accessorialKeys'     => $tmpAccessorials, // for insert
                            'accessorialsService' => $this->module->getAccessorialCodes($tmpAccessorials), // for api
                            'shippingServiceInfo' => $this->getShippingService($order['shipping_service']),
                            'isApCod'             => false,
                        )
                    );

                    if ($result['Code'] == 1) {
                        $arrReturn['resultList']["id_".$order['id_order']] = array(
                            'code' => 1,
                            'msg' => '',
                            'orderId' => $order['id_order'],
                            'icon' => "<img src='" . $checkMark . "' class='check-mark-size'/>",
                        );
                    } elseif ($result === 'API_ERR') {
                        $arrReturn['resultList']["id_".$order['id_order']] = array(
                            'code' => 0,
                            'msg' => 'There are errors connecting to the UPS API servers. Please try again',
                            'orderId' => $order['id_order'],
                            'icon' => "<img src='" . $crossMark . "' class='cross-mark-size'/>",
                        );
                    } else {
                        $arrReturn['resultList']["id_".$order['id_order']] = array(
                            'code' => 0,
                            'msg' => $result['Description'],
                            'orderId' => $order['id_order'],
                            'icon' => "<img src='" . $crossMark . "' class='cross-mark-size'/>",
                        );
                    }
                }
            }
        }

        $this->ajaxDie(json_encode($arrReturn));
    }

    public function ajaxProcessSaveSingleShipment()
    {
        $arrReturn = array();
        $orderId   = $this->getOrderId();

        if ($orderId) {
            $orders = $this->getOrders($orderId);

            if (!empty($orders)) {
                $accessorials              = array();
                $tmpAddr                   = array();
                $arrayError                = array();
                $arrReturn['error']        = '';
                $accountDefault            = '';
                $shippingService           = '';
                $shippingMethod            = '';
                $codAccessorial            = '';
                $serviceTypeName           = '';
                $shipToAddress             = Tools::getValue('shipToListAddress');
                $shippingServiceEdited     = Tools::getValue('shippingService');
                $accessorialsServiceEdited = Tools::getValue('accessorialsService');
                $idOrderSelected           = Tools::getValue('selectedAddress');
                $isApCod                   = false;
                $accessorialServiceCodes   = array();

                $firstOrder = $this->getFirstOrder($orders);

                if ($idOrderSelected) {
                    $this->processingAddressEdited($idOrderSelected, $tmpAddr, $firstOrder);
                } elseif ($shipToAddress) {
                    $tmpAddr = (array) json_decode($shipToAddress);
                }

                if (!empty($tmpAddr)) {
                    $this->setAddressEdited($tmpAddr, $firstOrder);
                }

                $this->setServicesRelatedWith(
                    $firstOrder,
                    $shippingService,
                    $shippingMethod,
                    $accountDefault,
                    $codAccessorial,
                    $serviceTypeName
                );

                if ($shippingServiceEdited === static::DID_NOT_EDIT) {
                    $accessorials        = $this->getAccessorialKeys($firstOrder, $codAccessorial);
                    $shippingServiceInfo = $this->getShippingService($firstOrder['shipping_service']);
                } else {
                    $accessorials        = $this->getAccessorialKeysEdited($accessorialsServiceEdited);
                    $shippingServiceInfo = $this->getShippingService($shippingServiceEdited);
                }

                if (!empty($accessorials)) {
                    $isApCod                 = in_array('UPS_ACSRL_ACCESS_POINT_COD', $accessorials);
                    $accessorialServiceCodes = $this->module->getAccessorialCodes($accessorials);
                }

                $this->setDummyPhoneAndMail($shippingMethod, $firstOrder);

                $packages = $this->getShipmentPackages(Tools::getValue('packageDetail'));

                if (empty($packages['errors']) && empty($arrayError)) {
                    $accountNumber = Tools::getValue('singleShipmentAccount');

                    $result = $this->createShipment(
                        array(
                            'tmpOrderId'          => $orderId,
                            'orders'              => $orders,
                            'firstOrder'          => $firstOrder,
                            'accountNumber'       => $accountNumber,
                            'packages'            => $packages['packages'],
                            'accessorialKeys'     => $accessorials,
                            'accessorialsService' => $accessorialServiceCodes,
                            'shippingServiceInfo' => $shippingServiceInfo,
                            'isApCod'             => $isApCod,
                        )
                    );

                    $createShipmentFailure = isset($result['error']) ? $result['error'] : array();

                    if ($result['Code'] == 'API_ERR') {
                        $arrReturn['error'] = $this->sdk->t('err-msg', 'message_error');
                    } elseif (empty($createShipmentFailure)) {
                        $orderIds = array_column($orders, 'id_order');
                        CommonFunction::sqlUpdateOrderStatus(Constants::STATUS_SHIPPED, $orderIds);
                        CommonFunction::sqlUpdateTrackingNumber($result['data'][0]['ShipmentId'], $orderIds);
                        $arrReturn['redirect'] = $this->context->link->getAdminLink('AdminUpsOpenOrders');
                    } else {
                        $arrReturn['error'] = $this->formatErrorMessage($createShipmentFailure);
                    }
                } else {
                    $fauilureMessages   = array_merge($arrayError, $packages['errors']);
                    $arrReturn['error'] = $this->formatErrorMessage($fauilureMessages);
                }
            }
        }
        $this->ajaxDie(json_encode($arrReturn));
    }

    public function ajaxProcessShipmentEstimated()
    {
        $orderId               = Tools::getValue('orderID');
        $allOrderId            = $this->getOrderId();
        $shippingService       = Tools::getValue('shippingService');
        $newAddressString      = (array) json_decode(Tools::getValue('newAdressString'));
        $newCountryId          = Tools::getValue('newCountryId');
        $firstOrder            = array();
        $shippingServiceEdited = array();
        $accessorialsService   = array();
        $countryIso            = '';
        $inforAllOrder         = array();

        $firstOrder = $this->getOrders($orderId)[0];
        $inforAllOrder = $this->getOrders($allOrderId);

        $selectedOrderId = (int) Tools::getValue('selectedAddress');

        if ($selectedOrderId > 0) { // Edited
            $selectedOrder = $this->getOrders($selectedOrderId)[0];

            $newAddressString = array(
                'newName'       => $selectedOrder['lastname'] . ' ' . $selectedOrder['firstname'],
                'newAddress1'   => $selectedOrder['address_delivery1'],
                'newAddress2'   => $selectedOrder['address_delivery2'],
                'newAddress3'   => '',
                'newPostalCode' => $selectedOrder['postcode'],
                'newCity'       => $selectedOrder['city'],
                'newCountry'    => $selectedOrder['country_code'],
                'newPhone'      => $selectedOrder['phone'],
                'newEmail'      => $selectedOrder['email'],
                'newState'      => $selectedOrder['state_code'],
            );

            if (!empty($selectedOrder['ap_city'])) {
                $newAddressString = array(
                    'newName'       => $selectedOrder['ap_name'],
                    'newAddress1'   => $selectedOrder['ap_address1'],
                    'newAddress2'   => $selectedOrder['ap_address2'],
                    'newAddress3'   => '',
                    'newPostalCode' => $selectedOrder['ap_postcode'],
                    'newCity'       => $selectedOrder['ap_city'],
                    'newCountry'    => $selectedOrder['country_code'],
                    'newPhone'      => $selectedOrder['phone'],
                    'newEmail'      => $selectedOrder['email'],
                    'newState'      => $selectedOrder['ap_state'],
                );
            }
        }

        // Check case not exist Newstate // Case Edit
        if (!empty($newAddressString) && !array_key_exists("newState", $newAddressString)) {
            $newAddressString['newState'] = '';
        }

        if ($newCountryId != '') {
            $countryIso = Country::getIsoById($newCountryId);
        }

        if ($shippingService) { // Edit
            $accessorialsService = Tools::getValue('accessorialsService');
            if (!empty($accessorialsService)) {
                $accessorialsService = array_unique($accessorialsService);
            }
            $shippingServiceEdited = $this->getRateCodeNameForShippingService($shippingService);
        } else { // Create
            if (!empty($firstOrder) && isset($firstOrder['accessorials_service'])) {
                if (!empty($firstOrder['accessorials_service'])) {
                    $accessorialsService = unserialize($firstOrder['accessorials_service']);
                }
            }

            if ($this->hasCOD($firstOrder)) {
                $shippingMethod = $this->module->checkServiceType($firstOrder['shipping_service']);
                if ($shippingMethod == 'AP') {
                    $accessorialsService[] = 'UPS_ACSRL_ACCESS_POINT_COD';
                } elseif ($shippingMethod == 'ADD') {
                    $accessorialsService[] = 'UPS_ACSRL_TO_HOME_COD';
                }
            }
        }
        $accessorialsService = $this->module->getAccessorialCodes($accessorialsService);

        $packages          = $this->getShipmentPackages(Tools::getValue('packageDetail'));
        $this->accountInfo = $this->getAccountByNumber(Tools::getValue('accountNumberShipment'));

        if (empty($packages['errors'])) {
            $response = $this->esimateTimeFee(
                $firstOrder,
                $inforAllOrder,
                $packages,
                $accessorialsService,
                $shippingServiceEdited,
                $newAddressString,
                $countryIso
            );
        } else {
            $response['error'] = $this->formatErrorMessage($packages['errors']);
        }

        $this->ajaxDie(json_encode($response));
    }

    private function esimateTimeFee(
        $firstOrder,
        $inforAllOrder,
        $packages,
        $accessorialsService,
        $shippingServiceEdited,
        $newAddressString,
        $countryIso
    ) {
        $cutOffTime = Configuration::get('UPS_SP_SERV_CUT_OFF_TIME');
        $pickupDate = $this->module->calculatePickupDate((int) $cutOffTime);
        $isApCod    = in_array('4', $accessorialsService);
        $arrReturnValue = array();

        $rateParams = $this->estimationFeeData(
            $firstOrder,
            $packages['packages'],
            $accessorialsService,
            $shippingServiceEdited,
            $newAddressString,
            $countryIso
        );

        $rateParams['ShipToAddress1'] = mb_convert_encoding(
            $rateParams['ShipToAddress1'],
            'UTF-8',
            'HTML-ENTITIES'
        );

        // Add more parameter required RATETIMEINTRANSIT
        $rateParams['RequestOption']            = Constants::RATE_API_REQ_OPT_TIME;
        $rateParams['PackageWeightCode']        = $packages['packages'][0]['weightUnit'];
        $rateParams['PackageWeightDescription'] = $packages['packages'][0]['weightUnit'];
        $rateParams['PackageWeightWeight']      = $packages['packages'][0]['weight'];
        $rateParams['MonetaryValue']            = (string) $this->totalValueOrder($inforAllOrder);
        $rateParams['CurrencyCode']             = $firstOrder['currency'];
        $rateParams['PickupDate']               = $pickupDate;
        $rateParams['isApCod']                  = $isApCod;

        $responseFee = $this->estimateFee($rateParams);

        // Alter reponseFee
        if ($responseFee['Description'] == 'Success') {
            // Alter reponse Time
            $ServiceSummary = $responseFee['TimeInTransit']->ServiceSummary;

            $date = $ServiceSummary->EstimatedArrival->Arrival->Date;
            $time = isset($ServiceSummary->EstimatedArrival->Arrival->Time)
                        ? $ServiceSummary->EstimatedArrival->Arrival->Time
                        : '000000';

            $arrReturnValue['error']               = '';
            $arrReturnValue['ShippingFee']         = $responseFee['ShippingFee'];
            $arrReturnValue['CurrencyCode']        = $responseFee['CurrencyCode'];
            $arrReturnValue['shippingDateArrival'] = date('Y-m-d H:i:s', strtotime($date . $time));
        } else {
            $arrReturnValue['error']               = $responseFee['Description'];
            $arrReturnValue['shippingFee']         = ' ';
            $arrReturnValue['CurrencyCode']        = ' ';
            $arrReturnValue['shippingDateArrival'] = ' ';
        }

        return $arrReturnValue;
    }

    private function totalValueOrder($inforAllOrder)
    {
        $totalValue = 0;
        foreach ($inforAllOrder as $inforOrder) {
            $totalValue += (float) $inforOrder['total_paid'];
        }

        return $totalValue;
    }

    public function getShipmentPackages($packages)
    {
        $packages = explode(';', $packages);
        $errors   = array();
        $txtPackage = $this->sdk->t('openorder', 'txtOpenPackage');

        foreach ($packages as $key => $packageDimension) {
            if (is_string($packageDimension) && strpos($packageDimension, "UPS_PKG") > -1) {
                $packageInfo = Configuration::get($packageDimension);
                if ($packageInfo) {
                    $packages[$key] = unserialize($packageInfo);
                } else {
                    $errors[] = $this->sdk->t('err-msg', 'pkgExist')
                        . ' '
                        . $txtPackage
                        . ': '
                        . ($key + 1);
                }
            } else { // check validate input
                if ($packageDimension) {
                    $packageDimension = json_decode($packageDimension);
                    foreach ($packageDimension as $keyWord => $element) {
                        if (in_array($keyWord, array(0, 1, 2, 4)) && !$this->validateAddPkgUnit($element)) {
                            $message = $this->sdk->t('err-msg', 'notValid');
                            if ($this->module->usa()) {
                                $message = $this->sdk->t('err-msg', 'notValidUS');
                            }
                            $errors[] = $message . ' ' . $txtPackage . ': ' . ($key + 1);
                            break;
                        }
                    }
                    $packages[$key] = array(
                        'id'         => $key,
                        'name'       => 'Custom Package ' . $key,
                        'lenght'     => $packageDimension[0],
                        'width'      => $packageDimension[1],
                        'height'     => $packageDimension[2],
                        'lenghtUnit' => $packageDimension[3],
                        'weight'     => $packageDimension[4],
                        'weightUnit' => $packageDimension[5],
                    );
                } else {
                    $message = $this->sdk->t('err-msg', 'notValid');
                    if ($this->module->usa()) {
                        $message = $this->sdk->t('err-msg', 'notValidUS');
                    }
                    $errors[] = $message . ' ' . $txtPackage . ': ' . ($key + 1);
                }
            }
        }

        if (!empty($errors)) {
            $errors   = array();
            $message = $this->sdk->t('err-msg', 'notValid');
            if ($this->module->usa()) {
                $message = $this->sdk->t('err-msg', 'notValidUS');
            }
            $errors[] = str_replace(' #', '', $txtPackage) . ': ' . $message;
        }

        return array(
            'errors'   => $errors,
            'packages' => $packages,
        );
    }

    // ======================== SQL FUNCTION ========================

    public function sqlArchiveOrder($orderId)
    {
        $sql = "UPDATE " . pSQL($this->tblOpenOrder)
                . " SET `status` = " . Constants::STATUS_ARCHIVED_ORDERS . ", `archived_at` = CURRENT_TIMESTAMP"
                . " WHERE id_order = " . "'" . pSQL($orderId) . "'";

        return Db::getInstance()->execute($sql);
    }

    /**
     * The function will be REMOVED at next verion.
     * For a new feature development, please use the function getOrders()
     * By UPS
     * Noted at 25/08/2018
     */
    public function sqlSelectOrder($id_order, $condition = 'equal')
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

        // OpenOrder
        $sql->select('op.shipping_service as shipping_service');
        $sql->select('op.ap_id as ap_id');
        $sql->select('op.ap_name as ap_name');
        $sql->select('op.ap_address1 as ap_address1');
        $sql->select('op.ap_address2 as ap_address2');
        $sql->select('op.ap_state as ap_state');
        $sql->select('op.ap_postcode as ap_postcode');
        $sql->select('op.ap_city as ap_city');
        $sql->select('op.accessorials_service as accessorials_service');

        // Order detail
        $sql->select('od.product_quantity as product_quantity');
        $sql->select('od.product_quantity as productQuantity');
        $sql->select('od.product_name as productName');

        // Product lang
        $sql->select('pl.name as product_name');

        // Customer
        $sql->select('ad.lastname as lastname');
        $sql->select('ad.firstname as firstname');
        $sql->select('c.email as email');

        // Address
        $sql->select('ad.address1 as address_delivery1');
        $sql->select('ad.address2 as address_delivery2');
        $sql->select('ad.postcode as postcode');
        $sql->select('ad.city as city');
        $sql->select('ct.iso_code as country_code');
        $sql->select('ad.phone as phone');
        $sql->select('ad.phone_mobile as phone_mobile');
        $sql->select('st.name as state_name');
        $sql->select('currency.iso_code as currency');
        $sql->select('st.iso_code as state_code');
        // Country lang
        $sql->select('cl.name as country_name');

        $sql->from('orders', 'o');
        $sql->leftJoin(Constants::DB_TABLE_OPENORDER, 'op', 'op.id_order = o.id_order');
        $sql->leftJoin('order_detail', 'od', 'od.id_order = o.id_order');
        $sql->innerJoin('product_lang', 'pl', 'pl.id_product = od.product_id AND pl.id_lang = ' . (int) $id_lang);
        $sql->innerJoin('address', 'ad', 'ad.id_address = o.id_address_delivery');
        $sql->innerJoin('currency', 'currency', 'currency.id_currency = o.id_currency');
        $sql->innerJoin('country', 'ct', 'ct.id_country = ad.id_country');
        $sql->leftJoin('country_lang', 'cl', 'ad.id_country = cl.id_country AND cl.id_lang = ' . (int) $id_lang);
        $sql->innerJoin('customer', 'c', 'c.id_customer = ad.id_customer');
        $sql->leftJoin('state', 'st', 'ad.id_state = st.id_state');

        switch ($condition) {
            case 'equal':
                $sql->where('o.id_order = ' . (int) $id_order);
                break;

            case 'in':
                $id_order = implode(',', array_map('intval', $id_order));
                $sql->where('o.id_order IN (' . $id_order . ')');
                break;

            default:
                $sql->where('o.id_order = ' . (int) $id_order);
        }

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Query Orders V2 on TESTING VERSION
     * By UPS
     * Created at 25/08/2018
     *
     * Fields were removed (please have a look at below comments).
     */
    public function getOrders($orderIds)
    {
        $languageId = (int) $this->context->language->id;

        if (is_array($orderIds)) {
            $orderIds = implode(',', array_map('intval', $orderIds));
        } else {
            $orderIds = (int) $orderIds;
        }

        $sql = new DbQuery();

        // Order
        $sql->select('o.id_order as id_order');
        $sql->select('DATE(o.date_add) as order_date');
        $sql->select('TIME(o.date_add) as order_time');
        $sql->select('o.module as cod');
        $sql->select('o.current_state as current_state');
        $sql->select('o.total_paid as total_paid');
        $sql->select('o.total_shipping as total_shipping');
        $sql->select('o.total_products as total_products');

        // OpenOrder
        $sql->select('op.shipping_service as shipping_service');
        $sql->select('op.ap_id as ap_id');
        $sql->select('op.ap_name as ap_name');
        $sql->select('op.ap_address1 as ap_address1');
        $sql->select('op.ap_address2 as ap_address2');
        $sql->select('op.ap_state as ap_state');
        $sql->select('op.ap_postcode as ap_postcode');
        $sql->select('op.ap_city as ap_city');
        $sql->select('op.accessorials_service as accessorials_service');

        // Products detail
        $supQuery = "(SELECT GROUP_CONCAT(CONCAT(od.product_quantity,' x ', od.product_name))
                    FROM " . pSQL($this->tblOrderDetail) . " `od` WHERE od.id_order = o.id_order) as products";

        $sql->select($supQuery);

        // Customer
        $sql->select('ad.lastname as lastname');
        $sql->select('ad.firstname as firstname');
        $sql->select('c.email as email');

        // Address
        $sql->select('ad.address1 as address_delivery1');
        $sql->select('ad.address2 as address_delivery2');
        $sql->select('ad.postcode as postcode');
        $sql->select('ad.city as city');
        $sql->select('ct.iso_code as country_code');
        $sql->select('ad.phone as phone');
        $sql->select('ad.phone_mobile as phone_mobile');
        $sql->select('cr.iso_code as currency');
        $sql->select('st.name as state_name');
        $sql->select('st.iso_code as state_code');

        // Country lang
        $sql->select('cl.name as country_name');

        $sql->from('orders', 'o');
        $sql->leftJoin(Constants::DB_TABLE_OPENORDER, 'op', 'op.id_order = o.id_order');
        $sql->innerJoin('address', 'ad', 'ad.id_address = o.id_address_delivery');
        $sql->leftJoin('country_lang', 'cl', 'ad.id_country = cl.id_country AND cl.id_lang = ' . (int) $languageId);
        $sql->innerJoin('currency', 'cr', 'cr.id_currency = o.id_currency');
        $sql->innerJoin('country', 'ct', 'ct.id_country = ad.id_country');
        $sql->innerJoin('customer', 'c', 'c.id_customer = ad.id_customer');
        $sql->leftJoin('state', 'st', 'st.id_state = ad.id_state');

        $sql->where('o.id_order IN (' . $orderIds . ')');
        $sql->orderBy('o.id_order ASC');

        return Db::getInstance()->executeS($sql);
    }

    public function sqlExportOpenOrder($ids)
    {
        $id_lang = (int) $this->context->language->id;

        $supQuery = "(SELECT GROUP_CONCAT(CONCAT(od.product_quantity,' x ', pl.name))
                    FROM " . pSQL($this->tblOrderDetail) . " `od`, "
                           . pSQL($this->tblProductLang) . " pl" .
                    " WHERE od.id_order = o.id_order
                            AND od.product_id = pl.id_product
                            AND pl.id_lang = " . (int) $id_lang . "
                    )
                 as products";

        $sql = new DbQuery();

        $sql->select('o.id_order as id_order');
        $sql->select('DATE(o.date_add) as order_date');
        $sql->select('TIME(o.date_add) as order_time');
        $sql->select('o.module as cod');

        $sql->select("'' as CODAmount");
        $sql->select("'' as CODCurrency");

        $sql->select('o.current_state as current_state');
        $sql->select('o.total_paid as total_paid');
        $sql->select('cr.iso_code as currency');
        $sql->select('o.total_products as total_products');
        $sql->select('op.shipping_service as shipping_service');
        $sql->select('op.accessorials_service as accessorials_service');
        $sql->select('"" as product_name');
        $sql->select($supQuery);

        $sql->select("'' as MerchantUPSaccountNumber");

        $sql->select('ad.lastname as lastname');
        $sql->select('ad.firstname as firstname');
        $sql->select('ad.address1 as address1');
        $sql->select('ad.address2 as address2');

        $sql->select("'' as address3");

        $sql->select('ad.postcode as postcode');
        $sql->select('ad.phone as phone');
        $sql->select('ad.city as city');

        $sql->select('st.name as StateOrProvince');

        $sql->select('cl.name as country');
        $sql->select('c.email as email');

        $sql->select("'' as AlternaetDeliveryAddressIndicator");
        $sql->select('op.ap_id as upsAcessPointID');
        $sql->select('op.ap_name as ap_name');
        $sql->select('op.ap_address1 as ap_address1');
        $sql->select('op.ap_address2 as ap_address2');
        $sql->select("'' as ap_address3");

        $sql->select('op.ap_city as ap_city');
        $sql->select('op.ap_state as ap_state');
        $sql->select('op.ap_postcode as ap_postcode');
        $sql->select('cl.name as ap_country');

        $sql->from('orders', 'o');
        $sql->leftJoin(Constants::DB_TABLE_OPENORDER, 'op', 'op.id_order = o.id_order');
        $sql->innerJoin('address', 'ad', 'ad.id_address = o.id_address_delivery');
        $sql->innerJoin('country_lang', 'cl', 'cl.id_country = ad.id_country AND cl.id_lang = ' . (int) $id_lang);
        $sql->innerJoin('currency', 'cr', 'cr.id_currency = o.id_currency');
        $sql->innerJoin('customer', 'c', 'c.id_customer = ad.id_customer');
        $sql->leftJoin('state', 'st', 'st.id_state = ad.id_state');

        $sql->where("o.id_order in (" . $this->module->explodeIds($ids) . ")");

        return Db::getInstance()->executeS($sql);
    }

    private function getAllOpenOrderIds()
    {
        $id_lang     = (int) $this->context->language->id;
        $statuses = array(
            Constants::STATUS_OPEN_ORDER,
            Constants::STATUS_AWAITING_CHECK_PAYMENT,
            Constants::STATUS_PAYMENT_ACCEPTED,
            Constants::STATUS_PROCESSING_IN_PROGRESS,
            Constants::STATUS_PAYMENT_ERROR,
            Constants::STATUS_ORDER_PAID,
            Constants::STATUS_AWAITING_BANK_WIRE_PAYMENT,
            Constants::STATUS_REMOTE_PAYMENT_ACCEPTED,
            Constants::STATUS_ORDER_NOT_PAID,
            Constants::STATUS_COD_VALIDATION,
        );
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $fromClause = 'FROM `' . _DB_PREFIX_ . 'orders` a ';
            $joinClause = 'LEFT JOIN '. _DB_PREFIX_ . Constants::DB_TABLE_OPENORDER . ' op
                            ON op.id_order = a.id_order
                            LEFT JOIN ' . _DB_PREFIX_ . 'order_detail od
                            ON od.id_order = a.id_order
                            LEFT JOIN ' . _DB_PREFIX_ . 'address ad
                            ON ad.id_address = a.id_address_delivery ';
            $whereClause = 'WHERE 1 AND op.status = 1
                            AND a.current_state IN (' . implode(",", $statuses) . ')
                            AND a.id_carrier IN ('. $this->strIdCarriers .') GROUP BY a.id_order';
        } else {
            $fromClause  = $this->getFromClause();
            $joinClause  = $this->getJoinClause((int) $id_lang, false);
            $whereClause = $this->getWhereClause();
        }

        $list_count = 'SELECT a.id_order AS `id_order` '
                    . $fromClause
                    . $joinClause
                    . $whereClause;

        return Db::getInstance()->executeS($list_count, true, false);
    }

    public function sqlSelectShipment($id_shipment)
    {
        $sql = new DbQuery();

        $sql->select('*');
        $sql->from('ups_shipment');
        $sql->where("id_ups_shipment = '" . pSQL($id_shipment) . "'");

        return Db::getInstance()->executeS($sql);
    }

    public function getNameAccessorials($listAccessorial)
    {
        if (!is_array($listAccessorial)) {
            return;
        }

        $arrAccessorial = '';

        if (count($listAccessorial) != 0) {
            $listNameAccessorials = array_map("$this->moduel->getNameAccessorialByKey", $listAccessorial);

            // Convert to map with API
            foreach ($listNameAccessorials as $nameAccessorials) {
                $arrAccessorial[] = array(
                    'name' => $nameAccessorials,
                );
            }
        }

        return $arrAccessorial;
    }

    public function getCODByOrder($idOrder)
    {
        $sql = new DbQuery();

        $sql->select('op.amount as amount');
        $sql->select('op.payment_method as cod');

        $sql->from('order_invoice_payment', 'oi');
        $sql->leftJoin('order_payment', 'op', 'oi.id_order_payment = op.id_order_payment');
        $sql->where("oi.id_order = " . (int) $idOrder);

        return Db::getInstance()->executeS($sql);
    }

    public function getProductsByOrder($idOrder)
    {
        $sql = new DbQuery();

        $sql->select('product_name as productName');
        $sql->select('product_quantity as productQuantity');

        $sql->from('order_detail');
        $sql->where("id_order = " . (int) $idOrder);

        return Db::getInstance()->executeS($sql);
    }

    private function validateAddPkgUnit($inputPkgs)
    {
        if (preg_match('/^\d+(\.\d{1,2})?$/', $inputPkgs) && $inputPkgs >= 0.01 && $inputPkgs <= 9999.99) {
            return true;
        }
        return false;
    }


    public function ajaxProcessGetState()
    {
        $id_country = Tools::getValue('countryID');
        $listState  = $this->getStateByCountry($id_country);
        $this->ajaxDie(json_encode($listState));
    }

    public function getStateByCountry($countryId)
    {
        $states = State::getStatesByIdCountry($countryId, true);
        $result = array();
        foreach ($states as $item) {
            $result[$item['iso_code']] = $item['name'];
        }
        return $result;
    }

    /**
     * get list shipping service information
     */
    public function getRateCodeNameForShippingService($keyService)
    {
        $keyService             = trim($keyService);
        $listShipServiceForEdit = array();
        // To AP
        if (isset($keyService) && strpos($keyService, '_AP_') !== false) {
            $listShipServiceForEditAll = $this->module::$shippingServices->getServicesAp();
        } else { // To Address
            $listShipServiceForEditAll = $this->module::$shippingServices->getServicesAdd();
        }

        if (!empty($listShipServiceForEditAll)) {
            foreach ($listShipServiceForEditAll as $listShipServiceElement) {
                if ($listShipServiceElement['key'] == $keyService) {
                    $listShipServiceForEdit = array(
                        'key'      => $listShipServiceElement['key'],
                        'Ratecode' => $listShipServiceElement['Ratecode'],
                        'TinTcode' => $listShipServiceElement['TinTcode'],
                        'name'     => $listShipServiceElement['name'],
                    );
                }
            }
        }
        return $listShipServiceForEdit;
    }

    private function createHeaderCsv()
    {
        return array(
            'OrderID',
            'OrderDate',
            'OrderTime',
            'COD',
            'CODAmount',
            'CODCurrency',
            'CurrentState',
            'TotalPaid',
            'TotalProducts',
            'ShippingService',
            'AccessorialsService',
            'ProductName',
            'MerchantUPSAccountNumber',
            'CustomerLastName',
            'CustomerFirstName',
            'CustomerAddressLine1',
            'CustomerAddressLine2',
            'CustomerAddressLine3',
            'CustomerPostalCode',
            'CustomerPhone',
            'CustomerCity',
            'CustomerStateOrProvince',
            'CustomerCountry',
            'CustomerEmail',
            'AlternaetDeliveryAddressIndicator',
            'UPSAcessPointID',
            'AccessPointAddressLine1',
            'AccessPointAddressLine2',
            'AccessPointAddressLine3',
            'AccessPointCity',
            'AccessPointStateOrProvince',
            'AccessPointPostalCode',
            'AccessPointCountry',
        );
    }

    private function addAccessorials($accessorials, $hasCOD, $shippingServiceKey)
    {
        if (empty($accessorials)) {
            return '';
        }

        $data = '';

        $accessorials = unserialize($accessorials);

        if ($hasCOD) {
            if (strpos($shippingServiceKey, '_AP_') !== false) {
                $accessorials[] = 'UPS_ACSRL_ACCESS_POINT_COD';
            } elseif (strpos($shippingServiceKey, '_ADD_') !== false) {
                $accessorials[] = 'UPS_ACSRL_TO_HOME_COD';
            }
        }

        foreach ($accessorials as $key) {
            $data .= $this->module->getNameAccessorialByKey($key) . '; ';
        }

        return rtrim($data, '; ');
    }

    private function alterDataExport($orders)
    {
        $content  = array();
        $contents = array();

        foreach ($orders as $order) {
            // $shippingService = $this->getNameShippingService($order["shipping_service"]);
            $shippingService = $this->module::$shippingServices->getServiceNameByKey($order["shipping_service"]);
            $hasCOD          = $order['cod'] === Constants::PS_COD_MODULE ? 1 : 0;
            $accessorials = $this->addAccessorials($order['accessorials_service'], $hasCOD, $order['shipping_service']);

            $content['OrderID']             = $order['id_order'];
            $content['OrderDate']           = $order['order_date'];
            $content['OrderTime']           = $order['order_time'];
            $content['COD']                 = $this->arrayYesNo[$hasCOD];
            $content['CODAmount']           = $hasCOD ? number_format(round($order['total_paid'], 2), 2) : '';
            $content['CODCurrency']         = $hasCOD ? $order['currency'] : '';
            $content['CurrentState']        = $this->getCurrentState($order['current_state']);
            $content['TotalPaid']           = number_format(round($order['total_paid'], 2), 2);
            $content['TotalProducts']       = number_format(round($order['total_products'], 2), 2);
            $content['ShippingService']     = $shippingService;
            $content['AccessorialsService'] = $accessorials;
            $content['ProductName']         = Tools::stripslashes($order['products']);

            $content['MerchantUPSaccountNumber'] = '';
            $content['CustomerLastName']         = $order['lastname'];
            $content['CustomerFirstName']        = $order['firstname'];
            $content['CustomerAddressLine1']     = $order['address1'];
            $content['CustomerAddressLine2']     = $order['address2'];
            $content['CustomerAddressLine3']     = $order['address3'];
            $content['CustomerPostalCode']       = $order['postcode'];
            $content['CustomerPhone']            = $order['phone'];
            $content['CustomerCity']             = $order['city'];
            $content['CustomerStateOrProvince']  = $order['StateOrProvince'];
            $content['CustomerCountry']          = $order['country'];
            $content['CustomerEmail']            = $order['email'];

            $content['AlternaetDeliveryAddressIndicator'] = (isset($order['upsAcessPointID']) && !empty($order['upsAcessPointID'])) ? 1 : 0;
            $content['UPSAcessPointID']                   = $order['upsAcessPointID'];
            $content['AccessPointAddressLine1']           = html_entity_decode($order['ap_name']);
            $content['AccessPointAddressLine2']           = $order['ap_address1'];
            $content['AccessPointAddressLine3']           = $order['ap_address2'];
            $content['AccessPointCity']                   = $order['ap_city'];
            $content['AccessPointStateOrProvince']        = $order['ap_state'];
            $content['AccessPointPostalCode']             = $order['ap_postcode'];
            $content['AccessPointCountry'] = (isset($order['upsAcessPointID']) && !empty($order['upsAcessPointID'])) ? $order['ap_country'] : '';

            $contents[] = $content;

            unset($content);
        }

        return $contents;
    }

    private function hasSameShippingMethods($orders)
    {
        $hasOrderAddr      = false;
        $hasOrderAp        = false;

        foreach ($orders as $order) {
            if (strpos($order['shipping_service'], '_AP_') !== false) {
                $hasOrderAp = true;
                continue;
            } elseif (strpos($order['shipping_service'], '_ADD_') !== false) {
                $hasOrderAddr = true;
                continue;
            }
        }

        return ($hasOrderAddr && $hasOrderAp) ? 0 : 1;
    }

    private function getDefaultPackage()
    {
        $packages = $this->displayPackageInfo();

        if (!empty($packages)) {
            return isset($packages[0]) ? $packages[0] : '';
        } else {
            return '';
        }
    }

    private function setCodMethod($order, $key)
    {
        return $this->hasCOD($order) ? $key : '';
    }

    private function setServicesRelatedWith(
        $order,
        &$shippingService,
        &$shippingMethod,
        &$accountDefault,
        &$codAccessorial,
        &$serviceTypeName
    ) {
        if ($shippingService === '') {
            $shippingService = $order['shipping_service'];
        }

        // $serviceName = $this->getNameShippingService($shippingService);
        $serviceName = $this->module::$shippingServices->getServiceNameByKey($shippingService);

        $shippingMethod = $this->module->checkServiceType($shippingService);

        if ($shippingMethod == 'AP') {
            $serviceTypeName = $this->sdk->t('openorder', 'To AP');
            $shippingService = $serviceTypeName . " (" . $serviceName . ")";
            $accountDefault  = Configuration::get('UPS_SP_SERV_AP_CHOOSE_ACC');
            $codAccessorial  = $this->setCodMethod($order, 'UPS_ACSRL_ACCESS_POINT_COD');
        } elseif ($shippingMethod == 'ADD') {
            $serviceTypeName = $this->sdk->t('openorder', 'To Address');
            $shippingService = $serviceTypeName . " (" . $serviceName . ")";
            $accountDefault  = Configuration::get('UPS_SP_SERV_ADDRESS_CHOOSE_ACC');
            $codAccessorial  = $this->setCodMethod($order, 'UPS_ACSRL_TO_HOME_COD');
        }
    }

    private function getReceiverAddressesIn($orders)
    {
        $addresses = array();
        foreach ($orders as $order) {
            //get State Selected
            $country_code = '';
            if (isset($order['country_code'])) {
                $country_code = $order['country_code'];
            }

            $stateList = $this->getListStateCode($country_code);
            $state_name = '';
            if (isset($stateList[$order['ap_state']])) {
                $state_name = $stateList[$order['ap_state']];
            } else {
                $state_name = $order['state_name'];
            }

            if (empty($order['ap_city'])) {
                $addresses[] = array(
                    'orderId'      => $order['id_order'],
                    'name'         => $order['firstname'] . ' ' . $order['lastname'],
                    'country'      => $order['country_name'],
                    'city'         => $order['city'],
                    'state'        => $state_name,
                    'email'        => $order['email'],
                    'phone'        => $order['phone'],
                    'postcode'     => $order['postcode'],
                    'addressLine1' => $order['address_delivery1'],
                    'addressLine2' => $order['address_delivery2'],
                );
            } else {
                $addresses[] = array(
                    'orderId'      => $order['id_order'],
                    'name'         => $order['ap_name'],
                    'country'      => $order['country_name'],
                    'city'         => $order['ap_city'],
                    'state'        => $state_name,
                    'email'        => $order['email'],
                    'phone'        => $order['phone'],
                    'postcode'     => $order['ap_postcode'],
                    'addressLine1' => $order['ap_address1'],
                    'addressLine2' => $order['ap_address2'],
                );
            }
        }

        return $addresses;
    }

    private function getAccessorialsIn($order, $codAccessorial)
    {
        $accessorials = $this->getAccessorialKeys($order, $codAccessorial);

        if (!empty($accessorials)) {
            $accessorials = $this->getListNamesAccessorial($accessorials);
        }

        return $accessorials;
    }

    private function getAccessorialKeys($order, $codAccessorial)
    {
        $accessorials = array();

        if (isset($order['accessorials_service']) && !empty($order['accessorials_service'])) {
            $accessorials = unserialize($order['accessorials_service']);
        }

        if (!empty($codAccessorial)) {
            $accessorials[] = $codAccessorial;
        }

        return $accessorials;
    }

    private function getListNamesAccessorial($arrKeyAccessorial)
    {
        $strAccessorial = '';

        foreach ($arrKeyAccessorial as $key) {
            $strAccessorial .= $this->module->getNameAccessorialByKey($key) . '<br/>';
        }

        return $strAccessorial;
    }

    private function getOrderId()
    {
        $id       = 0;
        $orderId  = Tools::getValue('orderID');
        $orderIds = Tools::getValue('allOrderID');
        if ($orderIds == '0') {
            $id = $orderId;
        } else {
            $id = $orderIds = explode(',', $orderIds);
        }
        return $id;
    }

    private function getFirstOrder($orders)
    {
        $firstOrder = array();

        if (is_array($orders)) {
            $firstOrder = $orders[0];
        } else {
            $firstOrder = $orders;
        }

        return $firstOrder;
    }

    private function getAccessorialKeysEdited($accessorialsServiceEdited)
    {
        return $accessorialsServiceEdited ? $accessorialsServiceEdited : array();
    }

    private function setAddressEdited($tmpAddr, &$firstOrder)
    {
        $arrayError = array();

        if (isset($tmpAddr['name'])) {
            $firstOrder['lastname']  = '';
            $firstOrder['firstname'] = $tmpAddr['name'];
        }

        if (isset($tmpAddr['shipToAddress1'])) {
            $firstOrder['address_delivery1'] = $tmpAddr['shipToAddress1'];
        }

        if (isset($tmpAddr['shipToAddress2'])) {
            $firstOrder['address_delivery2'] = $tmpAddr['shipToAddress2'];
        }

        if (isset($tmpAddr['shipToPostalCode'])) {
            $firstOrder['postcode'] = $tmpAddr['shipToPostalCode'];
        }

        if (isset($tmpAddr['shipToCity'])) {
            $firstOrder['city'] = $tmpAddr['shipToCity'];
        }

        if (isset($tmpAddr['shipToState'])) {
            $firstOrder['state'] = $tmpAddr['shipToState'];
        }

        if (isset($tmpAddr['shipToPhone'])) {
            $shipToPhone = trim($tmpAddr['shipToPhone']);

            // If is AP dont need check Phone or set dummy number
            if (strpos($firstOrder['shipping_service'], '_AP_') !== false) {
                if ($firstOrder['phone'] == '') {
                    $firstOrder['phone'] = Constants::DUMMY_PHONE_NUMBER;
                }
            } else {
                if (Tools::strlen($shipToPhone) >= 1 && Tools::strlen($shipToPhone) <= 15) {
                    $firstOrder['phone']        = $shipToPhone;
                    $firstOrder['phone_mobile'] = $shipToPhone;
                }
            }
        }

        if (isset($tmpAddr['shipToEmail'])) {
            // If is AP dont need check Mail or set dummy mail
            if (strpos($firstOrder['shipping_service'], '_AP_') !== false) {
                if ($firstOrder['email'] == '') {
                    $firstOrder['email'] = Constants::DUMMY_MAIL;
                }
            } else {
                if (Validate::isEmail($tmpAddr['shipToEmail'])) {
                    $firstOrder['email'] = $tmpAddr['shipToEmail'];
                } else {
                    // email_invalid
                    $arrayError[] = $this->sdk->t('openorder', 'email_invalid');
                }
            }
        }

        if (isset($tmpAddr['shipToCountry']) && $tmpAddr['shipToCountry'] > 0) {
            $idLang                     = $this->context->language->id;
            $firstOrder['country_code'] = Country::getIsoById($tmpAddr['shipToCountry']);
            $firstOrder['country_name'] = Country::getNameById($idLang, $tmpAddr['shipToCountry']);
        }
    }

    // Set dummy phone and email if service is AP
    private function setDummyPhoneAndMail($shippingMethod, &$order)
    {
        if ($shippingMethod == 'AP') {
            if ($order['phone'] == '') {
                $order['phone'] = Constants::DUMMY_PHONE_NUMBER;
            }

            if ($order['email'] == '') {
                $order['email'] = Constants::DUMMY_MAIL;
            }
        }
    }

    private function formatErrorMessage($errors)
    {
        return implode("<br />", $errors);
    }

    private function processingAddressEdited($orderId, &$shipToAddr, &$order)
    {
        $selectedOrder = $this->sqlSelectOrder($orderId)[0];

        $shipToAddr['name']             = $selectedOrder['lastname'] . ' ' . $selectedOrder['firstname'];
        $shipToAddr['shipToAddress1']   = $selectedOrder['address_delivery1'];
        $shipToAddr['shipToAddress2']   = $selectedOrder['address_delivery2'];
        $shipToAddr['shipToPostalCode'] = $selectedOrder['postcode'];
        $shipToAddr['shipToCity']       = $selectedOrder['city'];
        $shipToAddr['shipToPhone']      = $selectedOrder['phone'];
        $shipToAddr['shipToEmail']      = $selectedOrder['email'];
        $shipToAddr['shipToCountry']    = $selectedOrder['country_code'];
        if (!empty($selectedOrder['state_code'])) {
            $shipToAddr['shipToState']  = $selectedOrder['state_code'];
        }

        if (!empty($selectedOrder['ap_address1'])) {
            $order['ap_id']        = $selectedOrder['ap_id'];
            $order['ap_name']      = $selectedOrder['ap_name'];
            $order['ap_address1']  = $selectedOrder['ap_address1'] . $selectedOrder['ap_address2'];
            $order['ap_city']      = $selectedOrder['ap_city'];
            $order['ap_state']     = (!empty($selectedOrder['ap_state'])) ? $selectedOrder['ap_state'] : '';
            $order['ap_postcode']  = $selectedOrder['ap_postcode'];
            $order['country_code'] = $selectedOrder['country_code'];
        }
    }

    private function setPrimaryInfo($serviceCode, $serviceName)
    {
        $this->primaryInfo['shippingServiceCode']        = $serviceCode;
        $this->primaryInfo['ShippingServiceDescription'] = $serviceName;
        $this->primaryInfo['PackagingTypeCode']          = '02';
        $this->primaryInfo['PackagingTypeDescription']   = '';
    }

    private function estimationFeeData(
        $order,
        $packages,
        $accessorialsService = array(),
        $shippingServiceEdited = array(),
        $newAddress = '',
        $countryIso = ''
    ) {
        $shippingService = isset($order['shipping_service']) ?
        $this->getShippingService($order['shipping_service']) : '';

        $order['province_code'] = isset($order['state_code']) ?
        $order['state_code'] : 'XX';
        if (!empty($shippingServiceEdited)) {
            $shippingService = $shippingServiceEdited;
        }

        $return = array(
            'ShipperName'                => $this->primaryInfo['CustomerName'],
            'ShipperNumber'              => $this->accountInfo['AccountNumber'],
            'ShipperAddressLine1'        => $this->accountInfo['AddressLine1'],
            'ShipperAddressLine2'        => $this->accountInfo['AddressLine2'],
            'ShipperAddressLine3'        => $this->accountInfo['AddressLine3'],
            'ShipperCity'                => $this->accountInfo['City'],
            'ShipperStateProvinceCode'   => $this->accountInfo['ProvinceCode'],
            'ShipperStatePostalCode'     => $this->accountInfo['PostalCode'],
            'ShipperCountryCode'         => $this->accountInfo['CountryCode'],

            'ShipFromName'               => $this->primaryInfo['CustomerName'],
            'ShipFromAddress1'           => $this->accountInfo['AddressLine1'],
            'ShipFromAddress2'           => $this->accountInfo['AddressLine2'],
            'ShipFromAddress3'           => $this->accountInfo['AddressLine3'],
            'ShipFromCity'               => $this->accountInfo['City'],
            'ShipFromStateProvinceCode'  => $this->accountInfo['ProvinceCode'],
            'ShipFromPostalCode'         => $this->accountInfo['PostalCode'],
            'ShipFromCountryCode'        => $this->accountInfo['CountryCode'],

            'ShippingServiceCode'        => $shippingService['Ratecode'],
            'ShippingServiceDescription' => $shippingService['name'],

            // 'PackagingTypeCode' => '02', // Have to check on UPS Document
            'PackagingTypeDescription'   => 'Rate',

            'packages'                   => $packages,
            'order'                      => $order,
            'accessorialsService'        => $accessorialsService,
        );

        $arrShipTo = array();
        if (empty($order['ap_address1'])) {
            $arrShipTo = array(
                'ShipToName'              => $order['lastname'] . ' ' . $order['firstname'],
                'ShipToCity'              => $order['city'],
                'ShipToAddress1'          => $order['address_delivery1'],
                'ShipToAddress2'          => $order['address_delivery2'],
                'ShipToAddress3'          => '',
                'ShipToPostalCode'        => $order['postcode'],
                'ShipToCountryCode'       => $order['country_code'],
                'ShipToStateProvinceCode' => $order['ap_state'], //khong su dung province_code
            );
        } else {
            $arrShipTo = array(
                'ShipToName'              => $order['ap_name'],
                'ShipToCity'              => $order['ap_city'],
                'ShipToAddress1'          => $order['ap_address1'],
                'ShipToAddress2'          => $order['ap_address2'],
                'ShipToAddress3'          => '',
                'ShipToPostalCode'        => $order['ap_postcode'],
                'ShipToCountryCode'       => $order['country_code'],
                'ShipToStateProvinceCode' => $order['ap_state'],
            );

            $return['order']['ap_name']       = $arrShipTo['ShipToName'];
            $return['order']['ap_city']       = $arrShipTo['ShipToCity'];
            $return['order']['ap_address1']   = $arrShipTo['ShipToAddress1'];
            $return['order']['ap_address2']   = $arrShipTo['ShipToAddress2'];
            $return['order']['ap_postcode']   = $arrShipTo['ShipToPostalCode'];
            $return['order']['country_code']  = $arrShipTo['ShipToCountryCode'];
            $return['order']['province_code'] = $arrShipTo['ShipToStateProvinceCode'];
        }

        if (!empty($newAddress)) {
            $arrShipTo = array(
                'ShipToName'              => $newAddress['newName'],
                'ShipToCity'              => $newAddress['newCity'],
                'ShipToAddress1'          => $newAddress['newAddress1'],
                'ShipToAddress2'          => $newAddress['newAddress2'],
                'ShipToAddress3'          => $newAddress['newAddress3'],
                'ShipToPostalCode'        => $newAddress['newPostalCode'],
                'ShipToCountryCode'       => $newAddress['newCountry'],
                'ShipToStateProvinceCode' => $newAddress['newState'],
            );

            // case edit address
            if (!empty($countryIso)) {
                $arrShipTo['ShipToCountryCode'] = $countryIso;
            }

            if (strpos($shippingService['key'], '_AP_') !== false) {
                // for alternation 24-09
                if (!empty($arrShipTo)) {
                    $return['order']['ap_name']       = $arrShipTo['ShipToName'];
                    $return['order']['ap_city']       = $arrShipTo['ShipToCity'];
                    $return['order']['ap_address1']   = $arrShipTo['ShipToAddress1'];
                    $return['order']['ap_address2']   = $arrShipTo['ShipToAddress2'];
                    $return['order']['ap_postcode']   = $arrShipTo['ShipToPostalCode'];
                    $return['order']['country_code']  = $arrShipTo['ShipToCountryCode'];
                    $return['order']['province_code'] = $arrShipTo['ShipToStateProvinceCode'];
                }
            }
        }

        $return = array_merge($return, $arrShipTo);

        return $return;
    }

    private function getCurrentState($orderStatus)
    {
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);

        foreach ($statuses as $status) {
            if ($orderStatus == $status['id_order_state']) {
                return $status['name'];
            }
        }

        return '';
    }

    public function ajaxProcessGetText()
    {
        $textWarning = $this->sdk->t('openorder', 'txtWarning');
        $this->ajaxDie(json_encode($textWarning));
    }
}
