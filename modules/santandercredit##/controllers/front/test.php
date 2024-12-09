<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * http://localhost/presta/prestashop_1.7.1.0/module/santandercredit/test
 */
class SantandercreditTestModuleFrontController extends ModuleFrontController {

    public function __construct(){
        parent::__construct();
    }
    
    public function initContent(){
        parent::initContent();
        $mess = Tools::getValue('orderId');
        $this->context->smarty->assign('test', 'to tylko test '. $mess);
        $this->setTemplate('module:santandercredit/views/templates/front/test.tpl'); 
    }
    
    public function postProcess()
    {       
//        global $smarty;
//        $templatePath = dirname(__FILE__).'/../../views/templates/front/test.tpl';
//        $smarty->assign(array(
//            'test' => 'to tylko test'
//        ));
//        $smarty->display($templatePath);
    }
}
