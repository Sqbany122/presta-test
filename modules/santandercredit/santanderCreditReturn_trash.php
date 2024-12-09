<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/santandercredit.php');

$santanderCredit = new SantanderCredit();

if ( Tools::getValue('orderId') != 0 && Tools::getValue('id_wniosku') != '') {

	$cart = new Cart(Tools::getValue('orderId'));

	$errors = '';

	if ( !$cart->id ) {
	  $errors .= '<p>Błąd koszyka (ID).</p>';
	}


	if ( $errors == '' ) {
            $order = new Order(Tools::getValue('orderId'));
            $orderPaymentCollection = $order->getOrderPaymentCollection();
            $payment = $orderPaymentCollection->getFirst();
            if($payment) {
                $payment->transaction_id = Tools::getValue('id_wniosku');
                $payment->save();
                $smarty->assign(
                    array ('wniosekId'=> preg_replace('#[^0-9/ZAG]#', '', $_GET['id_wniosku']),
                        'orderId'=> (int)$_GET['orderId']
                    )
                );
                $smarty->display($module_dir.'santanderCreditReturn.tpl');
            }
	} else {
		$smarty->assign(array('HOOK_PAYMENT_RETURN' => $errors));
	}
}


?>