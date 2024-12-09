<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_'))
    exit;

class SantanderCredit extends PaymentModule {

    private $_errorsArray = array();
    private $shopTestId = '99995';
    private $ssl = false;

    public function __construct() {

        $this->name = 'santandercredit';
        $this->tab = 'payments_gateways';
        $this->version = 5.5;
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->author = 'Santander Consumer Bank';

        $this->bootstrap = true;
        parent::__construct();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Santander - System ratalny');
        $this->description = $this->l('Santander - Zakupy na raty w internecie');
        if (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on") {
            $this->ssl = true;
        }
    }

    public function install() {
        if (
                !parent::install() ||
                !Configuration::updateValue('SANTANDERCREDIT_SHOP_ID', $this->shopTestId) ||
                !Configuration::updateValue('SANTANDERCREDIT_BLOCK', 'left') ||
                !Configuration::updateValue('SANTANDERCREDIT_BLOCK_TITLE', 'eRaty Santander Consumer Bank') ||
                !Configuration::updateValue('SANTANDERCREDIT_SYMULATOR', 'true') ||
                !Configuration::updateValue('SANTANDERCREDIT_URL_SYMULATOR', 'https://wniosek.eraty.pl/symulator/oblicz/') ||
                !Configuration::updateValue('SANTANDERCREDIT_URL_WNIOSEK', 'https://wniosek.eraty.pl/formularz/') ||
                !Configuration::updateValue('SANTANDERCREDIT_QTY_SELECTOR', '#quantity_wanted') ||
                !Configuration::updateValue('SANTANDERCREDIT_PRICE_SELECTOR', 'div.current-price > span[itemprop="price"]') ||
                !$this->registerHook('paymentOptions') ||
                !$this->registerHook('paymentReturn') ||
                !$this->registerHook('displayProductAdditionalInfo') ||
                !$this->createOrderState() ||
//                !$this->registerHook('displayRightColumnProduct') ||
//                !$this->registerHook('displayCompareExtraInformation') ||
//                !$this->registerHook('displayFooterProduct') ||                
//                !$this->registerHook('rightColumn') ||
//                !$this->registerHook('leftColumn') ||
//                !$this->registerHook('productActions') ||
                !Configuration::updateValue('SANTANDERCREDIT_USE_ORDER_STATE', 'SANTANDERCREDIT_OS_AUTHORIZATION')
        ) {
            return false;
        }
        return true;
    }

    public function uninstall() {
        if (
                !Configuration::deleteByName('SANTANDERCREDIT_SHOP_ID') ||
                !Configuration::deleteByName('SANTANDERCREDIT_BLOCK') ||
                !Configuration::deleteByName('SANTANDERCREDIT_BLOCK_TITLE') ||
                !Configuration::deleteByName('SANTANDERCREDIT_SYMULATOR') ||
                !Configuration::deleteByName('SANTANDERCREDIT_USE_ORDER_STATE') ||
                !Configuration::deleteByName('SANTANDERCREDIT_URL_SYMULATOR') ||
                !Configuration::deleteByName('SANTANDERCREDIT_URL_WNIOSEK') ||
                !Configuration::deleteByName('SANTANDERCREDIT_QTY_SELECTOR') ||
                !Configuration::deleteByName('SANTANDERCREDIT_PRICE_SELECTOR') ||                
                !parent::uninstall()
        ) {
            return false;
        }
        return true;
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $scbShopID = strval(Tools::getValue('SANTANDERCREDIT_SHOP_ID'));
            if (!$scbShopID || empty($scbShopID) || !Validate::isGenericName($scbShopID))
                $output .= $this->displayError($this->l('Nieprawidłowy numer Sklepu'));
            else {
				Configuration::updateValue('SANTANDERCREDIT_SHOP_ID', '1234');
                Configuration::updateValue('SANTANDERCREDIT_SHOP_ID', trim(Tools::getValue('SANTANDERCREDIT_SHOP_ID')));
                Configuration::updateValue('SANTANDERCREDIT_SYMULATOR', strval(Tools::getValue('SANTANDERCREDIT_SYMULATOR')));
                Configuration::updateValue('SANTANDERCREDIT_URL_SYMULATOR', Tools::getValue('SANTANDERCREDIT_URL_SYMULATOR'));
                Configuration::updateValue('SANTANDERCREDIT_URL_WNIOSEK', Tools::getValue('SANTANDERCREDIT_URL_WNIOSEK'));
                Configuration::updateValue('SANTANDERCREDIT_QTY_SELECTOR', Tools::getValue('SANTANDERCREDIT_QTY_SELECTOR'));
                Configuration::updateValue('SANTANDERCREDIT_PRICE_SELECTOR', Tools::getValue('SANTANDERCREDIT_PRICE_SELECTOR'));
                $output .= $this->displayConfirmation($this->l('Zmiany zostały zapisane'));
            }
        }
        $output = $output . $this->display(__FILE__, 'infos.tpl');
        return $output . $this->displayForm();
    }

    public function displayForm() {
        // Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Parametry bramki płatniczej eRaty Santander:'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Numer Sklepu'),
                    'name' => 'SANTANDERCREDIT_SHOP_ID',
                    'size' => 20,
                    'required' => true
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Kalkulator na stronie produktu'),
                    'name' => 'SANTANDERCREDIT_SYMULATOR',
                    'is_bool' => true,
                    'hint' => $this->l('Umożliwia obliczanie wysokości raty na stronie produktu'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('TAK'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('NIE'),
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Adres symulatora'),
                    'name' => 'SANTANDERCREDIT_URL_SYMULATOR',
                    'size' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Adres rejestratora wniosków'),
                    'name' => 'SANTANDERCREDIT_URL_WNIOSEK',
                    'size' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Selektor ilości towaru'),
                    'name' => 'SANTANDERCREDIT_QTY_SELECTOR',
                    'hint' => 'Selektor (jQuery) wskazujący na pole zawierające ilość jednostek produktu. Odczyt wartości metodą val().',
                    'size' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Selektor ceny towaru'),
                    'name' => 'SANTANDERCREDIT_PRICE_SELECTOR',
                    'hint' => 'Selektor (jQuery) wskazujący na pole zawierające cenę jednostkową. Odczyt wartości z atrybutu content.',
                    'size' => 128,
                    'required' => true
                ),                
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['SANTANDERCREDIT_SHOP_ID'] = Configuration::get('SANTANDERCREDIT_SHOP_ID');
        $helper->fields_value['SANTANDERCREDIT_SYMULATOR'] = Configuration::get('SANTANDERCREDIT_SYMULATOR');
        $helper->fields_value['SANTANDERCREDIT_URL_SYMULATOR'] = Configuration::get('SANTANDERCREDIT_URL_SYMULATOR');
        $helper->fields_value['SANTANDERCREDIT_URL_WNIOSEK'] = Configuration::get('SANTANDERCREDIT_URL_WNIOSEK');
        $helper->fields_value['SANTANDERCREDIT_QTY_SELECTOR'] = Configuration::get('SANTANDERCREDIT_QTY_SELECTOR');
        $helper->fields_value['SANTANDERCREDIT_PRICE_SELECTOR'] = Configuration::get('SANTANDERCREDIT_PRICE_SELECTOR');

        return $helper->generateForm($fields_form);
    }

    public function hookPaymentOptions($params) {
//        Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
//            'imgDir' => $this->context->link->getModuleLink('santandercredit','images')        
        if ($params['cart']->getOrderTotal() < 100)
            return;
        $this->smarty->assign(array(
            'totalOrderC' => Tools::displayPrice($params['cart']->getOrderTotal(true, Cart::BOTH)),
            'shopId' => trim(Configuration::get('SANTANDERCREDIT_SHOP_ID')),
            'symulatorURL' => Configuration::get('SANTANDERCREDIT_URL_SYMULATOR'),
            'jq_qtySelector' => Configuration::get('SANTANDERCREDIT_QTY_SELECTOR'),
            'jq_priceSelector' => Configuration::get('SANTANDERCREDIT_PRICE_SELECTOR'),
            'totalOrder' => $params['cart']->getOrderTotal(),
            'imgDir' => $this->_path . 'images'
        ));
        if (!isset($this->context)) {
            $this->context = Context::getContext();
        }
        $newOption = new PaymentOption();
        $newOption->setModuleName($this->name)
                ->setCallToActionText('eRaty Santander Consumer Bank')
                ->setAction($this->context->link->getModuleLink('santandercredit', 'santanderCreditValidate', array(), true))
                ->setAdditionalInformation($this->fetch('module:santandercredit/views/templates/hook/santanderCreditInfo.tpl'));
        $payment_options = [
            $newOption,
        ];
        return $payment_options;
    }

    function hookPaymentReturn($params) {
        // global $cart, $cookie, $currency;
        $cart = new Cart(intval($params['order']->id_cart));
        $cookie = $this->context->cookie;
        $address = new Address(intval($cart->id_address_invoice));
        $customer = new Customer(intval($cart->id_customer));
        $total = floatval(number_format($cart->getOrderTotal(true, Cart::BOTH), 2, '.', ''));
        $santanderCreditShopId = trim(Configuration::get('SANTANDERCREDIT_SHOP_ID'));		
/*        
		if (!Validate::isUnsignedInt($santanderCreditShopId)) {
            return $this->l('Błąd płatności: nieprawidłowy numer sklepu.');
        }
*/
        if (!Validate::isLoadedObject($address) || !Validate::isLoadedObject($customer)) {
            return $this->l('Błąd płatności: nieprawidłowy adres lub dane klienta.');
        }
        // $productsInputs = '';
        // $products = $cart->getProducts(true);
        $summaryDetails = $cart->getSummaryDetails();
        $proto = 'http://';
        if ($this->ssl) {
            $proto = 'https://';
        }
        $this->smarty->assign(array(
            'applicationURL' => Configuration::get('SANTANDERCREDIT_URL_WNIOSEK'),
            'jq_qtySelector' => Configuration::get('SANTANDERCREDIT_QTY_SELECTOR'),
            'jq_priceSelector' => Configuration::get('SANTANDERCREDIT_PRICE_SELECTOR'),
            'orderId' => $params['order']->id,
            'shopId' => $santanderCreditShopId,
            'shopName' => Configuration::get('PS_SHOP_NAME'),
            'shopMailAdress' => Configuration::get('PS_SHOP_EMAIL'),
            'shopPhone' => Configuration::get('PS_SHOP_PHONE'),
            'shopHttp' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__,
//            http://localhost/presta/prestashop_1.7.1.0/module/santandercredit/test
            'returnTrue' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'module/' . $this->name . '/santanderCreditReturn?status=true&orderId=',
            'returnFalse' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'module/' . $this->name . '/santanderCreditReturn?status=false&orderId=',
            'email' => $customer->email,
            'imie' => ( $cookie->logged ? $cookie->customer_firstname : false ),
            'nazwisko' => ( $cookie->logged ? $cookie->customer_lastname : false ),
            'telKontakt' => $address->phone_mobile,
            'ulica' => $address->address1,
            'ulica2' => $address->address2,
            'miasto' => $address->city,
            'kodPocz' => $address->postcode,
//            'productsInputs' => $productsInputs,
            'shipping' => round($summaryDetails['total_shipping'], 2),
            'products' => $cart->getProducts(true),
            'totalOrder' => $total,
            'modDir' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name
        ));

        $this->context->controller->registerJavascript(
                $this->name . '-scb_js', 'modules/' . $this->name . '/js/santanderCredit.js', [
            'priority' => 200,
            'attribute' => 'async',
                ]
        );
        return $this->fetch('module:santandercredit/views/templates/hook/santanderCreditPayment.tpl');
    }

    public function displaySymulator($params) {
        global $smarty;

        $id_product = Tools::getValue('id_product');
        $product = new Product($id_product, true);

        $smarty->assign(array(
            'shopId' => trim(Configuration::get('SANTANDERCREDIT_SHOP_ID')),
            'santanderCreditProductPrice' => round($product->getPrice(true), 2),
            'jq_qtySelector' => Configuration::get('SANTANDERCREDIT_QTY_SELECTOR'),
            'jq_priceSelector' => Configuration::get('SANTANDERCREDIT_PRICE_SELECTOR'),
            'symulatorURL' => Configuration::get('SANTANDERCREDIT_URL_SYMULATOR')
        ));

        if (Configuration::get('SANTANDERCREDIT_SYMULATOR') <> null) {
            return $this->display(__FILE__, 'santanderCreditProduct.tpl');
        }
    }

    public function hookDisplayProductAdditionalInfo($params){
        return $this->displaySymulator($params);
    }
    public function hookDisplayFooterProduct($params){
        return $this->displaySymulator($params);
    }
    
    public function hookDisplayCompareExtraInformation($params) {
        return $this->displaySymulator($params);
    }

    public function hookDisplayRightColumnProduct($params) {

        return $this->displaySymulator($params);
    }

    public function hookProductActions($params) {
        return $this->displaySymulator($params);
    }

    public function hookRightColumn($params) {
        return $this->displaySymulator($params);
    }

    public function hookLeftColumn($params) {
        return $this->displaySymulator($params);
    }

    /**
     * Creates new order state for eRaty payment system and configuration parameter SANTANDERCREDIT_OS_AUTHORIZATION
     * containing new order id. If parameter already exists - do nothing.
     * 
     * @boolean creating order result
     */
    function createOrderState() {
        $result = true;
        if (!Configuration::get('SANTANDERCREDIT_OS_AUTHORIZATION')) {
            try {
                $order_state = new OrderState();
                $order_state = new OrderState();
                $order_state->name = array();

                foreach (Language::getLanguages() as $language) {
                    if (Tools::strtolower($language['iso_code']) == 'pl')
                        $order_state->name[$language['id_lang']] = 'Płatność eRaty – status decyzji Banku dostępny w Panel Sklep.';
                    else
                        $order_state->name[$language['id_lang']] = 'eRaty payment - decision state available in Panel Sklep.';
                }

                $order_state->send_email = false;
                $order_state->color = '#DDEEFF';
                $order_state->hidden = false;
                $order_state->delivery = false;
                $order_state->logable = true;
                $order_state->invoice = true;
                $order_state->module_name = 'eRaty';

                $order_state->add();
                $result = Configuration::updateValue('SANTANDERCREDIT_OS_AUTHORIZATION', (int) $order_state->id);
            } catch (Exception $exc) {
                $result = false;
            }
        }
        return $result;
    }

}
