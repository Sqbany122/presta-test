<?php

/**
 * @package   gm_omniprice
 * @author    Dariusz Tryba (contact@greenmousestudio.com)
 * @copyright Copyright (c) Green Mouse Studio (http://www.greenmousestudio.com)
 * @license   http://greenmousestudio.com/paid-license.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Gm_OmniPrice extends Module {

    protected $ignoredGroups = [];
    protected $batchSize = 1000;
    protected $ignoreCountries = false;
    protected $ignoreCombinations = false;
    protected $reindexOnSave = false;
    protected $textColor = '';
    protected $priceColor = '';
    protected $backgroundColor = '';
    protected $daysBack = 30;
    protected $defaultShopId;
    protected $defaultCountryId;
    protected $defaultGroupId;
    protected $defaultCurrencyId;
    protected $today;
    protected $yesterday;
    protected $groupNames = [];

    public function __construct() {
        $this->name = 'gm_omniprice';
        $this->prefix = strtoupper($this->name);
        $this->tab = 'front_office_features';
        $this->version = '1.0.16';
        $this->author = 'GreenMouseStudio.com';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('OmniPrice - Omnibus Directive price compliancy');
        $this->description = $this->l('Displays lowest price before current promotion for discounted products');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->getConfiguration();
    }

    public function getConfiguration() {
        $this->ignoredGroups = explode(',', Configuration::get($this->prefix . '_GROUPS'));
        $this->daysBack = Configuration::get($this->prefix . '_DAYS');
        $this->batchSize = Configuration::get($this->prefix . '_BATCH');
        $this->ignoreCountries = Configuration::get($this->prefix . '_IGNORE_COUNTRIES');
        $this->ignoreCombinations = Configuration::get($this->prefix . '_IGNORE_COMBINATIONS');
        $this->reindexOnSave = Configuration::get($this->prefix . '_REINDEX');
        $this->textColor = Configuration::get($this->prefix . '_TEXT_COLOR');
        $this->priceColor = Configuration::get($this->prefix . '_PRICE_COLOR');
        $this->backgroundColor = Configuration::get($this->prefix . '_BG_COLOR');

        $this->defaultShopId = Configuration::get('PS_SHOP_DEFAULT');
        $this->defaultCountryId = Configuration::get('PS_COUNTRY_DEFAULT');
        $this->defaultGroupId = Configuration::get('PS_CUSTOMER_GROUP');
        $this->defaultCurrencyId = Configuration::get('PS_CURRENCY_DEFAULT');
        $this->today = date('Y-m-d');
        $this->yesterday = date('Y-m-d', strtotime("-1 days"));
    }

    public function install() {
        if (parent::install() && $this->installDb() &&
                $this->registerHook('displayProductPriceBlock') &&
                $this->registerHook('displayAdminProductsExtra') &&
                $this->registerHook('actionProductUpdate') &&
                $this->registerHook('actionObjectSpecificPriceAddAfter') &&
                $this->registerHook('actionObjectSpecificPriceUpdateAfter') &&
                $this->registerHook('actionObjectSpecificPriceDeleteAfter')
        ) {
            $guestGroupId = Configuration::get('PS_GUEST_GROUP');
            $unidentifiedGroupId = Configuration::get('PS_UNIDENTIFIED_GROUP');
            Configuration::updateValue($this->prefix . '_GROUPS', $unidentifiedGroupId . ',' . $guestGroupId);
            Configuration::updateValue($this->prefix . '_DAYS', 30);
            Configuration::updateValue($this->prefix . '_BATCH', 1000);
            Configuration::updateValue($this->prefix . '_IGNORE_COUNTRIES', true);
            Configuration::updateValue($this->prefix . '_REINDEX', true);
            Configuration::updateValue($this->prefix . '_TEXT_COLOR', '#FFFFFF');
            Configuration::updateValue($this->prefix . '_PRICE_COLOR', '#FFFFFF');
            Configuration::updateValue($this->prefix . '_BG_COLOR', '#666666');
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                $this->registerHook('displayHeader');
            } else {
                $this->registerHook('actionFrontControllerSetMedia');
            }
            return true;
        }
        return false;
    }

    public function installDb() {
        return Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gm_omniprice_history` (
                        `date` DATE NOT NULL,
			`id_shop` INT(10) UNSIGNED NOT NULL,
			`id_product` INT(10) UNSIGNED NOT NULL,
			`id_product_attribute` INT(10) UNSIGNED NOT NULL,
			`id_currency` INT(10) UNSIGNED NOT NULL,
			`id_country` INT(10) UNSIGNED NOT NULL,
			`id_group` INT(10) UNSIGNED NOT NULL,
			`price_tex` DECIMAL(20,6),
			`price_tin` DECIMAL(20,6),
                        `is_specific_price` TINYINT(1),
			INDEX (`id_shop`, `id_product`)
		) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;') &&
                Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gm_omniprice_cache` (
			`id_shop` INT(10) UNSIGNED NOT NULL,
			`id_product` INT(10) UNSIGNED NOT NULL,
			`id_product_attribute` INT(10) UNSIGNED NOT NULL,
			`id_currency` INT(10) UNSIGNED NOT NULL,
			`id_country` INT(10) UNSIGNED NOT NULL,
			`id_group` INT(10) UNSIGNED NOT NULL,
			`price_tex` DECIMAL(20,6),
			`price_tin` DECIMAL(20,6),
                        `date` DATE NOT NULL,
			INDEX (`id_shop`, `id_product`, `id_product_attribute`, `id_currency`, `id_country`, `id_group`)
		) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;') &&
                Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gm_omniprice_index` (
                        `date` DATE NOT NULL,
			`id_shop` INT(10) UNSIGNED NOT NULL,
			`id_product` INT(10) UNSIGNED NOT NULL,
                        INDEX (`date`, `id_shop`)
		) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');
    }

    public function uninstall() {
        if (!parent::uninstall()) {
            return false;
        }

        if (!$this->uninstallDB() ||
                !Configuration::deleteByName($this->prefix . '_GROUPS') ||
                !Configuration::deleteByName($this->prefix . '_DAYS') ||
                !Configuration::deleteByName($this->prefix . '_BATCH') ||
                !Configuration::deleteByName($this->prefix . '_BG_COLOR') ||
                !Configuration::deleteByName($this->prefix . '_TEXT_COLOR') ||
                !Configuration::deleteByName($this->prefix . '_PRICE_COLOR') ||
                !Configuration::deleteByName($this->prefix . '_IGNORE_COUNTRIES') ||
                !Configuration::deleteByName($this->prefix . '_IGNORE_COMBINATIONS') ||
                !Configuration::deleteByName($this->prefix . '_REINDEX')
        ) {
            return false;
        }
        return true;
    }

    protected function uninstallDb() {
        $res = Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'gm_omniprice_history`');
        $res &= Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'gm_omniprice_cache`');
        $res &= Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'gm_omniprice_index`');
        return $res;
    }

    public function getContent() {
        $content = '';
        $content .= $this->postProcess();
        $content .= $this->displayForm();
        $content .= $this->displayInfo();
        $content .= $this->displayInformationPanel();
        return $content;
    }

    protected function postProcess() {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $this->ignoredGroups = Tools::getValue('groupBox');
            $groupsString = implode(',', $this->ignoredGroups);
            Configuration::updateValue($this->prefix . '_GROUPS', $groupsString);

            $this->daysBack = Tools::getValue($this->prefix . '_DAYS');
            Configuration::updateValue($this->prefix . '_DAYS', $this->daysBack);

            $this->batchSize = Tools::getValue($this->prefix . '_BATCH');
            Configuration::updateValue($this->prefix . '_BATCH', $this->batchSize);

            $this->ignoreCountries = Tools::getValue($this->prefix . '_IGNORE_COUNTRIES');
            Configuration::updateValue($this->prefix . '_IGNORE_COUNTRIES', $this->ignoreCountries);

            $this->ignoreCombinations = Tools::getValue($this->prefix . '_IGNORE_COMBINATIONS');
            Configuration::updateValue($this->prefix . '_IGNORE_COMBINATIONS', $this->ignoreCombinations);

            $this->reindexOnSave = Tools::getValue($this->prefix . '_REINDEX');
            Configuration::updateValue($this->prefix . '_REINDEX', $this->reindexOnSave);

            $this->textColor = Tools::getValue($this->prefix . '_TEXT_COLOR');
            Configuration::updateValue($this->prefix . '_TEXT_COLOR', $this->textColor);

            $this->priceColor = Tools::getValue($this->prefix . '_PRICE_COLOR');
            Configuration::updateValue($this->prefix . '_PRICE_COLOR', $this->priceColor);

            $this->backgroundColor = Tools::getValue($this->prefix . '_BG_COLOR');
            Configuration::updateValue($this->prefix . '_BG_COLOR', $this->backgroundColor);

            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output;
    }

    public function displayForm() {
        $helper = new HelperForm();
        $groups = Group::getGroups($this->context->language->id);
        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Period'),
                        'desc' => $this->l('Number of days before promotion start to analyze'),
                        'name' => $this->prefix . '_DAYS',
                        'class' => 'fixed-width-md',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Ignore countries'),
                        'name' => $this->prefix . '_IGNORE_COUNTRIES',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'hint' => $this->l('Analyze prices only for the default country, customers from other countries will see prices of the default country'),
                        'desc' => $this->l('Analyze prices only for the default country, customers from other countries will see prices of the default country')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Ignore combinations'),
                        'name' => $this->prefix . '_IGNORE_COMBINATIONS',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'hint' => $this->l('Analyze prices only for the default combination, recommended if combinations don\'t have price impacts'),
                        'desc' => $this->l('Analyze prices only for the default combination, recommended if combinations don\'t have price impacts')
                    ),
                    array(
                        'type' => 'group',
                        'label' => $this->l('Ignored groups'),
                        'name' => 'groupBox',
                        'values' => $groups,
                        'hint' => $this->l('Ignore selected groups, customers from ignored groups will see prices for the default group (Customer), recommended if no group discounts in use'),
                        'desc' => $this->l('Ignore selected groups, customers from ignored groups will see prices for the default group (Customer), recommended if no group discounts in use')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Batch size'),
                        'desc' => $this->l('Number of products to process in a single CRON task run'),
                        'name' => $this->prefix . '_BATCH',
                        'class' => 'fixed-width-md',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Reindex on product save'),
                        'name' => $this->prefix . '_REINDEX',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'hint' => $this->l('Reindex product on save'),
                        'desc' => $this->l('Reindex product on save')
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Background color'),
                        'name' => $this->prefix . '_BG_COLOR',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Text color'),
                        'name' => $this->prefix . '_TEXT_COLOR',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Price color'),
                        'name' => $this->prefix . '_PRICE_COLOR',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->table = $this->table;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach ($groups as $group) {
            $helper->fields_value['groupBox_' . $group['id_group']] = in_array($group['id_group'], $this->ignoredGroups);
            $helper->fields_value[$this->prefix . '_DAYS'] = $this->daysBack;
            $helper->fields_value[$this->prefix . '_BATCH'] = $this->batchSize;
            $helper->fields_value[$this->prefix . '_IGNORE_COUNTRIES'] = $this->ignoreCountries;
            $helper->fields_value[$this->prefix . '_IGNORE_COMBINATIONS'] = $this->ignoreCombinations;
            $helper->fields_value[$this->prefix . '_REINDEX'] = $this->reindexOnSave;
            $helper->fields_value[$this->prefix . '_TEXT_COLOR'] = $this->textColor;
            $helper->fields_value[$this->prefix . '_PRICE_COLOR'] = $this->priceColor;
            $helper->fields_value[$this->prefix . '_BG_COLOR'] = $this->backgroundColor;
        }
        return $helper->generateForm(array($fieldsForm));
    }

    public function savePrices($verbose = false, $productId = null) {
        $this->clearIndex($this->yesterday);
        $output = '';
        $usetax = true;
        if (Tax::excludeTaxeOption()) {
            $usetax = false;
        }
        $basicPrices = [];
        $stateId = 0;
        $zipcode = '';

        $output .= $this->today . '<br/>';
        $output .= $this->l('Batch size') . ': ' . $this->batchSize . '<br/>';
        $output .= $this->l('Default shop ID:') . ' ' . $this->defaultShopId . '<br/>';
        $output .= $this->l('Default country ID:') . ' ' . $this->defaultCountryId . '<br/>';
        $output .= $this->l('Default group ID:') . ' ' . $this->defaultGroupId . '<br/>';
        $output .= $this->l('Default currency ID:') . ' ' . $this->defaultCurrencyId . '<br/>';

        $shopIds = $this->getShopsIds();
        $specificPriceOutput = null;
        foreach ($shopIds as $shopId) {
            $currencyIds = $this->getCurrencyIds($shopId);
            $countryIds = $this->getCountryIds($shopId);
            $groupIds = $this->getGroupIds($shopId);
            $attributesMap = $this->getProductAttributeMap($shopId);
            if (!$productId) {
                $productIds = $this->getProductIds($shopId);
            } else {
                $productIds = [$productId];
            }
            $output .= '<h4>' . $this->l('Shop ID:') . ' ' . $shopId . '</h4>';
            if (count($productIds) < 1) {
                $output .= '<p>' . $this->l('All products indexed') . '</p>';
                continue;
            }
            $output .= '<table border="1"><tr>'
                    . '<th></th>'
                    . '<th>' . $this->l('Product ID') . '</th>'
                    . '<th>' . $this->l('Attribute ID') . '</th>'
                    . '<th>' . $this->l('Country ID') . '</th>'
                    . '<th>' . $this->l('Currency ID') . '</th>'
                    . '<th>' . $this->l('Group ID') . '</th>'
                    . '<th>' . $this->l('Price') . '</th>'
                    . '<th>' . $this->l('Previous price') . '</th>'
                    . '<th>' . $this->l('Is discounted') . '</th>'
                    . '<th>' . $this->l('Action') . '</th>'
                    . '<th>' . $this->l('Lowest price') . '</th>'
                    . '</tr>';
            $counter = 0;
            foreach ($currencyIds as $currencyId) {
                foreach ($countryIds as $countryId) {
                    foreach ($groupIds as $groupId) {
                        $discountedIds = $this->getDiscountedProductIds($shopId, $currencyId, $countryId, $groupId);
                        foreach ($productIds as $productId) {
                            $attributeId = 0;
                            $basicKey = $shopId . '-' . $productId . '-' . $attributeId . '-' . $currencyId . '-' . $countryId . '-' . $groupId;
                            $priceTin = Product::priceCalculation(
                                            $shopId, $productId, $attributeId, $countryId, $stateId, $zipcode, $currencyId, $groupId, 1, //quantity
                                            $usetax, 6, //decimals
                                            false, //only_reduc
                                            true, //use_reduc
                                            true, //with_ecotax
                                            $specificPriceOutput, true //use_group_reduction
                            );
                            $priceTin = sprintf("%.6f", $priceTin);
                            $basicPrices[$basicKey] = $priceTin;
                            $priceTex = $priceTin;
                            if ($usetax) {
                                $priceTex = Product::priceCalculation(
                                                $shopId, $productId, $attributeId, $countryId, $stateId, $zipcode, $currencyId, $groupId, 1, //quantity
                                                false, //no tax
                                                6, //decimals
                                                false, //only_reduc
                                                true, //use_reduc
                                                true, //with_ecotax
                                                $specificPriceOutput, true //use_group_reduction
                                );
                            }
                            $previousPrice = (float) $this->getPreviousPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                            $onDiscount = $this->checkIfProductIsDiscounted($discountedIds, $productId, $attributeId);

                            $output .= '<tr>'
                                    . '<td>' . ++$counter . '</td>'
                                    . '<td>' . $productId . '</td>'
                                    . '<td>' . $attributeId . '</td>'
                                    . '<td>' . $countryId . '</td>'
                                    . '<td>' . $currencyId . '</td>'
                                    . '<td>' . $groupId . '</td>'
                                    . '<td>' . $priceTin . ' (' . $priceTex . ') </td>'
                                    . '<td>' . $previousPrice . '</td>'
                                    . '<td>' . ($onDiscount ? $this->l('Yes') : $this->l('No')) . '</td>';
                            if (abs($previousPrice - $priceTin) > 0.01) {
                                $output .= '<td>' . $this->l('Save') . '</td>';
                                $this->savePrice($this->today, $shopId, $productId, $currencyId, $countryId, $groupId, $attributeId, $priceTex,
                                        $priceTin, $onDiscount);
                                //calculate lowest price and add it to the cache
                                if ($onDiscount) {
                                    $lowestPrices = $this->getLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                                    if ($lowestPrices) {
                                        $output .= '<td>' . $lowestPrices['price_tin'] . ' (' . $lowestPrices['price_tex'] . ')</td>';
                                        $this->saveLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId,
                                                $lowestPrices['price_tex'], $lowestPrices['price_tin'], $lowestPrices['date']);
                                    } else {
                                        $output .= '<td>???</td>';
                                    }
                                } else {
                                    $output .= '<td>---</td>';
                                }
                            } else {
                                $output .= '<td>' . $this->l('No change') . '</td>';
                                $output .= '<td>' . $this->l('No change') . '</td>';
                            }
                            if (!$onDiscount) {
                                $this->deleteLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                            }
                            $output .= '</tr>';
                            //attributes
                            if (array_key_exists($productId, $attributesMap)) {
                                foreach ($attributesMap[$productId] as $attributeId) {
                                    $priceTin = Product::priceCalculation(
                                                    $shopId, $productId, $attributeId, $countryId, $stateId, $zipcode, $currencyId, $groupId, 1, //quantity
                                                    $usetax, 6, //decimals
                                                    false, //only_reduc
                                                    true, //use_reduc
                                                    true, //with_ecotax
                                                    $specificPriceOutput, true //use_group_reduction
                                    );
                                    $priceTin = sprintf("%.6f", $priceTin);
                                    $priceTex = $priceTin;
                                    if ($usetax) {
                                        $priceTex = Product::priceCalculation(
                                                        $shopId, $productId, $attributeId, $countryId, $stateId, $zipcode, $currencyId, $groupId,
                                                        1, //quantity
                                                        false, //no tax
                                                        6, //decimals
                                                        false, //only_reduc
                                                        true, //use_reduc
                                                        true, //with_ecotax
                                                        $specificPriceOutput, true //use_group_reduction
                                        );
                                    }
                                    if (abs($priceTin - $basicPrices[$basicKey]) > 0.01) {
                                        $previousPrice = (float) $this->getPreviousPrice($shopId, $productId, $currencyId, $countryId, $groupId,
                                                        $attributeId);
                                        $onDiscount = $this->checkIfProductIsDiscounted($discountedIds, $productId, $attributeId);
                                        $output .= '<tr>'
                                                . '<td>' . ++$counter . '</td>'
                                                . '<td>' . $productId . '</td>'
                                                . '<td>' . $attributeId . '</td>'
                                                . '<td>' . $countryId . '</td>'
                                                . '<td>' . $currencyId . '</td>'
                                                . '<td>' . $groupId . '</td>'
                                                . '<td>' . $priceTin . ' (' . $priceTex . ') </td>'
                                                . '<td>' . $previousPrice . '</td>'
                                                . '<td>' . ($onDiscount ? $this->l('Yes') : $this->l('No')) . '</td>';
                                        if (abs($previousPrice - $priceTin) > 0.01) {
                                            $output .= '<td>' . $this->l('Save') . '</td>';
                                            $this->savePrice($this->today, $shopId, $productId, $currencyId, $countryId, $groupId, $attributeId,
                                                    $priceTex, $priceTin, $onDiscount);
                                            if ($onDiscount) {
                                                $lowestPrices = $this->getLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId,
                                                        $attributeId);
                                                if ($lowestPrices) {
                                                    $output .= '<td>' . $lowestPrices['price_tin'] . ' (' . $lowestPrices['price_tex'] . ')</td>';
                                                    $this->saveLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId,
                                                            $lowestPrices['price_tex'], $lowestPrices['price_tin'], $lowestPrices['date']);
                                                } else {
                                                    $output .= '<td>???</td>';
                                                }
                                            } else {
                                                $output .= '<td>---</td>';
                                            }
                                        } else {
                                            $output .= '<td>' . $this->l('No change') . '</td>';
                                            $output .= '<td>' . $this->l('No change') . '</td>';
                                        }
                                        if (!$onDiscount) {
                                            $this->deleteLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                                        }
                                    } else {
                                        //skip attribute if price is the same as basic
                                    }
                                }
                            }
                            $this->addProductToIndex($shopId, $productId, $this->today);
                        }
                    }
                }
            }
            $output .= '</table>';
        }
        if ($verbose) {
            echo $output;
        }
        return true;
    }

    public function getLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId) {
        $lowestPriceTin = INF;
        $lowestPriceTex = INF;
        $lowestDate = '0000-00-00';
        for ($d = 1; $d <= $this->daysBack; $d++) {
            $date = date('Y-m-d', strtotime("-$d days"));
            $row = Db::getInstance()->getRow('SELECT `price_tin`, `price_tex` '
                    . ' FROM `' . _DB_PREFIX_ . 'gm_omniprice_history` '
                    . ' WHERE `id_shop` = ' . $shopId
                    . ' AND `id_product` = ' . $productId
                    . ' AND `id_product_attribute` = ' . $attributeId
                    . ' AND `id_currency` = ' . $currencyId
                    . ' AND `id_group` = ' . $groupId
                    . ' AND `id_country` = ' . $countryId
                    . ' AND `date` <= \'' . $date . '\''
                    . ' ORDER BY `date` DESC'
            );
            if ($attributeId != 0 && $row == false) {
                $attributeId = 0;
                $row = Db::getInstance()->getRow('SELECT `price_tin`, `price_tex` '
                        . ' FROM `' . _DB_PREFIX_ . 'gm_omniprice_history` '
                        . ' WHERE `id_shop` = ' . $shopId
                        . ' AND `id_product` = ' . $productId
                        . ' AND `id_product_attribute` = ' . $attributeId
                        . ' AND `id_currency` = ' . $currencyId
                        . ' AND `id_group` = ' . $groupId
                        . ' AND `id_country` = ' . $countryId
                        . ' AND `date` <= \'' . $date . '\''
                        . ' ORDER BY `date` DESC'
                );
            }
            if ($row) {
                $priceTin = $row['price_tin'];
                if ($priceTin < $lowestPriceTin) {
                    $lowestPriceTin = $priceTin;
                }
                $priceTex = $row['price_tex'];
                if ($priceTex < $lowestPriceTex) {
                    $lowestPriceTex = $priceTex;
                    $lowestDate = $date;
                }
            } else {
                break;
            }
        }
        if ($lowestPriceTex < INF) {
            return [
                'price_tin' => $lowestPriceTin,
                'price_tex' => $lowestPriceTex,
                'date' => $lowestDate
            ];
        } else {
            return false;
        }
    }

    public function checkIfProductIsDiscounted($discountedIds, $productId, $attributeId) {
        foreach ($discountedIds as $item) {
            if (($item['id_product'] == $productId) && ($item['id_product_attribute'] == $attributeId)) {
                return true;
            }
            if (($item['id_product'] == $productId) && ($item['id_product_attribute'] == 0)) {
                return true;
            }
        }
        return false;
    }

    public function clearIndex($date) {
        return Db::getInstance()->delete('gm_omniprice_index', '`date` <= \'' . $date . '\'');
    }

    public function addProductToIndex($shopId, $productId, $date) {
        Db::getInstance()->insert('gm_omniprice_index',
                [
                    'date' => $date,
                    'id_shop' => $shopId,
                    'id_product' => $productId
        ]);
    }

    public function getPreviousPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId) {
        return Db::getInstance()->getValue('SELECT `price_tin` FROM `' . _DB_PREFIX_ . 'gm_omniprice_history`'
                        . ' WHERE `id_shop` = ' . $shopId . ' AND `id_product` = ' . $productId
                        . ' AND `id_currency` = ' . $currencyId . ' AND `id_country` = ' . $countryId
                        . ' AND `id_group` = ' . $groupId . ' AND `id_product_attribute` = ' . $attributeId
                        . ' AND `date` < \'' . $this->today . '\''
                        . ' ORDER BY `date` DESC');
    }

    public function saveLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId, $priceTex, $priceTin, $date) {
        $this->deleteLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
        return Db::getInstance()->insert('gm_omniprice_cache',
                        [
                            'id_shop' => $shopId,
                            'id_product' => $productId,
                            'id_currency' => $currencyId,
                            'id_country' => $countryId,
                            'id_group' => $groupId,
                            'id_product_attribute' => $attributeId,
                            'price_tex' => $priceTex,
                            'price_tin' => $priceTin,
                            'date' => $date
        ]);
    }

    public function deleteLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId) {
        return Db::getInstance()->delete('gm_omniprice_cache',
                        '`id_shop` = ' . $shopId
                        . ' AND `id_product` = ' . $productId
                        . ' AND `id_currency` = ' . $currencyId
                        . ' AND `id_country` = ' . $countryId
                        . ' AND `id_group` = ' . $groupId
                        . ' AND `id_product_attribute` = ' . $attributeId
        );
    }

    public function savePrice($date, $shopId, $productId, $currencyId, $countryId, $groupId, $attributeId, $priceTex, $priceTin, $onDiscount = false) {
        Db::getInstance()->insert('gm_omniprice_history',
                [
                    'date' => $date,
                    'id_shop' => $shopId,
                    'id_product' => $productId,
                    'id_currency' => $currencyId,
                    'id_country' => $countryId,
                    'id_group' => $groupId,
                    'id_product_attribute' => $attributeId,
                    'price_tex' => $priceTex,
                    'price_tin' => $priceTin,
                    'is_specific_price' => $onDiscount
        ]);
    }

    public function getGroupIds($shopId) {
        $ids = [$this->defaultGroupId];
        if (!Group::isFeatureActive()) {
            return $ids;
        }
        $query = 'SELECT `gs`.`id_group`
                            FROM `' . _DB_PREFIX_ . 'group_shop` `gs`
                            WHERE `gs`.`id_shop` = ' . $shopId;
        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                if (($row['id_group'] != $this->defaultGroupId) && !in_array($row['id_group'], $this->ignoredGroups)) {
                    $ids[] = (int) $row['id_group'];
                }
            }
        }
        return $ids;
    }

    public function getCountryIds($shopId) {
        $ids = [$this->defaultCountryId];
        if (!$this->ignoreCountries) {
            $query = 'SELECT `cs`.`id_country`
                            FROM `' . _DB_PREFIX_ . 'country_shop` `cs`
                            LEFT JOIN `' . _DB_PREFIX_ . 'country` `c` ON (`cs`.`id_country` = `c`.`id_country`)
                            WHERE `cs`.`id_shop` = ' . $shopId
                    . ' AND `c`.`active` = 1';
            $res = Db::getInstance()->executeS($query);
            if ($res) {
                foreach ($res as $row) {
                    if ($row['id_country'] != $this->defaultCountryId) {
                        $ids[] = (int) $row['id_country'];
                    }
                }
            }
        }
        return $ids;
    }

    public function getCurrencyIds($shopId) {
        $ids = [$this->defaultCurrencyId];
        $query = 'SELECT `cs`.`id_currency`
                            FROM `' . _DB_PREFIX_ . 'currency` c
                            LEFT JOIN `' . _DB_PREFIX_ . 'currency_shop` cs ON (cs.`id_currency` = c.`id_currency`)
                            WHERE cs.`id_shop` = ' . (int) $shopId
                . ' AND c.`active` = 1';
        $currencies = Db::getInstance()->executeS($query);
        foreach ($currencies as $currency) {
            if ($currency['id_currency'] != $this->defaultCurrencyId) {
                $ids[] = (int) $currency['id_currency'];
            }
        }
        return $ids;
    }

    public function getProductIds($shopId) {
        $productIds = [];
        $query = 'SELECT `ps`.`id_product` '
                . ' FROM `' . _DB_PREFIX_ . 'product_shop` `ps`'
                . ' WHERE `ps`.`active` = 1 '
                . ' AND `ps`.`id_product` NOT IN '
                . ' (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'gm_omniprice_index`'
                . '  WHERE `id_shop` = ' . $shopId . ' AND `date` = \'' . $this->today . '\')'
                . ' AND `ps`.`id_shop` = ' . $shopId . ' LIMIT ' . $this->batchSize;
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $productIds[] = (int) $row['id_product'];
            }
        }
        return $productIds;
    }

    public function getProductAttributeMap($shopId) {
        $map = [];
        if (!$this->ignoreCombinations) {
            $query = 'SELECT `id_product`, `id_product_attribute` '
                    . ' FROM `' . _DB_PREFIX_ . 'product_attribute_shop` '
                    . ' WHERE `id_shop` = ' . $shopId;
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            if ($res) {
                foreach ($res as $row) {
                    $map[(int) $row['id_product']][] = (int) $row['id_product_attribute'];
                }
            }
        }
        return $map;
    }

    public function getShopsIds() {
        $list = [$this->defaultShopId];
        $sql = 'SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'shop`
                WHERE `active` = 1 AND `deleted` = 0
                ORDER BY FIELD(`id_shop`, ' . $this->defaultShopId . ')';
                
        foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $row) {
            if ($row['id_shop'] != $this->defaultShopId) {
                $list[] = (int) $row['id_shop'];
            }
        }
        return $list;
    }

    public function hookDisplayProductPriceBlock($hookParams) {
        if (($hookParams['type'] == 'after_price') && ((isset($hookParams['product']->id)) || Tools::isSubmit('id_product') )) {
            if (isset($hookParams['product']->id)) {
                $productId = (int) $hookParams['product']->id;
            } else {
                $productId = (int) Tools::getValue('id_product');
            }
            if (Tools::isSubmit('omnipricetest')) {
                $lowestCachedPrice = Tools::getValue('omnipricetest');
            } else {
                $params = $this->getCurrentParams($productId);
                $lowestCachedPrice = $this->getLowestCachedPrice($params);
            }
            if ($lowestCachedPrice) {
                $this->context->smarty->assign(
                        [
                            'gm_omniprice_lowest' => $lowestCachedPrice,
                            'gm_omniprice_days' => $this->daysBack,
                            'gm_omniprice_color' => $this->textColor,
                            'gm_omniprice_price_color' => $this->priceColor,
                            'gm_omniprice_background' => $this->backgroundColor
                        ]
                );
                return $this->display(__FILE__, 'price.tpl');
            }
        }
    }

    public function getLowestCachedPrice($params) {
        $displayMethod = Group::getPriceDisplayMethod($params['id_group']);
        if ($displayMethod) {
            $field = '`price_tex`';
        } else {
            $field = '`price_tin`';
        }
        $price = Db::getInstance()->getValue('SELECT  ' . $field
                . ' FROM `' . _DB_PREFIX_ . 'gm_omniprice_cache`'
                . ' WHERE `id_shop` = ' . $params['id_shop']
                . ' AND `id_product` = ' . $params['id_product']
                . ' AND `id_currency` = ' . $params['id_currency']
                . ' AND `id_country` = ' . $params['id_country']
                . ' AND `id_group` = ' . $params['id_group']
                . ' AND `id_product_attribute` = ' . $params['id_product_attribute']
        );
        if ($price) {
            return $this->getFormattedPrice($price);
        } else if ($params['id_product_attribute'] != 0) {
            $price = Db::getInstance()->getValue('SELECT  ' . $field
                    . ' FROM `' . _DB_PREFIX_ . 'gm_omniprice_cache`'
                    . ' WHERE `id_shop` = ' . $params['id_shop']
                    . ' AND `id_product` = ' . $params['id_product']
                    . ' AND `id_currency` = ' . $params['id_currency']
                    . ' AND `id_country` = ' . $params['id_country']
                    . ' AND `id_group` = ' . $params['id_group']
                    . ' AND `id_product_attribute` = 0'
            );
            if ($price) {
                return $this->getFormattedPrice($price);
            }
        }
        if (!$price) {
            //try to get the only one stored historical promo price - secret feature ;)
            return $this->getLatestHistoricalPrice($params);
        }
        return false;
    }

    protected function getLatestHistoricalPrice($params) {
        $displayMethod = Group::getPriceDisplayMethod($params['id_group']);
        if ($displayMethod) {
            $field = '`price_tex`';
            $arrayField = 'price_tex';
        } else {
            $field = '`price_tin`';
            $arrayField = 'price_tin';
        }
        $prices = Db::getInstance()->executeS('SELECT  ' . $field . ', `is_specific_price`'
                . ' FROM `' . _DB_PREFIX_ . 'gm_omniprice_history`'
                . ' WHERE `id_shop` = ' . $params['id_shop']
                . ' AND `id_product` = ' . $params['id_product']
                . ' AND `id_currency` = ' . $params['id_currency']
                . ' AND `id_country` = ' . $params['id_country']
                . ' AND `id_group` = ' . $params['id_group']
                . ' AND `id_product_attribute` = ' . $params['id_product_attribute']
        );
        if ((count($prices) == 1) && ($prices[0]['is_specific_price'])) {
            return $this->getFormattedPrice($prices[0][$arrayField]);
        } else if ($params['id_product_attribute'] != 0) {
            $prices = Db::getInstance()->executeS('SELECT  ' . $field . ', `is_specific_price`'
                    . ' FROM `' . _DB_PREFIX_ . 'gm_omniprice_history`'
                    . ' WHERE `id_shop` = ' . $params['id_shop']
                    . ' AND `id_product` = ' . $params['id_product']
                    . ' AND `id_currency` = ' . $params['id_currency']
                    . ' AND `id_country` = ' . $params['id_country']
                    . ' AND `id_group` = ' . $params['id_group']
                    . ' AND `id_product_attribute` = 0'
            );
            if ((count($prices) == 1) && ($prices[0]['is_specific_price'])) {
                return $this->getFormattedPrice($prices[0][$arrayField]);
            }
        }
        return false;
    }

    public function getLowestCachedPricesForCombinations($params) {
        $prices = [];
        $displayMethod = Group::getPriceDisplayMethod($params['id_group']);
        if ($displayMethod) {
            $field = '`price_tex`';
        } else {
            $field = '`price_tin`';
        }
        $result = Db::getInstance()->executeS('SELECT  ' . $field . ' AS `price`, `id_product_attribute` '
                . ' FROM `' . _DB_PREFIX_ . 'gm_omniprice_cache`'
                . ' WHERE `id_shop` = ' . $params['id_shop']
                . ' AND `id_product` = ' . $params['id_product']
                . ' AND `id_currency` = ' . $params['id_currency']
                . ' AND `id_country` = ' . $params['id_country']
                . ' AND `id_group` = ' . $params['id_group']
        );
        if ($result) {
            foreach ($result as $row) {
                $prices[$row['id_product_attribute']] = $this->getFormattedPrice($row['price']);
            }
        }
        return $prices;
    }

    public function getFormattedPrice($price) {
        $context = Context::getContext();
        if (isset($context->currentLocale)) {
            return $context->currentLocale->formatPrice($price, $context->currency->iso_code);
        } else {
            return Tools::displayPrice($price);
        }
    }

    public function getCurrentParams($productId) {
        $params = [];
        $params['id_shop'] = (int) $this->context->shop->id;
        $params['id_currency'] = (int) $this->context->currency->id;
        $params['id_product'] = (int) $productId;
        if ($this->ignoreCombinations) {
            $params['id_product_attribute'] = 0;
        } else {
            $params['id_product_attribute'] = $this->getIdProductAttribute($params['id_product']);
        }
        if ($this->ignoreCountries) {
            $params['id_country'] = $this->defaultCountryId;
        } else {
            $params['id_country'] = $this->context->country->id;
        }
        $currentGroup = $this->context->customer->id_default_group;
        if (in_array($currentGroup, $this->ignoredGroups)) {
            $params['id_group'] = $this->defaultGroupId;
        } else {
            $params['id_group'] = $currentGroup;
        }
        return $params;
    }

    public function getDiscountedProductIds($shopId, $currencyId, $countryId, $groupId) {
        if ($this->globalRuleExists($shopId, $currencyId, $countryId, $groupId)) {
            return $this->getAllProductIdsFromShop($shopId);
        }
        $ids = SpecificPrice::getProductIdByDate($shopId, $currencyId, $countryId, $groupId, null, null, 0, true);
        return $ids;
    }

    protected function getAllProductIdsFromShop($shopId) {
        $ids = [];
        $query = 'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_shop` WHERE `id_shop` = ' . $shopId;
        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $ids[] = [
                    'id_product' => (int) $row['id_product'],
                    'id_product_attribute' => 0
                ];
            }
        }
        return $ids;
    }

    protected function globalRuleExists($shopId, $currencyId, $countryId, $groupId) {
        $query = 'SELECT `id_specific_price` FROM `' . _DB_PREFIX_ . 'specific_price` '
                . ' WHERE (`id_shop` = 0 OR `id_shop` = ' . $shopId . ') '
                . ' AND (`id_currency` = 0 OR `id_currency` = ' . $currencyId . ') '
                . ' AND (`id_country` = 0 OR `id_country` = ' . $countryId . ') '
                . ' AND (`id_group` = 0 OR `id_group` = ' . $groupId . ') '
                . ' AND (`from` <= NOW() OR `from` = \'0000-00-00 00:00:00\') '
                . ' AND (`to` >= NOW() OR `to` = \'0000-00-00 00:00:00\' ) '
                . ' AND `id_product` = 0 '
                . ' AND `id_product_attribute` = 0 '
                . ' AND `from_quantity` > 0 ';
        $result = (int) Db::getInstance()->getValue($query);
        return ($result > 0);
    }

    public function getIdProductAttribute($productId) {
        $idProductAttribute = $this->getIdProductAttributeByGroup($productId);
        if (null === $idProductAttribute) {
            $idProductAttribute = (int) Tools::getValue('id_product_attribute');
        }
        return $idProductAttribute;
    }

    protected function getIdProductAttributeByGroup($productId) {
        $groups = Tools::getValue('group');
        if (empty($groups)) {
            return null;
        }
        return (int) Product::getIdProductAttributeByIdAttributes(
                        $productId, $groups, true
        );
    }

    public function hookActionFrontControllerSetMedia($params) {
        $this->context->controller->registerStylesheet(
                'module-gm_omniprice-style', 'modules/' . $this->name . '/views/css/gm_omniprice.css',
                [
                    'media' => 'all',
                    'priority' => 200,
                ]
        );
    }

    protected function displayInfo() {
        $token = Tools::getAdminToken($this->name);
        $output = '<div class="panel">'
                . '<div class="panel-heading"><i class="icon-link"></i> '
                . $this->l('Gathering price history')
                . '</div>';
        $output .= '<input type="text" size="90" value="' . Tools::getHttpHost(true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/cron.php?token=' . $token . '"/>';
        $output .= '</div>';
        $output .= '<div class="panel">'
                . '<div class="panel-heading"><i class="icon-link"></i> '
                . $this->l('Cleaning old price history')
                . '</div>';
        $output .= '<input type="text" size="90" value="' . Tools::getHttpHost(true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/cleanup.php?token=' . $token . '"/>';
        $output .= '</div>';
        return $output;
    }

    protected function displayInformationPanel() {
        $output = '<div class="panel">'
                . '<div class="panel-heading"><i class="icon-info"></i> '
                . $this->l('Information')
                . '</div>';
        if (!defined('_TB_VERSION_')) { //TB has a nasty bug here
            $output .= '<p>' . $this->l('Groups with no customers:') . ' ' . implode(', ', $this->findEmptyGroups()) . '</p>';
        }
        $output .= '<p>' . $this->l('Groups with group reductions:') . ' ' . implode(', ', $this->findGroupsWithGroupReduction()) . '</p>';
        $output .= '<p>' . $this->l('Groups with specific prices:') . ' ' . implode(', ', $this->findGroupsWithSpecificPrices()) . '</p>';
        $output .= '<p>' . $this->l('Groups with specific price rules:') . ' ' . implode(', ', $this->findGroupsWithSpecifiPriceRules()) . '</p>';
        $output .= '<p>' . $this->l('Products have combinations with price impacts:') . ' ' . $this->getPriceImpactsInfo() . '</p>';
        $output .= '</div>';
        return $output;
    }

    protected function getPriceImpactsInfo() {
        $query = 'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_attribute` WHERE `price` > 0';
        $res = Db::getInstance()->getValue($query);
        if ($res) {
            return $this->l('Yes');
        } else {
            return $this->l('No');
        }
    }

    protected function getGroupNames() {
        $langId = $this->context->language->id;
        $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'group_lang` WHERE `id_lang` = ' . $langId;
        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $this->groupNames[(int) $row['id_group']] = $row['name'];
            }
        }
    }

    protected function findEmptyGroups() {
        $emptyGroups = [];
        $res = Group::getGroups($this->context->language->id);
        foreach ($res as $row) {
            $group = new Group((int) $row['id_group']);
            $customerCount = $group->getCustomers(true);
            if ($customerCount < 1) {
                $emptyGroups[] = $row['name'];
            }
        }
        if (!count($emptyGroups)) {
            return [$this->l('None')];
        }
        return $emptyGroups;
    }

    protected function findGroupsWithSpecifiPriceRules() {
        $groupIds = [];
        $query = 'SELECT `id_group` FROM `' . _DB_PREFIX_ . 'specific_price_rule` WHERE `id_group` > 0';
        $res = Db::getInstance()->executes($query);
        if ($res) {
            foreach ($res as $row) {
                $groupIds[] = (int) $row['id_group'];
            }
        }
        $groupIds = array_unique($groupIds);
        sort($groupIds);
        return $this->getGroupNamesForIds($groupIds);
    }

    protected function findGroupsWithSpecificPrices() {
        $groupIds = [];
        $query = 'SELECT `id_group` FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `id_group` > 0';
        $res = Db::getInstance()->executes($query);
        if ($res) {
            foreach ($res as $row) {
                $groupIds[] = (int) $row['id_group'];
            }
        }
        $groupIds = array_unique($groupIds);
        sort($groupIds);
        return $this->getGroupNamesForIds($groupIds);
    }

    protected function findGroupsWithGroupReduction() {
        $groupIds = [];
        $query = 'SELECT `id_group` FROM `' . _DB_PREFIX_ . 'group` WHERE `reduction` > 0';
        $res = Db::getInstance()->executes($query);
        if ($res) {
            foreach ($res as $row) {
                $groupIds[] = (int) $row['id_group'];
            }
        }
        $query = 'SELECT `id_group` FROM `' . _DB_PREFIX_ . 'group_reduction` WHERE `reduction` > 0';
        $res = Db::getInstance()->executes($query);
        if ($res) {
            foreach ($res as $row) {
                $groupIds[] = (int) $row['id_group'];
            }
        }
        $groupIds = array_unique($groupIds);
        sort($groupIds);
        return $this->getGroupNamesForIds($groupIds);
    }

    protected function getGroupNamesForIds($groupIds) {
        if (!count($groupIds)) {
            return [$this->l('None')];
        }
        $names = [];
        $this->getGroupNames();
        foreach ($groupIds as $groupId) {
            $names[] = $this->groupNames[$groupId];
        }
        return $names;
    }

    public function hookActionProductUpdate($params) {
        $productId = $params['id_product'];
        $this->reindexProduct($productId);
    }

    public function hookActionObjectSpecificPriceAddAfter($params) {
        $sp = $params['object'];
        if ($sp->id_product) {
            $this->reindexProduct($sp->id_product);
        }
    }

    public function hookActionObjectSpecificPriceUpdateAfter($params) {
        $sp = $params['object'];
        if ($sp->id_product) {
            $this->reindexProduct($sp->id_product);
        }
    }

    public function hookActionObjectSpecificPriceDeleteAfter($params) {
        $sp = $params['object'];
        if ($sp->id_product) {
            $this->reindexProduct($sp->id_product);
        }
    }

    public function reindexProduct($productId) {
        $this->removeProductFromTodaysIndex($productId);
        $this->removeProductFromTodaysHistory($productId);
        if ($this->reindexOnSave) {
            $this->savePrices(false, $productId);
        }
    }

    public function removeProductFromTodaysIndex($productId) {
        Db::getInstance()->delete('gm_omniprice_index', '`id_product` = ' . $productId . ' AND `date` = \'' . $this->today . '\'');
    }

    public function resetIndex() {
        Db::getInstance()->execute('TRUNCATE `' . _DB_PREFIX_ . 'gm_omniprice_index`');
    }

    public function removeProductFromTodaysHistory($productId) {
        Db::getInstance()->delete('gm_omniprice_history', '`id_product` = ' . $productId . ' AND `date` = \'' . $this->today . '\'');
    }

    public function hookDisplayHeader($params) {
        if (Tools::isSubmit('id_product')) {
            $this->context->controller->addCSS($this->_path . 'views/css/gm_omniprice.css', 'all');
            if (!$this->ignoreCombinations) {
                $params = $this->getCurrentParams((int) Tools::getValue('id_product'));
                $prices = $this->getLowestCachedPricesForCombinations($params);
                if (count($prices) > 0) {
                    $this->context->controller->addJS($this->_path . 'views/js/gm_omniprice.js');
                    Media::addJsDef(['gm_omniprice_attr_prices' => $prices]);
                }
            }
        }
    }

    public function cleanUp($verbose = false) {
        $output = '';
        //general cleanup
        Db::getInstance()->delete('gm_omniprice_history', '`id_product` NOT IN (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product`)');
        Db::getInstance()->delete('gm_omniprice_history', '`id_shop` NOT IN (SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'shop`)');
        Db::getInstance()->delete('gm_omniprice_history', '`id_currency` NOT IN (SELECT `id_currency` FROM `' . _DB_PREFIX_ . 'currency`)');
        $date = date("Y-m-d", strtotime("-" . $this->daysBack . " days"));
        $output .= $this->l('Period') . ': ' . $this->daysBack . ' (' . $date . ')<br/>';
        $shopIds = $this->getShopsIds();
        foreach ($shopIds as $shopId) {
            $currencyIds = $this->getCurrencyIds($shopId);
            $countryIds = $this->getCountryIds($shopId);
            $groupIds = $this->getGroupIds($shopId);
            foreach ($currencyIds as $currencyId) {
                foreach ($countryIds as $countryId) {
                    foreach ($groupIds as $groupId) {
                        $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'gm_omniprice_history` '
                                . ' WHERE `id_shop` = ' . $shopId . ' AND `id_currency` = ' . $currencyId .
                                ' AND `id_country` = ' . $countryId . ' AND `id_group` = ' . $groupId . ' ORDER BY `date` ASC';
                        $res = Db::getInstance()->executeS($query);
                        $datesMap = [];
                        if ($res) {
                            foreach ($res as $row) {
                                $day = $row['date'];
                                $productId = $row['id_product'];
                                $attributeId = $row['id_product_attribute'];
                                if ($day < $date) {
                                    $datesMap[$productId][$attributeId][] = $day;
                                }
                            }
                            //$output .= var_export($datesMap, true);
                            foreach ($datesMap as $productId => $dateItem) {
                                foreach ($dateItem as $attributeId => $dates) {
                                    $output .= "Product ID {$productId}, attribute ID: {$attributeId}<br/>";
                                    $datesCount = count($dates);
                                    if ($datesCount > 1) {
                                        for ($i = 0; $i < $datesCount - 1; $i++) {
                                            $output .= ' ' . $dates[$i] . ' X<br/>';
                                            $where = '`id_shop` = ' . $shopId . ' AND `id_currency` = ' . $currencyId .
                                                    ' AND `id_country` = ' . $countryId . ' AND `id_group` = ' . $groupId;
                                            $where .= ' AND `id_product` = ' . $productId . ' AND `id_product_attribute` = ' . $attributeId;
                                            $where .= ' AND `date` = \'' . $dates[$i] . '\'';
                                            Db::getInstance()->delete('gm_omniprice_history', $where);
                                        }
                                    }
                                    $output .= ' ' . $dates[$datesCount - 1] . ' OK<br/>';
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($verbose) {
            echo '<pre>';
            echo $output;
        }
    }

    public function hookDisplayAdminProductsExtra(array $params) {
        $data = [];
        if (isset($params['id_product'])) {
            $productId = (int) $params['id_product'];
        } else {
            $productId = (int) Tools::getValue('id_product');
        }
        $shopId = (int) $this->context->shop->id;
        $currencyId = (int) $this->defaultCurrencyId;
        $countryId = (int) $this->defaultCountryId;
        $groupId = (int) $this->defaultGroupId;
        $attributeId = 0;

        $query = 'SELECT * '
                . ' FROM `' . _DB_PREFIX_ . 'gm_omniprice_history`'
                . ' WHERE `id_shop` = ' . $shopId
                . ' AND `id_product` = ' . $productId
                . ' AND `id_product_attribute` = ' . $attributeId
                . ' AND `id_currency` = ' . $currencyId
                . ' AND `id_country` = ' . $countryId
                . ' AND `id_group` = ' . $groupId
                . ' ORDER BY `date` DESC';

        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $data[$row['date']] = [
                    'date' => $row['date'],
                    'price_tin' => $row['price_tin'],
                    'is_specific_price' => $row['is_specific_price']
                ];
            }
        }

        $query = 'SELECT * '
                . ' FROM `' . _DB_PREFIX_ . 'gm_omniprice_cache`'
                . ' WHERE `id_shop` = ' . $shopId
                . ' AND `id_product` = ' . $productId
                . ' AND `id_product_attribute` = ' . $attributeId
                . ' AND `id_currency` = ' . $currencyId
                . ' AND `id_country` = ' . $countryId
                . ' AND `id_group` = ' . $groupId
                . ' ORDER BY `date` DESC';

        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                if (!array_key_exists($row['date'], $data)) {
                    $data[$row['date']] = [
                        'date' => $row['date'],
                        'price_tin' => $row['price_tin'],
                        'is_specific_price' => ''
                    ];
                }
            }
        }
        krsort($data);
        $indexed = (int) Db::getInstance()->getValue('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'gm_omniprice_index` WHERE `id_product` = ' . $productId .
                        ' AND `date` = \'' . $this->today . '\'');
        $this->context->smarty->assign(array(
            'historyData' => $data,
            'indexedToday' => $indexed
        ));
        return $this->display(__FILE__, 'tab.tpl');
    }

}
