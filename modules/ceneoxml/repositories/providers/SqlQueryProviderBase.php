<?php

abstract class SqlQueryProviderBase
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function GetProductsSql($limit, $offset)
    {
        return 'SELECT 
                    p.`id_product`, p.`id_category_default`, p.`ean13`, p.`price`, 
                    p.`reference`, sa.`quantity`, sa.`out_of_stock`, pl.`name`,
                    pl.`description`, pl.`link_rewrite`, m.`name` as manufacturer_name, 
                    IF(t.`rate` IS NULL, 0, t.`rate`) as tax_rate, p.`minimal_quantity`
                FROM 
                    `'._DB_PREFIX_.'product` p LEFT JOIN
                    `'._DB_PREFIX_.'tax_rule` tr ON (tr.`id_tax_rules_group` = p.`id_tax_rules_group` AND `id_country` = '.$this->context->country->id.') LEFT JOIN
                    `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`) INNER JOIN
                    `'._DB_PREFIX_.'stock_available` sa ON (sa.`id_product` = p.`id_product` AND sa.`id_product_attribute` = 0) INNER JOIN
                    `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.$this->context->language->id.') LEFT JOIN
                    `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                WHERE p.`active` = 1 AND p.`available_for_order` = 1 AND p.`condition` = \'new\' AND p.`cache_is_pack` = 0
                ORDER BY p.`id_product`
                LIMIT '.$limit.' OFFSET '.$offset;				
    }

    public function GetProductAttributeCombinationsSql($prod_ids)
    {
        return 'SELECT 
                    pac.`id_product_attribute`, pa.`id_product`,  pac.`id_attribute`, pa.`price`, pa.`weight`, 
                    sa.`quantity`, sa.`out_of_stock`, al.`name` as attr_value, pa.`ean13`, pa.`reference`
                FROM 
                    `'._DB_PREFIX_.'product_attribute` pa INNER JOIN
                    `'._DB_PREFIX_.'product_attribute_combination` pac ON pa.`id_product_attribute` = pac.`id_product_attribute` INNER JOIN
                    `'._DB_PREFIX_.'attribute_lang` al ON (al.`id_attribute` = pac.`id_attribute` AND al.`id_lang` = '.$this->context->language->id.') INNER JOIN
                    `'._DB_PREFIX_.'stock_available` sa ON (sa.`id_product_attribute` = pac.`id_product_attribute`) INNER JOIN
                    `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                WHERE pa.`id_product` IN ('.$prod_ids.') AND pa.`minimal_quantity` = 1 AND
                    (sa.`quantity` > 0 OR (sa.`quantity` <= 0 AND sa.`out_of_stock` = 1))
                ORDER BY pac.`id_attribute` ASC';
    }

    public function GetProductsImagesSql($prod_ids)
    {
        return 'SELECT prod_imgs.id_product, prod_imgs.id_image, prod_imgs.id_product_attribute 
                FROM
                (
                    SELECT 
                        i.`id_product`, i.`id_image`, 0 as id_product_attribute, 
                        IF(i.`cover` is null, 0, i.`cover`) as \'cover\', i.`position`
                        FROM 
                                `'._DB_PREFIX_.'product` p INNER JOIN
                                `'._DB_PREFIX_.'image` i ON p.`id_product` = i.`id_product`
                    WHERE 
                        p.`active` = 1 AND p.`id_product` IN ('.$prod_ids.')     

                            UNION ALL                    

                        SELECT 
                                i.`id_product`, i.`id_image`, pai.`id_product_attribute`, 
                                IF(i.`cover` is null, 0, i.`cover`) as \'cover\', i.`position`
                        FROM 
                                `'._DB_PREFIX_.'product` p INNER JOIN
                                `'._DB_PREFIX_.'image` i ON p.`id_product` = i.`id_product` INNER JOIN
                            `'._DB_PREFIX_.'product_attribute_image` pai ON pai.`id_image` = i.`id_image`
                        WHERE p.`active` = 1 AND p.`id_product` IN ('.$prod_ids.') 
                ) prod_imgs
                        ORDER BY prod_imgs.id_product, prod_imgs.id_product_attribute, prod_imgs.cover DESC, prod_imgs.position';
    }

    public function GetAllCategoriesSql()
    {
        return 'SELECT 
                        c.`id_category`, c.`id_parent`, cl.`name` 
                FROM 
                    `'._DB_PREFIX_.'category` c INNER JOIN 
                    `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`
                WHERE c.`id_parent` != 0 AND cl.`id_lang` = '.$this->context->language->id;
    }

    public function GetProductsFeaturesSql($prod_ids)
    {
        return 'SELECT 
                        fp.`id_product`, fp.`id_feature`, fl.`name`, fvl.`value`
                FROM 
                    `'._DB_PREFIX_.'feature_product` fp INNER JOIN
                    `'._DB_PREFIX_.'feature_lang` fl ON fp.`id_feature` = fl.`id_feature` INNER JOIN 
                    `'._DB_PREFIX_.'feature_value_lang` fvl ON fp.`id_feature_value` = fvl.`id_feature_value`
                WHERE 
                        fvl.`id_lang` = '.$this->context->language->id.' AND 
                        fl.`id_lang` = '.$this->context->language->id.' AND fp.`id_product` IN ('.$prod_ids.')';
    }

    abstract public function GetProductsSpecialPricesSql($prod_ids);
}