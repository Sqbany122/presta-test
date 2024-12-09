<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'builders' . DIRECTORY_SEPARATOR . 'ArrayBuilders' . DIRECTORY_SEPARATOR . 'ProductsFeaturesArrayBuilder.php'); 

class ProductsFeaturesArrayBuilderTest extends PHPunit_Framework_Testcase
{
    public function test_Build_ShouldBuildProperArray()
    {
        $features_by_id = ProductsFeaturesArrayBuilder::Build($this->getFeatures());
        
        $this->assertEquals(count($features_by_id[1]), 3);
        $this->assertEquals(count($features_by_id[2]), 1);
        
        $this->assertEquals($features_by_id[1]["Słuchawki"], "Jack Stereo");
        $this->assertEquals($features_by_id[1][4], 28);
    }
    
    private function getFeatures()
    {
        return array(
            array(
                "id_product"    =>  "1",
                "id_feature"    =>  "5",
                "name"          =>  "Słuchawki",
                "value"         =>  "Jack Stereo"
            ),
            array(
                "id_product"    =>  "1",
                "id_feature"    =>  "1",
                "name"          =>  "Wysokość",
                "value"         =>  "11"
            ),
            array(
                "id_product"    =>  "1",
                "id_feature"    =>  "4",
                "name"          =>  "Waga",
                "value"         =>  "28"
            ),
            array(
                "id_product"    =>  "2",
                "id_feature"    =>  "13",
                "name"          =>  "Kabel HDMI",
                "value"         =>  "1,5"
            )
        ); 
    }
}
