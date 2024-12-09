<?php

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'UrlProviderFactory.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'UrlProvider_1_5.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'UrlProvider_1_6.php'); 

class UrlProviderFactoryTest extends PHPunit_Framework_Testcase
{
    public function test_Create_ShouldCreateProperClass()
    {
        $this->assertTrue(UrlProviderFactory::Create("1.5", '') instanceof UrlProvider_1_5);
        $this->assertTrue(UrlProviderFactory::Create("1.6", '') instanceof UrlProvider_1_6);
    }
}
