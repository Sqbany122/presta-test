<?php
require_once('../../config/config.inc.php');
$token = Tools::getValue('token');
$comparedToken = Tools::getAdminToken('gmtidy');
if ($token != $comparedToken) {
    die('invalid token');
}
$tidy = Module::getInstanceByName('gmtidy');
$preview = Tools::isSubmit('preview');
if ($tidy->assignMostCommonTaxGroupToProductsWithNoTaxGroup(true, $preview)) {
    echo 'OK';
} else {
    echo 'Error';
}