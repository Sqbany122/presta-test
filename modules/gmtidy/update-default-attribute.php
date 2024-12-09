<?php

require_once('../../config/config.inc.php');
//a script fixing cache_default_attribute

$sql = 'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` WHERE `cache_default_attribute` = 0 OR `cache_default_attribute` IS NULL';
$res = Db::getInstance()->executeS($sql);
if ($res) {
    foreach ($res as $row) {
        $productId = (int) $row['id_product'];
        $def = Product::getDefaultAttribute($productId);
        echo $productId . ' - ' . $def . '<br/>';
        if ($def > 0) {
            $result = Db::getInstance()->update('product_shop', array(
                'cache_default_attribute' => $def,
                    ), 'id_product = ' . (int) $productId);

            $result &= Db::getInstance()->update('product', array(
                'cache_default_attribute' => $def,
                    ), 'id_product = ' . (int) $productId);
        }
    }
}