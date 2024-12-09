<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('ProductsOffModel'))
    require_once(dirname(__FILE__).'/classes/ProductsOffModel.php');

class x13productsoff extends ProductsOffModel
{
    public function __construct()
    {
        $this->name = 'x13productsoff';
        $this->tab = 'search_filter';
        $this->version = '2.1.3';
        $this->author = 'X13.pl';
        $this->need_instance = 0;
        $this->ps_version = substr(_PS_VERSION_, 0, 3);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('OFF Product');
        $this->description = $this->l('Turn off the product with zero in stock');
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
    
    public function getContent()
    {
        return parent::getContent();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $conf = Configuration::getMultiple(self::$_confList);
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $switchType = $this->ps_version == 1.5 ? 'radio' : 'switch';
        $forms = array();
        $forms[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Settings modules'),
            ),
            'input' => array(
            )
        );
        
        $forms[0]['form']['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Off type'),
            'name' => 'PRODUCT_OFF_TYPE',
            'options' => array(
                'query' => array(
                    array( 
                        'id' => 1,
                        'name' => $this->l('Disabled products')
                    ),
                    array( 
                        'id' => 2,
                        'name' => $this->l('Hidden (Visibility Nowhere)')
                    ),
                    array( 
                        'id' => 4,
                        'name' => $this->l('Hidden (Visibility Catalog only)')
                    ),
                    array( 
                        'id' => 3,
                        'name' => $this->l('Hidden (Visibility Search only)')
                    ),
                ),
                'id' => 'id',
                'name' => 'name'
            ),
        );

        $forms[0]['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Stock threshold for disabling (or hiding) products'),
            'name' => 'PRODUCT_OFF_TRESHOLD',
            'desc' => $this->l('Turn off (or hide) product when quantity is lower than... (1 by default)'),
        );

        $forms[0]['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Ignored products'),
            'name' => 'PRODUCT_OFF_IGNORE_PRODUCTS',
            'hint' => $this->l('Module will not take any actions on products from this list'),
            'desc' => $this->l('id of products, separated by comma'),
        );

        $forms[0]['form']['input'][] = array(
            'type' => $switchType,
            'label' => $this->l('Handle return of the products when they are back in stock'),
            'name' => 'PRODUCT_OFF_AUTOENABLE',
            'desc' => $this->l('IMPORTANT: this will enable/or show product no matter if it was previously disabled with other reason than stock, you can always ignore some products above. Use this option with caution.'),
            'class' => 't',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'PRODUCT_OFF_AUTOENABLE_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'PRODUCT_OFF_AUTOENABLE_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        );

        $forms[0]['form']['input'][] = array(
            'type' => 'select',
            'label' => $this->l('What is the behavior of returned products?'),
            'name' => 'PRODUCT_OFF_AUTOENABLE_TYPE',
            'options' => array(
                'query' => array(
                    array( 
                        'id' => 1,
                        'name' => $this->l('Use option of how to disable them')
                    ),
                    array(
                        'id' => 2,
                        'name' => $this->l('Enable & Show')
                    ),
                ),
                'id' => 'id',
                'name' => 'name'
            ),
        );

        $forms[0]['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Stock threshold for auto enabling (or showing) products'),
            'name' => 'PRODUCT_OFF_AUTOENABLE_TRESHOLD',
            'desc' => $this->l('Turn on (or show) product when quantity is higher than...'),
        );

        if ($this->_ver == '1_6') {
            $forms[0]['form']['submit'] = array(
                'title' => $this->l('Save')
            );  
        } else {
            $forms[0]['form']['submit'] = array(
                'name' => 'submitUpdate'.$this->name,
                'class' => 'btn button btn-default',
                'title' => $this->l('Save')
            );
        }           
        
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang)
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = false;
        $helper->title = $this->displayName;
        $helper->submit_action = 'submitUpdate'.$this->name;
        
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );      
        
        return $helper->generateForm($forms);
    }
}
