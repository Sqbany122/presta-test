<?php

class OrderOpcController extends OrderOpcControllerCore {

	public function cartReload() {
		$this->_assignSummaryInformations();
		if ($this->context->customer->id) {
			$this->context->smarty->assign('address_list', $this->context->customer->getAddresses($this->context->language->id));
		} else {
			$this->context->smarty->assign('address_list', array());
		}

		$this->context->smarty->assign('opc', TRUE);
		$this->context->smarty->assign('image_type', $this->getImageType());
		/*$this->setTemplate($this->getTemplatePath('shopping-cart.tpl'));
		$this->display();*/
		$this->smartyOutputContent($this->getTemplatePath('shopping-cart.tpl'));
		die();
	}

	public function handleVouchers() {
		FrontController::init();

		if (Tools::isSubmit('submitAddDiscount'))
		{
			if (!($code = trim(Tools::getValue('discount_name'))))
				$this->errors[] = Tools::displayError('You must enter a voucher code.');
			elseif (!Validate::isCleanHtml($code))
				$this->errors[] = Tools::displayError('The voucher code is invalid.');
			else
			{
				if (($cartRule = new CartRule(CartRule::getIdByCode($code))) && Validate::isLoadedObject($cartRule))
				{
					if ($error = $cartRule->checkValidity($this->context, false, true))
						$this->errors[] = $error;
					else
					{
						$this->context->cart->addCartRule($cartRule->id);
						$this->cartReload();
					}
				}
				else
					$this->errors[] = Tools::displayError('This voucher does not exists.');
			}
			$this->context->smarty->assign(array(
				'errors' => $this->errors,
				'discount_name' => Tools::safeOutput($code)
			));
		}
		elseif (($id_cart_rule = (int)Tools::getValue('deleteDiscount')) && Validate::isUnsignedId($id_cart_rule))
		{
			$this->context->cart->removeCartRule($id_cart_rule);
			$this->cartReload();
		}
		
		$this->cartReload();
	}

    public function init() {
        if (!$this->getStatus()) {
            return parent::init();
        }

        require_once _PS_MODULE_DIR_ . 'bestkit_opc/includer.php';

        /* only for our demo store --start */
        /* if (!$this->context->cookie->id_cart) {
            Tools::redirect('index.php?controller=cart&id_product=2&qty=1&ipa=2id_product_attribute=2&add=1&token=' . Tools::getToken(FALSE));
        } */
        /* only for our demo store --end */

        $this->moduleInstance = Module::getInstanceByName('bestkit_opc');
        $this->context->smarty->assign('opcModuleObj', $this->moduleInstance); //override
		if (Tools::isSubmit('submitAddDiscount') || Tools::getValue('deleteDiscount')) {
			//from v.1.6.3 [ajax for vouchers actions]
			$this->handleVouchers();
		}
		
		ParentOrderController::init();

        if ($this->nbProducts) {
            $this->context->smarty->assign('virtual_cart', FALSE);
        }

        $this->context->smarty->assign('is_multi_address_delivery', $this->context->cart->isMultiAddressDelivery() || ((int) Tools::getValue('multi-shipping') == 1));
        $this->context->smarty->assign('open_multishipping_fancybox', (int) Tools::getValue('multi-shipping') == 1);

        $this->isLogged = (bool) ($this->context->customer->id && Customer::customerIdExistsStatic((int) $this->context->cookie->id_customer));

        if ($this->context->cart->nbProducts()) {
            if (Tools::isSubmit('ajax')) {
                if (Tools::isSubmit('method')) {
                    switch (Tools::getValue('method')) {
                        case 'updateMessage':
                            if (Tools::isSubmit('message')) {
                                $txtMessage = urldecode(Tools::getValue('message'));
                                $this->_updateMessage($txtMessage);
                                if (count($this->errors)) {
                                    die('{"hasError" : TRUE, "errors" : ["' . implode('\',\'', $this->errors) . '"]}');
                                }

                                die(TRUE);
                            }
                            break;

                        case 'updateCarrierAndGetPayments':
                            if ((Tools::isSubmit('delivery_option') || Tools::isSubmit('id_carrier')) && Tools::isSubmit('recyclable') && Tools::isSubmit('gift') && Tools::isSubmit('gift_message')) {
                                $this->_assignWrappingAndTOS();
                                if ($this->_processCarrier()) {
                                    $carriers = $this->context->cart->simulateCarriersOutput();
                                    $return = array(
                                        'summary' => $this->context->cart->getSummaryDetails(),
                                        'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                                        'HOOK_PAYMENT' => $this->_getPaymentMethods(),
                                        'carrier_data' => $this->_getCarrierList(),
                                        'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array('carriers' => $carriers))
                                    );
                                    Cart::addExtraCarriers($return);
                                    die(Tools::jsonEncode($return));
                                } else {
                                    $this->errors[] = Tools::displayError('Error occurred while updating cart.');
                                }

                                if (count($this->errors)) {
                                    die('{"hasError" : TRUE, "errors" : ["' . implode('\',\'', $this->errors) . '"]}');
                                }

                                exit;
                            }
                            break;

                        case 'updateTOSStatusAndGetPayments':
                            if (Tools::isSubmit('checked')) {
                                $this->context->cookie->checkedTOS = (int) (Tools::getValue('checked'));
                                die(Tools::jsonEncode(array(
                                    'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                                    'HOOK_PAYMENT' => $this->_getPaymentMethods()
                                )));
                            }
                            break;

                        case 'getCarrierList':
                            die(Tools::jsonEncode($this->_getCarrierList()));
                        //break;

                        case 'editCustomer':
                            if (!$this->isLogged) {
                                exit;
                            }

                            if (Tools::getValue('years')) {
                                $this->context->customer->birthday = (int) Tools::getValue('years') . '-' . (int) Tools::getValue('months') . '-' . (int) Tools::getValue('days');
                            }

                            $_POST['lastname'] = Tools::getValue('customer_lastname');
                            $_POST['firstname'] = Tools::getValue('customer_firstname');
                            $this->errors = $this->context->customer->validateController();
                            $this->context->customer->newsletter = (int) Tools::isSubmit('newsletter');
                            $this->context->customer->optin = (int) Tools::isSubmit('optin');
                            $return = array(
                                'hasError' => !empty($this->errors),
                                'errors' => $this->errors,
                                'id_customer' => (int) $this->context->customer->id,
                                'token' => Tools::getToken(FALSE)
                            );
                            if (!count($this->errors)) {
                                $return['isSaved'] = (bool) $this->context->customer->update();
                            } else {
                                $return['isSaved'] = FALSE;
                            }

                            die(Tools::jsonEncode($return));
                        //break;

                        case 'getAddressBlockAndCarriersAndPayments':
                            if ($this->context->customer->isLogged())
                            {
                                // check if customer have addresses
                                if (!Customer::getAddressesTotalById($this->context->customer->id))
                                    die(Tools::jsonEncode(array('no_address' => 1)));
                                if (file_exists(_PS_MODULE_DIR_.'blockuserinfo/blockuserinfo.php'))
                                {
                                    include_once(_PS_MODULE_DIR_.'blockuserinfo/blockuserinfo.php');
                                    $blockUserInfo = new BlockUserInfo();
                                }
                                $this->context->smarty->assign('isVirtualCart', $this->context->cart->isVirtualCart());
                                $this->_processAddressFormat();
                                $this->_assignAddress();

                                if (!($formatedAddressFieldsValuesList = $this->context->smarty->getTemplateVars('formatedAddressFieldsValuesList')))
                                    $formatedAddressFieldsValuesList = array();

                                // Wrapping fees
                                $wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
                                $wrapping_fees_tax_inc = $wrapping_fees = $this->context->cart->getGiftWrappingPrice();
                                $return = array_merge(array(
                                        'order_opc_adress' => $this->context->smarty->fetch($this->moduleInstance->getTemplatePath('order-address.tpl')),
                                        'block_user_info' => (isset($blockUserInfo) ? $blockUserInfo->hookDisplayTop(array()) : ''),
                                        'formatedAddressFieldsValuesList' => $formatedAddressFieldsValuesList,
                                        'carrier_data' => $this->_getCarrierList(),
                                        'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                                        'HOOK_PAYMENT' => $this->_getPaymentMethods(),
                                        'no_address' => 0,
                                        'gift_price' => Tools::displayPrice(Tools::convertPrice(Product::getTaxCalculationMethod() == 1 ? $wrapping_fees : $wrapping_fees_tax_inc, new Currency((int)($this->context->cookie->id_currency))))
                                    ),
                                    $this->getFormatedSummaryDetail()
                                );
                                die(Tools::jsonEncode($return));
                            }
                            die(Tools::displayError());
                        //break;

                        case 'makeFreeOrder':
                            /* Bypass payment step if total is 0 */
                            if (($id_order = $this->_checkFreeOrder()) && $id_order) {
                                $order = new Order((int) $id_order);
                                $email = $this->context->customer->email;
                                if ($this->context->customer->is_guest) {
                                    $this->context->customer->logout(); // If guest we clear the cookie for security reason
                                }

                                die('freeorder:' . $order->reference . ':' . $email);
                            }

                            exit;
                        //break;

                        case 'updateAddressesSelected':
                            if ($this->context->customer->isLogged(TRUE)) {
                                $address_delivery = new Address((int) (Tools::getValue('id_address_delivery')));
                                $this->context->smarty->assign('isVirtualCart', $this->context->cart->isVirtualCart());
                                $address_invoice = ((int) (Tools::getValue('id_address_delivery')) == (int) (Tools::getValue('id_address_invoice')) ? $address_delivery : new Address((int) (Tools::getValue('id_address_invoice'))));
                                if ($address_delivery->id_customer != $this->context->customer->id || $address_invoice->id_customer != $this->context->customer->id) {
                                    $this->errors[] = Tools::displayError('This address is not yours.');
                                } elseif (!Address::isCountryActiveById((int) (Tools::getValue('id_address_delivery')))) {
                                    $this->errors[] = Tools::displayError('This address is not in a valid area.');
                                } elseif (!Validate::isLoadedObject($address_delivery) || !Validate::isLoadedObject($address_invoice) || $address_invoice->deleted || $address_delivery->deleted) {
                                    $this->errors[] = Tools::displayError('This address is invalid.');
                                } else {
                                    $this->context->cart->id_address_delivery = (int) (Tools::getValue('id_address_delivery'));
                                    $this->context->cart->id_address_invoice = Tools::isSubmit('same') ? $this->context->cart->id_address_delivery : (int) (Tools::getValue('id_address_invoice'));
                                    if (!$this->context->cart->update()) {
                                        $this->errors[] = Tools::displayError('An error occurred while updating your cart.');
                                    }

                                    // Address has changed, so we check if the cart rules still apply
                                    CartRule::autoRemoveFromCart($this->context);
                                    CartRule::autoAddToCart($this->context);

                                    if (!$this->context->cart->isMultiAddressDelivery()) {
                                        $this->context->cart->setNoMultishipping(); // As the cart is no multishipping, set each delivery address lines with the main delivery address
                                    }

                                    if (!count($this->errors)) {
                                        $result = $this->_getCarrierList();
                                        // Wrapping fees
                                        $wrapping_fees = (float) (Configuration::get('PS_GIFT_WRAPPING_PRICE'));
                                        $wrapping_fees_tax = new Tax((int) (Configuration::get('PS_GIFT_WRAPPING_TAX')));
                                        $wrapping_fees_tax_inc = $wrapping_fees * (1 + (((float) ($wrapping_fees_tax->rate) / 100)));
                                        $result = array_merge($result, array(
                                            'summary' => $this->context->cart->getSummaryDetails(),
                                            'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                                            'HOOK_PAYMENT' => $this->_getPaymentMethods(),
                                            'gift_price' => Tools::displayPrice(Tools::convertPrice(Product::getTaxCalculationMethod() == 1 ? $wrapping_fees : $wrapping_fees_tax_inc, new Currency((int) ($this->context->cookie->id_currency)))),
                                            'carrier_data' => $this->_getCarrierList()
                                        ));
                                        die(Tools::jsonEncode($result));
                                    }
                                }

                                if (count($this->errors)) {
                                    die(Tools::jsonEncode(array(
                                        'hasError' => TRUE,
                                        'errors' => $this->errors
                                    )));
                                }
                            }

                            die(Tools::displayError());
                        //break;

                        case 'multishipping':
                            $this->_assignSummaryInformations();
                            $this->context->smarty->assign('product_list', $this->context->cart->getProducts());

                            if ($this->context->customer->id) {
                                $this->context->smarty->assign('address_list', $this->context->customer->getAddresses($this->context->language->id));
                            } else {
                                $this->context->smarty->assign('address_list', array());
                            }

                            $this->setTemplate($this->getTemplatePath('order-address-multishipping-products.tpl'));
                            $this->display();
                            die();
                        //break;

                        case 'cartReload':
                            $this->cartReload();
                            die();
                        //break;

                        case 'noMultiAddressDelivery':
                            $this->context->cart->setNoMultishipping();
                            die();
                        //break;

                        default:
                            //throw new PrestaShopException('Unknown method "' . Tools::getValue('method') . '"');
                            break;
                    }
                } else {
                    throw new PrestaShopException('Method is not defined');
                }
            }
        } elseif (Tools::isSubmit('ajax')) {
            throw new PrestaShopException('Method is not defined');
        }
//print_r(new Collection('Group')); die;
        $this->context->smarty->assign(array(
            'fields_customer' => BestkitOpcCheckoutFields::getFieldsForStep('customer', $this->context->language->id),
            'fields_delivery' => BestkitOpcCheckoutFields::getFieldsForStep('delivery', $this->context->language->id),
            'fields_invoice' => BestkitOpcCheckoutFields::getFieldsForStep('invoice', $this->context->language->id),
            'customer_groups' => $this->getModuleConfig('allow_select_group') ? new Collection('Group', $this->context->language->id) : FALSE,
        ));
    }

    protected function _getCarrierList() {
        if (!$this->getStatus()) {
            return parent::_getCarrierList();
        }

        $address_delivery = new Address($this->context->cart->id_address_delivery);

        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, TRUE);
        if (!strpos($link_conditions, '?')) {
            $link_conditions .= '?content_only=1';
        } else {
            $link_conditions .= '&content_only=1';
        }

        // If a rule offer free-shipping, force hidding shipping prices
        $free_shipping = FALSE;
        foreach ($this->context->cart->getCartRules() as $rule) {
            if ($rule['free_shipping']) {
                $free_shipping = TRUE;
                break;
            }
        }

        $carriers = $this->context->cart->simulateCarriersOutput();
        $delivery_option = $this->context->cart->getDeliveryOption(NULL, FALSE, FALSE);
        $wrapping_fees = (float) (Configuration::get('PS_GIFT_WRAPPING_PRICE'));
        $wrapping_fees_tax = new Tax((int) (Configuration::get('PS_GIFT_WRAPPING_TAX')));
        $wrapping_fees_tax_inc = $wrapping_fees * (1 + (((float) ($wrapping_fees_tax->rate) / 100)));
        $vars = array(
            'free_shipping' => $free_shipping,
            'checkedTOS' => (int) ($this->context->cookie->checkedTOS),
            'recyclablePackAllowed' => (int) (Configuration::get('PS_RECYCLABLE_PACK')),
            'giftAllowed' => (int) (Configuration::get('PS_GIFT_WRAPPING')),
            'cms_id' => (int) (Configuration::get('PS_CONDITIONS_CMS_ID')),
            'conditions' => (int) (Configuration::get('PS_CONDITIONS')),
            'link_conditions' => $link_conditions,
            'recyclable' => (int) ($this->context->cart->recyclable),
            'gift_wrapping_price' => (float) (Configuration::get('PS_GIFT_WRAPPING_PRICE')),
            'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, $this->context->currency),
            'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, $this->context->currency),
            'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
            'carriers' => $carriers,
            'checked' => $this->context->cart->simulateCarrierSelectedOutput(),
            'delivery_option' => $delivery_option,
            'address_collection' => $this->context->cart->getAddressCollection(),
            'opc' => TRUE,
            'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
                'carriers' => $carriers,
                'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
                'delivery_option' => $delivery_option
            ))
        );

        Cart::addExtraCarriers($vars);

        $this->context->smarty->assign($vars);

        if (!Address::isCountryActiveById((int) ($this->context->cart->id_address_delivery)) && $this->context->cart->id_address_delivery != 0) {
            $this->errors[] = Tools::displayError('This address is not in a valid area.');
        } elseif ((!Validate::isLoadedObject($address_delivery) || $address_delivery->deleted) && $this->context->cart->id_address_delivery != 0) {
            $this->errors[] = Tools::displayError('This address is invalid.');
        } else {
            $result = array(
                'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
                    'carriers' => $carriers,
                    'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
                    'delivery_option' => $this->context->cart->getDeliveryOption(NULL, TRUE)
                )),
                'carrier_block' => $this->context->smarty->fetch($this->getTemplatePath('order-carrier.tpl'))
            );
            Cart::addExtraCarriers($result);
            return $result;
        }

        if (count($this->errors)) {
            return array(
                'hasError' => TRUE,
                'errors' => $this->errors,
                'carrier_block' => $this->context->smarty->fetch($this->getTemplatePath('order-carrier.tpl'))
            );
        }
    }

    public function getLayout() {
        if (!$this->getStatus()) {
            return parent::getLayout();
        } else {
            $this->context->smarty->assign(array(
                'image_type' => $this->getImageType(),
                'style_tpl' => self::getTemplatePath('style-' . ($this->getModuleConfig('style') ? $this->getModuleConfig('style') : '3columns') . '.tpl'),
            ));

            $layout =$this->getTemplatePath('layout.tpl');
        }

        return $layout;
    }

    public function setMedia() {
        parent::setMedia();

        if ($this->getStatus()) {
            $this->removeJS(_THEME_JS_DIR_ . 'order-opc.js');
            $this->removeJS(_THEME_JS_DIR_ . 'cart-summary.js');
            $this->addJS($this->getModuleUrl() . '/js/order-opc.js');
            $this->addJS($this->getModuleUrl() . '/js/cart-summary.js');

            $this->addCSS($this->getModuleUrl() . '/css/styles.css');
            $this->addCSS($this->getModuleUrl() . '/css/skin/' . $this->getModuleConfig('skin'));
        }
    }

    public function display() {
        if ($this->getStatus() && !Tools::isSubmit('method')) {
            $this->template = $this->getTemplatePath('order-opc.tpl');
        }

        return parent::display();
    }

    /*public function removeJS($file) {
        foreach ($this->js_files as $key => $js_file) {
            if ($js_file == $file) {
                unset($this->js_files[$key]);
            }
        }
    }*/

    protected function getImageType(){
        return ImageType::getFormatedName('small');
    }

    public function getModuleConfig($node) {
        $module = Module::getInstanceByName('bestkit_opc');
        return $module->getConfig($node);
    }

    public function getModuleUrl() {
        return __PS_BASE_URI__ . 'modules/bestkit_opc';
    }

    public function getModulePath() {
        return _PS_MODULE_DIR_ . 'bestkit_opc';
    }

    public function getTemplatePath($file = '', $type = 'front') {
        return $this->getModulePath() . DS . 'views' . DS . 'templates' . DS . $type . DS . $file;
    }

    protected function getStatus($moduleName = 'bestkit_opc') {
        return (Module::isEnabled($moduleName) ); //&& !$this->context->getMobileDevice()
    }

}

