<?php
if (!defined('_PS_VERSION_'))
  exit;

include_once(_PS_MODULE_DIR_.'koloryznazwy/mapa_kolor.php');

class KoloryZNazwy extends Module
{
	public function __construct()
	{
		$this->name = 'koloryznazwy';
		$this->tab = 'other';
		$this->version = '1.0.0';
		$this->author = 'AC';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
		$this->bootstrap = true;
	 
		parent::__construct();
	 
		$this->displayName = $this->l('KoloryZNazwy');
		$this->description = $this->l('KoloryZNazwy');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	
		if (!Configuration::get('KOLORYZNAZWY'))
			$this->warning = $this->l('No name provided');
	}

	public function install()
	{
		$parent_tab = new Tab();
		$parent_tab->name[$this->context->language->id] = $this->l('koloryznazwy');
		$parent_tab->class_name = 'KoloryZNazwy';
		$parent_tab->id_parent = 0;
		$parent_tab->module = $this->name;
		$parent_tab->add();
		
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);
	 
		if (!parent::install() ||
			!$this->registerHook('displayRightColumnProduct') ||
			!Configuration::updateValue('SIMPLEPRINTRORDERTOPDF', 'KoloryZNazwy')
		){
			return false;
		}
		return true;
	}

	public function uninstall()
	{
		$tab = new Tab((int)Tab::getIdFromClassName('KoloryZNazwy'));
		$tab->delete();

		if (!parent::uninstall() ||
			!Configuration::deleteByName('KOLORYZNAZWY')
		){
			return false;
		}
		return true;
	}
	
	public function hookdisplayRightColumnProduct($params)
	{
		
		$id_product = (int)Tools::getValue('id_product');
		$p = new Product($id_product, false, (int)Context::getContext()->language->id);
		$accessories = $p->getAccessories((int)Context::getContext()->language->id);
	
		$retdiv = '';
		if (is_array($accessories) && count($accessories) > 0 && isset($GLOBALS['KOLORYZNAZWY']))
		{
			$retdiv = '<div style="display: flex;">';
			
			$cntr = false;
			
			foreach($accessories as $accessorie){
				
				foreach($GLOBALS['KOLORYZNAZWY'] as $kolor=>$kod_koloru){
					
					if(strpos(strtoupper($accessorie['name']), strtoupper($kolor)) !== false){
						
						$cntr = true;
						
						$product = new Product($accessorie['id_product']);
						$link = new Link();
						$url = $link->getProductLink($product);

//						$retdiv .= $accessorie['name'];
//						$retdiv .= $accessorie['id_product'];
						$retdiv .= '<div style="float:left;"><a href="'.$url.'"><div style="height: 30px; width: 30px; background-color: '.$kod_koloru.'; border: 1px solid black; margin-right: 10px;"></div></a></div>';						
						
					}
				}
			}
			$retdiv .= '</div>';
			
			if($cntr){
				
				return '<h4>Dostępne inne kolory</h4>'.$retdiv;
			} else {
				
				return '';
			}
		} else {
			
			return '';
		}
	}
}