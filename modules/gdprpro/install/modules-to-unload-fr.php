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
    'frontend_name' => 'Sélection de devise',
    'expiry' => '30 jours',
    'description' => 'Cookie qui permet de choisir la devise que vous souhaitez. Si elle est désactivée, la devise par défaut du magasin sera affichée.',
  ),
  'ps_languageselector' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 jours',
    'frontend_name' => 'Sélecteur de langue',
    'description' => 'Cookie nous utilisons pour vous offrir la possibilité de changer la langue de nos contenus. Si le cookie est désactivé, vous verrez le site dans la langue d\'origine.',
  ),
  'ps_shoppingcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 jours',
    'frontend_name' => 'Chariot',
    'description' => 'Cela fournit et garde les produits dans votre panier. Désactiver ce cookie arrêterait d\'autoriser les commandes. Ce cookie n\'enregistre aucune donnée personnelle concernant un client du magasin.',
  ),
  'ps_googleanalytics' =>
  array(
    'category' => 'statistics',
    'enabled' => 1,
    'provider' => 'Google',
    'expiry' => '30 jours',
    'frontend_name' => 'Google Analytics',
    'description' => 'Suivi standard, ce qui fait comprendre à notre boutique la nécessité et les lieux pour améliorer notre boutique. Le cookie dure 30 jours.',
  ),
  'ps_legalcompliance' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'Prestashop',
    'expiry' => '30 jours',
    'frontend_name' => 'Conformité légale',
    'description' => 'Cookie qui ne mesure que l\'acceptation de nos termes et services. La désactivation de ce cookie désactivera l\'option de commande.',
  ),
  'blockcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 jours',
    'frontend_name' => 'Chariot',
    'description' => 'Cela fournit et garde les produits dans votre panier. Désactiver ce cookie arrêterait d\'autoriser les commandes. Ce cookie n\'enregistre aucune donnée personnelle concernant un client du magasin.',
  ),
  'blocklanguages' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 jours',
    'frontend_name' => 'Sélecteur de langue',
    'description' => 'Cookie nous utilisons pour vous offrir la possibilité de changer la langue de nos contenus. Si le cookie est désactivé, vous verrez le site dans la langue d\'origine.',
  ),
  'blockcurrencies' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'frontend_name' => 'Sélection de devise',
    'expiry' => '30 jours',
    'description' => 'Cookie qui permet de choisir la devise que vous souhaitez. Si elle est désactivée, la devise par défaut du magasin sera affichée.',
  ),
);
