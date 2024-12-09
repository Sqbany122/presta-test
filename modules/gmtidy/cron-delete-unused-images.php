<?php
require_once('../../config/config.inc.php');
$token = Tools::getValue('token');
$comparedToken = Tools::getAdminToken('gmtidy');
if ($token != $comparedToken) {
    die('invalid token');
}
$tidy = Module::getInstanceByName('gmtidy');
$preview = Tools::isSubmit('preview');
$sub = null;
if (Tools::isSubmit('sub')) {
    $sub = Tools::getValue('sub');
}
if ($tidy->deleteUnusedImages(true, $sub, $preview)) {
    echo 'OK';
} else {
    echo 'Error';
}