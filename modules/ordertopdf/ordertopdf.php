<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class OrderToPdf extends Module
{
	public function __construct()
	{
		$this->name = 'ordertopdf';
		$this->tab = 'other';
		$this->version = '1.0.0';
		$this->author = 'AC';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
		$this->bootstrap = true;
	 
		parent::__construct();
	 
		$this->displayName = $this->l('OrderToPdf');
		$this->description = $this->l('Description OrderToPdf.');
	 
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	 
		if (!Configuration::get('ORDERTOPDF_NAME'))      
			$this->warning = $this->l('No name provided');
	}
	public function install()
{
    // Install Tabs
    $parent_tab = new Tab();
    // Need a foreach for the language
    $parent_tab->name[$this->context->language->id] = $this->l('OrderToPdf');
    $parent_tab->class_name = 'OrderToPdf';
    $parent_tab->id_parent = 0; // Home tab
    $parent_tab->module = $this->name;
    $parent_tab->add();
	
  if (Shop::isFeatureActive())
    Shop::setContext(Shop::CONTEXT_ALL);
 
  if (!parent::install() ||
    !$this->registerHook('displayAdminOrder') ||
    !Configuration::updateValue('ORDERTOPDF_NAME', 'my friend')
  )
    return false;
 
  return true;
}
public function uninstall()
{
    // Uninstall Tabs
    $tab = new Tab((int)Tab::getIdFromClassName('OrderToPdf'));
    $tab->delete();

  if (!parent::uninstall() ||
    !Configuration::deleteByName('ORDERTOPDF_NAME')
  )
    return false;
 
  return true;
}



	public function hookDisplayAdminOrder($params)
	{
		if (!$this->active){
				return ;
		} else {
			$this->smarty->assign(array('id_order' => $params['objOrder']->id));
		}
		
	//	$this->context->smarty->assign('mess', $arr);
		return $this->display(__FILE__, 'ordertopdf.tpl');
	}


}