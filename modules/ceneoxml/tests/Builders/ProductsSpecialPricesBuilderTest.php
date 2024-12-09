<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'ProductsSpecialPricesBuilder.php'); 

class ProductsSpecialPricesBuilderTest extends PHPunit_Framework_Testcase
{
    private $special_price_builder;

    public function setUp()
    { 
        $this->special_price_builder = new ProductsSpecialPricesBuilder('id_currency;id_country;id_group;id_shop');
    }
    
    public function test_Build_ShouldReturnSpecialPriceForHighestCurrency()
    {
        $special_prices = array(
            array(
                "id_product"              =>  "1",
                "id_product_attribute"    =>  "0",
                "price"                   =>  NULL,
                "reduction"               =>  "100.000000",
                "reduction_tax"           =>  "1",
                "reduction_type"          =>  "1",
                "id_currency"             =>  "0",
                "id_country"              =>  "0",
                "date_from"               =>  NULL,
                "date_to"                 =>  NULL,
                "id_shop"                 =>    0,
                "id_group"                =>    0,
                "priority"                =>  'id_country;id_currency;id_group;id_shop',
                "id_country"              =>    0,
                "id_currency"             =>    1
            ),
            array(
                "id_product"              =>  "1",
                "id_product_attribute"    =>  "0",
                "price"                   =>  NULL,
                "reduction"               =>  "100.000000",
                "reduction_tax"           =>  "1",
                "reduction_type"          =>  "1",
                "id_currency"             =>  "1",
                "id_country"              =>  "0",
                "date_from"               =>  NULL,
                "date_to"                 =>  NULL,
                "id_shop"                 =>    0,
                "id_group"                =>    1,
                "priority"                =>  'id_country;id_currency;id_group;id_shop',
                "id_country"              =>    2,
                "id_currency"             =>    0
            )
        );
        
        $special_prices_by_prod_id = $this->special_price_builder->Build($special_prices);
        
        $this->assertEquals(count($special_prices_by_prod_id), 1);
        $this->assertEquals($special_prices_by_prod_id[1][0]["id_currency"], 0);
        $this->assertEquals($special_prices_by_prod_id[1][0]["id_country"], 2);
    }
    
    public function test_Build_ShouldReturnSpecialPriceForGlobalPriorities()
    {
        $special_prices = array(
            array(
                "id_product"              =>  "1",
                "id_product_attribute"    =>  "0",
                "price"                   =>  NULL,
                "reduction"               =>  "100.000000",
                "reduction_tax"           =>  "1",
                "reduction_type"          =>  "1",
                "id_currency"             =>  "0",
                "id_country"              =>  "0",
                "date_from"               =>  NULL,
                "date_to"                 =>  NULL,
                "id_shop"                 =>    0,
                "id_group"                =>    0,
                "priority"                => NULL,
                "id_country"              =>    0,
                "id_currency"             =>    1
            ),
            array(
                "id_product"              =>  "1",
                "id_product_attribute"    =>  "0",
                "price"                   =>  NULL,
                "reduction"               =>  "100.000000",
                "reduction_tax"           =>  "1",
                "reduction_type"          =>  "1",
                "id_currency"             =>  "1",
                "id_country"              =>  "4",
                "date_from"               =>  NULL,
                "date_to"                 =>  NULL,
                "id_shop"                 =>    0,
                "id_group"                =>    0,
                "priority"                => NULL,
                "id_country"              =>    0,
                "id_currency"             =>    0
            )
        );
        
        $special_prices_by_prod_id = $this->special_price_builder->Build($special_prices);
        
        $this->assertEquals(count($special_prices_by_prod_id), 1);
        $this->assertEquals($special_prices_by_prod_id[1][0]["id_currency"], 1);
    }
    
    public function test_Build_ShouldReturnSpecialPriceIfDateIsSet()
    {
        $special_prices = array(
            array(
                "id_product"              =>  "1",
                "id_product_attribute"    =>  "0",
                "price"                   =>  NULL,
                "reduction"               =>  "100.000000",
                "reduction_tax"           =>  "1",
                "reduction_type"          =>  "1",
                "id_currency"             =>  "0",
                "id_country"              =>  "0",
                "date_from"               =>  "2016-02-24 00:00:00",
                "date_to"                 =>  NULL,
                "id_shop"                 =>    0,
                "id_group"                =>    0,
                "priority"                => NULL,
                "id_country"              =>    0,
                "id_currency"             =>    0
            ),
            array(
                "id_product"              =>  "1",
                "id_product_attribute"    =>  "0",
                "price"                   =>  NULL,
                "reduction"               =>  "100.000000",
                "reduction_tax"           =>  "1",
                "reduction_type"          =>  "1",
                "id_currency"             =>  "0",
                "id_country"              =>  "0",
                "date_from"               =>  NULL,
                "date_to"                 =>  NULL,
                "id_shop"                 =>    0,
                "id_group"                =>    0,
                "priority"                => NULL,
                "id_country"              =>    0,
                "id_currency"             =>    0
            ),
            array(
                "id_product"              =>  "3",
                "id_product_attribute"    =>  "1",
                "price"                   =>  NULL,
                "reduction"               =>  "100.000000",
                "reduction_tax"           =>  "1",
                "reduction_type"          =>  "1",
                "id_currency"             =>  "0",
                "id_country"              =>  "0",
                "date_from"               =>  NULL,
                "date_to"                 =>  NULL,
                "id_shop"                 =>    0,
                "id_group"                =>    1,
                "priority"                => NULL,
                "id_country"              =>    0,
                "id_currency"             =>    0
            ),
            array(
                "id_product"              =>  "3",
                "id_product_attribute"    =>  "1",
                "price"                   =>  NULL,
                "reduction"               =>  "100.000000",
                "reduction_tax"           =>  "1",
                "reduction_type"          =>  "1",
                "id_currency"             =>  "0",
                "id_country"              =>  "0",
                "date_from"               =>  NULL,
                "date_to"                 =>  "2016-02-12 00:00:00",
                "id_shop"                 =>    0,
                "id_group"                =>    1,
                "priority"                => NULL,
                "id_country"              =>    0,
                "id_currency"             =>    0
            )
        );
        
        $special_prices_by_prod_id = $this->special_price_builder->Build($special_prices);
        
        $this->assertEquals(count($special_prices_by_prod_id), 2);
        $this->assertEquals($special_prices_by_prod_id[1][0]["date_from"], "2016-02-24 00:00:00");
        $this->assertEquals($special_prices_by_prod_id[3][1]["date_to"], "2016-02-12 00:00:00");
    }
}
