<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license commercial
 */
 // @codingStandardsIgnoreFile
return array(
  'ps_currencyselector' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'frontend_name' => '货币选择',
    'expiry' => '30天',
    'description' => 'Cookie可以选择你想要的货币。如果禁用，则会显示商店默认货币。',
  ),
  'ps_languageselector' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30天',
    'frontend_name' => '语言选择器',
    'description' => '我们使用Cookie来为您提供切换内容语言的可能性。如果cookie被禁用，您将以原始语言看到该网站。',
  ),
  'ps_shoppingcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30天',
    'frontend_name' => '购物车',
    'description' => '这提供并保持购物车内的产品。取消激活这个cookie会停止允许订单。此Cookie不保存关于任何商店客户的任何个人数据。',
  ),
  'ps_googleanalytics' =>
  array(
    'category' => 'statistics',
    'enabled' => 1,
    'provider' => 'Google',
    'expiry' => '30天',
    'frontend_name' => '谷歌分析',
    'description' => '标准跟踪，这使我们的商店了解改善我们商店的必要性和地点。 Cookie持续30天。',
  ),
  'ps_legalcompliance' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'Prestashop',
    'expiry' => '30天',
    'frontend_name' => '合法合规',
    'description' => 'Cookie仅衡量我们的条款和服务的接受程度。禁用此cookie将禁用订购选项。',
  ),
  'blockcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30天',
    'frontend_name' => '购物车',
    'description' => '这提供并保持购物车内的产品。取消激活这个cookie会停止允许订单。此Cookie不保存关于任何商店客户的任何个人数据。',
  ),
  'blocklanguages' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30天',
    'frontend_name' => '语言选择器',
    'description' => '我们使用Cookie来为您提供切换内容语言的可能性。如果cookie被禁用，您将以原始语言看到该网站。',
  ),
  'blockcurrencies' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'frontend_name' => '货币选择',
    'expiry' => '30天',
    'description' => 'Cookie可以选择你想要的货币。如果禁用，则会显示商店默认货币。',
  ),
);
