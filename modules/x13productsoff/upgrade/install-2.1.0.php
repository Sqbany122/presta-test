<?php
/**
* @author    Krystian Podemski for x13.pl <krystian@x13.pl>
* @copyright Copyright (c) 2018 Krystian Podemski - www.x13.pl
* @license   Commercial license, only to use on restricted domains
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_0($module)
{
    Configuration::updateGlobalValue('PRODUCT_OFF_IGNORE_PRODUCTS', '');
    Configuration::updateGlobalValue('PRODUCT_OFF_TRESHOLD', '1');
    Configuration::updateGlobalValue('PRODUCT_OFF_AUTOENABLE', 0);
    Configuration::updateGlobalValue('PRODUCT_OFF_AUTOENABLE_TRESHOLD', '0');
    
    return true;
}
