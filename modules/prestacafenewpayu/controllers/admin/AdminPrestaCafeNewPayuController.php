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

class AdminPrestaCafeNewPayuController extends ModuleAdminController
{
    private $id_order;

    public function __construct()
    {
        parent::__construct();

        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payutools.php';

        $this->id_order = Tools::getValue('id_order');
    }

    public function postProcess()
    {
        $order = new Order($this->id_order);
        if ($this->sendPayagainEmail($order)) {
            $result = array('status' => 'ok');
        } else {
            $result = array('status' => 'error');
        }

        die(Tools::jsonEncode($result));
    }

    private function sendPayagainEmail($order)
    {
        // Send email to the customer with a link for paying again in case there is a problem
        $cart = new Cart($order->id_cart);
        $customer = new Customer($order->id_customer);
        $templateVars = array(
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{order_name}' => $order->reference,
            '{payu_pay_again_url}' => Context::getContext()->link->getModuleLink(
                'prestacafenewpayu',
                'payagain',
                PayUTools::getSsecureTokenCartParams($cart, $order)
            ),
        );

        PrestaCafeNewPayu::addLog("Sending 'pending' mail to $customer->email", 1);

        if (!Mail::Send(
            (int)Context::getContext()->cookie->id_lang,
            'payagain',
            $this->module->l('Try paying for your order again in PayU', 'AdminPrestaCafeNewPayuController'),
            $templateVars,
//'jan.b@a-creative.pl',
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'prestacafenewpayu/mails/',
            false,
            $cart->id_shop
        )) {
            PrestaCafeNewPayu::addLog("Failed sending 'payagain' email to $customer->email", 2);
            return false;
        }

        return true;
    }
}
