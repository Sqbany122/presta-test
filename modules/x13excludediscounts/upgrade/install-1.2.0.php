<?php

if (!defined('_PS_VERSION_')) {
	exit;
}

function upgrade_module_1_2_0($module)
{
    $module->uninstallOverrides();
    $module->installOverrides();

    $module->deleteBOOverrides();
    $module->installBOOverrides();

    $module->registerHook('displayBackOfficeHeader');
    
	return true;
}
