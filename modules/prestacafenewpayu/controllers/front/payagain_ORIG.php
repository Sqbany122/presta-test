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
 * This controller lets the customer pay for the order again without creating
 * a duplicate order.
 * Product list, discounts and shipping amounts are taken from the Order
 * object, not the Cart, as the order may be modified by the time the
 * customer attempts a new payment.
 *
 * TODO: test paczki produktów (payagain_cart)
 */

class PrestacafenewpayuPayAgainModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    /** @var PrestaCafeNewPayu */
    public $module;
    public $display_column_left = false;
    public $display_column_right = false;
    /**
     * @var PrestaCafePayuApi
     */
    private $api;

    public function postProcess()
    {
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payupayment.php';
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payutools.php';
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/restapi.php';

        $cart = PayUTools::getSsecureTokenCart();

        if (!$cart) {
            PrestaCafeNewPayu::addLog(
                "payagain: cart #".Tools::getValue('id_cart')." not found or the security parameters are wrong, "
                ."redirecting the customer to ordering",
                2
            );
            $this->redirect_after = $this->context->link->getPageLink('order');
            return;
        }

        if (!$cart->orderExists()) {
            PrestaCafeNewPayu::addLog(
                "payagain: cart #".Tools::getValue('id_cart')." has no order, redirecting customer to ordering",
                2
            );
            $this->redirect_after = $this->context->link->getPageLink('order');
            return;
        }

        $order = new Order(Order::getOrderByCartId($cart->id));
        $currency = new Currency($order->id_currency);

        $pos_id = (int)PrestaCafeNewPayu::getData('pos_id_'.$currency->iso_code);
        $pos_key = PrestaCafeNewPayu::getData('key_'.$currency->iso_code);
        $pos_second_key = PrestaCafeNewPayu::getData('second_key_'.$currency->iso_code);
        $display_payment_methods = PrestaCafeNewPayu::getData('display_payment_methods_'.$currency->iso_code);
        $basic_payment = PrestaCafeNewPayu::getData('basic_payment_'.$currency->iso_code);
        $direct_card = PrestaCafeNewPayu::getData('direct_card_'.$currency->iso_code);

        $this->api = new PrestaCafePayuApi($pos_id, $pos_key);

        // If anything was paid towards this order, repeat payment should not be possible
        if ($order->getTotalPaid() > 0) {
            PrestaCafeNewPayu::addLog(
                "payagain: cart #".Tools::getValue('id_cart')." already paid to the amount of "
                .$order->getTotalPaid(),
                2
            );
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payagain_already_paid.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payagain_already_paid_ps17.tpl');
            }
            return;
        }

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
//            PayUTools::addSurchargeProductToCart($cart, $surcharge_product);
//            PayUTools::resetSurchargeProductPrice($surcharge_product, $surcharge);
        } else {
            $surcharge = 0;
        }

        // Can proceed if the customer selected a payment method or clicked on the "Pay in PayU" block
        $can_proceed = Tools::getValue('cart_displayed')
            && (Tools::getValue('pbl') || !$display_payment_methods);

        $payu_error = false;

        // Cancel the PayU payment
        if ($can_proceed) {
            try {
                $last_payment = $this->cancelLastPayment($order);
                if ($last_payment && $last_payment->payu_order_status == PayUPayment::STATUS_COMPLETED) {
                    if (version_compare(_PS_VERSION_, '1.7', '<')) {
                        $this->setTemplate('payagain_already_paid.tpl');
                    } else {
                        $this->setTemplate(
                            'module:prestacafenewpayu/views/templates/front/payagain_already_paid_ps17.tpl'
                        );
                    }
                    return;
                }
            } catch (Exception $e) {
                PrestaCafeNewPayu::addLog(
                    "payagain: cancel payment for order #$order->id failed: ".$e->getMessage(),
                    2
                );
                $payu_error = true;
                $can_proceed = false;
            }
        }

        if (!$can_proceed) {
            // from OrderDetailController
            $customizedDatas = Product::getAllCustomizedDatas((int)$order->id_cart);
            $id_order_state = (int)$order->getCurrentState();
            $order_status = new OrderState((int)$id_order_state, (int)$order->id_lang);
            $products = $order->getProducts();
            $customer = new Customer($order->id_customer);
            $this->context->smarty->assign(array(
                'shop_name' => (string) Configuration::get('PS_SHOP_NAME'),
                'order' => $order,
                'return_allowed' => false,
                'currency' => $currency,
                'logable' => (bool)$order_status->logable,
                'products' => $products,
                'discounts' => $order->getCartRules(),
                'is_guest' => false,
                'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
                'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
                'use_tax' => Configuration::get('PS_TAX'),
                'group_use_tax' => (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC),
                'customizedDatas' => $customizedDatas,
                'show_basic_payment' => $basic_payment,
                'show_direct_card' => $direct_card,
                'display_payment_methods' => $display_payment_methods,
                'payu_error' => $payu_error,
                'module_img_dir' => _MODULE_DIR_ . $this->module->name . '/views/img/',
                'img_dir' => __PS_BASE_URI__ . 'img/',
                'disable_javascript_payment_block' => PrestaCafeNewPayu::getData('disable_javascript_payment_block'),
                'surcharge' => $surcharge / 100.0
            ));
            if ($display_payment_methods) {
                try {
                    $payByLinks = $this->api->getPayMethodsAssoc();
                    $this->context->smarty->assign('payByLinks', $payByLinks);
                } catch (Exception $e) {
                    PrestaCafeNewPayu::addLog("payagain: getPayMethods: ".$e->getMessage(), 2);
                    $this->context->smarty->assign('display_payment_methods', false);
                }
            }
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payagain_cart.tpl');
            } else {
                $this->context->smarty->assign(
                    'order',
                    (new PrestaShop\PrestaShop\Adapter\Order\OrderPresenter())->present($order)
                );

                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payagain_cart_ps17.tpl');
            }
            return;
        }

        // Right before paying
        if (Validate::isLoadedObject($surcharge_product)) {
            PayUTools::addSurchargeProductToCart($cart, $surcharge_product);
            PayUTools::resetSurchargeProductPrice($surcharge_product, $surcharge);
        }

        $this->processWithOrder($order, $pos_id, $pos_second_key, $currency->iso_code);
    }

    private function cancelLastPayment($order)
    {
        $last_payment = PayUPayment::getLastPaymentByCartId($order->id_cart);

        if ($last_payment) {
            $get_response = $this->api->getOrder($last_payment->payu_order_id);
            $need_saving = false;
            if ($last_payment->payu_order_status != $get_response->orders[0]->status) {
                $last_payment->payu_order_status = $get_response->orders[0]->status;
                $need_saving = true;
            }
            // Completed payment should not be canceled, canceled payment need not to
            if ($last_payment->payu_order_status != PayUPayment::STATUS_COMPLETED
                && $last_payment->payu_order_status != PayUPayment::STATUS_CANCELED) {
                // Cancel the payment. Must succeed before proceeding.
                $this->api->deleteOrder($last_payment->payu_order_id);
                $last_payment->payu_order_status = PayUPayment::STATUS_CANCELED;
                $need_saving = true;
            }

            if ($need_saving && !$last_payment->save()) {
                PrestaCafeNewPayu::addLog(
                    "payagain: cancelLastPayment: error saving PayUPayment ".Db::getInstance()->getMsgError()
                    .", PayUPayment: ".var_export($last_payment, true),
                    2
                );
            }
        }

        return $last_payment;
    }

    private function processWithOrder(Order $order, $pos_id, $second_key, $iso_currency)
    {

        // Prepare JSON structure for PayU API.
        $openpayu_struct = PayUTools::createCreateOrderStructFromOrder(
            $order,
            $pos_id,
            $this->module->name,
            sprintf($this->module->l('Order in %s', 'payment'), Configuration::get('PS_SHOP_NAME')),
            $this->module->l('Discount', 'payment'),
            $this->module->l('Shipping: %s', 'payment')
        );

        if (Tools::getValue('pbl')) {
            $openpayu_struct['payMethods'] = array(
                'payMethod' => array(
                    'type' => 'PBL',
                    'value' => Tools::getValue('pbl')
                )
            );
        }

        // PayU API returns the URI where the customer should be redirected.
        // Create a new PayU payment.
        $new_payment = new PayUPayment;
        $new_payment->id_cart = $order->id_cart;
        // No point in copying those two values from previous payment, the merchant
        // could have intentionally changed the POS data.
        $new_payment->payu_pos_id = $pos_id;
        $new_payment->payu_second_key = $second_key;
        $new_payment->payu_order_status = PayUPayment::STATUS_NEW;
        $new_payment->payu_external_order_id = $openpayu_struct['extOrderId'];
        $new_payment->iso_currency = $iso_currency;
        if (!$new_payment->save()) {
            PrestaCafeNewPayu::addLog(
                "payagain: error creating PayUPayment ".Db::getInstance()->getMsgError()
                .", PayUPayment: ".var_export($new_payment, true),
                2
            );
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payagain_try_again.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payagain_try_again_ps17.tpl');
            }
            return;
        }

        try {
            $create_response = $this->api->createOrder($openpayu_struct);
        } catch (Exception $e) {
            PrestaCafeNewPayu::addLog("payagain: createOrder: ".$e->getMessage(), 2);
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payagain_try_again.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payagain_try_again_ps17.tpl');
            }
            return;
        }

        if (!$create_response) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payagain_try_again.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payagain_try_again_ps17.tpl');
            }
            return;
        }

        $new_payment->payu_order_id = $create_response->orderId;
        if (!$new_payment->save()) {
            PrestaCafeNewPayu::addLog(
                "payagain: error updating PayUPayment ".Db::getInstance()->getMsgError()
                .", PayUPayment: ".var_export($new_payment, true),
                2
            );
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payagain_try_again.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/payagain_try_again_ps17.tpl');
            }
            return;
        }

        $direct_card_payment = Tools::getValue('pbl') == 'c';
        $available_languages = $direct_card_payment ?
            $this->module->card_payment_languages : $this->module->rest_payment_languages;
        $redirectUri = $create_response->redirectUri
            .'&lang='.PayUTools::coerceLanguageIsoCode($this->context->language->iso_code, $available_languages);

        // Change the order status to "Waiting for PayU". Otherwise, if PayU does not send
        // the "COMPLETED" notification before sending the customer back to the shop,
        // the customer would be presented the "payment failed" message.
        if ($order->current_state != PrestaCafeNewPayu::getOrderStateId()) {
            $history = new OrderHistory();
            $history->id_order = $order->id;
            $history->changeIdOrderState(
                PrestaCafeNewPayu::getOrderStateId(),
                $order->id
            );
            $history->addWithemail(true);
        }
        $this->redirect_after = $redirectUri;
    }

    // For PS 1.7
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        // So that the div.box look like on the order-detail page (white background and shadow)
        $page['body_classes']['page-order-detail'] = true;
        return $page;
    }
}
