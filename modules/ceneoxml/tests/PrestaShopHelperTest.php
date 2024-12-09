<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'PrestaShopHelper.php'); 

class PrestaShopHelperTest extends PHPunit_Framework_Testcase
{
    public function test_GetPSVersionBasePart_ShouldReturnProperVersion()
    {
        $this->assertEquals(PrestaShopHelper::GetPSVersionBasePart("1.5.6.7"), "1.5");
        $this->assertEquals(PrestaShopHelper::GetPSVersionBasePart("1"), "1");
    }
}
