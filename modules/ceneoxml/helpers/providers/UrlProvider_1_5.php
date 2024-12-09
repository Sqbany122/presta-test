<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'UrlProviderBase.php'); 

class UrlProvider_1_5 extends UrlProviderBase
{
    public function getProductLink($product_id, $category_id, $prod_attr_id)
    {
        return $this->context->link->getProductLink((int)$product_id, null, $category_id, null, null, null, $prod_attr_id);
    }
}
