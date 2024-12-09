<?php
	
	require_once(dirname(__FILE__).'../../../config/config.inc.php');
	require_once(dirname(__FILE__).'../../../init.php');
	$id_cart = (int)(Tools::getValue('id_cart'));
	$value = (int)(Tools::getValue('value'));
	$db = Db::getInstance();
	$ch = $db->ExecuteS('SELECT choice FROM `'._DB_PREFIX_.'pf` WHERE id_cart = '.$id_cart);
	if(count($ch) == 0)
	{
		$db->insert('pf', array(
			'id_cart' => $id_cart,
			'choice' => $value
		));
	} else {
		$db->query('UPDATE `'._DB_PREFIX_.'pf` SET choice = '.$value.' WHERE id_cart ='.$id_cart);
	}
?>