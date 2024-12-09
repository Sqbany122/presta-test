<?php
class CartRule extends CartRuleCore
{
    public $exclude_discounts = 0;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::$definition['fields']['exclude_discounts'] = array('type' => self::TYPE_INT, 'shop' => false, 'validate' => 'isUnsignedInt');
        parent::__construct($id, $id_lang, $id_shop);
    }

    /**
     * Return the cart rules Ids on the cart.
     * @param $filter
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getOrderedCartRulesIds($filter = CartRule::FILTER_ACTION_ALL)
    {
        $context = Context::getContext();
        $cache_key = 'Cart::getOrderedCartRulesIds_'.$context->cart->id.'-'.$filter.'-ids';
        if (!Cache::isStored($cache_key)) {
            $result = Db::getInstance()->executeS('
                SELECT cr.`id_cart_rule`
                FROM `'._DB_PREFIX_.'cart_cart_rule` cd
                LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
                LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (
                    cd.`id_cart_rule` = crl.`id_cart_rule`
                    AND crl.id_lang = '.(int)$context->cart->id_lang.'
                )
                WHERE `id_cart` = '.(int)$context->cart->id.'
                '.($filter == CartRule::FILTER_ACTION_SHIPPING ? 'AND free_shipping = 1' : '').'
                '.($filter == CartRule::FILTER_ACTION_GIFT ? 'AND gift_product != 0' : '').'
                '.($filter == CartRule::FILTER_ACTION_REDUCTION ? 'AND (reduction_percent != 0 OR reduction_amount != 0)' : '')
                .' ORDER BY cr.priority ASC'
            );
            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    /**
     * The arguments are optional and only serve as return values in case caller needs the details.
     */
    public function getAverageProductsTaxRate(&$cart_amount_te = null, &$cart_amount_ti = null)
    {
        $context = Context::getContext();
        $cart_amount_ti = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $cart_amount_te = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $cart_vat_amount = $cart_amount_ti - $cart_amount_te;

        if ($cart_vat_amount == 0 || $cart_amount_te == 0) {
            return 0;
        } else {
            return Tools::ps_round($cart_vat_amount / $cart_amount_te, 3);
        }
    }

    /**
     * The reduction value is POSITIVE
     *
     * @param bool $use_tax
     * @param Context $context
     * @param bool $use_cache Allow using cache to avoid multiple free gift using multishipping
     * @return float|int|string
     */
    public function getContextualValue($use_tax, Context $context = null, $filter = null, $package = null, $use_cache = true)
    {
        if (!CartRule::isFeatureActive()) {
            return 0;
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$filter) {
            $filter = CartRule::FILTER_ACTION_ALL;
        }
        $all_products = $context->cart->getProducts();
        $package_products = (is_null($package) ? $all_products : $package['products']);
        $reduction_value = 0;
        $cache_id = 'getContextualValue_'.(int)$this->id.'_'.(int)$use_tax.'_'.(int)$context->cart->id.'_'.(int)$filter;
        foreach ($package_products as $product) {
            $cache_id .= '_'.(int)$product['id_product'].'_'.(int)$product['id_product_attribute'].(isset($product['in_stock']) ? '_'.(int)$product['in_stock'] : '');
        }
        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }
        $all_cart_rules_ids = $this->getOrderedCartRulesIds();
        $cart_amount_ti = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $cart_amount_te = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        if ($this->free_shipping && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_SHIPPING))) {
            if (!$this->carrier_restriction) {
                $reduction_value += $context->cart->getOrderTotal($use_tax, Cart::ONLY_SHIPPING, is_null($package) ? null : $package['products'], is_null($package) ? null : $package['id_carrier']);
            } else {
                $data = Db::getInstance()->executeS('
                    SELECT crc.id_cart_rule, c.id_carrier
                    FROM '._DB_PREFIX_.'cart_rule_carrier crc
                    INNER JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)
                    WHERE crc.id_cart_rule = '.(int)$this->id.'
                    AND c.id_carrier = '.(int)$context->cart->id_carrier);
                if ($data) {
                    foreach ($data as $cart_rule) {
                        $reduction_value += $context->cart->getCarrierCost((int)$cart_rule['id_carrier'], $use_tax, $context->country);
                    }
                }
            }
        }
        if (in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_REDUCTION))) {
            if ($this->reduction_percent && $this->reduction_product == 0) {
                $order_total = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package_products);
                foreach ($context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT) as $cart_rule) {
                    $order_total -= Tools::ps_round($cart_rule['obj']->getContextualValue($use_tax, $context, CartRule::FILTER_ACTION_GIFT, $package), _PS_PRICE_COMPUTE_PRECISION_);
                }
                if (Module::isEnabled('x13excludediscounts') && $this->exclude_discounts == 2) {
                    foreach ($context->cart->getProducts() as $product) {
                        if ($product['reduction_applies'] && $product['id_product'] != $this->gift_product) {
                            $order_total -= ($use_tax ? $product['price_wt'] : $product['price']) * $product['cart_quantity'];
                        }
                    }

                    // remove gift product from calculation
                    if ($this->gift_product) {
                        foreach ($context->cart->getProducts() as $product) {
                            if ($product['id_product'] == $this->gift_product && $product['cart_quantity'] > 1) {
                                $order_total -= ($use_tax ? $product['price_wt'] : $product['price']) * 1;
                            }
                        }
                    }
                }

                $reduction_value += $order_total * $this->reduction_percent / 100;
            }
            if ($this->reduction_percent && $this->reduction_product > 0) {
                foreach ($package_products as $product) {
                    if ($product['id_product'] == $this->reduction_product) {
                        if (Module::isEnabled('x13excludediscounts') && $this->exclude_discounts == 2 && $product['reduction_applies']) {
                            continue;
                        }
                        $reduction_value += ($use_tax ? $product['total_wt'] : $product['total']) * $this->reduction_percent / 100;
                    }
                }
            }
            if ($this->reduction_percent && $this->reduction_product == -1) {
                $minPrice = false;
                $cheapest_product = null;
                foreach ($all_products as $product) {
                    if (Module::isEnabled('x13excludediscounts')) {
                        if ($product['reduction_applies'] && $product['id_product'] != $this->gift_product) {
                            continue;
                        }
                    }
                    $price = $product['price'];
                    if ($use_tax) {
                        $price *= (1 + $this->getAverageProductsTaxRate());
                    }
                    if ($price > 0 && ($minPrice === false || $minPrice > $price)) {
                        $minPrice = $price;
                        $cheapest_product = $product['id_product'].'-'.$product['id_product_attribute'];
                    }
                }
                $in_package = false;
                foreach ($package_products as $product) {
                    if ($product['id_product'].'-'.$product['id_product_attribute'] == $cheapest_product || $product['id_product'].'-0' == $cheapest_product) {
                        $in_package = true;
                    }
                }
                if ($in_package) {
                    $reduction_value += $minPrice * $this->reduction_percent / 100;
                }
            }
            if ($this->reduction_percent && $this->reduction_product == -2) {
                $selected_products_reduction = 0;
                $selected_products = $this->checkProductRestrictions($context, true);
                if (is_array($selected_products)) {
                    foreach ($package_products as $product) {
                        if (in_array($product['id_product'].'-'.$product['id_product_attribute'], $selected_products)
                            || in_array($product['id_product'].'-0', $selected_products)) {
                            $price = $product['price'];
                            if ($use_tax) {
                                $infos = Product::getTaxesInformations($product, $context);
                                $tax_rate = $infos['rate'] / 100;
                                $price *= (1 + $tax_rate);
                            }
                            if (Module::isEnabled('x13excludediscounts') && $this->exclude_discounts == 2) {
                                if ($product['reduction_applies']) {
                                    $price = 0;
                                }
                            }
                            $selected_products_reduction += $price * $product['cart_quantity'];
                        }
                    }
                }
                $reduction_value += $selected_products_reduction * $this->reduction_percent / 100;
            }
            if ((float)$this->reduction_amount > 0) {
                $prorata = 1;
                if (!is_null($package) && count($all_products)) {
                    $total_products = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS);
                    if ($total_products) {
                        $prorata = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package['products']) / $total_products;
                    }
                }
                $reduction_amount = $this->reduction_amount;
                if (isset($context->currency) && $this->reduction_currency != $context->currency->id) {
                    $voucherCurrency = new Currency($this->reduction_currency);
                    if ($reduction_amount == 0 || $voucherCurrency->conversion_rate == 0) {
                        $reduction_amount = 0;
                    } else {
                        $reduction_amount /= $voucherCurrency->conversion_rate;
                    }
                    $reduction_amount *= $context->currency->conversion_rate;
                    $reduction_amount = Tools::ps_round($reduction_amount, _PS_PRICE_COMPUTE_PRECISION_);
                }
                if ($this->reduction_tax == $use_tax) {
                    if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                        $cart_amount = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS);
                        $reduction_amount = min($reduction_amount, $cart_amount);
                    }
                    $reduction_value += $prorata * $reduction_amount;
                } else {
                    if ($this->reduction_product > 0) {
                        foreach ($context->cart->getProducts() as $product) {
                            if ($product['id_product'] == $this->reduction_product) {
                                $product_price_ti = $product['price_wt'];
                                $product_price_te = $product['price'];
                                $product_vat_amount = $product_price_ti - $product_price_te;
                                if ($product_vat_amount == 0 || $product_price_te == 0) {
                                    $product_vat_rate = 0;
                                } else {
                                    $product_vat_rate = $product_vat_amount / $product_price_te;
                                }
                                if ($this->reduction_tax && !$use_tax) {
                                    $reduction_value += $prorata * $reduction_amount / (1 + $product_vat_rate);
                                } elseif (!$this->reduction_tax && $use_tax) {
                                    $reduction_value += $prorata * $reduction_amount * (1 + $product_vat_rate);
                                }
                            }
                        }
                    }
                    elseif ($this->reduction_product == 0) {
                        $cart_amount_te = null;
                        $cart_amount_ti = null;
                        $cart_average_vat_rate = $this->getAverageProductsTaxRate($cart_amount_te, $cart_amount_ti);
                        if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                            $reduction_amount = min($reduction_amount, $this->reduction_tax ? $cart_amount_ti : $cart_amount_te);
                        }
                        if ($this->reduction_tax && !$use_tax) {
                            $reduction_value += $prorata * $reduction_amount / (1 + $cart_average_vat_rate);
                        } elseif (!$this->reduction_tax && $use_tax) {
                            $reduction_value += $prorata * $reduction_amount * (1 + $cart_average_vat_rate);
                        }
                    }
                    /*
                     * Reduction on the cheapest or on the selection is not really meaningful and has been disabled in the backend
                     * Please keep this code, so it won't be considered as a bug
                     * elseif ($this->reduction_product == -1)
                     * elseif ($this->reduction_product == -2)
                    */
                }
                if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                    $cart = Context::getContext()->cart;
                    if (!Validate::isLoadedObject($cart)) {
                        $cart = new Cart();
                    }
                    $cart_average_vat_rate = $this->getAverageProductsTaxRate();
                    $current_cart_amount = $use_tax ? $cart_amount_ti : $cart_amount_te;
                    if (Module::isEnabled('x13excludediscounts') && $this->exclude_discounts == 2) {
                        foreach ($context->cart->getProducts() as $product) {
                            if ($product['reduction_applies']) {
                                $current_cart_amount -= ($use_tax ? $product['price_wt'] : $product['price']) * $product['cart_quantity'];
                            }
                        }
                    }
                    foreach ($all_cart_rules_ids as $current_cart_rule_id) {
                        if ((int)$current_cart_rule_id['id_cart_rule'] == (int)$this->id) {
                            break;
                        }
                        $previous_cart_rule = new CartRule((int)$current_cart_rule_id['id_cart_rule']);
                        $previous_reduction_amount = $previous_cart_rule->reduction_amount;
                        if ($previous_cart_rule->reduction_tax && !$use_tax) {
                            $previous_reduction_amount = $prorata * $previous_reduction_amount / (1 + $cart_average_vat_rate);
                        } elseif (!$previous_cart_rule->reduction_tax && $use_tax) {
                            $previous_reduction_amount = $prorata * $previous_reduction_amount * (1 + $cart_average_vat_rate);
                        }
                        $current_cart_amount = max($current_cart_amount - (float)$previous_reduction_amount, 0);
                    }
                    $reduction_value = min($reduction_value, $current_cart_amount);
                }
            }
        }
        if ((int)$this->gift_product && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_GIFT))) {
            $id_address = (is_null($package) ? 0 : $package['id_address']);
            foreach ($package_products as $product) {
                if ($product['id_product'] == $this->gift_product && ($product['id_product_attribute'] == $this->gift_product_attribute || !(int)$this->gift_product_attribute)) {
                    if (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product])
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == $id_address
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0
                        || $id_address == 0
                        || !$use_cache) {
                        $reduction_value += ($use_tax ? $product['price_wt'] : $product['price']);
                        if ($use_cache && (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product]) || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0)) {
                            CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] = $id_address;
                        }
                        break;
                    }
                }
            }
        }
        Cache::store($cache_id, $reduction_value);
        return $reduction_value;
    }

    /**
     * Check if this cart rule can be applied
     *
     * @param Context $context
     * @param bool $alreadyInCart Check if the voucher is already on the cart
     * @param bool $display_error Display error
     * @return bool|mixed|string
     */
    public function checkValidity(Context $context, $alreadyInCart = false, $display_error = true, $check_carrier = true)
    {
        if ($this->exclude_discounts == 1 && Module::isEnabled('x13excludediscounts')) {
            $x13ExcludedDiscounts = Module::getInstanceByName('x13excludediscounts');
            $hasDiscounts = false;
            foreach ($context->cart->getProducts() as $product) {
                if ($product['reduction_applies']) {
                    $hasDiscounts = true;
                    break;
                }
            }
            
            if ($hasDiscounts) {
                return !$display_error ? false : $x13ExcludedDiscounts->renderErrorMessage();
            }
        }

        if ($this->exclude_discounts == 2 && Module::isEnabled('x13excludediscounts')) {
            $x13ExcludedDiscounts = Module::getInstanceByName('x13excludediscounts');
            $hasOnlyDiscountedProducts = true;
            foreach ($context->cart->getProducts() as $product) {
                if (!$product['reduction_applies']) {
                    $hasOnlyDiscountedProducts = false;
                }
            }

            if ($hasOnlyDiscountedProducts) {
                return !$display_error ? false : $x13ExcludedDiscounts->renderErrorMessage();
            }
        }
        
        return parent::checkValidity($context, $alreadyInCart, $display_error, $check_carrier);
    }
}
