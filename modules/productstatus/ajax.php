<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 27.12.13
 * Time: 11:24
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once ('../../config/config.inc.php');
require_once _PS_MODULE_DIR_.'/productstatus/productstatus.php';

if ( tools::getValue('action') == 'getStatuses' )
{
    $result = productStatus::getStatuses(tools::getValue('id_lang'));
    echo tools::jsonEncode(array('result' => $result));
}

if ( tools::getValue('action') == 'setStatus' )
{
    $result = productStatus::setStatus(tools::getValue('id_detail'), tools::getValue('id_state'), tools::getValue('id_employee'), tools::getValue('id_lang'));
    echo tools::jsonEncode(array('result' => $result));
}

if ( tools::getValue('action') == 'test' )
{
    $result = productStatus::getOrderProducts(tools::getValue('id_details'));
    echo tools::jsonEncode(array('result' => $result));
}

if ( tools::getValue('action') == 'export' )
{
    $result = productStatus::exportStatuses();
    echo "done";
}

if ( tools::getValue('action') == 'getHistory' )
{
    $result = productStatus::getProductStatusHistory(tools::getValue('id_order_detail'), tools::getValue('id_lang'));
    echo tools::jsonEncode(array('result' => $result));
}

if ( tools::getValue('action') == 'setDate')
{
    $result = productStatus::setDate(tools::getValue('field'), tools::getValue('id_order_detail'), tools::getValue('date'));
}