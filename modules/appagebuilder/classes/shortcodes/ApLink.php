<?php
/**
* 2007-2015 Apollotheme
*
* NOTICE OF LICENSE
*
* ApPageBuilder is module help you can build content for your shop
*
* DISCLAIMER
*
*  @Module Name: AP Page Builder
*  @author    Apollotheme <apollotheme@gmail.com>
*  @copyright 2007-2015 Apollotheme
*  @license   http://apollotheme.com - prestashop template provider
*/

class ApLink extends ApShortCodeBase
{
	public $name = 'ApLink';
	public $for_module = 'manage';
	
	public function getInfo()
	{
		return array('label' => $this->l('Link'), 'position'=> 6,
			'desc' => $this->l('Put any link at anywhere'),
			'icon_class' => 'icon icon-link',
			'tag' => 'content slider');
	}
	
	public function getConfigList()
	{
		$types = array(
			array(
				'value' => 'link-cms',
				'text' => $this->l('CMS Link')
			),
			array(
				'value' => 'link-category',
				'text' => $this->l('Category Link')
			),
			array(
				'value' => 'link-product',
				'text' => $this->l('Product Link')
			),
			
		);
		
		$target = array(
			array(
				'value' => '_blank',
				'text' => $this->l('Blank')
			),
			array(
				'value' => '_self',
				'text' => $this->l('Self')
			),
			array(
				'value' => '_parent',
				'text' => $this->l('Parent')
			),
			array(
				'value' => '_top',
				'text' => $this->l('Top')
			),
		);
		
		$inputs = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'label' => $this->l('Text display of link'),
				'desc'  => $this->l('Auto hide if leave it blank'),
				'lang'  => 'true',
				'form_group_class' => 'aprow_general',
				'default' => ''
			),
			array(
				'type' 	  => 'select',
				'label'   => $this->l('Target Type'),
				'name' 	  => 'target_type',				
				'options' => array(  'query' => $target,
					'id' 	  => 'value',
					'name' 	  => 'text' ),
				'default' => '1',
				'desc'	=> $this->l('Select a target type')
			),
			
			array(
				'type' 	  => 'select',
				'label'   => $this->l('Link Type'),
				'name' 	  => 'link_type',
				'class' => 'form-action',
				'options' => array(  'query' => $types,
					'id' 	  => 'value',
					'name' 	  => 'text' ),
				'default' => '1',
				'desc'	=> $this->l('Select a link type')
			),
			
			array(
				'type' => 'text',
				'label' => $this->l('ID CMS'),
				'name' => 'cmspage_id',
				'default'=> '',
				'form_group_class' => 'link_type_sub link_type-link-cms',
				'desc'	=> $this->l('Enter a cms id'),
			),
			array(
				'type' => 'text',
				'label' => $this->l('ID Category'),
				'name' => 'category_id',
				'default'=> '',
				'form_group_class' => 'link_type_sub link_type-link-category',
				'desc'	=> $this->l('Enter a category id'),
			),
			
			array(
				'type' => 'text',
				'label' => $this->l('ID Product'),
				'name' => 'product_id',
				'default'=> '',
				'form_group_class' => 'link_type_sub link_type-link-product',
				'desc'	=> $this->l('Enter a product id'),
			),
			array(
					'type' => 'switch',
					'label' => $this->l('Display'),
					'name' => 'is_diplay',
					'is_bool' => true,
					'default'=> 1,
					'values' => ApPageSetting::returnYesNo(),
			),
			
		);
		return $inputs;
	}
	public function prepareFontContent($assign, $module = null)
	{
		
		unset($module);
		
		$form_atts = $assign['formAtts'];
	
		if($form_atts['is_diplay'] == 1)
		{
			if(($form_atts['cmspage_id'] != '' && (int)$form_atts['cmspage_id']>=0) || ($form_atts['category_id'] != '' && (int)$form_atts['category_id']>=0) || ($form_atts['product_id'] != '' && (int)$form_atts['product_id']>=0))
			{
				
				switch ($form_atts['link_type'])
				{
					case 'link-cms':
						$cms = new CMS((int)$form_atts['cmspage_id'], Context::getContext()->language->id, Context::getContext()->shop->id);
						$assign['link'] = Context::getContext()->link->getCMSLink($cms, $cms->link_rewrite, (bool)Configuration::get('PS_SSL_ENABLED'));
						$assign['link_title'] = $cms->meta_title;
						break;
					case 'link-category':
						$category = new Category((int)$form_atts['category_id'], Context::getContext()->language->id, Context::getContext()->shop->id);
						$assign['link'] = Context::getContext()->link->getCategoryLink($category);
						$assign['link_title'] = $category->name;
						break;
					case 'link-product':
						$product = new Product((int)$form_atts['product_id'], true, Context::getContext()->language->id, Context::getContext()->shop->id);
						$assign['link'] = Context::getContext()->link->getProductLink($product);
						$assign['link_title'] = $product->name;	
						break;
					
				}
			}
		}
		
		return $assign;
		
	}
}