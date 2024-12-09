<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'helpers' . DIRECTORY_SEPARATOR . 'PrestaShopHelper.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'repositories' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'SqlQueryProvider_1_5.php'); 
require_once(__CENEO_XML_MODULE_PATH__ . 'repositories' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR . 'SqlQueryProvider_1_6.php'); 


class SqlQueryProviderFactory
{
    public static function Create($version, $context)
    {
        switch($version)
        {
            case '1.5':
                return new SqlQueryProvider_1_5($context);
                break;

            case '1.6':
                return new SqlQueryProvider_1_6($context);
                break;
        }		
    }
}
