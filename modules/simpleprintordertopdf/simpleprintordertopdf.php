<?php
if (!defined('_PS_VERSION_'))
  exit;

class SimplePrintOrderToPdf extends Module
{
	public function __construct()
	{
		$this->name = 'simpleprintordertopdf';
		$this->tab = 'other';
		$this->version = '1.0.0';
		$this->author = 'ARIMONIT.PL';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
		$this->bootstrap = true;
	 
		parent::__construct();
	 
		$this->displayName = $this->l('Simple printing of orders to pdf');
		$this->description = $this->l('Simple printing of orders to pdf');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	
		if (!Configuration::get('SIMPLEPRINTRORDERTOPDF'))
			$this->warning = $this->l('No name provided');
	}

	public function install()
	{
		$parent_tab = new Tab();
		$parent_tab->name[$this->context->language->id] = $this->l('SimplePrintOrderToPdf');
		$parent_tab->class_name = 'SimplePrintOrderToPdf';
		$parent_tab->id_parent = 0;
		$parent_tab->module = $this->name;
		$parent_tab->add();
		
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);
	 
		if (!parent::install() ||
			!$this->registerHook('displayAdminOrder') ||
			!Configuration::updateValue('SIMPLEPRINTRORDERTOPDF', 'Simple printing of orders to pdf')
		){
			return false;
		}
		return true;
	}

	public function uninstall()
	{
		$tab = new Tab((int)Tab::getIdFromClassName('SimplePrintOrderToPdf'));
		$tab->delete();

		if (!parent::uninstall() ||
			!Configuration::deleteByName('SIMPLEPRINTRORDERTOPDF')
		){
			return false;
		}
		return true;
	}

	public function hookDisplayAdminOrder($params)
	{
		if (!$this->active){
			return ;
		} else {
			$this->smarty->assign(array('id_order' => $params['objOrder']->id));
		}
		
		return $this->display(__FILE__, 'views/templates/admin/printbutton.tpl');
	}
}