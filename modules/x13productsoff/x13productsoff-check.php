<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/x13productsoff.php');

if (substr(Tools::encrypt('x13productsoff/index'), 0, 10) != Tools::getValue('token') || !Module::isEnabled('x13productsoff')) {
	die('Bad token');
}

$x13productsoff = new x13productsoff();
echo $x13productsoff->checkProductQuantity();