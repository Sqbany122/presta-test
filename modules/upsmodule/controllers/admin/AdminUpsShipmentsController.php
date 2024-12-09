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

class AdminUpsShipmentsController extends CommonController
{
    protected $statuses_array = array();
    private $printLabel       = array();
    public $arrayYesNo        = array();

    private $tblOrderDetail = _DB_PREFIX_ . 'order_detail';
    private $tblProductLang = _DB_PREFIX_ . 'product_lang';
    private $tblShipment    = _DB_PREFIX_ . Constants::DB_TABLE_SHIPMENT;

    private $languageId = '';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table     = Constants::DB_TABLE_SHIPMENT;
        $this->lang      = false;
        $this->addRowAction(' ');
        $this->identifier = 'id';
        parent::__construct();
        $this->setLanguageId();

        $this->_select = "
            a.id_ups_shipment as id_ups_shipment,
            a.tracking_number as tracking_number,
            a.id_order as id_order,

            CONCAT_WS(
                '<br>',
                IF (CHARACTER_LENGTH(a.shipping_address1) > '40',
                    CONCAT(left(a.shipping_address1,37) , '...'),
                    a.shipping_address1),
                IF (CHARACTER_LENGTH(a.shipping_address2) > '40',
                    CONCAT(left(a.shipping_address2,37) , '...'),
                    a.shipping_address2),
                IF (CHARACTER_LENGTH(a.city) > '40',
                    CONCAT(left(a.city,37) , '...'),
                    a.city)
            ) as shipping_address1,
            a.shipping_service as shipping_service,
            DATE(a.create_date) as date,
            TIME(a.create_date) as time,
            a.status as status,
            a.cod as cod,
            ROUND(a.shipping_fee, 2) as shipping_fee
        ";

        // Button Bulk Action
        // Confirm to action onclick="sendBulkAction(form, action)"
        // function sendBulkAction in file admin.js
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
    }

    public function setLanguageId()
    {
        $this->languageId = (int) $this->context->language->id;
    }

    private function createFieldsList()
    {
        $fieldsList = array(
            'id_ups_shipment'   => array(
                'title'          => $this->sdk->t('colname', 'txtShipmentId'),
                'align'          => 'text-center',
                'class'          => 'fixed-width-xs',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'showViewShipmentModal',
            ),
            'tracking_number'   => array(
                'title'          => $this->sdk->t('colname', 'txtTrackingNumber'),
                'align'          => 'text-center',
                'search'         => false,
                'remove_onclick' => true,
                // 'callback'       => 'showViewShipmentModal', // THIS COMMENT IMPORTANT
            ),
            'id_order'          => array(
                'title'          => $this->sdk->t('colname', 'txtOrderId'),
                'align'          => 'text-center',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'showViewShipmentModal',
            ),
            'date'              => array(
                'title'          => $this->sdk->t('colname', 'date'),
                'type'           => 'date',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'showViewShipmentModal',
            ),
            // 'time'              => array(
            //     'title'          => $this->sdk->t('shipment', 'time'),
            //     'search'         => false,
            //     'remove_onclick' => true,
            //     'callback'       => 'showViewShipmentModal',
            // ),
            'shipping_address1' => array(
                'title'          => $this->sdk->t('colname', 'txtDeliveryAdd'),
                'class'          => 'fixed-width-xxl',
                'search'         => false,
                'remove_onclick' => true,
                'callback'       => 'showViewShipmentModal',
            ),
            'shipping_fee'      => array(
                'title'          => $this->sdk->t('colname', 'estimated_fee'),
                'search'         => false,
                'type'           => 'price',
                'remove_onclick' => true,
                'callback'       => 'showViewShipmentModal',
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
                'desc' => $this->sdk->t('shipment', 'print_label_pdf'),
                'icon' => 'process-icon-new',
                'js'   => 'printLabel(\'PDF\');',
            );

             $this->page_header_toolbar_btn['print_label_zpl'] = array(
                 'desc' => $this->sdk->t('shipment', 'print_label_zpl'),
                 'icon' => 'process-icon-download',
                 'js'   => 'printLabel(\'ZPL\');',
             );

            // Function processExport in this class or in AdminController
            $this->page_header_toolbar_btn['export_shipments'] = array(
                'desc' => $this->sdk->t('shipment', 'export_shipments'),
                'icon' => 'process-icon-save-date',
            );

            $this->page_header_toolbar_btn['cancel_shipments'] = array(
                'desc' => $this->sdk->t('shipment', 'cancel_shipments'),
                'icon' => 'process-icon-cancel',
                'js'   => 'cancelShipment();',
            );
        }
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsShipments');
    }

    public function initContent()
    {
        $retention = new OrderRetention();
        $retention->delteShipmentMoreThan90Days();

        $this->informations[] = $this->getTextTermandConditions();
        parent::initContent();
    }

    public function ajaxProcessCancelShipment()
    {
        $response = array();
        $trackingId = Tools::getValue('trackingId');
        $response['canceled'] = $this->cancelShipments($trackingId);

        $this->ajaxDie(json_encode($response));
    }

    public function cancelShipments($trackingNumbers)
    {
        if ($trackingNumbers === '') {
            return false;
        }

        $shipmentCancelled = array();
        $collection        = array();
        $shipments         = $this->getShipments($trackingNumbers);
        $shipmentFlag      = '';

        foreach ($shipments as $shipment) {
            if ($shipmentFlag !== '' && $shipment['id_ups_shipment'] == $shipmentFlag) {
                $shipmentCancelled[] = array(
                    'idOrders'       => $shipment['id_order'],
                    'idShipments'    => $shipment['id_ups_shipment'],
                    'trackingNumber' => $shipment['tracking_number'],
                    'shipmentStatus' => 'CANCELED',
                );
                continue;
            }

            // $informations = $this->processTracking($shipment['id_ups_shipment']);
            // $shipmentStatus = $this->getStatus($informations);

            // if ($shipmentStatus && $shipmentStatus != '003') {
            $args = array();
            $args['shipmentId'] = $shipment['id_ups_shipment'];
            $args['sdk'] = $this->sdk;
            $obj = new Ups\Api\VoidShipment();
            $res = $obj($args);

            if ($res['Code'] == '1') {
                $shipmentCancelled[] = array(
                    'idOrders'       => $shipment['id_order'],
                    'idShipments'    => $shipment['id_ups_shipment'],
                    'trackingNumber' => $shipment['tracking_number'],
                    'shipmentStatus' => 'CANCELED',
                );

                $collection = array_merge($collection, $this->getPackageActivities($shipment['id_ups_shipment']));
            } else {
                $this->errors[] = $this->sdk->t(
                    'shipment',
                    'cancel_denied',
                    array('trackingNumber' => $shipment['tracking_number'])
                );
            }
            // }

            $shipmentFlag = $shipment['id_ups_shipment'];
        }

        if (!empty($shipmentCancelled)) {
            $this->handleOrder(
                array_column($shipmentCancelled, 'idShipments'),
                array_column($shipmentCancelled, 'idOrders')
            );

            try {
                $this->transferShipmentStatus($collection);
            } catch (Exception $e) {
            }
        }

        if (empty($this->errors)) {
            return true;
        } else {
            return false;
        }
    }

    private function getPackageActivities($shipmentId)
    {
        $sql = new DbQuery();
        $sql->select('s.tracking_number as TrackingNumber');
        $sql->from(Constants::DB_TABLE_SHIPMENT, 's');
        $sql->where("s.id_ups_shipment = '$shipmentId'");
        $rows = Db::getInstance()->executeS($sql);
        $tmp = array();

        if ($rows) {
            foreach ($rows as $package) {
                $tmp[] = array(
                    'trackingNumber' => $package['TrackingNumber'],
                    'shipmentStatus' => 'processing_in_progress'
                );
            }
        }

        return empty($tmp) ? false : $tmp;
    }

    public function handleOrder($shipmentIds, $orderIds)
    {
        $shipmentIds = implode(",", $shipmentIds);
        CommonFunction::sqlUpdateOrderStatus(Constants::STATUS_PROCESSING_IN_PROGRESS, $orderIds);
        $this->updateOpenOrderStatus($orderIds, '1');
        $this->deleteShipment($shipmentIds);
        CommonFunction::sqlUpdateTrackingNumber(0, $orderIds);
    }

    public function ajaxProcessExportShipment()
    {
        $shipmentIDs = Tools::getValue('shipmentID');

        if ($shipmentIDs) {
            $this->context->cookie->__set('shipmentID', $shipmentIDs);
        }

        $this->ajaxDie(json_encode($shipmentIDs));
    }

    public function processExport($text_delimiter = '"')
    {
        $shipmentIds = $this->context->cookie->shipmentID;

        if (!$shipmentIds) {
            return;
        }

        $shipments   = $this->sqlExportShipment($shipmentIds);

        if (empty($shipments)) {
            return;
        }

        $headers  = $this->setCsvHeader();
        $contents = $this->buildContents($shipments);
        $this->module->putContents($headers, $contents, $text_delimiter, Constants::PREFIX_CSV_SHIPMENT);
        die; // DO NOT DELETE [IMPORTANT]
    }

    private function buildContents($shipments)
    {
        $content  = array();
        $contents = array();

        foreach ($shipments as $shipment) {
            $shippingService = '';
            $hasCOD          = $shipment['cod'];
            $orderId         = $shipment["order_id"];
            $orderValue      = $this->roundTwoDecimals($shipment['order_value']);
            $shippingFee     = $this->roundTwoDecimals($shipment['shipping_fee']);
            $totalShipping   = $this->roundTwoDecimals($shipment['total_shipping']);
            $codAmount       = $this->setCODAmount($hasCOD, $orderValue);
            $shippingService = $this->module::$shippingServices->getServiceNameByKey($shipment['shipping_service']);

            $content['ShipmentID']           = $shipment['shipment_id'];
            $content['Date']                 = $shipment['date'];
            $content['Time']                 = $shipment['time'];
            $content['TrackingNumber']       = $shipment['tracking_number'];
            $content['DeliveryStatus']       = $shipment['delivery_status'];
            $content['COD']                  = $this->arrayYesNo[$hasCOD];
            $content['CODAmount']            = $codAmount;
            $content['CODCurrency']          = $hasCOD ? $shipment['currency'] : '';
            $content['EstimatedShippingFee'] = $shippingFee;
            $content['ShippingService']      = $shippingService;
            $content['Accessorials']         = isset($shipment['accessorials']) ?
                $this->addAccessorials($shipment['accessorials']) : '';
            $content['OrderID']              = $orderId;
            $content['OrderDate']            = $shipment['order_date'];
            $content['OrderValue']           = $orderValue;
            $content['ShippingFee']          = $totalShipping;
            $content['PackageDetails']       = $this->mappingUnitPrototype($shipment['package_detail']);
            $content['ProductDetails']       = Tools::stripslashes($shipment['product_detail']);
            $content['CustomerName']         = $shipment['customer_name'];

            if (strpos($shipment['shipping_service'], '_AP_') !== false) {
                $content['CustomerAddressLine1']    = html_entity_decode($shipment['customer_address1']);
                $content['CustomerAddressLine2']    = html_entity_decode($shipment['customer_address2']);
                $content['CustomerAddressLine3']    = '';
                $content['CustomerPostalCode']      = $shipment['customer_postcode'];
                $content['CustomerPhoneNo']         = $shipment['phone'];
                $content['CustomerCity']            = $shipment['customer_city'];
                $content['CustomerStateOrProvince'] = $shipment['customer_state_name'];
                $content['CustomerCountry']         = html_entity_decode($shipment['country']);
                $content['CustomerEmail']           = $shipment['email'];

                $content['AlternateDeliveryAddressIndicator'] = Constants::ALTERNATE_INDICATOR_FLAG;
                $content['UPSAcessPointID']                   = $shipment['ap_id'];
                $content['AccessPointAddressLine1']           = $shipment['ap_name'];
                $content['AccessPointAddressLine2']           = html_entity_decode($shipment['shipping_address1']);
                $content['AccessPointAddressLine3']           = html_entity_decode($shipment['shipping_address2']);
                $content['AccessPointCity']                   = $shipment['city_order'];
                $content['AccessPointStateOrProvince'] = $this->module->getStateNameByCode($shipment['state_order']);
                $content['AccessPointPostalCode']             = $shipment['postcode_order'];
                $content['AccessPointCountry']                = html_entity_decode($shipment['country_order']);
            } elseif (strpos($shipment['shipping_service'], '_ADD_') !== false) {
                $content['CustomerAddressLine1']    = html_entity_decode($shipment['shipping_address1']);
                $content['CustomerAddressLine2']    = html_entity_decode($shipment['shipping_address2']);
                $content['CustomerAddressLine3']    = '';
                $content['CustomerPostalCode']      = $shipment['postcode_order'];
                $content['CustomerPhoneNo']         = $shipment['phone'];
                $content['CustomerCity']            = $shipment['city_order'];
                $content['CustomerStateOrProvince'] = $shipment['customer_state_name'];
                $content['CustomerCountry']         = html_entity_decode($shipment['country_order']);
                $content['CustomerEmail']           = $shipment['email'];

                $content['AlternateDeliveryAddressIndicator'] = Constants::ALTERNATE_INDICATOR_FLAG_OFF;
                $content['UPSAcessPointID']                   = '';
                $content['AccessPointAddressLine1']           = '';
                $content['AccessPointAddressLine2']           = '';
                $content['AccessPointAddressLine3']           = '';
                $content['AccessPointCity']                   = '';
                $content['AccessPointStateOrProvince']        = '';
                $content['AccessPointPostalCode']             = '';
                $content['AccessPointCountry']                = '';
            }

            $contents[] = $content;

            unset($content);
        }

        return $contents;
    }

    private function roundTwoDecimals($value)
    {
        return number_format(round($value, 2), 2);
    }

    private function mappingUnitPrototype($packageDetail)
    {
        $packageDetail = Tools::strtoupper($packageDetail);

        $unitPrototypes = array_merge($this->sdk->weightUnitPrototypes, $this->sdk->lengthUnitPrototypes);

        foreach ($unitPrototypes as $key => $unit) {
            $packageDetail = str_replace($key, $unit, $packageDetail);
        }

        return $packageDetail;
    }

    private function addAccessorials($accessorials)
    {
        if (empty($accessorials)) {
            return '';
        }

        $data = '';

        $accessorials = unserialize($accessorials);

        foreach ($accessorials as $key) {
            $data .= $this->module->getNameAccessorialByKey($key) . '; ';
        }

        return rtrim($data, '; ');
    }

    private function setCODAmount($hasCOD, $orderValue)
    {
        return $hasCOD ? $orderValue : '';
    }

    public function ajaxProcessPrintLabel()
    {
        $labelFormat     = Tools::getValue('labelFormat');
        $strShipmentId   = Tools::getValue('trackingId');
        $trackingNumbers = $this->getShippmentIdByIdStr($strShipmentId);

        if (!empty($trackingNumbers)) {
            $arrShipmentId = array_column($trackingNumbers, 'id_ups_shipment');
            $arrShipmentId = array_unique($arrShipmentId, SORT_REGULAR); // Remove tracking number duplicate
            $filePaths     = array();
            $redirect      = true;

            foreach ($arrShipmentId as $trackingNum) {
                $args = array();
                $args['sdk'] = $this->sdk;
                $args['trackingNumber'] = $trackingNum;
                $args['labelFormat'] = $labelFormat;
                $objLabel = new Ups\Api\LabelRecovery();
                $res = $objLabel($args);
                // have one record errors, show errors and not download
                if ($res['Code'] != 1) {
                    $redirect = false;
                    break;
                }
                $fileExt = '.' . strtolower($labelFormat);
                $decoded = '';
                if (!empty($res['LabelResults'])) {
                    if (is_array($res['LabelResults'])) {
                        foreach ($res['LabelResults'] as $labelResult) {
                            $decoded .= base64_decode($labelResult->LabelImage->GraphicImage);
                        }
                    } else {
                        $decoded = base64_decode($res['LabelResults']->LabelImage->GraphicImage);
                    }
                }
                $tempName = tempnam('', Constants::NAME_LABEL_SHIPMENT . '_');
                file_put_contents($tempName, $decoded);

                $filePaths[] = array(
                    'srcPath' => $tempName,
                    'desPath' => Constants::NAME_LABEL_SHIPMENT . '_' . $trackingNum . $fileExt,
                );
            }

            $zipFilePath = tempnam('', Constants::NAME_LABEL_SHIPMENT . '_');
            rename($zipFilePath, $zipFilePath .= '.zip');
            $this->context->cookie->__set('ZipPath', $zipFilePath);

            $result = CommonFunction::createZipArchive($filePaths, $zipFilePath);
            $return = array();

            if ($result && $redirect) {
                Commonfunction::removeFiles($filePaths);
                $return['error'] = '';
                $return['link']  = $this->context->link->getAdminLink('AdminUpsShowBarcodeImage');
            } else {
                $return['error'] = $this->sdk->t('shipment', 'msgLabel');
                $return['link']  = '';
            }

            $this->ajaxDie(json_encode($return));
        }
    }

    public function showShipmentModal($value, $order)
    {
        return '<p class="' . ('action-disabled') . '"
                    onclick="showPrintLabelModal(' . $order["id_order"] . ');">
                    ' . $value .
            '</p>';
    }

    public function initModal()
    {
        $countryCode = Configuration::get('UPS_COUNTRY_SELECTED');
        $texts = array(
            'txtShipments'                     => $this->sdk->t('colname', 'txtShipments') . ' #:',
            'txtShipmentsOrderID'              => $this->sdk->t('shipment', 'txtShipmentsOrderID'),
            'txtShipmentsTracking'             => $this->sdk->t('colname', 'txtTrackingNumber'),
            'txtCustomer'                      => $this->sdk->t('colname', 'txtCustomer'),
            'txtArcProduct'                    => $this->sdk->t('colname', 'txtProduct'),
            'txtArcAddress'                    => $this->sdk->t('address', 'txtAddress'),
            'txtAccPhoneNumber'                => $this->sdk->t('colname', 'txtPhoneNumber'),
            'txtAccEmail'                      => $this->sdk->t('colname', 'txtEmail'),
            'txtArcShippingService'            => $this->sdk->t('colname', 'txtShippingService'),
            'txtShipmentsPackageDetails'       => $this->sdk->t('shipment', 'txtShipmentsPackageDetails'),
            'txtShipmentsAccessorialService'   => $this->sdk->t('shipment', 'txtShipmentsAccessorialService'),
            'txtArcOrderValue'                 => $this->sdk->t('colname', 'txtOrderValue'),
            'txtShipmentsShippingFee'          => $this->sdk->t('shipment', 'txtShipmentsShippingFee'),
            'txtPrintLabel'                    => $this->sdk->t('shipment', 'txtPrintLabel'),
            'txtTrackingTermConditions'        => $this->sdk->t('shipment', 'txtTrackingTermConditions'),
            'txtContentTrackingTermConditions' => $this->sdk->t('shipment', 'txtContentTrackingTermConditions'),
            'txtArcOk'                         => $this->sdk->t('button', 'txtOk'),
            'txtPrint'                         => $this->sdk->t('button', 'txtPrint'),
            'txtAfterAccessorialService'       => $this->sdk->t('shipment', 'txtAfterAccessorialService'),

        );

        $this->context->smarty->assign(array(
            'arrtext' => $texts,
            'countryCode' => $countryCode,
            'view_dir' => _MODULE_DIR_ . $this->module->name . '/views'
        ));
        $this->modals[] = array(
            'modal_id'      => 'modalViewDetail',
            'modal_class'   => 'modal-md',
            'modal_content' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/ups_shipment/modalViewDetail.tpl'
            ),
        );

        $this->modals[] = array(
            'modal_id'      => 'modalShowNotice',
            'modal_class'   => 'modal-md',
            'modal_title'   => $this->sdk->t('shipment', 'txtTrackingTermConditions'),
            'modal_content' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/ups_shipment/modalShowNotice.tpl'
            ),
        );
    }

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

    public function setMedia($isNewTheme = false)
    {
        $sql = new DbQuery();
        $sql->select('s.id as id');
        $sql->from(Constants::DB_TABLE_SHIPMENT, 's');

        /**
         * CONDITION-UPDATING
         * Still UPDATING on the condition
         * UPS
         * #22-08-2018
         */
        $sql->where('s.status != 0');

        $row      = Db::getInstance()->executeS($sql);
        $countRow = 0;
        $variables = array();

        if ($row) {
            $countRow = count($row);
            $variables = array(
                'shipmentID' => $row[0]['id'],
                'countRow'   => $countRow,
            );
        } else {
            $variables = array(
                'countRow' => $countRow,
            );
        }
        $sdk = $this->sdk;

        $variables['txtSuccess'] = $sdk->t('shipment', 'txtSuccess');
        $variables['txtUnsuccess'] = $sdk->t('shipment', 'txtUnsuccess');
        $variables['txtCancelConfirm'] = $sdk->t('shipment', 'txtCancelConfirm');

        Media::addJsDef($variables);

        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/showModal.js');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upsshipments.js');
    }

    // load order info
    public function ajaxProcessGetOrderById()
    {
        $id_ups_shipment = Tools::getValue('shipmentID');
        $shipment        = $this->sqlSelectShipment($id_ups_shipment);
        // Currency
        $currency = Currency::getCurrencyInstance(Configuration::get('PS_CURRENCY_DEFAULT'));

        $shipment[0]['currency'] = $currency->sign;
        // Create array have length width height weight
        $arrPkgs = $this->listPkgs();

        foreach ($arrPkgs as $arrPkg) {
            if ($shipment[0]["package_detail"] == $arrPkg["id"]) {
                // Get length width height weight
                $listPkgs = array();
                array_push($listPkgs, unserialize(Configuration::get('UPS_PKG_' . $arrPkg["id"] . '_DIMENSION')));

                // Get string
                $detail = $listPkgs[0]["lenght"] . 'x' . $listPkgs[0]["width"] . 'x' .
                    $listPkgs[0]["height"] . $listPkgs[0]["lenghtUnit"];
                $detail = $detail . ',' . $listPkgs[0]["weight"] . $listPkgs[0]["weightUnit"];

                // Update value of package_detail
                $shipment[0]["package_detail"] = $detail;
            }
        }

        // Update value of status
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($statuses as $status) {
            $shipment[0]["status"] = $status['name'];
        }

        if (!empty($shipment[0]["shipping_service"])) {
            $keyService                      = $shipment[0]["shipping_service"];
            $shipment[0]["shipping_service"] = $this->module::$shippingServices->getServiceNameByKey($keyService);
        } else {
            $shipment[0]["shipping_service"] = '';
        }

        // Convert Accessorial Name
        if (isset($shipment[0]["accessorials_service"]) && !empty($shipment[0]["accessorials_service"])) {
            $arrayAccessorial = unserialize($shipment[0]["accessorials_service"]);

            if (!empty($arrayAccessorial)) {
                $shipment[0]["accessorials_service"] = $this->module->getListNamesAccessorial($arrayAccessorial);
            } else {
                $shipment[0]['accessorials_service'] = '';
            }
        } else {
            $shipment[0]["accessorials_service"] = '';
        }

        $shipment[0]['shipping_fee'] = round($shipment[0]['shipping_fee'], 2);
        $shipment[0]['order_value']  = round($shipment[0]['order_value'], 2);

        $this->ajaxDie(json_encode($shipment));
    }

    // Duplicate function at deadline, it should not be here. UPS 19-08-2018
    public function ajaxProcessGetOrderByTrackingNumber()
    {
        $recordId = Tools::getValue('combinationKeys');
        $shipment = $this->sqlSelectShipmentById($recordId)[0];
        $infomations = $this->processTracking($shipment['tracking_number']);
        $shipmentStatus = $this->getStatus($infomations);
        // update delivery status when view shipment detail.
        if ($shipmentStatus) {
            Db::getInstance()->update(
                'ups_shipment',
                array('delivery_status' => pSQL($shipmentStatus)),
                "`id_ups_shipment` = '" . pSQL($shipment['id_ups_shipment']) . "'"
            );
            $code = $shipmentStatus == 'DELIVERED' ? Constants::STATUS_DELIVERED : Constants::STATUS_SHIPPED;
            Db::getInstance()->update(
                'orders',
                array('current_state' => (int) $code),
                '`id_order` = ' . (int) $shipment['id_order']
            );
        }
        // get customer by order id
        $shipment['countrytoAD'] = $shipment['country'];

        // Update value of status
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($statuses as $status) {
            $shipment["status"] = $status['name'];
        }

        $shippingServiceName = $this->module::$shippingServices->getServiceNameByKey($shipment["shipping_service"]);

        $infoShoper                       = $this->getAddressCustomerByOrder($shipment['id_order']);
        $shipment['stateName']            = $infoShoper[0]['stateName'];
        $shipment['apStateName']          = $this->module->getStateNameByCode($shipment['state']);
        $shipment['customerAddressLine1'] = $infoShoper[0]['customerAddressLine1'];
        $shipment['customerAddressLine2'] = $infoShoper[0]['customerAddressLine2'];
        $shipment['cityCustomer']         = $infoShoper[0]['city'];
        $shipment['postcodeCustomer']     = $infoShoper[0]['postcode'];
        $shipment['country']              = $infoShoper[0]['country_name'];
        if (strpos($shipment['shipping_service'], '_AP_') !== false) {
            $shipment['shipping_service'] = 'To AP (' . $shippingServiceName . ')';
            $shipment['flagToAP']         = true;
        } else {
            $shipment['shipping_service'] = 'To Address (' . $shippingServiceName . ')';
            $shipment['flagToAP']         = false;
        }

        // Convert Accessorial Name
        if (isset($shipment["accessorials_service"]) && !empty($shipment["accessorials_service"])) {
            $arrayAccessorial = unserialize($shipment["accessorials_service"]);

            if (!empty($arrayAccessorial)) {
                $shipment["accessorials_service"] = $this->module->getListNamesAccessorial($arrayAccessorial);
            } else {
                $shipment['accessorials_service'] = '';
            }
        } else {
            $shipment["accessorials_service"] = '';
        }

        $shipment['product_details']   = $shipment['products'];
        $shipment['shipping_fee']      = round($shipment['shipping_fee'], 2);
        $shipment['currencyMerchant']  = CommonFunction::getCurrencyMerchant();
        $shipment['order_value']       = round($shipment['order_value'], 2);
        $shipment['shipping_address1'] = mb_convert_encoding(
            $shipment['shipping_address1'],
            'UTF-8',
            'HTML-ENTITIES'
        );
        $shipment['package_detail'] = Tools::strtolower($shipment['package_detail']);
        $shipment['package_detail'] = str_replace(
            ['kgs', 'in', 'lbs'],
            ['kg', 'Inch', 'Pounds'],
            $shipment['package_detail']
        );
        $shipment['status']         = $shipmentStatus;

        $collection = array();
        $collection[] = array(
            'trackingNumber' => $shipment['tracking_number'],
            'shipmentStatus' => $shipmentStatus,
        );

        try {
            $this->transferShipmentStatus($collection);
        } catch (Exception $e) {
        }

        $this->ajaxDie(json_encode($shipment));
    }

    public function showViewShipmentModal($value, $shipment)
    {
        return "<p class='action-disabled'
                    onclick=" . "showViewShipmentModal('" . $shipment['id'] . "');>
                    " . $value .
            "</p>";
    }

    private function listPkgs()
    {
        $listPkgs     = array();
        $listIndexPkg = unserialize(Configuration::get('UPS_PKG_DIMENSION_COUNT'));
        if (!empty($listIndexPkg)) {
            foreach ($listIndexPkg as $indexPkg) {
                array_push($listPkgs, unserialize(Configuration::get('UPS_PKG_' . $indexPkg . '_DIMENSION')));
            }
        }

        return $listPkgs;
    }

    // ======================== SQL FUNCTION ========================

    public function deleteShipment($ids_ups_shipment)
    {
        $ids_ups_shipment = $this->module->explodeIds($ids_ups_shipment);
        $sql              = new DbQuery();
        $sql->type('DELETE');
        $sql->from(Constants::DB_TABLE_SHIPMENT);
        $sql->where("id_ups_shipment IN (" . $ids_ups_shipment . ")");
        return Db::getInstance()->execute($sql);
    }

    public function sqlSelectShipment($id_ups_shipment)
    {
        $sql = new DbQuery();

        $sql->select('s.id_ups_shipment as id_ups_shipment');
        $sql->select('s.tracking_number as tracking_number');
        $sql->select('s.id_order as id_order');
        $sql->select('s.customer_name as customer_name');
        $sql->select('s.product as product_details');
        $sql->select('s.shipping_address1 as shipping_address1');
        $sql->select('s.shipping_address2 as shipping_address2');
        $sql->select('s.city as city');
        $sql->select('s.phone as phone');
        $sql->select('s.email as email');
        $sql->select('s.shipping_service as shipping_service');
        $sql->select('s.package_detail as package_detail');
        $sql->select('DATE(s.create_date) as date');
        $sql->select('TIME(s.create_date) as time');
        $sql->select('s.status as status');
        $sql->select('s.accessorials_service as accessorials_service');
        $sql->select('s.order_value as order_value');
        $sql->select('s.shipping_fee as shipping_fee');
        $sql->select('currency.iso_code as currency');

        $sql->from(Constants::DB_TABLE_SHIPMENT, 's');
        $sql->innerJoin('orders', 'o', 'o.id_order = s.id_order');
        $sql->leftJoin('currency', 'currency', 'o.id_currency = currency.id_currency');

        $sql->where("s.id_ups_shipment = '" . pSQL($id_ups_shipment) . "'");

        return Db::getInstance()->executeS($sql);
    }

    public function sqlSelectShipmentById($id)
    {
        $supQuery = "(SELECT GROUP_CONCAT(CONCAT(od.product_quantity,' x ', pl.name))
                    FROM " . pSQL($this->tblOrderDetail) . " `od`, "
        . pSQL($this->tblProductLang) . " pl" .
        " WHERE od.id_order = o.id_order
                            AND od.product_id = pl.id_product
                            AND pl.id_lang = " . (int) $this->languageId . "
                    )
                 as products";

        $sql = new DbQuery();

        $sql->select('s.id_ups_shipment as id_ups_shipment');
        $sql->select('s.tracking_number as tracking_number');
        $sql->select('s.id_order as id_order');
        $sql->select('s.customer_name as customer_name');
        $sql->select('s.product as product_details');
        $sql->select($supQuery);
        $sql->select('s.shipping_address1 as shipping_address1');
        $sql->select('s.shipping_address2 as shipping_address2');
        $sql->select('s.city as city');
        $sql->select('s.postcode as postalcode');
        $sql->select('s.phone as phone');
        $sql->select('s.email as email');
        $sql->select('s.shipping_service as shipping_service');
        $sql->select('s.package_detail as package_detail');
        $sql->select('DATE(s.create_date) as date');
        $sql->select('TIME(s.create_date) as time');
        $sql->select('s.status as status');
        $sql->select('s.accessorials_service as accessorials_service');
        $sql->select('s.order_value as order_value');
        $sql->select('s.shipping_fee as shipping_fee');
        $sql->select('cr.iso_code as currency');
        $sql->select('s.country as country');
        $sql->select('s.state as state');

        $sql->from(Constants::DB_TABLE_SHIPMENT, 's');
        $sql->innerJoin('orders', 'o', 'o.id_order = s.id_order');
        $sql->leftJoin('currency', 'cr', 'o.id_currency = cr.id_currency');

        $sql->where("s.id = '" . pSQL($id) . "'");

        return Db::getInstance()->executeS($sql);
    }

    private function sqlExportShipment($ids)
    {
        $sql = new DbQuery();

        $sql->select('s.id_ups_shipment as shipment_id');
        $sql->select('s.tracking_number as tracking_number');
        $sql->select('DATE(s.create_date) as date');
        $sql->select('TIME(s.create_date) as time');
        $sql->select('s.status as package_status');
        $sql->select('s.cod as cod');
        $sql->select('s.order_value as order_value');
        $sql->select('s.shipping_service as shipping_service');
        $sql->select('s.accessorials_service as accessorials');
        $sql->select('s.id_order as order_id');
        $sql->select("DATE(o.date_add) as order_date");
        $sql->select('o.total_paid as total_paid');
        $sql->select('cr.iso_code as currency');

        $sql->select('o.total_shipping as total_shipping');
        $sql->select('s.delivery_status as delivery_status');
        $sql->select('s.shipping_fee as shipping_fee');
        $sql->select('s.package_detail as package_detail');
        $sql->select('s.product as product_detail');
        $sql->select('s.customer_name as customer_name');
        $sql->select('s.email as email');
        // $sql->select('s.cod as cod');
        $sql->select('s.ap_id as ap_id');
        $sql->select('s.ap_name as ap_name');
        $sql->select('s.shipping_address1 as shipping_address1');
        $sql->select('s.shipping_address2 as shipping_address2');
        $sql->select('s.postcode as postcode_order');
        $sql->select('s.city as city_order');
        $sql->select('s.state as state_order');
        $sql->select('s.country as country_order');
        $sql->select('s.phone as phone');
        $sql->select('p.amount as amount');
        $sql->select('addr.address1 as customer_address1');
        $sql->select('addr.address2 as customer_address2');
        $sql->select('addr.postcode as customer_postcode');
        $sql->select('addr.city as customer_city');
        $sql->select('st.name as customer_state_name');
        $sql->select('country.name as country');

        $sql->from(Constants::DB_TABLE_SHIPMENT, 's');
        $sql->leftJoin(Constants::DB_TABLE_OPENORDER, 'op', 'op.id_order = s.id_order');
        $sql->leftJoin('orders', 'o', 'o.id_order = s.id_order');
        $sql->leftJoin('address', 'addr', 'addr.id_address = o.id_address_delivery');
        $sql->leftJoin(
            'country_lang',
            'country',
            'country.id_country = addr.id_country and country.id_lang = ' . (int) $this->languageId
        );
        $sql->leftJoin('order_invoice_payment', 'oi', 'oi.id_order = s.id_order');
        $sql->leftJoin('order_payment', 'p', 'oi.id_order_payment = p.id_order_payment');
        $sql->leftJoin('state', 'st', 'addr.id_state = st.id_state');
        $sql->innerJoin('currency', 'cr', 'cr.id_currency = o.id_currency');

        $sql->where("s.id in (" . $this->module->explodeIds($ids) . ")");

        return Db::getInstance()->executeS($sql);
    }

    public function getOrderStatusByOrder($idOrder)
    {
        $sql = new DbQuery();

        $sql->select('osl.name as deliveryStatus');
        $sql->from('order_history', 'oh');
        $sql->leftJoin(
            'order_state_lang',
            'osl',
            'oh.id_order_state = osl.id_order_state AND osl.id_lang = ' . (int) $this->languageId
        );

        $sql->where("oh.id_order = '" . (int) $idOrder . "'");
        return Db::getInstance()->executeS($sql);
    }

    public function getAccessPointInfoByOrder($idOrder)
    {
        $sql = new DbQuery();

        $sql->select('ap_name as upsAcessPointID');
        $sql->select('ap_address1 as accessPointAddressLine1');
        $sql->select('ap_address2 as accessPointAddressLine2');
        $sql->select('"" as accessPointAddressLine3');
        $sql->select('ap_city as accessPointCity');
        $sql->select('ap_state as accessPointStateOrProvince');
        $sql->select('ap_postcode as accessPointPostalCode');

        $sql->from('ups_openorder');
        $sql->where("id_order = '" . pSQL($idOrder) . "'");

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

    public function getAddressCustomerByOrder($idOrder)
    {
        $sql = new DbQuery();

        $sql->select('st.name as stateName');
        $sql->select('add.address1 as customerAddressLine1');
        $sql->select('add.address2 as customerAddressLine2');
        $sql->select('add.city as city');
        $sql->select('add.postcode as postcode');
        $sql->select('cl.name as country_name');
        $sql->select('st.iso_code as state_order');

        $sql->from('orders', 'o');
        $sql->leftJoin('address', 'add', 'o.id_address_delivery = add.id_address');
        $sql->leftJoin('country_lang', 'cl', 'add.id_country = cl.id_country');
        $sql->leftJoin('state', 'st', 'add.id_state = st.id_state');

        $sql->where("o.id_order = " . (int) $idOrder);
        $sql->where("cl.id_lang = " . (int) $this->languageId);

        return Db::getInstance()->executeS($sql);
    }

    private function getShippmentIdByIdStr($Ids)
    {
        $Ids = implode(',', array_map('intval', explode(',', $Ids)));
        $sql = new DbQuery();

        $sql->select('s.id_ups_shipment as id_ups_shipment');
        $sql->from(Constants::DB_TABLE_SHIPMENT, 's');
        $sql->where("s.id in (" . $Ids . ")");

        return Db::getInstance()->executeS($sql);
    }

    public function getShipments($trackingNumbers)
    {
        $tmpTrackingNumbers = $this->module->explodeIds($trackingNumbers);

        $subQuery = "
            SELECT sub.id_ups_shipment
            FROM " . pSQL($this->tblShipment) . " sub
            WHERE sub.id IN (" . $tmpTrackingNumbers . ")";

        $sql = new DbQuery();

        $sql->select('s.id_ups_shipment as id_ups_shipment');
        $sql->select('s.tracking_number as tracking_number');
        $sql->select('s.id_order as id_order');
        $sql->from(Constants::DB_TABLE_SHIPMENT, 's');
        $sql->where("s.id_ups_shipment IN (" . $subQuery . ")");

        return Db::getInstance()->executeS($sql);
    }


    private function setCsvHeader()
    {
        return array(
            "ShipmentID",
            "Date",
            "Time",
            "TrackingNumber",
            "DeliveryStatus",
            "COD",
            "CODAmount",
            "CODCurrency",
            "EstimatedShippingFee",
            "ShippingService",
            "Accessorials",
            "OrderID",
            "OrderDate",
            "OrderValue",
            "ShippingFee",
            "PackageDetails",
            "ProductDetails",
            "CustomerName",
            "CustomerAddressLine1",
            "CustomerAddressLine2",
            "CustomerAddressLine3",
            "CustomerPostalCode",
            "CustomerPhoneNo",
            "CustomerCity",
            "CustomerStateOrProvince",
            "CustomerCountry",
            "CustomerEmail",
            "AlternaetDeliveryAddressIndicator",
            "UPSAcessPointID",
            "AccessPointAddressLine1",
            "AccessPointAddressLine2",
            "AccessPointAddressLine3",
            "AccessPointCity",
            "AccessPointStateOrProvince",
            "AccessPointPostalCode",
            "AccessPointCountry",
        );
    }

    public function getTextTermandConditions()
    {
        $textFirst  = $this->sdk->t('shipment', 'txtFistTandC');
        $textSecond = $this->sdk->t('shipment', 'txtSecondTandC');
        $textHtml   = '<a href="#" onclick="showPopup();">';
        return $textFirst . ' ' . $textHtml . ' ' . $textSecond . '</a>';
    }
}
