<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'CategoriesArrayBuilder.php'); 

class CategoriesArrayBuilderTest extends PHPunit_Framework_Testcase
{
    public function test_Build_ShouldBuildProperArray()
    {
        $categories_by_id = CategoriesArrayBuilder::Build($this->getCategories());
        
        $this->assertEquals($categories_by_id[12]["name"], "T-Shirts");
        $this->assertEquals($categories_by_id[15]["id_parent"], 3);
    }
    
    private function getCategories()
    {
        return array(
            array(
                "id_category" => 12,
                "id_parent" => 4,
                "name" => "T-Shirts"
            ),
            array(
                "id_category" => 15,
                "id_parent" => 3,
                "name" => "Trousers"
            )
        );
    }
}
