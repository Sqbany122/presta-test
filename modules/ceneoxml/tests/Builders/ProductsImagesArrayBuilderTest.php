<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'ProductsImagesArrayBuilder.php'); 

class ProductsImagesArrayBuilderTest extends PHPunit_Framework_Testcase
{
    public function test_Build_ShouldBuildProperArray()
    {
        $images_by_prod_id = ProductsImagesArrayBuilder::Build($this->getProductsImages());
        
        $this->assertEquals(count($images_by_prod_id[1]), 3);
        $this->assertEquals(count($images_by_prod_id[1][0]), 2);
        $this->assertEquals(count($images_by_prod_id[2]), 1);
        
        $this->assertEquals($images_by_prod_id[1][0][0]["id_image"], 15);
        $this->assertEquals($images_by_prod_id[1][0][1]["id_image"], 16);
        $this->assertEquals($images_by_prod_id[1][21][0]["id_image"], 17);
        $this->assertEquals($images_by_prod_id[2][0][0]["id_image"], 33);
    }
    
    private function getProductsImages()
    {
        return array(
            array(
                "id_product"            =>  "1",
                "id_image"              =>  "15",
                "id_product_attribute"  =>  "0"
            ),
            array(
                "id_product"            =>  "1",
                "id_image"              =>  "16",
                "id_product_attribute"  =>  "0"
            ),
            array(
                "id_product"            =>  "1",
                "id_image"              =>  "17",
                "id_product_attribute"  =>  "21"
            ),
            array(
                "id_product"            =>  "1",
                "id_image"              =>  "18",
                "id_product_attribute"  =>  "22"
            ),
            array(
                "id_product"            =>  "2",
                "id_image"              =>  "33",
                "id_product_attribute"  =>  "0"
            )
        ); 
    }
}
