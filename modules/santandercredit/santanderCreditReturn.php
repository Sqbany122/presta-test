<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/santandercredit.php');

$santanderCredit = new SantanderCredit();
$returnTemplate = 'santanderCreditReturn.tpl';
$errors = '';

if ( array_key_exists('orderId', $_GET) && array_key_exists('id_wniosku', $_GET) && array_key_exists('status', $_GET)){

        $order = new Order(Tools::getValue('orderId'));
	if ( $order ) {
            
            $orderPaymentCollection = $order->getOrderPaymentCollection();
            $payment = $orderPaymentCollection->getFirst();
            if($payment) {
                $payment->transaction_id = Tools::getValue('id_wniosku');
                $payment->save();
                $smarty->assign(array (
						'status' => $_GET['status'],
						'wniosekId'=> preg_replace('#[^0-9/ZAG]#', '', $_GET['id_wniosku']),
                        'orderId'=> $_GET['orderId']
                    )
                );
            } else {
                $errors .= "<li>Błąd w trakcie aktualizacji numeru transakcji (transactionId, wniosekId).</li>";
                $smarty->assign(array(
						'errors' => $errors,
						'status' => $_GET['status'],
						'wniosekId'=> preg_replace('#[^0-9/ZAG]#', '', $_GET['id_wniosku']),
                        'orderId'=> $_GET['orderId'])
					);
                $returnTemplate = 'paymentErrors.tpl';
            }
	} else {
            $errors .= "<li>Błędny numer zamówienia w sklepie (orderId).</li>";  
            $smarty->assign(array(
						'errors' => $errors, 
						'status' => $_GET['status'],
						'wniosekId'=> preg_replace('#[^0-9/ZAG]#', '', $_GET['id_wniosku']),
						'orderId'=> $_GET['orderId']
					)
				);
            $returnTemplate = 'paymentErrors.tpl';
	}
} else {		
        $errors .= "<li>Nieokreślony numer wniosku lub numer zamówienia w odpowiedzi Banku (orderId, id_wniosku).</li>";  	
        $smarty->assign(array(
					'errors' => $errors, 
					'status' => array_key_exists('status', $_GET) ? $_GET['status'] : '',
					'wniosekId'=> array_key_exists('id_wniosku', $_GET) ? preg_replace('#[^0-9/ZAG]#', '', $_GET['id_wniosku']) : '',
					'orderId'=> array_key_exists('orderId', $_GET) ? $_GET['orderId'] : ''
				)
			);
        
		$returnTemplate = 'paymentErrors.tpl';    
}
$smarty->display($returnTemplate);
include_once('../../footer.php');
?>