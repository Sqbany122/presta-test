<?php
$error = false;

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/x13productswithoutimages.php');

if (!Module::isEnabled('x13productswithoutimages')) {
    $error = 'Module is not active';
}

if (Tools::getValue('token') != substr(Tools::encrypt('x13productswithoutimages/cron/shop' . Tools::getValue('id_shop')), 0, 10)) {
    $error = 'Token error';
}

if ($error) {
    echo $error;
    exit;
}

XProductsWI::updateProducts((int)Tools::getValue('id_shop'));
echo 'OK';
