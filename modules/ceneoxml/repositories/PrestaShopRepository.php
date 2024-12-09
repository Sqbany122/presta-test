<?php

class PrestaShopRepository
{
    private $sql_query_provider;

    public function __construct($sql_query_provider)
    {
        $this->sql_query_provider = $sql_query_provider;
    }

    public function GetProducts($limit, $offset)
    {
        return Db::getInstance()->executeS(
            $this->sql_query_provider->GetProductsSql($limit, $offset)
        );
    }

    public function GetProductsAttributeCombinations($prod_ids)
    {
        return Db::getInstance()->executeS(
            $this->sql_query_provider->GetProductAttributeCombinationsSql(join(',', $prod_ids))
        );
    }

    public function GetProductsSpecialPrices($prod_ids)
    {
        return Db::getInstance()->executeS(
            $this->sql_query_provider->GetProductsSpecialPricesSql(join(',', $prod_ids))
        );	
    }

    public function GetProductsImages($prod_ids)
    {
        return Db::getInstance()->executeS(
            $this->sql_query_provider->GetProductsImagesSql(join(',', $prod_ids))
        );
    }

    public function GetAllCategories()
    {
        return Db::getInstance()->executeS(
            $this->sql_query_provider->GetAllCategoriesSql()
        );
    }

    public function GetProductsFeatures($prod_ids)
    {
        return Db::getInstance()->executeS(
            $this->sql_query_provider->GetProductsFeaturesSql(join(',', $prod_ids))
        );
    }
}