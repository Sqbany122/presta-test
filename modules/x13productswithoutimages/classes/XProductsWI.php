<?php
require_once (dirname(__FILE__) . '/../x13productswithoutimages.php');

class XProductsWI
{
    const PWI_DISABLE = 1;
    const PWI_HIDE = 2;
    const PWI_HIDE_LEFT_SEARCH = 3;
    const PWI_HIDE_LEFT_CATALOG = 4;

    public static function updateProducts($id_shop, $get_names = false)
    {
        $PRODUCT_OFF_TYPE = (int)Configuration::get('X13_PWI_TYPE');

        switch ($PRODUCT_OFF_TYPE) {
            case XProductsWI::PWI_DISABLE:
                $where = '(p.`active` = 1 OR ps.`active` = 1)';
                $set = 'p.`active` = 0, ps.`active` = 0';
                break;

            case XProductsWI::PWI_HIDE:
                $where = 'ps.`visibility` = \'both\'';
                $set = 'ps.`visibility` = \'none\', p.`visibility` = \'none\'';
                break;

            case XProductsWI::PWI_HIDE_LEFT_SEARCH:
                $where = 'ps.`visibility` = \'both\'';
                $set = 'ps.`visibility` = \'search\', p.`visibility` = \'search\'';
                break;

            case XProductsWI::PWI_HIDE_LEFT_CATALOG:
                $where = 'ps.`visibility` = \'both\'';
                $set = 'ps.`visibility` = \'catalog\', p.`visibility` = \'catalog\'';
                break;
            
            default:
                break;
        }

        $products = array();
        $productsToIgnore = array();

        $ignoredProducts = Configuration::get('X13_PWI_IGNORE_PRODUCTS');
        $ignoredProducts = trim(preg_replace('/\s/', '', $ignoredProducts), ',');
        
        if ($ignoredProducts) {
            $parts = explode(',', $ignoredProducts);
            if (is_array($parts) && count($parts) > 0) {
                $productsToIgnore = $parts;
            }
        }

        $context = Context::getContext();

        $products = Db::getInstance()->executeS('
            SELECT p.`id_product`, pl.`name`, i.`id_image`
            FROM `' . _DB_PREFIX_ . 'product` p
            JOIN `' . _DB_PREFIX_ . 'product_shop` ps
                ON (p.`id_product` = ps.`id_product` AND ps.`id_shop` = ' . (int)$id_shop . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$context->language->id . ' AND pl.`id_shop` = '.(int) $id_shop.')
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i
                ON (ps.`id_product` = i.`id_product`)
            LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                ON (image_shop.`id_image` = i.`id_image`)    
            WHERE image_shop.`id_shop` = '.(int) $id_shop.'
                '.(count($productsToIgnore) > 0 ? 'AND p.`id_product` NOT IN ('.implode(',', $productsToIgnore).')' : '').'
                AND ' . $where
        );

        $productsWithImages = array();

        foreach ($products as $product) {
            $productsWithImages[] = $product['id_product'];
        }

        $updatedProducts = Db::getInstance()->executeS('
            SELECT p.`id_product`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'product` p
            JOIN `' . _DB_PREFIX_ . 'product_shop` ps
                ON (p.`id_product` = ps.`id_product` AND ps.`id_shop` = ' . (int)$id_shop . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$context->language->id . ' AND pl.`id_shop` = '.(int) $id_shop.')
            WHERE 1=1
                '.(count($productsWithImages) > 0 ? 'AND p.`id_product` NOT IN ('.implode(',', array_unique($productsWithImages)).')' : '').'
                '.(count($productsToIgnore) > 0 ? 'AND p.`id_product` NOT IN ('.implode(',', $productsToIgnore).')' : '')
            .' AND '.$where
        );

        if (!Shop::isFeatureActive()) {
            Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'product` p
                JOIN `' . _DB_PREFIX_ . 'product_shop` ps
                    ON (p.`id_product` = ps.`id_product`)
                SET ' . $set . '
                WHERE 1=1
                    '.(count($productsWithImages) > 0 ? 'AND p.`id_product` NOT IN ('.implode(',', array_unique($productsWithImages)).')' : '').'
                    '.(count($productsToIgnore) > 0 ? 'AND p.`id_product` NOT IN ('.implode(',', $productsToIgnore).')' : '').'
                    AND ps.`id_shop` = ' . (int)$id_shop . '
                    AND ' . $where
            );
        }


        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'product_shop` ps
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p
                ON (p.`id_product` = ps.`id_product`)
            SET ' . $set . '
            WHERE 1=1
                '.(count($productsWithImages) > 0 ? 'AND p.`id_product` NOT IN ('.implode(',', array_unique($productsWithImages)).')' : '').'
                AND ps.`id_shop` = ' . (int)$id_shop . '
                '.(count($productsToIgnore) > 0 ? 'AND ps.`id_product` NOT IN ('.implode(',', $productsToIgnore).')' : '').'
                AND ps.`id_shop` = ' . (int)$id_shop . '
                AND ' . $where
        );

        Configuration::updateGlobalValue('X13_PWI_UPDATE_'.$id_shop, date('d.m.Y H:i:s'));

        try {
            Hook::exec('updateproduct');
        } catch (Exception $e) {}

        if ($get_names) {
            return $updatedProducts;
        }

        return true;
    }
}
