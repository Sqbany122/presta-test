<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2015 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Iqitfreedeliverycount extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'iqitfreedeliverycount';
        $this->tab = 'administration';
        $this->version = '1.2.3';
        $this->author = 'iqit-commerce.com';
        $this->need_instance = 0;
        $this->module_key = '834065271d1b45780b9a539963f2b3d7';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Amount Left to free shipping');
        $this->description = $this->l('Module countdown amount which left to free shipping');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->config_name = 'iqitfdc';
        $this->defaults = array(
            'custom_status' => 0,
            'custom_amount' => 450,
            'txt_color' => '#ffffff',
            'bg_color' => '#E7692A',
            'border_color' => '#AB3E07',
            'txt' => '',

        );
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        $this->setDefaults();
        $this->generateCss();

        return parent::install() &&
        $this->registerHook('header') &&
        $this->registerHook('top') &&
        $this->registerHook('displayShoppingCartFooter');

    }

    public function uninstall()
    {
        foreach ($this->defaults as $default => $value) {
            Configuration::deleteByName($this->config_name . '_' . $default);
        }

        return parent::uninstall();
    }

    public function setDefaults()
    {
        foreach ($this->defaults as $default => $value) {
            Configuration::updateValue($this->config_name . '_' . $default, $value);
        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (Tools::isSubmit('submitModule')) {
            $this->postProcess();
            $this->generateCss();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $this->renderForm();
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
        $helper->submit_action = 'submitModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Custom free shipping amount status'),
                        'name' => 'custom_status',
                        'is_bool' => true,
                        'desc' => $this->l('By default module use free shipping value definien in Shipping >
                        preferences, but if you set free shipping price indvidual per carrier,
                        then you put same value here'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Custom free shipping amount value'),
                        'name' => 'custom_amount',
                        'desc' => $this->l('Put price with tax '),
                        'size' => 20,
                        'suffix' => $this->context->currency->getSign(), 3,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Additional info'),
                        'desc' => $this->l('For example if you only offer free shipping for one carrier and you want to inform about that'),
                        'name' => 'txt',
                        'autoload_rte' => true,
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 30,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Text color'),
                        'name' => 'txt_color',
                        'size' => 30,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Background color'),
                        'name' => 'bg_color',
                        'size' => 30,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Border color'),
                        'name' => 'border_color',
                        'size' => 30,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    public function generateCss()
    {

        $css = '.iqitfreedeliverycount{
            Background: ' . Configuration::get($this->config_name . '_bg_color') . ';
            border: 1px solid ' . Configuration::get($this->config_name . '_border_color') . ';
            color: ' . Configuration::get($this->config_name . '_txt_color') . ';
        }
        .iqitfreedeliverycount .ifdc-txt-content{border-color: ' . Configuration::get($this->config_name . '_txt_color') . '; }
        ';

        $css = trim(preg_replace('/\s+/', ' ', $css));

        if (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $myFile = $this->local_path . "views/css/custom_g_" . (int) $this->context->shop->getContextShopGroupID() . ".css";
        } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $myFile = $this->local_path . "views/css/custom_s_" . (int) $this->context->shop->getContextShopID() . ".css";
        }

        $fh = fopen($myFile, 'w');
        fwrite($fh, $css);
        fclose($fh);

    }
    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $var = array();
        foreach ($this->defaults as $default => $value) {
            if ($default == 'txt') {
                foreach (Language::getLanguages(false) as $lang) {
                    $var[$default][(int) $lang['id_lang']] = Configuration::get($this->config_name . '_' . $default, (int) $lang['id_lang']);
                }

            } else {
                $var[$default] = Configuration::get($this->config_name . '_' . $default);
            }

        }
        return $var;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        foreach ($this->defaults as $default => $value) {
            if ($default == 'txt') {
                $message_trads = array();
                foreach ($_POST as $key => $value) {
                    if (preg_match('/txt_/i', $key)) {
                        $id_lang = preg_split('/txt_/i', $key);
                        $message_trads[(int) $id_lang[1]] = $value;
                    }
                }

                Configuration::updateValue($this->config_name . '_' . $default, $message_trads, true);
            } else {
                Configuration::updateValue($this->config_name . '_' . $default, (Tools::getValue($default)));
            }

        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');

        if (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $this->context->controller->addCSS(($this->_path) . 'views/css/custom_g_' . (int) $this->context->shop->getContextShopGroupID() . '.css', 'all');
        } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->context->controller->addCSS(($this->_path) . 'views/css/custom_s_' . (int) $this->context->shop->getContextShopID() . '.css', 'all');
        }

    }

    public function hookDisplayShoppingCartFooter($params)
    {
        if (Configuration::get($this->config_name . '_custom_status')) {
            $free_ship_from = Tools::convertPrice(
                (float) Configuration::get($this->config_name . '_custom_amount'),
                Currency::getCurrencyInstance((int) Context::getContext()->currency->id)
            );
        } else {
            $free_ship_from = Tools::convertPrice(
                (float) Configuration::get('PS_SHIPPING_FREE_PRICE'),
                Currency::getCurrencyInstance((int) Context::getContext()->currency->id)
            );
        }

        $total = Context::getContext()->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);

        if ($free_ship_from == 0) {
            return;
        }

        if (count(Context::getContext()->cart->getOrderedCartRulesIds(CartRule::FILTER_ACTION_SHIPPING))) {
            return;
        }

        $free_ship_remaining = $free_ship_from - $total;

        if ($free_ship_remaining <= 0) {
            return;
        }

        $this->smarty->assign(
            array(
                'free_ship_from' => $free_ship_from,
                'free_ship_remaining' => $free_ship_remaining,
                'txt' => Configuration::get($this->config_name . '_txt', $this->context->language->id),

            )
        );

        return $this->display(__FILE__, 'iqitfreedeliverycount.tpl');
    }

    public function hookTop($params)
    {
        if (Configuration::get($this->config_name . '_custom_status')) {
            $free_ship_from = Tools::convertPrice(
                (float) Configuration::get($this->config_name . '_custom_amount'),
                Currency::getCurrencyInstance((int) Context::getContext()->currency->id)
            );
        } else {
            $free_ship_from = Tools::convertPrice(
                (float) Configuration::get('PS_SHIPPING_FREE_PRICE'),
                Currency::getCurrencyInstance((int) Context::getContext()->currency->id)
            );
        }

        $total = Context::getContext()->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);

        if ($free_ship_from == 0) {
            return;
        }

        if (count(Context::getContext()->cart->getOrderedCartRulesIds(CartRule::FILTER_ACTION_SHIPPING))) {
            return;
        }

        Media::addJsDef(array('iqitfdc_from' => $free_ship_from));

        $free_ship_remaining = $free_ship_from - $total;

        $this->smarty->assign(
            array(
                'free_ship_remaining' => $free_ship_remaining,
                'txt' => Configuration::get($this->config_name . '_txt', $this->context->language->id),

            )
        );

        return $this->display(__FILE__, 'cart.tpl');
    }
}
