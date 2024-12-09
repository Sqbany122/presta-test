<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
 
class StOutOfStockToNowhere extends Module
{
    private $_html = '';
    public $fields_form;
    public $fields_value;
    private $_prefix_st = 'ST_NOWHERE_';
    public $validation_errors = array();
    public $tabs;
    private $_st_is_16;
	function __construct()
    {
		$this->name           = 'stoutofstocktonowhere';
		$this->tab            = 'front_office_features';
		$this->version        = '1.0.5';
		$this->author         = 'SUNNYTOO.COM';
		$this->need_instance  = 0;
		$this->bootstrap 	  = true;
		parent::__construct();

		$this->displayName = $this->l('Hiding out of stock products by ST-themes');
		$this->description = $this->l('Set the visibility of out of stock products to nowhere to force them not showing on the front office.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->tabs = array(
            array('id'  => '0', 'name' => $this->l('General')),
            array('id'  => '1', 'name' => $this->l('About ST-themes')),
        );
        $this->_st_is_16      = Tools::version_compare(_PS_VERSION_, '1.7');
	}
     
	function install()
	{
		if (!parent::install() 
            || !$this->registerHook('displayOrderConfirmation')
            || !$this->registerHook('actionProductSave')
        )
			return false;
		return true;
	}

    public function getContent()
    {
        $this->context->controller->addCSS(($this->_path).'views/css/admin.css');
        $this->context->controller->addJS(($this->_path).'views/js/admin.js');
        if(Tools::getValue('act')=='to_nowhere')
        {
            $res = $this->setVisibilityToNone($this->getOutOfStockIds());
            if($res)
                $this->_html .= $this->displayConfirmation($this->l('Success.'));
        }
        if(Tools::getValue('act')=='to_everywhere')
        {
            $res = $this->setVisibilityToVisible($this->getNowhereInstockIds());
            if($res)
                $this->_html .= $this->displayConfirmation($this->l('Success.'));
        }

        $this->initFieldsForm();
        if (isset($_POST['savestoutofstocktonowhere']))
        {
            foreach($this->fields_form as $form)
                foreach($form['form']['input'] as $field)
                    if(isset($field['validation']))
                    {
                        $ishtml = ($field['validation']=='isAnything') ? true : false;
                        $errors = array();       
                        $value = Tools::getValue($field['name']);
                        if (isset($field['required']) && $field['required'] && $value==false && (string)$value != '0')
                                $errors[] = sprintf($this->l('Field "%s" is required.'), $field['label']);
                        elseif($value)
                        {
                            $field_validation = $field['validation'];
                            if (!Validate::$field_validation($value))
                                $errors[] = sprintf($this->l('Field "%s" is invalid.'), $field['label']);
                        }
                        // Set default value
                        if ($value === false && isset($field['default_value']))
                            $value = $field['default_value'];
                        
                        if(count($errors))
                        {
                            $this->validation_errors = array_merge($this->validation_errors, $errors);
                        }
                        elseif($value==false)
                        {
                            switch($field['validation'])
                            {
                                case 'isUnsignedId':
                                case 'isUnsignedInt':
                                case 'isInt':
                                case 'isBool':
                                    $value = 0;
                                break;
                                default:
                                    $value = '';
                                break;
                            }
                            Configuration::updateValue($this->_prefix_st.strtoupper($field['name']), $value);
                        }
                        else
                            Configuration::updateValue($this->_prefix_st.strtoupper($field['name']), $value, $ishtml);
                    }

            if(count($this->validation_errors))
                $this->_html .= $this->displayError(implode('<br/>',$this->validation_errors));
            else 
                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));

            $this->_clearCache('*');
        }

        $helper = $this->initForm();
        Media::addJsDef(array(
            'id_tab_index' => Tools::getValue('id_tab_index', 0),
        ));
        return $this->_html.'<div class="tabbable row sttab">'.$this->initTab().'<div class="col-xs-12 col-lg-10 tab-content">'.$helper->generateForm($this->fields_form).'</div></div>';
    }

    public function initTab()
    {
        $html = '<div class="st_sidebar col-xs-12 col-lg-2"><ul class="nav nav-tabs">';
        foreach($this->tabs AS $tab)
            $html .= '<li class="nav-item"><a href="javascript:;" title="'.$tab['name'].'" data-fieldset="'.$tab['id'].'">'.$tab['name'].'</a></li>';
        $html .= '</ul></div>';
        return $html;
    }
    protected function initFieldsForm()
    {
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('General:'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_tab_index',
                    'default_value' => 0,
                ),
                'out_of_stock_info' => array(
                    'type' => 'html',
                    'id' => '',
                    'label' => $this->l('Stock information'),
                    'name' => '',
                ),
                'back_to_everywhere' => array(
                    'type' => 'html',
                    'id' => '',
                    'label' => $this->l('Set back to everywhere for products which are back in stock'),
                    'name' => '',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );
        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('About ST-THEMES:'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'html',
                    'id' => '',
                    'label' => '',
                    'name' => 'This free module was created by <a href="https://www.sunnytoo.com" target="_blank">ST-THEMES</a>, it\'s not allow to sell it, it\'s also not allow to create new modules based on it. <br/>Check more <a href="https://www.sunnytoo.com/blogs?term=743&orderby=date&order=desc" target="_blank">free modules</a>, <a href="https://www.sunnytoo.com/product-category/prestashop-modules" target="_blank">advanced paid modules</a> and <a href="https://www.sunnytoo.com/product-category/prestashop-themes" target="_blank">themes(transformer theme and panda  theme)</a> created by <a href="https://www.sunnytoo.com" target="_blank">ST-THEMES</a>.',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );

        $ids = $this->getOutOfStockIds();
        if(is_array($ids) && count($ids)){
            $this->fields_form[0]['form']['input']['out_of_stock_info']['name'] .= sprintf($this->l('There are %s products out of stock.'), count($ids));
            $visible_number = Db::getInstance()->getValue('
                SELECT count(*)
                FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                WHERE p.`id_product` IN ('.implode($ids, ',').')
                AND product_shop.`visibility` IN ("both", "catalog")');
            if($visible_number){
                $this->fields_form[0]['form']['input']['out_of_stock_info']['name'] .= '<br/>'.sprintf($this->l('%s of them are still visible on the front office.'), $visible_number);
                $this->fields_form[0]['form']['input']['out_of_stock_info']['name'] .= '<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&act=to_nowhere&token='.Tools::getAdminTokenLite('AdminModules').'">'.$this->l('Click here to set visible ones to be invisible.').'</a>';
            }
            else
                $this->fields_form[0]['form']['input']['out_of_stock_info']['name'] .= $this->l('All of them have been set to be invisible on the front office.');
        }
        else
            $this->fields_form[0]['form']['input']['out_of_stock_info']['name'] .= $this->l('All products are in stock.');

        $nowhere_instock_ids = $this->getNowhereInstockIds();
        if(is_array($nowhere_instock_ids) && count($nowhere_instock_ids)){
            $this->fields_form[0]['form']['input']['back_to_everywhere']['name'] .= sprintf($this->l('There are %s products are back in stock, but they are still in Nowhere.'), count($nowhere_instock_ids));
            $this->fields_form[0]['form']['input']['back_to_everywhere']['name'] .= '<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&act=to_everywhere&token='.Tools::getAdminTokenLite('AdminModules').'">'.$this->l('Click here to set the "Visibility" setting of them to Everywhere.').'</a>';
        }
        else
            $this->fields_form[0]['form']['input']['back_to_everywhere']['name'] .= $this->l('There is no product which is back to stock but still in Nowhere');
    }
    protected function initForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $helper->module = $this;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'savestoutofstocktonowhere';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper;
    }
    
    private function getConfigFieldsValues()
    {
        $fields_values = array(
        );
        $fields_values['id_tab_index'] = Tools::getValue('id_tab_index', 0);
        return $fields_values;
    }


    public function hookDisplayOrderConfirmation($params){
        if(Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') || !Configuration::get('PS_STOCK_MANAGEMENT'))
            return;
        $order = $this->_st_is_16 ? $params['objOrder'] : $params['order'];
        if (!Validate::isLoadedObject($order) || $order->getCurrentState() == (int)Configuration::get('PS_OS_ERROR'))
            return ;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT p.`id_product`
        FROM `'._DB_PREFIX_.'order_detail` od
        LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = od.product_id)
        LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
        WHERE od.`id_order` = '.$order->id);
        if(!$products)
            return;
        $product_ids = array();
        foreach ($products as $product) {
            $product_ids[] = $product['id_product'];
        }
        if(!count($product_ids))
            return;
        $out_of_stock_ids = $this->getOutOfStockIds($product_ids);
        if(!count($out_of_stock_ids))
            return;
        $this->setVisibilityToNone($out_of_stock_ids);
        //clear cache may slow site down a lot
        return;
    }
    public function hookActionProductSave($params){
        if(Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') || !Configuration::get('PS_STOCK_MANAGEMENT'))
            return;
        if(!isset($params['id_product']))
            return;
        if(!isset($params['product']->visibility) || $params['product']->visibility!='none')
            return;
        $id_product = $params['id_product'];
        $out_or_not = $this->getOutOfStockIds([$id_product]);
        if(!count($out_or_not)){
            $this->setVisibilityToVisible([$id_product]);
        }
        return true;
    }

    public function getOutOfStockIds($ids=array()){
        $query = new DbQuery();
        $query->select('`id_product`');
        $query->from('stock_available');
        if(is_array($ids) && count($ids))
            $query->where('id_product IN ('.implode($ids, ',').')');
        $query->where('id_product_attribute = 0');
        $query->where('quantity <= 0');
        $query->where('(out_of_stock = 0 '.(Configuration::get('PS_ORDER_OUT_OF_STOCK') ? '' : ' || out_of_stock=2').' )');
        $query = StockAvailable::addSqlShopRestriction($query, Context::getContext()->shop->id);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $out_of_stock_ids = array();
        if($result)
            foreach ($result as $product) {
                $out_of_stock_ids[] = $product['id_product'];
            }
        return $out_of_stock_ids;
    }
    public function getNowhereInstockIds($ids=array()){
        $query = new DbQuery();
        $query->select('sa.`id_product`');
        $query->from('stock_available', 'sa');
        $query->leftJoin('product_shop', 'ps', 'ps.`id_product` = sa.`id_product` and ps.`id_shop` = '.Context::getContext()->shop->id);
        if(is_array($ids) && count($ids))
            $query->where('sa.`id_product` IN ('.implode($ids, ',').')');
        $query->where('ps.`visibility` IN ("none")');
        $query->where('sa.`id_product_attribute` = 0');
        $query->where('sa.`quantity` > 0');
        $query->where('(sa.`out_of_stock` = 0 '.(Configuration::get('PS_ORDER_OUT_OF_STOCK') ? '' : ' || sa.`out_of_stock`=2').' )');
        $query = StockAvailable::addSqlShopRestriction($query, Context::getContext()->shop->id, 'sa');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $nowhere_instock_ids = array();
        if($result)
            foreach ($result as $product) {
                $nowhere_instock_ids[] = $product['id_product'];
            }
        return $nowhere_instock_ids;
    }
    public function setVisibilityToNone($ids){
        if(!is_array($ids) || !count($ids))
            return false;
        return Db::getInstance()->execute(
                    'UPDATE `'._DB_PREFIX_.'product` p'.Shop::addSqlAssociation('product', 'p').'
                    SET p.`visibility` = "none", product_shop.`visibility` = "none"
                    WHERE p.`id_product` IN ('.implode($ids, ',').')'
                );
    }
    public function setVisibilityToVisible($ids){
        if(!is_array($ids) || !count($ids))
            return false;
        return Db::getInstance()->execute(
                    'UPDATE `'._DB_PREFIX_.'product` p'.Shop::addSqlAssociation('product', 'p').'
                    SET p.`visibility` = "both", product_shop.`visibility` = "both"
                    WHERE p.`id_product` IN ('.implode($ids, ',').')'
                );
    }
}