<?php
/**
* 2013 - 2018 HiPresta
*
* MODULE Facebook Connect
*
* @author    HiPresta <suren.mikaelyan@gmail.com>
* @copyright HiPresta 2018
* @license   Addons PrestaShop license limitation
* @link      http://www.hipresta.com
*
* NOTICE OF LICENSE
*
* Don't use this module on several shops. The license provided by PrestaShop Addons
* for all its modules is valid only once for a single shop.
*/

function upgrade_module_1_1_1($module)
{
    $module->registerHook('DashboardZoneOne');
    $module->updatePosition(Hook::getIdByName('DashboardZoneOne'), 0);
    $module->registerHook('displayBackOfficeHome');
    $module->updatePosition(Hook::getIdByName('displayBackOfficeHome'), 0);
    return true;
}
