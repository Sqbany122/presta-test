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
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

// @codingStandardsIgnoreFile
use \PrestaChamps\Common\Helpers\MultilangHelper;

/**
 * I've got this error in the validator:
 * `Hard coded database prefixes are forbidden, you should use '_DB_PREFIX_' instead` but
 * these are not db prefixes, these are just default install values for the module
 */
return array(
    'ps_currencyselector' => array(
        'category'      => GdprPro::COOKIE_CATEGORY_PREFERENCES,
        'enabled'       => 1,
        'provider'      => 'PrestaShop',
        'frontend_name' => 'Currency Selection',
        'expiry'        => '30 days',
        'description'   =>
            'Cookie which makes possible to choose the 
            currency you would like. If disabled the store default currency will be shown.'
    ),
    'ps_languageselector' => array(
        'category'      => GdprPro::COOKIE_CATEGORY_PREFERENCES,
        'enabled'       => 1,
        'provider'      => 'PrestaShop',
        'expiry'        => '30 days',
        'frontend_name' => 'Language Selector',
        'description'   =>
            'Cookie we use to offer you the possibility to switch the language of our contents. 
            If cookie is disabled you\'ll see the site in the 
            original language.'
    ),
    'ps_shoppingcart'     => array(
        'category'      => GdprPro::COOKIE_CATEGORY_NECESSARY,
        'enabled'       => 1,
        'provider'      => 'PrestaShop',
        'expiry'        => '30 days',
        'frontend_name' => 'Shopping cart',
        'description'   =>
            'This provides, and keeps the products inside your shopping cart. 
            Deactivating this cookie would stop permitting the orders. This cookie doesn\'t 
            save any personal Data about any shop client.'
    ),
    'ps_googleanalytics'  => array(
        'category'      => GdprPro::COOKIE_CATEGORY_STATISTICS,
        'enabled'       => 1,
        'provider'      => 'Google',
        'expiry'        => '30 days',
        'frontend_name' => 'Google Analytics',
        'description'   =>
            'Standard tracking, which make our shop understand the necessity and places to improve our shop. 
            Cookie last for 30 days.'
    ),
    'ps_legalcompliance'  => array(
        'category'      => GdprPro::COOKIE_CATEGORY_PREFERENCES,
        'enabled'       => 1,
        'provider'      => 'Prestashop',
        'expiry'        => '30 days',
        'frontend_name' => 'Legal Compliance',
        'description'   =>
            'Cookie which measures only the acceptance of our terms and services. 
            Disabling this cookie will disable the ordering option.'
    ),
    // 1.6 Modules
    'blockcart'           => array(
        'category'      => GdprPro::COOKIE_CATEGORY_NECESSARY,
        'enabled'       => 1,
        'provider'      => 'PrestaShop',
        'expiry'        => '30 days',
        'frontend_name' => 'Shopping cart',
        'description'   => 'This provides, and keeps the products inside your shopping cart. 
            Deactivating this cookie would stop permitting the orders. This cookie doesn\'t 
            save any personal Data about any shop client.',

    ),
    'blocklanguages'      => array(
        'category'      => GdprPro::COOKIE_CATEGORY_PREFERENCES,
        'enabled'       => 1,
        'provider'      => 'PrestaShop',
        'expiry'        => '30 days',
        'frontend_name' => 'Language Selector',
        'description'   => 'Cookie we use to offer you the possibility to switch the language of our contents. 
            If cookie is disabled you\'ll see the site in the 
            original language.',
    ),
    'blockcurrencies'     => array(
        'category'      => GdprPro::COOKIE_CATEGORY_PREFERENCES,
        'enabled'       => 1,
        'provider'      => 'PrestaShop',
        'frontend_name' => 'Currency Selection',
        'expiry'        => '30 days',
        'description'   => 'Cookie which makes possible to choose the 
            currency you would like. If disabled the store default currency will be shown.',
    ),
);
