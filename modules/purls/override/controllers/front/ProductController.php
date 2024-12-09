<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2017 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class ProductController extends ProductControllerCore
{
    public function init()
    {
        if (Configuration::get('purls_products') == 1)
        {
            $link_pattern = Tools::safeOutput(urldecode(Tools::getValue('product_rewrite')));
            if (Tools::getValue('id_product', 'false') != 'false')
            {
                $_GET['product_rewrite'] = '';
                return parent::init();
            }

            if (Tools::getValue('action', 'false') != 'false')
            {
                if (Tools::getValue('action') == 'quickview')
                {
                    return parent::init();
                }
            }

            if ($link_pattern)
            {
                $sql = "SELECT id_product FROM " . _DB_PREFIX_ . "product_lang WHERE link_rewrite='" . $link_pattern . "' AND id_lang=" . Context::getContext()->language->id . " AND id_shop=" . $this->context->shop->id;
                $id_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                if ($id_product != "")
                {
                    //$_POST['id_product'] = $id_product;
                    $_GET['id_product'] = $id_product;
                    $_GET['product_rewrite'] = '';
                }
                else
                {
                    header('HTTP/1.1 404 Not Found');
                    header('Status: 404 Not Found');
                }
            }
            else
            {
                header('HTTP/1.1 404 Not Found');
                header('Status: 404 Not Found');
            }
            parent::init();
        }
        else
        {
            parent::init();
        }
    }
}