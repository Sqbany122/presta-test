<?php
/**
* 2007-2014 PrestaShop
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
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class bestkit_livechat extends Module
{
	const PREFIX = 'BK_LC';
	const OFFLINE_PERIOD = 120; //in seconds

	protected $config_form = false;
	
	protected $_hooks = array(
		'header',
		'displayBackOfficeFooter',
		'displayBackOfficeHeader',
		'displayFooter',
	);

	protected static $_configs = array(
		'employees' => array(1),
		'operator_name' => 'LiveChat',
		'first_message' => 'Can we help you?',
		'chat_style' => '#428bca',
		'last_admin_activity' => 0,
		'current_mode' => 'auto',
		'interval' => 5000,
		'recaptcha_secretkey' => '',
		'recaptcha_sitekey' => '',
	);

	public function __construct()
	{
		$this->name = 'bestkit_livechat';
		$this->tab = 'advertising_marketing';
		$this->version = '1.0.0';
		$this->author = 'best-kit.com';
		$this->need_instance = 0;
		$this->module_key = 'bd9e56b99d66b618e17549c571706192';

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Responsive Live Chat');
		$this->description = $this->l('Responsive Live Chat');

		$this->confirmUninstall = $this->l('');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

		if (!session_id()) {
			session_start();
		}
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install()
	{
		include(dirname(__FILE__).'/sql/install.php');
		
		foreach (self::$_configs as $name => $value) {
			self::getConfig($name, $value);
		}

		$install = parent::install();
		foreach ($this->_hooks as $hook) {
			$this->registerHook($hook);
		}

        $languages = Language::getLanguages();
        $_tab = new Tab();
        $_tab->class_name = 'AdminBestkitLivechat';
        $_tab->id_parent = Tab::getIdFromClassName('AdminParentModules');
        $_tab->active = 0;
        if (empty($_tab->id_parent)) {
            $_tab->id_parent = 0;
        }

        $_tab->module = $this->name;
        foreach ($languages as $language) {
            $_tab->name[$language['id_lang']] = 'BestKit LiveChat Ajax';
        }

        $_tab->add();

		return $install;
	}

	public function uninstall()
	{
		include(dirname(__FILE__).'/sql/uninstall.php');
		foreach ($this->_hooks as $hook) {
			$this->unregisterHook($hook);
		}

        $idTab = Tab::getIdFromClassName('AdminBestkitLivechat');
        if ($idTab) {
            $_tab = new Tab($idTab);
            $_tab->delete();
        }

		return parent::uninstall();
	}

	public static function getConfig($name, $value = null)
	{
		$result = false;
		if (array_key_exists($name, self::$_configs)) {
			$key = self::PREFIX . $name;
			if (is_null($value)) {
				if ($name == 'last_admin_activity') {
					$result = Configuration::getGlobalValue($key);
				} else {
					$result = Configuration::get($key);
				}

				if (is_array(self::$_configs[$name])) {
					$result = explode(',', $result);
				}
			} else {
				if (is_array(self::$_configs[$name])) {
					$value = implode(',', $value);
				} else {
					$value = pSQL($value);
				}

				if ($name == 'last_admin_activity') {
					$result = Configuration::updateGlobalValue($key, $value);
				} else {
					$result = Configuration::updateValue($key, $value);
				}
			}
		}

		return $result;
	}

	public function getConfigureUrl($conf = false)
	{
		return $this->context->link->getAdminLink('AdminModules')
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name
			.($conf ? '&conf=4' : '');
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{
		/**
		 * If values have been submitted in the form, process.
		 */
		
		if (Tools::getIsset('submitBestkit_livechatModule')) {
			$this->_postProcess();
		}

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
		$helper->submit_action = 'submitBestkit_livechatModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		foreach (array_keys(self::$_configs) as $key) {
			$helper->tpl_vars['fields_value'][$key] = self::getConfig($key);
		}
		
		//print_r($helper->tpl_vars['fields_value']); die;
		$helper->tpl_vars['fields_value']['employees[]'] = self::getConfig('employees');

		return $helper->generateForm(array($this->getConfigForm()));
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm()
	{
		$employees = Employee::getEmployees();
		foreach ($employees as &$employee) {
			$employee['name'] = $employee['firstname'] . ' ' . $employee['lastname'];
		}

		return array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Live Chat Settings'),
				'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Allow to employees'),
						'name' => 'employees[]',
						'multiple' => TRUE,
						'desc' => $this->l('The chat will allow to these employees in admin panel'),
						'class' => ' fixed-width-xxl',
						'size' => 5,
						'required' => true,
		                'options' => array(
		                    'query' => $employees,
		                    'id' => 'id_employee',
		                    'name' => 'name',
		                )
					),
					array(
						'type' => 'text',
						'name' => 'operator_name',
						'label' => $this->l('Operator name'),
						'class' => ' fixed-width-xxl',
						'required' => true,
					),
					array(
						'type' => 'text',
						'name' => 'first_message',
						'label' => $this->l('First Message'),
						'class' => ' fixed-width-xxl',
						'required' => true,
					),
					array(
						'type' => 'color',
						'name' => 'chat_style',
						'label' => $this->l('Chat color style'),
					),
	                array(
	                    'type' => 'radio',
	                    'label' => $this->l('Online/Offline Mode:'),
	                    'name' => 'current_mode',
	                    'class' => 't',
	                    //'is_bool' => TRUE,
	                    'values' => array(array(
			                'id' => 'mode_auto',
			                'value' => 'auto',
			                'label' => $this->l('Automatically')), array(
			                'id' => 'mode_online',
			                'value' => 'online',
			                'label' => $this->l('Online')), array(
			                'id' => 'mode_offline',
			                'value' => 'offline',
			                'label' => $this->l('Offline'))
			            )
	                ),
					array(
						'type' => 'text',
						'name' => 'interval',
						'label' => $this->l('Interval between the requests'),
						'desc' => $this->l('in milliseconds'),
					),
					array(
						'type' => 'text',
						'name' => 'recaptcha_sitekey',
						'label' => $this->l('reCAPTCHA Site Key'),
						'required' => true,
					),
					array(
						'type' => 'text',
						'name' => 'recaptcha_secretkey',
						'label' => $this->l('reCAPTCHA Secret Key'),
						'required' => true,
						'desc' => $this->l('Please visit this page to get reCaptcha: ') . '<a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">https://www.google.com/recaptcha/intro/index.html</a>'
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
			),
		);
	}

	/**
	 * Save form data.
	 */
	protected function _postProcess()
	{
		foreach (array_keys(self::$_configs) as $key) {
			self::getConfig($key, Tools::getValue($key));
		}
		
		Tools::redirectAdmin($this->getConfigureUrl(true));
	}

	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader()
	{
		//$this->context->controller->addJqueryUI('effects.shake');
		$this->context->controller->addJS($this->_path.'/js/front.js');
		$this->context->controller->addCSS($this->_path.'/css/front.css');
		return '<script src="https://www.google.com/recaptcha/api.js"></script>';
	}

	public function isAllowToCurrentEmployee()
	{
		if (in_array($this->context->employee->id, self::getConfig('employees'))) {
			return true;
		}

		return false;
	}

	public function isAdminOnline()
	{
		$mode = $this->getConfig('current_mode');
		if ($mode == 'online') {
			return true;
		}

		if ($mode == 'offline') {
			return false;
		}

		$last_admin_activity = self::getConfig('last_admin_activity');
		return (time() - $last_admin_activity < self::OFFLINE_PERIOD);
	}

	/**
	* Add the CSS & JavaScript files you want to be loaded in the BO.
	*/
	public function hookDisplayBackOfficeHeader()
	{
		if (!$this->isAllowToCurrentEmployee()) {
			return null;
		}

		self::getConfig('last_admin_activity', time());

		$this->context->controller->addJS($this->_path.'js/back.js');
		$this->context->controller->addCSS($this->_path.'css/back.css');
	}

	public function hookDisplayBackOfficeFooter()
	{
		if (!$this->isAllowToCurrentEmployee()) {
			return null;
		}

		$vars = array('bestkit_livechat' => array(
			'configure_url' => $this->getConfigureUrl(),
			'users' => $this->getUsers(),
			'total_new_messages' => $this->getTotalNewMessages(),
			'last_message_time' => $this->getLastMessageTime(),
		));

		$this->context->smarty->assign($vars);

		return $this->display(__FILE__, 'views/templates/admin/chat.tpl');
	}

	public function hookDisplayFooter()
	{
		$last_admin_activity = self::getConfig('last_admin_activity');
		$vars = array('bestkit_livechat' => array(
			'operator_name' => self::getConfig('operator_name'),
			'first_message' => self::getConfig('first_message'),
			'chat_style' => self::getConfig('chat_style'),
			'last_admin_activity' => $last_admin_activity,
			'is_online' => $this->isAdminOnline(), //(time() - $last_admin_activity < self::OFFLINE_PERIOD),
			'is_logged' => $this->isUserLogged(),
			'sound_url' => $this->_path . 'sound/sound.mp3',
			'color' => self::getConfig('chat_style'),
			'recaptcha_sitekey' => self::getConfig('recaptcha_sitekey'),
			'interval' => self::getConfig('interval'),
		));

		$this->context->smarty->assign($vars);
		return $this->display(__FILE__, 'chat.tpl');
	}

	public static function relative_date($timestamp, $days = false, $format = "M j, Y") 
	{
	  if (!is_numeric($timestamp)) {
	    // It's not a time stamp, so try to convert it...
	    $timestamp = strtotime($timestamp);
	  }
	  
	  if (!is_numeric($timestamp)) {
	    // If its still not numeric, the format is not valid
	    return false;
	  }
	  
	  // Calculate the difference in seconds
	  $difference = time() - $timestamp;
	  
	  // Check if we only want to calculate based on the day
	  if ($days && $difference < (60*60*24)) { 
	    return "Dzisiaj"; 
	  }
	  if ($difference < 3) { 
	    return "Teraz"; 
	  }
	  if ($difference < 60) {    
	    return $difference . " sekund temu"; 
	  }
	  if ($difference < (60*2)) {    
	    return "1 minute temu"; 
	  }
	  if ($difference < (60*60)) { 
	    return (int)($difference / 60) . " minut temu"; 
	  }
	  if ($difference < (60*60*2)) { 
	    return "1 hour ago"; 
	  }
	  if ($difference < (60*60*24)) {    
	    return (int)($difference / (60*60)) . " godzin temu"; 
	  }
	  if ($difference < (60*60*24*2)) { 
	    return "1 day ago"; 
	  }
	  if ($difference < (60*60*24*7)) { 
	    return (int)($difference / (60*60*24)) . " dni temu"; 
	  }
	  if ($difference < (60*60*24*7*2)) { 
	    return "1 week ago"; 
	  }
	  if ($difference < (60*60*24*7*(52/12))) { 
	    return (int)($difference / (60*60*24*7)) . " tygodni temu"; 
	  }
	  if ($difference < (60*60*24*7*(52/12)*2)) { 
	    return "1 miesiąc temu"; 
	  }
	  if ($difference < (60*60*24*364)) { 
	    return (int)($difference / (60*60*24*7*(52/12))) . " miesięcy temu"; 
	  }
	  
	  // More than a year ago, just return the formatted date
	  return @date($format, $timestamp);
	 
	}

	public function isUserLogged()
	{
		if (isset($_SESSION['livechat_key'])) {
			$user = $this->getUserByKey($_SESSION['livechat_key']);
			if ($user && $user['user_key'] == $_SESSION['livechat_key']) {
				return true;
			}
		}
		
		return false;
	}

	public function initUser()
	{
		if (isset($_SESSION['livechat_key'])) {
			$liveChatKey = $_SESSION['livechat_key'];
		} else {
			$liveChatKey = md5(Context::getContext()->shop->id . bestkit_livechat::PREFIX . microtime());
			$_SESSION['livechat_key'] = $liveChatKey;
		}

		return $liveChatKey;
	}
	
	public static function getTotalNewMessages()
	{
		return Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'bestkit_livechat_message`
			WHERE `is_new` = 1
		');
	}

	public static function getLastMessageTime($is_admin = 0)
	{
		return (int)Db::getInstance()->getValue('
			SELECT `date_add` FROM `' . _DB_PREFIX_ . 'bestkit_livechat_message`
			WHERE `is_admin` = ' . (int)$is_admin . '
			ORDER BY `date_add` DESC
		');
	}

	public static function getUsers()
	{
		return Db::getInstance()->ExecuteS('
			SELECT u.*, COUNT(m.`id_bestkit_livechat_message`) as `all_messages`, SUM(m.`is_new`) as `new_messages` FROM `' . _DB_PREFIX_ . 'bestkit_livechat_user` u
			LEFT JOIN `' . _DB_PREFIX_ . 'bestkit_livechat_message` m ON (u.`user_key` = m.`user_key`)
			GROUP BY u.`user_key`
			ORDER BY m.`date_add` ASC
		');
	}

	public static function getMessagesByUserKey($userKey)
	{
		return Db::getInstance()->ExecuteS('
			SELECT * FROM `' . _DB_PREFIX_ . 'bestkit_livechat_message`
			WHERE `user_key` = "' . $userKey . '"
			ORDER BY `date_add` ASC
		');
	}

	public static function getUserByKey($userKey)
	{
		return Db::getInstance()->getRow('
			SELECT * FROM `' . _DB_PREFIX_ . 'bestkit_livechat_user`
			WHERE `user_key` = "' . $userKey . '"
		');
	}

	public function getUserDialogHtml($userKey)
	{
		$messages = $this->getMessagesByUserKey($userKey);
		foreach ($messages as &$message) {
			$message['relative_date'] = self::relative_date($message['date_add']);
		}

		$this->context->smarty->assign('livechat_user', $this->getUserByKey($userKey));
		$this->context->smarty->assign('livechat_messages', $messages);

		//$last_admin_activity = self::getConfig('last_admin_activity');
		$this->context->smarty->assign('is_online', $this->isAdminOnline()); //(time() - $last_admin_activity < self::OFFLINE_PERIOD));
		return $this->display(__FILE__, 'messages.tpl');
	}

	public function getAdminDialogHtml($userKey)
	{
		$messages = $this->getMessagesByUserKey($userKey);
		foreach ($messages as &$message) {
			$message['relative_date'] = self::relative_date($message['date_add']);
		}

		$this->context->smarty->assign('livechat_operator', self::getConfig('operator_name'));
		$this->context->smarty->assign('livechat_user', $this->getUserByKey($userKey));
		$this->context->smarty->assign('livechat_messages', $messages);
		return $this->display(__FILE__, 'views/templates/admin/messages.tpl');
	}
}
