<?php
include_once(dirname(__FILE__).'/../../santandercredit.php');

class santandercreditsantanderCreditPaymentModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
  
	parent::initContent();
	
	$santanderCredit = new SantanderCredit();
	$santanderCredit->execPayment();
    
    $this->setTemplate('santanderCreditPayment.tpl');
	
  }
}

?>