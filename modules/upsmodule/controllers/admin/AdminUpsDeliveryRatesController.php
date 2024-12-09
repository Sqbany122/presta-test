<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsDeliveryRatesController extends CommonController
{
    public $errors;
    private $arrErrorMessage = array();
    private $listSpServiceAp = array();
    private $listSpServiceAdd = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table     = 'configuration';
        parent::__construct();

        $result = CommonFunction::checkScreenConfig();
        if ($result !== 'DONE' &&
            $result !== Tools::getValue('controller')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink($result));
        }

        $this->listSpServiceAp  = $this->getListSpServiceActive($this->module::$shippingServices->getServicesAp());
        $this->listSpServiceAdd = $this->getListSpServiceActive($this->module::$shippingServices->getServicesAdd());
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsDeliveryRates');
    }

    public function initContent()
    {
        $keys = array();
        if ($this->module->usa()) {
            $keys['txtDeliveryUPSAp'] = 'txtDeliveryUPSApUS';
            $txtNote = '';
            $textHeader0 = $this->sdk->t('deliveryrate', 'txtDeliveryHeader0');
            $textHeader1 = $this->sdk->t('deliveryrate', 'txtDeliveryHeader1');
            $textHeader2 = $this->sdk->t('deliveryrate', 'txtDeliveryHeader2');
            $textHeader1Link = $this->sdk->t('deliveryrate', 'txtDeliveryHeader1Link');
            $txtDeliveryYourShopper = '';
            $us = 1;
        } else {
            $keys['txtDeliveryUPSAp'] = 'txtDeliveryUPSAp';
            $txtNote = $this->sdk->t('deliveryrate', 'txtNote');
            $textHeader0 = '';
            $textHeader1 = '';
            $textHeader2 = '';
            $textHeader1Link = '';
            $txtDeliveryYourShopper = $this->sdk->t('deliveryrate', 'txtDeliveryYourShopper');
            $us = 0;
        }

        $texts = array(
            'txtDelivery'                => $this->sdk->t('deliveryrate', 'txtDelivery'),
            'txtDeliveryCurrency'        => $this->sdk->t('ups', 'txtCurrency'),
            'txtDeliveryUPSAp'           => $this->sdk->t('deliveryrate', $keys['txtDeliveryUPSAp']),
            'txtDeliveryFlatRates'       => $this->sdk->t('deliveryrate', 'txtDeliveryFlatRates'),
            'txtDeliveryRealTime'        => $this->sdk->t('deliveryrate', 'txtDeliveryRealTime'),
            'txtDeliveryValueThresholds' => $this->sdk->t('deliveryrate', 'txtDeliveryValueThresholds'),
            'txtDeliveryRates'           => $this->sdk->t('deliveryrate', 'txtDeliveryRates'),
            'txtDeliveryRatesIs'         => $this->sdk->t('deliveryrate', 'txtDeliveryRatesIs'),
            'txtDeliveryShippingRates'   => $this->sdk->t('deliveryrate', 'txtDeliveryShippingRates'),
            'txtDeliveryYourShopper'     => $txtDeliveryYourShopper,
            'txtDeliveryUPSAdd'          => $this->sdk->t('deliveryrate', 'txtDeliveryUPSAdd'),
            'txtDeliverySave'            => $this->sdk->t('button', 'txtSave'),
            'txtPkgSave'                 => $this->sdk->t('button', 'txtSave'),
            'txtNext'                    => $this->sdk->t('button', 'txtNext'),
            'txtNote'                    => $txtNote,
        );

        $arrSpServiceAp  = array();
        $arrSpServiceAdd = array();

        if (Configuration::get('UPS_SP_SERV_AP_DELIVERY') == 1) {
            $arrSpServiceAp = $this->getListConfig($this->listSpServiceAp);
            if ($this->module->usa()) {
                $arrSpServiceAp = $this->setDefaultUS($arrSpServiceAp);
            }
        }

        if (Configuration::get('UPS_SP_SERV_ADDRESS_DELIVERY') == 1) {
            $arrSpServiceAdd = $this->getListConfig($this->listSpServiceAdd);
            if ($this->module->usa()) {
                $arrSpServiceAdd = $this->setDefaultUS($arrSpServiceAdd);
            }
        }

        $currency = Currency::getCurrencyInstance(Configuration::get('PS_CURRENCY_DEFAULT'));

        $pathTpl = _PS_MODULE_DIR_
            . $this->module->name
            . '/views/templates/admin/ups_delivery_rates/delivery_rates.tpl';

        $listVar = array(
            'arrtext'          => $texts,
            'listServiceAp'    => $arrSpServiceAp,
            'listServiceAdd'   => $arrSpServiceAdd,
            'listCurrency'     => Constants::LIST_CURRENCY,
            'selectedCurrency' => $currency->iso_code,
            'us' => $us,
            'textHeader0' => $textHeader0,
            'textHeader1' => $textHeader1,
            'textHeader2' => $textHeader2,
            'textHeader1Link' => $textHeader1Link,
        );

        $this->content = $this->context->smarty->createTemplate($pathTpl, null, null, $listVar)->fetch();
        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));

        parent::initContent();
    }
    public function setDefaultUS($service)
    {
        $adminUpsDeliveryRates =  Configuration::get('UPS_SAVE_ADMIN_DELIVERY_RATES');
        //Chưa lưu thì set default check all
        if (empty($adminUpsDeliveryRates)) {
            $newArray = array();
            foreach ($service as $item) {
                $item['val'] = 100;
                $item['type'] = 'REAL_TIME';
                $newArray[] = $item;
            }
            return $newArray;
        } else {
            return $service;
        }
    }
    private function getListConfig($arrayService)
    {
        $listService = array();

        foreach ($arrayService as $item) {
            $serviceType = Configuration::get($item['key']);
            $serviceVal  = ($serviceType == 'FLAT_RATE') ? unserialize(Configuration::get($item['keyVal'])) :
            Configuration::get($item['keyVal']);
            $service = array(
                'type' => $serviceType,
                'name' => $item['name'],
                'key'  => $item['key'],
                'val'  => $serviceVal,
            );

            array_push($listService, $service);
        }

        return $listService;
    }

    private function getListSpServiceActive($listSpService)
    {
        $arrayShipToAp = array();

        foreach ($listSpService as $service) {
            if (Configuration::get($service['key'])) {
                $arrCurent = array(
                    'key'    => $service['keyDeli'],
                    'keyVal' => $service['keyVal'],
                    'name'   => $service['name'],
                );

                array_push($arrayShipToAp, $arrCurent);
            }
        }

        return $arrayShipToAp;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitDeliveryRates') || Tools::isSubmit('submitNext')) {
            $services = array_merge($this->listSpServiceAp, $this->listSpServiceAdd);
            $result = $this->validate($services);
            //Set Save Screen AdminUpsDeliveryRates
            Configuration::updateValue('UPS_SAVE_ADMIN_DELIVERY_RATES', 1);

            if ($result) {
                $this->updateConfig($services);
                Configuration::updateValue('UPS_DELI_CURRENCY', Tools::getValue('currency'));
                $this->transferData();
                if (Tools::isSubmit('submitNext')) {
                    CommonFunction::setDoneConfigScreen(Tools::getValue('controller'));
                    Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsBillPref'));
                } else {
                    Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsDeliveryRates'));
                }
            } else {
                $message = $this->sdk->t('err-msg', 'notValid');
                if ($this->module->usa()) {
                    $message = $this->sdk->t('err-msg', 'notValidUS');
                }
                $this->errors = $message;
            }
        }
    }

    private function transferData()
    {
        if (!Configuration::get('UPS_CONFIG_DONE')) {
            return false;
        }

        $shippingServices = $this->getShippingServicesActived();
        $shippingServices = $this->getDeliveryOptions($shippingServices);
        $shippingServices = $this->removeElementWithValue($shippingServices, 'keyVal');
        $this->transferDeliveryRates(
            array(
                'merchantKey' => Configuration::get('MERCHANT_KEY'),
                'deliveryRates' => $shippingServices
            )
        );
    }

    private function updateConfig($listService)
    {
        $errorUpdate = '';
        foreach ($listService as $service) {
            $serviceKey = $service['key'];
            $type = $serviceKey . '_Type';
            if (Tools::getIsset($type)) {
                $serviceType = Tools::getValue($type);
                Configuration::updateValue($serviceKey, $serviceType);

                if ($serviceType == 'FLAT_RATE') {
                    $countRates = Tools::getValue($serviceKey);
                    $countRates = max(min(100, $countRates), 0);
                    $listRates  = array();

                    for ($index = 0; $index < $countRates; $index++) {
                        $minValue = $serviceKey . '_MinValue_' . $index;
                        $deliRate = $serviceKey . '_DeliRate_' . $index;
                        if (Tools::getIsset($minValue) && Tools::getIsset($deliRate)) {
                            $valueMin  = (float) Tools::getValue($minValue);
                            $valueDeli = (float) Tools::getValue($deliRate);
                            if (Tools::getIsset($minValue) && Tools::getIsset($deliRate)) {
                                $rate = array(
                                    'MinValue' => $valueMin,
                                    'DeliRate' => $valueDeli,
                                );

                                array_push($listRates, $rate);
                            }
                        }
                    }

                    Configuration::updateValue($service['keyVal'], serialize($listRates));
                } elseif (Tools::getIsset($serviceKey . '_Percent')) { //$serviceType == 'REAL_TIME'
                    Configuration::updateValue($service['keyVal'], (float) Tools::getValue($serviceKey . '_Percent'));
                }
            }
        }

        return $errorUpdate;
    }

    private function validate($listService)
    {
        $result = true;
        foreach ($listService as $service) {
            $serviceKey = $service['key'];
            $type = $serviceKey . '_Type';
            if (Tools::getIsset($type)) {
                if (Tools::getValue($type) == 'FLAT_RATE') {
                    $result = $this->a($serviceKey);
                } else {
                    $realtime = $serviceKey . '_Percent';
                    if (!Tools::getIsset($realtime)
                        || !$this->validateRealRate(Tools::getValue($realtime))) {
                            $result = false;
                    }
                }
            }
            if (!$result) {
                break;
            }
        }

        return $result;
    }

    private function a($serviceKey)
    {
        $result = true;
        $countRates = 0;
        $arrRates   = array();

        if (Tools::getIsset($serviceKey)) {
            $countRates = Tools::getValue($serviceKey);
        }

        for ($index = 0; $index < $countRates; $index++) {
            $minValue = $serviceKey . '_MinValue_' . $index;
            $deliRate = $serviceKey . '_DeliRate_' . $index;

            if (Tools::getIsset($minValue) && Tools::getIsset($deliRate)) {
                array_push($arrRates, array(
                    'minValue' => Tools::getValue($minValue),
                    'deliRate' => Tools::getValue($deliRate),
                ));

                if (!$this->validateFlatRate(Tools::getValue($minValue))) {
                    $result = false;
                }

                if (!$this->validateFlatRate(Tools::getValue($deliRate))) {
                    $result = false;
                }
            }
        }

        if ($this->hasDuplicated($arrRates)) {
            $result = false;
        }

        return $result;
    }

    private function validateFlatRate($rate)
    {
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $rate) || Tools::strlen($rate) < 1 || Tools::strlen($rate) > 15) {
            return false;
        }

        return true;
    }

    private function validateRealRate($rate)
    {
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $rate) || (float) $rate > 400 || (float) $rate < 1) {
            return false;
        }

        return true;
    }

    private function getErrorsByKey($keyErrors, $serviceName)
    {
        return $this->sdk->t('deliveryrate', $keyErrors, array('serviceName' => $serviceName));
    }

    private function hasDuplicated($arrRates)
    {
        $arrayMinValue = array_column($arrRates, 'minValue');
        return count($arrayMinValue) != count(array_unique($arrayMinValue, SORT_REGULAR));
    }
}
