<?php

/*
	1 	Oczekiwanie płatności czekiem 					 	
	2 -	 Płatność zaakceptowana 					 	
	3 -	 Przygotowanie w toku 					 	
	4 -	 Wysłane 					 	
	5 	Dostarczone 						
	6 	Anulowane 					 	
	7 	Zwrot 					 	
	8 	Błąd płatonści 					 	
	9 	Brak towaru 					 	
	10 - Oczekiwanie na płatność przelewem bankowym 					 	
	11 	Oczekiwanie na płatność Paypal 						
	12 - Płatność przyjęta 					 	
	24 	Płatność PayU rozpoczęta 						
	25 	Płatność PayU oczekuje na odbiór 						
	26 	Płatność PayU anulowana 						
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../config/config.inc.php');
require_once '../classes/order/Order.php';
require_once '../classes/order/OrderHistory.php';

//
// CONFIG
//
$objOrder = new Order();

$id_order = 135639;
$id_status = 4;

//
// GET LIST ORDERS
//
	/*$sql = 'SELECT id_order
			FROM '._DB_PREFIX_.'orders o
			WHERE o.`current_state` = '.(int)$id_status.'
			'.Shop::addSqlRestriction(false, 'o').'
			ORDER BY invoice_date ASC';*/
	$sql = 'SELECT DISTINCT email FROM `ps_customer` WHERE date_add >= DATE_SUB(NOW(),INTERVAL 144  HOUR);';
	$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	$orders = array();
	foreach ($result as $customer) {
		echo $customer['email'].'<br>';
	}

	/*foreach ($id_customers as $key => $value) { 
		$sql = "SELECT * FROM `ps_customer` WHERE `id_customer` = $value;";
		$email = Db::getInstance()->getRow($sql);
		//echo $sql.'<br>';
		$email = $email['email'];
		echo $email .' mail($email, $subject, $message); <br>';
	}*/
	
/*
//
// EDIT ORDERS
//
$objOrder = new Order($id_order);

$history = new OrderHistory();
$history->id_order = (int)$objOrder->id;
$history->changeIdOrderState($id_status, (int)($id_order));
//
//$history->save();
*/

//
// DEBUG
//
//echo count($orders);
//print_r($orders);
//print_r($_GET);


?>
