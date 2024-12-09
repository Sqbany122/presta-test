<?php

require_once(_PS_MODULE_DIR_.'/ordertopdf/HTMLTemplateCustomPdf.php');

class OrderToPdfController extends ModuleAdminController 
{

	public function __construct()
	{
		if(Tools::getValue('id_order')){
			
			global $smarty;
			
			$id_order = Tools::getValue('id_order');
			
			$custom_object = array();
			
			$custom_object['id_order'] = $id_order;
			
			$order = new Order ($id_order);
			
			$customer = new Customer($order->id_customer);
			
			$address = new Address($order->id_address_delivery);
			
//			$state = new State ($address->id_state);
			
//			$country = new Country ($address->id_country);
	
			$order_details = $order->getOrderDetailList();
	
//echo '<pre>';

//print_r($order_details);
//exit;
//echo '------------------------------------------';
//			print_r($customer);
//exit;			
//print_r($custom_object);
			$pdf_order_obj = array();
			
			$pdf_order_obj['order'] = $order;
			
			
//			$pdf_order_obj['address'] = $address;
			
//			$pdf_order_obj['state'] = $state;
			
//			$pdf_order_obj['country'] = $country;
			
//			$pdf_order_obj['order_details'] = $order_details;
			
			$smarty->assign('reference', $order->reference);

			$smarty->assign('id_order', $id_order);

			$smarty->assign('address', $address);

			$smarty->assign('customer', $customer);
			
			$smarty->assign('order_details', $order_details);
			
			$pdf = new PDF(array($order_full => $pdf_order_obj), 'CustomPdf', Context::getContext()->smarty);

			$pdf->render();
		}
	}
}