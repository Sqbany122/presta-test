<?php
/**
 * 2007-2019 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2019 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
 
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
include(_PS_ADMIN_DIR_.'/../../config/config.inc.php');
include(dirname(__FILE__).'/ajax.init.php');
$context = Context::getContext();
$ets_livechat = Module::getInstanceByName('ets_livechat');
if(Tools::getValue('token')!=md5($ets_livechat->id))
    die();
$query = Tools::getValue('q', false);
if (!$query OR $query == '' OR Tools::strlen($query) < 1)
	die();
$sql = 'SELECT c.*,a.phone,a.phone_mobile FROM '._DB_PREFIX_.'customer c
LEFT JOIN '._DB_PREFIX_.'address a ON (c.id_customer=a.id_customer)
WHERE CONCAT(c.firstname," ",c.lastname) LIKE "'.pSQL($query).'%" OR c.email like "'.pSQL($query).'%" OR c.id_customer="'.(int)$query.'" OR a.phone like "'.pSQL($query).'%" OR a.phone_mobile like"'.pSQL($query).'%"';
$customers = Db::getInstance()->executeS($sql);
if($customers)
{
    foreach($customers as $customer)
    {
        echo trim($customer['firstname'] .' '.$customer['lastname']).'|'.($customer['email']).'|'.($customer['phone'] ? $customer['phone'] : $customer['phone_mobile']).'|'.(int)$customer['id_customer']."\n";
    }
}
die();