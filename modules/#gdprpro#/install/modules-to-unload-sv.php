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
    'frontend_name' => 'Valutaväljning',
    'expiry' => '30 dagar',
    'description' => 'Cookie som gör det möjligt att välja den valuta du vill ha. Om den är inaktiverad visas standardvaluta för butiken.',
  ),
  'ps_languageselector' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dagar',
    'frontend_name' => 'Språkväljare',
    'description' => 'Cookie vi använder för att erbjuda dig möjlighet att byta språk i vårt innehåll. Om cookie är inaktiverad ser du webbplatsen på originalspråket.',
  ),
  'ps_shoppingcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dagar',
    'frontend_name' => 'Kundvagn',
    'description' => 'Detta ger och håller produkterna i din kundvagn. Om du avaktiverar denna cookie skulle du sluta tillåta beställningarna. Denna cookie sparar inte några personuppgifter om någon butiksklient.',
  ),
  'ps_googleanalytics' =>
  array(
    'category' => 'statistics',
    'enabled' => 1,
    'provider' => 'Google',
    'expiry' => '30 dagar',
    'frontend_name' => 'Google Analytics',
    'description' => 'Standard spårning, vilket gör vår butik förstå nödvändigheten och ställen att förbättra vår butik. Kakan varar i 30 dagar.',
  ),
  'ps_legalcompliance' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'Prestashop',
    'expiry' => '30 dagar',
    'frontend_name' => 'Lagstiftning efterlevs',
    'description' => 'Cookie som endast mäter acceptansen av våra villkor och tjänster. Om du avaktiverar den här cookien avaktiveras beställningsalternativet.',
  ),
  'blockcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dagar',
    'frontend_name' => 'Kundvagn',
    'description' => 'Detta ger och håller produkterna i din kundvagn. Om du avaktiverar denna cookie skulle du sluta tillåta beställningarna. Denna cookie sparar inte några personuppgifter om någon butiksklient.',
  ),
  'blocklanguages' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dagar',
    'frontend_name' => 'Språkväljare',
    'description' => 'Cookie vi använder för att erbjuda dig möjlighet att byta språk i vårt innehåll. Om cookie är inaktiverad ser du webbplatsen på originalspråket.',
  ),
  'blockcurrencies' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'frontend_name' => 'Valutaväljning',
    'expiry' => '30 dagar',
    'description' => 'Cookie som gör det möjligt att välja den valuta du vill ha. Om den är inaktiverad visas standardvaluta för butiken.',
  ),
);
