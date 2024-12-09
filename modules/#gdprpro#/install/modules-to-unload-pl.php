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
    'frontend_name' => 'Wybór waluty',
    'expiry' => '30 dni',
    'description' => 'Plik cookie, który umożliwia wybranie waluty, którą chcesz. W przypadku wyłączenia zostanie wyświetlona domyślna waluta sklepu.',
  ),
  'ps_languageselector' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dni',
    'frontend_name' => 'Wybór języka',
    'description' => 'Cookie używamy, aby zaoferować Ci możliwość zmiany języka naszej zawartości. Jeśli ciasteczko jest wyłączone, zobaczysz stronę w oryginalnym języku.',
  ),
  'ps_shoppingcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dni',
    'frontend_name' => 'Wózek sklepowy',
    'description' => 'Zapewnia to i utrzymuje produkty w koszyku. Dezaktywacja tego pliku cookie przestałaby umożliwiać zamówienia. Ten plik cookie nie zapisuje żadnych danych osobowych dotyczących żadnego klienta sklepu.',
  ),
  'ps_googleanalytics' =>
  array(
    'category' => 'statistics',
    'enabled' => 1,
    'provider' => 'Google',
    'expiry' => '30 dni',
    'frontend_name' => 'Google Analytics',
    'description' => 'Standardowe śledzenie, dzięki któremu nasz sklep rozumie konieczność i miejsca, w których można ulepszyć nasz sklep. Plik cookie jest dostępny przez 30 dni.',
  ),
  'ps_legalcompliance' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'Prestashop',
    'expiry' => '30 dni',
    'frontend_name' => 'Zgodność z prawem',
    'description' => 'Plik cookie, który mierzy jedynie akceptację naszych warunków i usług. Wyłączenie tego pliku cookie spowoduje wyłączenie opcji zamawiania.',
  ),
  'blockcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dni',
    'frontend_name' => 'Wózek sklepowy',
    'description' => 'Zapewnia to i utrzymuje produkty w koszyku. Dezaktywacja tego pliku cookie przestałaby umożliwiać zamówienia. Ten plik cookie nie zapisuje żadnych danych osobowych dotyczących żadnego klienta sklepu.',
  ),
  'blocklanguages' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dni',
    'frontend_name' => 'Wybór języka',
    'description' => 'Cookie używamy, aby zaoferować Ci możliwość zmiany języka naszej zawartości. Jeśli ciasteczko jest wyłączone, zobaczysz stronę w oryginalnym języku.',
  ),
  'blockcurrencies' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'frontend_name' => 'Wybór waluty',
    'expiry' => '30 dni',
    'description' => 'Plik cookie, który umożliwia wybranie waluty, którą chcesz. W przypadku wyłączenia zostanie wyświetlona domyślna waluta sklepu.',
  ),
);
