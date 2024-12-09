<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

class RediconPaypoCloneModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if ($message = Tools::getValue('message', false)) {
            $this->errors[] = $message;
        }

        $id_cart = Tools::getValue('id_cart');

        if (Validate::isLoadedObject($cart = new Cart($id_cart))) {

            $duplicate = $cart->duplicate();

            if (isset($duplicate['success']) && $duplicate['success']) {
                $newCart = $duplicate['cart'];
                if ($newCart->id) {
                    $this->context->cookie->id_cart = (int) $newCart->id;
                    $this->context->cookie->write();
                }
            }

            if (method_exists('Order', 'getByCartId')) {
                //wersja 1.7+
                $order = Order::getByCartId($id_cart);
            } else {
                // wersja 1.6
                $order = new Order(Order::getOrderByCartId($id_cart));
            }

            if (Validate::isLoadedObject($order)) {
                $status = Configuration::get('REDICON_PAYPO_STATUS_CANCELLED') ? Configuration::get('REDICON_PAYPO_STATUS_CANCELLED') : Configuration::get('PS_OS_CANCELED');
                $order->setCurrentState($status);
            }
        }

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->redirectWithNotifications('index.php?controller=order&step=1');
        } else {
            Tools::redirect('index.php?controller=order&step=1');
        }
    }
}
