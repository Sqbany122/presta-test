<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsShippingServicesController extends CommonController
{
    const PRE_AP                = 'selection_ap';
    const PRE_ADD               = 'selection_add';
    const MAX_NUMBER_AP_VISIBEL = 10;
    const LIST_RANGE            = array(5, 10, 15, 20, 30, 50);
    const LIST_STR_HOURS        = array(
        '00' => '12 AM',
        '01' => '1 AM',
        '02' => '2 AM',
        '03' => '3 AM',
        '04' => '4 AM',
        '05' => '5 AM',
        '06' => '6 AM',
        '07' => '7 AM',
        '08' => '8 AM',
        '09' => '9 AM',
        '10' => '10 AM',
        '11' => '11 AM',
        '12' => '12 PM',
        '13' => '1 PM',
        '14' => '2 PM',
        '15' => '3 PM',
        '16' => '4 PM',
        '17' => '5 PM',
        '18' => '6 PM',
        '19' => '7 PM',
        '20' => '8 PM',
        '21' => '9 PM',
        '22' => '10 PM',
        '23' => '11 PM',
        '24' => 'Disable',
    );
    private $listShipServiceAp;
    private $listShipServiceAdd;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $result = CommonFunction::checkScreenConfig();
        if ($result !== 'DONE' &&
            $result !== Tools::getValue('controller')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink($result));
        }
        $this->setDefaultUS();
        $this->listShipServiceAp  = $this->createArrService($this->module::$shippingServices->getServicesAp());
        $this->listShipServiceAdd = $this->createArrService($this->module::$shippingServices->getServicesAdd());
        $this->fields_form  = $this->createFieldsForm();
        $this->fields_value = $this->createFieldsValue();
    }
    public function setDefaultUS()
    {
        if ($this->module->usa()) {
            Configuration::updateValue('UPS_SP_SERV_AP_DELIVERY', 1);
            Configuration::updateValue('UPS_SP_SERV_ADDRESS_DELIVERY', 1);
            //Check Screen Configuration
            $configScreen =  unserialize(Configuration::get('UPS_CONFIG_SCREEN_STATUS'));
            $adminUpsShippingServices = 0;
            foreach ($configScreen as $item) {
                if ($item['class_name'] == 'AdminUpsShippingServices') {
                    if ($item['status'] == 1) {
                        $adminUpsShippingServices = 1;
                    }
                    break;
                }
            }
            //Chưa lưu thì set default check all
            if ($adminUpsShippingServices == 0) {
                $listShipServiceAp = $this->module::$shippingServices->getServicesAp();
                $listShipServiceAdd = $this->module::$shippingServices->getServicesAdd();
                foreach ($listShipServiceAp as $item) {
                    Configuration::updateValue($item['key'], 1);
                }
                foreach ($listShipServiceAdd as $item) {
                    Configuration::updateValue($item['key'], 1);
                }
                Configuration::updateValue('UPS_SP_SERV_SET_DEFAULT_SHIPPING_OPTION', 0);
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/upsshippingservices.css');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upsshippingservices.js');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsShippingServices');
    }

    public function initContent()
    {
        $this->content = $this->renderForm();

        parent::initContent();
    }

    private function createListAccountNumber()
    {
        $arrayAccountNumber = array();
        $accounts           = $this->module->getListAccount();
        if ($accounts != null) {
            foreach ($accounts as $value) {
                $listTmp = $value['AddressType'] . ' (' . $value['AccountNumber'] . ')';

                $arrayAccountNumber[] = array(
                    'id'   => $value['AccountNumber'],
                    'name' => $listTmp,
                );
            }
        } else {
            $arrayAccountNumber = null;
        }

        return $arrayAccountNumber;
    }

    private function createArrCutOffTime()
    {
        $arrayCutOffTime = array();

        foreach (self::LIST_STR_HOURS as $intHour => $strHour) {
            $arrayCutOffTime[] = array(
                'id_time' => $intHour,
                'name'    => $strHour,
            );
        }

        return $arrayCutOffTime;
    }

    private function createArrService($listService)
    {
        $arrService = array();
        $outside = $this->sdk->t('shippingservice', 'outside_eu');
        foreach ($listService as $service) {
            $name = $service['name'];
            if ($service['Ratecode'] == '08' && !$this->module->usa()) {
                $name .= "<p class=\'help-block\'>" . $outside . '</p>';
            } else {
                if ($this->module->usa()) {
                    if (in_array($service['Ratecode'], ['11','65','08','07','54'])) {
                        $name .= '&nbsp;' . $this->sdk->t('shippingservice', 'outside_us_line2');
                    } else {
                        $name .= '&nbsp;' . $this->sdk->t('shippingservice', 'outside_us_line1');
                    }
                }
            }
            $arrService[] = array(
                'id_service' => $service['key'],
                'name'       => $name,
            );
        }

        return $arrService;
    }
    //
    private function createArraySingatures()
    {
        $rraySingatures = array();
        $rraySingatures[] = array('id_singatures' => 0, 'name' => 'No');
        $rraySingatures[] = array('id_singatures' => 1, 'name' => 'Yes');

        return $rraySingatures;
    }
    
    private function createArrayApVisible()
    {
        $arrayApVisible = array();

        for ($index = 3; $index <= self::MAX_NUMBER_AP_VISIBEL; $index++) {
            $arrayApVisible[] = array(
                'id_visible' => $index,
                'name'       => $index,
            );
        }

        return $arrayApVisible;
    }

    private function createArrApRange()
    {
        $arrRange = array();

        foreach (self::LIST_RANGE as $range) {
            $arrRange[] = array(
                'id_range' => $range,
                'name'     => $range,
            );
        }

        return $arrRange;
    }

    private function createFieldsValue()
    {
        $arrValueConfigShipAp  = $this->getConfigListService($this->listShipServiceAp, self::PRE_AP);
        $arrValueConfigShipAdd = $this->getConfigListService($this->listShipServiceAdd, self::PRE_ADD);

        $arrValueConfig = array(
            // AP
            'UPS_SP_SERV_AP_DELIVERY'                 => Configuration::get('UPS_SP_SERV_AP_DELIVERY'),
            'UPS_SP_SERV_SET_DEFAULT_SHIPPING_OPTION' => Configuration::get('UPS_SP_SERV_SET_DEFAULT_SHIPPING_OPTION'),
            'UPS_SP_SERV_AP_NUM_VISIBLE'              => Configuration::get('UPS_SP_SERV_AP_NUM_VISIBLE'),
            'UPS_SP_SERV_AP_RANGE_DISPLAY'            => Configuration::get('UPS_SP_SERV_AP_RANGE_DISPLAY'),
            'UPS_SP_SERV_AP_CHOOSE_ACC'               => Configuration::get('UPS_SP_SERV_AP_CHOOSE_ACC'),

            // ADDRESS
            'UPS_SP_SERV_ADDRESS_DELIVERY'            => Configuration::get('UPS_SP_SERV_ADDRESS_DELIVERY'),
            'UPS_SP_SERV_ADDRESS_CHOOSE_ACC'          => Configuration::get('UPS_SP_SERV_ADDRESS_CHOOSE_ACC'),
            'UPS_SP_SERV_CUT_OFF_TIME'                => Configuration::get('UPS_SP_SERV_CUT_OFF_TIME'),
            'UPS_SP_ORDERS_REQUIRE_ADULT_SIGNATURES'  => Configuration::get('UPS_SP_ORDERS_REQUIRE_ADULT_SIGNATURES'),
            //Oders require Adult Singatures
        );

        $arrfieldsValues = array();
        $arrfieldsValues = array_merge($arrfieldsValues, $arrValueConfigShipAp);
        $arrfieldsValues = array_merge($arrfieldsValues, $arrValueConfigShipAdd);
        $arrfieldsValues = array_merge($arrfieldsValues, $arrValueConfig);

        return $arrfieldsValues;
    }

    private function getConfigListService($listService, $prefixName)
    {
        $arrValueConfig = array();

        foreach ($listService as $service) {
            $arrValueConfig[$prefixName . '_' . $service['id_service']] = Configuration::get($service['id_service']);
        }

        return $arrValueConfig;
    }

    public function createFieldsForm()
    {
        $txtKeys = array();
        if ($this->module->usa()) {
            $txtKeys['ttlAP'] = 'ttlAPUS';
            $txtKeys['txtAPDesc'] = 'txtAPDescUS';
            $txtKeys['txtAPNumber'] = 'txtAPNumberUS';
            $txtKeys['txtAPRange'] = 'txtAPRangeUS';
            
            $txtCotDesc1 = $this->sdk->t('shippingservice', 'txtCotDesc1US');
            $txtCotDesc2 = $this->sdk->t('shippingservice', 'txtCotDesc2US');
            $txtCotDesc3 = '';
            $array = array(
                        'type'   => 'switch',
                        'label'  => $this->sdk->t('shippingservice', 'txtAdultSingature') . ':',
                        'name'   => 'UPS_SP_ORDERS_REQUIRE_ADULT_SIGNATURES',
                        'values' => array(
                            array(
                                'id_configuration' => 'UPS_SP_ORDERS_REQUIRE_ADULT_SIGNATURES',
                                'value'            => 1,
                            ),
                            array(
                                'id_configuration' => 'UPS_SP_ORDERS_REQUIRE_ADULT_SIGNATURES',
                                'value'            => 0,
                            ),
                        ),
                    );
        } else {
            $txtKeys['ttlAP'] = 'ttlAP';
            $txtKeys['txtAPDesc'] = 'txtAPDesc';
            $txtKeys['txtAPNumber'] = 'txtAPNumber';
            $txtKeys['txtAPRange'] = 'txtAPRange';
            
            $txtCotDesc1 = $this->sdk->t('shippingservice', 'txtCotDesc');
            $txtCotDesc2 = $this->sdk->t('shippingservice', 'txtCotDesc2');
            $txtCotDesc3 = $this->sdk->t('shippingservice', 'txtCotDesc3');
            $array = null;
        }
        $txtAPRangeDescription = $this->sdk->t('shippingservice', 'txtAPRangeDesc');
        $txtAPSetting =  $this->sdk->t('shippingservice', 'txtAPSetting');
        if ($this->module->usa()) {
            $txtAPRangeDescription = $this->sdk->t('shippingservice', 'txtAPRangeDescUS');
            $txtAPSetting =  $this->sdk->t('shippingservice', 'txtAPSettingUS');
        }

        return array(
            'legend'      => array(
                'title' => $this->sdk->t('shippingservice', 'ttlSPService'),
                'icon'  => 'icon-AdminParentShipping',
            ),
            'description' => $this->sdk->t('shippingservice', 'txtDesc'),
            'input'       => array(
                array(
                    'type'   => 'switch',
                    'label'  => $this->sdk->t('shippingservice', $txtKeys['ttlAP']),
                    'desc'   => $this->sdk->t('shippingservice', $txtKeys['txtAPDesc']),
                    'name'   => 'UPS_SP_SERV_AP_DELIVERY',
                    'values' => array(
                        array(
                            'id_configuration' => 'UPS_SP_SERV_AP_DELIVERY',
                            'value'            => 1,
                        ),
                        array(
                            'id_configuration' => 'UPS_SP_SERV_AP_DELIVERY',
                            'value'            => 0,
                        ),
                    ),
                ),
                array(
                    'type'   => 'switch',
                    'label'  => $this->sdk->t('shippingservice', 'txtShipOption'),
                    'name'   => 'UPS_SP_SERV_SET_DEFAULT_SHIPPING_OPTION',
                    'values' => array(
                        array(
                            'id_configuration' => 'UPS_SP_SERV_SET_DEFAULT_SHIPPING_OPTION',
                            'value'            => 1,
                        ),
                        array(
                            'id_configuration' => 'UPS_SP_SERV_SET_DEFAULT_SHIPPING_OPTION',
                            'value'            => 0,
                        ),
                    ),
                ),
                array(
                    'type'   => 'checkbox',
                    'label'  => $this->sdk->t('shippingservice', 'txtSelect'),
                    'name'   => self::PRE_AP,
                    'lang'   => true,
                    'values' => array(
                        'query' => $this->listShipServiceAp,
                        'id'    => 'id_service',
                        'name'  => 'name',
                    ),
                ),
                array(
                    'type'  => 'free',
                    'label' => $txtAPSetting . ':',
                    'name'  => 'ap_setting',
                ),
                $array,
                array(
                    'type'    => 'select',
                    'class'   => 'chosen',
                    'label'   => $this->sdk->t('shippingservice', $txtKeys['txtAPNumber']) . ':',
                    'name'    => 'UPS_SP_SERV_AP_NUM_VISIBLE',
                    'class'   => 'fixed-width-sm',
                    'options' => array(
                        'query' => $this->createArrayApVisible(),
                        'id'    => 'id_visible',
                        'name'  => 'name',
                    ),
                ),
                array(
                    'type'    => 'select',
                    'class'   => 'chosen',
                    'label'   => $this->sdk->t('shippingservice', $txtKeys['txtAPRange']) . ':',
                    'desc'    => $txtAPRangeDescription,
                    'name'    => 'UPS_SP_SERV_AP_RANGE_DISPLAY',
                    'class'   => 'fixed-width-sm',
                    'options' => array(
                        'query' => $this->createArrApRange(),
                        'id'    => 'id_range',
                        'name'  => 'name',
                    ),
                ),
                array(
                    'type'    => 'select',
                    'class'   => 'chosen',
                    'label'   => $this->sdk->t('shippingservice', 'txtChoose'),
                    'name'    => 'UPS_SP_SERV_AP_CHOOSE_ACC',
                    'class'   => 'fixed-width-xxl',
                    'options' => array(
                        'query' => $this->createListAccountNumber(),
                        'id'    => 'id',
                        'name'  => 'name',
                    ),
                ),
                array(
                    'type'         => 'html',
                    'name'         => 'line',
                    'html_content' => '<hr style="border-style: inset; border-width: 1px;">',
                ),
                array(
                    'type'   => 'switch',
                    'label'  => $this->sdk->t('shippingservice', 'ttlAD'),
                    'desc'   => $this->sdk->t('shippingservice', 'txtADDesc'),
                    'name'   => 'UPS_SP_SERV_ADDRESS_DELIVERY',
                    'values' => array(
                        array(
                            'id_configuration' => 'UPS_SP_SERV_ADDRESS_DELIVERY',
                            'value'            => 1,
                        ),
                        array(
                            'id_configuration' => 'UPS_SP_SERV_ADDRESS_DELIVERY',
                            'value'            => 0,
                        ),
                    ),
                ),
                array(
                    'type'   => 'checkbox',
                    'label'  => $this->sdk->t('shippingservice', 'txtSelect'),
                    'name'   => self::PRE_ADD,
                    'lang'   => true,
                    'values' => array(
                        'query' => $this->listShipServiceAdd,
                        'id'    => 'id_service',
                        'name'  => 'name',
                    ),
                ),
                array(
                    'type'    => 'select',
                    'class'   => 'chosen',
                    'label'   => $this->sdk->t('shippingservice', 'txtChoose'),
                    'name'    => 'UPS_SP_SERV_ADDRESS_CHOOSE_ACC',
                    'class'   => 'fixed-width-xxl',
                    'options' => array(
                        'query' => $this->createListAccountNumber(),
                        'id'    => 'id',
                        'name'  => 'name',
                    ),
                ),
                array(
                    'type'         => 'html',
                    'name'         => 'line',
                    'html_content' => '<hr style="border-style: inset; border-width: 1px;">',
                ),
                array(
                    'type'    => 'select',
                    'class'   => 'chosen',
                    'label'   => $this->sdk->t('shippingservice', 'txtCot'),
                    'desc'    => $txtCotDesc1 .'<br/>'. $txtCotDesc2 .'<br/>'. $txtCotDesc3,
                    'name'    => 'UPS_SP_SERV_CUT_OFF_TIME',
                    'class'   => 'fixed-width-sm',
                    'options' => array(
                        'query' => $this->createArrCutOffTime(),
                        'id'    => 'id_time',
                        'name'  => 'name',
                    ),
                ),
            ),
            'submit'      => array(
                'title' => $this->sdk->t('button', 'txtSave'),
                'name'  => 'submitShippingServices',
                'id'    => 'submit_shipping_services_form',
            ),
        );
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitShippingServices')) {
            $result1 = $this->processAddressService();
            $result2 = $this->processAccessPointService();
            if (!$result1 && !$result2) {
                $this->errors[] = $this->sdk->t('err-msg', 'selectOne');
                return;
            }

            if (!empty($this->errors)) {
                return;
            }

            $this->transferData();
            $this->errors = null;

            CommonFunction::setDoneConfigScreen(Tools::getValue('controller'));
            return Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsCod'));
        }
    }

    private function processAddressService()
    {
        $toAdd = 'UPS_SP_SERV_ADDRESS_DELIVERY';
        $this->updateValueItem($toAdd);
        
        if (Tools::getValue($toAdd) == '1') {
            $adPrefix = self::PRE_ADD;
            $adServices = $this->listShipServiceAdd;

            if (!$this->processConfig($adServices, $adPrefix)) {
                return false;
            }
            
            $this->updateValueItem('UPS_SP_SERV_ADDRESS_CHOOSE_ACC');
            return true;
        } else {
            return false;
        }
    }

    private function processAccessPointService()
    {
        $toAp = 'UPS_SP_SERV_AP_DELIVERY';
        $this->updateValueItem($toAp);

        if (Tools::getValue($toAp) == '1') {
            $apPrefix = self::PRE_AP;
            $apServices = $this->listShipServiceAp;

            if (!$this->processConfig($apServices, $apPrefix)) {
                return false;
            }
            $this->updateValueItem('UPS_SP_ORDERS_REQUIRE_ADULT_SIGNATURES');
            $this->updateValueItem('UPS_SP_SERV_AP_NUM_VISIBLE');
            $this->updateValueItem('UPS_SP_SERV_AP_RANGE_DISPLAY');
            $this->updateValueItem('UPS_SP_SERV_AP_CHOOSE_ACC');
            $this->updateValueItem('UPS_SP_SERV_SET_DEFAULT_SHIPPING_OPTION');
            return true;
        } else {
            return false;
        }
    }

    private function processConfig($listService, $prefixName)
    {
        $validConfig = false;

        // Check exist one config on
        foreach ($listService as $service) {
            $nameService = $prefixName . '_' . $service['id_service'];
            if (Tools::getIsset($nameService) && Tools::getValue($nameService) == 'on') {
                $validConfig = true;
                break;
            }
        }

        if ($validConfig) {
            foreach ($listService as $service) {
                $nameService = $prefixName . '_' . $service['id_service'];
                if (Tools::getIsset($nameService)) {
                    Configuration::updateValue($service['id_service'], 1);
                } else {
                    Configuration::updateValue($service['id_service'], 0);
                }
            }
        }
        $this->updateValueItem('UPS_SP_SERV_CUT_OFF_TIME');
        return $validConfig;
    }

    private function updateValueItem($item)
    {
        if (Tools::getIsset($item)) {
            Configuration::updateValue($item, Tools::getValue($item));
        }
    }

    private function transferData()
    {
        $shippingServices = $this->getShippingServicesActived();
        $shippingServices = $this->removeElementWithValue($shippingServices, 'keyVal');
        $shippingServices = $this->removeElementWithValue($shippingServices, 'keyDeli');
        $shippingServices = $this->removeElementWithValue($shippingServices, 'Ratecode');
        $shippingServices = $this->removeElementWithValue($shippingServices, 'TinTcode');

        if (Configuration::get('UPS_MERCHANTINFO_EXIST')) {
            $this->transferShippingService(array(
                'merchantKey' => Configuration::get('MERCHANT_KEY'),
                'shippingServices' => $shippingServices
            ));
        }
    }
}
