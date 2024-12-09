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
    'frontend_name' => 'Seleção de Moeda',
    'expiry' => '30 dias',
    'description' => 'Cookie que torna possível escolher a moeda que você gostaria. Se desativado, a moeda padrão da loja será mostrada.',
  ),
  'ps_languageselector' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dias',
    'frontend_name' => 'Seletor de Idiomas',
    'description' => 'Cookie que usamos para lhe oferecer a possibilidade de mudar o idioma do nosso conteúdo. Se o cookie estiver desativado, você verá o site no idioma original.',
  ),
  'ps_shoppingcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dias',
    'frontend_name' => 'Carrinho de compras',
    'description' => 'Isso fornece e mantém os produtos dentro de seu carrinho de compras. Desativar esse cookie deixaria de permitir os pedidos. Este cookie não salva nenhum dado pessoal sobre qualquer cliente de loja.',
  ),
  'ps_googleanalytics' =>
  array(
    'category' => 'statistics',
    'enabled' => 1,
    'provider' => 'Google',
    'expiry' => '30 dias',
    'frontend_name' => 'Google Analytics',
    'description' => 'Rastreamento padrão, que faz com que nossa loja compreenda a necessidade e os locais para melhorar nossa loja. Cookie dura por 30 dias.',
  ),
  'ps_legalcompliance' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'Prestashop',
    'expiry' => '30 dias',
    'frontend_name' => 'Conformidade legal',
    'description' => 'Cookie que mede apenas a aceitação de nossos termos e serviços. Desativar esse cookie desativará a opção de pedido.',
  ),
  'blockcart' =>
  array(
    'category' => 'necessary',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dias',
    'frontend_name' => 'Carrinho de compras',
    'description' => 'Isso fornece e mantém os produtos dentro de seu carrinho de compras. Desativar esse cookie deixaria de permitir os pedidos. Este cookie não salva nenhum dado pessoal sobre qualquer cliente de loja.',
  ),
  'blocklanguages' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'expiry' => '30 dias',
    'frontend_name' => 'Seletor de Idiomas',
    'description' => 'Cookie que usamos para lhe oferecer a possibilidade de mudar o idioma do nosso conteúdo. Se o cookie estiver desativado, você verá o site no idioma original.',
  ),
  'blockcurrencies' =>
  array(
    'category' => 'preferences',
    'enabled' => 1,
    'provider' => 'PrestaShop',
    'frontend_name' => 'Seleção de Moeda',
    'expiry' => '30 dias',
    'description' => 'Cookie que torna possível escolher a moeda que você gostaria. Se desativado, a moeda padrão da loja será mostrada.',
  ),
);
