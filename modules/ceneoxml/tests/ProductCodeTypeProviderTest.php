<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR .'ProductCodeTypeProvider.php'); 

class ProductCodeTypeProviderTest extends PHPunit_Framework_Testcase
{
    public function test_GetCodeType_ShouldReturnProperIsbn()
    {
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("9781234567891"), "ISBN");
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("9799393283294"), "ISBN");
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("2343245643"), "ISBN");
    }

    public function test_GetCodeType_ShouldReturnProperEan()
    {
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("2343443443422"), "EAN");
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("32443"), "EAN");
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("12345678"), "EAN");
    }

    public function test_GetCodeType_ShouldReturnProperBloz7()
    {
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("1234567"), "BLOZ_7");
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("9784567"), "BLOZ_7");
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("9794567"), "BLOZ_7");
    }

    public function test_GetCodeType_ShouldReturnProperBloz12()
    {
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("123456789120"), "BLOZ_12");
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("978456789120"), "BLOZ_12");
        $this->assertEquals(ProductCodeTypeProvider::GetCodeType("979456789120"), "BLOZ_12");
    }
}