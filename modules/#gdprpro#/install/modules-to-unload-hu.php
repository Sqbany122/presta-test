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
    'frontend_name' => 'Valutaválasztás',
    'expiry' => '30 nap',
    'description' => 'Cookie, amely lehetővé teszi a kívánt pénznem kiválasztását. Ha letiltja a tároló alapértelmezett pénznemét, akkor megjelenik.',
  ),
  'ps_languageselector' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 nap',
    'frontend_name' => 'Nyelvválasztó',
    'description' => 'Cookie-t használunk, hogy felajánljuk a lehetőséget a tartalom nyelvének megváltoztatására. Ha a cookie ki van kapcsolva, az eredeti nyelven megjelenik a webhely.',
  ),
  'ps_shoppingcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 nap',
    'frontend_name' => 'Bevásárlókocsi',
    'description' => 'Ez biztosítja a termékek tárolását a bevásárlókosárban. A cookie deaktiválása leállítja a megrendelések engedélyezését. Ez a cookie nem ment meg semmilyen személyes adatot sem az ügyféllel kapcsolatban.',
  ),
  'ps_googleanalytics' =>
  array(
    'category' => 'statistics',
    'enabled' => 1,
    'provider' => 'Google',
    'expiry' => '30 nap',
    'frontend_name' => 'A Google Analytics',
    'description' => 'Szabványos nyomon követés, amely a boltunknak megértette a boltunk szükségességét és helyét. A cookie 30 napig tart.',
  ),
  'ps_legalcompliance' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'Prestashop',
    'expiry' => '30 nap',
    'frontend_name' => 'A jogszabályok betartása',
    'description' => 'Cookie, amely csak a feltételeink és szolgáltatásaink elfogadását méri. A cookie letiltása letiltja a rendelési lehetőséget.',
  ),
  'blockcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 nap',
    'frontend_name' => 'Bevásárlókocsi',
    'description' => 'Ez biztosítja a termékek tárolását a bevásárlókosárban. A cookie deaktiválása leállítja a megrendelések engedélyezését. Ez a cookie nem ment meg semmilyen személyes adatot sem az ügyféllel kapcsolatban.',
  ),
  'blocklanguages' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 nap',
    'frontend_name' => 'Nyelvválasztó',
    'description' => 'Cookie-t használunk, hogy felajánljuk a lehetőséget a tartalom nyelvének megváltoztatására. Ha a cookie ki van kapcsolva, az eredeti nyelven megjelenik a webhely.',
  ),
  'blockcurrencies' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'frontend_name' => 'Valutaválasztás',
    'expiry' => '30 nap',
    'description' => 'Cookie, amely lehetővé teszi a kívánt pénznem kiválasztását. Ha letiltja a tároló alapértelmezett pénznemét, akkor megjelenik.',
  ),
);
