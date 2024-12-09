<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'repositories' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'SqlQueryProviderFactory.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'repositories' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'SqlQueryProvider_1_5.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'repositories' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'SqlQueryProvider_1_6.php'); 

class SqlQueryProviderFactoryTest extends PHPunit_Framework_Testcase
{
    public function test_Create_ShouldCreateProperClass()
    {
        $this->assertTrue(SqlQueryProviderFactory::Create("1.5", '') instanceof SqlQueryProvider_1_5);
        $this->assertTrue(SqlQueryProviderFactory::Create("1.6", '') instanceof SqlQueryProvider_1_6);
    }
}
