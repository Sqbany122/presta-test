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
    'frontend_name' => 'Selección de moneda',
    'expiry' => '30 dias',
    'description' => 'Cookie que hace posible elegir la moneda que desea. Si está deshabilitado, se mostrará la moneda predeterminada de la tienda.',
  ),
  'ps_languageselector' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dias',
    'frontend_name' => 'Selector de idioma',
    'description' => 'Cookie que utilizamos para ofrecerle la posibilidad de cambiar el idioma de nuestros contenidos. Si la cookie está desactivada, verá el sitio en el idioma original.',
  ),
  'ps_shoppingcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dias',
    'frontend_name' => 'Carrito de compras',
    'description' => 'Esto proporciona y mantiene los productos dentro de su carrito de compras. La desactivación de esta cookie dejaría de permitir las órdenes. Esta cookie no guarda ningún dato personal sobre ningún cliente de la tienda.',
  ),
  'ps_googleanalytics' =>
  array(
    'category' => 'statistics',
    'enabled' => 1,
    'provider' => 'Google',
    'expiry' => '30 dias',
    'frontend_name' => 'Google analitico',
    'description' => 'Seguimiento estándar, que hace que nuestra tienda comprenda la necesidad y los lugares para mejorar nuestra tienda. La cookie dura 30 días.',
  ),
  'ps_legalcompliance' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'Prestashop',
    'expiry' => '30 dias',
    'frontend_name' => 'Cómplice legal',
    'description' => 'Cookie que mide solo la aceptación de nuestros términos y servicios. Deshabilitar esta cookie deshabilitará la opción de ordenar.',
  ),
  'blockcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dias',
    'frontend_name' => 'Carrito de compras',
    'description' => 'Esto proporciona y mantiene los productos dentro de su carrito de compras. La desactivación de esta cookie dejaría de permitir las órdenes. Esta cookie no guarda ningún dato personal sobre ningún cliente de la tienda.',
  ),
  'blocklanguages' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dias',
    'frontend_name' => 'Selector de idioma',
    'description' => 'Cookie que utilizamos para ofrecerle la posibilidad de cambiar el idioma de nuestros contenidos. Si la cookie está desactivada, verá el sitio en el idioma original.',
  ),
  'blockcurrencies' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'frontend_name' => 'Selección de moneda',
    'expiry' => '30 dias',
    'description' => 'Cookie que hace posible elegir la moneda que desea. Si está deshabilitado, se mostrará la moneda predeterminada de la tienda.',
  ),
);
