<?php
/**
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
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
*
*  @author     BEST-KIT.COM (contact@best-kit.com)
*  @copyright  http://best-kit.com
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'bestkit_opc/includer.php';

class bestkit_opc extends Module {
    const PREFIX = 'bestkit_opc_';

    protected $_hooks = array(
        'header',
        'actionCustomerAccountAdd',
    );

    protected $_tabs = array(
        array(
            'class_name' => 'AdminBestkitOpcFields',
            'name' => 'Checkout Fields'
        ),
    );

    protected $_moduleParams = array(
        'style' => '3columns',
        'skin' => 'default.css',
        'billing' => TRUE,
        'delivery_step_virt' => FALSE,
        'PS_CONDITIONS' => FALSE,
        'PS_CONDITIONS_CMS_ID' => NULL,
        'PS_GUEST_CHECKOUT_ENABLED' => FALSE,
        'PS_DISALLOW_HISTORY_REORDERING' => FALSE,
        'PS_PURCHASE_MINIMUM' => 0,
        'PS_SHIP_WHEN_AVAILABLE' => FALSE,
        'PS_GIFT_WRAPPING' => FALSE,
        'PS_GIFT_WRAPPING_PRICE' => NULL,
        'PS_RECYCLABLE_PACK' => NULL,
        'PS_GIFT_WRAPPING_TAX_RULES_GROUP' => NULL,
        'PS_CUSTOMER_CREATION_EMAIL' => FALSE,
        'continue_shopping' => FALSE,
        'group_customer' => NULL,
        //'also_group_customer' => NULL,
        'allow_select_group' => FALSE,
        'show_breadcrumbs' => TRUE,
		//Order Summary
        'show_total_products' => TRUE,
        'show_total_discount' => TRUE,
        'show_total_wrapping' => TRUE,
        'show_total_shipping' => TRUE,
        'show_total_tax_excl' => TRUE,
        'show_total_tax' => TRUE,
        'show_total' => TRUE,
    );
    protected $_moduleParamsLang = array();

    public function __construct() {
        $this->name = 'bestkit_opc';
        $this->tab = 'front_office_features';
        $this->version = '1.6.7';
        $this->author = 'best-kit.com';
        $this->need_instance = 0;
        $this->bootstrap = TRUE;
        $this->module_key = '3434a5701ed921e58d9d03d056cae764';

        parent::__construct();

        $this->displayName = $this->l('One Step Checkout / One Page Checkout');
        $this->description = $this->l('One Step Checkout extension transforms your clumsy checkout service into the fast all-in-one form, that requires minimum time and effort to fill in. Package for Prestashop v.1.6.x');
    
		$this->settings = array(
			'style' => array(
				array(
					'id' => '3columns',
					'style' => $this->l('3 columns'),
				),
				array(
					'id' => 'big_cart',
					'style' => $this->l('Big cart'),
				),
			),
			'skin' => $this->prepareSkinList(),
		);
	}

    public function install() {
        if (!parent::install() || !$this->installDB()) {
            return FALSE;
        }

        foreach ($this->_hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return FALSE;
            }
        }

		$languages = Language::getLanguages();
		foreach ($this->_tabs as $tab) {
			$_tab = new Tab();
			$_tab->class_name = $tab['class_name'];
			$_tab->id_parent = Tab::getIdFromClassName($tab['parent']);
			if (empty($_tab->id_parent)) {
				$_tab->id_parent = -1;
			}

			$_tab->module = $this->name;
			foreach ($languages as $language) {
				$_tab->name[$language['id_lang']] = $this->l($tab['name']);
			}

			$_tab->add();
		}

        Configuration::updateValue('PS_ORDER_PROCESS_TYPE', 1);

        if (!$this->installConfiguration()) {
            return FALSE;
        }

        return TRUE;
    }

    public function uninstall() {
        if (!parent::uninstall() || !$this->uninstallDB()) {
            return FALSE;
        }

        return TRUE;
    }

    protected function installDb()
    {
        $return = TRUE;
        $return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'module_carrier` (
				`id_module_carrier` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_module` int(10) NOT NULL,
				`id_shop` int(10) NOT NULL,
				`id_carrier` int(10) NOT NULL,
				PRIMARY KEY (`id_module_carrier`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');

        /* Get all modules then select only payment ones */
        $modules = Module::getModulesOnDisk(true);
        $payments_gateways = array();
        foreach ($modules as $module) {
            if ($module->tab == 'payments_gateways') {
                if ($module->id) {
                    $payments_gateways[] = $module;
                }
            }
        }
        $carriers = Carrier::getCarriers(Context::getContext()->language->id, TRUE, FALSE, FALSE, NULL, 5);
        foreach ($payments_gateways as $payment_module) {
            foreach ($carriers as $carrier) {
                foreach (Shop::getShops() as $shop) {
                    Db::getInstance()->execute('
                        INSERT INTO `' . _DB_PREFIX_ . 'module_carrier`
                        (`id_module`, `id_shop`, `id_carrier`)
                        VALUES (' . (int)$payment_module->id . ', ' . (int)$shop['id_shop'] . ', ' . (int)$carrier['id_carrier'] . ')'
                    );
                }
            }
        }
		
		foreach ($this->_tabs as $tab) {
			$_tab_id = Tab::getIdFromClassName($tab['class_name']);
			$_tab = new Tab($_tab_id);
			$_tab->delete();
		}
		
		//checkout fields
        $return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield` (
				`id_bestkit_opc_checkoutfield` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`step` varchar(255) DEFAULT NULL,
				`name` varchar(255) DEFAULT NULL,
				`validate` varchar(255) DEFAULT NULL,
				`required` int(10) unsigned NOT NULL DEFAULT \'0\',
				`default_value` varchar(255) DEFAULT NULL,
				`position` int(10) unsigned NOT NULL DEFAULT \'0\',
				`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
				`standard` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
				PRIMARY KEY (`id_bestkit_opc_checkoutfield`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');
			
        $return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield_lang` (
				`id_bestkit_opc_checkoutfield` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_lang` int(10) unsigned NOT NULL,
				`public_name` varchar(255) DEFAULT NULL,
				PRIMARY KEY (`id_bestkit_opc_checkoutfield`,`id_lang`),
				KEY `id_lang` (`id_lang`,`public_name`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');
		
		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield_shop` (
				`id_bestkit_opc_checkoutfield` INT( 11 ) UNSIGNED NOT NULL,
				`id_shop` INT( 11 ) UNSIGNED NOT NULL,
				PRIMARY KEY (`id_bestkit_opc_checkoutfield`, `id_shop`)
			) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
			
		$this->installDefaultCheckoutFields();

        return $return;
    }

    protected function uninstallDb()
    {
        $return = TRUE;
        $return =& Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'module_carrier`');
        $return =& Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield`');
        $return =& Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield_lang`');
        $return =& Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield_shop`');
		
		return $return;
    }

    protected function getDefaultConfigVariables()
	{
		return array('PS_GUEST_CHECKOUT_ENABLED', 'PS_DISALLOW_HISTORY_REORDERING', 'PS_PURCHASE_MINIMUM', 'PS_SHIP_WHEN_AVAILABLE', 'PS_CONDITIONS', 'PS_CONDITIONS_CMS_ID',
				'PS_GIFT_WRAPPING', 'PS_GIFT_WRAPPING_PRICE', 'PS_GIFT_WRAPPING_TAX_RULES_GROUP', 'PS_RECYCLABLE_PACK', 'PS_CUSTOMER_CREATION_EMAIL');
	}
	
	protected function installDefaultCheckoutFields()
	{
		$checkout_fields = array(
			'customer' => array(
				array(
					'name' => 'email',
					'validate' => 'isEmail',
					'public_name' => $this->l('Email'),
					'required' => 1,
				),
				array(
					'name' => 'passwd',
					'validate' => 'isEmail',
					'public_name' => $this->l('Password'),
					'required' => 1,
				),
				array(
					'name' => 'id_gender',
					'validate' => 'isUnsignedId',
					'public_name' => $this->l('Title'),
				),
				array(
					'name' => 'lastname',
					'validate' => 'isName',
					'public_name' => $this->l('Lastname'),
					'required' => 1,
				),
				array(
					'name' => 'firstname',
					'validate' => 'isName',
					'public_name' => $this->l('Firstname'),
					'required' => 1,
				),
				array(
					'name' => 'birthday',
					'validate' => 'isBirthDate',
					'public_name' => $this->l('Date of Birth'),
				),
				array(
					'name' => 'newsletter',
					'validate' => 'isBool',
					'public_name' => $this->l('Sign up for our newsletter'),
				),
				array(
					'name' => 'optin',
					'validate' => 'isBool',
					'public_name' => $this->l('Receive special offers from our partners'),
				),
				array(
					'name' => 'website',
					'validate' => 'isUrl',
					'public_name' => $this->l('Website'),
					'active' => 0,
				),
				array(
					'name' => 'company',
					'validate' => 'isGenericName',
					'public_name' => $this->l('Company'),
					'active' => 0,
				),
				array(
					'name' => 'ape',
					'validate' => 'isApe',
					'public_name' => $this->l('APE'),
					'active' => 0,
				),
				array(
					'name' => 'siret',
					'validate' => 'isSiret',
					'public_name' => $this->l('Siret'),
					'active' => 0,
				),
			), 
			'invoice' => array(
				array(
					'name' => 'firstname',
					'validate' => 'isName',
					'public_name' => $this->l('First name'),
					'required' => 1,
				),
				array(
					'name' => 'lastname',
					'validate' => 'isName',
					'public_name' => $this->l('Last name'),
					'required' => 1,
				),
				array(
					'name' => 'company',
					'validate' => 'isGenericName',
					'public_name' => $this->l('Company'),
					'active' => 0,
				),
				array(
					'name' => 'vat_number',
					'validate' => 'isGenericName',
					'public_name' => $this->l('Vat'),
				),
				array(
					'name' => 'address1',
					'validate' => 'isAddress',
					'public_name' => $this->l('Address'),
					'required' => 1,
				),
				array(
					'name' => 'address2',
					'validate' => 'isAddress',
					'public_name' => $this->l('Address (Line 2)'),
				),
				array(
					'name' => 'city',
					'validate' => 'isCityName',
					'public_name' => $this->l('City'),
					'required' => 1,
				),
				array(
					'name' => 'postcode',
					'validate' => 'isPostCode',
					'public_name' => $this->l('Zip / Postal code'),
					'required' => 1,
				),
				array(
					'name' => 'id_country',
					'validate' => 'isUnsignedId',
					'public_name' => $this->l('Country'),
					'required' => 1,
				),
				array(
					'name' => 'id_state',
					'validate' => 'isNullOrUnsignedId',
					'public_name' => $this->l('State'),
				),
				array(
					'name' => 'other',
					'validate' => 'isMessage',
					'public_name' => $this->l('Additional information'),
				),
				array(
					'name' => 'phone',
					'validate' => 'isPhoneNumber',
					'public_name' => $this->l('Home phone'),
				),
				array(
					'name' => 'phone_mobile',
					'validate' => 'isPhoneNumber',
					'public_name' => $this->l('Mobile phone'),
				),
				array(
					'name' => 'dni',
					'validate' => 'isDniLite',
					'public_name' => $this->l('DNI'),
					'active' => 0,
				),
			), 
			'delivery' => array(), 
		);
		$checkout_fields['delivery'] = $checkout_fields['invoice'];
		
		$languages = Language::getLanguages();
		foreach ($checkout_fields as $step => $step_fields) {
			foreach ($step_fields as $checkout_field) {
				if (!BestkitOpcCheckoutFields::checkExists($checkout_field['name'], $step)) {
					$field = new BestkitOpcCheckoutFields();
					$field->step = $step;
					$field->name = $checkout_field['name'];
					$field->validate = $checkout_field['validate'];
					$field->required = isset($checkout_field['required']) ? $checkout_field['required'] : 0;
					$field->default_value = isset($checkout_field['default_value']) ? $checkout_field['default_value'] : '';
					$field->active = isset($checkout_field['active']) ? $checkout_field['active'] : 1;
					$field->standard = isset($checkout_field['standard']) ? $checkout_field['standard'] : 1;
					foreach ($languages as $language) {
						$field->public_name[$language['id_lang']] = $checkout_field['public_name'];
					}
					
					$field->add();
				}
			}
		}
	}
	
	public function getOnOffValues($attr_name)
	{
		return array(
			array(
				'id' => $attr_name . '_on',
				'value' => 1,
				'label' => $this->l('Enabled')
			),
			array(
				'id' => $attr_name . '_off',
				'value' => 0,
				'label' => $this->l('Disabled')
			)
		);
	}
	
    public function installConfiguration()
    {
        foreach ($this->_moduleParams as $param => $value) {
			if (in_array($param, $this->getDefaultConfigVariables())) {
				continue;
			}
			
            if (!$this->setConfig($param, $value)) {
                return FALSE;
            }
        }

        foreach ($this->_moduleParamsLang as $param => $value) {
            $values = array();
            foreach (Language::getLanguages(FALSE) as $lang) {
                $values[$lang['id_lang']] = $value;
            }

            if (!$this->setConfig($param, $values)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function getConfig($name)
    {
        if (array_key_exists($name, $this->_moduleParamsLang)) {
            $values = array();
            foreach (Language::getLanguages(FALSE) as $lang) {
                $values[$lang['id_lang']] = Configuration::get(self::PREFIX . $name, $lang['id_lang']);
            }

            return $values;
        } else {
			/*if (strpos($name, '[]')) {
				return Tools::jsonDecode(Configuration::get(self::PREFIX . $name));
			}*/
			
            return Configuration::get(self::PREFIX . $name);
        }
    }

    public function setConfig($name, $value)
    {
		if (in_array($name, $this->getDefaultConfigVariables())) {
			return Configuration::updateValue($name, $value, TRUE); //update without prefix -> update global config
		}

		/*
        if (is_array($value)) {
            return Configuration::updateValue(self::PREFIX . $name, Tools::jsonEncode($value), TRUE);
        }
		*/
		
        return Configuration::updateValue(self::PREFIX . $name, $value, TRUE);
    }
	
	public function prepareSkinList()
	{
		$skin_list = array();
		if ($handle = opendir(_PS_MODULE_DIR_ . 'bestkit_opc' . DS . 'css' . DS . 'skin')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$skin_list[] = array(
						'id' => $entry,
						'skin' => $entry,
					);
				}
			}
			
			closedir($handle);
		}
		
		return $skin_list;
	}

    public function initForm() {
		require_once _PS_MODULE_DIR_ . 'bestkit_opc/classes/BestkitHelperForm.php';
		
        $helper = new BestkitHelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->toolbar_scroll = TRUE;
        $helper->toolbar_btn = $this->initToolbar();
        $helper->title = $this->displayName;
        //$helper->submit_action = 'submitUpdate';

        $languages = Language::getLanguages(FALSE);
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = (int)($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }

        $helper->languages = $languages;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$cms_collection = new Collection('CMS', $this->context->language->id);
		$terms_and_cond = array();
		foreach ($cms_collection as $cms) {
			$terms_and_cond[] = array(
				'id' => $cms->id,
				'PS_CONDITIONS_CMS_ID' => $cms->meta_title
			);
		}
		
		$tax_rules_collection = new Collection('TaxRulesGroup', $this->context->language->id);
		$tax_rules_group = array();
		foreach ($tax_rules_collection as $tax_rule) {
			$tax_rules_group[] = array(
				'id' => $tax_rule->id,
				'PS_GIFT_WRAPPING_TAX_RULES_GROUP' => $tax_rule->name
			);
		}

		$group_customer_collection = new Collection('Group', $this->context->language->id);
		$group_customer = array();
		foreach ($group_customer_collection as $group_customer_item) {
            $group_customer[] = array(
				'id' => $group_customer_item->id,
				'group_customer' => $group_customer_item->name,
				//'also_group_customer' => $group_customer_item->name
			);
		}

        $this->fields_form['opc_settings']['form'] = array(
            'tinymce' => TRUE,
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Style'),
                    'name' => 'style',
                    'options' => array(
                        'query' => $this->settings['style'],
                        'id' => 'id',
                        'name' => 'style',
                    ),
                ),
                /*array(
                    'type' => 'select',
                    'label' => $this->l('Skin'),
                    'name' => 'skin',
                    'options' => array(
                        'query' => $this->settings['skin'],
                        'id' => 'id',
                        'name' => 'skin',
                    ),
                ),*/
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show billing'),
                    'name' => 'billing',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('billing'),
                ),
                /*array(
                    'type' => 'switch',
                    'label' => $this->l('Show delivery address for virtual products'),
                    'name' => 'delivery_step_virt',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('delivery_step_virt'),
                ),*/
                array(
                    'type' => 'switch',
                    'label' => $this->l('Request Terms & Conditions'),
                    'name' => 'PS_CONDITIONS',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('PS_CONDITIONS'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('CMS page for the Conditions of use'),
                    'name' => 'PS_CONDITIONS_CMS_ID',
                    'options' => array(
                        'query' => $terms_and_cond,
                        'id' => 'id',
                        'name' => 'PS_CONDITIONS_CMS_ID',
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Allow guest checkout'),
                    'name' => 'PS_GUEST_CHECKOUT_ENABLED',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('PS_GUEST_CHECKOUT_ENABLED'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Disable Reordering Option'),
                    'name' => 'PS_DISALLOW_HISTORY_REORDERING',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('PS_DISALLOW_HISTORY_REORDERING'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Minimum purchase total required in order to validate the order'),
                    'desc' => $this->l('tax excl'),
                    'name' => 'PS_PURCHASE_MINIMUM',
                    'required' => false,
                    'class' => 't',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Delayed shipping'),
                    'name' => 'PS_SHIP_WHEN_AVAILABLE',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('PS_SHIP_WHEN_AVAILABLE'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Offer gift wrapping'),
                    'name' => 'PS_GIFT_WRAPPING',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('PS_GIFT_WRAPPING'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Gift-wrapping price'),
                    'desc' => $this->l('tax excl'),
                    'name' => 'PS_GIFT_WRAPPING_PRICE',
                    'required' => false,
                    'class' => 't',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Gift-wrapping tax'),
                    'name' => 'PS_GIFT_WRAPPING_TAX_RULES_GROUP',
                    'options' => array(
                        'query' => $tax_rules_group,
                        'id' => 'id',
                        'name' => 'PS_GIFT_WRAPPING_TAX_RULES_GROUP',
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Offer recycled packaging'),
                    'name' => 'PS_RECYCLABLE_PACK',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('PS_RECYCLABLE_PACK'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Send an email after registration'),
                    'name' => 'PS_CUSTOMER_CREATION_EMAIL',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('PS_CUSTOMER_CREATION_EMAIL'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show the `Continue shopping` link'),
                    'name' => 'continue_shopping',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('continue_shopping'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show the breadcrumbs'),
                    'name' => 'show_breadcrumbs',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('show_breadcrumbs'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Allow customer manual select customer group'),
                    'name' => 'allow_select_group',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('allow_select_group'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Default customer group'),
                    'name' => 'group_customer',
                    'options' => array(
                        'query' => $group_customer,
                        'id' => 'id',
                        'name' => 'group_customer',
                    ),
                ),
                /*array(
                    'type' => 'select',
                    'label' => $this->l('Also register new customers in the groups'),
                    'name' => 'also_group_customer[]',
                    'multiple' => TRUE,
                    'options' => array(
                        'query' => $group_customer,
                        'id' => 'id',
                        'name' => 'also_group_customer',
                    ),
                ),*/
            )
        );
		
        $this->fields_form['opc_order_summary']['form'] = array(
            'tinymce' => TRUE,
            /*'legend' => array(
                'title' => $this->l('General configuration'),
                'image' => $this->_path . 'logo.gif'
            ),
            'submit' => array(
                'name' => 'submitUpdate',
                'title' => $this->l('   Save   '),
            ),*/
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show total products'),
                    'name' => 'show_total_products',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('show_total_products'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show total discount'),
                    'name' => 'show_total_discount',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('show_total_discount'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show total wrapping'),
                    'name' => 'show_total_wrapping',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('show_total_wrapping'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show total shipping'),
                    'name' => 'show_total_shipping',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('show_total_shipping'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show total without tax'),
                    'name' => 'show_total_tax_excl',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('show_total_tax_excl'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show total tax'),
                    'name' => 'show_total_tax',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('show_total_tax'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show total'),
                    'name' => 'show_total',
                    'required' => false, 
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('show_total'),
                ),
            )
        );

        //$this->context->smarty->assign('shipadnpay', Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'module_carrier`'));
        $this->assignShipAndPayInfo();
        $this->fields_form['opc_ship_to_pay']['form'] = array(
            'tinymce' => TRUE,
            'col' => '12 ',
            /*'legend' => array(
                'title' => $this->l('General configuration'),
                'image' => $this->_path . 'logo.gif'
            ),
            'submit' => array(
                'name' => 'submitUpdate',
                'title' => $this->l('   Save   '),
            ),*/
            'input' => array(
                array(
                    'type' => 'html',
                    'id' => 'shipadnpay',
                    'label' => '',
                    'name' => $this->context->smarty->createTemplate($this->getTemplatePath('shipadnpay.tpl', 'admin'), $this->context->smarty)->fetch(),
                ),
            )
        );
		
        $this->assignCheckoutFieldsInfo();
        $this->fields_form['opc_checkout_fields']['form'] = array(
            'tinymce' => TRUE,
            'col' => '12 ',
            /*'legend' => array(
                'title' => $this->l('General configuration'),
                'image' => $this->_path . 'logo.gif'
            ),
            'submit' => array(
                'name' => 'submitUpdate',
                'title' => $this->l('   Save   '),
            ),*/
            'input' => array(
                array(
                    'type' => 'html',
                    'id' => 'checkout_fields',
                    'label' => '',
                    'name' => $this->context->smarty->createTemplate($this->getTemplatePath('checkout_fields.tpl', 'admin'), $this->context->smarty)->fetch(),
                ),
            )
        );
		
		$step = array();
		$available_steps = array('customer', 'invoice', 'delivery');
		foreach ($available_steps as $available_step) {
			$step[] = array(
				'id' => $available_step,
				'step' => $available_step,
			);
		}
		
		$validate = array();
		$reflection = new ReflectionClass('Validate');
		$aMethods = $reflection->getMethods();
		foreach ($aMethods as $reflectionMethodObject) {
			$validate[] = array(
				'id' => $reflectionMethodObject->name,
				'validate' => $reflectionMethodObject->name,
			);
		}
	
        $this->fields_form['opc_checkout_fields_new']['form'] = array(
            'tinymce' => TRUE,
            'col' => '12',
            'legend' => array(
                'title' => $this->l('Create new field'),
                'title' => $this->l('Create new field'),
                //'image' => $this->_path . 'logo.gif'
            ),
            'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Step'),
					'required' => TRUE,
					'name' => 'step',
					'options' => array(
						'query' => $step,
						'id' => 'id',
						'name' => 'step',
					),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Name'),
					'required' => TRUE,
					'name' => 'name',
					'col' => 3,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Public name'),
					'name' => 'public_name',
					'lang' => TRUE,
					'required' => TRUE,
					'col' => 3,
				),
				array(
					'type' => 'select',
					'label' => $this->l('Validator'),
					'name' => 'validate',
					'options' => array(
						'query' => $validate,
						'id' => 'id',
						'name' => 'validate',
					),
				),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is required'),
                    'name' => 'required',
                    'required' => false, 
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('required'),
                ),
				array(
					'type' => 'text',
					'label' => $this->l('Default value'),
					'name' => 'default_value',
					'col' => 3,
				),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => false, 
                    'class' => 't',
                    'is_bool' => true,
                    'values' => $this->getOnOffValues('active'),
                ),
            ),
            'buttons' => array(
                array(
					'type' => 'submit',
					'id' => 'submit_new_field',
					'name' => 'submit_new_field',
					'class' => 'pull-right',
					'icon' => 'process-icon-save',
					'title' => $this->l('Save field'),
				),
			)
        );

        return $helper;
    }
	
	protected function assignCheckoutFieldsInfo() {
		$this->context->controller->addjqueryPlugin('sortable');

		$checkout_fields = array();
		$checkout_fields_steps = BestkitOpcCheckoutFields::getSteps();
		foreach ($checkout_fields_steps as $step) {
			$checkout_fields[] = array(
				'step' => $step['step'], 
				'fields' => BestkitOpcCheckoutFields::getFieldsForStep($step['step'], $this->context->language->id, $this->context->shop->id)
			);
		}

        $this->context->smarty->assign(array(
            'checkout_fields' => $checkout_fields,
            'opc_controller_url' => Dispatcher::getInstance()->createUrl('AdminModules', $this->context->language->id, array('token' => Tools::getAdminTokenLite('AdminModules'), 'configure' => 'bestkit_opc',), FALSE),
        ));
	}

    protected function assignShipAndPayInfo() {
        $shop_id = Context::getContext()->shop->id;

        /* Get all modules then select only payment ones */
        $modules = Module::getModulesOnDisk(true);
        $_payment_modules = array();
        foreach ($modules as $module) {
            if ($module->tab == 'payments_gateways') {
                $_payment_modules[] = $module;
            }
        }

        foreach ($_payment_modules as &$module) {
            if ($module->id) {
                $module->carrier = array();

                $carriers = DB::getInstance()->executeS('
					SELECT id_carrier
					FROM '._DB_PREFIX_.'module_carrier
					WHERE id_module = '.(int)$module->id.' AND `id_shop`='.(int)$shop_id
                );

                foreach ($carriers as $carrier) {
                    $module->carrier[] = $carrier['id_carrier'];
                }
            } else {
                $module->carrier = null;
            }
        }

//print_r($_payment_modules); die;
        $this->context->smarty->assign(array(
            'shipadnpay' => $_payment_modules,
            'carriers' => Carrier::getCarriers(Context::getContext()->language->id, TRUE, FALSE, FALSE, NULL, 5),
        ));
        return $_payment_modules;
    }

    private function initToolbar() {
        $this->toolbar_btn['save'] = array(
            'href' => '#',
            'desc' => $this->l('Save')
        );

        return $this->toolbar_btn;
    }

    protected function postProcess() {
        if (Tools::isSubmit('submitUpdate')) {
            foreach (array_keys($this->_moduleParams) as $param) {
                $this->setConfig($param, Tools::getValue($param));
            }

            if (Tools::isSubmit('carrier')) {
                $carriers = Tools::getValue('carrier');

                $modules = array();
                $_payment_modules = $this->assignShipAndPayInfo();
                foreach ($_payment_modules as $module) {
                    if ($module->active) {
                        $modules[] = (int)$module->id;
                    }
                }
                Db::getInstance()->execute('
                    DELETE FROM `'._DB_PREFIX_.'module_carrier`
                    WHERE id_shop = '.Context::getContext()->shop->id.'
                    AND `id_module` IN ('.implode(', ', $modules).')'
                );

                // Fill the new restriction selection for active module.
                foreach ($carriers as $id_carier => $carrier_payment_modules) {
                    $values = array();
                    foreach (array_keys($carrier_payment_modules) as $id_payment_modules) {
                        $values[] = '('.(int)$id_payment_modules.', '.(int)Context::getContext()->shop->id.', '.(int)$id_carier.')';
                    }

                    //print_r($values);
                    if (count($values)) {
                        Db::getInstance()->execute('
                            INSERT INTO `'._DB_PREFIX_.'module_carrier`
                            (`id_module`, `id_shop`, `id_carrier`)
                            VALUES '.implode(',', $values));
                    }
                }
            }
			
            if (Tools::isSubmit('checkout_field')) {
                $checkout_fields = Tools::getValue('checkout_field');
				foreach ($checkout_fields as $fields) {
					$new_position = 0;
					foreach ($fields as $id) {
						$tmpObj = new BestkitOpcCheckoutFields($id);
						if ($tmpObj->id && $tmpObj->position != $new_position) {
							$tmpObj->position = (int)$new_position;
							$tmpObj->update();
						}
						
						$new_position++;
					}
				
					//BestkitOpcCheckoutFields::cleanPositionsManually($step);
				}
			}

            Tools::redirectAdmin('index.php?tab=AdminModules&conf=4&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int) (Tab::getIdFromClassName('AdminModules')) . (int) $this->context->employee->id));
        }
		
		if (Tools::isSubmit('submit_new_field')) {
			$fieldObj = new BestkitOpcCheckoutFields();
			$fieldObj->step = Tools::getValue('step');
			$fieldObj->name = Tools::getValue('name');
			$fieldObj->validate = Tools::getValue('validate');
			$fieldObj->required = Tools::getValue('required');
			$fieldObj->default_value = Tools::getValue('default_value');
			$fieldObj->active = Tools::getValue('active');
			$languages = Language::getLanguages();
			foreach ($languages as $language) {
				$fieldObj->public_name[$language['id_lang']] = Tools::getValue('public_name_' . $language['id_lang']);
			}
//print_r($fieldObj); die;
			if ($fieldObj->add()) {
				Tools::redirectAdmin('index.php?tab=AdminModules&conf=3&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int) (Tab::getIdFromClassName('AdminModules')) . (int) $this->context->employee->id));
			}
		}
		
		if (Tools::isSubmit('submit_edit_field')) {
			$id_bestkit_opc_checkoutfield = (int)Tools::getValue('id_bestkit_opc_checkoutfield');
			if ($id_bestkit_opc_checkoutfield) {
				$fieldObj = new BestkitOpcCheckoutFields($id_bestkit_opc_checkoutfield);
				$fieldObj->step = Tools::getValue('step');
				$fieldObj->name = Tools::getValue('name');
				$fieldObj->validate = Tools::getValue('validate');
				$fieldObj->required = Tools::getValue('required_new');
				$fieldObj->default_value = Tools::getValue('default_value');
				$fieldObj->active = Tools::getValue('active_new');
				$languages = Language::getLanguages();
				foreach ($languages as $language) {
					$fieldObj->public_name[$language['id_lang']] = Tools::getValue('public_name_' . $language['id_lang']);
				}

				if ($fieldObj->update()) {
					Tools::redirectAdmin('index.php?tab=AdminModules&conf=4&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int) (Tab::getIdFromClassName('AdminModules')) . (int) $this->context->employee->id));
				}
			}
		}
		
		if (Tools::isSubmit('delete_field')) {
			$id_bestkit_opc_checkoutfield = (int)Tools::getValue('id_bestkit_opc_checkoutfield');
			if ($id_bestkit_opc_checkoutfield) {
				$fieldObj = new BestkitOpcCheckoutFields($id_bestkit_opc_checkoutfield);

				if ($fieldObj->delete()) {
					Tools::redirectAdmin('index.php?tab=AdminModules&conf=1&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int) (Tab::getIdFromClassName('AdminModules')) . (int) $this->context->employee->id));
				}
			}
		}
    }

    public function getContent() {
        $this->postProcess();
        $helper = $this->initForm();

        foreach ($this->fields_form as $fieldset) {
            foreach ($fieldset['form']['input'] as $input) {
                $helper->fields_value[$input['name']] = $this->getConfig($input['name']);
            }
        }
		
		$helper->fields_value['PS_GUEST_CHECKOUT_ENABLED'] = Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
		$helper->fields_value['PS_DISALLOW_HISTORY_REORDERING'] = Configuration::get('PS_DISALLOW_HISTORY_REORDERING');
		$helper->fields_value['PS_PURCHASE_MINIMUM'] = Configuration::get('PS_PURCHASE_MINIMUM');
		$helper->fields_value['PS_SHIP_WHEN_AVAILABLE'] = Configuration::get('PS_SHIP_WHEN_AVAILABLE');
		$helper->fields_value['PS_CONDITIONS'] = Configuration::get('PS_CONDITIONS');
		$helper->fields_value['PS_CONDITIONS_CMS_ID'] = Configuration::get('PS_CONDITIONS_CMS_ID');
		$helper->fields_value['PS_GIFT_WRAPPING'] = Configuration::get('PS_GIFT_WRAPPING');
		$helper->fields_value['PS_GIFT_WRAPPING_PRICE'] = Configuration::get('PS_GIFT_WRAPPING_PRICE');
		$helper->fields_value['PS_GIFT_WRAPPING_TAX_RULES_GROUP'] = Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP');
		$helper->fields_value['PS_RECYCLABLE_PACK'] = Configuration::get('PS_RECYCLABLE_PACK');
		$helper->fields_value['PS_CUSTOMER_CREATION_EMAIL'] = Configuration::get('PS_CUSTOMER_CREATION_EMAIL');
		
		$url_params = array(
			'token' => Tools::getAdminTokenLite('AdminModules'),
			'configure' => 'bestkit_opc',
		);
		$ajax_url_params = array(
			'token' => Tools::getAdminTokenLite('AdminBestkitOpcFields'),
			'ajax' => 1,
			'action' => 'loadFieldInfo',
		);

		$this->context->smarty->assign(array(
			'opc_settings' => $helper->generateForm(array($this->fields_form['opc_settings'])),
			'opc_order_summary' => $helper->generateForm(array($this->fields_form['opc_order_summary'])),
			'opc_checkout_fields' => $helper->generateForm(array($this->fields_form['opc_checkout_fields'])),
			'opc_ship_to_pay' => $helper->generateForm(array($this->fields_form['opc_ship_to_pay'])),
			'opc_controller_url' => Dispatcher::getInstance()->createUrl('AdminModules', $this->context->language->id, $url_params, FALSE),
			'opc_controller_ajax' => Dispatcher::getInstance()->createUrl('AdminBestkitOpcFields', $this->context->language->id, $ajax_url_params, FALSE),
			'opc_checkout_fields_new' => $helper->generateForm(array($this->fields_form['opc_checkout_fields_new'])),
		));
		
		return $this->context->smarty->fetch($this->getTemplatePath('tabs.tpl', 'admin'));

        //return $helper->generateForm($this->fields_form);

        /*$redirect = $this->context->link->getAdminLink('AdminOrderPreferences');
        header('Location: ' . $redirect);
        exit;*/
    }

    public function getTemplatePath($file = '', $type = 'front') {
        return _PS_MODULE_DIR_ . 'bestkit_opc' . DS . 'views' . DS . 'templates' . DS . $type . DS . $file;
    }

    public function getTemplatePathTheme($file) {
        return _PS_THEME_DIR_ . DS . $file;
    }

    public function hookActionCustomerAccountAdd($params)
    {
        if ($params['newCustomer'] instanceof Customer) {
            if (isset($params['_POST']['customer_groups'])) {
                $customer_groups = array();
                foreach ($params['_POST']['customer_groups'] as $_customer_groups) {
                    if (is_array($_customer_groups)) {
                        foreach (explode(',', $_customer_groups) as $_customer_group) {
                            $customer_groups[] = (int)$_customer_group;
                        }
                    } else {
                        $customer_groups[] = (int)$_customer_groups;
                    }
                }

                $params['newCustomer']->addGroups($customer_groups);
            }

            if ($this->getConfig('group_customer') && $params['newCustomer']->id_default_group != $this->getConfig('group_customer')) {
                $params['newCustomer']->id_default_group = (int)$this->getConfig('group_customer');
                $params['newCustomer']->update();
            }
        }
    }

}
