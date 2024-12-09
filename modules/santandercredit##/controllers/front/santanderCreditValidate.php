<?php

class santandercreditsantanderCreditValidateModuleFrontController extends ModuleFrontController {

    public function initContent() {

        parent::initContent();

        $santanderCredit = new SantanderCredit();
        $santanderCredit->execValidation();

        $this->setTemplate('santanderCreditValidate.tpl');
    }

    public function postProcess() {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
            Tools::redirect('index.php?controller=order&step=1');

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module)
            if ($module['name'] == 'santandercredit') {
                $authorized = true;
                break;
            }
        if (!$authorized)
            die('NOT AUTHORIZED: santanderCreditValidate');

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');
/*
        $currency = $this->context->currency;
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);

        $this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $total, $this->module->displayName, NULL, $mailVars, (int) $currency->id, false, $customer->secure_key);
        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key);
*/
        $currency = $this->context->currency;
        $total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
        $useOrderState = Configuration::get('SANTANDERCREDIT_USE_ORDER_STATE');                      
        $this->module->validateOrder($cart->id, (int) Configuration::get($useOrderState), $total, $this->module->displayName, NULL, NULL, $currency->id, false, $customer->secure_key);
        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key);
    }

}

?>