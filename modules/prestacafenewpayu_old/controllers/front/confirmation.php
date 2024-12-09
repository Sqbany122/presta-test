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
 *
 * v2.2.0 - removed order validation from this class as it was deemed too dangerous
 *          and prone to introduce errors in the payment flow.
 *
 * Class PrestacafenewpayuConfirmationModuleFrontController
 */
class PrestacafenewpayuConfirmationModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public $display_column_right = false;
    /**
     * @var PrestaCafeNewPayu
     */
    public $module;

    public function postProcess()
    {
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payupayment.php';
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payutools.php';
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/restapi.php';

        $cart = PayUTools::getSsecureTokenCart();

        // Invalid cart parameters, nothing can be done
        if (!$cart) {
            PrestaCafeNewPayu::addLog('confirmation: called with invalid parameters', 2);
            $this->redirect_after = Context::getContext()->link->getPageLink('order');
            return;
        }

        // No PayU payments are present and yet there were correct secure parameters,
        // something is seriously wrong here.
        $last_payment = PayUPayment::getLastPaymentByCartId($cart->id);
        if (!$last_payment) {
            if (PrestaCafeNewPayu::getData('warn_confirmation_before_validation')) {
                PrestaCafeNewPayu::addLog(
                    "confirmation: no PayUPayment for cart #$cart->id (order #".Order::getOrderByCartId($cart->id)
                    .") was found",
                    2
                );
                $this->sendWarningMail('confirmation_missing_payment', $cart, Order::getOrderByCartId($cart->id), '', '');
            }
            $this->context->smarty->assign(array(
                'shop_name' => (string) Configuration::get('PS_SHOP_NAME')
            ));

            // The customer should not see the error message but the owner will get an email
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('confirmation_thank_you.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/confirmation_thank_you_ps17.tpl');
            }
            return;
        }

        // At this point at least a PENDING status should have been sent by PayU
        // thus creating an order. If no order is found, something is very wrong.
        if (!$cart->orderExists()) {
            PrestaCafeNewPayu::addLog(
                "confirmation: order for cart #$cart->id and payment {$last_payment->payu_order_id} "
                ."({$last_payment->payu_payment_id}) not found",
                2
            );
            $this->sendWarningMail(
                'validation_fail',
                $cart,
                false,
                $last_payment->payu_order_id,
                $last_payment->payu_payment_id
            );

            $this->context->smarty->assign(array(
                'shop_name' => (string) Configuration::get('PS_SHOP_NAME')
            ));

            // The customer should not see the error message but the owner will get an email
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('confirmation_thank_you.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/confirmation_thank_you_ps17.tpl');
            }
            return;
        }

        if ($last_payment->payu_order_status == PayUPayment::STATUS_CANCELED) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('confirmation_try_again.tpl');
            } else {
                $this->setTemplate('module:prestacafenewpayu/views/templates/front/confirmation_try_again_ps17.tpl');
            }
            return;
        }

        $order = new Order(Order::getOrderByCartId($cart->id));
        $customer = new Customer($order->id_customer);
        $this->redirect_after = Context::getContext()->link->getPageLink(
            'order-confirmation',
            true,
            null,
            'id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$order->id
            .'&key='.$customer->secure_key
        );
    }

    private function sendWarningMail($template, $cart, $id_order, $payu_order_id, $payu_payment_id)
    {
        // Send a warning email to the shop owner
        $mailVars = array(
            'id_cart' => $cart->id,
            'id_order' => $id_order,
            'payu_order_id' => $payu_order_id,
            'payu_payment_id' => $payu_payment_id,
        );

        if (!Mail::Send(
            (int)$this->context->cookie->id_lang,
            $template,
            Mail::l('Probable problem with PayU notifications', (int)$this->context->cookie->id_lang),
            $mailVars,
            Configuration::get('PS_SHOP_EMAIL'),
            'Administrator',
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'prestacafenewpayu/mails/',
            false,
            $cart->id_shop
        )) {
            PrestaCafeNewPayu::addLog(
                "confirmation: failed sending 'validation_fail' email to ".Configuration::get('PS_SHOP_EMAIL'),
                2
            );
        }
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
