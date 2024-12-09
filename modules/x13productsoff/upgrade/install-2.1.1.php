<?php
/**
* @author    Krystian Podemski for x13.pl <krystian@x13.pl>
* @copyright Copyright (c) 2020 Krystian Podemski - www.x13.pl
* @license   Commercial license, only to use on restricted domains
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_1($module)
{
    return $module->registerHook('displayBackOfficeHeader');
}
