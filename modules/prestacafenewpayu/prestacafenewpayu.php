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

class PrestaCafeNewPayu extends PaymentModule
{
    /** @var array Data (module configuration) cache */
    private static $data;
    private $postErrors = array();
    private $new_version_available = false;

    private static $payu_order_state_id = false;

    const PAYU_API_HOST = 'secure.payu.com';

    public $rest_payment_languages;
    public $card_payment_languages;
    public $limited_currencies;
    public $pbl_allowed_values;

    public function __construct()
    {
        $this->name = 'prestacafenewpayu';
        $this->tab = 'payments_gateways';
        $this->version = '2.6.14';
        $this->author = 'PrestaCafe';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $max_version = _PS_VERSION_;
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
            // Certain early 1.5 versions check the max version erroneously
            // (version_compare(_PS_VERSION_, max) >= 0) instead of > 0.
            $v = explode('.', $max_version);
            $v[3] = $v[3] + 1;
            $max_version = join('.', $v);
        }
        $this->ps_versions_compliancy = array('min' => '1.5.0.17', 'max' => $max_version);

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        parent::__construct();

        $this->displayName = $this->l('PayU payments');
        $this->description = $this->l('Accept payments by PayU');

        $this->limited_countries = array('PL');
        $this->limited_currencies = array('PLN', 'EUR', 'USD', 'GBP', 'CZK');

        $this->rest_payment_languages = array('en', 'pl', 'cs');
        $this->card_payment_languages = array('en', 'pl', 'cs', 'bg', 'de', 'el', 'hu', 'sk', 'sl', 'ro', 'ru');
        $this->pbl_allowed_values = array('c', 'b');
    }

    public function install()
    {
        $iso_code = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));

        if (in_array($iso_code, $this->limited_countries) == false) {
            $this->_errors[] = $this->l('This module is not available in your country');
            return false;
        }

        include(_PS_MODULE_DIR_ . 'prestacafenewpayu/sql/install.php');

        $result = parent::install() &&
            $this->installTab() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('backOfficeFooter') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('displayAdminOrderTabOrder') &&
            $this->registerHook('displayAdminOrderContentOrder');

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $result = $result &&
                $this->registerHook('paymentOptions');
        } else {
            $result = $result &&
                $this->registerHook('payment') &&
                $this->registerHook('displayPaymentEU');
        }

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->registerHook('displayAdminOrder');
        }

        if (!$result) {
            return false;
        }

        // Previous incarnation of this module also created a new order state. Let's try to find and reuse it.
        if (!self::getOrderStateId()) {
            $os = new OrderState(Configuration::getGlobalValue('PSPPL_OS_PAYU'));
            if (self::isValidOldPayUOrderState($os)) {
                $os->module_name = $this->name;
                $os->save();
            } else {
                $added = self::addNewOrderState(
                    array(
                        'en' => 'Awaiting PayU payment',
                        'pl' => 'Oczekiwanie na PayU'
                    ),
                    '#A6C307'
                );
                if (!$added) {
                    return false;
                }
            }
        }

        return true;
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminPrestaCafeNewPayu';

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'PayU';
        }

        $tab->id_parent = -1;   // invisible tab
        $tab->module = $this->name;

        return $tab->add();
    }

    public function uninstall()
    {
        include(_PS_MODULE_DIR_ . 'prestacafenewpayu/sql/uninstall.php');
        return $this->uninstallTab() && parent::uninstall();
    }

    private function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminPrestaCafeNewPayu');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }

        return true;
    }

    private function testValidationLink()
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException($this->l('cURL not available, cannot test the notification url.'));
        }

        $notification_url = $this->context->link->getModuleLink(
            'prestacafenewpayu',
            'validation',
            array('test' => '1'),
            true
        );

        $ch = @curl_init($notification_url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) {
            self::addLog(
                "testValidationLink: GET $notification_url failed with error $error ($errno)",
                2
            );
            throw new RuntimeException(
                sprintf(
                    $this->l('Network connection to notification url %s failed (%s)'),
                    $notification_url,
                    $error
                )
            );
        }

        if ($result !== 'TEST_OK') {
            self::addLog(
                "testValidationLink: GET $notification_url yielded http code $http_code and result $result",
                2
            );
            throw new RuntimeException($this->l('Notification url test failed'));
        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        // If the shop is upgraded to 1.7, the module is not upgraded since its version
        // has not changed! That's why we always perform this check for the 1.7 specific
        // hooks.
        if (version_compare(_PS_VERSION_, '1.7', '>=') && !$this->isRegisteredInHook('paymentOptions')) {
            $this->registerHook('paymentOptions');
            $this->unregisterHook('payment');
            $this->unregisterHook('displayPaymentEU');
        }

        $messages = array();
        $warnings = array();
        $errors = array();

        if (Module::getInstanceByName('shiptopay') && Module::getInstanceByName('shiptopay')->active) {
            $warnings[] = $this->l('We detected the Ship To Pay module. Please enable the PayU module in the Ship To Pay module configuration.');
        }

        $output = '';

        if (Tools::isSubmit('btnSubmitPayu')) {
            if (Configuration::getGlobalValue('PS_SHOP_ENABLE')) {
                try {
                    $this->testValidationLink();
                    $messages[] = $this->l('Notification url test was successful');
                } catch (Exception $e) {
                    $warnings[] = $e->getMessage();
                }
            } else {
                $warnings[] = $this->l('Your store is in maintenance mode, the module will not function properly.');
            }

            $this->postValidation();
            if (!count($this->postErrors)) {
                if ($this->postProcess()) {
                    $messages[] = $this->l('Settings saved');
                }
            } else {
                foreach ($this->postErrors as $err) {
                    $errors[] = $err;
                }
            }
        } else {
            $output .= '<br />';
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        if (Configuration::get('PS_ALLOW_MULTISHIPPING')) {
            $errors[] = $this->l('Multishipping is enabled but this module is not fully compatible with it.') . ' ' .
                $this->l('The PayU payment will not be possible for multi-shipping orders.');
        }
        if (extension_loaded('curl') == false) {
            $errors[] = $this->l('You have to enable the cURL extension on your server to use this module');
        }

        $this->context->smarty->assign('errors', $errors);
        $this->context->smarty->assign('warnings', $warnings);
        $this->context->smarty->assign('messages', $messages);
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/messages.tpl');

        if ($this->new_version_available) {
            $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/update.tpl');
        }

        $this->context->smarty->assign('validation_link', Context::getContext()->link->getModuleLink(
            'prestacafenewpayu',
            'validation',
            array(),
            true
        ));

        if ($this->context->language->iso_code == 'pl') {
            $this->context->smarty->assign(
                'support_link',
                'https://prestacafe.pl/pl/kontakt'
            );
        } else {
            $this->context->smarty->assign(
                'support_link',
                'https://prestacafe.pl/en/contact-us'
            );
        }

        $this->context->smarty->assign('img_dir', _PS_IMG_);
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/logo_panel.tpl');
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/tabs.tpl');
        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        // If submit_action is not an empty string, it is appended to the form's action url.
        // In this case, switching shops in a multistore setup tricks the module into thinking
        // the form has been submitted, as submit_action is still in the query string but no
        // form data has been submitted. This problem manifests itself in Prestashop 1.5.
        $helper->submit_action = '';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(), // AdminControllerCore
            'id_language' => $this->context->language->id,
        );

        $forms = array($this->getConfirmFormGeneral());
        foreach ($this->limited_currencies as $iso_currency) {
            $forms[] = $this->getConfigFormPos($iso_currency);
        }
        $forms[] = $this->getConfirmFormTroubleshooting();

        return $helper->generateForm($forms);
    }

    private function getConfirmFormGeneral()
    {
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('General settings'),
                    'icon' => 'icon-cogs',
                ),
            ),
        );

        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => $this->l('Allow changing order state to \'Payment accepted\' at any time'),
            'name' => 'set_os_payment_in_any_os',
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('This option allows the module to set the \'Payment accepted\' state regardless of the order\'s current state. If this option is disabled, the \'Payment accepted\' state is set automatically only when the order\'s current state is one of: \'Payment error\', \'Awaiting PayU payment\', \'Out of stock\''),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );
        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => $this->l('Update invoice data from PayU'),
            'name' => 'update_invoice_from_payu',
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('If the customer requests an invoice on the PayU site and fills out the invoice data form, this data is imported back into the customer\'s address in the order.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );

        if (version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
            $form['form']['input'][1]['prefix'] = '<i class="icon icon-cog"></i>';
        }

        $form['form']['submit'] = array(
            'title' => $this->l('Save'),
            'name' => 'btnSubmitPayu',
        );

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $form['form']['submit']['class'] = 'button';
        }

        return $form;
    }

    private function getConfirmFormTroubleshooting()
    {
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Troubleshooting'),
                    'icon' => 'icon-cogs',
                ),
            ),
        );

        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => $this->l('Disable JavaScript in payment block'),
            'name' => 'disable_javascript_payment_block',
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('Set this option to \'Yes\' if you experience problems with nonstandard checkout modules.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );

        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => $this->l('Record all HTTP communication to database'),
            'name' => 'log_http_all',
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('This option turns on recording all HTTP communication into a database table. Serious errors are always logged.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );

        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => $this->l('Record all notifications to database'),
            'name' => 'log_notification_all',
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('This option turns on recording all notifications into a database table. Serious errors are always logged.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );

        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => $this->l('Send warning when notification too late'),
            'name' => 'warn_confirmation_before_validation',
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('Send information to the shop owner if a customer returns to the store after successful payment but PayU has not yet confirmed the payment. This may be temporary slowdown of the PayU servers but also a problem with the store. Enable this setting if you experience problems with orders not being confirmed by PayU.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );

        if (version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
            $form['form']['input'][0]['prefix'] = '<i class="icon icon-cog"></i>';
        }

        $form['form']['submit'] = array(
            'title' => $this->l('Save'),
            'name' => 'btnSubmitPayu',
        );

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $form['form']['submit']['class'] = 'button';
        }

        return $form;
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigFormPos($iso_currency)
    {
        require_once _PS_MODULE_DIR_ . 'prestacafenewpayu/classes/payutools.php';

        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => sprintf($this->l('%s POS settings'), $iso_currency),
                    'icon' => 'icon-cogs',
                ),
            ),
        );

        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => sprintf($this->l('Enable POS for currency %s'), $iso_currency),
            'name' => 'enable_pos_'.$iso_currency,
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );

        $form['form']['input'][] = array(
            'col' => 3,
            'type' => 'text',
            'size' => 50,
            'prefix' => '<i class="icon icon-cog"></i>',
            'name' => 'pos_id_'.$iso_currency,
            'label' => sprintf($this->l('POS ID (pos_id) for %s'), $iso_currency),
        );
        $form['form']['input'][] = array(
            'col' => 3,
            'type' => 'text',
            'size' => 50,
            'prefix' => '<i class="icon icon-cog"></i>',
            'name' => 'second_key_'.$iso_currency,
            'label' => sprintf($this->l('Second key (MD5) for %s'), $iso_currency),
        );
        $form['form']['input'][] = array(
            'col' => 3,
            'type' => 'text',
            'size' => 50,
            'prefix' => '<i class="icon icon-cog"></i>',
            'name' => 'key_'.$iso_currency,
            'label' => sprintf($this->l('OAuth protocol - client_secret for %s'), $iso_currency),
        );

        // Prestashop 1.6 (Bootstrap) uses nice looking "switch" control while
        // Prestashop 1.5 needs "radio" control.
        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => sprintf($this->l('Show the general payment block for %s'), $iso_currency),
            'name' => 'basic_payment_'.$iso_currency,
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('The general payment block redirects the customer to the PayU site where they can choose the payment method and optionally log in to their PayU account.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );
        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => sprintf($this->l('Show the card payment block for %s'), $iso_currency),
            'name' => 'direct_card_'.$iso_currency,
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('The card payment block takes the customer directly to the PayU site where they can only pay with a credit card. This is suitable for international customers who cannot use the quick transfers.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );
        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => sprintf($this->l('Display payment channels in the store for %s'), $iso_currency),
            'name' => 'display_payment_methods_'.$iso_currency,
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('The general payment block will take the customer to an interim page in the store where all payment methods available in PayU are displayed. The customer will be able to choose a payment method directly in the store.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );

        $form['form']['input'][] = array(
            'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
            'label' => sprintf($this->l('Send email with a link for retrying payment for %s'), $iso_currency),
            'name' => 'send_payment_email_'.$iso_currency,
            'is_bool' => true,
            'required' => true,
            'class' => 't',
            'desc' => $this->l('When the customer is taken to the PayU site, the module sends them an email with a link that the customer can use to try paying again if anything goes wrong with the original payment. The module will not allow the customer to pay again for an order which has already been paid for.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );

        // Jaki produkt:
        // PS 1.5: Wirtualny, Status: dostępny, Widoczność: nigdzie: Opcje: sprzedaż
        // PS 1.6: Wirtualny, Włączony: tak, Widoczność: nigdzie, Opcje: sprzedaż
        // PS 1.7: Dropdown: wirtualny, Opcje: ma się pojawiać nigdzie,

        $form['form']['input'][] = array(
            'col' => 3,
            'type' => 'text',
            'size' => 50,
            'prefix' => '<i class="icon icon-cog"></i>',
            'suffix' => '%',
            'name' => 'surcharge_'.$iso_currency,
            'label' => $this->l('Surcharge'),
            'desc' => $this->l('Optional surcharge as a percentage of the transaction total amount.')
        );
        $form['form']['input'][] = array(
            'col' => 3,
            'type' => 'text',
            'size' => 50,
            'prefix' => '<i class="icon icon-cog"></i>',
            'suffix' => $iso_currency,
            'name' => 'surcharge_min_'.$iso_currency,
            'label' => sprintf($this->l('Minimum surcharge for %s'), $iso_currency),
            'desc' => sprintf($this->l('Optional minimum value for the surcharge in %s'), $iso_currency)
        );
        $form['form']['input'][] = array(
            'col' => 3,
            'type' => 'text',
            'size' => 50,
            'prefix' => '<i class="icon icon-cog"></i>',
            'suffix' => $iso_currency,
            'name' => 'surcharge_max_'.$iso_currency,
            'label' => sprintf($this->l('Maximum surcharge for %s'), $iso_currency),
            'desc' => sprintf($this->l('Optional maximum value for the surcharge in %s'), $iso_currency)
        );
        $form['form']['input'][] = array(
            'type' => 'select',
            'name' => 'surcharge_product_'.$iso_currency,
            'label' => sprintf($this->l('Virtual product for %s'), $iso_currency),
            'desc' => $this->l('Select a virtual product from the store\'s main category. This product must be marked as active but may not (and should not) be visible. This product will be added to the cart as the surcharge line item.'),
            'options' => array(
                'query' => $this->getSurchargeProducts(),
                'id' => 'id',
                'name' => 'name',
            )
        );

        if (version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
            $form['form']['input'][1]['prefix'] = '<i class="icon icon-cog"></i>';
        }

        $form['form']['submit'] = array(
            'title' => $this->l('Save'),
            'name' => 'btnSubmitPayu',
        );

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $form['form']['submit']['class'] = 'button';
        }

        return $form;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        if (Tools::isSubmit('btnSubmitPayu')) {
            $values = array(
                'set_os_payment_in_any_os' => Tools::getValue('set_os_payment_in_any_os') ? '1' : '0',
                'update_invoice_from_payu' => Tools::getValue('update_invoice_from_payu') ? '1' : '0',
                'disable_javascript_payment_block' => Tools::getValue('disable_javascript_payment_block') ? '1' : '0',
                'log_http_all' => Tools::getValue('log_http_all') ? '1' : '0',
                'log_notification_all' => Tools::getValue('log_notification_all') ? '1' : '0',
                'warn_confirmation_before_validation' => Tools::getValue('warn_confirmation_before_validation') ? '1' : '0',
            );
            foreach ($this->limited_currencies as $iso_currency) {
                $values['enable_pos_'.$iso_currency] = Tools::getValue('enable_pos_'.$iso_currency) ? '1' : '0';
                $values['pos_id_'.$iso_currency] = Tools::getValue('pos_id_'.$iso_currency);
                $values['key_'.$iso_currency] = Tools::getValue('key_'.$iso_currency);
                $values['second_key_'.$iso_currency] = Tools::getValue('second_key_'.$iso_currency);
                $values['direct_card_'.$iso_currency] = Tools::getValue('direct_card_'.$iso_currency) ? '1' : '0';
                $values['basic_payment_'.$iso_currency] = Tools::getValue('basic_payment_'.$iso_currency) ? '1' : '0';
                $values['send_payment_email_'.$iso_currency] = Tools::getValue('send_payment_email_'.$iso_currency)
                    ? '1' : '0';
                $values['display_payment_methods_'.$iso_currency] =
                    Tools::getValue('display_payment_methods_'.$iso_currency) ? '1' : '0';
                $values['surcharge_'.$iso_currency] = Tools::getValue('surcharge_'.$iso_currency);
                $values['surcharge_min_'.$iso_currency] = Tools::getValue('surcharge_min_'.$iso_currency);
                $values['surcharge_max_'.$iso_currency] = Tools::getValue('surcharge_max_'.$iso_currency);
                $values['surcharge_product_'.$iso_currency] = Tools::getValue('surcharge_product_'.$iso_currency);
            }
        } else {
            $values = array(
                'set_os_payment_in_any_os' => self::getData('set_os_payment_in_any_os'),
                'update_invoice_from_payu' => self::getData('update_invoice_from_payu'),
                'disable_javascript_payment_block' => self::getData('disable_javascript_payment_block'),
                'log_http_all' => self::getData('log_http_all'),
                'log_notification_all' => self::getData('log_notification_all'),
                'warn_confirmation_before_validation' => self::getData('warn_confirmation_before_validation'),
            );
            foreach ($this->limited_currencies as $iso_currency) {
                $values['enable_pos_'.$iso_currency] = self::getData('enable_pos_'.$iso_currency);
                $values['pos_id_'.$iso_currency] = self::getData('pos_id_'.$iso_currency);
                $values['key_'.$iso_currency] = self::getData('key_'.$iso_currency);
                $values['second_key_'.$iso_currency] = self::getData('second_key_'.$iso_currency);
                $values['direct_card_'.$iso_currency] = self::getData('direct_card_'.$iso_currency);
                $values['basic_payment_'.$iso_currency] = self::getData('basic_payment_'.$iso_currency);
                if (!$values['basic_payment_'.$iso_currency]) {
                    $values['basic_payment_'.$iso_currency] = true;
                }
                $values['send_payment_email_'.$iso_currency] = self::getData('send_payment_email_'.$iso_currency);
                $values['display_payment_methods_'.$iso_currency] =
                    self::getData('display_payment_methods_'.$iso_currency);
                $values['surcharge_'.$iso_currency] = self::getData('surcharge_'.$iso_currency);
                $values['surcharge_min_'.$iso_currency] = self::getData('surcharge_min_'.$iso_currency);
                $values['surcharge_max_'.$iso_currency] = self::getData('surcharge_max_'.$iso_currency);
                $values['surcharge_product_'.$iso_currency] = self::getData('surcharge_product_'.$iso_currency);
            }
        }
        return $values;
    }

    /**
     * Validate user input data.
     */
    private function postValidation()
    {
        require_once _PS_MODULE_DIR_ . 'prestacafenewpayu/classes/validate.php';

        if (Tools::isSubmit('btnSubmitPayu')) {
            $any_pos_enabled = false;
            foreach ($this->limited_currencies as $iso_currency) {
                if (Tools::getValue('enable_pos_'.$iso_currency)) {
                    $any_pos_enabled = true;

                    if (!trim(Tools::getValue('pos_id_'.$iso_currency))) {
                        $this->postErrors[] =
                            sprintf($this->l('%s: value for POS ID (pos_id) is required'), $iso_currency);
                    }
                    if (!trim(Tools::getValue('key_'.$iso_currency))) {
                        $this->postErrors[] = sprintf($this->l('%s: value for Key (MD5) is required'), $iso_currency);
                    }
                    if (!trim(Tools::getValue('second_key_'.$iso_currency))) {
                        $this->postErrors[] =
                            sprintf($this->l('%s: value for Second key (MD5) is required'), $iso_currency);
                    }
                    if (!Tools::getValue('basic_payment_'.$iso_currency)
                            && !Tools::getValue('direct_card_'.$iso_currency)) {
                        $this->postErrors[] = sprintf(
                            $this->l('%s: you must enable at least of the options: \'Show the general payment block\', \'Show the card payment block\''),
                            $iso_currency
                        );
                    }

                    $surcharge_percentage = str_replace(',', '.', trim(Tools::getValue('surcharge_'.$iso_currency)));
                    $surcharge_min = str_replace(',', '.', trim(Tools::getValue('surcharge_min_'.$iso_currency)));
                    $surcharge_max = str_replace(',', '.', trim(Tools::getValue('surcharge_max_'.$iso_currency)));
                    $surcharge_product = (int)trim(Tools::getValue('surcharge_product_'.$iso_currency));

                    // Surcharge percentage must be empty or a non-negative float
                    if ($surcharge_percentage &&
                            !PrestaCafePayuValidate::isSurchargePercentage($surcharge_percentage)) {
                        $this->postErrors[] = sprintf(
                            $this->l('%s: surcharge percentage is invalid'),
                            $iso_currency
                        );
                    }
                    // Surcharge minimum must be empty or a non-negative float
                    if ($surcharge_min && !PrestaCafePayuValidate::isSurchargeMin($surcharge_min)) {
                        $this->postErrors[] = sprintf(
                            $this->l('%s: minimum surcharge amount invalid'),
                            $iso_currency
                        );
                    }
                    // Surcharge maximum must be empty or a non-negative float
                    if ($surcharge_max && !PrestaCafePayuValidate::isSurchargeMax($surcharge_max)) {
                        $this->postErrors[] = sprintf(
                            $this->l('%s: maximum surcharge amount invalid'),
                            $iso_currency
                        );
                    }

                    if ($surcharge_percentage && !$surcharge_product) {
                        $this->postErrors[] = sprintf(
                            $this->l('%s: surcharge product must be selected when the surcharge is specified'),
                            $iso_currency
                        );
                    }

                    if ($surcharge_product && !$surcharge_percentage) {
                        $this->postErrors[] = sprintf(
                            $this->l('%s: surcharge must be specified when the surcharge product is selected'),
                            $iso_currency
                        );
                    }

                    if (($surcharge_min || $surcharge_max) && !$surcharge_percentage) {
                        $this->postErrors[] = sprintf(
                            $this->l('%s: surcharge must be specified when the surcharge minimum or maximum value is specified'),
                            $iso_currency
                        );
                    }

                    if ($surcharge_min && $surcharge_max && $surcharge_min > $surcharge_max) {
                        $this->postErrors[] = sprintf(
                            $this->l('%s: surcharge minimum value must not be larger than surcharge maximum value'),
                            $iso_currency
                        );
                    }

                    // Connection test
                    $pos_id = trim(Tools::getValue('pos_id_'.$iso_currency));
                    $key = trim(Tools::getValue('key_'.$iso_currency));
                    if ($pos_id && $key) {
                        require_once _PS_MODULE_DIR_ . 'prestacafenewpayu/classes/restapi.php';
                        $api = new PrestaCafePayuApi($pos_id, $key);
                        try {
                            $payMethodsAssoc = $api->getPayMethodsAssoc();

                            if (Tools::getValue('direct_card_' . $iso_currency)) {
                                if (!isset($payMethodsAssoc['c']) || $payMethodsAssoc['c']->status != 'ENABLED') {
                                    $this->postErrors[] = sprintf(
                                        $this->l('%s: card payment is not available for this POS,') . ' '
                                        . $this->l('please disable the \'Show card payment block\' setting.'),
                                        $iso_currency
                                    );
                                }
                            }
                        } catch (Exception $e) {
                            PrestaCafeNewPayu::addLog("postValidation: getPayMethodsAssoc: {$e->getMessage()}", 2);
                            $this->postErrors[] = sprintf(
                                $this->l('%s: connection to PayU failed with the provided POS parameters. Make sure you are using a POS of type Checkout.'),
                                $iso_currency
                            );
                        }
                    }
                }
            }
            if (!$any_pos_enabled) {
                $this->postErrors[] = $this->l('At least one POS must be enabled');
            }
        }
    }

    /**
     * Save form data.
     */
    private function postProcess()
    {
        if (Tools::isSubmit('btnSubmitPayu')) {
            self::setData('set_os_payment_in_any_os', Tools::getValue('set_os_payment_in_any_os') ? '1' : '0');
            self::setData('update_invoice_from_payu', Tools::getValue('update_invoice_from_payu') ? '1' : '0');
            self::setData(
                'disable_javascript_payment_block',
                Tools::getValue('disable_javascript_payment_block') ? '1' : '0'
            );
            self::setData(
                'log_http_all',
                Tools::getValue('log_http_all') ? '1' : '0'
            );
            self::setData(
                'log_notification_all',
                Tools::getValue('log_notification_all') ? '1' : '0'
            );
            self::setData(
                'warn_confirmation_before_validation',
                Tools::getValue('warn_confirmation_before_validation') ? '1' : '0'
            );
            foreach ($this->limited_currencies as $iso_currency) {
                self::setData(
                    'enable_pos_'.$iso_currency,
                    Tools::getValue('enable_pos_'.$iso_currency) ? '1' : '0'
                );
                self::setData('pos_id_'.$iso_currency, trim(Tools::getValue('pos_id_'.$iso_currency)));
                self::setData('key_'.$iso_currency, trim(Tools::getValue('key_'.$iso_currency)));
                self::setData('second_key_'.$iso_currency, trim(Tools::getValue('second_key_'.$iso_currency)));
                self::setData(
                    'direct_card_'.$iso_currency,
                    Tools::getValue('direct_card_'.$iso_currency) ? '1' : '0'
                );
                self::setData(
                    'basic_payment_'.$iso_currency,
                    Tools::getValue('basic_payment_'.$iso_currency) ? '1' : '0'
                );
                self::setData(
                    'send_payment_email_'.$iso_currency,
                    Tools::getValue('send_payment_email_'.$iso_currency) ? '1' : '0'
                );
                self::setData(
                    'display_payment_methods_'.$iso_currency,
                    Tools::getValue('display_payment_methods_'.$iso_currency) ? '1' : '0'
                );

                $surcharge_percentage = str_replace(',', '.', trim(Tools::getValue('surcharge_'.$iso_currency)));
                $surcharge_min = round(
                    str_replace(',', '.', trim(Tools::getValue('surcharge_min_'.$iso_currency))),
                    2
                );
                $surcharge_max = round(
                    str_replace(',', '.', trim(Tools::getValue('surcharge_max_'.$iso_currency))),
                    2
                );
                $surcharge_product = Tools::getValue('surcharge_product_'.$iso_currency);

                self::setData('surcharge_'.$iso_currency, self::positiveOrFalse((float)$surcharge_percentage));
                self::setData('surcharge_min_'.$iso_currency, self::positiveOrFalse((float)$surcharge_min));
                self::setData('surcharge_max_'.$iso_currency, self::positiveOrFalse((float)$surcharge_max));
                self::setData('surcharge_product_'.$iso_currency, self::positiveOrFalse((int)$surcharge_product));
            }
            return true;
        }
        return false;
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the back office.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
        return false;
    }

    public function hookBackOfficeFooter()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            $this->context->smarty->assign('payu_currencies', $this->limited_currencies);
            return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/back_office_footer.tpl');
        }
        return false;
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    private static $payments_for_tab = false;

    private static function updatePaymentsForTab($id_cart)
    {
        if (!is_array(self::$payments_for_tab)) {
            require_once _PS_MODULE_DIR_ . 'prestacafenewpayu/classes/payupayment.php';
            self::$payments_for_tab = PayUPayment::getPaymentsByCartId($id_cart);
        }
    }

    public function hookDisplayAdminOrderTabOrder($params)
    {
        $order = $params['order'];
        self::updatePaymentsForTab($order->id_cart);

        if (self::$payments_for_tab) {
            $latest_payment = self::$payments_for_tab[0];
            if ($latest_payment['payu_order_status'] == PayUPayment::STATUS_COMPLETED) {
                $label = ' <span class="label label-success">' . $this->l('OK') . '</span>';
            } elseif ($latest_payment['payu_order_status'] == PayUPayment::STATUS_NEW) {
                $label = ' <span class="label label-info">' . $this->l('New') . '</span>';
            } elseif ($latest_payment['payu_order_status'] == PayUPayment::STATUS_PENDING) {
                $label = ' <span class="label label-info">' . $this->l('Pending') . '</span>';
            } elseif ($latest_payment['payu_order_status'] == PayUPayment::STATUS_WAITING_FOR_CONFIRMATION) {
                $label = ' <span class="label label-warning">' .
                    $this->l('Confirm this payment in the PayU panel') . '</span>';
            } elseif ($latest_payment['payu_order_status'] == PayUPayment::STATUS_CANCELED) {
                $label = ' <span class="label label-danger">' . $this->l('Cancelled') . '</span>';
            } elseif ($latest_payment['payu_order_status'] == PayUPayment::STATUS_REJECTED) {
                $label = ' <span class="label label-danger">' . $this->l('Rejected') . '</span>';
            } else {
                $label = ' <span class="label label-danger">' . $this->l('Unknown status') . '</span>';
            }
        } else {
            $label = '';
        }

        return '
            <li>
                <a href="#prestacafenewpayu">
                    <i class="icon-money"></i> ' . $this->l('PayU') . $label . '
                </a>
            </li>';
    }

    public function hookDisplayAdminOrderContentOrder($params)
    {
        $order = $params['order'];
        self::updatePaymentsForTab($order->id_cart);
        $this->context->smarty->assign(
            array(
                'payments' => self::$payments_for_tab,
                'id_order' => $order->id,
                'secure_key' => $order->secure_key,
                'payagain_disabled' => $order->getTotalPaid() > 0,
            )
        );
        return $this->display(_PS_MODULE_DIR_ . 'prestacafenewpayu/prestacafenewpayu.php', 'admin_order.tpl');
    }

    public function hookDisplayAdminOrder($params)
    {
        $order = new Order($params['id_order']);
        $params['order'] = $order;
        return $this->hookDisplayAdminOrderContentOrder($params);
    }

    public function hookPaymentOptions($params)
    {
        $cart = $params['cart'];

        if (!$this->paymentChecks('hookPaymentOptions', $cart)) {
            return array();
        }

        $payment_options = array();

        $currency = new Currency($cart->id_currency);

        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payutools.php';
        $surcharge_percentage = (float)PrestaCafeNewPayu::getData('surcharge_'.$currency->iso_code);
        $surcharge_min = (int)round(PrestaCafeNewPayu::getData('surcharge_min_'.$currency->iso_code) * 100, 0);
        $surcharge_max = (int)round(PrestaCafeNewPayu::getData('surcharge_max_'.$currency->iso_code) * 100, 0);
        $surcharge = PayUTools::calculateSurcharge($cart, $surcharge_percentage, $surcharge_min, $surcharge_max);

        $cta_text_basic = $this->l('Pay in PayU');
        $cta_text_card = $this->l('Pay by card in PayU');

        if ($surcharge > 0) {
            $surcharge_price = Tools::displayPrice(Tools::ps_round($surcharge/100.0, 2), $currency);
            $add_text = $this->l('Additional surcharge:');
            $cta_text_basic .= '. '.$add_text.' '.$surcharge_price;
            $cta_text_card .= '. '.$add_text.' '.$surcharge_price;
        }

        $this->context->smarty->assign(
            'display_payment_methods',
            self::getData('display_payment_methods_'.$currency->iso_code)
        );

        if (self::getData('basic_payment_'.$currency->iso_code)) {
            $basicOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption;
            $basicOption
                ->setCallToActionText($cta_text_basic)
                ->setAction($this->context->link->getModuleLink('prestacafenewpayu', 'payment', array(), true));
            $payment_options[] = $basicOption;
        }

        if (self::getData('direct_card_'.$currency->iso_code)) {
            $cardOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption;
            $cardOption
                ->setCallToActionText($cta_text_card)
                ->setAction(
                    $this->context->link->getModuleLink('prestacafenewpayu', 'payment', array('pbl' => 'c'), true)
                );
            $payment_options[] = $cardOption;
        }

        return $payment_options;
    }

    /**
     * This method is used to render the payment button,
     * Take care if the button should be displayed or not.
     * @param $params array
     * @return string
     */
    public function hookPayment($params)
    {
        $cart = $params['cart'];
        if (!$this->paymentChecks('hookPayment', $cart)) {
            return false;
        }

        $currency = new Currency($cart->id_currency);

        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payutools.php';
        $surcharge_percentage = (float)PrestaCafeNewPayu::getData('surcharge_'.$currency->iso_code);
        $surcharge_min = (int)round(PrestaCafeNewPayu::getData('surcharge_min_'.$currency->iso_code) * 100, 0);
        $surcharge_max = (int)round(PrestaCafeNewPayu::getData('surcharge_max_'.$currency->iso_code) * 100, 0);
        $surcharge = PayUTools::calculateSurcharge($cart, $surcharge_percentage, $surcharge_min, $surcharge_max);

        $this->smarty->assign(
            array(
                'module_dir' => $this->_path,
                'img_dir' => __PS_BASE_URI__ . 'img/',
                'module_img_dir' => _MODULE_DIR_ . $this->name . '/views/img/',
                'disable_javascript_payment_block' => self::getData('disable_javascript_payment_block'),
                'show_direct_card' => self::getData('direct_card_'.$currency->iso_code),
                'show_basic_payment' => self::getData('basic_payment_'.$currency->iso_code),
                'surcharge' => Tools::ps_round($surcharge/100.0, 2),
                'currency' => $currency
            )
        );

        return $this->display(
            _PS_MODULE_DIR_ . 'prestacafenewpayu/prestacafenewpayu.php',
            'views/templates/hook/payment.tpl'
        );
    }

    public function hookDisplayPaymentEU($params)
    {
        $cart = $params['cart'];

        if (!$this->paymentChecks('hookDisplayPaymentEU', $cart)) {
            return array();
        }

        $payment_options = array();

        $currency = new Currency($cart->id_currency);

        require_once _PS_MODULE_DIR_.'prestacafenewpayu/classes/payutools.php';
        $surcharge_percentage = (float)PrestaCafeNewPayu::getData('surcharge_'.$currency->iso_code);
        $surcharge_min = (int)round(PrestaCafeNewPayu::getData('surcharge_min_'.$currency->iso_code) * 100, 0);
        $surcharge_max = (int)round(PrestaCafeNewPayu::getData('surcharge_max_'.$currency->iso_code) * 100, 0);
        $surcharge = PayUTools::calculateSurcharge($cart, $surcharge_percentage, $surcharge_min, $surcharge_max);

        $cta_text_basic = $this->l('Pay in PayU');
        $cta_text_card = $this->l('Pay by card in PayU');
        if ($surcharge > 0) {
            $surcharge_price = Tools::displayPrice(Tools::ps_round($surcharge/100.0, 2), $currency);
            $cta_text_basic .= '. '.$this->l('Additional surcharge:').' '.$surcharge_price;
            $cta_text_card .= '. '.$this->l('Additional surcharge:').' '.$surcharge_price;
        }

        if (self::getData('basic_payment_'.$currency->iso_code)) {
            $payment_options[] =
                array(
                    'cta_text' => $cta_text_basic,
                    'logo' => Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/prestacafenewpayu.png'),
                    'action' => $this->context->link->getModuleLink(
                        $this->name,
                        'payment',
                        array('id_cart' => $cart->id),
                        true
                    )
                );
        }

        if (self::getData('direct_card_'.$currency->iso_code)) {
            $payment_options[] =
                array(
                    'cta_text' => $cta_text_card,
                    'logo' => Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/prestacafenewpayu.png'),
                    'action' => $this->context->link->getModuleLink(
                        $this->name,
                        'payment',
                        array('id_cart' => $cart->id, 'direct_card' => 1),
                        true
                    )
                );
        }

        return $payment_options;
    }

    /**
     * @param $hookName string
     * @param $cart Cart
     * @return bool
     */
    private function paymentChecks($hookName, $cart)
    {
        if (Configuration::get('PS_ALLOW_MULTISHIPPING')
                && (Configuration::get('PS_ORDER_PROCESS_TYPE')
                    || Tools::getValue('multi-shipping'))) {
            self::addLog(
                $hookName.': not displaying because this module does not support multishipping',
                2
            );
            return false;
        }
        if (extension_loaded('curl') == false) {
            self::addLog($hookName.': not displaying because curl is not enabled', 2);
            return false;
        }
        if (!$this->active) {
            self::addLog($hookName.': not displaying because the module is not active', 2);
            return false;
        }
        if ($cart->getOrderTotal() < 1) {
            self::addLog($hookName . ': not displaying because total < 1.00', 2);
            return false;
        }

        $currency = new Currency((int)$cart->id_currency);
        if (in_array($currency->iso_code, $this->limited_currencies) == false) {
            self::addLog(
                $hookName.': not displaying because currency ' . $currency->iso_code . ' is not supported',
                2
            );
            return false;
        }

        if (!self::getData('enable_pos_'.$currency->iso_code)) {
            self::addLog(
                $hookName.': not displaying because pos_id_'.$currency->iso_code
                .'or second_key_'.$currency->iso_code.' is missing',
                2
            );
            return false;
        }

        return true;
    }

    /**
     * This hook is used to display the order confirmation page.
     */
    public function hookPaymentReturn($params)
    {
        return $this->display(
            _PS_MODULE_DIR_ . 'prestacafenewpayu/prestacafenewpayu.php',
            'views/templates/hook/payment_return.tpl'
        );
    }

    /**
     * Returns this module's specific OrderState::id, if that OrderState exists.
     * @return int|bool OrderState::id or false
     */
    public static function getOrderStateId()
    {
        if (!self::$payu_order_state_id) {
            $query = 'SELECT `id_order_state` FROM `' . _DB_PREFIX_ . 'order_state` os
                      WHERE module_name = \'prestacafenewpayu\'
                      ORDER BY `id_order_state`
                      LIMIT 1';
            if ($result = Db::getInstance()->executeS($query)) {
                $id_order_state = $result[0]['id_order_state'];
                if ($id_order_state) {
                    self::$payu_order_state_id = $id_order_state;
                }
            }
        }
        return self::$payu_order_state_id;
    }

    /**
     * Adds a new order state.
     *
     * @param $names array names indexed by language's ISO code
     * @param $color
     * @return bool|int new OrderState's id or false
     */
    private static function addNewOrderState($names, $color)
    {
        if (!isset($names['en'])) {
            self::addLog('addNewOrderState: order state name in English is required');
            return false;
        }
        $order_state = new OrderState();
        $order_state->name = array();
        foreach (Language::getLanguages() as $language) {
            if (isset($names[$language['iso_code']])) {
                $order_state->name[$language['id_lang']] = $names[$language['iso_code']];
            } else {
                $order_state->name[$language['id_lang']] = $names['en'];
            }
        }
        $order_state->send_email = false;
        $order_state->invoice = false;
        $order_state->unremovable = false;
        $order_state->color = $color;
        $order_state->module_name = 'prestacafenewpayu';

        if (!$order_state->add()) {
            self::addLog('addNewOrderState: cannot add a new order state', 3);
            return false;
        }
        copy(
            _PS_MODULE_DIR_ . 'prestacafenewpayu/logo.gif',
            _PS_ROOT_DIR_ . '/img/os/' . (int)$order_state->id . '.gif'
        );
        return $order_state->id;
    }

    private static function isValidOldPayUOrderState(OrderState $os)
    {
        if (!Validate::isLoadedObject($os)) {
            return false;
        }
        $name = $os->name;
        if (!is_array($name)) {
            $name = array($name);
        }
        foreach ($name as $lang_name) {
            if (preg_match('/\bpayu\b/i', $lang_name)) {
                return true;
            }
        }
        return false;
    }

    private static function loadData()
    {
        self::$data = array();

        $sql = 'SELECT d.`name`, d.`id_lang`, d.`id_shop_group`, d.`id_shop`, d.`value`
                FROM `' . _DB_PREFIX_ . 'prestacafenewpayu_data` d';
        $db = Db::getInstance();
        $result = $db->executeS($sql, false);
        while ($row = $db->nextRow($result)) {
            if (!isset(self::$data[$row['id_lang']])) {
                self::$data[$row['id_lang']] = array(
                    'global' => array(),
                    'group' => array(),
                    'shop' => array(),
                );
            }

            if ($row['id_shop']) {
                self::$data[$row['id_lang']]['shop'][$row['id_shop']][$row['name']] = $row['value'];
            } elseif ($row['id_shop_group']) {
                self::$data[$row['id_lang']]['group'][$row['id_shop_group']][$row['name']] = $row['value'];
            } else {
                self::$data[$row['id_lang']]['global'][$row['name']] = $row['value'];
            }
        }
    }

    /**
     * Check if key exists in configuration
     *
     * This function is based on the Configuration core class.
     *
     * @param string $key
     * @param int $id_lang
     * @param int $id_shop_group
     * @param int $id_shop
     * @return bool
     */
    public static function hasDataKey($key, $id_lang = null, $id_shop_group = null, $id_shop = null)
    {
        if (!is_int($key) && !is_string($key)) {
            return false;
        }
        $id_lang = (int)$id_lang;
        if ($id_shop) {
            return isset(self::$data[$id_lang]['shop'][$id_shop])
            && array_key_exists($key, self::$data[$id_lang]['shop'][$id_shop]);
        } elseif ($id_shop_group) {
            return isset(self::$data[$id_lang]['group'][$id_shop_group])
            && array_key_exists($key, self::$data[$id_lang]['group'][$id_shop_group]);
        }
        return isset(self::$data[$id_lang]['global']) && array_key_exists($key, self::$data[$id_lang]['global']);
    }

    /**
     * This function is based on the Configuration core class.
     */
    public static function getData($key, $id_lang = null, $id_shop_group = null, $id_shop = null)
    {
        if (!is_array(self::$data)) {
            self::loadData();
        }
        if (!self::$data) {
            return false;
        }

        $id_lang = (int)$id_lang;

        if ($id_shop === null || !Shop::isFeatureActive()) {
            $id_shop = Shop::getContextShopID(true);
        }
        if ($id_shop_group === null || !Shop::isFeatureActive()) {
            $id_shop_group = Shop::getContextShopGroupID(true);
        }

        if (!isset(self::$data[$id_lang])) {
            $id_lang = 0;
        }

        if ($id_shop && self::hasDataKey($key, $id_lang, null, $id_shop)) {
            return self::$data[$id_lang]['shop'][$id_shop][$key];
        } elseif ($id_shop_group && self::hasDataKey($key, $id_lang, $id_shop_group)) {
            return self::$data[$id_lang]['group'][$id_shop_group][$key];
        } elseif (self::hasDataKey($key, $id_lang)) {
            return self::$data[$id_lang]['global'][$key];
        }
        return false;
    }

    public static function getGlobalData($key, $id_lang = null)
    {
        return self::getData($key, $id_lang, 0, 0);
    }

    /**
     * Update configuration key and value into database (automatically insert if key does not exist)
     *
     * This function is based on the Configuration core class.
     *
     * @param string $key Key
     * @param mixed $values $values is an array if the configuration is multilingual, a single string else.
     * @param boolean $html Specify if html is authorized in value
     * @param int $id_shop_group
     * @param int $id_shop
     * @return boolean Update result
     */
    public static function setData($key, $values, $html = false, $id_shop_group = null, $id_shop = null)
    {
        if (!Validate::isConfigName($key)) {
            die(sprintf(Tools::displayError('[%s] is not a valid configuration key'), $key));
        }

        if (!is_array(self::$data)) {
            self::loadData();
        }

        if ($id_shop === null || !Shop::isFeatureActive()) {
            $id_shop = Shop::getContextShopID(true);
        }
        if ($id_shop_group === null || !Shop::isFeatureActive()) {
            $id_shop_group = Shop::getContextShopGroupID(true);
        }

        if (!is_array($values)) {
            $values = array($values);
        }

        if ($html) {
            foreach ($values as &$value) {
                $value = Tools::purifyHTML($value);
            }
        }

        $result = true;
        foreach ($values as $lang => $value) {
            $stored_value = self::getData($key, $lang, $id_shop_group, $id_shop);
            // if there isn't a $stored_value, we must insert $value
            if ((!is_numeric($value) && $value === $stored_value)
                || (is_numeric($value) && $value == $stored_value && self::hasDataKey($key, $lang))
            ) {
                continue;
            }

            // If key already exists, update value
            if (self::hasDataKey($key, $lang, $id_shop_group, $id_shop)) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'prestacafenewpayu_data` d
							SET d.`value` = \'' . pSQL($value, $html) . '\',
								d.`date_upd` = NOW()
							WHERE d.`id_lang` = ' . (int)$lang . '
								AND d.`name` = \'' . pSQL($key) . '\''
                    . self::sqlRestriction($id_shop_group, $id_shop);
                $result &= Db::getInstance()->execute($sql);
            } else {
                // If key does not exists, create it
                if (!self::getDataIdByName($key, $id_shop_group, $id_shop)) {
                    $columns = array(
                        'name' => $key,
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_upd' => date('Y-m-d H:i:s'),
                        'id_lang' => $lang
                    );
                    if ($id_shop) {
                        $columns['id_shop'] = (int)$id_shop;
                    }
                    if ($id_shop_group) {
                        $columns['id_shop_group'] = (int)$id_shop_group;
                    }
                    $columns['value'] = pSQL($value);

                    $result &= Db::getInstance()->insert('prestacafenewpayu_data', $columns);
                }
            }
        }

        foreach ($values as $lang => $value) {
            if ($id_shop) {
                self::$data[$lang]['shop'][$id_shop][$key] = $value;
            } elseif ($id_shop_group) {
                self::$data[$lang]['group'][$id_shop_group][$key] = $value;
            } else {
                self::$data[$lang]['global'][$key] = $value;
            }
        }

        return $result;
    }

    public static function setGlobalData($key, $values, $html = false)
    {
        return self::setData($key, $values, $html, 0, 0);
    }

    /**
     * Return ID a configuration key
     *
     * This function is based on the Configuration core class.
     *
     * @param string $key
     * @param int $id_shop_group
     * @param int $id_shop
     * @return int
     */
    public static function getDataIdByName($key, $id_shop_group = null, $id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = Shop::getContextShopID(true);
        }
        if ($id_shop_group === null) {
            $id_shop_group = Shop::getContextShopGroupID(true);
        }

        $sql = 'SELECT `id_prestacafenewpayu_data`
				FROM `' . _DB_PREFIX_ . 'prestacafenewpayu_data`
				WHERE name = \'' . pSQL($key) . '\'
				' . self::sqlRestriction($id_shop_group, $id_shop);
        return (int)Db::getInstance()->getValue($sql);
    }

    /**
     * Add SQL restriction on shops for configuration table
     *
     * This function is based on the Configuration core class.
     *
     * @param int $id_shop_group
     * @param int $id_shop
     * @return string
     */
    private static function sqlRestriction($id_shop_group, $id_shop)
    {
        if ($id_shop) {
            return ' AND id_shop = ' . (int)$id_shop;
        } elseif ($id_shop_group) {
            return ' AND id_shop_group = ' . (int)$id_shop_group . ' AND (id_shop IS NULL OR id_shop = 0)';
        } else {
            return ' AND (id_shop_group IS NULL OR id_shop_group = 0) AND (id_shop IS NULL OR id_shop = 0)';
        }
    }

    private static function positiveOrFalse($num)
    {
        if ($num > 0) {
            return $num;
        }
        return false;
    }

    private function getSurchargeProducts()
    {
        $data = array(array('id' => '0', 'name' => $this->l('Choose a product')));

        $root = Category::getRootCategory();
        $start = 0;
        $limit = 50;

        $products = Product::getProducts($this->context->language->id, $start, $limit, 'name', 'ASC', $root->id);

        while ($products) {
            foreach ($products as $product) {
                if ($product['is_virtual'] && $product['available_for_order']) {
                    $data[] = array('id' => $product['id_product'], 'name' => $product['name']);
                }
            }
            $start += $limit;
            $products = Product::getProducts($this->context->language->id, $start, $limit, 'name', 'ASC', $root->id);
        }

        return $data;
    }

    /**
     * Outputs a log message to the PrestaShopLogger and to the PHP error log.
     * May be called before the module is fully installed or after it is
     * partially uninstalled.
     *
     * @param $message string
     * @param int $severity in line with PrestaShopLogger::addLog's severity
     */
    public static function addLog($message, $severity = 1)
    {
        if ($severity < 2) {
            return;
        }
        $message = '[' . Tools::getRemoteAddr() . '] prestacafenewpayu: ' . $message;
        if (!Validate::isMessage($message)) {
            $message = preg_replace('/[<>{}]/', ' ', $message);
        }
        if (class_exists('PrestaShopLogger')) {
            PrestaShopLogger::addLog($message, $severity, null, null, null, true);
        } elseif (class_exists('Logger') && method_exists('Logger', 'addLog')) {
            Logger::addLog($message, $severity);
        } else {
            error_log($message);
        }
    }

    /**
     * @param $severity int 1 debug, 2 warning, 3 error, 4 fatal
     */
    public static function addDebug($message, $severity, $tag, $data = null)
    {
        $sql = 'INSERT INTO `'._DB_PREFIX_.'prestacafenewpayu_debug`
            (`message`, `severity`, `tag`, `data`)
            VALUES('.
                '\''.pSQL($message).'\','.
                (int)$severity.','.
                ($tag !== null ? '\''.pSQL($tag).'\'' : 'NULL').','.
                ($data !== null ? '\''.pSQL(serialize($data), true).'\'' : 'NULL').'
            )';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql);
    }

    public static function logAddTrigraphs($message)
    {
        $message = str_replace('{', '??lcb??', $message);   // left curly brace
        $message = str_replace('}', '??rcb??', $message);   // right curly brace
        $message = str_replace('<', '??lab??', $message);   // left angle brace
        $message = str_replace('>', '??rab??', $message);   // right angle brace
        return $message;
    }

    /**
     * This function should be called in a template to show a "Pay again" button.
     *
     * @param $order array
     * @return string html
     * @throws Exception
     * @throws SmartyException
     */
    public static function historyPayAgain($params)
    {
        require_once _PS_MODULE_DIR_ . 'prestacafenewpayu/classes/payupayment.php';
        require_once _PS_MODULE_DIR_ . 'prestacafenewpayu/classes/payutools.php';

        if (!empty($params['details']['id']) && $params['details']['id']) {
            $order = new Order($params['details']['id']);
        } else {
            $order = new Order((int)$params['id_order']);
        }
        if ($order->getTotalPaid() > 0) {
            // Cannot pay again for an already-paid order
            return '';
        }

        $last_payment = PayUPayment::getLastPaymentByCartId($order->id_cart);
        if ($last_payment && $last_payment->payu_order_status == PayUPayment::STATUS_COMPLETED) {
            return '';
        }

        $cart = new Cart((int) $order->id_cart);

        $args = PayUTools::getSsecureTokenCartParams($cart);
        Context::getContext()->smarty->assign($args);
        return Context::getContext()->smarty->fetch(
            _PS_MODULE_DIR_ . 'prestacafenewpayu/views/templates/front/history_pay_again.tpl'
        );
    }

    public static function getPayuApiHost()
    {
        return self::PAYU_API_HOST;
    }

    public function validateOrderWithSurcharge(
        $cart,
        $id_product,
        $surcharge,
        $id_order_state,
        $amount_paid,
        $customer_secure_key
    ) {
        if ($surcharge > 0) {
            $surcharge_product = new Product($id_product, true);
            PayUTools::addSurchargeProductToCart($cart, $surcharge_product);
            PayUTools::resetSurchargeProductPrice($surcharge_product, $surcharge);
        }

        $this->validateOrder(
            $cart->id,
            $id_order_state,
            // total_paid_real
            $amount_paid,
            // payment name
            $this->displayName,
            // private message for order
            null,
            // extra_vars
            array(),
            // currency_special
            null,
            // dont_touch_amount
            false,
            $customer_secure_key
        );
    }
}
