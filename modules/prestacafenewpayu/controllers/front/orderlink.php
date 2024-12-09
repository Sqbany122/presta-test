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
 * PayU allows passing a link to the order details. Yet at the time the payment is created
 * we don't know the final order id yet. This controller takes a cart's id, checks the
 * cart ownership and redirects to the order details.
 *
 * TODO: guest-tracking
 */
class PrestacafenewpayuOrderLinkModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public $display_column_right = false;

    public function postProcess()
    {
        $cart = new Cart(Tools::getValue('id_cart'));
        if (!Validate::isLoadedObject($cart)
                || $cart->id_customer != $this->context->customer->id
                || !$cart->orderExists()) {
            $this->redirect_after = $this->context->link->getPageLink('history');
            return;
        }

        $this->redirect_after = $this->context->link->getPageLink(
            'order-detail',
            true,
            $this->context->language->id,
            array('id_order' => Order::getOrderByCartId($cart->id))
        );
    }
}
