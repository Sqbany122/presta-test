<?php
/**
 * @author    Innovation Software Sp.z.o.o
 * @copyright 2018 Innovation Software Sp.z.o.o
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category  apaczkashipment
 * @package   apaczkashipment
 */

if (! defined('_PS_VERSION_')) {
    exit;
}


require_once _PS_MODULE_DIR_ . 'apaczkashipping/includer.php';

//******************************************************************************
//********************** CLASS APACZKASHIPPING *********************************
//******************************************************************************

class ApaczkaShipping extends CarrierModule
{
    const PREFIX = 'apaczka_';
    const APACZKA_PARCEL_LOCKERS_ID = 'APACZKA_PARCEL_LOCKERS_ID';
    const PARCEL_LOCKER_CARRIER_NAME = 'InPost - Paczkomaty';
    const TRACK_URL = 'https://inpost.pl/pl/pomoc/znajdz-przesylke?parcel=@';
    const TRACK_URL1 = 'https://inpost.pl/pl/pomoc/znajdz-przesylke?';
    const PARCEL_LOCKER_MAX_H = 38;
    const PARCEL_LOCKER_MAX_L = 41;
    const PARCEL_LOCKER_MAX_D = 64;
    const PARCEL_LOCKER_MAX_W = 30;

    private $debugMode = false;

    public function __construct()
    {
        $this->name = 'apaczkashipping';
        $this->tab  = 'shipping_logistics';
        $this->version = '1.2.0';
        $this->author = 'Innovation Software Sp. z.o.o';
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.6',
            'max' => '1.6.99'
        );
        $test = 'variable';
        parent::__construct();

        $this->displayName = $this->l('Apaczka.pl');
        $this->description = $this->l('Szybka i tania wysyłka z Apaczka.pl');

        $this->confirmUninstall
            = $this->l('Czy napewno chcesz odinstalowac? Utracisz wszystkie dane konfiguracyjne.');

        $this->bootstrap = true;
        $this->defaultVar();        //Zaladowanie zmiennych konfiguracyjnych
    }

//******************************************************************************
    public function defaultVar()
    {
// Loading Fields List

        $this->_fieldsList = array(
            'countries'                        => '',
            'apaczka_login'                    => '',
            'apaczka_password'                 => '',
            'apaczka_apikey'                   => '',
            'apaczka_test'                     => '',
            'apaczka_sender_postcode'          => '',
            'apaczka_sender_city'              => '',
            'apaczka_sender_country'           => '',
            'apaczka_sender_shop_name'         => '',
            'apaczka_sender_address1'          => '',
            'apaczka_sender_address2'          => '',
            'apaczka_sender_contactName'       => '',
            'apaczka_sender_phone'             => '',
            'apaczka_sender_email'             => '',
            //**************************
            'apaczka_shipment_price'           => '',
            'apaczka_account'                  => '',
            'apaczka_contents'                 => '',
            'apaczka_serviceCode'              => '',
            'apaczka_insurance'                => '',
            'apaczka_orderPickupType'          => '',
            'apaczka_ref_text'                 => '',
            'apaczka_weight'                   => '',
            'apaczka_dimension1'               => '',
            'apaczka_dimension2'               => '',
            'apaczka_dimension3'               => '',
            //**************************
            'apaczka_sender_notif_delivered'   => '',
            'apaczka_sender_notif_exception'   => '',
            'apaczka_sender_notif_sent'        => '',
            'apaczka_sender_notif_register'    => '',
            'apaczka_receiver_notif_delivered' => '',
            'apaczka_receiver_notif_exception' => '',
            'apaczka_receiver_notif_sent'      => '',
            'apaczka_receiver_notif_register'  => ''
            //**************************
        );
    }

//******************************************************************************
    public function resetVar()
    {
        Configuration::updateValue('apaczka_login', '');
        Configuration::updateValue('apaczka_password', '');
        Configuration::updateValue('apaczka_apikey', '');
        Configuration::updateValue('apaczka_test', '');
        //*************************
        Configuration::updateValue('apaczka_sender_shop_name', '');
        Configuration::updateValue('apaczka_sender_address1', '');
        Configuration::updateValue('apaczka_sender_address2', '');
        Configuration::updateValue('apaczka_sender_postcode', '');
        Configuration::updateValue('apaczka_sender_city', '');
        Configuration::updateValue('apaczka_sender_country', '');
        Configuration::updateValue('apaczka_sender_contactName', '');
        Configuration::updateValue('apaczka_sender_phone', '');
        Configuration::updateValue('apaczka_sender_email', '');
        Configuration::updateValue('apaczka_account', '');
        //*************************
        Configuration::updateValue('apaczka_ref_text', '');
        Configuration::updateValue('apaczka_contents', '');
        Configuration::updateValue('apaczka_weight', '');
        Configuration::updateValue('apaczka_dimension1', '');
        Configuration::updateValue('apaczka_dimension2', '');
        Configuration::updateValue('apaczka_dimension3', '');
        //*************************
        Configuration::updateValue('apaczka_sender_notif_delivered', '');
        Configuration::updateValue('apaczka_sender_notif_exception', '');
        Configuration::updateValue('apaczka_sender_notif_register', '');
        Configuration::updateValue('apaczka_sender_notif_sent', '');
        Configuration::updateValue('apaczka_receiver_notif_delivered', '');
        Configuration::updateValue('apaczka_receiver_notif_exception', '');
        Configuration::updateValue('apaczka_receiver_notif_register', '');
        Configuration::updateValue('apaczka_receiver_notif_sent', '');
        //*************************
        Configuration::updateValue('apaczka_insurance', '');
        Configuration::updateValue('apaczka_orderPickupType', '');
        Configuration::updateValue('apaczka_serviceCode', '');
    }

//******************************************************************************
    public function install()
    {
        if (! parent::install() || ! $this->createTables()
            || ! $this->createCarrier(
                self::APACZKA_PARCEL_LOCKERS_ID,
                self::PARCEL_LOCKER_CARRIER_NAME
            )
            || ! $this->registerHook('header')
            || ! $this->registerHook('displayBackOfficeHeader')
            || ! $this->registerHook('displayAdminOrder')
            || ! $this->registerHook('extraCarrier')
            || ! $this->registerHook('updateCarrier')
            || ! $this->registerHook('displayOrderConfirmation')
            || ! $this->registerHook('actionAdminControllerSetMedia')
            || ! $this->registerHook('actionAdminControllerSetMedia')
            || ! $this->installDB()
        ) {
            return false;
        }
        if (! extension_loaded('soap')) {
            return false;
        }

        return true;
    }

    protected function installDB()
    {
        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'apaczka_parcel_locker_delivery` (
            `id_apaczka_parcel_locker_delivery` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_order` INT( 11 ) UNSIGNED,
            `receiver_parcel_locker_code` TEXT,
            `sender_parcel_locker_code` TEXT,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_apaczka_parcel_locker_delivery`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        foreach ($sql as $_sql) {
            if (! Db::getInstance()->Execute($_sql)) {
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        Db::getInstance()->update(
            'carrier',
            array('deleted' => 1),
            '`external_module_name` = \'apaczkacarrier\''
        );

        $sql = 'DROP TABLE IF EXISTS `apaczka_address_book`;';
        if (! Db::getInstance()->execute($sql)) {
            return false;
        }

        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_
               . 'apaczka_parcel_locker_delivery`;';
        if (! Db::getInstance()->execute($sql)) {
            return false;
        }

        $this->resetVar();
        if (! parent::uninstall()
            || ! (bool)self::deleteCarrier((int)Configuration::get(self::APACZKA_PARCEL_LOCKERS_ID))
        ) {
            return false;
        }

        return true;
    }

    public function enable($forceAll = false)
    {
        Db::getInstance()->update(
            'carrier',
            array('active' => 1),
            '`external_module_name` = \'apaczkacarrier\''
        );
        parent::enable($forceAll);
    }

    public function disable($forceAll = false)
    {
        Db::getInstance()->update(
            'carrier',
            array('active' => 0),
            '`external_module_name` = \'apaczkacarrier\''
        );
        parent::disable($forceAll);
    }

    public function createTables()
    {
        $sql
            = 'CREATE TABLE IF NOT EXISTS `apaczka_address_book` (
			`contact_id` int(10) NOT NULL AUTO_INCREMENT,
			`nazwa` varchar(35) NOT NULL,
                        `adres` varchar(35) NOT NULL,
                        `adres2` varchar(35),
                        `kod_pocztowy` varchar(7) NOT NULL,
                        `miasto` varchar(35) NOT NULL,
                        `id_kraju` int(12) NOT NULL,
                        `osoba_kontaktowa` varchar(35) NOT NULL,
                        `telefon` varchar(15) NOT NULL,
                        `email` varchar(100),
                        `konto_pobraniowe` varchar(40) NOT NULL,
                        PRIMARY KEY  (`contact_id`),
                        UNIQUE (`nazwa`, `miasto`,`adres`, `konto_pobraniowe`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
        if (! Db::getInstance()->execute($sql)) {
            return false;
        }
        $sql
            = 'CREATE TABLE IF NOT EXISTS `apaczka_orders` (
			`id_order_presta` int(12) NOT NULL,
			`id_order_apaczka` int(14) NOT NULL,
                  `order_number_apaczka` varchar(24) NOT NULL,
			PRIMARY KEY  (`id_order_presta`)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
        if (! Db::getInstance()->execute($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @param $id
     * @param $name
     *
     * Create parcel locker carrier
     *
     * @return bool
     */
    private function createCarrier($id, $name)
    {
        $id_carrier = (int)Configuration::get($id);
        $carrier    = Carrier::getCarrierByReference((int)$id_carrier);

        if ($id_carrier && Validate::isLoadedObject($carrier)) {
            if (! $carrier->deleted) {
                return true;
            } else {
                $carrier->deleted = 0;

                return (bool)$carrier->save();
            }
        }

        $carrier                       = new Carrier();
        $carrier->name                 = $name;
        $carrier->active               = 1;
        $carrier->is_free              = 0;
        $carrier->shipping_handling    = 1;
        $carrier->shipping_external    = 1;
        $carrier->shipping_method      = 1;
        $carrier->max_width            = self::PARCEL_LOCKER_MAX_L;
        $carrier->max_height           = self::PARCEL_LOCKER_MAX_H;
        $carrier->max_depth            = self::PARCEL_LOCKER_MAX_D;
        $carrier->max_weight           = self::PARCEL_LOCKER_MAX_W;
        $carrier->grade                = 0;
        $carrier->is_module            = 1;
        $carrier->need_range           = 1;
        $carrier->range_behavior       = 1;
        $carrier->external_module_name = $this->name;
        $carrier->url                  = self::TRACK_URL;

        $delay = array();
        foreach (Language::getLanguages(false) as $language) {
            $delay[$language['id_lang']] = '2-3 dni robocze ';
        }
        $carrier->delay = $delay;

        if (! $carrier->save()) {
            return false;
        }

        $range_obj             = $carrier->getRangeObject();
        $range_obj->id_carrier = (int)$carrier->id;
        $range_obj->delimiter1 = 0;
        $range_obj->delimiter2 = self::PARCEL_LOCKER_MAX_W;
        if (! $range_obj->save()) {
            return false;
        }

        if (! self::assignGroups($carrier)) {
            return false;
        }
        if (! self::createZone($carrier->id)) {
            return false;
        }
        if (! self::createDelivery($carrier->id, $range_obj->id)) {
            return false;
        }
        if (! Configuration::updateValue($id, (int)$carrier->id)) {
            return false;
        }

        return true;
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS(
            array(
                $this->_path . 'views/css/admin_apaczka.css',
            )
        );
        $this->context->controller->addJS(
            array(
                'https://mapa.apaczka.pl/client/apaczka.map.js',
            )
        );

        $link = $this->context->link;

        $this->smarty->assign(
            array(
                'apaczka_parcel_locker_carrier_id' => Configuration::get(self::APACZKA_PARCEL_LOCKERS_ID),
                'link'                             => $link
            )
        );

        return $this->display(__FILE__, 'views/templates/admin/header.tpl');
    }

    /**
     * @param $id_carrier
     *
     * Delete carrier upon module uninstall
     *
     * @return bool
     */
    private static function deleteCarrier($id_carrier)
    {
        if (! $id_carrier) {
            return true;
        }

        $carrier = Carrier::getCarrierByReference($id_carrier);

        if (! Validate::isLoadedObject($carrier)) {
            return true;
        }
        if ($carrier->deleted) {
            return true;
        }

        $carrier->deleted = 1;

        return (bool)$carrier->save();
    }

    /**
     * @param $id_carrier
     *
     * @return mixed
     */
    private static function createZone($id_carrier)
    {
        return DB::getInstance()->Execute('
        	INSERT INTO `' . _DB_PREFIX_ . 'carrier_zone`
            (`id_carrier`, `id_zone`)
            VALUES
            ("' . (int)$id_carrier . '", "1")');
    }

    /**
     * @param $id_carrier
     * @param $id_range
     *
     * @return mixed
     */
    private static function createDelivery($id_carrier, $id_range)
    {
        return DB::getInstance()->Execute('
        	INSERT INTO `' . _DB_PREFIX_ . 'delivery`
            (`id_carrier`, `id_range_weight`, `id_zone`, `price`)
            VALUES
            ("' . (int)$id_carrier . '", "' . (int)$id_range . '", "1", "10")');
    }


    /**
     * @param $carrier
     *
     * Assign carrier groups
     *
     * @return bool
     */
    private function assignGroups($carrier)
    {
        $groups = array();
        $chosen_groups
                = Group::getGroups((int)Context::getContext()->language->id);
        foreach ($chosen_groups as $group) {
            $groups[] = $group['id_group'];
        }

        if (version_compare(_PS_VERSION_, '1.5.5', '<')) {
            if (! self::setGroupsOld((int)$carrier->id, $groups)) {
                return false;
            }
        } else {
            if (! $carrier->setGroups($groups)) {
                return false;
            }
        }

        return true;
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->addJS(
            array(
                $this->_path . 'views/js/admin_apaczka.js',
            )
        );
    }

    public function hookExtraCarrier($params)
    {
        if(isset($this->context->cookie->apaczka_parcel_locker_name) && !empty($this->context->cookie->apaczka_parcel_locker_name)) {
            $parcelLockerCode = $this->context->cookie->apaczka_parcel_locker_name;
        } else {
            $parcelLockerCode = null;
        }

        $this->smarty->assign(
            'parcelLockerCode',
            $parcelLockerCode
        );

        return $this->display(
            __FILE__,
            'views/templates/hook/displayExtraCarrier.tpl'
        );
    }

    public function hookHeader($params)
    {
        if (in_array(
            Context::getContext()->controller->php_self,
            array(
                'order-opc',
                'order'
            )
        )
        ) {
            $this->context->controller->addCSS(
                array(
                    $this->_path . 'views/css/front_apaczka.css'
                )
            );

            $this->context->controller->addJS(
                array(
                    $this->_path . 'views/js/front_apaczka.js'
                )
            );

            $this->smarty->assign(
                'parcel_locker_carrier_id',
                Configuration::get(self::APACZKA_PARCEL_LOCKERS_ID)
            );

            return $this->display(__FILE__, 'header.tpl');
        }
    }

    /**
     * @param $params
     */
    public function hookUpdateCarrier($params)
    {
        $id_carrier_old = (int)($params['id_carrier']);
        $id_carrier_new = (int)($params['carrier']->id);
        if ($id_carrier_old
            == (int)(Configuration::get(self::APACZKA_PARCEL_LOCKERS_ID))
        ) {
            Configuration::updateValue(
                self::APACZKA_PARCEL_LOCKERS_ID,
                $id_carrier_new
            );
        }
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $objOrder = $params['objOrder'];
        /*Multishipping support*/
        $ordersCollectionResults = Order::getByReference($objOrder->reference)
                                        ->getResults();
        foreach ($ordersCollectionResults as $itemOrderObj) {
            if (($itemOrderObj->id_carrier
                 == Configuration::get(self::APACZKA_PARCEL_LOCKERS_ID))
                && $itemOrderObj->id
            ) {
                $parcelLockerDeliveryObj
                    = ParcelLockerDelivery::loadByOrderId($itemOrderObj->id);
                //prevent multiple insert after reload success-page
                if (! $parcelLockerDeliveryObj->id) {
                    $parcelLockerDeliveryObj->id_order = (int)$itemOrderObj->id;
                    $parcelLockerDeliveryObj->receiver_parcel_locker_code
                                                       = pSQL($this->context->cookie->apaczka_parcel_locker_name);

                    if ($parcelLockerDeliveryObj->add()) {
                        unset($this->context->cookie->apaczka_parcel_locker_name);
                    }
                }
            }
        }
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        if (isset($params) && ($shipping_cost)) {
            //reason of using $shipping_cost => CarrierModuleCore::getOrderShippingCost()
            //$params - you can use this parameter for customizing delivery price calculation
            $carrierObj = new Carrier(Configuration::get(self::APACZKA_PARCEL_LOCKERS_ID));
            $delivery_price =
                Carrier::getDeliveryPriceByRanges(
                    $carrierObj->getRangeTable(),
                    Configuration::get(self::APACZKA_PARCEL_LOCKERS_ID)
                );
            $max_delivery_price = 0;
            foreach ($delivery_price as $d_price) {
                if ($d_price['price'] > $max_delivery_price) {
                    $max_delivery_price = $d_price['price'];
                }
            }
            return $max_delivery_price;
        } else {
            return 0;
        }
    }

    public function getOrderShippingCostExternal($params)
    {
        return $this->getOrderShippingCost($params, 0);
    }

//******************************************************************************

    /**
     * Przygotowuje zmienne Smarty do wyswietlenia formularza po wystąpieniu
     * błedu przy wysyłce (Przepisuje dane ze starego formularza).
     * Dodatkowo pobiera ksiazke adresowa oraz liste krajow, sprawdza czy
     * wybrano usluge pobrania.
     *
     * @param type $params - dane zamowienia.
     */
    public function smartyAssignOrderFormError($params)
    {
        $order  = new Order((int)$params['id_order']);
        $sql    = 'SELECT * FROM apaczka_address_book';
        $result = Db::getInstance()->executeS($sql);

        $countries        = unserialize((string)Configuration::get('countries'));
        $smarty_variables = array(
            'countries'            => $countries,
            'contacts'             => $result,
            'cod'                  => (int)Tools::getValue('cod'),
            'id_order'             => $params['id_order'],
            'token'                => Tools::getValue('token'),
            'R_company'            => (string)Tools::getValue('receiver_name'),
            'R_address1'           => (string)Tools::getValue('receiver_addressLine1'),
            'R_address2'           => (string)Tools::getValue('receiver_addressLine2'),
            'R_postcode'           => (string)Tools::getValue('receiver_postalCode'),
            'R_city'               => (string)Tools::getValue('receiver_city'),
            'R_country'            => (string)Tools::getValue('receiver_countryId'),
            'R_firstname'          => (string)Tools::getValue('receiver_contactName'),
            'R_lastname'           => '',
            'R_phone'              => (string)Tools::getValue('receiver_phone'),
            'R_email'              => (string)Tools::getValue('receiver_email'),
            'S_company'            => (string)Tools::getValue('sender_name'),
            'S_address1'           => (string)Tools::getValue('sender_addressLine1'),
            'S_address2'           => (string)Tools::getValue('sender_addressLine2'),
            'S_postcode'           => (string)Tools::getValue('sender_postalCode'),
            'S_city'               => (string)Tools::getValue('sender_city'),
            'S_country'            => (string)Tools::getValue('sender_countryID'),
            'S_contactname'        => (string)Tools::getValue('sender_contactName'),
            'S_phone'              => (string)Tools::getValue('sender_phone'),
            'S_email'              => (string)Tools::getValue('sender_email'),
            'S_account'            => (string)Tools::getValue('sender_account'),
            'SHOP_cost'            => $order->total_paid,
            'DEF_contents'         => (string)Tools::getValue('contents'),
            'RefNum'               => (string)Tools::getValue('referenceNumber'),
            'DEF_serviceCode'      => Configuration::get('apaczka_serviceCode'),
            'DEF_orderPickupType'  => (string)Tools::getValue('orderPickupType'),
            'DEF_insurance'        => (string)Tools::getValue('insurance'),
            'DEF_weight'           => (float)Tools::getValue('shipments_weight'),
            'DEF_dim1'             => (float)Tools::getValue('shipments_dim1'),
            'DEF_dim2'             => (float)Tools::getValue('shipments_dim2'),
            'DEF_dim3'             => (float)Tools::getValue('shipments_dim3'),
            'DEF_numOdPacks'       => (string)Tools::getValue('packageCount'),
            'S_DEF_notifDelivered' => (Tools::getValue('cbo12')),
            //(Configuration::get('apaczka_sender_notif_delivered')),
            'S_DEF_notifException' => (Tools::getValue('cbo22')),
            //(Configuration::get('apaczka_sender_notif_exception')),
            'S_DEF_notifRegister'  => (Tools::getValue('cbo32')),
            //(Configuration::get('apaczka_sender_notif_register')),
            'S_DEF_notifSent'      => (Tools::getValue('cbo42')),
            //(Configuration::get('apaczka_sender_notif_sent')),
            'R_DEF_notifDelivered' => (Tools::getValue('cbo11')),
            //(Configuration::get('apaczka_receiver_notif_delivered')),
            'R_DEF_notifException' => (Tools::getValue('cbo21')),
            //(Configuration::get('apaczka_receiver_notif_exception')),
            'R_DEF_notifRegister'  => (Tools::getValue('cbo31')),
            //(Configuration::get('apaczka_receiver_notif_register')),
            'R_DEF_notifSent'      => (Tools::getValue('cbo41'))
            //(Configuration::get('apaczka_receiver_notif_sent'))
        );

        $parcel_locker_delivery_obj
            = ParcelLockerDelivery::loadByOrderId($params['id_order']);
        if (isset($parcel_locker_delivery_obj)
            && ! empty($parcel_locker_delivery_obj)
        ) {
            $smarty_variables['apaczka_parcel_locker_delivery_obj']
                = $parcel_locker_delivery_obj;
        } else {
            $smarty_variables['apaczka_parcel_locker_delivery_obj'] = null;
        }

        $this->context->smarty->assign($smarty_variables);
    }

//******************************************************************************

    /**
     * Przygotowuje zmienne Smarty do wyswietlenia formularza
     * (Pola pobierane z orderu presty i z danych configuracyjnych)
     * Dodatkowo pobiera ksiazke adresowa oraz liste krajow, sprawdza
     * czy wybrano usluge pobrania.
     *
     * @param type $params - informacje o zamowieniu
     */
    public function smartyAssignOrderForm($params)
    {
        $order    = new Order((int)$params['id_order']);
        $address
                  = new Address((int)$params['cart']->id_address_delivery);
        $customer = new Customer((int)$params['cart']->id_customer);

        $sql    = 'SELECT * FROM apaczka_address_book';
        $result = Db::getInstance()->executeS($sql);

        $sql      = 'SELECT iso_code FROM ' . _DB_PREFIX_
                    . 'country WHERE id_country = ' . $address->id_country;
        $res      = Db::getInstance()->executeS($sql);
        $iso_code = $res[0]['iso_code'];


        $cod = $order->module === 'cashondelivery' ? 1
            : 0; //Odczytuje z zamówienia czy wybrano opcje cash on delivery
        //Lista krajów.

        $countries = unserialize((string)Configuration::get('countries'));

        if ($address->phone_mobile === '') {
            $phone = $address->phone;
        } else {
            $phone = $address->phone_mobile;
        }

        //FORMULARZ DO WYSYŁANIA ZAMÓWIENIA
        $smarty_variables = array(
            'countries'            => $countries,
            'contacts'             => $result,
            'cod'                  => $cod,
            'id_order'             => $params['id_order'], //$order->id_cart,
            'token'                => Tools::getValue('token'),
            'R_company'            => $address->company,
            'R_address1'           => $address->address1,
            'R_address2'           => $address->address2,
            'R_postcode'           => $address->postcode,
            'R_city'               => $address->city,
            'R_country'            => $iso_code ? $iso_code : '',
            'R_firstname'          => $address->firstname,
            'R_lastname'           => $address->lastname,
            'R_phone'              => $phone,
            'R_email'              => $customer->email,
            'S_company'            => Configuration::get('apaczka_sender_shop_name'),
            'S_address1'           => Configuration::get('apaczka_sender_address1'),
            'S_address2'           => Configuration::get('apaczka_sender_address2'),
            'S_postcode'           => Configuration::get('apaczka_sender_postcode'),
            'S_city'               => Configuration::get('apaczka_sender_city'),
            'S_country'            => Configuration::get('apaczka_sender_country'),
            'S_contactname'        => Configuration::get('apaczka_sender_contactName'),
            'S_phone'              => Configuration::get('apaczka_sender_phone'),
            'S_email'              => Configuration::get('apaczka_sender_email'),
            'S_account'            => Configuration::get('apaczka_account'),
            'SHOP_cost'            => $order->total_paid,
            'DEF_contents'         => Configuration::get('apaczka_contents'),
            'RefNum'               => Configuration::get('apaczka_ref_text'),
            'DEF_insurance'        => Configuration::get('apaczka_insurance'),
            'DEF_orderPickupType'  => Configuration::get('apaczka_orderPickupType'),
            'DEF_serviceCode'      => Configuration::get('apaczka_serviceCode'),
            'DEF_weight'           => Configuration::get('apaczka_weight'),
            'DEF_dim1'             => Configuration::get('apaczka_dimension1'),
            'DEF_dim2'             => Configuration::get('apaczka_dimension2'),
            'DEF_dim3'             => Configuration::get('apaczka_dimension3'),
            'DEF_numOdPacks'       => 1,
            'S_DEF_notifDelivered' => Configuration::get('apaczka_sender_notif_delivered'),
            'S_DEF_notifException' => Configuration::get('apaczka_sender_notif_exception'),
            'S_DEF_notifRegister'  => Configuration::get('apaczka_sender_notif_register'),
            'S_DEF_notifSent'      => Configuration::get('apaczka_sender_notif_sent'),
            'R_DEF_notifDelivered' => Configuration::get('apaczka_receiver_notif_delivered'),
            'R_DEF_notifException' => Configuration::get('apaczka_receiver_notif_exception'),
            'R_DEF_notifRegister'  => Configuration::get('apaczka_receiver_notif_register'),
            'R_DEF_notifSent'      => Configuration::get('apaczka_receiver_notif_sent')
        );

        /**
         * Add parcel locker object if client chosen one.
         */
        $parcelLockerDeliveryObj
            = ParcelLockerDelivery::loadByOrderId($params['id_order']);
        if (isset($parcelLockerDeliveryObj)
            && ! empty($parcelLockerDeliveryObj)
        ) {
            $smarty_variables['apaczka_parcel_locker_delivery_obj']
                = $parcelLockerDeliveryObj;
        } else {
            $smarty_variables['apaczka_parcel_locker_delivery_obj'] = null;
        }

        $this->context->smarty->assign($smarty_variables);
    }

//******************************************************************************

    /**
     * Funkcja pobiera dane Wysylajacego z formularza i zapisuje je do ksiazki
     * adresowej. Jesli takie dane juz istnieja to nie duplikuje ich.
     */
    public function addToContacts()
    {
        $name        = (string)Tools::getValue('sender_name');
        $addresLine1 = (string)Tools::getValue('sender_addressLine1');
        $addresLine2 = (string)Tools::getValue('sender_addressLine2');
        $postalCode  = (string)Tools::getValue('sender_postalCode');
        $city        = (string)Tools::getValue('sender_city');
        $countryID   = '0';
        $contactName = (string)Tools::getValue('sender_contactName');
        $phone       = (string)Tools::getValue('sender_phone');
        $email       = (string)Tools::getValue('sender_email');
        $account     = (string)Tools::getValue('sender_account');

        $sql
            = "INSERT IGNORE INTO `apaczka_address_book` (`nazwa`,`adres`,
            `adres2`, `kod_pocztowy`, `miasto`,`osoba_kontaktowa`, `id_kraju`,
            `telefon`, `email`, `konto_pobraniowe`)
                VALUES ('$name','$addresLine1','$addresLine2','$postalCode',
                '$city', '$contactName', $countryID, '$phone', '$email',
                '$account');";
        Db::getInstance()->execute($sql);
    }

//******************************************************************************

    /**
     * Pobranie listu przewozowego z API Apaczka.pl.
     * Najpierw znajac numer zlecenia z Presty pobieramy z tabeli
     * 'apaczka_orders' id_zamowienia_apaczkowego i na bazie tego pobieramy
     * dokument przewozowy.
     */
    public function sendWaybill()
    {
        if (Tools::getValue('id_order') != null
            && Tools::getValue('id_order') != ''
        ) {
            $sql
                         = 'SELECT id_order_apaczka FROM apaczka_orders WHERE id_order_presta = '
                           . Tools::getValue('id_order');
            $result      = Db::getInstance()->executeS($sql);
            $isOrderSent = (int)$result[0]['id_order_apaczka'];
        } else {
            print_r("PUSTY id_order", 1);
        }

        $apaczka = new ApaczkaAPI(
            (string)Configuration::get('apaczka_login'),
            (string)Configuration::get('apaczka_password'),
            (string)Configuration::get('apaczka_apikey')
        );
        if ((Configuration::get('apaczka_test'))) {
            $apaczka->setTestMode();
        } else {
            $apaczka->setProductionMode();
        }
        ob_end_clean();     //bez tego sa problemy przy otwieraniu PDFa (smieci z bufora)
        $result      = $apaczka->getWaybillDocument($isOrderSent);
        $pdf_decoded = $result->return->waybillDocument;
        $name        = 'ApaczkaWayBill' . Tools::getValue('id_order') . '.pdf';
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Disposition: attachment; filename=' . $name);

        header('Connection: close');
        die($pdf_decoded);
    }

    //******************************************************************************
    /**
     * Funkcja pobiera z formularza DisplayAdminOrder.tpl wszystkie pola
     * niezbedne do wyslania zamowienia i opakowuje je w klase ApaczkaOrder,
     * ktora zwraca.
     *
     * @return \ApaczkaOrder
     */
    //kex_default_accounts
    public function prepareOrderToApi()
    {

        $order_apacz = new ApaczkaOrder();

        $order_apacz->notificationDelivered
            = $order_apacz->createNotification(
                (Tools::getValue('cbo11')),
                false,
                (Tools::getValue('cbo12')),
                false
            );
        $order_apacz->notificationException
            = $order_apacz->createNotification(
                (Tools::getValue('cbo21')),
                false,
                (Tools::getValue('cbo22')),
                false
            );
        $order_apacz->notificationNew
            = $order_apacz->createNotification(
                (Tools::getValue('cbo31')),
                false,
                (Tools::getValue('cbo32')),
                false
            );
        $order_apacz->notificationSent
            = $order_apacz->createNotification(
                (Tools::getValue('cbo41')),
                false,
                (Tools::getValue('cbo42')),
                false
            );

        $order_apacz->setServiceCode((string)Tools::getValue('serviceCode'));
        $order_apacz->referenceNumber
                                 = (string)Tools::getValue('referenceNumber');
        $order_apacz->isDomestic = (((string)Tools::getValue('sender_countryID')
                                     == '0')
                                    && ((string)Tools::getValue('receiver_countryId')
                                        == '0')) ? true : false;
        $order_apacz->contents   = (string)Tools::getValue('contents');

        $order_apacz->setSenderAddress(
            (string)Tools::getValue('sender_name'),
            (string)Tools::getValue('sender_contactName'),
            (string)Tools::getValue('sender_addressLine1'),
            (string)Tools::getValue('sender_addressLine2'),
            (string)Tools::getValue('sender_city'),
            (string)Tools::getValue('sender_countryID'),
            (string)Tools::getValue('sender_postalCode'),
            '',
            (string)Tools::getValue('sender_email'),
            (string)Tools::getValue('sender_phone')
        );
        $order_apacz->setReceiverAddress(
            (string)Tools::getValue('receiver_name'),
            (string)Tools::getValue('receiver_contactName'),
            (string)Tools::getValue('receiver_addressLine1'),
            (string)Tools::getValue('receiver_addressLine2'),
            (string)Tools::getValue('receiver_city'),
            (string)Tools::getValue('receiver_countryId'),
            (string)Tools::getValue('receiver_postalCode'),
            '',
            (string)Tools::getValue('receiver_email'),
            (string)Tools::getValue('receiver_phone')
        );
        //***************   Czy pobranie           ********
        if ((string)Tools::getValue('cod') == '1') {
            $order_apacz->setPobranie(
                (string)Tools::getValue('sender_account'),
                (float)(100 * Tools::getValue('codAmount'))
            );
        }
        //***************   Czy paczka czy list    ********
        if ((string)Tools::getValue('shipmentTypeCode') === 'PACZ') {
            $order_shipment = new ApaczkaOrderShipment(
                (string)Tools::getValue('shipmentTypeCode'),
                (float)Tools::getValue('shipments_dim1'),
                (float)Tools::getValue('shipments_dim2'),
                (float)Tools::getValue('shipments_dim3'),
                (float)Tools::getValue('shipments_weight')
            );
        } else {
            $order_shipment
                = new ApaczkaOrderShipment(
                    (string)Tools::getValue('shipmentTypeCode'),
                    10,
                    10,
                    10,
                    1
                );
        }
        //***************   Czy ubezpieczenie      ********
        if ((string)Tools::getValue('insurance') == '1') {
            $order_shipment->setShipmentValue((float)(100
                                                      * Tools::getValue('shipments_shipmentValue')));
        }
        //***************   Czy przesyłka nietypowa (checkbox noStd klikniety)      ********
        if (Tools::getValue('noStd') != 0
            && (string)Tools::getValue('shipmentTypeCode') === 'PACZ'
        ) {
            $order_shipment->addOrderOption('PRZES_NIETYP');
        }
        if (Tools::getValue('bigPack') != 0
            && (string)Tools::getValue('shipmentTypeCode') === 'PACZ'
        ) {
            $order_shipment->addOrderOption('DUZA_PACZKA');
        }

        if (Tools::getValue('orderPickupType') === 'BOX_MACHINE') {
            $sender_parcel_locker_code
                = Tools::getValue('apaczka-chosenSenderParcelLockerName');
            if (isset($sender_parcel_locker_code)
                && ! empty($sender_parcel_locker_code)
            ) {
                $order_shipment->addOrderOption($sender_parcel_locker_code);
            }
        }

        if (Tools::getValue('serviceCode') === 'PACZKOMAT') {
            $receiver_parcel_locker_code
                = Tools::getValue('apaczka-chosenReceiverParcelLockerName');
            if (isset($receiver_parcel_locker_code)
                && ! empty($receiver_parcel_locker_code)
            ) {
                $order_shipment->addOrderOption($receiver_parcel_locker_code);
            }
        }


        $ilePaczek = (int)Tools::getValue('packageCount');
        if (! ($ilePaczek >= 1 && $ilePaczek <= 20)) {
            $ilePaczek = 1;
        }
        for ($i = 0; $i < $ilePaczek; $i++) {
            $order_apacz->addShipment($order_shipment);
        }
        //$order_apacz->addShipment($order_shipment);
        $order_apacz->setPickup(
            (string)Tools::getValue('orderPickupType'),
            (string)Tools::getValue('pickupTimeFrom'),
            (string)Tools::getValue('pickupTimeTo'),
            (string)Tools::getValue('pickupDate')
        );

        return $order_apacz;
    }


//******************************************************************************

    /**
     * Przygotowuje zmienne smarty dla formularza AdminOrderSent.tpl
     * (wyswietlany gdy zamowienie wysylki powiodlo sie)
     *
     * @param type $order - dane zamowienia
     */
    public function assignSmartyToSentOrder($id_order)
    {
        $smarty_variables = array();

        $parcelLockerDeliveryObj
            = ParcelLockerDelivery::loadByOrderId($id_order);
        if (isset($parcelLockerDeliveryObj)
            && ! empty($parcelLockerDeliveryObj)
        ) {
            $smarty_variables['apaczka_parcel_locker_delivery_obj']
                = $parcelLockerDeliveryObj;
        } else {
            $smarty_variables['apaczka_parcel_locker_delivery_obj'] = null;
        }

        $sql
                          = 'SELECT order_number_apaczka FROM apaczka_orders WHERE id_order_presta = '
                            . $id_order;
        $id_apaczka       = Db::getInstance()->executeS($sql);
        $smarty_variables = array(
            'id_order'             => $id_order,
            'order_number_apaczka' => $id_apaczka[0]["order_number_apaczka"],
            'token'                => Tools::getValue('token'),
            'isTest'               => (Configuration::get('apaczka_test'))
        );

        $this->context->smarty->assign(
            $smarty_variables
        );
    }

    //******************************************************************************

    /**
     * Funkcja sprawdza czy to zamowienie bylo juz wyslane przez Apaczka.pl czy nie.
     * Id_order pobierane jest ze zmiennej$_GET.
     * Dzieki wowolaniu isThisOrderSent funkcja hookdisplayAdminOrder sprawdza
     * przy jej pomocy czy wyswietlic formularz do wysylki czy tylko informacje
     * o juz wyslanej paczce.
     *
     * @return true or false. True gdy zamówienie zostąło wysłane
     */
    public function isThisOrderSent()
    {
        $isOrderSent
            = 0;               //Potrzebne do sprawezenia czy dane zamówienie zostało już wysłane czy nie
        if (Tools::getValue('id_order') != null
            && Tools::getValue('id_order') != ''
        ) {
            $sql
                         = 'SELECT COUNT(*) FROM apaczka_orders WHERE id_order_presta = '
                           . Tools::getValue('id_order');
            $result      = Db::getInstance()->executeS($sql);
            $isOrderSent = (int)$result[0]['COUNT(*)'];
        }

        return ($isOrderSent == 0) ? false : true;
    }

//******************************************************************************

    /**
     * Funkcja sprawdza czy podany Login, Hasło i apikey sa poprawne.
     *
     * @return true lub false.
     */
    public function validateAuth()
    {
        $apaczka = new ApaczkaAPI(
            (string)Configuration::get('apaczka_login'),
            (string)Configuration::get('apaczka_password'),
            (string)Configuration::get('apaczka_apikey')
        );
        if ((Configuration::get('apaczka_test'))) {
            $apaczka->setTestMode();
        } else {
            $apaczka->setProductionMode();
        }

        $resp = $apaczka->validateAuthData();

        return $resp->return->isValid ? true : false;
    }

//******************************************************************************

    /**
     * Funkcja wywolywana jest kiedy w menu 'Orders' wejdziemy w formularz do
     * wysylania zamowień i zasubmitujemy je guzikiem 'wyslij z apaczka'.
     * Zadaniem tej funkcji jest sprawdzenie polaczenia z Apaczka.pl i jesli
     * to sie uda to wyslanie danych przez API.
     * W razie niepowodzenia przygotowuje komunikaty do wyswietlenia
     * uzytkownikowi.
     *
     * @return kontener z polem 'status' oraz 'out'
     *      status - zmienna przyjmuje wartosc:
     *          0 gdy dane przejda walidacje wstepna i zostana poprawnie wyslane.
     *          1 gdy login, haslo lub klucz API sa bledne.
     *          2 gdy nastapi blad przy wysylaniu (blad danych lub blad SOAP.
     *      out - opis tekstowy błędu w przypadku statusu różnego od 0, jak nie to ''
     */
    public function prepareAndSendOrder()
    {
        $ret = 0;
        $out = '';

        if (! $this->validateAuth()) {      //czy login i haslo do API sa poprawne?
            $ret = 1;
            $out = 'Błąd przy logowaniu do serwisu apaczka. Niemozliwe jest
                 wysyłanie paczek. Sprawdz czy Twoje dane konfiguracyjne
                 (Login, Haslo, kluczAPI lub wybór serwera(testowy/nie)) są
                 poprawne.';
        } else {
            $apaczka
                = new ApaczkaAPI(
                    (string)Configuration::get('apaczka_login'),
                    (string)Configuration::get('apaczka_password'),
                    (string)Configuration::get('apaczka_apikey')
                );
            if ((Configuration::get('apaczka_test'))) {
                $apaczka->setTestMode();
            } else {
                $apaczka->setProductionMode();
            }

            //OPAKOWANIE ZAMÓWIENIA W KLASE ORDER (GOTOWE DO API)
            $order_apacz
                = $this->prepareOrderToApi();

            $resp = $apaczka->placeOrder($order_apacz); //Jesli sie udalo

            if ($resp !== false && $resp->return->order) {  //Wyslanie do API
                $orderIdApaczka
                                    = $resp->return->order->id; //Udalo sie
                $orderNumberApaczka = $resp->return->order->orderNumber;
                $orderIdPresta      = Tools::getValue('id_order');


                $sql
                    = "INSERT IGNORE INTO `apaczka_orders` (`id_order_presta`,
                    `id_order_apaczka`, `order_number_apaczka`) VALUES
                    ('$orderIdPresta','$orderIdApaczka','$orderNumberApaczka');";
                Db::getInstance()->execute($sql);
            } else { //Jesli nie udalo sie zlozyc zamówienia przez API apaczka.pl
                // - dane niepoprawne lub inny problem
                $apiError = '';
                if (isset($resp->return->result->messages->Message->description)) {
                    $apiError = $resp->return->result->messages->Message->description;
                }
                $ret = 2;
                $out = $apiError;
            }
        }

        return array(
            "status" => $ret,
            "out"    => $out,
        );
    }

//******************************************************************************

    /**
     * Funkcja wywolywana przez Preste w momencie wejscia w zakładkę Orders i
     * wybranie ktoregos z zamowien. W zaleznosci od tego czy dane zamowienie
     * bylo juz wyslane///nie bylo wyslane lub nie udalo sie wyslac
     * wyswietlany jest odpowienio formularz displayAdminOrderSent.tpl,
     * displayAdminOrder.tpl.
     * Jesli zamowienie bylo wyslane poprawnie i
     *
     * @param type $params
     *
     * @return type
     */
    public function hookdisplayAdminOrder($params)
    {
        $output = '';

        file_put_contents(
            'XOLTResult.log',
            "[" . date('c') . "]\n" . "----------hookDisplayAdminOrder link:\n"
            . json_encode($_GET) . " \n",
            FILE_APPEND
        );

        if (Tools::isSubmit('sendwaybill')) {
            $this->sendWaybill();

            return $this->display(
                __FILE__,
                'views/templates/hook/displayAdminOrderSent.tpl'
            );
        } elseif (Tools::isSubmit('sendsubmit')) {
            $result = $this->prepareAndSendOrder();

            if (! $result['status']) {
                $this->assignSmartyToSentOrder($params['id_order']);

                return $this->display(
                    __FILE__,
                    'views/templates/hook/displayAdminOrderSent.tpl'
                );
            } else {
                $this->smartyAssignOrderFormError($params);
                $output = $this->displayError(
                    'Nie udało sie złożyć zamówienia w serwisie apaczka.pl. 
                    Błąd SOAP: ' . $this->l($result['out']) .
                    ' (*) Pamiętaj o ponownym ustawieniu
                    godzin odbioru oraz typie przesyłki.'
                );


                return $output .
                       $this->display(
                           __FILE__,
                           'views/templates/hook/displayAdminOrder.tpl'
                       );
            }
        } elseif ($this->isThisOrderSent()) {
            $this->assignSmartyToSentOrder($params['id_order']);

            return $this->display(
                __FILE__,
                'views/templates/hook/displayAdminOrderSent.tpl'
            );
        } else {
            if (Tools::isSubmit('addContact')) {
                $this->addToContacts();
            }

            $this->smartyAssignOrderForm($params);
            if (! $this->validateAuth()) {
                $output
                    = $this->displayError($this->l('Moduł nie jest poprawnie skonfigurowany.
                    Proszę wejść w zakładkę Modules->apaczkashipping->configure
                    i uzupełnić wymagane pola.'));
            }

            return $output .
                   $this->display(
                       __FILE__,
                       'views/templates/hook/displayAdminOrder.tpl'
                   );
        }
    }

//******************************************************************************

    /**
     * Funkcja wywolywana przez preste w momencie wejscia w zakladke Modules a
     * potem wybranie Buttona 'Configure'.
     * Celem funkcji jest obsluga zasubmitowanego formularza konfiguracayjnego
     * i zapisanie poprawnych danych do zmiennych konfiguracyjnych.
     * Dodatkowo testuje polaczenie z serwerem Apaczka.pl
     *
     * @return formularz do wyswietlenia rzez funkcje displayForm oraz informacje
     * o poprawnosci lub nie danych oraz czy udalo sie polaczyc z serwerem.
     */
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $name_API   = (string)Tools::getValue('ApaczkaNameAPI');
            $passwd_API = (string)Tools::getValue('ApaczkaPasswordAPI');
            $key_API    = (string)Tools::getValue('ApaczkaKeyAPI');
            $test_API   = (Tools::getValue('ApaczkaIsTest'));

            $shopName     = (string)Tools::getValue('ApaczkaSenderName');
            $shopAddr1    = (string)Tools::getValue('ApaczkaSenderAddress1');
            $shopAddr2    = (string)Tools::getValue('ApaczkaSenderAddress2');
            $shopPostCode = (string)Tools::getValue('ApaczkaSenderPostalCode');
            $shopCity     = (string)Tools::getValue('ApaczkaSenderCity');
            $shopCountry  = (string)Tools::getValue('ApaczkaSenderCountry');
            $shopContact  = (string)Tools::getValue('ApaczkaSenderContactName');
            $shopPhone    = (string)Tools::getValue('ApaczkaSenderPhone');
            $shopEmail    = (string)Tools::getValue('ApaczkaSenderEmail');
            $shopAccount  = (string)Tools::getValue('ApaczkaSenderAccount');

            $shopRefText     = (string)Tools::getValue('ApaczkaRefText');
            $shopPackContent = (string)Tools::getValue('ApaczkaContent');
            $shopPackWeight  = (int)Tools::getValue('ApaczkaWeight');
            $shopPackDim1    = (string)Tools::getValue('ApaczkaDim1');
            $shopPackDim2    = (string)Tools::getValue('ApaczkaDim2');
            $shopPackDim3    = (string)Tools::getValue('ApaczkaDim3');

            $notifSendDel  = (string)Tools::getValue('ApaczkaSenderDelivered');
            $notifSendExc  = (string)Tools::getValue('ApaczkaSenderException');
            $notifSendReg  = (string)Tools::getValue('ApaczkaSenderRegister');
            $notifSendSent = (string)Tools::getValue('ApaczkaSenderSent');
            $notifRecDel
                           = (string)Tools::getValue('ApaczkaReceiverDelivered');
            $notifRecExc
                           = (string)Tools::getValue('ApaczkaReceiverException');
            $notifRecReg   = (string)Tools::getValue('ApaczkaReceiverRegister');
            $notifRecSent  = (string)Tools::getValue('ApaczkaReceiverSent');

            $defInsurance   = (string)Tools::getValue('ApaczkaInsurance');
            $defOrderPickupType
                            = (string)Tools::getValue('ApaczkaOrderPickupType');
            $defServiceCode = (string)Tools::getValue('ApaczkaServiceCode');

            //Sprawdzenie czy Login, Haslo, KluczAPI sa wlasciwe
            $apaczka = new ApaczkaAPI($name_API, $passwd_API, $key_API);
            if ($test_API) {
                $apaczka->setTestMode();
            } else {
                $apaczka->setProductionMode();
            }
            $resp = $apaczka->validateAuthData();

            //jesli dane logowania sa dobre to zapisujemy do zmiennych konfiguracyjnych
            if (! ($resp->return->isValid)) {
                $output .= $this->displayError($this->l('Nieudało się połączyć z serwerem.
            <br>Sprawdz poprawność Loginu, Hasła, kluczaAPI oraz wybór serwera(testowy/nie)'));
            } else {
                Configuration::updateValue('apaczka_login', $name_API);
                Configuration::updateValue('apaczka_password', $passwd_API);
                Configuration::updateValue('apaczka_apikey', $key_API);
                Configuration::updateValue('apaczka_test', (($test_API)));
            }                                   //jesli I blok danych sklepu dobry to zapisujemy
            if (! $shopName || empty($shopName) || ! $shopAddr1
                || empty($shopAddr1)
                || ! $shopPostCode
                || empty($shopPostCode)
                || ! $shopCity
                || empty($shopCity)
            ) {
                $output .= $this->displayError($this->l('Nie uzupełniono danych nadawcy!<br>'));
            } else {
                Configuration::updateValue(
                    'apaczka_sender_shop_name',
                    $shopName
                );
                Configuration::updateValue(
                    'apaczka_sender_address1',
                    $shopAddr1
                );
                Configuration::updateValue(
                    'apaczka_sender_address2',
                    $shopAddr2
                );
                Configuration::updateValue(
                    'apaczka_sender_postcode',
                    $shopPostCode
                );
                Configuration::updateValue(
                    'apaczka_sender_city',
                    $shopCity
                );
            }//jesli II blok danych sklepu dobry to zapisujemy
            if (! $shopContact || empty($shopContact) || ! $shopPhone
                || empty($shopPhone)
                || ! $shopEmail
                || empty($shopEmail)
                || ! $shopAccount
                || empty($shopAccount)
            ) {
                $output .= $this->displayError($this->l('Nie uzupełniono danych nadawcy!<br>'));
            } else {
                Configuration::updateValue(
                    'apaczka_sender_contactName',
                    $shopContact
                );
                Configuration::updateValue(
                    'apaczka_sender_phone',
                    $shopPhone
                );
                Configuration::updateValue(
                    'apaczka_sender_email',
                    $shopEmail
                );
                Configuration::updateValue(
                    'apaczka_account',
                    $shopAccount
                );
            }//jesli III blok danych sklepu dobry to zapisujemy
            if (empty($shopPackContent) || ! $shopPackWeight > 0
                || empty($shopPackWeight)
                || ! $shopPackDim1 > 0
                || empty($shopPackDim1)
                || ! $shopPackDim2 > 0
                || empty($shopPackDim2)
                || ! $shopPackDim3 > 0
                || empty($shopPackDim3)
            ) {
                $shopPackContentMessage = 'Nie uzupełniono
                ustawień domyślnych paczki oraz fakturowania!<br>';
                $output .= $this->displayError($this->l($shopPackContentMessage));
            } else {
                Configuration::updateValue(
                    'apaczka_ref_text',
                    $shopRefText
                );
                Configuration::updateValue(
                    'apaczka_contents',
                    $shopPackContent
                );
                Configuration::updateValue(
                    'apaczka_weight',
                    $shopPackWeight
                );
                Configuration::updateValue(
                    'apaczka_dimension1',
                    $shopPackDim1
                );
                Configuration::updateValue(
                    'apaczka_dimension2',
                    $shopPackDim2
                );
                Configuration::updateValue(
                    'apaczka_dimension3',
                    $shopPackDim3
                );
            } //jesli kraj istnieje to zapisujemy (bez wspisanych danych
            // logowania lista krajow nie wyswietla sie
            if ($shopCountry == '') {
                $shopCountryMessage = 'Nie uzupełniono nazwy
                    kraju, prosze wybrac jeden z listy
                    (do wyświetlenia listy krajów niezbędne są login, hasło
                    oraz klucz api) <br>';
                $output .= $this->displayError($this->l($shopCountryMessage));
            } else {
                Configuration::updateValue(
                    'apaczka_sender_country',
                    $shopCountry
                );
            }
            Configuration::updateValue(
                'apaczka_sender_notif_delivered',
                $notifSendDel
            );
            Configuration::updateValue(
                'apaczka_sender_notif_exception',
                $notifSendExc
            );
            Configuration::updateValue(
                'apaczka_sender_notif_register',
                $notifSendReg
            );
            Configuration::updateValue(
                'apaczka_sender_notif_sent',
                $notifSendSent
            );
            Configuration::updateValue(
                'apaczka_receiver_notif_delivered',
                $notifRecDel
            );
            Configuration::updateValue(
                'apaczka_receiver_notif_exception',
                $notifRecExc
            );
            Configuration::updateValue(
                'apaczka_receiver_notif_register',
                $notifRecReg
            );
            Configuration::updateValue(
                'apaczka_receiver_notif_sent',
                $notifRecSent
            );

            Configuration::updateValue(
                'apaczka_insurance',
                $defInsurance
            );
            Configuration::updateValue(
                'apaczka_orderPickupType',
                $defOrderPickupType
            );
            Configuration::updateValue(
                'apaczka_serviceCode',
                $defServiceCode
            );

            if ($output == '') {
                $output .= $this->displayConfirmation(
                    $this->l('Ustawienia zachowane!')
                );
                //JESLI KONFIGURACJA SIE POWIODLA DO LADUJEMY DO ZMIENNYCH
                // KONFIGURACYJNYCH LISTE KRAJOW (zeby jej ciagle nie pobierac)
                $apaczka = new ApaczkaAPI($name_API, $passwd_API, $key_API);
                if ((Configuration::get('apaczka_test'))) {
                    $apaczka->setTestMode();
                } else {
                    $apaczka->setProductionMode();
                }
                $resp  = $apaczka->getCountries();
                $count = $resp->return->countries->Country;
                //************* COUNTRIES *****************
                Configuration::updateValue('countries', serialize($count));
            }
        }

        return $output . $this->displayForm();
    }

//******************************************************************************

    /**
     * Przygotowuje zmienne smarty do wyswietlenia formularza konfiguracyjnego,
     * nastepnie Includuje zapisany na sposob zrozumialy na helpera presty plik
     * views/helperFormViewConfig.php, ktory generuje formularz.
     *
     * @return wyswietlony formularz.
     */
    public function displayForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $apaczka = new ApaczkaAPI(
            (string)Configuration::get('apaczka_login'),
            (string)Configuration::get('apaczka_password'),
            (string)Configuration::get('apaczka_apikey')
        );
        if ((Configuration::get('apaczka_test'))) {
            $apaczka->setTestMode();
        } else {
            $apaczka->setProductionMode();
        }
        $resp = $apaczka->getCountries();
        if (isset($resp->return->countries->Country)) {
            $countries
                = $resp->return->countries->Country;        //TO JEST UZYWANE w views/helperFormViewConfig.php !!!
        } else {
            $countries = array(
                array(                            //TO JEST UZYWANE w views/helperFormViewConfig.php !!!
                    "code" => "PL",
                    "id"   => 0,
                    "name" => "Polska"
                )
            );
        }

        $fields_form = $this->getHelperFormConfig($countries);

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module          = $this;
        $helper->name_controller = $this->name;
        $helper->token           = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex    = AdminController::$currentIndex
                                   . '&configure=' . $this->name;

        // Language
        $helper->default_form_language    = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title         = $this->displayName;
        $helper->show_toolbar  = true;
        $helper->toolbar_scroll
                               = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;

        // Load current value
        $helper->fields_value['ApaczkaNameAPI']
            = Configuration::get('apaczka_login');
        $helper->fields_value['ApaczkaPasswordAPI']
            = Configuration::get('apaczka_password');
        $helper->fields_value['ApaczkaKeyAPI']
            = Configuration::get('apaczka_apikey');
        $helper->fields_value['ApaczkaIsTest']
            = Configuration::get('apaczka_test');
        //********************
        $helper->fields_value['ApaczkaSenderName']
            = Configuration::get('apaczka_sender_shop_name');
        $helper->fields_value['ApaczkaSenderAddress1']
            = Configuration::get('apaczka_sender_address1');
        $helper->fields_value['ApaczkaSenderAddress2']
            = Configuration::get('apaczka_sender_address2');
        $helper->fields_value['ApaczkaSenderPostalCode']
            = Configuration::get('apaczka_sender_postcode');
        $helper->fields_value['ApaczkaSenderCity']
            = Configuration::get('apaczka_sender_city');
        $helper->fields_value['ApaczkaSenderCountry']
            = Configuration::get('apaczka_sender_country');
        $helper->fields_value['ApaczkaSenderContactName']
            = Configuration::get('apaczka_sender_contactName');
        $helper->fields_value['ApaczkaSenderPhone']
            = Configuration::get('apaczka_sender_phone');
        $helper->fields_value['ApaczkaSenderEmail']
            = Configuration::get('apaczka_sender_email');
        $helper->fields_value['ApaczkaSenderAccount']
            = Configuration::get('apaczka_account');
        //********************
        $helper->fields_value['ApaczkaContent']
            = Configuration::get('apaczka_contents');
        $helper->fields_value['ApaczkaRefText']
            = Configuration::get('apaczka_ref_text');
        $helper->fields_value['ApaczkaWeight']
            = Configuration::get('apaczka_weight');
        $helper->fields_value['ApaczkaDim1']
            = Configuration::get('apaczka_dimension1');
        $helper->fields_value['ApaczkaDim2']
            = Configuration::get('apaczka_dimension2');
        $helper->fields_value['ApaczkaDim3']
            = Configuration::get('apaczka_dimension3');
        //********************
        $helper->fields_value['ApaczkaSenderDelivered']
            = Configuration::get('apaczka_sender_notif_delivered');
        $helper->fields_value['ApaczkaSenderException']
            = Configuration::get('apaczka_sender_notif_exception');
        $helper->fields_value['ApaczkaSenderRegister']
            = Configuration::get('apaczka_sender_notif_register');
        $helper->fields_value['ApaczkaSenderSent']
            = Configuration::get('apaczka_sender_notif_sent');
        $helper->fields_value['ApaczkaReceiverDelivered']
            = Configuration::get('apaczka_receiver_notif_delivered');
        $helper->fields_value['ApaczkaReceiverException']
            = Configuration::get('apaczka_receiver_notif_exception');
        $helper->fields_value['ApaczkaReceiverRegister']
            = Configuration::get('apaczka_receiver_notif_register');
        $helper->fields_value['ApaczkaReceiverSent']
            = Configuration::get('apaczka_receiver_notif_sent');


        $helper->fields_value['ApaczkaInsurance']
            = Configuration::get('apaczka_insurance');
        $helper->fields_value['ApaczkaOrderPickupType']
            = Configuration::get('apaczka_orderPickupType');
        $helper->fields_value['ApaczkaServiceCode']
            = Configuration::get('apaczka_serviceCode');

        return $helper->generateForm($fields_form);
    }

    private function getHelperFormConfig($countries)
    {
        $fields_form            = array();
        $fields_form[0]['form'] = $this->getApiConfigFormPart();
        $fields_form[1]['form'] = $this->getDefaultConfigFormPart();
        $fields_form[2]['form'] = $this->getAdressesConfigFormPart($countries);
        $fields_form[3]['form'] = $this->getPackageAndInvoiceConfigFormPart();
        $fields_form[4]['form'] = $this->getNotificationsConfigFormPart();

        return $fields_form;
    }

    protected function getApiConfigFormPart()
    {
        return array(
            'legend' => array(
                'title' => $this->l(
                    'Ustawienia polączenia z serwisem apaczka',
                    'apaczkashipping'
                ),
                'image' => "../modules/apaczkashipping/views/img/logoMed.png"
            ),
            'input'  => array(
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Nazwa użytkownika API'),
                    'name'     => 'ApaczkaNameAPI',
                    'size'     => 20,
                    'required' => true
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Hasło API'),
                    'name'     => 'ApaczkaPasswordAPI',
                    'size'     => 20,
                    'required' => true
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Klucz API'),
                    'name'     => 'ApaczkaKeyAPI',
                    'size'     => 20,
                    'required' => true
                )
                /* ,array(
                        'type' => 'radio',
                        'label' => $this->l('Używaj systemu testowego do składania zamówień'),
                        'name' => 'ApaczkaIsTest',
                        'is_bool' => true,
                        'class' => 'radio-inline',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Tak')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Nie')
                            )
                        )
                    )*/
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
    }

    protected function getDefaultConfigFormPart()
    {
        return array(
            'legend' => array(
                'title' => $this->l('Sposoby przesyłki'),
            ),
            'input'  => array(
                array(
                    'type'     => 'select',
                    'label'    => $this->l(
                        'Domyślny kod usługi: ',
                        'apaczkashipping'
                    ),
                    'name'     => 'ApaczkaServiceCode',
                    'required' => true,
                    'options'  => array(
                        'query' => array(
                            array(
                                'id_option' => 'UPS Standard',
                                'name'      => 'UPS Standard'
                            ),
                            array(
                                'id_option' => 'UPS Zagranica',
                                'name'      => 'UPS Zagranica'
                            ),
                            array(
                                'id_option' => 'PACZKOMAT',
                                'name'      => 'InPost Paczkomaty'
                            ),
                            array(
                                'id_option' => 'APACZKA_DE',
                                'name'      => 'Apaczka Niemcy'
                            ),
                            array(
                                'id_option' => 'INPOST',
                                'name'      => 'InPost Kurier'
                            ),
                            array(
                                'id_option' => 'UPS Express Saver KRAJ',
                                'name'      => 'UPS Express Saver KRAJ'
                            ),
                            array(
                                'id_option' => 'UPS Express Saver ZAGR',
                                'name'      => 'UPS Express Saver ZAGR'
                            ),
                            array(
                                'id_option' => 'DHL Standard',
                                'name'      => 'DHL Standard'
                            ),
                            array(
                                'id_option' => 'DHL Express 12',
                                'name'      => 'DHL Express 12'
                            ),
                            array(
                                'id_option' => 'DPD Classic Foreign',
                                'name'      => 'DPD Classic Foreign'
                            ),
                            array(
                                'id_option' => 'TNT Economy Express',
                                'name'      => 'TNT Economy Express'
                            ),
                            array(
                                'id_option' => 'Pocztex 24',
                                'name'      => 'Pocztex 24'
                            ),
                            array(
                                'id_option' => 'K-EX Express',
                                'name'      => 'K-EX Express'
                            ),
                            array(
                                'id_option' => 'FEDEX',
                                'name'      => 'FEDEX'
                            ),
                            array(
                                'id_option' => 'DPD Classic',
                                'name'      => 'DPD Classic'
                            )
                        ),
                        'id'    => 'id_option',
                        'name'  => 'name'
                    )
                ),
                array(
                    'type'     => 'select',
                    'label'    => $this->l('Domyślne ubezpieczenie: '),
                    'name'     => 'ApaczkaInsurance',
                    'required' => true,
                    'options'  => array(
                        'query' => array(
                            array(
                                'id_option' => 'insurOn',
                                'name'      => 'Tak'
                            ),
                            array(
                                'id_option' => 'insurOff',
                                'name'      => 'Nie'
                            ),
                        ),
                        'id'    => 'id_option',
                        'name'  => 'name'
                    )
                ),

                array(
                    'type'     => 'select',
                    'label'    => $this->l('Domyślny sposób nadania: '),
                    'name'     => 'ApaczkaOrderPickupType',
                    'required' => true,
                    'options'  => array(
                        'query' => array(
                            array(
                                'id_option' => 'SELF',
                                'name'      => 'Samodzielne dostarczenie do kuriera'
                            ),
                            array(
                                'id_option' => 'COURIER',
                                'name'      => 'Zamówienie dobioru przesyłek'
                            ),
                            array(
                                'id_option' => 'BOX_MACHINE',
                                'name'      => $this->l('InPost Paczkomaty'),
                            ),
                        ),
                        'id'    => 'id_option',
                        'name'  => 'name'
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
    }

    protected function getAdressesConfigFormPart($countries)
    {
        return array(
            'legend' => array(
                'title' => $this->l('Dane nadawcy (sklepu)'),
            ),
            'input'  => array(
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Nazwa'),
                    'name'      => 'ApaczkaSenderName',
                    'size'      => 20,
                    'maxlength' => 35,
                    'required'  => true
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Adres'),
                    'name'      => 'ApaczkaSenderAddress1',
                    'size'      => 20,
                    'maxlength' => 35,
                    'required'  => true
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Adres cd'),
                    'name'      => 'ApaczkaSenderAddress2',
                    'size'      => 20,
                    'maxlength' => 35,
                    'required'  => false
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Kod pocztowy'),
                    'name'      => 'ApaczkaSenderPostalCode',
                    'size'      => 20,
                    'maxlength' => 6,
                    'required'  => true
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Miasto'),
                    'name'      => 'ApaczkaSenderCity',
                    'size'      => 20,
                    'maxlength' => 35,
                    'required'  => true
                ),
                array(
                    'type'     => 'select',
                    'label'    => $this->l('Kraj'),
                    'desc'     => $this->l('Pamiętaj że przesyłki zagraniczne są obsługiwane tylko UPS zagranica'),
                    // A help text, displayed right next to the <select> tag.
                    'name'     => 'ApaczkaSenderCountry',
                    'required' => true,
                    'options'  => array(
                        'query' => $countries,
                        'id'    => 'id',
                        'name'  => 'name'
                    )
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Osoba kontaktowa'),
                    'name'     => 'ApaczkaSenderContactName',
                    'size'     => 20,
                    'required' => true
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Telefon'),
                    'name'      => 'ApaczkaSenderPhone',
                    'size'      => 20,
                    'maxlength' => 15,
                    'required'  => true
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('E-mail'),
                    'name'      => 'ApaczkaSenderEmail',
                    'size'      => 30,
                    'maxlength' => 100,
                    'required'  => true
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Konto pobraniowe'),
                    'name'     => 'ApaczkaSenderAccount',
                    'size'     => 30,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
    }

    protected function getPackageAndInvoiceConfigFormPart()
    {
        return array(
            'legend' => array(
                'title' => $this->l('Ustawienia domyślne paczki oraz fakturowania'),
            ),
            'input'  => array(
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Prefiks faktury:'),
                    'name'      => 'ApaczkaRefText',
                    'size'      => 20,
                    'maxlength' => 5,
                    'required'  => false
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Domyślna zawartość paczki'),
                    'name'      => 'ApaczkaContent',
                    'size'      => 20,
                    'maxlength' => 40,
                    'required'  => true
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Domyślna waga paczki'),
                    'name'      => 'ApaczkaWeight',
                    'size'      => 20,
                    'maxlength' => 2,
                    'suffix'    => 'KG',
                    'required'  => true
                ),
                array(
                    'type'  => 'label',
                    'label' => $this->l('Domyślne wymiary paczki:'),
                    'name'  => 'prompt1'
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Długość'),
                    'name'      => 'ApaczkaDim1',
                    'size'      => 20,
                    'maxlength' => 3,
                    'required'  => true,
                    'suffix'    => 'CM'
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Szerokość'),
                    'name'      => 'ApaczkaDim2',
                    'size'      => 20,
                    'maxlength' => 3,
                    'required'  => true,
                    'suffix'    => 'CM'
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('Wysokość'),
                    'name'      => 'ApaczkaDim3',
                    'size'      => 20,
                    'maxlength' => 3,
                    'required'  => true,
                    'suffix'    => 'CM'
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
    }

    protected function getNotificationsConfigFormPart()
    {
        return array(
            'legend' => array(
                'title' => $this->l('Ustawienia powiadomień emailowych'),
            ),
            'input'  => array(
                array(
                    'type'  => 'label',
                    'name'  => 'prompt2',
                    'label' => $this->l('Powiadomienia nadawcy (sklepu): '),

                ),
                array(
                    'type'    => 'radio',
                    'label'   => $this->l('Dostarczenie'),
                    'name'    => 'ApaczkaSenderDelivered',
                    'is_bool' => true,
                    'class'   => 'radio-inline',
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Tak')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Nie')
                        )
                    )
                ),
                array(
                    'type'    => 'radio',
                    'label'   => $this->l('Niepowodzenie'),
                    'name'    => 'ApaczkaSenderException',
                    'is_bool' => true,
                    'class'   => 'radio-inline',
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Tak')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Nie')
                        )
                    )
                ),
                array(
                    'type'    => 'radio',
                    'label'   => $this->l('W momencie zarejestrowania'),
                    'name'    => 'ApaczkaSenderRegister',
                    'is_bool' => true,
                    'class'   => 'radio-inline',
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Tak')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Nie')
                        )
                    )
                ),
                array(
                    'type'    => 'radio',
                    'label'   => $this->l('Wysłanie'),
                    'name'    => 'ApaczkaSenderSent',
                    'is_bool' => true,
                    'class'   => 'radio-inline',
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Tak')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Nie')
                        )
                    )
                ),
                array(
                    'type'  => 'label',
                    'name'  => 'prompt2',
                    'label' => $this->l('Powiadomienia odbiorcy (klienta) : ')
                ),
                array(
                    'type'    => 'radio',
                    'label'   => $this->l('Dostarczenie'),
                    'name'    => 'ApaczkaReceiverDelivered',
                    'is_bool' => true,
                    'class'   => 'radio-inline',
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Tak')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Nie')
                        )
                    )
                ),
                array(
                    'type'    => 'radio',
                    'label'   => $this->l('Niepowodzenie'),
                    'name'    => 'ApaczkaReceiverException',
                    'is_bool' => true,
                    'class'   => 'radio-inline',
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Tak')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Nie')
                        )
                    )
                ),
                array(
                    'type'    => 'radio',
                    'label'   => $this->l('W momencie zarejestrowania'),
                    'name'    => 'ApaczkaReceiverRegister',
                    'is_bool' => true,
                    'class'   => 'radio-inline',
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Tak')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Nie')
                        )
                    )
                ),
                array(
                    'type'    => 'radio',
                    'label'   => $this->l('Wysłanie'),
                    'name'    => 'ApaczkaReceiverSent',
                    'is_bool' => true,
                    'class'   => 'radio-inline',
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Tak')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Nie')
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
    }
}
