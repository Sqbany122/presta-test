<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/lib/sdk/autoloader.php';
require_once dirname(__FILE__) . '/common/InitFunction.php';

class UpsModule extends Module
{
    const HOOKS_16 = array(
        'extraCarrier'
    );

    const HOOKS_17 = array(
        'displayCarrierExtraContent',
        'actionClearSf2Cache'
    );

    const HOOKS = array(
        'actionValidateOrder',
        'actionOutputHTMLBefore',
        'actionAdminControllerSetMedia',
        'actionAdminLoginControllerSetMedia',
        'displayHeader',
        'backOfficeHeader',
    );

    private static $increase = 0;
    private $sdk;
    public static $globalSdk = null;
    public static $shippingServices = array();
    public static $accessorials = array();
    public static $supportedLocale = array(
        'en-GB',
        'en-PL','pl-PL',
        'en-FR','fr-FR',
        'en-DE','de-DE',
        'en-ES','es-ES',
        'en-IT','it-IT',
        'en-NL', 'nl-NL',
        'en-BE','fr-BE','nl-BE',
    );
    public $pluginCountryCode = 'PL';
    public $pluginCountryList = array();
    public $lang = 'en';
    public $defaultLanguage = 'en';

    const COMPATIBLE_VERSIONS = array(
        '1.6.0.4',
        '1.6.1.0',
        '1.6.1.23',
        '1.7.2',
        '1.7.3.1',
        '1.7.3.2',
        '1.7.3.4',
        '1.7.5.0',
        '1.7.6.0',
        '1.7.6.4',
        '1.7.6.5'
    );
    const RELEASE_DATE = array(
        'd' => '08',
        'm' => '05',
        'y' => '2020'
    );

    public function __construct()
    {
        $this->name                   = 'upsmodule';
        $this->module_key             = 'f9f09723bdfc1c8215fa9da1b20a2e03';
        $this->tab                    = 'front_office_features';
        $this->version                = '2.0.6';
        $this->author                 = 'UPS';
        $this->need_instance          = 0;
        if (version_compare(_PS_VERSION_, '1.7.7') == -1) {
            $maxVersion = _PS_VERSION_;
        } else {
            $maxVersion = '1.7.6';
        }
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => $maxVersion);
        $this->bootstrap              = true;
        $this->limited_countries = array(
            'US',
            'GB',
            'PL',
            'FR',
            'DE',
            'ES',
            'IT',
            'NL',
            'BE',
        );
        $this->displayName      = $this->l('UPS Shipping and UPS Access Point™: Official Module');
        $this->description      = $this->l('This module allows you to easily integrate UPS services into your store with a range of guaranteed delivery services (including deliveries to UPS Access Points™) to meet your customers\' need for speed and budget.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall!?');
        parent::__construct();

        $this->buildSdk();
        $this->initConfig();
        $this->initLanguage();
        $this->initCountry();
        $this->initShippingServices();
        $this->initAccessorials();
        $this->upgradeUPSMenu();
        $this->upgradePluginVersion();
        $this->updateDeliveryRateWithUpgradeVersion();
    }

    private function buildSdk()
    {
        if (is_null(static::$globalSdk)) {
            static::$globalSdk = new Ups\Sdk(array(
                'currencyCode' => Currency::getCurrencyInstance(Configuration::get('PS_CURRENCY_DEFAULT'))->iso_code,
                'dbInstance' => Db::getInstance()
            ));
            static::$globalSdk->setLanguage($this->context->language->iso_code);
            static::$globalSdk->setVersion($this->version);
            static::$globalSdk->setCompatibleVersions(static::COMPATIBLE_VERSIONS);
            static::$globalSdk->setReleaseDate(static::RELEASE_DATE);
            static::$globalSdk->dbQuery = new DbQuery();
        }

        $this->sdk = static::$globalSdk;
    }

    private function initConfig()
    {
        if ((Configuration::get('UPS_CONFIG_DONE') == 1)) {
            if (Configuration::get('MY_UPS_ID') == null) {
                $this->sdk->setUsername($this->updateKey('Username'));
                $this->sdk->setPassword($this->updateKey('Password'));
                $this->sdk->setLicense($this->updateKey('LicenseKey'));
            } else {
                $this->sdk->setUsername(Configuration::get('MY_UPS_ID'));
                $this->sdk->setPassword(Configuration::get('UPS_PASS'));
                $this->sdk->setLicense(Configuration::get('UPS_LICENSE'));
            }

            if (Configuration::get('REGISTERED_TOKEN') == null) {
                $this->registerToken();
            }

            if (Configuration::get('BINGMAPS_KEY') == null) {
                $this->getBingKey();
            }
        }

        if (Configuration::get('MERCHANT_KEY') == null) {
            $this->initKeys();
            $this->doHandShake(); // We have 'PRE_KEY'
        }
    }

    private function initLanguage()
    {
        $this->lang = Language::getIsoById($this->context->cookie->id_lang);
        if (!empty($this->context->language->locale)) {
            if (!array_search($this->context->language->locale, static::$supportedLocale)) {
                $this->lang = 'en';
            } else {
                $this->lang = $this->context->language->iso_code;
            }
        }
    }

    private function initCountry()
    {
        if (Configuration::get('UPS_COUNTRY_SELECTED')) {
            $iso = Configuration::get('UPS_COUNTRY_SELECTED');
            $country = $this->getCountriesObject();
            $this->pluginCountryCode = $iso;
            $this->pluginCountryName = $country->getCountryNameByIso($iso);
            $this->pluginCountryList = $country->getCountriesPair();
        }
    }

    private function initShippingServices()
    {
        static::$shippingServices = new Bean\ShippingService(
            include(PATH_ASSETS_FOLDER . 'ShippingServices/AccessPoint/' . $this->pluginCountryCode . '.php'),
            include(PATH_ASSETS_FOLDER . 'ShippingServices/ToAddress/' . $this->pluginCountryCode . '.php')
        );
    }

    private function initAccessorials()
    {
        static::$accessorials = new Bean\Accessorial(
            $this->lang,
            include(PATH_ASSETS_FOLDER . 'Accessorials/accessorials.php')
        );
    }

    private function getCountriesObject()
    {
        return new Bean\UPSCountry(
            $this->lang,
            include(PATH_ASSETS_FOLDER . 'UPSCountries.php')
        );
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        include(dirname(__FILE__).'/sql/install.php');

        $this->createParentTab();
        $this->createSubTabs();
        $this->initListConfig();

        InitFunction::enableTab('AdminUpsAbout');
        InitFunction::enableTab('AdminUpsCountry');
        InitFunction::enableTab('AdminUpsTermCondition');

        if ($this->registerListHook()) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $langId = (int) Configuration::get('PS_LANG_DEFAULT');
                $obj = new InitFunction();
                $obj->addTabNone($this->name, $langId);
            }

            return true;
        }
        return false;
    }


    public function uninstall()
    {
        $this->doHandShake();
        $this->registerToken();

        $transfer = new PluginManager\CollectionApi\MerchantStatus();
        $transfer($this->addParam(
            array(
                'merchantKey' => Configuration::get('MERCHANT_KEY'),
                'status' => 30
            )
        ));

        $this->uninstallAllTab();
        $obj = new InitFunction();
        $obj->removeCarriers();

        if (!parent::uninstall()) {
            return false;
        }

        $this->unregisterListHook();
        $this->removeConfiguration();

        include(dirname(__FILE__).'/sql/uninstall.php');

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            include(dirname(__FILE__).'/sql/uninstall.16.php');
        }
        Configuration::deleteByName('UPS_UPGRRADE_MENU');
        Configuration::deleteByName('UPS_SAVE_ADMIN_DELIVERY_RATES');
        Configuration::deleteByName('UPS_SP_ORDERS_REQUIRE_ADULT_SIGNATURES');
        return true;
    }

    public function disable($force_all = false)
    {
        $this->changeStatusCarrier(false);

        if (!parent::disable($force_all)) {
            return false;
        }

        return true;
    }

    public function enable($force_all = false)
    {
        $this->changeStatusCarrier(true);

        if (!parent::enable($force_all)) {
            return false;
        }

        return true;
    }

    private function initListConfig()
    {
        foreach (Constants::LIST_CONFIGURATION as $key => $val) {
            if (is_array($val)) {
                Configuration::updateValue($key, serialize($val));
            } else {
                Configuration::updateValue($key, $val);
            }
        }

        Configuration::updateValue('UPS_CONFIG_SCREEN_STATUS', serialize(Constants::CONFIG_SCREEN_STATUS));
    }

    private function createParentTab()
    {
        $tab = new Tab();
        $moduleNamesTranslated = include(PATH_ASSETS_FOLDER . 'ModuleName.php');
        $langIsoIds = Language::getIsoIds();

        foreach ($langIsoIds as $lang) {
            if (array_key_exists($lang['iso_code'], $moduleNamesTranslated)) {
                $tab->name[$lang['id_lang']] = $moduleNamesTranslated[$lang['iso_code']];
            }
        }

        $tab->class_name = 'AdminUpsShipping';
        $tab->id_parent = 0;
        $tab->module = $this->name;
        $tab->add();

        unset($tab);
    }

    private function createSubTabs()
    {
        $langIsoIds = Language::getIsoIds();
        $subTabs = $this->getUpsTabs();

        foreach ($subTabs as $subTab) {
            $parentId = Tab::getIdFromClassName($subTab['parent_name']);
            $tab = new Tab();

            foreach ($langIsoIds as $lang) {
                if (array_key_exists($lang['iso_code'], $subTab)) {
                    $tab->name[$lang['id_lang']] = $subTab[$lang['iso_code']];
                }
            }

            $tab->class_name = $subTab['class_name'];
            $tab->id_parent  = $parentId;
            $tab->active     = 0; // Default tabs be disabled
            $tab->module     = $this->name;
            $tab->position   = $subTab['position'];
            $tab->add();

            unset($tab);
        }
    }

    public function getUpsTabs()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            return include(PATH_ASSETS_FOLDER . 'Tabs/tabs.17.php');
        } else {
            if (Tools::version_compare(_PS_VERSION_, '1.6.1.23', '<=')) {
                return include(PATH_ASSETS_FOLDER . 'Tabs/tabs.16123.php');
            } else {
                return include(PATH_ASSETS_FOLDER . 'Tabs/tabs.16.php');
            }
        }
    }

    private function uninstallAllTab()
    {
        $tabs = $this->getUpsTabs();
        foreach ($tabs as $tab) {
            Tab::getInstanceFromClassName($tab['class_name'])->delete();
        }

        Tab::getInstanceFromClassName('AdminUpsShipping')->delete();
    }

    public function hookDisplayHeader($params)
    {
        $context = Context::getContext();
        $shippingFee = $context->cookie->shippingFee;
        //$action = Tools::getValue('action');
        //$idAddressDelivery = $context->cart->id_address_delivery;
        $controller = Tools::getValue('controller');
        if ($controller != 'order') {
            if (empty($shippingFee)) {
                $addClass = 'none';
            } else {
                $addClass = 'block';
            }
        } else {
            $addClass = 'block';
        }
        $class = "<style>
                    #cart-subtotal-shipping{ display: $addClass; }
                    .cart-content p:nth-child(3n){ display: $addClass; }
                    .summary-selected-carrier .carrier-delay{ display: none; }
                </style>";
        if (Configuration::get('UPS_SEC_CLICKJACKING') && Configuration::get('UPS_SEC_FRAME_KILLER')) {
            return '<style id="antiClickjack">
                        body{
                            display:none !important;}
                        }
                    </style>
                    ' .  $class . '
                    <script type="text/javascript">
                        if (self === top) {
                            var antiClickjack = document.getElementById("antiClickjack");
                            antiClickjack.parentNode.removeChild(antiClickjack);
                            } else {
                            top.location = self.location;
                        }
                    </script>';
        }

        return null;
    }

    public function hookActionClearSf2Cache($params)
    {
        $this->updateLangMenu($params, 1);
        $this->updateCarrierLang();
        Configuration::updateValue('UPS_UPGRRADE_MENU', 1);
    }

    public function updateCarrierLang()
    {
        $languages = Language::getLanguages(true);
        $sql    = new DbQuery();
        $sql->select('id_carrier');
        $sql->from('carrier');
        $sql->where("name = 'UPS SHIPPING'");
        $sql->where("deleted = '0'");
        $sql->where("id_reference != '0'");
        $carrier = Db::getInstance()->executeS($sql);

        if (!empty($carrier[0]['id_carrier'])) {
            $id_carrier = $carrier[0]['id_carrier'];
            foreach ($languages as $item) {
                $langId = $item['id_lang'];
                Db::getInstance()->update(
                    'carrier_lang',
                    array('delay' => '  '),
                    '`id_shop` = ' . (int) ($this->context->shop->id) . ' AND `id_lang` = ' . (int) $langId . ' AND `id_carrier` = ' . (int) $id_carrier
                );
            }
        }

    }

    public function updateLangMenu($params, $isall)
    {
        /** handling update table languages tab module */
        $sql    = new DbQuery();
        $sql->select('iso_code, id_lang');
        $sql->from('lang');
        if ($isall == 1) {
            $lang = !empty($params['lang']) ? $params['lang'] : '';
            $langId = $lang->id;
            $sql->where("id_lang = '" . $langId . "'");
        }

        $content = Db::getInstance()->executeS($sql);

        //Parent Tab
        $parentTabs = include(PATH_ASSETS_FOLDER . 'ModuleName.php');
        $langIsoIds = Language::getIsoIds();
        //Tab ID module
        $idTabModule = Tab::getIdFromClassName('AdminUpsShipping');

        foreach ($content as $item) {
            //Child Tab
            $subTabs = $this->getUpsTabs();
            foreach ($subTabs as $subTab) {
                $idTab = Tab::getIdFromClassName($subTab['class_name']);
                foreach ($langIsoIds as $lang) {
                    if (array_key_exists($lang['iso_code'], $subTab) && $lang['iso_code'] == $item['iso_code']) {
                        Db::getInstance()->update(
                            'tab_lang',
                            array('name' => $subTab[$lang['iso_code']]),
                            '`id_tab` = ' . (int) $idTab . ' AND `id_lang` = ' . (int) $item['id_lang']
                        );
                    }
                }
            }
            //Parent Tab
            foreach ($parentTabs as $langCode => $prentName) {
                $langId = Language::getIdByIso($langCode);
                Db::getInstance()->update(
                    'tab_lang',
                    array('name' => $prentName),
                    '`id_tab` = ' . (int) $idTabModule . ' AND `id_lang` = ' . (int) $langId
                );
            }
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/icon.css');
    }

    public function hookActionOutputHTMLBefore($params)
    {
        $this->addHeaderBaseSecurityOption();
    }

    // Implement security Admin Login page
    public function hookActionAdminLoginControllerSetMedia()
    {
        $this->addHeaderBaseSecurityOption();
    }

    // Implement security Back Office page
    public function hookActionAdminControllerSetMedia()
    {
        $this->addHeaderBaseSecurityOption();
    }

    public function getStateNameByCode($stateCode)
    {
        $idState = State::getIdByIso($stateCode);
        return State::getNameById($idState);
    }

    public function hookActionValidateOrder($param)
    {
        $context = Context::getContext();
        $cook = $context->cookie;
        $idCarrierCurrent = $this->getIdCarriesByIdReference();
        $shippingService = '';
        $selectedAddress = '';
        if ($param['order']->id_carrier == $idCarrierCurrent) {
            $shippingService = ($cook->__isset('selectedShippingService')) ?
                $cook->selectedShippingService : '';
            $selectedAddress = ($cook->__isset('selectedAcessPointAddress')) ?
                $cook->selectedAcessPointAddress : '';
            if (empty($shippingService)) {
                $shippingService = Configuration::get('UPS_SELECTED_SHIPPING_SERVICE');
            }
            if (empty($selectedAddress)) {
                $selectedAddress = Configuration::get('UPS_SELECTED_ACESSPOINT_ADDRESS');
            }
            if ($this->checkServiceType($shippingService) == 'AP' && !empty($selectedAddress)) {
                $arrSelectedAddress = json_decode($selectedAddress);
                $add = $arrSelectedAddress->AddressInfo;

                $ap_id = isset($arrSelectedAddress->AccessPointId) ?
                    $arrSelectedAddress->AccessPointId : '';

                $ap_name = isset($add->ConsigneeName) ? $add->ConsigneeName : '';
                $ap_address1 = isset($add->AddressLine) ? ($add->AddressLine) : '';
                $ap_address2 = '';
                $ap_city = isset($add->PoliticalDivision2) ? $add->PoliticalDivision2 : '';
                $ap_postcode = isset($add->PostcodePrimaryLow) ? $add->PostcodePrimaryLow : '';
                $ap_state    = isset($add->PoliticalDivision1) ? $add->PoliticalDivision1 : '';
            } else {
                $ap_id       = '';
                $ap_name     = '';
                $ap_address1 = '';
                $ap_address2 = '';
                $ap_city     = '';
                $ap_postcode = '';
                $ap_state    = '';
            }

            // get list accessorial active
            $arrAccessorial = $this->listAccessorialActive();

            if (!empty($shippingService)) {
                if (strpos($shippingService, "SATDELI") !== false && is_array($arrAccessorial)) {
                    if (!in_array("UPS_ACSRL_STATURDAY_DELIVERY", $arrAccessorial)) {
                        $arrAccessorial[] = "UPS_ACSRL_STATURDAY_DELIVERY";
                    }
                }

                Db::getInstance()->insert(
                    Constants::DB_TABLE_OPENORDER,
                    array(
                        array(
                            'id_order'             => (int) $param['order']->id,
                            'ap_id'                => pSQL($ap_id),
                            'ap_name'              => pSQL($ap_name),
                            'ap_address1'          => pSQL($ap_address1),
                            'ap_address2'          => pSQL($ap_address2),
                            'ap_city'              => pSQL($ap_city),
                            'ap_postcode'          => pSQL($ap_postcode),
                            'ap_state'             => pSQL($ap_state),
                            'shipping_service'     => pSQL($shippingService),
                            'accessorials_service' => pSQL(serialize($arrAccessorial)),
                        ),
                    )
                );
            }
        }

        $cook->__unset('idAddressDelivery');
        $cook->__unset('selectedAcessPointAddress');
        Configuration::updateValue('UPS_SELECTED_ACESSPOINT_ADDRESS', '');
        $cook->__unset('selectedShippingService');
        Configuration::updateValue('UPS_SELECTED_SHIPPING_SERVICE', '');
        $cook->__unset('shippingFee');
        Configuration::updateValue('UPS_SHIPPING_FEE', 0);
    }

    public function hookDisplayCarrierExtraContent()
    {
        $this->createContent();
        return $this->display(__FILE__, 'extra_content_shipping.tpl');
    }

    public function hookextraCarrier($params)
    {
        $active = Db::getInstance()->getValue(
            'SELECT active FROM `'._DB_PREFIX_.'carrier`
            WHERE external_module_name = "upsmodule"
            AND deleted = 0
            ORDER BY id_carrier DESC'
        );

        if ($active) {
            $this->createContent();
            return $this->display(__FILE__, 'extra_content_shipping-16.tpl');
        }
    }

    /**
     * The override platform's function
     */
    public function getOrderShippingCost()
    {
        $context = Context::getContext();

        //Have to check again 23/08/2019

        // if ($context->cookie->__isset('shippingFee')) {
        //     return Tools::convertPrice(
        //         $context->cookie->shippingFee,
        //         Currency::getCurrencyInstance((int)$context->currency->id)
        //     );
        // }
        $upsShippingFee = Configuration::get('UPS_SHIPPING_FEE');
        if ($context->cookie->__isset('shippingFee')) {
            return $context->cookie->shippingFee;
        } else if ($upsShippingFee > 0) {
            return $upsShippingFee;
        } else {
            return 0;
        }
    }

    public function getLocale()
    {
        return Configuration::get('PS_LOCALE_LANGUAGE') . '_' . Configuration::get('UPS_COUNTRY_SELECTED');
    }

    public function doHandShake()
    {
        $https_link = Tools::getHttpHost(true) . __PS_BASE_URI__ . Constants::LINK_DOHANDSHAKE;
        if (strpos($https_link, 'https://') === false) {
            $https_link = str_replace("http://", "https://", $https_link);
        }
        $data = array(
            'sdk' => $this->sdk,
            'data' => array(
                "WebstoreMetadata" => array_merge(
                    $this->getShopInfo(),
                    array("UpsReadyPluginName" => "UPS Access Point and Shipping Official Module")
                ),
                "WebstoreUpsServiceLinkUrl" => $https_link
            )
        );

        $handshake = new PluginManager\ToolApi\Handshake();
        return $handshake($data);
    }

    public function getPreTokenKey()
    {
        $sql = new DbQuery();

        $sql->select('name');
        $sql->select('value');
        $sql->from('configuration');
        $sql->where("name = 'PRE_KEY'");
        $sql->orderBy('id_configuration DESC');

        $content   = Db::getInstance()->executeS($sql);

        $token = Configuration::get('PRE_KEY');
        if (empty($token)) {
            foreach ($content as $item) {
                if (!empty($item['value'])) {
                    $token = $item['value'];
                    break;
                }
            }
        }

        return $token;
    }

    private function convertRateShipping($rateApi, $currencyApi, $idCurrencyDisplay)
    {
        $idCurrencyApi               = Currency::getIdByIsoCode($currencyApi);
        $exchangeRateCurrencyApi     = $this->getExchangeRateById($idCurrencyApi);
        $exchangeRateCurrencyDisplay = $this->getExchangeRateById($idCurrencyDisplay);

        return $rateApi / $exchangeRateCurrencyApi * $exchangeRateCurrencyDisplay;
    }

    public function updateKey($key)
    {
        $sql = new DbQuery();

        $sql->select('ups_value');
        $sql->from('ups_data');
        $sql->where("key_name = '$key'");
        $result = Db::getInstance()->getValue($sql);

        switch ($key) {
            case 'LicenseKey':
                $key = 'UPS_LICENSE';
                break;

            case 'Username':
                $key = 'MY_UPS_ID';
                break;

            case 'Password':
                $key = 'UPS_PASS';
                break;
        }

        if ($result) {
            Configuration::updateValue($key, $result);
        }

        return $result;
    }

    public function getUPSAccountMerchant()
    {
        $datafetch = array();

        $license = Configuration::get('UPS_LICENSE');
        $username = Configuration::get('MY_UPS_ID');
        $password = Configuration::get('UPS_PASS');

        $datafetch['LicenseKey'] = $license != null ? $license : $this->updateKey('LicenseKey');
        $datafetch['Username'] = $username != null ? $username : $this->updateKey('Username');
        $datafetch['Password'] = $password != null ? $password : $this->updateKey('Password');

        return $datafetch;
    }

    public function transferMerchantStatus($params)
    {
        $update = new PluginManager\CollectionApi\MerchantStatus();
        $response = $update($this->addParam($params));

        $this->refreshToken($response, $this, 'transferMerchantStatus', $params);
    }

    public function addParam($params)
    {
        return array(
            'sdk' => $this->sdk,
            'preToken' => Configuration::get('REGISTERED_TOKEN'),
            'data' => $params
        );
    }

    /**
     * Maximum callback function in 5 time.
     */
    public function refreshToken($res, $obj, $func, $data)
    {
        if (!$res) {
            static $i = 0;
            $this->doHandShake();
            $this->registerToken();

            // Callback in 5 time
            if ($i == 2) {
                $i = 0;
                return false;
            } else {
                $i++;
                $obj->$func($data);
            }
        }
    }

    public function getUpsData($operator, $value, $where = array())
    {
        $sql = new DbQuery();

        $sql->select('key_name');
        $sql->select('ups_value');
        $sql->from('ups_data');

        switch ($operator) {
            case 'like':
                $sql->where("key_name LIKE '" . pSQL($value) . "%'");
                break;

            case 'exactly':
                $sql->where("key_name = '" . pSQL($value) . "'");
                break;

            case 'in':
                $value = implode(',', array_map('intval', $value));
                $sql->where("key_name IN (" . $value . ")");
                break;

            default:
                $sql->where("key_name = ''");
        }

        if (!empty($where)) {
            foreach ($where as $column) {
                if (!isset($column['operator'])) {
                    $column['operator'] = " = ";
                }

                $sql->where(pSQL($column['name']) . " " . pSQL($column['operator']) . " " . pSQL($column['value']));
            }
        }
        $sql->orderBy('id;');

        $content = Db::getInstance()->executeS($sql);

        $datafetch = array();

        foreach ($content as $item) {
            $datafetch[$item[KEY_COL]] = $item[VAL_COL];
        }

        return $datafetch;
    }

    public function calculatePickupDate($cutOffTime)
    {
        $stringTinTDate = date("Ymd");

        if ($cutOffTime != 24) {
            $currentHour = (int) date('H');
            if ($currentHour >= $cutOffTime) {
                $today = date("d-m-Y");
                $stringTinTDate = date('Ymd', strtotime($today . "1 day"));
            }
        }

        return $stringTinTDate;
    }

    public function callLocatorAPI($locatorInfo)
    {
        $locatorInfo['Username'] = Configuration::get('MY_UPS_ID');
        $locatorInfo['Password'] = Configuration::get('UPS_PASS');
        $locatorInfo['LicenseKey'] = Configuration::get('UPS_LICENSE');
        $locatorAPI = new Ups\Api\Locator();
        $response   = $locatorAPI($locatorInfo);
        return $response;
    }

    // For the Old VERSION - missing isDefaultAccount on the account default
    public function checkDefaultAccount($isDefault)
    {
        return $isDefault === 'Yes' ? true : false;
    }

    public function getDefaultAccount($accounts)
    {
        if (!is_array($accounts)) {
            return false;
        }

        foreach ($accounts as $account) {
            // For the Old VERSION - missing isDefaultAccount on the account default
            if (!isset($account['isDefaultAccount'])) {
                return $account;
            } elseif ($this->checkDefaultAccount($account['isDefaultAccount'])) {
                return $account;
            }
        }

        return false;
    }

    public function checkDecode($str)
    {
        return isset($str) ? base64_decode($str) : '';
    }

    public function getListAccount()
    {
        $listAccount = $this->getUpsData(
            'like',
            ACCOUNT_NUM_KEY,
            array(
                array(
                    'name' => STATUS_COL,
                    'value' => '1'
                )
            )
        );

        $displayInfo = array();
        foreach ($listAccount as $info) {
            $info                 = (array) json_decode($info);
            $info['Country']      = $this->pluginCountryName;
            $info['AddressType']  = $this->checkDecode($info['AddressType']);
            $info['AddressLine1'] = $this->checkDecode($info['AddressLine1']);
            $info['AddressLine2'] = $this->checkDecode($info['AddressLine2']);
            $info['AddressLine3'] = $this->checkDecode($info['AddressLine3']);
            $info['City']         = $this->checkDecode($info['City']);
            $displayInfo[]        = $info;
        }

        return $displayInfo;
    }

    public function getListNamesAccessorial($arrKeyAccessorial)
    {
        $strAccessorial = '';

        foreach ($arrKeyAccessorial as $key) {
            $strAccessorial .= $this->getNameAccessorialByKey($key);
            $strAccessorial .= '<br/>';
        }

        return $strAccessorial;
    }

    public function getNameAccessorialByKey($key)
    {
        return static::$accessorials->getAccessorialNameByKey($key);
    }

    /**
     * Use for two types of key list
     * 1. Serialize
     * 2. Array
     *
     * Updated at 26-08-2018
     * Updated by UPS
     */
    public function getAccessorialCodes($accessorialServiceKeys)
    {
        $codes = array();

        if (!isset($accessorialServiceKeys)) {
            return $codes;
        }

        if (is_array($accessorialServiceKeys)) {
            $keys = $accessorialServiceKeys;
        } else {
            $keys = unserialize($accessorialServiceKeys);
        }

        if (empty($keys)) {
            return $codes;
        }

        $services = static::$accessorials->getServices();

        foreach ($keys as $key) {
            $foundKey = array_search($key, array_column($services, 'key'));
            $codes[]  = $services[$foundKey]['code'];
        }

        return $codes;
    }

    public function checkServiceType($serviceKey)
    {
        if (strpos($serviceKey, '_AP_') !== false) {
            return "AP";
        } else {
            return "ADD";
        }
    }

    private function listAccessorialActive()
    {
        $arrayReturn = array();
        $services = static::$accessorials->getServices();

        foreach ($services as $service) {
            if ($service['show_config']) {
                if (Configuration::get($service['key'])) {
                    $arrayReturn[] = $service['key'];
                }
            }
        }

        return $arrayReturn;
    }

    private function createContent()
    {
        if (static::$increase == 0) {
            if (is_null($this->sdk)) {
                $this->buildSdk();
            }
            $this->initTexts();
            $this->createContextSmarty();
            static::$increase++;
        }
    }

    private function getAddressString($addressCustomer)
    {
        $addressString = $addressCustomer->address1;

        if (isset($addressCustomer->address2) && !empty($addressCustomer->address2)) {
            $addressString .= ' ' . $addressCustomer->address2;
        }
        if (isset($addressCustomer->city) && !empty($addressCustomer->city)) {
            $addressString .= ', ' . $addressCustomer->city;
        }
        if (isset($addressCustomer->id_state) && !empty($addressCustomer->id_state)) {
            $addressString .= ', ' . State::getNameById($addressCustomer->id_state);
        }
        if (isset($addressCustomer->postcode) && !empty($addressCustomer->postcode)) {
            $addressString .= ', ' . $addressCustomer->postcode;
        }

        return $addressString;
    }

    private function visibleAccessPoint($countryCode, $addressString, $sdk)
    {
        if (Configuration::get('UPS_SP_SERV_AP_DELIVERY') == 1) {
            $unitOfMeasurement = "KM";
            if ($this->usa()) {
                $unitOfMeasurement = "MI";
            }
            $locatorInfo = array(
                'fullAddress'       => $addressString,
                'countryCode'       => $countryCode,
                'locale'            => $this->getLocale(),
                'nearby'            => "100", // Check visible
                'maximumListSize'   => "1", // Check visible
                'sdk'               => $sdk,
                'unitOfMeasurement' => $unitOfMeasurement,
            );

            $respone = $this->callLocatorAPI($locatorInfo);

            if ($respone['Code'] == 1 && !empty($respone['Data']->AddressKeyFormat)) {
                $add = $respone['Data']->AddressKeyFormat;
                $point = array();
                $point['consigneeName'] = isset($add->ConsigneeName) ? $add->ConsigneeName : '';
                $point['addressLine'] = isset($add->AddressLine) ? $add->AddressLine : '';
                $point['city'] = isset($add->PoliticalDivision2) ? $add->PoliticalDivision2 : '';
                $point['state'] = isset($add->PoliticalDivision1) ? $add->PoliticalDivision1 : '';
                $point['postalCode'] = isset($add->PostcodePrimaryLow) ? $add->PostcodePrimaryLow : '';
                $point['countryCode'] = isset($add->CountryCode) ? $add->CountryCode : '';

                return $point;
            }
        }

        return false;
    }

    private function createContextSmarty()
    {
        $context = $this->context;
        $cookie = $context->cookie;
        $orderTotalPrice   = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $idAddressDelivery = $context->cart->id_address_delivery;

        $this->processCheckExistCookie($idAddressDelivery);
        $addressCustomer = new Address($idAddressDelivery);
        $countryCode     = Country::getIsoById((int) $addressCustomer->id_country);
        $addressString   = $this->getAddressString($addressCustomer);
        $countryName     = $addressCustomer->country;
        $listAP = $this->visibleAccessPoint($countryCode, $addressString, $this->sdk);
        $apServices = $this->getAPServices($idAddressDelivery, $orderTotalPrice, $listAP);
        $adServices = $this->getAddressServices($idAddressDelivery, $orderTotalPrice);
        $arrayShippingMerge = array_merge($apServices, $adServices);

        if (!empty($arrayShippingMerge)) {
            $serviceSelected = $this->processGetShippingService(
                $apServices,
                $adServices,
                $cookie->selectedShippingService
            );
            $cheapestPrice = $serviceSelected['priceDisplay'];
            $cheapestTime = $serviceSelected['totalTransitDays'];
            $shippingServiceType = $this->checkServiceType($serviceSelected['id_service']);

            $cookie->__set('selectedShippingService', $serviceSelected['id_service']);
            Configuration::updateValue('UPS_SELECTED_SHIPPING_SERVICE', $serviceSelected['id_service']);
            $cookie->__set('shippingFee', $serviceSelected['shippingFeeValue']);
            Configuration::updateValue('UPS_SHIPPING_FEE', $serviceSelected['shippingFeeValue']);
        } else {
            $cookie->__set('selectedShippingService', '');
            $cookie->__set('shippingFee', 0);
        }

        $this->context->smarty->assign(
            array(
                'cookieShippingFee'         => $cookie->__isset('shippingFee') ? $cookie->shippingFee : 0,
                'list_ship_service_ap'      => $apServices,
                'list_ship_service_add'     => $adServices,
                'id_carrier'                => $this->getIdCarriesByIdReference(),
                'shippingServiceType'       => (isset($shippingServiceType) && $shippingServiceType == 'AP') ? 1 : 0,
                'countryName'               => $countryName,
                'customerIso'               => $countryCode,
                'myAddress'                 => $addressString,
                'bingMapKey'                => Configuration::get('BINGMAPS_KEY'),
                'view_dir'                  => _MODULE_DIR_ . $this->name . '/views',
                'cheapestPrice'             => isset($cheapestPrice) ? $cheapestPrice : 'FREE',
                'cheapestTime'              => isset($cheapestTime) ? $cheapestTime : '',
                'hiddenUPSShipping'         => empty($arrayShippingMerge),
                'showHiddenAccesPointBlock' => !empty($apServices),
                'chooseShippingService'     => $cookie->__isset('selectedShippingService') ?
                    $cookie->selectedShippingService : '',
                'arrtext'                   => $this->texts,
                'carrierName'               => 'UPS SHIPPING',
                'idAddress'                 => $addressCustomer->id,
                'pluginCountry'             => Configuration::get('UPS_COUNTRY_SELECTED'),
                'isUSA'                     => $this->usa(),
            )
        );
    }

    private function getAPServices($idAddressDelivery, $orderTotalPrice, $listAP)
    {
        $list = array();
        if ($listAP !== false) {
            $list = $this->showShippingServices(
                'AP',
                $idAddressDelivery,
                $orderTotalPrice,
                $listAP
            );
        }

        return $list;
    }

    private function getAddressServices($idAddressDelivery, $orderTotalPrice)
    {
        $list = array();

        if (Configuration::get('UPS_SP_SERV_ADDRESS_DELIVERY') == 1) {
            $list = $this->showShippingServices('ADD', $idAddressDelivery, $orderTotalPrice);
        }

        return $list;
    }

    private function getBingKey()
    {
        $bingmapService = new PluginManager\ToolApi\BingMapsService();
        $result = $bingmapService(array(
            'preToken' => Configuration::get('REGISTERED_TOKEN'),
            'sdk' => $this->sdk
        ));

        Configuration::updateValue('BINGMAPS_KEY', $result->data);
    }

    private function registerToken()
    {
        $data = array(
            'sdk' => $this->sdk,
            'data' => array_merge(
                $this->getShopInfo(),
                array("UPSAccountMerchant" => $this->getUPSAccountMerchant())
            )
        );

        $registeredToken = new PluginManager\ToolApi\RegisteredPluginToken();
        $result = $registeredToken($data);

        if (is_null($result->error)) {
            Configuration::updateValue('REGISTERED_TOKEN', $result->data);
            return true;
        }

        return false;
    }

    private function initKeys()
    {
        Configuration::updateValue('MERCHANT_KEY', $this->generateGUID());
        Configuration::updateValue('SECURITY_TOKEN', $this->generateGUID());
    }

    private function getShopInfo()
    {
        $https_link = Tools::getHttpHost(true) . __PS_BASE_URI__;
        if (strpos($https_link, 'https://') === false) {
            $https_link = str_replace("http://", "https://", $https_link);
        }
        return array(
            "MerchantKey" => Configuration::get('MERCHANT_KEY'),
            "WebstoreUpsServiceLinkSecurityToken" => Configuration::get('SECURITY_TOKEN'),
            "UpsReadyPluginVersion" => $this->sdk->getVersion(),
            "WebstoreUrl" => $https_link,
            "WebstorePlatformVersion" => _PS_VERSION_,
            "WebstorePlatform" => "PrestaShop"
        );
    }

    private function processCheckExistCookie($idAddressDelivery)
    {
        $context = $this->context;

        if (!$context->cookie->__isset('selectedShippingService')) {
            $context->cookie->__set('selectedShippingService', '');
        }

        // Check id Address Delivery
        if (!$context->cookie->__isset('idAddressDelivery')) {
            $context->cookie->__set('idAddressDelivery', $idAddressDelivery);
        }

        if ($idAddressDelivery != $context->cookie->idAddressDelivery) {
            $context->cookie->__set('selectedShippingService', '');
            $context->cookie->__set('idAddressDelivery', $idAddressDelivery);
        }
    }

    private function getCheapestInArray($arrService)
    {
        if (isset($arrService[0])) {
            $serviceReturn = $arrService[0];
            $cheapestPrice = $arrService[0]['shippingFeeValue'];
            foreach ($arrService as $service) {
                if ($service['shippingFeeValue'] < $cheapestPrice) {
                    $cheapestPrice = $service['shippingFeeValue'];
                    $serviceReturn = $service;
                }
            }
            return $serviceReturn;
        } else {
            return null;
        }
    }
    private function getServiceSelected($arrayShippingMerge, $showShippingServiceAP)
    {
        $accessPointDefault = Configuration::get('UPS_SP_SERV_SET_DEFAULT_SHIPPING_OPTION');

        if (!empty($showShippingServiceAP)
            && $accessPointDefault) {
            $serviceChosen = $this->getCheapestInArray($showShippingServiceAP);
        } else {
            $serviceChosen = $this->getCheapestInArray($arrayShippingMerge);
        }

        return $serviceChosen;
    }

    private function checkServiceExist($sessionShippingService, $arrayShippingMerge)
    {
        foreach ($arrayShippingMerge as $shippingService) {
            if ($sessionShippingService == $shippingService['id_service']) {
                return $shippingService;
            }
        }

        return false;
    }

    private function processGetShippingService($showShippingServiceAP, $showShippingServiceAdd, $cookieShippingService)
    {
        $serviceSelected    = array();
        $countryCode = Configuration::get('UPS_COUNTRY_SELECTED');
        $selectedServiceType = 'AP';
        if (!empty($cookieShippingService)) {
            $selectedServiceType = $this->checkServiceType($cookieShippingService);
        } else {
            $selectedServiceType = 'ADD';
        }
        if (Tools::strtolower($countryCode) == 'us' && 'ADD' == $selectedServiceType) {
            $arrayShippingMerge = $showShippingServiceAdd;
        } else {
            $arrayShippingMerge = array_merge($showShippingServiceAP, $showShippingServiceAdd);
        }

        if ($cookieShippingService == '' && Tools::strtolower($countryCode) != 'us') {
            $serviceSelected = $this->getServiceSelected($arrayShippingMerge, $showShippingServiceAP);
        } else {
            $resultCheckExist = $this->checkServiceExist($cookieShippingService, $arrayShippingMerge);
            if ($resultCheckExist === false) {
                $serviceSelected = $this->getServiceSelected($arrayShippingMerge, $showShippingServiceAP);
            } else {
                $serviceSelected = $resultCheckExist;
            }
        }

        return $serviceSelected;
    }

    private function registerListHook()
    {
        $listHook = $this->getListHook();

        foreach ($listHook as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    private function unregisterListHook()
    {
        $listHook = $this->getListHook();

        foreach ($listHook as $hook) {
            if (!$this->unregisterHook($hook)) {
                return false;
            }
        }

        return true;
    }

    private function getFeeFlatRateService($shippingService, $orderTotalPrice)
    {
        $arrDeliveryRates = $shippingService['val'];

        if (count($arrDeliveryRates) == 1 && $arrDeliveryRates[0]['MinValue'] == 0) {
            $shippingServiceFee = $arrDeliveryRates[0]['DeliRate'];
        } else {
            $arrMinValue = array_column($arrDeliveryRates, 'MinValue');
            array_multisort($arrMinValue, SORT_ASC, $arrDeliveryRates);

            $shippingServiceFee = 0; // Default Fee rather than all Minvalue
            foreach ($arrDeliveryRates as $rates) {
                if ($orderTotalPrice <= $rates['MinValue']) {
                    $shippingServiceFee = $rates['DeliRate'];
                    break;
                }
            }
        }

        return $shippingServiceFee;
    }

    private function getListServiceActive($serviceType)
    {
        $servicesActive = array();
        $services = array();
        if ($serviceType == 'AP') {
            $services = static::$shippingServices->getServicesAp();
        } else {
            $services = static::$shippingServices->getServicesAdd();
        }

        foreach ($services as $service) {
            if (Configuration::get($service['key'])) {
                $typeRate  = Configuration::get($service['keyDeli']);
                $valueRate = ($typeRate == 'FLAT_RATE') ?
                    unserialize(Configuration::get($service['keyVal'])) :
                    Configuration::get($service['keyVal']);

                $servicesActive[] = array(
                    'id_service' => $service['key'],
                    'name'       => $service['name'],
                    'typeRate'   => $typeRate,
                    'val'        => $valueRate,
                    'Ratecode'   => $service['Ratecode'],
                    'TinTcode'   => $service['TinTcode'],
                );
            }
        }

        return $servicesActive;
    }

    private function getExchangeRateById($idCurrency)
    {
        $currencyInstance = Currency::getCurrencyInstance($idCurrency);

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            return ($idCurrency != (int) Configuration::get('PS_CURRENCY_DEFAULT')) ?
                $currencyInstance->conversion_rate : 1;
        } else {
            return $currencyInstance->getConversionRate();
        }
    }

    private function showShippingServices($serviceType, $idAddressDelivery, $orderTotalPrice, $arrApAddress = null)
    {
        // Currency
        $context    = $this->context;
        $currencyId = $context->currency->id;

        // get list Shipping Service valid
        $serviceInfo = (object) $this->getShopTimeInTransit(
            $this->sdk,
            $serviceType,
            $idAddressDelivery,
            $orderTotalPrice,
            $arrApAddress
        );
        // map list service in config and flate rate
        $showShippingService = empty($serviceInfo) ? array() :
                            $this->makeListShippingServices(
                                $serviceType,
                                $serviceInfo,
                                $orderTotalPrice,
                                $currencyId
                            );

        return $showShippingService;
    }

    private function getShopTimeInTransit(
        $sdk,
        $serviceType,
        $idAddressDelivery,
        $orderTotalPrice,
        $arrApAddress = null
    ) {
        $serviceInfor = array();

        $response = $this->callRateAPI(
            $sdk,
            Constants::RATE_API_REQ_OPT_STIT,
            $idAddressDelivery,
            $serviceType,
            $arrApAddress,
            $serviceInfor,
            Configuration::get('UPS_DELI_CURRENCY'),
            $orderTotalPrice
        );

        return $response;
    }

    private function makeListShippingServices(
        $serviceType,
        $apiResponseService,
        $orderTotalPrice,
        $currencyId
    ) {
        $showShippingService = array();
        $cutOffTime          = Configuration::get('UPS_SP_SERV_CUT_OFF_TIME');

        $listServiceConfig = $this->getListServiceActive($serviceType);

        if (!empty($apiResponseService->ShippingService)) {
            $rateShipment = $apiResponseService->ShippingService;
            if (!empty($listServiceConfig)) {
                foreach ($listServiceConfig as $item) {
                    $service = (object) $item;
                    $serviceCode = $service->Ratecode;
                    $serviceKey = $service->id_service;
                    $rateData = $this->getRateService($rateShipment, $serviceCode, $serviceKey);

                    if (!empty($rateData)) {
                        $apiCurrencyCode = '';
                        $apiMonetaryValue = '';
                        if (isset($rateData->TotalCharges)) {
                            $apiCurrencyCode = $rateData->TotalCharges->CurrencyCode;
                            $apiMonetaryValue = $rateData->TotalCharges->MonetaryValue;
                        }
                        if (isset($rateData->NegotiatedRateCharges->TotalCharge)) {
                            $apiCurrencyCode = $rateData->NegotiatedRateCharges->TotalCharge->CurrencyCode;
                            $apiMonetaryValue = $rateData->NegotiatedRateCharges->TotalCharge->MonetaryValue;
                        }

                        if ($service->typeRate == 'FLAT_RATE') {
                            $exchangeRate    = $this->getExchangeRateById($currencyId);
                            $orderPrice      = $orderTotalPrice / $exchangeRate;
                            $flatRateService = $this->getFeeFlatRateService($item, $orderPrice);
                            $shipFee         = $flatRateService * $exchangeRate;
                        } else {
                            $totalCharges = $this->convertRateShipping(
                                $apiMonetaryValue,
                                $apiCurrencyCode,
                                $currencyId
                            );

                            $shipFee = $totalCharges * $service->val / 100;
                        }

                        $shippingFeeValue = (float) round($shipFee, 2);

                        if (isset($rateData->TimeInTransit)) {
                            $timeInTransitData = $rateData->TimeInTransit;
                            $ServiceSummary = $timeInTransitData->ServiceSummary;
                            $dayOfWeek   = $ServiceSummary->EstimatedArrival->DayOfWeek;

                            $totalDays   = "0";
                            if (!empty($ServiceSummary->EstimatedArrival->TotalTransitDays)) {
                                $totalDays   = $ServiceSummary->EstimatedArrival->TotalTransitDays;
                            }

                            $date        = $ServiceSummary->EstimatedArrival->Arrival->Date;
                            $time        = isset($ServiceSummary->EstimatedArrival->Arrival->Time) ?
                                            $ServiceSummary->EstimatedArrival->Arrival->Time :
                                            '000000';

                            $service->shippingFeeValue      = $shippingFeeValue;
                            $service->shippingArrivalDate   = date('Y-m-d H:i:s', strtotime($date . $time));

                            $inforTinTDate = $this->transTimesTotalDays($time). ' ' .
                                            trim($this->transDayOfWeekTotalDays($dayOfWeek)) .
                                            ', ' . $this->transDaysOfTotalDays($date);

                            $service->detailTime  = $cutOffTime == 24 ?
                                '' :
                                '(' . $this->sdk->t('ups', 'txtE_DeliveredOn') . ' ' . $inforTinTDate . ')';
                            $service->detailFee = Tools::displayPrice($shippingFeeValue);
                            //$service->totalTransitDays      = $this->transTotalTransitDays($totalDays);
                            $service->totalTransitDays      = $inforTinTDate;

                            $service->priceDisplay  = Tools::displayPrice($shippingFeeValue);
                        }

                        $showShippingService[] = (array) $service;
                    } else {
                        continue;
                    }
                }
            }
        }

        return $showShippingService;
    }

    private function getRateService($rateShipmentData, $serviceCode, $serviceKey)
    {
        $result = array();
        if (!empty($rateShipmentData) && is_array($rateShipmentData)) {
            foreach ($rateShipmentData as $item) {
                if ($serviceCode == $item->Service->Code) {
                    $result[] = $item;
                }
            }
        }
        if (!empty($result)) {
            foreach ($result as $item) {
                if (strpos($serviceKey, "SATDELI") !== false) {
                    if ($item->TimeInTransit->ServiceSummary->SaturdayDelivery == "1") {
                        return $item;
                    }
                } else {
                    if ($item->TimeInTransit->ServiceSummary->SaturdayDelivery == "0") {
                        return $item;
                    }
                }
            }
        }
        return [];
    }

    private function transTotalTransitDays($totalDays)
    {
        $translatedTotalDays = '';
        $dayWeeks            = array(
            0  => $this->sdk->t('ups', 'txtE_DeliveryToday'),
            1  => $this->sdk->t('ups', 'txtE_DeliveryNextDay'),
            2  => $this->sdk->t('ups', 'txtE_DeliveryTwoday'),
            3  => $this->sdk->t('ups', 'txtE_DeliveryThreeday'),
            4  => $this->sdk->t('ups', 'txtE_DeliveryFourday'),
            5  => $this->sdk->t('ups', 'txtE_DeliveryFiveday'),
            6  => $this->sdk->t('ups', 'txtE_DeliverySixday'),
            7  => $this->sdk->t('ups', 'txtE_DeliverySevenday'),
            8  => $this->sdk->t('ups', 'txtE_DeliveryEightday'),
            9  => $this->sdk->t('ups', 'txtE_DeliveryNineday'),
            10 => $this->sdk->t('ups', 'txtE_DeliveryTenday'),
        );

        if (isset($totalDays) && is_numeric($totalDays)) {
            if ($totalDays >= 0 && $totalDays < 11) {
                $translatedTotalDays = $dayWeeks[$totalDays];
            } else {
                $translatedTotalDays = $this->sdk->t('ups', 'txtE_DeliveryFirstday') . ' '
                . $totalDays . ' '
                . $this->sdk->t('ups', 'txtE_DeliveryFinalday');
            }
        } else {
            $translatedTotalDays = 'The String reponse when API return not Total TransitDays';
        }

        return $translatedTotalDays;
    }

    private function transDayOfWeekTotalDays($day)
    {
        $dayWeeks = array(
            'MON' => $this->sdk->t('ups', 'txtE_ShoppingAPMonday'),
            'TUE' => $this->sdk->t('ups', 'txtE_ShoppingAPTuesday'),
            'WED' => $this->sdk->t('ups', 'txtE_ShoppingAPWednesday'),
            'THU' => $this->sdk->t('ups', 'txtE_ShoppingAPThursday'),
            'FRI' => $this->sdk->t('ups', 'txtE_ShoppingAPFriday'),
            'SAT' => $this->sdk->t('ups', 'txtE_ShoppingAPSaturday'),
            'SUN' => $this->sdk->t('ups', 'txtE_ShoppingAPSunday'),
        );
        return $dayWeeks[$day];
    }

    private function transTimesTotalDays($stringTimes)
    {
        $transTimesTotalDays = '';
        $transTimesTotalDays = date(Constants::FORMAT_TIME_ESHOPER, strtotime($stringTimes));
        if (((int) $stringTimes) >= 230000) {
            $transTimesTotalDays = $this->sdk->t('ups', 'txtE_EndOfDay');
        }
        return $transTimesTotalDays;
    }

    private function transDaysOfTotalDays($stringDays)
    {
        $transDaysOfTotalDays = '';
        $transDaysOfTotalDays = date('d F Y', strtotime($stringDays));
        return $transDaysOfTotalDays;
    }

    private function getUpsDataInfoByKey($key)
    {
        $info = array();
        $info = $this->getUpsData('exactly', $key);

        if (array_key_exists($key, $info) && isset($info[$key])) {
            return $info[$key];
        }

        return false;
    }

    private function getListHook()
    {
        $listHook = array();

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $listHook = static::HOOKS_16;
        } else {
            $listHook = static::HOOKS_17;
        }

        return array_merge($listHook, static::HOOKS);
    }

    private function initTexts()
    {
        $addLine = $this->sdk->t('address', 'txtAddressLine')
            . ', ' .
            Tools::strtolower(
                $this->sdk->t('address', 'txtCity')
                . ', ' .
                $this->sdk->t('address', 'txtState')
                . ', ' .
                $this->sdk->t('address', 'txtPostalCode')
            );

        $txtE_ShippingMethod = $this->sdk->t('ups', 'txtE_ShippingMethod');
        if ($this->usa()) {
            $txtE_ShippingMethod = $this->sdk->t('ups', 'txtE_ShippingMethodUPS');
        }

        $this->texts = array(
            'txtAccessPointBrand'    => $this->sdk->t('shippingservice', 'ttlAP2'),
            'txtSearchAP'            => $this->sdk->t('ups', 'txtSearchAP'),
            'txtNear'                => $this->sdk->t('ups', 'txtNear'),
            'txtUseAddress'          => $this->sdk->t('ups', 'txtUseAddress'),
            'txtAddressLine'         => $addLine,
            'txtSearch'              => $this->sdk->t('button', 'txtSearch'),
            'txtResults'             => $this->sdk->t('ups', 'txtResults'),
            'txtE_DeliverAddress'    => $this->sdk->t('ups', 'txtE_DeliverAddress'),
            'txtE_AddressRequired'   => $this->sdk->t('ups', 'txtE_AddressRequired'),
            'txtE_ShippingMethod'    => $txtE_ShippingMethod,
            'txtAccessPointBrand2'   => $this->sdk->t('ups', 'txtAccessPointBrand2'),
            'txtE_DeliverAddressPkg' => $this->sdk->t('ups', 'txtE_DeliverAddressPkg'),
        );
        if ($this->usa()) {
            $this->texts['txtAccessPointBrandUS'] = $this->sdk->t('shippingservice', 'ttlAP2US');
            $this->texts['txtSearchAP']     = $this->sdk->t('ups', 'txtSearchAPUSshop');
            $this->texts['txtAccessPointBrand2']  = $this->sdk->t('ups', 'txtAccessPointBrand2US');
        }
    }

    private function getIsoById($idState)
    {
        $sql = new DbQuery();

        $sql->select('iso_code');
        $sql->from('state');
        $sql->where("id_state = '" . pSQL($idState) . "'");

        return Db::getInstance()->getValue($sql);
    }

    private function callRateAPI(
        $sdk,
        $requestOption,
        $idAddressDelivery,
        $serviceType,
        $arrApAddress = null,
        $serviceInfor = array(),
        $currencyCode = '',
        $orderTotalPrice = 0
    ) {
        $packageInfo     = array();
        $addressCustomer = new Address($idAddressDelivery);
        $packageInfo[]   = unserialize(Configuration::get('UPS_PKG_1_DIMENSION'));
        $accountNumber   = static::getAccountNumberByService($serviceType);
        // $accountInfo     = $this->getDefaultAccount($this->getListAccount());
        $accountShiper   = $this->getAccountByNumber($accountNumber);

        $stateCodeGet = $this->getIsoById($addressCustomer->id_state);
        $accessorials = $this->getAccessorialCodes($this->listAccessorialActive());
        $cutOffTime   = Configuration::get('UPS_SP_SERV_CUT_OFF_TIME');
        $pickupDate   = $this->calculatePickupDate((int) $cutOffTime);

        $rateInfo = array(
            'RequestOption' => $requestOption,

            'ShipperName' => isset($accountShiper['AccountName']) ? $accountShiper['AccountName'] : '',
            'ShipperNumber' => isset($accountShiper['AccountNumber']) ? $accountShiper['AccountNumber'] : '',
            'ShipperAddressLine1' => isset($accountShiper['AddressLine1']) ? $accountShiper['AddressLine1'] : '',
            'ShipperAddressLine2' => isset($accountShiper['AddressLine2']) ? $accountShiper['AddressLine2'] : '',
            'ShipperAddressLine3' => isset($accountShiper['AddressLine3']) ? $accountShiper['AddressLine3'] : '',
            'ShipperCity' => isset($accountShiper['City']) ? $accountShiper['City'] : '',
            'ShipperStateProvinceCode' => isset($accountShiper['ProvinceCode']) ? $accountShiper['ProvinceCode'] : 'XX',
            'ShipperStatePostalCode' => isset($accountShiper['PostalCode']) ? $accountShiper['PostalCode'] : '',
            'ShipperCountryCode' => isset($accountShiper['CountryCode']) ? $accountShiper['CountryCode'] : '',

            'ShipToName' => $addressCustomer->lastname . ' ' . $addressCustomer->firstname,
            'ShipToAddress1' => $addressCustomer->address1,
            'ShipToAddress2' => $addressCustomer->address2,
            'ShipToAddress3' => '',
            'ShipToCity' => $addressCustomer->city,
            'ShipToStateProvinceCode' => !empty($stateCodeGet) ? $stateCodeGet : 'XX',
            'ShipToPostalCode' => $addressCustomer->postcode,
            'ShipToCountryCode' => Country::getIsoById($addressCustomer->id_country),

            'ShipFromName' => isset($accountShiper['CustomerName']) ? $accountShiper['CustomerName'] : '',
            'ShipFromAddress1' => isset($accountShiper['AddressLine1']) ? $accountShiper['AddressLine1'] : '',
            'ShipFromAddress2' => isset($accountShiper['AddressLine2']) ? $accountShiper['AddressLine2'] : '',
            'ShipFromAddress3' => isset($accountShiper['AddressLine3']) ? $accountShiper['AddressLine3'] : '',
            'ShipFromCity'  => isset($accountShiper['City']) ? $accountShiper['City'] : '',
            'ShipFromStateProvinceCode' => isset($accountShiper['ProvinceCode']) ?
                $accountShiper['ProvinceCode'] : 'XX',
            'ShipFromPostalCode' => isset($accountShiper['PostalCode']) ? $accountShiper['PostalCode'] : '',
            'ShipFromCountryCode' => isset($accountShiper['CountryCode']) ? $accountShiper['CountryCode'] : '',

            'packages' => $packageInfo,
            'accessorialsService' => $accessorials,
        );

        if (!empty($serviceInfor) && isset($serviceInfor['Code'])) {
            $rateInfo['ShippingServiceCode']        = $serviceInfor['Code'];
            $rateInfo['ShippingServiceDescription'] = $serviceInfor['Description'];
        }

        if ($requestOption == Constants::RATE_API_REQ_OPT_TIME || $requestOption == Constants::RATE_API_REQ_OPT_STIT) {
            $rateInfo['PackageWeightCode']        = $packageInfo[0]['weightUnit'];
            $rateInfo['PackageWeightDescription'] = $packageInfo[0]['weightUnit'];
            $rateInfo['PackageWeightWeight']      = $packageInfo[0]['weight'];

            $rateInfo['CurrencyCode']  = $currencyCode;
            $rateInfo['MonetaryValue'] = (string) $orderTotalPrice;

            $rateInfo['PickupDate'] = $pickupDate;
        }

        if ($arrApAddress != null) {
            $rateInfo['AlternateDeliveryAddress']['ApName']              = $arrApAddress['consigneeName'];
            $rateInfo['AlternateDeliveryAddress']['ApAddressLine']       = $arrApAddress['addressLine'];
            $rateInfo['AlternateDeliveryAddress']['ApCity']              = $arrApAddress['city'];
            $rateInfo['AlternateDeliveryAddress']['ApStateProvinceCode'] = $arrApAddress['state'];
            $rateInfo['AlternateDeliveryAddress']['ApPostalCode']        = $arrApAddress['postalCode'];
            $rateInfo['AlternateDeliveryAddress']['ApCountryCode']       = $arrApAddress['countryCode'];
        }

        $rateInfo['sdk'] = $sdk;

        $rateAPI  = new Ups\Api\Rate();
        $response = $rateAPI($rateInfo);

        return $response;
    }

    private static function getAccountNumberByService($serviceType)
    {
        if ($serviceType == 'AP') {
            return Configuration::get('UPS_SP_SERV_AP_CHOOSE_ACC');
        } else {
            return Configuration::get('UPS_SP_SERV_ADDRESS_CHOOSE_ACC');
        }
    }

    private function getAccountByNumber($accountNumber)
    {
        $accountNumberKey  = "AccountNumber$accountNumber";
        $accountInfoReturn = array();

        if ($accountNumber) {
            $getAccount = $this->getUpsData('exactly', $accountNumberKey);

            if (isset($getAccount[$accountNumberKey])) {
                $accountInfoReturn = (array) json_decode($getAccount[$accountNumberKey]);

                $accountInfoReturn['Country']      = $this->pluginCountryName;
                $accountInfoReturn['AccountName']  = isset($accountInfoReturn['AccountName']) ?
                    $this->checkDecode($accountInfoReturn['AccountName']) : '';
                $accountInfoReturn['AddressType']  = $this->checkDecode($accountInfoReturn['AddressType']);
                $accountInfoReturn['AddressLine1'] = $this->checkDecode($accountInfoReturn['AddressLine1']);
                $accountInfoReturn['AddressLine2'] = $this->checkDecode($accountInfoReturn['AddressLine2']);
                $accountInfoReturn['AddressLine3'] = $this->checkDecode($accountInfoReturn['AddressLine3']);
                $accountInfoReturn['City']         = $this->checkDecode($accountInfoReturn['City']);
                $accountInfoReturn['ProvinceCode'] = isset($accountInfoReturn['ProvinceCode'])
                                                        ? $accountInfoReturn['ProvinceCode'] : 'XX';
            }
        }

        return $accountInfoReturn;
    }

    private function getIdCarriesByIdReference()
    {
        $id = Configuration::get('UPS_SHIPING_METHOD_REFERENCE_ID');
        $carrier = Carrier::getCarrierByReference($id);

        if ($carrier !== false) {
            return $carrier->id;
        }

        return false;
    }

    private function changeStatusCarrier($target)
    {
        $id = $this->getIdCarriesByIdReference();
        if ($id) {
            $carrier = new Carrier($id);

            if (($carrier->name != null) && ($carrier->active != $target)) {
                $carrier->toggleStatus();
            }
        }
    }

    private function addHeaderBaseSecurityOption()
    {
        // Check Clickjacking Defense
        if (Configuration::get('UPS_SEC_CLICKJACKING')) {
            // Check X Frame Options
            switch (Configuration::get('UPS_SEC_X_FRAME_OPTIONS')) {
                case 0:
                    break;
                case 1:
                    header('X-Frame-Options: DENY');
                    break;
                case 2:
                    header('X-Frame-Options: SAMEORIGIN');
                    break;
                default:
                    break;
            }

            // Check frame killer
            // implment in hookDisplayHeader

            // Check Content Security Policy
            switch (Configuration::get('UPS_SEC_CONTENT_SEC_POLICY')) {
                case 0:
                    break;
                case 1:
                    header('Content-Security-Policy: frame-ancestors \'none\'');
                    break;
                case 2:
                    header('Content-Security-Policy: frame-ancestors \'self\'');
                    break;
                default:
                    break;
            }
        }

        // Check Content Sniffing Prevention
        if (Configuration::get('UPS_SEC_CONTENT_SNIFFING')) {
            header('X-Content-Type-Options: nosniff');
        }

        // Check X-XSS-Protection
        if (Configuration::get('UPS_SEC_CROSS_SITE')) {
            header('X-XSS-Protection: 1; mode=block');
        }

        // Check Strict Transport
        if (Configuration::get('UPS_SEC_STRICT_TRANSPORT')) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        // Check From Caching
        if (Configuration::get('UPS_SEC_FROM_CACHING')) {
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Expires: 0');
            header('Pragma: no-cache');
        }

        header('X-Powered-By: ');
    }

    private function removeConfiguration()
    {
        $this->removeConfigPkgDimension();
        $this->removeListConfig();
        $this->removeShippingServices();
        $this->removeAccessorials();

        return true;
    }

    private function removeListConfig()
    {
        foreach (array_keys(Constants::LIST_CONFIGURATION) as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }
    }

    private function removeConfigPkgDimension()
    {
        $listIndexPkg = unserialize(Configuration::get('UPS_PKG_DIMENSION_COUNT'));
        if ($listIndexPkg) {
            foreach ($listIndexPkg as $indexPkg) {
                if (!Configuration::deleteByName('UPS_PKG_' . $indexPkg . '_DIMENSION')) {
                    return false;
                }
            }
        }
    }

    private function removeShippingServices()
    {
        $services = static::$shippingServices->getShippingServices();

        foreach ($services as $service) {
            Configuration::deleteByName($service['key']);
            Configuration::deleteByName($service['keyDeli']);
            Configuration::deleteByName($service['keyVal']);
        }
    }

    private function removeAccessorials()
    {
        foreach (static::$accessorials as $accessorial) {
            Configuration::deleteByName($accessorial['key']);
        }
    }

    private function generateGUID()
    {
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

    public function explodeIds($ids)
    {
        $idExploded = '';
        $ids = explode(',', $ids);

        foreach ($ids as $id) {
            $idExploded .= "'" . pSQL($id) . "',";
        }

        return rtrim($idExploded, ',');
    }

    public function putContents($headers, $contents, $delimiter, $prefix)
    {
        // Clean buffer
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }
        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="' . $prefix .date('dmy') . '.csv"');

        $fd = fopen('php://output', 'wb');
        fprintf($fd, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($fd, $headers, ',', $delimiter);

        foreach ($contents as $content) {
            fputcsv($fd, $content, ',', $delimiter);
        }

        @fclose($fd);
    }

    public function upgradeUPSMenu()
    {
        $check = Configuration::get('UPS_UPGRRADE_MENU');
        if ($check == false) {
            $this->updateLangMenu(array(), 0);
        }
    }

    public function upgradePluginVersion()
    {
        $versionPlugin = Configuration::get('UPS_PLUGIN_VERSION');
        if ($versionPlugin != $this->version) {
            Configuration::updateValue('UPS_PLUGIN_VERSION', $this->version);
            $upgradePluginVersion = new PluginManager\CollectionApi\UpgradePluginVersion();
            $data = $this->addParam(
                array(
                    'merchantKey' => Configuration::get('MERCHANT_KEY'),
                    'version' => $this->version
                )
            );
            $upgradePluginVersion($data);
            $this->updateCarrierLang();
        }
    }

    public function usa()
    {
        return $this->pluginCountryCode === 'US';
    }

    private function updateDeliveryRateWithUpgradeVersion() {
        $arrDefaultRate = array(
            array(
                'MinValue' => 0,
                'DeliRate' => 0,
            ),
        );

        $services = static::$shippingServices->getShippingServices();

        foreach ($services as $service) {
            if (Configuration::get($service['keyVal']) == null) {
                Configuration::updateValue($service['key'], 0);
                Configuration::updateValue($service['keyDeli'], 'FLAT_RATE');
                Configuration::updateValue($service['keyVal'], serialize($arrDefaultRate));
            }
        }
    }
}
