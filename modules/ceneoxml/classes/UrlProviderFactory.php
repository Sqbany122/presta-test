<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'PrestaShopHelper.php'); 

class UrlProviderFactory
{
    public static function Generate($product_id, $category_id, $prod_attr_id, $link)
    {
        switch(PrestaShopHelper::GetPSVersionBasePart())
        {
            case '1.5':
                return $link->getProductLink($product_id, null, $category_id, null, null, null, $prod_attr_id);
                break;

            case '1.6':
                return $link->getProductLink($product_id, null, $category_id, null, null, null, $prod_attr_id, false, false, true);
                break;

            default:	
                return NULL;
        }
    }
}
