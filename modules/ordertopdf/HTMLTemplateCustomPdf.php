<?php

class HTMLTemplateCustomPdf extends HTMLTemplate
{
	public $custom_model;
 
	public function __construct($custom_object, $smarty)
	{
		$this->custom_model = $custom_object;
		$this->smarty = $smarty;
 
		// header informations
		$id_lang = Context::getContext()->language->id;
		$this->title = HTMLTemplateCustomPdf::l('');
		// footer informations
		$this->shop = new Shop(Context::getContext()->shop->id);
	}
 

	public function getContent()
	{

		$this->smarty->assign(array(
			'custom_model' => $this->custom_model,
		));
 
		return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ordertopdf/order_pdf.tpl');
	}
/*
	public function getLogo()
	{

	}
*/
	public function getHeader()
	{
		
        $logo = '';

        $id_shop = (int)$this->shop->id;

        if (Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop))) {
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop);
        } elseif (Configuration::get('PS_LOGO', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop))) {
            $logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop);
        }
		
		$this->smarty->assign(array(
			'logo_path' => $logo,
		));
 
		return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ordertopdf/logo_pdf.tpl');
	}
 

	public function getFooter()
	{
		return '';
	}
 

	public function getFilename()
	{
		$id_order = Tools::getValue('id_order');
		return 'Zwrot_'.$id_order.'.pdf';
	}
 

	public function getBulkFilename()
	{
		$id_order = Tools::getValue('id_order');
		return 'Zwrot_'.$id_order.'.pdf';
	}

}