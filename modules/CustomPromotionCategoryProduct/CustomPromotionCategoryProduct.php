<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomPromotionCategoryProduct extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'CustomPromotionCategoryProduct';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'JBA';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Promocyjne kategorie');
        $this->description = $this->l('Moduł zmienia cenę produktu w koszyku w zależności ...');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateValue('CUSTOMPROMOTIONCATEGORYPRODUCT_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionCartSave') &&
            $this->registerHook('actionBeforeCartUpdateQty') &&
            $this->registerHook('displayShoppingCartFooter');
    }

    public function uninstall()
    {
        Configuration::deleteByName('CUSTOMPROMOTIONCATEGORYPRODUCT_LIVE_MODE');

        return parent::uninstall();
    }

    public function getContent()
    {

        if (((bool)Tools::isSubmit('submitCustomPromotionCategoryProductModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }


    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCustomPromotionCategoryProductModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
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
                        'label' => $this->l('Live mode'),
                        'name' => 'CUSTOMPROMOTIONCATEGORYPRODUCT_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'CUSTOMPROMOTIONCATEGORYPRODUCT_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'CUSTOMPROMOTIONCATEGORYPRODUCT_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CUSTOMPROMOTIONCATEGORYPRODUCT_LIVE_MODE' => Configuration::get('CUSTOMPROMOTIONCATEGORYPRODUCT_LIVE_MODE', true),
            'CUSTOMPROMOTIONCATEGORYPRODUCT_ACCOUNT_EMAIL' => Configuration::get('CUSTOMPROMOTIONCATEGORYPRODUCT_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'CUSTOMPROMOTIONCATEGORYPRODUCT_ACCOUNT_PASSWORD' => Configuration::get('CUSTOMPROMOTIONCATEGORYPRODUCT_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookActionCartSave()
    {

    }

    public function hookDisplayShoppingCartFooter()
    {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		};
		if($ip == '46.227.241.107' || $ip == '91.227.89.130' || $ip == '77.65.70.14'){

			$conf[1458] = 250; // $conf[id_category] = suma w koszyku;
			$conf[1457] = 500;
			$conf[1459] = 1000;
			$conf[1460] = 1500;
			$conf[1461] = 2000;

			$suma = $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
			$custom_products = array();
			foreach($conf as $k=>$v){
				
				if($suma >= $v){
					
					$category = new Category($k);
					$products = $category->getProducts($this->context->language->id, 1, 100);
					$custom_products = $custom_products + $products;
				}
			}
			$ses = array();
			foreach($custom_products as &$prod){
				
				$ses[(int)$prod['id_product']] = 1;

				$prod['price'] = 1;
				$prod['price_tax_exc'] = 1;
				$prod['orderprice'] = 1;
			}

			$this->context->cookie->__set('CustomPromotionCategoryProduct',json_encode($ses));

			$this->context->smarty->assign(array(
			
				'custom_suma' => $suma,
				'custom_products' => $custom_products
			));
			return $this->display(__FILE__, 'views/templates/footercart.tpl');
		}
    }

}
