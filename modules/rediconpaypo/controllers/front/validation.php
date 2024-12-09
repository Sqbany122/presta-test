<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// $autoloadPath = 'vendor/autoload.php';
// if (file_exists($autoloadPath)) {
//     require_once $autoloadPath;
// }
// use PrestaShop\Module\Rediconpaypo\Helper\PaypoLog;
// use PrestaShop\Module\Rediconpaypo\Paypo;

require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Paypo.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/PaypoLog.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/classes/PaypoTransaction.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/classes/Statustransaction.php";

class RediconPaypoValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;
        $context = $this->context;

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            $this->errors[] = $this->module->l('Brak wybranego produktu na stanie lub sprawdź poprawność wymaganych danych (adresy,dane kupującego)', 'validation');
            $this->redirectWithNotifications('index.php?controller=cart&action=show');
        }

        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'rediconpaypo') {
                $authorized = true;
                break;
            }
        }

        $currency = $this->context->currency;

        $settings = $this->module->getModuleSettings($currency->iso_code);

        if (!$authorized || !$settings) {
            die('This payment method is not available.');
        }

        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);

        $mailVars = array();

        $this->module->validateOrder(
            (int) $cart->id,
            Configuration::get('REDICON_PAYPO_STATUS_NEW'),
            $total,
            $this->module->displayName,
            null,
            $mailVars,
            (int) $currency->id,
            false,
            $customer->secure_key
        );
        
        try {
            $order_id = (int) $this->module->currentOrder;
            $order = new Order($order_id);
            $paypo = new Paypo($order, $settings);

            $transaction = $paypo->createTransaction((int) $this->module->id);

            if (isset($transaction['response']) && $transaction['response']->getRedirectUrl()) {
                $obj = new PaypoTransaction();
                $obj->create((int) $cart->id, $order_id, $transaction);
                $redirectUrl = $transaction['response']->getRedirectUrl();
            } else {
                $this->errors[] = $this->module->l('Ups... nie udało się wykonać płatności z paypo.', 'validation');
                $redirectUrl = $context->link->getModuleLink('rediconpaypo', 'clone', array('id_cart' => $order->id_cart), true);
            }
        } catch (Exception $e) {

            PaypoLog::log($e->getMessage());

            $msgs = [
                'Bad Request' => $this->module->l('Bład płatności próbuj ponownie lub skontaktuj się ze sprzedawcą.', 'validation'),
            ];
            $msg = isset($msgs[$e->getMessage()]) ? $msgs[$e->getMessage()] : $e->getMessage();

            $this->errors[] = $msg;
            $redirectUrl = $context->link->getModuleLink('rediconpaypo', 'clone', array(
                'id_cart' => $order->id_cart,
                'message' => $msg,
            ), true);
        }

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->redirectWithNotifications($redirectUrl);
        } else {
            Tools::redirect($redirectUrl);
        }
    }

}
