<?php

class CustomPdf extends HTMLTemplate
{
	public $custom_model;

	public function __construct($custom_object, $smarty)
	{
		$this->custom_model = $custom_object;
		$this->smarty = $smarty;
		$id_lang = Context::getContext()->language->id;
		$this->title = CustomPdf::l('Order');
		$this->shop = new Shop(Context::getContext()->shop->id);
	}

	public function getContent()
	{
		$this->smarty->assign(array(
			'custom_model' => $this->custom_model,
		));
		return $this->smarty->fetch(_PS_MODULE_DIR_ . 'views/templates/pdf/pdf.tpl');
	}

	public function getHeader()
	{
		return '';
	}

	public function getFooter()
	{
		return '';
	}

	public function getFilename()
	{
		$id_order = Tools::getValue('id_order');
		return 'Order_'.$id_order.'.pdf';
	}

	public function getBulkFilename()
	{
		$id_order = Tools::getValue('id_order');
		return 'Order_'.$id_order.'.pdf';
	}
}