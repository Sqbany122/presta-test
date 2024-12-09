<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// Located in /modules/mymodule/ajax.php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
include_once(dirname(__FILE__).'/santandercredit.php');

switch (Tools::getValue('method')) {
  case 'validateOrder' :
    $santanderCredit = new SantanderCredit();      
    try{
        $oid = $santanderCredit->execValidation();   
    } catch (Exception $ex) {
        $oid = "VALIDATION ERROR: " . $ex->getMessage();
    }    
    die(Tools::jsonEncode( array('result'=>$oid)));
    break;
  default:
    exit;
}
exit;

