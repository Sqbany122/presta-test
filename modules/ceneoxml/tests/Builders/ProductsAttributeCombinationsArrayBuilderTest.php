<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'ProductsAttributeCombinationsArrayBuilder.php'); 

class ProductsAttributeCombinationsArrayBuilderTest extends PHPunit_Framework_Testcase
{
    public function test_Build_ShouldBuildProperArray()
    {
        $attr_combinations_by_prod_id = ProductsAttributeCombinationsArrayBuilder::Build(
                $this->getAtributeCombinations()
        );
        
        $this->assertEquals(count($attr_combinations_by_prod_id), 2);
        $this->assertEquals(count($attr_combinations_by_prod_id[3]), 2);
        $this->assertEquals(count($attr_combinations_by_prod_id[3][6]), 2);
        $this->assertEquals(count($attr_combinations_by_prod_id[3][5]), 1);
        $this->assertEquals(count($attr_combinations_by_prod_id[7]), 1);

        $this->assertEquals($attr_combinations_by_prod_id[3][6][0]["id_attribute"], 8);
        $this->assertEquals($attr_combinations_by_prod_id[3][6][0]["ean13"], "112345");
        $this->assertEquals($attr_combinations_by_prod_id[3][6][0]["reference"], null);
        $this->assertEquals($attr_combinations_by_prod_id[3][6][1]["attr_value"], "Kolor czerwony");
        $this->assertEquals($attr_combinations_by_prod_id[3][5][0]["price"], "751.672241");
        $this->assertEquals($attr_combinations_by_prod_id[7][17][0]["quantity"], 15);
    }
    
    private function getAtributeCombinations()
    {
        return array(
            array(
                "id_product_attribute" => "6",
                "id_product" => "3",
                "id_attribute" => "8",
                "price" => "0.000000",
                "weight" => "0.000000",
                "quantity" => "100",
                "out_of_stock" => "2",
                "attr_value" => "Opcjonalnie Dysk SSD - 64GB",
                "ean13" => "112345",
                "reference" => null,
            ),
            array(
                "id_product_attribute" => "6",
                "id_product" => "3",
                "id_attribute" => "77",
                "price" => "1.00",
                "weight" => "0.000000",
                "quantity" => "100",
                "out_of_stock" => "2",
                "attr_value" => "Kolor czerwony",
                "ean13" => null,
                "reference" => "produkt_kolor_czerwony",
            ),
            array(
                "id_product_attribute" => "5",
                "id_product" => "3",
                "id_attribute" => "9",
                "price" => "751.672241",
                "weight" => "0.000000",
                "quantity" => "14",
                "out_of_stock" => "2",
                "attr_value" => "80GB Dysk ATA @ 4200 rpm",
                "ean13" => null,
                "reference" => null,
            ),
            array(
                "id_product_attribute" => "17",
                "id_product" => "7",
                "id_attribute" => "11",
                "price" => "199.99",
                "weight" => "0.000000",
                "quantity" => "15",
                "out_of_stock" => "2",
                "attr_value" => "120GB Dysk ATA",
                "ean13" => "9932932",
                "reference" => null,
            ),
        );
    }
}
