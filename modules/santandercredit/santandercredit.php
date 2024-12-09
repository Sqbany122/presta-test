<?php

if (!defined('_CAN_LOAD_FILES_'))
    exit;

class SantanderCredit extends PaymentModule {

    private $_html = '';
    private $_errorsArray = array();
    private $shopTestId = '99995';
    private $ssl = false;     


    public function __construct() {

        $this->name = 'santandercredit';
        $this->tab = 'payments_gateways';
        $this->version = 4.10;
        $this->author = 'Santander';

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
                !Configuration::updateValue('SANTANDERCREDIT_SYMULATOR', 'tak') ||
                !$this->registerHook('payment') ||
                !$this->registerHook('paymentReturn') ||
                !$this->registerHook('rightColumn') ||
                !$this->registerHook('leftColumn') ||
                !$this->registerHook('shoppingCart') ||
                !$this->registerHook('productActions') ||
                !$this->createOrderState() ||
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
                !parent::uninstall()
        ) {

            return false;
        }

        return true;
    }

    public function getContent() {

        $this->_html = '<h2>Zakupy na raty z systemem ratalnym Santander</h2>';

        if (isset($_POST['santanderCreditSubmit'])) {


            if (empty($_POST['santanderCreditShopId'])) {

                $this->_errorsArray[] = $this->l('Musisz podać id sklepu w systemie eRaty Santander');
            } else if (!is_numeric($_POST['santanderCreditShopId'])) {

                $this->_errorsArray[] = $this->l('Numer sklepu musi być liczbą.');
            }


            $santanderCreditBlock = $_POST['santanderCreditBlock'];
            $santanderCreditBlockTitle = $_POST['santanderCreditBlockTitle'];
            $santanderCreditSymulator = $_POST['santanderCreditSymulator'];
//            SANTANDERCREDIT_USE_ORDER_STATE
            $santanderCreditUseOrderState = $_POST['santanderCreditUseOrderState'];

            if (!sizeof($this->_errorsArray)) {

                Configuration::updateValue('SANTANDERCREDIT_SHOP_ID', intval($_POST['santanderCreditShopId']));
                Configuration::updateValue('SANTANDERCREDIT_BLOCK', $santanderCreditBlock);
                Configuration::updateValue('SANTANDERCREDIT_BLOCK_TITLE', $santanderCreditBlockTitle);
                Configuration::updateValue('SANTANDERCREDIT_SYMULATOR', $santanderCreditSymulator);
//                SANTANDERCREDIT_USE_ORDER_STATE
                Configuration::updateValue('SANTANDERCREDIT_USE_ORDER_STATE', $santanderCreditUseOrderState);
                $this->displayConf();
            } else {
                $this->displayErrors();
            }
        }

        //$this->displayInformation();
        $this->displayFormSettings();

        return $this->_html;
    }

    public function displayConf() {

        $this->_html .= '
	<div class="conf confirm">
		<img src="../img/admin/ok.gif" />
		' . $this->l('Ustawienia zostały zapisane') . '
	</div>
  ';
    }

    public function displayErrors() {

        $this->_html .= '
	<div class="alert error">
		<h3>Wystąpiły błędy:</h3>
		<ol>
	';

        foreach ($this->_errorsArray as $error) {
            $this->_html .= '<li>' . $error . '</li>';
        }

        $this->_html .= '
	  </ol>
	</div>
	';
    }

    public function displayInformation() {

        $this->_html .= '
	<fieldset style="background: #fff; margin: 10px 0;">

	    <legend><img src="../img/admin/details.gif" />' . $this->l('Informacje') . '</legend>

	    <p>
	        <img src="../modules/' . $this->name . '/images/moduleLogo.jpg" />
	    </p>

	    <p>
	        <b>' . $this->l('Moduł umożliwia sprzedaż ratalną za pomocą systemu Santander Consumer Bank') . '</b>
	    </p>

	    <p>
	        ' . $this->l('Przed użyciem modułu skonfiguruj swoje ustawienia dla Santander za pomocą opcji dostępnych poniżej.') . '
	    </p>

	</fieldset>
	';
    }

    public function displayFormSettings() {
        global $smarty;
        $currentLangId = Context::getContext()->language->id;

        $santandercreditOsAuthName = 'SANTANDERCREDIT_OS_AUTHORIZATION';
        $santandercreditOsAuthNameObj = (new OrderState(Configuration::get('SANTANDERCREDIT_OS_AUTHORIZATION')));
        if ($santandercreditOsAuthNameObj != null) {
            $santandercreditOsAuthName = $santandercreditOsAuthNameObj->name[$currentLangId];
        };

        $psOsPaymentName = 'PS_OS_PAYMENT';
        $psOsPaymentNameObj = (new OrderState(Configuration::get('PS_OS_PAYMENT')));
        if ($psOsPaymentNameObj != null) {
            $psOsPaymentName = $psOsPaymentNameObj->name[$currentLangId];
        }

		$proto = 'http://';               
        if ( $this->ssl ) {
            $proto = 'https://';
        }
		
        $smarty->assign(array(
            'santanderCreditShopId' => Configuration::get('SANTANDERCREDIT_SHOP_ID'),
            'santanderCreditBlock' => Configuration::get('SANTANDERCREDIT_BLOCK'),
            'santanderCreditBlockTitle' => Configuration::get('SANTANDERCREDIT_BLOCK_TITLE'),
            'santanderCreditSymulator' => Configuration::get('SANTANDERCREDIT_SYMULATOR'),
            'santanderCreditUseOrderState' => Configuration::get('SANTANDERCREDIT_USE_ORDER_STATE'),
            'santandercreditOsAuthName' => $santandercreditOsAuthName,
            'psOsPaymentName' => $psOsPaymentName,
            'requestUri' => $_SERVER['REQUEST_URI'],
            'shopTestId' => $this->shopTestId,
            'bannerUrl' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . '/modules/' . $this->name . '/images/bannerBlok.jpg'
        ));
        $this->_html .= $this->display(__FILE__, 'moduleConfiguration.tpl');
    }

    public function hookPayment($params) {

        global $smarty;

        $smarty->assign(array(
            'totalOrder' => $params['cart']->getOrderTotal(),
            'shopId' => Configuration::get('SANTANDERCREDIT_SHOP_ID'),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
        ));

        return $this->display(__FILE__, 'santanderCredit.tpl');
    }

    public function hookShoppingCart($params) {

        global $smarty;

        $smarty->assign(array(
            'totalOrder' => $params['cart']->getOrderTotal(),
            'shopId' => Configuration::get('SANTANDERCREDIT_SHOP_ID')
        ));

        return $this->display(__FILE__, 'santanderCreditCart.tpl');
    }

    public function execValidation() {
        global $cart, $currency;
        $total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
//      may be PS_OS_PAYMENT  or SANTANDERCREDIT_OS_AUTHORIZATION
        $useOrderState = Configuration::get('SANTANDERCREDIT_USE_ORDER_STATE');
        
//        calling validateOrderMethod *****************************************************
//        OLD WAY 
//        $this->validateOrder($cart->id, (int) Configuration::get($useOrderState), $total, $this->displayName, NULL, NULL, $currency->id);
//        NEW WAY   
        $customer = new Customer((int)$cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');
        $this->validateOrder($cart->id, (int) Configuration::get($useOrderState), $total, $this->displayName, NULL, NULL, $currency->id, false, $customer->secure_key, null);
//      calling validateOrderMethod END *****************************************************
        
        return $this->currentOrder;
    }

    public function execPayment() {

        global $smarty, $cookie, $cart, $currency;

        $address = new Address(intval($cart->id_address_invoice));
        $customer = new Customer(intval($cart->id_customer));

        $total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));

//	$this->validateOrder($cart->id, (int)Configuration::get('PS_OS_PAYMENT'), $total, $this->displayName, NULL, NULL, $currency->id);

        $santanderCreditShopId = Configuration::get('SANTANDERCREDIT_SHOP_ID');


        if (!Validate::isUnsignedInt($santanderCreditShopId)) {
            return $this->l('Błąd płatności: nieprawidłowy numer eRaty sklepu.');
        }

        if (!Validate::isLoadedObject($address) || !Validate::isLoadedObject($customer)) {
            return $this->l('Błąd płatności: nieprawidłowy adres lub dane klienta.');
        }


        $productsInputs = '';
        $products = $cart->getProducts(true);
        $summaryDetails = $cart->getSummaryDetails();

        for ($i = 0, $nr = 1; $i < sizeof($products); $i++, $nr++) {

            $productsInputs .= '
			<input name="idTowaru' . $nr . '" readonly="readonly" type="hidden" value="' . $products[$i]['id_product'] . '" />
			<input name="nazwaTowaru' . $nr . '" readonly="readonly" type="hidden" value="' . $products[$i]['name'] . '" />
			<input name="wartoscTowaru' . $nr . '" readonly="readonly" type="hidden" value="' . round($products[$i]['price_wt'], 2) . '" />
			<input name="liczbaSztukTowaru' . $nr . '" readonly="readonly" type="hidden" value="' . $products[$i]['quantity'] . '" />
			<input name="jednostkaTowaru' . $nr . '" readonly="readonly" type="hidden" value="szt" />
		';
        }

        if ($summaryDetails['total_shipping'] > 0) {

            $productsInputs .= '
  		<input type="hidden" name="idTowaru' . $nr . '" readonly="readonly" value="KosztPrzesylki" />
  		<input type="hidden" name="nazwaTowaru' . $nr . '" readonly="readonly" value="Koszt przesyłki" />
  		<input type="hidden" name="wartoscTowaru' . $nr . '" readonly="readonly" value="' . round($summaryDetails['total_shipping'], 2) . '" />
                <input type="hidden" name="liczbaSztukTowaru' . $nr . '" readonly="readonly" value="1" />
                <input type="hidden" name="jednostkaTowaru' . $nr . '" readonly="readonly" value="szt" />';
        }

        $productsInputs .= '<input type="hidden" name="liczbaSztukTowarow" value="' . $nr . '" />';

		$proto = 'http://';
//		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
//			$proto = 'https://';
//		}

        if ( $this->ssl ) {
            $proto = 'https://';
        }

        $smarty->assign(array(
            'orderId' => intval($this->currentOrder),
            'shopId' => $santanderCreditShopId,
            'shopName' => Configuration::get('PS_SHOP_NAME'),
            'shopMailAdress' => Configuration::get('PS_SHOP_EMAIL'),
            'shopPhone' => Configuration::get('PS_SHOP_PHONE'),
            'shopHttp' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__,
            'returnTrue' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/santanderCreditReturn.php?status=true&orderId=',
            'returnFalse' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/santanderCreditReturn.php?status=false&orderId=',
            'email' => $customer->email,
            'imie' => ( $cookie->logged ? $cookie->customer_firstname : false ),
            'nazwisko' => ( $cookie->logged ? $cookie->customer_lastname : false ),
            'telKontakt' => $address->phone_mobile,
            'ulica' => $address->address1,
            'ulica2' => $address->address2,
            'miasto' => $address->city,
            'kodPocz' => $address->postcode,
            'productsInputs' => $productsInputs,
            'modDir' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name
        ));

        //return $this->display( __FILE__, 'santanderCreditForm.tpl' );
    }

    function paymentReturn() {

        global $smarty;

        $status = Tools::getValue('status');
        $wniosekId = Tools::getValue('wniosekId');

        $smarty->assign(array(
            'status' => $status,
            'wniosekId' => $wniosekId
        ));

        return $this->display(__FILE__, 'santanderCreditReturn.tpl');
    }

    function hookLeftColumn() {

        global $smarty;

        $smarty->assign(array(
            'santanderCreditBlockTitle' => Configuration::get('SANTANDERCREDIT_BLOCK_TITLE')
        ));

        if (Configuration::get('SANTANDERCREDIT_BLOCK') == 'left') {
            return $this->display(__FILE__, 'santanderCreditBlock.tpl');
        }
    }

    function hookRightColumn() {

        global $smarty;

        $smarty->assign(array(
            'santanderCreditBlockTitle' => Configuration::get('SANTANDERCREDIT_BLOCK_TITLE')
        ));

        if (Configuration::get('SANTANDERCREDIT_BLOCK') == 'right') {
            return $this->display(__FILE__, 'santanderCreditBlock.tpl');
        }
    }

    function hookProductActions() {

        global $smarty;

        $id_product = Tools::getValue('id_product');
        $product = new Product($id_product, true);

        $smarty->assign(array(
            'shopId' => Configuration::get('SANTANDERCREDIT_SHOP_ID'),
            'santanderCreditProductPrice' => round($product->getPrice(true), 2)
        ));

        if (Configuration::get('SANTANDERCREDIT_SYMULATOR') == 'tak') {
            return $this->display(__FILE__, 'santanderCreditProduct.tpl');
        }
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
                $order_state->logable = TRUE;	//YOU HAVE TO SET IT TRUE (see problem with SCB application number update)
                $order_state->invoice = FALSE;
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

?>