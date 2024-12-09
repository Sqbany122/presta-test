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

class PayUTools
{
    /**
     * @param Cart $cart
     * @param string $discounts_string
     */
    public static function getOpenPayUProductList(Cart $cart, $discounts_string)
    {
        $products = array();

        foreach ($cart->getProducts() as $product) {
            $products[] = array(
                'name' => $product['name'],
                'unitPrice' => (string)(round($product['price_wt'] * 100, 0)),
                'quantity' => (string)($product['quantity'])
            );
        }

        // PayU does not allow negative amounts in order items, thus 0 is used and the
        // actual discount amount is specified in the description (including the minus sign).
        $total_discounts_tax_incl = (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
        if ($total_discounts_tax_incl > 0) {
            $products[] = array(
                'name' => $discounts_string,
                'unitPrice' => '-'.round($total_discounts_tax_incl * 100, 0),
                'quantity' => '1'
            );
        }

        return $products;
    }

    public static function getOpenPayUProductListFromOrder(Order $order, $discounts_string)
    {
        $products = array();

        foreach ($order->getProducts() as $product) {
            $products[] = array(
                'name' => $product['product_name'],
                'unitPrice' => (string)(round($product['product_price_wt'] * 100, 0)),
                'quantity' => (string)($product['product_quantity'])
            );
        }

        // PayU does not allow negative amounts in order items, thus 0 is used and the
        // actual discount amount is specified in the description (including the minus sign).
        $total_discounts_tax_incl = (float)abs($order->total_discounts_tax_incl);
        if ($total_discounts_tax_incl > 0) {
            $products[] = array(
                'name' => $discounts_string,
                'unitPrice' => '-'.round($total_discounts_tax_incl * 100, 0),
                'quantity' => '1'
            );
        }

        return $products;
    }

    /**
     * Retrieves a Cart from request parameters: id_cart, secure_token, secure_key_hash.
     * @return null|Cart
     */
    public static function getSsecureTokenCart()
    {
        // Load cart and validate the secure_key
        $cart = new Cart((int)Tools::getValue('id_cart'));
        if (!Validate::isLoadedObject($cart)
            || strcmp(
                sha1($cart->secure_key.Tools::getValue('secure_token')),
                Tools::getValue('secure_key_hash')
            )
        ) {
            return null;
        }
        return $cart;
    }
    /**
     * @param $cart
     * @return array of 'secure_key_hash', 'secure_token' and 'id_cart'
     */
    public static function getSsecureTokenCartParams($cart, $order = false)
    {
        $secure_token = sha1(mt_rand());
        $params = array(
            'secure_key_hash' => sha1($cart->secure_key.$secure_token),
            'secure_token' => $secure_token,
            'id_cart' => $cart->id,
        );
		if($order != false){
			$params['id_order'] = $order->id;
		};
        return $params;
    }

    public static function coerceLanguageIsoCode($iso_code, $available_languages, $fallback_iso_code = 'en')
    {
        if (in_array($iso_code, $available_languages)) {
            return $iso_code;
        } elseif ($available_languages) {
            return $available_languages[0];
        }
        return $fallback_iso_code;
    }

    /**
     * @param $header
     * @return array
     */
    public static function parseOpenpayuHeader($header)
    {
        $result = array();
        $name_values = explode(';', $header);
        foreach ($name_values as $name_value) {
            list($name, $value) = explode('=', $name_value);
            $result[$name] = $value;
        }
        return $result;
    }

    public static function verifyOpenpayuSignature($signature_header, $content, $second_key)
    {
        $sig_values = self::parseOpenpayuHeader($signature_header);
        if (!isset($sig_values['signature']) || !isset($sig_values['algorithm'])) {
            return false;
        }
        $source = $content.$second_key;
        if ($sig_values['algorithm'] == 'MD5') {
            if (strcmp(md5($source), $sig_values['signature']) == 0) {
                return true;
            }
        }
        if ($sig_values['algorithm'] == 'SHA1') {
            if (strcmp(sha1($source), $sig_values['signature']) == 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Order $order
     * @param $payment_id
     * @return OrderPayment
     */
    public static function findPayuPayment(Order $order, $payment_id)
    {
        foreach ($order->getOrderPayments() as $payment) {
            if ($payment->transaction_id == $payment_id && $payment->payment_method == 'PayU') {
                return $payment;
            }
        }
        return null;
    }

    private static function generateExtOrderId($id_cart)
    {
        // External order id (for PayU) must be as unique as possible because there is always
        // a possibility that the customer double clicks the payment block and the script that
        // is supposed to prevent it from happening does not prevent it from happening.
        $guid = time().'-'.mt_rand().'-' . Tools::substr(sha1(_COOKIE_KEY_), 0, 10);
        return "prestacafenewpayu.cart.$id_cart.$guid";
    }

    /**
     * @param Cart $cart
     * @param int $pos_id
     * @param string $module_name
     * @param string $description
     * @param string $discounts_string
     * @return array
     */
    public static function createCreateOrderStructFromCart(
        Cart $cart,
        $pos_id,
        $module_name,
        $description,
        $discounts_string,
        $shipping_pattern
    ) {
        $customer = new Customer($cart->id_customer);
        $currency = new Currency($cart->id_currency);
        $carrier = new Carrier($cart->id_carrier);
        $address_delivery = new Address($cart->id_address_delivery);
        $address_invoice = new Address($cart->id_address_invoice);
        $country_delivery = new Country($address_delivery->id_country);
        $country_invoice = new Country($address_invoice->id_country);
        $phone = self::getCustomerPhone($address_delivery, $address_invoice);
        $payu_initial_shipping = (int)round($cart->getTotalShippingCost() * 100, 0);
        // PayU will sum up the total amount and the shipping amount values,
        // that's why PayU totalAmount DOES NOT include shipping charges.
        $payu_initial_total = (int)round($cart->getOrderTotal() * 100, 0) - $payu_initial_shipping;

        // PayU requires the totalAmount to be of certain value, different for
        // various payment channels. In particular, totalAmount cannot be 0,
        // even if totalAmount + shipping is a sane amount. That's why in
        // certain circumstances shipping is moved to the product list.

        if ($payu_initial_total == 0) {
            $merge_shipping_and_products = true;
            $payu_initial_total = $payu_initial_shipping;
        } else {
            $merge_shipping_and_products = false;
        }

        $continue_secure_token = sha1(mt_rand());
        $continue_secure_key_hash = sha1($cart->secure_key.$continue_secure_token);
		
		$address_delivery_address2 = '';
		if (!empty($address_delivery->address2)) {
			$address_delivery_address2 = $address_delivery->address2;
		}
		
		$address_invoice_address2 = '';
		if (!empty($address_invoice->address2)) {
			$address_invoice_address2 = $address_invoice->address2;
		}
		
        $openpayu_struct = array(
            'extOrderId' => self::generateExtOrderId($cart->id),
            'notifyUrl' => Context::getContext()->link->getModuleLink(
                'prestacafenewpayu',
                'validation',
                array(),
                true
            ),
            'orderUrl' => Context::getContext()->link->getModuleLink(
                $module_name,
                'orderlink',
                array('id_cart' => $cart->id),
                true
            ),
            'continueUrl' => Context::getContext()->link->getModuleLink(
                $module_name,
                'confirmation',
                array(
                    'id_cart' => $cart->id,
                    'secure_key_hash' => $continue_secure_key_hash,
                    'secure_token' => $continue_secure_token,
                ),
                true
            ),
            'customerIp' => Tools::getRemoteAddr(),
            'merchantPosId' => (string)$pos_id,
            'description' => $description,
            'currencyCode' => $currency->iso_code,
            'totalAmount' => (string)$payu_initial_total,
            'buyer' => array(
                'extCustomerId' => $customer->id,
                'email' => $customer->email,
                'phone' => $phone,
                'firstName' => $customer->firstname,
                'lastName' => $customer->lastname,
                'delivery' => array(
                    'street' => trim($address_delivery->address1.' '.$address_delivery_address2),
                    'postalCode' => $address_delivery->postcode,
                    'city' => $address_delivery->city,
                    'countryCode' => $country_delivery->iso_code,
                    'name' => $address_delivery->alias,
                    'recipientName' => trim($address_delivery->firstname.' '.$address_delivery->lastname),
                    'recipientEmail' => $customer->email,
                    'recipientPhone' => $phone,
                ),
            ),
            'products' => self::getOpenPayUProductList($cart, $discounts_string)
        );

        if ($address_invoice->vat_number) {
            $openpayu_struct['buyer']['invoice'] = array(
                'street' => trim($address_invoice->address1.' '.$address_invoice_address2),
                'postalCode' => $address_invoice->postcode,
                'city' => $address_invoice->city,
                'countryCode' => $country_invoice->iso_code,
                'name' => $address_invoice->alias,
                'recipientName' => trim($address_invoice->firstname.' '.$address_invoice->lastname),
                'recipientEmail' => $customer->email,
                'recipientPhone' => $phone,
                'tin' => $address_invoice->vat_number
            );
        }

        // If, for instance, due to coupons or other circumstances the order total is 0 and only
        // the shipping is paid, the shipping must be moved to the products struct. Total for an
        // order cannot be 0.
        if ($merge_shipping_and_products) {
            // Add shipping
            $openpayu_struct['products'][] = array(
                'name' => sprintf($shipping_pattern, $carrier->name),
                'unitPrice' => (string)$payu_initial_shipping,
                'quantity' => 1
            );
        } else {
            $openpayu_struct['shippingMethods'] = array(
                array(
                    'name' => $carrier->name,
                    'price' => (string)$payu_initial_shipping,
                    'country' => $country_delivery->iso_code ? $country_delivery->iso_code : 'PL'
                )
            );
        }

        return $openpayu_struct;
    }

    /**
     * @param Order $order
     * @param int $pos_id
     * @param string $module_name
     * @param string $description
     * @param string $discounts_string
     * @return array
     */
    public static function createCreateOrderStructFromOrder(
        Order $order,
        $pos_id,
        $module_name,
        $description,
        $discounts_string,
        $shipping_pattern
    ) {
        $cart = new Cart($order->id_cart);
        $customer = new Customer($order->id_customer);
        $currency = new Currency($order->id_currency);
        $carrier = new Carrier($order->id_carrier);
        $address_delivery = new Address($order->id_address_delivery);
        $address_invoice = new Address($order->id_address_invoice);
        $country_delivery = new Country($address_delivery->id_country);
        $country_invoice = new Country($address_invoice->id_country);
        $phone = self::getCustomerPhone($address_delivery, $address_invoice);
        $payu_initial_shipping = (int)round($order->total_shipping_tax_incl * 100, 0);
        // PayU is summing up the total amount and the shipping amount values
        $payu_initial_total = (int)round($order->total_paid_tax_incl * 100, 0) - $payu_initial_shipping;

        if ($payu_initial_total == 0) {
            $merge_shipping_and_products = true;
            $payu_initial_total = $payu_initial_shipping;
        } else {
            $merge_shipping_and_products = false;
        }

        $continue_secure_token = sha1(mt_rand());
        $continue_secure_key_hash = sha1($cart->secure_key.$continue_secure_token);
		
		$address_delivery_address2 = '';
		if (!empty($address_delivery->address2)) {
			$address_delivery_address2 = $address_delivery->address2;
		}
		
		$address_invoice_address2 = '';
		if (!empty($address_invoice->address2)) {
			$address_invoice_address2 = $address_invoice->address2;
		}
		
        $openpayu_struct = array(
            'extOrderId' => self::generateExtOrderId($order->id_cart),
            'notifyUrl' => Context::getContext()->link->getModuleLink($module_name, 'validation', array(), true),
            'orderUrl' => Context::getContext()->link->getModuleLink(
                $module_name,
                'orderlink',
                array('id_cart' => $order->id_cart),
                true
            ),
            'continueUrl' => Context::getContext()->link->getModuleLink(
                $module_name,
                'confirmation',
                array(
                    'id_cart' => $order->id_cart,
                    'secure_key_hash' => $continue_secure_key_hash,
                    'secure_token' => $continue_secure_token,
                ),
                true
            ),
            'customerIp' => Tools::getRemoteAddr(),
            'merchantPosId' => (string)$pos_id,
            'description' => $description,
            'currencyCode' => $currency->iso_code,
            'totalAmount' => (string)$payu_initial_total,
            'buyer' => array(
                'extCustomerId' => $customer->id,
                'email' => $customer->email,
                'phone' => $phone,
                'firstName' => $customer->firstname,
                'lastName' => $customer->lastname,
                'delivery' => array(
                    'street' => trim($address_delivery->address1.' '.$address_delivery_address2),
                    'postalCode' => $address_delivery->postcode,
                    'city' => $address_delivery->city,
                    'countryCode' => $country_delivery->iso_code,
                    'name' => $address_delivery->alias,
                    'recipientName' => trim($address_delivery->firstname.' '.$address_delivery->lastname),
                    'recipientEmail' => $customer->email,
                    'recipientPhone' => $phone,
                ),
            ),
            'products' => self::getOpenPayUProductListFromOrder($order, $discounts_string)
        );

        if ($address_invoice->vat_number) {
            $openpayu_struct['buyer']['invoice'] = array(
                'street' => trim($address_invoice->address1.' '.$address_invoice_address2),
                'postalCode' => $address_invoice->postcode,
                'city' => $address_invoice->city,
                'countryCode' => $country_invoice->iso_code,
                'name' => $address_invoice->alias,
                'recipientName' => trim($address_invoice->firstname.' '.$address_invoice->lastname),
                'recipientEmail' => $customer->email,
                'recipientPhone' => $phone,
                'tin' => $address_invoice->vat_number
            );
        }

        // If, for instance, due to coupons or other circumstances the order total is 0 and only
        // the shipping is paid, the shipping must be moved to the products struct. Total for an
        // order cannot be 0.
        if ($merge_shipping_and_products) {
            // Add shipping
            $openpayu_struct['products'][] = array(
                'name' => sprintf($shipping_pattern, $carrier->name),
                'unitPrice' => (string)$payu_initial_shipping,
                'quantity' => 1
            );
            unset($openpayu_struct['shippingMethods']);
        } else {
            $openpayu_struct['shippingMethods'] = array(
                array(
                    'name' => $carrier->name,
                    'price' => (string)$payu_initial_shipping,
                    'country' => $country_delivery->iso_code ? $country_delivery->iso_code : 'PL'
                )
            );
        }

        return $openpayu_struct;
    }

    private static function getCustomerPhone(Address $address_delivery, Address $address_invoice)
    {
        $phones = array_filter(array(
            $address_delivery->phone_mobile,
            $address_invoice->phone_mobile,
            $address_delivery->phone,
            $address_invoice->phone
        ));
        if ($phones) {
            return current($phones);
        } else {
            return '';
        }
    }

    public static function calculateSurcharge($cart, $percentage, $min, $max)
    {
        // recalculate the surcharge
        $total = (int)round($cart->getOrderTotal() * 100, 0);
        $surcharge = (int)round($total * $percentage / 100.0, 0);
        if ($min > 0 && $surcharge < $min) {
            $surcharge = (int)$min;
        }
        if ($max > 0 && $surcharge > $max) {
            $surcharge = (int)$max;
        }
        return $surcharge;
    }

    public static function addSurchargeProductToCart($cart, $product)
    {
        // Product must be available for order (Product::available_for_order), otherwise updateQty won't work
        StockAvailable::setQuantity($product->id, null, 1000);
        $cart->deleteProduct($product->id);
        $cart->updateQty(1, $product->id, null, false, 'up');
        $cart->update();
    }

    public static function resetSurchargeProductPrice($product, $price)
    {
        $price_no_tax = round($price / ($product->tax_rate + 100.0), 8);
        $product->price = $price_no_tax;
        $product->save();

        $product->flushPriceCache();
    }

    public static function getOrderStateString($id_order_state, $id_language = null)
    {
        $os = new OrderState($id_order_state);
        if (!Validate::isLoadedObject($os)) {
            return "[$id_order_state]";
        }
        if (!$id_language) {
            $id_language = Context::getContext()->cookie->id_language;
        }
        $os_name = @$os->name[$id_language];
        if (!$os_name) {
            foreach ($os->name as $k => $n) {
                if ($n) {
                    $os_name = $n;
                    break;
                }
            }
        }
        return $os_name." [$id_order_state]";
    }
}
