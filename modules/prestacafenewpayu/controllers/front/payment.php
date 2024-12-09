<?php
/**
 * 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
 *
 * NOTICE OF LICENSE
 *
 * Lukasz Kowalczyk is the owner of the copyright of this module.
 * All rights of any kind, which are not expressly granted in this
 * License, are entirely and exclusively reserved to and by Lukasz
 * Kowalczyk.
 *
 * You may not rent, lease, transfer, modify or create derivative
 * works based on this module.
 *
 * @author    Lukasz Kowalczyk <lkowalczyk@prestacafe.pl>
 * @copyright Lukasz Kowalczyk
 * @license   LICENSE.txt
 */

/**
 * Creates a new order (with PaymentModule::validateOrder) and also
 * a new PayU transaction.
 */
class PrestacafenewpayuPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    /** @var PrestaCafeNewPayu */
    public $module;
    public $display_column_left = false;
    public $display_column_right = false;

    public function postProcess()
    {
        $cart = $this->context->cart;

        if (!$cart->id || $cart->orderExists() || $cart->getOrderTotal() < 1) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payment_already_placed.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payment_already_placed_ps17.tpl');
            }
            return;
        }

        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payupayment.php';
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payutools.php';
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/restapi.php';

        $currency = new Currency($cart->id_currency);
        $pos_id = (int)PrestaCafeNewPayu::getData('pos_id_'.$currency->iso_code);
        $pos_key = PrestaCafeNewPayu::getData('key_'.$currency->iso_code);
        $pos_second_key = PrestaCafeNewPayu::getData('second_key_'.$currency->iso_code);
        $send_payment_email = PrestaCafeNewPayu::getData('send_payment_email_'.$currency->iso_code);
        $display_payment_methods = PrestaCafeNewPayu::getData('display_payment_methods_'.$currency->iso_code);

        $api = new PrestaCafePayuApi($pos_id, $pos_key);

        $pbl = Tools::getValue('pbl');

        if ($display_payment_methods && !$pbl) {
            try {
                $payByLinks = $api->getPayMethodsAssoc();
                $this->context->smarty->assign('payByLinks', $payByLinks);
                if (version_compare(_PS_VERSION_, '1.7', '<')) {
                    $this->setTemplate('payment_payment_methods.tpl');
                } else {
                    $this->setTemplate(
                        'module:prestacafenewpayu/views/templates/front/payment_payment_methods_ps17.tpl'
                    );
                }
                return;
            } catch (Exception $e) {
                // If getting payment links failed, going on with the default behavior
                PrestaCafeNewPayu::addLog("payment: getPayMethods: ".$e->getMessage(), 2);
            }
        }

        // Surcharge calculation
        $surcharge = 0;

        $surcharge_product_id = PrestaCafeNewPayu::getData('surcharge_product_'.$currency->iso_code);
        $surcharge_product = new Product($surcharge_product_id, true);
        $surcharge_percentage = (float)PrestaCafeNewPayu::getData('surcharge_'.$currency->iso_code);
        $surcharge_min = (int)round(PrestaCafeNewPayu::getData('surcharge_min_'.$currency->iso_code) * 100, 0);
        $surcharge_max = (int)round(PrestaCafeNewPayu::getData('surcharge_max_'.$currency->iso_code) * 100, 0);

        if (Validate::isLoadedObject($surcharge_product)) {
            $surcharge = PayUTools::calculateSurcharge(
                $cart,
                $surcharge_percentage,
                $surcharge_min,
                $surcharge_max
            );
            PayUTools::addSurchargeProductToCart($cart, $surcharge_product);
            PayUTools::resetSurchargeProductPrice($surcharge_product, $surcharge);
        }

        $openpayu_struct = PayUTools::createCreateOrderStructFromCart(
            $cart,
            $pos_id,
            $this->module->name,
            sprintf($this->module->l('Order in %s', 'payment'), Configuration::get('PS_SHOP_NAME')),
            $this->module->l('Discount', 'payment'),
            $this->module->l('Shipping: %s', 'payment')
        );

        if ($surcharge_product_id) {
            $cart->deleteProduct($surcharge_product_id);
            $cart->update();
        }

        if ($pbl) {
            $openpayu_struct['payMethods'] = array(
                'payMethod' => array(
                    'type' => 'PBL',
                    'value' => $pbl
                )
            );
        }

        $payment = new PayUPayment;
        $payment->id_cart = (int)$cart->id;
        $payment->payu_pos_id = $pos_id;
        $payment->payu_second_key = $pos_second_key;
        $payment->payu_order_status = PayUPayment::STATUS_NEW;
        $payment->payu_external_order_id = $openpayu_struct['extOrderId'];
        $payment->iso_currency = $currency->iso_code;
        $payment->payu_surcharge = $surcharge;

        if (!$payment->save()) {
            PrestaCafeNewPayu::addLog("payment: error creating PayUPayment ".Db::getInstance()->getMsgError(), 2);
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payment_try_again.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payment_try_again_ps17.tpl');
            }
            return;
        }

        try {
            $create_result = $api->createOrder($openpayu_struct);
        } catch (Exception $e) {
            PrestaCafeNewPayu::addLog("payment: createOrder failed: ".$e->getMessage(), 2);
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payment_try_again.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payment_try_again_ps17.tpl');
            }
            return;
        }

        $payment->payu_order_id = $create_result->orderId;
        if (!$payment->save()) {
            PrestaCafeNewPayu::addLog("payment: error updating PayUPayment ".Db::getInstance()->getMsgError(), 2);
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payment_try_again.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payment_try_again_ps17.tpl');
            }
            return;
        }

        PrestaCafeNewPayu::addLog("payment: created PayUPayment for cart #$cart->id", 1);

        // Available Payu languages differ between card payment and quick wire payments
        $available_languages = $pbl == 'c' ?
            $this->module->card_payment_languages : $this->module->rest_payment_languages;
        $redirectUri = $create_result->redirectUri
            .'&lang='.PayUTools::coerceLanguageIsoCode($this->context->language->iso_code, $available_languages);

        if ($send_payment_email) {
            // Send email to the customer with a link for paying again in case there is a problem
            $customer = new Customer($cart->id_customer);
            $payu_pay_again_url = Context::getContext()->link->getModuleLink(
                'prestacafenewpayu',
                'payagain',
                PayUTools::getSsecureTokenCartParams($cart)
            );
            $templateVars = array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{payu_pay_again_url}' => $payu_pay_again_url,
            );

            PrestaCafeNewPayu::addLog(
                "payment: sendPaymentPendingEmail: sending url $payu_pay_again_url to " . $customer->email
            );

            if (!Mail::Send(
                (int)Context::getContext()->cookie->id_lang,
                'pending',
                $this->module->l('Your PayU payment has started', 'payment'),
                $templateVars,
                $customer->email,
                $customer->firstname.' '.$customer->lastname,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_.'prestacafenewpayu/mails/',
                false,
                $cart->id_shop
            )) {
                PrestaCafeNewPayu::addLog(
                    "payment: sendPaymentPendingEmail: failed sending 'pending' email to $customer->email",
                    2
                );
            }
        }

        $this->redirect_after = $redirectUri;
    }

    // For PS 1.7
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        // So that section#main looks like on other customer pages (white background and shadow)
        $page['body_classes']['page-customer-account'] = true;
        return $page;
    }
}
