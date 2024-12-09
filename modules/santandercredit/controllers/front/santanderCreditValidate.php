<?php
include_once(dirname(__FILE__).'/../../santandercredit.php');

class santandercreditsantanderCreditValidateModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
  
    parent::initContent();
	
    $santanderCredit = new SantanderCredit();
    $santanderCredit->execValidation();
    
    $this->setTemplate('santanderCreditValidate.tpl');
	
  }
}

?>