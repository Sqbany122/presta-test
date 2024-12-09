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

class PrestacafenewpayuValidationModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    protected $content_only = true;
    /**
     * @var PrestaCafeNewPayu
     */
    public $module;

    public function postProcess()
    {
        if (Tools::getValue('test') === '1') {
            if (PrestaCafeNewPayu::getData('log_notification_all')) {
                $data = array(
                    '_server' => $_SERVER,
                    'GET' => $_GET,
                    'input' => Tools::file_get_contents('php://input')
                );
                PrestaCafeNewPayu::addDebug('Notification test', 1, 'validation', $data);
            }
            die('TEST_OK');
        }

        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payupayment.php';
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payutools.php';
        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/restapi.php';

        try {
            // This header is always required
            if (!isset($_SERVER['HTTP_OPENPAYU_SIGNATURE'])) {
                $data = array(
                    '_server' => $_SERVER,
                    'GET' => $_GET,
                    'input' => Tools::file_get_contents('php://input')
                );
                PrestaCafeNewPayu::addDebug(
                    'Missing Openpayu-Signature header in notification',
                    2,
                    'validation',
                    $data
                );

                self::dieWithError("Received notification without the OpenPayU-Signature header");
            }

            // Read and decode the JSON notification. Save the original JSON string for
            // calculating the signature (HTTP_OPENPAYU_SIGNATURE).
            $jsonStr = Tools::file_get_contents('php://input');

            $notification = Tools::jsonDecode($jsonStr);
            if (!$notification
                    || !property_exists($notification, 'order')
                    || !property_exists($notification->order, 'orderId')
                    || !property_exists($notification->order, 'status')
                    || !property_exists($notification->order, 'totalAmount')) {
                $data = array(
                    '_server' => $_SERVER,
                    'GET' => $_GET,
                    'input' => $jsonStr
                );
                PrestaCafeNewPayu::addDebug('Invalid JSON in notification', 2, 'validation', $data);

                self::dieWithError("Malformed JSON");
            }

            // Retrieve the payment object which must exist.
            if (!($payment = PayUPayment::findByPayuOrderId($notification->order->orderId))) {
                $data = array(
                    '_server' => $_SERVER,
                    'GET' => $_GET,
                    'input' => $jsonStr
                );
                PrestaCafeNewPayu::addDebug(
                    "[{$notification->order->orderId}] Notification for non existing payment",
                    2,
                    'validation',
                    $data
                );

                // If the payment object does not exist, the controller sends HTTP 200 anyway.
                die;
            }

            // PayU may send notifications asynchronously, ie. not in order. Any notification
            // received after the payment is already 'COMPLETED' or 'CANCELED' should be ignored.
            if ($payment->payu_order_status == PayUPayment::STATUS_COMPLETED
                    || $payment->payu_order_status == PayUPayment::STATUS_CANCELED) {
                if (PrestaCafeNewPayu::getData('log_notification_all')) {
                    $data = array(
                        '_server' => $_SERVER,
                        'GET' => $_GET,
                        'input' => $jsonStr
                    );
                    PrestaCafeNewPayu::addDebug(
                        "[{$notification->order->orderId}] Ignored notification, ".
                            "payment already in state {$payment->payu_order_status}",
                        1,
                        'validation',
                        $data
                    );
                }

                die;
            }

            // Update payment properties

            // Look for payment_id.
            if (property_exists($notification, 'properties')) {
                foreach ($notification->properties as $prop) {
                    if (property_exists($prop, 'name')
                            && property_exists($prop, 'value')
                            && $prop->name == 'PAYMENT_ID') {
                        $payment->payu_payment_id = $prop->value;
                    }
                }
            }

            if (property_exists($notification->order, 'currencyCode')) {
                $payment->payu_currency_code = $notification->order->currencyCode;

                if ($payment->payu_currency_code != $payment->iso_currency) {
                    $data = array(
                        '_server' => $_SERVER,
                        'GET' => $_GET,
                        'input' => $jsonStr
                    );
                    PrestaCafeNewPayu::addDebug(
                        "[{$notification->order->orderId}] Currency in notification differs from ".
                            "currency in payment ({$payment->iso_currency})",
                        2,
                        'validation',
                        $data
                    );

                    self::dieWithError("Invalid currency for payment");
                }
            }

            if (property_exists($notification->order, 'totalAmount')) {
                $payment->payu_total_amount = $notification->order->totalAmount;
            }

            // Update payment status.
            $payment->payu_order_status = $notification->order->status;

            $cart = new Cart($payment->id_cart);
            if (!Validate::isLoadedObject($cart)) {
                $data = array(
                    '_server' => $_SERVER,
                    'GET' => $_GET,
                    'input' => $jsonStr
                );
                PrestaCafeNewPayu::addDebug(
                    "[{$notification->order->orderId}] Missing cart #{$payment->id_cart}",
                    2,
                    'validation',
                    $data
                );

                self::dieWithError("Missing cart #$payment->id_cart for payment");
            }

            $pos_second_key = PrestaCafeNewPayu::getData('second_key_'.$payment->iso_currency);
            if (!self::verifySignature($jsonStr, $cart->id_shop, $pos_second_key)) {
                $data = array(
                    '_server' => $_SERVER,
                    'GET' => $_GET,
                    'input' => $jsonStr
                );
                PrestaCafeNewPayu::addDebug(
                    "[{$notification->order->orderId}] Invalid signature in notification",
                    2,
                    'validation',
                    $data
                );

                self::dieWithError("OpenPayU signature failed validation");
            }

            $payments = PayUPayment::getPaymentsByCartId($payment->id_cart);
            $this_is_latest = $payments[0]['id_prestacafenewpayu_payment'] == $payment->id;

            if (!$this_is_latest) {
                $data = array(
                    '_server' => $_SERVER,
                    'GET' => $_GET,
                    'input' => $jsonStr
                );
                PrestaCafeNewPayu::addDebug(
                    "[{$notification->order->orderId}] Ignoring notification (not latest payment)",
                    2,
                    'validation',
                    $data
                );

                die;
            }

            $customer = new Customer($cart->id_customer);
            $shop = new Shop($cart->id_shop);

            $this->resetContext($cart, $customer, $shop);

            // Log this regardless of the log_notification_all setting
            // if (PrestaCafeNewPayu::getData('log_notification_all')) {
                $data = array(
                    '_server' => $_SERVER,
                    'GET' => $_GET,
                    'input' => $jsonStr
                );
                PrestaCafeNewPayu::addDebug(
                    "[{$notification->order->orderId}] Valid notification for payment: ".
                        $notification->order->status,
                    1,
                    'validation',
                    $data
                );
            // }

            // Validate the order or change its state.
            if ($payment->payu_order_status == PayUPayment::STATUS_COMPLETED) {
                // The money is now available to the merchant on their PayU account.
                if (!$cart->orderExists()) {
                    $this->module->validateOrderWithSurcharge(
                        $cart,
                        PrestaCafeNewPayu::getData('surcharge_product_'.$payment->iso_currency),
                        $payment->payu_surcharge,
                        Configuration::get('PS_OS_PAYMENT'),
                        Tools::ps_round($notification->order->totalAmount / 100.0, 2),
                        $customer->secure_key
                    );
                } else {
					
                    $order = new Order(Order::getOrderByCartId($cart->id));

                    // PS_OS_OUTOFSTOCK_UNPAID is set automatically by PrestaShop if an unavailable
                    // product is ordered

                    if ($order->current_state == PrestaCafeNewPayu::getOrderStateId()
                        || $order->current_state == Configuration::get('PS_OS_ERROR')
                        || $order->current_state == Configuration::get('PS_OS_OUTOFSTOCK')  // PS 1.5
                        || $order->current_state == Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')   // PS 1.6
                        || PrestaCafeNewPayu::getData('set_os_payment_in_any_os')
                    ) {
						
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
						
                        $history->changeIdOrderState(
                            Configuration::get('PS_OS_PAYMENT'),
                            $order->id,
                            true
                        );
						
                        $history->addWithemail(true);
						
						// Zmieniamy statusy innych zamówień o tym samym card_id
						
						$sql = 'SELECT `id_order`
						FROM `'._DB_PREFIX_.'orders`
						WHERE id_order != '.$order->id.' AND `id_cart` = '.(int)$cart->id;
											
						if ($result = Db::getInstance()->executeS($sql)) {
							
							if(isset($result[0]['id_order']) && (int)$result[0]['id_order'] > 0)
							{
								foreach($result as $my_order){
									$history = new OrderHistory();
									$history->id_order = $my_order['id_order'];
									
									$history->changeIdOrderState(
										Configuration::get('PS_OS_PAYMENT'),
										$my_order['id_order'],
										true
									);
								}
							}
						}
                    } else {
                        $data = array(
                            '_server' => $_SERVER,
                            'GET' => $_GET,
                            'input' => $jsonStr
                        );
                        PrestaCafeNewPayu::addDebug(
                            "[{$notification->order->orderId}] Cannot change order #$order->id to PS_OS_PAYMENT, ".
                                "current order state is unsupported: ".
                                PayUTools::getOrderStateString($order->current_state),
                            2,
                            'validation',
                            $data
                        );
                    }
                }

            } elseif ($notification->order->status == PayUPayment::STATUS_CANCELED
                    || $notification->order->status == PayUPayment::STATUS_REJECTED) {
                // The payment was cancelled or rejected by the merchant (in the PayU panel).
                if (!$cart->orderExists()) {
                    $this->module->validateOrderWithSurcharge(
                        $cart,
                        PrestaCafeNewPayu::getData('surcharge_product_'.$payment->iso_currency),
                        $payment->payu_surcharge,
                        Configuration::get('PS_OS_ERROR'),
                        0,
                        $customer->secure_key
                    );
                } else {
                    $order = new Order(Order::getOrderByCartId($cart->id));

                    // If the PayUPayment is canceled it means that payagain canceled it
                    // and the order state need not be changed.
                    if ($order->current_state == PrestaCafeNewPayu::getOrderStateId()
                        || $order->current_state == Configuration::get('PS_OS_OUTOFSTOCK')  // PS 1.5
                        || $order->current_state == Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')   // PS 1.6
                    ) {
                        $id_order = Order::getOrderByCartId($cart->id);
                        $history = new OrderHistory();
                        $history->id_order = $id_order;
                        $history->changeIdOrderState(
                            Configuration::get('PS_OS_ERROR'),
                            $id_order
                        );
                        $history->addWithemail(true);
						
						// Zmieniamy statusy innych zamówień o tym samym card_id
						
						$sql = 'SELECT `id_order`
						FROM `'._DB_PREFIX_.'orders`
						WHERE id_order != '.$order->id.' AND `id_cart` = '.(int)$cart->id;

						if ($result = Db::getInstance()->executeS($sql)) {
							
							if(isset($result[0]['id_order']) && (int)$result[0]['id_order'] > 0)
							{
								foreach($result as $my_order){
									
									$history = new OrderHistory();
									$history->id_order = $my_order['id_order'];
									
									$history->changeIdOrderState(
										Configuration::get('PS_OS_ERROR'),
										$my_order['id_order'],
										true
									);
								}
							}
						}
                    } else {
                        $data = array(
                            '_server' => $_SERVER,
                            'GET' => $_GET,
                            'input' => $jsonStr
                        );
                        PrestaCafeNewPayu::addDebug(
                            "[{$notification->order->orderId}] Cannot change order #$order->id to PS_OS_ERROR, ".
                                "current order state is unsupported: ".
                                PayUTools::getOrderStateString($order->current_state),
                            2,
                            'validation',
                            $data
                        );
                    }
                }
            } elseif ($notification->order->status == PayUPayment::STATUS_PENDING
                    || $notification->order->status == PayUPayment::STATUS_WAITING_FOR_CONFIRMATION) {
                // The payment is waiting to be processed or accepted/rejected by the merchant.
                // This state can last for a long time thus the order is being validated now.
                // If the order already exists, there is no point in changing anything about it yet.
                if (!$cart->orderExists()) {
                    $this->module->validateOrderWithSurcharge(
                        $cart,
                        PrestaCafeNewPayu::getData('surcharge_product_'.$payment->iso_currency),
                        $payment->payu_surcharge,
                        PrestaCafeNewPayu::getOrderStateId(),
                        Tools::ps_round($notification->order->totalAmount / 100.0, 2),
                        $customer->secure_key
                    );
                } else {
                    $data = array(
                        '_server' => $_SERVER,
                        'GET' => $_GET,
                        'input' => $jsonStr
                    );
                    PrestaCafeNewPayu::addDebug(
                        "[{$notification->order->orderId}] Cannot accept notification with status ".
                            "{$notification->order->status}, order already validated",
                        2,
                        'validation',
                        $data
                    );
                }
            }

            if ($cart->orderExists()) {
                $the_order = new Order(Order::getOrderByCartId($cart->id));

                // I don't like it but it is necessary. If stock management is on, PaymentModule
                // will automatically set the "Out of stock" status which in turn is invoicable.
                // Invoice generation adds a payment automatically and it's this payment we need
                // to update with PayU-generated id.

                foreach ($the_order->getOrderPayments() as $order_payment) {
                    if (!$order_payment->transaction_id
//                        && $order_payment->amount == Tools::ps_round($payment->payu_total_amount / 100.0, 2)
                        && preg_match('/\bpayu\b/i', $order_payment->payment_method) === 1
                    ) {
                        $order_payment->transaction_id = $payment->payu_payment_id;
                        $order_payment->amount = Tools::ps_round($payment->payu_total_amount / 100.0, 2);
                        $order_payment->save();
                        break;
                    }
                }
            }

            // Update the invoice address if allowed. Create a new Address entity only if necessary.

            if (PrestaCafeNewPayu::getData('update_invoice_from_payu')) {
                if ($cart->orderExists()) {
                    if (property_exists($notification->order, 'buyer')
                        && property_exists($notification->order->buyer, 'invoice')
                    ) {
                        $tin = property_exists($notification->order->buyer->invoice, 'tin') ?
                            $notification->order->buyer->invoice->tin : '';
                        $recipientName = property_exists($notification->order->buyer->invoice, 'recipientName') ?
                            $notification->order->buyer->invoice->recipientName : '';
                        $street = property_exists($notification->order->buyer->invoice, 'street') ?
                            $notification->order->buyer->invoice->street : '';
                        $postalCode = property_exists($notification->order->buyer->invoice, 'postalCode') ?
                            $notification->order->buyer->invoice->postalCode : '';
                        $city = property_exists($notification->order->buyer->invoice, 'city') ?
                            $notification->order->buyer->invoice->city : '';
                        $countryCode = property_exists($notification->order->buyer->invoice, 'countryCode') ?
                            $notification->order->buyer->invoice->countryCode : '';

                        // Wyszukać adres o nazwie PayU i użyć go jeżeli się da albo zaktualizować

                        $the_order = new Order(Order::getOrderByCartId($cart->id));
                        $old_address_invoice = new Address($the_order->id_address_invoice);

                        $id_payu_address = self::findCustomerAddress($customer->id, 'PayU');
                        if ($id_payu_address) {
                            $payu_address = new Address($id_payu_address);
                            $payu_country = new Country($payu_address->id_country);
                            $payu_country_iso_code = $payu_country->iso_code;
                        } else {
                            $payu_address = new Address;
                            $payu_address->id_customer = $customer->id;
                            $payu_address->alias = 'PayU';
                            $payu_address->firstname = $old_address_invoice->firstname;
                            $payu_address->lastname = $old_address_invoice->lastname;
                            $payu_country_iso_code = null;
                        }

                        if ($tin && $recipientName && $street && $postalCode && $city && $countryCode
                            && ($payu_address->vat_number != $tin
                                || $payu_address->company != $recipientName
                                || $payu_address->address1 != $street
                                || $payu_address->postcode != $postalCode
                                || $payu_country_iso_code != $countryCode)) {
                            $payu_address->vat_number = $tin;
                            $payu_address->company = $recipientName;
                            $payu_address->address1 = $street;
                            $payu_address->postcode = $postalCode;
                            $payu_address->city = $city;
                            $payu_address->id_country =
                                Country::getByIso($notification->order->buyer->invoice->countryCode);
                            if ($payu_address->save() && $the_order->id_address_invoice != $payu_address->id) {
                                $the_order->id_address_invoice = $payu_address->id;
                                $the_order->save();
                            }
                        }
                    }
                }
            }

            // PayUPayment is saved as the last step, when we are sure that the order is
            // properly updated.
            if (!$payment->save()) {
                $data = array(
                    '_server' => $_SERVER,
                    'GET' => $_GET,
                    'input' => $jsonStr
                );
                PrestaCafeNewPayu::addDebug(
                    "[{$notification->order->orderId}] Cannot save payment: ".Db::getInstance()->getMsgError(),
                    3,
                    'validation',
                    $data
                );

                self::dieWithError("Payment update failed: ".Db::getInstance()->getMsgError());
            }
        } catch (Exception $e) {
            $data = array(
                '_server' => $_SERVER,
                'GET' => $_GET,
                'input' => $jsonStr
            );
            PrestaCafeNewPayu::addDebug(
                "[{$notification->order->orderId}] Unexpected error: ".$e->getMessage(),
                3,
                'validation',
                $data
            );

            self::dieWithError("Unexpected error: ".$e->getMessage());
        }
        die;
    }

    /**
     * Dies with the supplied error message and HTTP status 400. If $admin_log is supplied,
     * it is logged to the PrestaShopLogger (if available) or error_log.
     * @param $public_log string Log message visible to the HTTP client.
     * @param string|null $admin_log Log message logged to the system log.
     * @param int $severity Log severity as defined by Prestashop.
     */
    private static function dieWithError($public_log)
    {
        header('HTTP/1.1 400 Error');
        die($public_log);
    }

    private static function verifySignature($jsonStr, $id_shop, $second_key)
    {
        if (!$second_key) {
            PrestaCafeNewPayu::addLog("validation: second key is not configured in shop #$id_shop", 2);
            return false;
        }

        if (!PayUTools::verifyOpenpayuSignature($_SERVER['HTTP_OPENPAYU_SIGNATURE'], $jsonStr, $second_key)) {
            return false;
        }

        return true;
    }

    private static function findCustomerAddress($id_customer, $alias)
    {
        $result = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_address`
            FROM `'._DB_PREFIX_.'address`
            WHERE `id_customer` = '.(int)$id_customer.
            ' AND `alias` = \''.pSQL($alias).'\'
            AND `deleted` = 0 AND `active` = 1'
        );
        return $result;
    }

    /**
     * shiptopay, among other modules, expects to have a nearly fully functional
     * Context at $this->context.
     */
    private function resetContext(Cart $cart, Customer $customer, Shop $shop)
    {
        if (!$this->context) {
            $this->context = Context::getContext();
        }

        if (!$this->context->cart || !$this->context->cart->id) {
            $this->context->cart = $cart;
            $this->context->customer = $customer;
        }

        if (!$this->context->shop || !$this->context->shop->id) {
            $this->context->shop = $shop;
        }
    }
}