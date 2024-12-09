<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonFunction.php';
require_once dirname(__FILE__) . '/../../common/Constants.php';

class UpsModuleEshoperModuleFrontController extends ModuleFrontController
{
    public $sdk;

    public function initContent()
    {
        $this->sdk = new Ups\Sdk(
            array(
                'language' => $this->context->language->iso_code,
                'dbQuery' => new DbQuery(),
                'dbInstance' => Db::getInstance(),
            )
        );
    }

    public function displayAjaxChangeShippingService()
    {
        ob_end_clean();
        header('Content-Type: application/json');

        $context = Context::getContext();
        $context->cookie->__unset('selectedShippingService');
        $context->cookie->__unset('shippingFee');
        $context->cookie->__set('selectedShippingService', Tools::getValue('selectedShippingService'));
        $context->cookie->__set('shippingFee', Tools::getValue('selectedShippingFee'));
        $context->cookie->write();

        $shippingService = array(
            'selectedShippingService' => $context->cookie->selectedShippingService,
            'selectedShippingFee'     => $context->cookie->shippingFee,
        );

        $this->ajaxDie(json_encode($shippingService));
    }

    public function displayAjaxSelectAccessPoint()
    {
        ob_end_clean();
        header('Content-Type: application/json');

        $context = Context::getContext();
        $context->cookie->__unset('selectedAcessPointAddress');
        $context->cookie->__set('selectedAcessPointAddress', Tools::getValue('acessPointAddress'));
        Configuration::updateValue('UPS_SELECTED_ACESSPOINT_ADDRESS', Tools::getValue('acessPointAddress'));
        $context->cookie->write();
        $accessPoint = array(
            'selectedAddress' => $context->cookie->selectedAcessPointAddress,
        );

        $this->ajaxDie(json_encode($accessPoint));
    }

    public function displayAjaxSearchAccessPoint()
    {
        ob_end_clean();
        header('Content-Type: application/json');
        $indexArrayListInforAP = 1;
        $numberApVisible = Configuration::get('UPS_SP_SERV_AP_NUM_VISIBLE');
        $nearbyDistance  = Configuration::get('UPS_SP_SERV_AP_RANGE_DISPLAY');
        $fullAddress     = Tools::getValue('fullAddress');
        $countryCode     = Tools::getValue('country');
        $selectedService     = Tools::getValue('selectedService');
        $unitOfMeasurement = "KM";
        if ($this->module->usa()) {
            $unitOfMeasurement = "MI";
        }
        $locale = $this->module->getLocale();
        $sign = Configuration::get('UPS_SP_ORDERS_REQUIRE_ADULT_SIGNATURES');
        $numberVisible = '';
        if (Configuration::get('UPS_COD_ENABLE') || strpos($selectedService, "SATDELI") !== false) {
            $numberVisible = (string)($numberApVisible * 2);
        } else if ($this->module->usa() && $sign == 1) {
            $numberVisible = (string)($numberApVisible * 2);
        } else {
            $numberVisible = (string) $numberApVisible;
        }

        $locatorInfo     = array(
            'fullAddress'       => $fullAddress,
            'countryCode'       => $countryCode,
            'nearby'            => $nearbyDistance,
            'locale'            => $locale,
            'unitOfMeasurement' => $unitOfMeasurement,
            'maximumListSize'   => $numberVisible,
            'sdk'             => $this->sdk,
        );

        $respone = $this->module->callLocatorAPI($locatorInfo);
        $arrayLocatorGeoCode = array();
        $arrayLocatorInfo    = array();
        $selectAddress       = array();

        if ($respone['Description'] == 'Success' && !empty($respone['Data'])) {
            $arrayLocations      = $this->listResponeData($respone['Data']);
            $arrAccessPointValid = $this->listAccessPointValid($arrayLocations, $countryCode);

            if (!empty($arrAccessPointValid)) {
                if ($this->module->usa() && $sign == 1) {
                    foreach ($arrAccessPointValid as $locator) {
                        $serviceOffering = $locator->ServiceOfferingList->ServiceOffering;
                        $check = 0;
                        foreach ($serviceOffering as $item) {
                            if ($item->Code == '013') {
                                $check = 1;
                                break;
                            }
                        }
                        if ($check == 1) {
                            $locatorInfo = $this->alterLocatorInfo($locator, $indexArrayListInforAP, $selectedService);
                            if (!empty($locatorInfo)) {
                                $arrayLocatorGeoCode[] = $locator->Geocode->Latitude . ', ' . $locator->Geocode->Longitude;
                                $arrayLocatorInfo[] = $locatorInfo;
                                $selectAddress[]       = array(
                                    'AccessPointId' => $locator->AccessPointInformation->PublicAccessPointID,
                                    'AddressInfo' => $locator->AddressKeyFormat,
                                );
                                $indexArrayListInforAP++;
                            }
                        }
                        if ($indexArrayListInforAP > $numberApVisible) {
                            break;
                        }
                    }
                } elseif (Configuration::get('UPS_COD_ENABLE')) {
                    foreach ($arrAccessPointValid as $locator) {
                        $locatorInfo = $this->alterLocatorInfo($locator, $indexArrayListInforAP, $selectedService);
                        if (!empty($locatorInfo)) {
                            $arrayLocatorGeoCode[] = $locator->Geocode->Latitude . ', ' . $locator->Geocode->Longitude;
                            $arrayLocatorInfo[] = $locatorInfo;
                            $selectAddress[] = array(
                                'AccessPointId' => $locator->AccessPointInformation->PublicAccessPointID,
                                'AddressInfo' => $locator->AddressKeyFormat,
                            );
                            $indexArrayListInforAP++;
                        }
                        if ($indexArrayListInforAP > $numberApVisible) {
                            break;
                        }
                    }
                } else {
                    foreach ($arrAccessPointValid as $locator) {
                        $locatorInfo = $this->alterLocatorInfo($locator, $indexArrayListInforAP, $selectedService);
                        if (!empty($locatorInfo)) {
                            $arrayLocatorGeoCode[] = $locator->Geocode->Latitude . ', ' . $locator->Geocode->Longitude;
                            $arrayLocatorInfo[] = $locatorInfo;
                            $selectAddress[]       = array(
                                'AccessPointId' => $locator->AccessPointInformation->PublicAccessPointID,
                                'AddressInfo' => $locator->AddressKeyFormat,
                            );
                            $indexArrayListInforAP++;
                        }
                        if ($indexArrayListInforAP > $numberApVisible) {
                            break;
                        }
                    }
                }
            }
        }

        $pathFileChild = _PS_MODULE_DIR_ . 'upsmodule' . '/views/templates/front/_partials/content_accesspoint.tpl';

        $this->ajaxDie(Tools::jsonEncode(array(
            'preview' => $this->renderOverrideTemplates($pathFileChild, array(
                'Infor'         => $arrayLocatorInfo,
            )),
            'Description' => $respone['Description'],
            'arrGeoCode'    => $arrayLocatorGeoCode,
            'selectAddress' => json_encode($selectAddress),
        )));
    }

    private function listResponeData($resPonseData)
    {
        $listResponseData = array();

        if (count($resPonseData) == 1) {
            $listResponseData[] = $resPonseData;
        } else {
            $listResponseData = $resPonseData;
        }

        return $listResponseData;
    }

    private function listAccessPointValid($arrResponeLocator, $countryCode)
    {
        $arrReturn = array();
        $isEuCountry = in_array($countryCode, Constants::COUNTRY_CODE_EU);

        foreach ($arrResponeLocator as $locator) {
            if ($this->checkCodAccessPoint($locator, $isEuCountry)) {
                $arrReturn[] = $locator;
            }
        }

        return $arrReturn;
    }

    private function checkCodAccessPoint($locator, $isEuCountry)
    {
        $serviceOfferings = isset($locator->ServiceOfferingList->ServiceOffering) ?
            $locator->ServiceOfferingList->ServiceOffering : '';

        if (empty($serviceOfferings)) {
            return false;
        }

        $arrServiceOffering = $this->getArrServiceOffering($serviceOfferings);
        $arrCode            = array_column($arrServiceOffering, 'Code');

        $sign = Configuration::get('UPS_SP_ORDERS_REQUIRE_ADULT_SIGNATURES');
        if ($this->module->usa() && $sign == 1) {
            $arrCode[] = '013';
        }

        if ($isEuCountry && Configuration::get('UPS_COD_ENABLE')) {
            return (in_array(Constants::SERVICE_OFFERING_001, $arrCode)
                && in_array(Constants::SERVICE_OFFERING_011, $arrCode));
        }

        return in_array(Constants::SERVICE_OFFERING_001, $arrCode);
    }

    private function getArrServiceOffering($serviceOfferings)
    {
        $arrServiceOffering = array();

        foreach ($serviceOfferings as $service) {
            $arrServiceOffering[] = array(
                'Code'        => $service->Code,
                'Description' => $service->Description,
            );
        }

        return $arrServiceOffering;
    }

    private function alterLocatorInfo($locator, $indexArrayListInforAP, $selectedService)
    {
        $add = $locator->AddressKeyFormat;

        $state = isset($add->PoliticalDivision1) ? ", $add->PoliticalDivision1" : '';
        $city = isset($add->PoliticalDivision2) ? ", $add->PoliticalDivision2" : '';
        $primaryPostCode = isset($add->PostcodePrimaryLow) ? ", $add->PostcodePrimaryLow" : '';
        $extendPostCode = isset($add->PostcodeExtendedLow) ? ", $add->PostcodeExtendedLow" : '';
        $line = $add->AddressLine . $city . $state . $primaryPostCode . $extendPostCode;
        $dayOfWeek = $locator->OperatingHours->StandardHours->DayOfWeek;
        $saturday = $dayOfWeek[6];
        if (strpos($selectedService, "SATDELI") !== false && !empty($saturday) && isset($saturday->ClosedIndicator)) {
            return array();
        }
        $operationTime = $this->arrOperationTime($dayOfWeek);

        $unit = "km";
        if (!empty($locator->Distance->UnitOfMeasurement->Description)) {
            $unit = Tools::strtolower($locator->Distance->UnitOfMeasurement->Description) == 'kilometers' ? 'km': 'Miles';
        } else {
            if (!empty($locator->AddressKeyFormat->CountryCode) &&
                Tools::strtolower($locator->AddressKeyFormat->CountryCode) == 'us'
            ) {
                $unit = "Miles";
            }
        }

        return array(
            'indexArrayListInforAP'    => $indexArrayListInforAP,
            'name' => html_entity_decode($add->ConsigneeName),
            'address' => html_entity_decode($line),
            'distance' => $locator->Distance->Value,
            'unit' => $unit,
            'operatingHours' => $operationTime,
            'StandardHoursOfOperation' => $locator->StandardHoursOfOperation,
            'txtSelect' => $this->sdk->t('ups', 'txtSelect'),
            'txtE_ShoppingAPOperating' => $this->sdk->t('ups', 'txtE_ShoppingAPOperating'),
            'txtE_ShoppingAPopen' => $this->sdk->t('ups', 'txtE_ShoppingAPopen'),
            'txtE_ShoppingAPclose' => $this->sdk->t('ups', 'txtE_ShoppingAPclose'),
        );
    }

    private function arrOperationTime($openCloseHours)
    {
        $arrInforTimeAPs = array();

        $dayOfWeek = array(
            "1" => $this->sdk->t('ups', 'txtE_ShoppingAPSunday'),
            "2" => $this->sdk->t('ups', 'txtE_ShoppingAPMonday'),
            "3" => $this->sdk->t('ups', 'txtE_ShoppingAPTuesday'),
            "4" => $this->sdk->t('ups', 'txtE_ShoppingAPWednesday'),
            "5" => $this->sdk->t('ups', 'txtE_ShoppingAPThursday'),
            "6" => $this->sdk->t('ups', 'txtE_ShoppingAPFriday'),
            "7" => $this->sdk->t('ups', 'txtE_ShoppingAPSaturday'),
        );

        if ($openCloseHours) {
            foreach ($openCloseHours as $openDay) {
                $arrInforTimeAP = array();
                $arrInforTimeAP['dayOfWeek'] = $dayOfWeek[$openDay->Day];

                if (isset($openDay->OpenHours) && isset($openDay->CloseHours)) {
                    $arrTimeOpenClose = $this->convertOperationTime($openDay->OpenHours, $openDay->CloseHours);
                    $arrInforTimeAP = array_merge($arrInforTimeAP, $arrTimeOpenClose);
                } elseif (isset($openDay->Open24HoursIndicator)) {
                    $arrInforTimeAP['timeOpen'] = $this->sdk->t('ups', 'txtE_APOpenAllTime');
                } else {
                    $arrInforTimeAP['timeOpen'] = $this->sdk->t('ups', 'txtE_ShoppingAPclosed');
                }

                $arrInforTimeAPs[] = $arrInforTimeAP;
            }
        }

        return $arrInforTimeAPs;
    }

    private function convertOperationTime($openHours, $closeHours)
    {
        $arrInforTimeAP = array();

        if (is_array($openHours) && is_array($closeHours)) {
            $arrInforTimeAP['timeOpen'] = $openHours;
            $arrInforTimeAP['timeClose'] = $closeHours;
        } else {
            $arrInforTimeAP['timeOpen']  = array($openHours);
            $arrInforTimeAP['timeClose'] = array($closeHours);
        }

        $arrInforTimeAP['timeOpen'] = array_map("CommonFunction::formatDisplayTime", $arrInforTimeAP['timeOpen']);
        $arrInforTimeAP['timeClose'] = array_map("CommonFunction::formatDisplayTime", $arrInforTimeAP['timeClose']);

        return $arrInforTimeAP;
    }

    protected function renderOverrideTemplates($template, $params = array())
    {
        $templateContent = '';
        $scope = $this->context->smarty->createData(
            $this->context->smarty
        );

        $scope->assign($params);

        $tpl = $this->context->smarty->createTemplate(
            $template,
            $scope
        );

        $templateContent = $tpl->fetch();

        return $templateContent;
    }
}
