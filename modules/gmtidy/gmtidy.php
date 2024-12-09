<?php

/**
 * Helps you keep your store neat and clean
 *
 * @package   gmtidy
 * @author    Dariusz Tryba (contact@greenmousestudio.com)
 * @copyright Copyright (c) GreenMouseStudio (http://www.greenmousestudio.com)
 * @license   http://greenmousestudio.com/paid-license.txt
 */
if (!defined('_PS_VERSION_'))
    exit;

class GMTidy extends Module {

    protected $token;
    protected $days;
    protected $parentCategories = [];

    public function __construct() {
        $this->name = 'gmtidy';
        $this->tab = 'administration';
        $this->version = '1.4.3';
        $this->author = 'GreenMouseStudio.com';
        $this->module_key = '';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Tidy');
        $this->description = $this->l('Helps you tidy up your shop');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->token = Tools::getAdminToken('gmtidy');
        $this->days = (int) Configuration::get('GMTIDY_DAYS');
    }

    public function install() {
        if (parent::install()) {
            Configuration::updateValue('GMTIDY_DAYS', 30);
            return true;
        }
        return false;
    }

    public function getContent() {
        $content = $this->postProcess();
        $content .= $this->displayStatsPanel();
        $content .= '<div class="panel"><h3>' . $this->l('Cleanup') . '</h3>';
        $content .= $this->displayButtonPanel('delete-abandoned-carts', $this->l('Delete old abandoned carts'));
        $content .= $this->displayButtonPanel('delete-connections', $this->l('Delete old connections stats'));
        $content .= $this->displayButtonPanel('delete-search-stats', $this->l('Delete old search stats'));
        $content .= $this->displayButtonPanel('delete-email-logs', $this->l('Delete old email logs'));
        $content .= $this->displayButtonPanel('delete-logs', $this->l('Delete old logs'));
        $content .= $this->displayButtonPanel('delete-guests', $this->l('Delete old guests with no addresses'));
        $content .= $this->displayButtonPanel('delete-customers', $this->l('Delete old customers with no addresses'));
        $content .= $this->displayButtonPanel('delete-guests-orders', $this->l('Delete old guests with no orders'));
        $content .= $this->displayButtonPanel('delete-customers-orders', $this->l('Delete old customers with no orders'));
        $content .= $this->displayButtonPanel('delete-customer-threads', $this->l('Delete old customer threads'));
        $content .= $this->displayButtonPanel('delete-specific-prices', $this->l('Delete expired specific prices'));
        $content .= $this->displayButtonPanel('delete-vouchers', $this->l('Delete expired cart rules'));
        $content .= $this->displayButtonPanel('delete-feature-values', $this->l('Delete unused feature values'));
        $content .= $this->displayButtonPanel('delete-features', $this->l('Delete empty features'));
        $content .= $this->displayButtonPanel('clear-cache', $this->l('Clear all cache'));
        $content .= '</div>';

        $content .= '<div class="panel"><h3>' . $this->l('Data consistency') . '</h3>';
        $content .= $this->displayButtonPanel('regenerate-product-urls',
                $this->l('Regenerate friendly URL\'s for products'));
        $content .= $this->displayButtonPanel('regenerate-category-urls',
                $this->l('Regenerate friendly URL\'s for categories'));
        $content .= $this->displayButtonPanel('cheapest-comb', $this->l('Set cheapest combinations as default'));
        $content .= $this->displayButtonPanel('tax-groups',
                $this->l('Assign most common tax group to products with no tax group'));
        $content .= '</div>';

        $content .= '<div class="panel"><h3>' . $this->l('Fix associations') . '</h3>';
        $content .= $this->displayButtonPanel('cat-assign', $this->l('Set product\'s deepest category as default'));
        $content .= $this->displayButtonPanel('cat-parents', $this->l('Assign products to all parent categories'));
        $content .= $this->displayButtonPanel('cat-groups', $this->l('Assign all customer groups to all categories'));
        $content .= '</div>';

        $content .= '<div class="panel"><h3>' . $this->l('Fix images') . '</h3>';
        $content .= $this->displayButtonPanel('fix-covers',
                $this->l('Set first image as cover for products without cover'));
        $content .= $this->displayButtonPanel('cover-first', $this->l('Set cover as first image'));
        $content .= $this->displayButtonPanel('img-assoc', $this->l('Associate all images to all shops'));
        $content .= $this->displayButtonPanel('delete-tmp-img', $this->l('Delete temporary images'));
        $content .= $this->displayButtonPanel('delete-broken-images', $this->l('Delete broken images'));
        $content .= $this->displayButtonPanel('delete-unused-images', $this->l('Delete unused image files'));
        $content .= '</div>';

        $content .= '<div class="panel"><h3>' . $this->l('Mass activation and deactivation') . '</h3>';
        $content .= $this->displayButtonPanel('disable-oos',
                $this->l('Deactivate active out of stock products'));
        $content .= $this->displayButtonPanel('enable-is',
                $this->l('Activate inactive in stock products'));
        $content .= $this->displayButtonPanel('cat-deact',
                $this->l('Deactivate active categories without active products'));
        $content .= $this->displayButtonPanel('cat-activate',
                $this->l('Activate inactive categories with active products'));
        $content .= $this->displayButtonPanel('prod-deact',
                $this->l('Deactivate active products without active categories'));
        $content .= $this->displayButtonPanel('man-deact',
                $this->l('Deactivate active manufacturers without active products'));
        $content .= $this->displayButtonPanel('man-activate',
                $this->l('Activate inactive manufacturers with active products'));
        $content .= '</div>';

        return $content . $this->displayForm() .
                $this->context->smarty->fetch($this->local_path . 'views/templates/admin/gms.tpl');
    }

    protected function displayStatsPanel() {

        $this->context->smarty->assign(array(
            'abandonedCarts' => number_format($this->countAbandonedCarts()),
            'connectionsStats' => number_format($this->countConnections()),
            'searchStats' => number_format($this->countSearchStats()),
            'emailLogs' => number_format($this->countEmailLogs()),
            'logs' => number_format($this->countLogs()),
            'expiredSpecificPrices' => number_format($this->countExpiredSpecificPrices()),
            'expiredVouchers' => number_format($this->countExpiredVouchers()),
            'guestsWithoutAddresses' => number_format($this->countGuestsWithNoAddresses()),
            'customersWithoutAddresses' => number_format($this->countCustomersWithNoAddresses()),
            'guestsWithoutOrders' => number_format($this->countGuestsWithNoOrders()),
            'customersWithoutOrders' => number_format($this->countCustomersWithNoOrders()),
            'unusedFeatureValues' => number_format($this->countUnusedFeatureValues()),
            'emptyFeatures' => number_format($this->countEmptyFeatures()),
            'productsWithNoTaxGroup' => number_format($this->countProductsWithNoTaxGroups()),
            'customerThreads' => number_format($this->countCustomerThreads()),
        ));
        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/stats.tpl');
    }

    protected function displayForm() {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Interval in days'),
                        'name' => 'GMTIDY_DAYS',
                        'hint' => $this->l('Used for deleting old data'),
                        'class' => 'fixed-width-xs',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigFieldsValues() {
        return array(
            'GMTIDY_DAYS' => (int) Configuration::get('GMTIDY_DAYS'),
        );
    }

    protected function displayButtonPanel($name, $caption) {
        $content = '<div class="panel">';
        $content .= '<form method="post" action="">';
        $content .= '<div class="form-group">';
        $content .= '<button type="submit" name="submit-' . $name . '" class="btn btn-default">'
                . '<i class="icon-check"></i> ' . $caption . '</button>';
        $content .= '</form>';
        $content .= '</div>';
        $url = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/cron-' . $name . '.php?token=' . $this->token;
        $content .= '<p><a class="btn btn-default" target="_blank" href="' . $url . '&preview">' . $this->l('Preview results of this operation without executing it') . '</a></p>';
        $content .= '<p>' . $this->l('Cron URL:') . ' ' . $url . '</p>';
        $content .= '</div>';
        return $content;
    }

    protected function postProcess() {
        $result = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $nbr = (int) Tools::getValue('GMTIDY_DAYS');
            if ($nbr < 0 || $nbr > 1000) {
                $nbr = 1000;
            }
            Configuration::updateValue('GMTIDY_DAYS', $nbr);
            $result .= $this->displayConfirmation($this->l('Settings updated'));
        }
        if (Tools::isSubmit('submit-man-deact')) {
            if ($this->deactivateManufacturersWithoutActiveProducts() != false) {
                $result .= $this->displayConfirmation($this->l('Deactivate active manufacturers without active products') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-man-activate')) {
            if ($this->activateManufacturersWithActiveProducts() != false) {
                $result .= $this->displayConfirmation($this->l('Activate inactive manufacturers with active products') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-cat-deact')) {
            if ($this->deactivateCategoriesWithoutActiveProducts() != false) {
                $result .= $this->displayConfirmation($this->l('Deactivate active categories without active products') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-prod-deact')) {
            if ($this->deactivateProductsWithoutActiveCategories() != false) {
                $result .= $this->displayConfirmation($this->l('Deactivate active products without active categories') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-cat-groups')) {
            if ($this->assingAllUserGroupsToAllProductCategories() != false) {
                $result .= $this->displayConfirmation($this->l('Assign all customer groups to all categories') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-cat-activate')) {
            if ($this->activateCategoriesWithActiveProducts() != false) {
                $result .= $this->displayConfirmation($this->l('Activate inactive categories with active products') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-cat-assign')) {
            if ($this->assignProductsDeepestCategoryAsDefault() != false) {
                $result .= $this->displayConfirmation($this->l('Set product\'s deepest category as default') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-cat-parents')) {
            if ($this->assignProductsToAllParentCategories() != false) {
                $result .= $this->displayConfirmation($this->l('Assign products to all parent categories') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-regenerate-product-urls')) {
            if ($this->regenerateProductUrls() != false) {
                $result .= $this->displayConfirmation($this->l('Regenerate friendly URL\'s for products') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-regenerate-category-urls')) {
            if ($this->regenerateCategoryUrls() != false) {
                $result .= $this->displayConfirmation($this->l('Regenerate friendly URL\'s for categories') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-abandoned-carts')) {
            if ($this->deleteOldAbandonedShoppingCarts() != false) {
                $result .= $this->displayConfirmation($this->l('Delete old abandoned carts') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-connections')) {
            if ($this->deleteOldConnectionStats() != false) {
                $result .= $this->displayConfirmation($this->l('Delete old connections stats') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-search-stats')) {
            if ($this->deleteOldSearchStats() != false) {
                $result .= $this->displayConfirmation($this->l('Delete old search stats') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-specific-prices')) {
            if ($this->deleteExpiredSpecificPrices() != false) {
                $result .= $this->displayConfirmation($this->l('Delete expired specific prices') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-vouchers')) {
            if ($this->deleteExpiredVouchers() != false) {
                $result .= $this->displayConfirmation($this->l('Delete expired vouchers') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-email-logs')) {
            if ($this->deleteOldEmailLogs() != false) {
                $result .= $this->displayConfirmation($this->l('Delete old email logs') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-logs')) {
            if ($this->deleteOldLogs() != false) {
                $result .= $this->displayConfirmation($this->l('Delete old logs') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-customer-threads')) {
            if ($this->deleteOldCustomerThreads() != false) {
                $result .= $this->displayConfirmation($this->l('Delete old customer threads') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-fix-covers')) {
            if ($this->fixCovers() != false) {
                $result .= $this->displayConfirmation($this->l('Set first image as cover for products without cover') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-cover-first')) {
            if ($this->coverFirst() != false) {
                $result .= $this->displayConfirmation($this->l('Set cover as first image') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-img-assoc')) {
            if ($this->associateImages() != false) {
                $result .= $this->displayConfirmation($this->l('Associate all images to all shops') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-tmp-img')) {
            if ($this->deleteTemporaryImages() != false) {
                $result .= $this->displayConfirmation($this->l('Delete temporary images') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-broken-images')) {
            if ($this->deleteBrokenImages() != false) {
                $result .= $this->displayConfirmation($this->l('Delete broken images') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-unused-images')) {
            if ($this->deleteUnusedImages() != false) {
                $result .= $this->displayConfirmation($this->l('Delete unused image files') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-cheapest-comb')) {
            if ($this->setCheapestCombinationsAsDefault() != false) {
                $result .= $this->displayConfirmation($this->l('Set cheapest combinations as default') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-clear-cache')) {
            if ($this->setCheapestCombinationsAsDefault() != false) {
                $result .= $this->displayConfirmation($this->l('Clear all cache') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-guests')) {
            if ($this->deleteOldGuestsWithNoAddresses() != false) {
                $result .= $this->displayConfirmation($this->l('Delete old guests with no addresses') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-guests-orders')) {
            if ($this->deleteOldGuestsWithNoOrders() != false) {
                $result .= $this->displayConfirmation($this->l('Delete old guests with no orders') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-customers-orders')) {
            if ($this->deleteOldCustomersWithNoOrders() != false) {
                $result .= $this->displayConfirmation($this->l('Delete old customers with no orders') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-feature-values')) {
            if ($this->deleteUnusedFeatureValues() != false) {
                $result .= $this->displayConfirmation($this->l('Delete unused feature values') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-delete-features')) {
            if ($this->deleteEmptyFeatures() != false) {
                $result .= $this->displayConfirmation($this->l('Delete empty features') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-disable-oos')) {
            if ($this->disableOutOfStockProducts() != false) {
                $result .= $this->displayConfirmation($this->l('Deactivate active out of stock products') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-enable-is')) {
            if ($this->enableInStockProducts() != false) {
                $result .= $this->displayConfirmation($this->l('Activate inactive in stock products') . ' - ' . $this->l('Request processed'));
            }
        }
        if (Tools::isSubmit('submit-tax-groups')) {
            if ($this->assignMostCommonTaxGroupToProductsWithNoTaxGroup() != false) {
                $result .= $this->displayConfirmation($this->l('Assign most common tax group to products with no tax group') . ' - ' . $this->l('Request processed'));
            }
        }
        return $result;
    }

    public function assignMostCommonTaxGroupToProductsWithNoTaxGroup($verbose = false, $preview = false) {
        $output = '';
        $result = true;
        //find most common global tax group ID
        $query = 'SELECT `id_tax_rules_group` FROM `' . _DB_PREFIX_ . 'product` '
                . ' WHERE `id_tax_rules_group` > 0 '
                . ' GROUP BY `id_tax_rules_group` '
                . ' ORDER BY COUNT(`id_tax_rules_group`) DESC ';
        $mostCommonGlobalTaxRulesGroup = Db::getInstance()->getValue($query);
        if ($mostCommonGlobalTaxRulesGroup > 0) {
            $output .= $this->l('Most common global tax rules group ID') . ': ' . $mostCommonGlobalTaxRulesGroup . '<br/>';
            if (!$preview) {
                $result &= Db::getInstance()->update('product',
                        ['id_tax_rules_group' => $mostCommonGlobalTaxRulesGroup], '`id_tax_rules_group` = 0');
            }
        }
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $shopId = $shop['id_shop'];
            $query = 'SELECT `id_tax_rules_group` FROM `' . _DB_PREFIX_ . 'product_shop` '
                    . ' WHERE `id_tax_rules_group` > 0 AND `id_shop` = ' . $shopId
                    . ' GROUP BY `id_tax_rules_group` '
                    . ' ORDER BY COUNT(`id_tax_rules_group`) DESC ';
            $mostCommonShopTaxRulesGroup = Db::getInstance()->getValue($query);
            if ($mostCommonShopTaxRulesGroup > 0) {
                $output .= $this->l('Most common tax rules group ID for shop') . ' ' . $shopId . ': ' . $mostCommonShopTaxRulesGroup . '<br/>';
                if (!$preview) {
                    $result &= Db::getInstance()->update('product_shop',
                            ['id_tax_rules_group' => $mostCommonShopTaxRulesGroup],
                            '`id_tax_rules_group` = 0 AND `id_shop` = ' . $shopId);
                }
            }
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Assign most common tax group to products with no tax group'));
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Assign most common tax group to products with no tax group');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $output;
        }
        return $result;
    }

    public function deleteOldGuestsWithNoAddresses($verbose = false, $preview = false) {
        $output = '';
        $query = 'SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'customer` '
                . ' WHERE `is_guest` = 1 AND date_upd <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY) AND '
                . ' `id_customer` NOT IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'address`)';
        $res = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($res) {
            foreach ($res as $row) {
                $counter++;
                $customerId = $row['id_customer'];
                $customer = new Customer($customerId);
                if ($verbose) {
                    $output .= $this->l('Guest:') . ' ' . $customer->id . ' - ' . $customer->firstname . ' ' . $customer->lastname . ', ' . $customer->email . '<br/>';
                }
                if (!$preview) {
                    $customer->delete();
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old guests with no addresses');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Old guests with no addresses found and deleted: ') . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old guests with no addresses') . ' (' . $counter . ')');
        }
        return true;
    }

    public function deleteOldGuestsWithNoOrders($verbose = false, $preview = false) {
        $output = '';
        $query = 'SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'customer` '
                . ' WHERE `is_guest` = 1 AND date_upd <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY) AND '
                . ' `id_customer` NOT IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'orders`)';
        $res = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($res) {
            foreach ($res as $row) {
                $counter++;
                $customerId = $row['id_customer'];
                $customer = new Customer($customerId);
                if ($verbose) {
                    $output .= $this->l('Guest:') . ' ' . $customer->id . ' - ' . $customer->firstname . ' ' . $customer->lastname . ', ' . $customer->email . '<br/>';
                }
                if (!$preview) {
                    $customer->delete();
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old guests with no orders');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Old guests with no orders found and deleted: ') . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old guests with no orders') . ' (' . $counter . ')');
        }
        return true;
    }

    public function deleteOldCustomersWithNoAddresses($verbose = false, $preview = false) {
        $output = '';
        $query = 'SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'customer` '
                . ' WHERE `is_guest` = 0 AND date_upd <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY) AND '
                . ' `id_customer` NOT IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'address`)';
        $res = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($res) {
            foreach ($res as $row) {
                $counter++;
                $customerId = $row['id_customer'];
                $customer = new Customer($customerId);
                if ($verbose) {
                    $output .= $this->l('Customer:') . ' ' . $customer->id . ' - ' . $customer->firstname . ' ' . $customer->lastname . ', ' . $customer->email . '<br/>';
                }
                if (!$preview) {
                    $customer->delete();
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old customers with no addresses');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Old customers with no addresses found and deleted: ') . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old customers with no addresses') . ' (' . $counter . ')');
        }
        return true;
    }

    public function deleteOldCustomersWithNoOrders($verbose = false, $preview = false) {
        $output = '';
        $query = 'SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'customer` '
                . ' WHERE `is_guest` = 0 AND date_upd <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY) AND '
                . ' `id_customer` NOT IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'orders`)';
        $res = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($res) {
            foreach ($res as $row) {
                $counter++;
                $customerId = $row['id_customer'];
                $customer = new Customer($customerId);
                if ($verbose) {
                    $output .= $this->l('Customer:') . ' ' . $customer->id . ' - ' . $customer->firstname . ' ' . $customer->lastname . ', ' . $customer->email . '<br/>';
                }
                if (!$preview) {
                    $customer->delete();
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old customers with no orders');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Old customers with no orders found and deleted: ') . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old customers with no orders') . ' (' . $counter . ')');
        }
        return true;
    }

    public function deleteTemporaryImages($verbose = false, $preview = false) {
        if (!$preview) {
            array_map('unlink', glob(_PS_TMP_IMG_DIR_ . "/*"));
            array_map('unlink', glob(_PS_TMP_IMG_DIR_ . "/cms/*"));
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete temporary images'));
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete temporary images');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
        }
        return true;
    }

    public function associateImages($verbose = false, $preview = false) {
        $output = '';
        $shops = Shop::getShops();
        $counter = 0;
        foreach ($shops as $shop) {
            $shopId = $shop['id_shop'];
            $output .= '--- ' . $this->l('Shop ID:') . ' ' . $shopId . ' ---<br/>';
            $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'image` WHERE `id_product` IN (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_shop` WHERE `id_shop` = ' . $shopId . ') '
                    . ' AND `id_image` NOT IN (SELECT `id_image` FROM `' . _DB_PREFIX_ . 'image_shop` WHERE `id_shop` = ' . $shopId . ')';
            $res = Db::getInstance()->executeS($query);
            if ($res) {
                foreach ($res as $row) {
                    $counter++;
                    $output .= $this->l('Prod. ID:') . ' ' . $row['id_product'] . ', ' . $this->l('Image ID:') . ' ' . $row['id_image'] . '<br/>';
                    if (!$preview) {
                        if ((int) $row['cover']) {
                            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'image_shop` SET `cover` = null '
                                    . 'WHERE `id_product` = ' . $row['id_product'] . ' AND `id_shop` = ' . $shopId);
                            Db::getInstance()->insert('image_shop',
                                    [
                                        'id_image' => $row['id_image'],
                                        'id_product' => $row['id_product'],
                                        'cover' => $row['cover'],
                                        'id_shop' => $shopId
                            ]);
                        } else {
                            Db::getInstance()->insert('image_shop',
                                    [
                                        'id_image' => $row['id_image'],
                                        'id_product' => $row['id_product'],
                                        'id_shop' => $shopId
                            ]);
                        }
                    }
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Associate all images to all shops');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Unassociated images found:') . ' ' . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Associate all images to all shops') . ' (' . $counter . ')');
        }
        return true;
    }

    public function coverFirst($verbose = false, $preview = false) {
        $query = 'SELECT `id_image` FROM `' . _DB_PREFIX_ . 'image` WHERE `cover` > 0 AND `position` > 1';
        $result = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($result) {
            foreach ($result as $row) {
                $counter++;
                $imageId = $row['id_image'];
                if (!$preview) {
                    $img = new Image($imageId);
                    $img->updatePosition(0, 1);
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Set cover as first image');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Number of affected products:') . ' ' . $counter . '<br/>';
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Set cover as first image') . ' (' . $counter . ')');
        }
        return true;
    }

    public function deleteExpiredSpecificPrices($verbose = false, $preview = false) {
        $counter = 0;
        if (!$preview) {
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `id_specific_price_rule` = 0 AND'
                    . ' `to` > 0 AND `to` < NOW()';
            if (Db::getInstance()->execute($query)) {
                $counter = Db::getInstance()->Affected_Rows();
            }
            $query = 'OPTIMIZE TABLE `' . _DB_PREFIX_ . 'specific_price`';
            Db::getInstance()->execute($query);
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete expired specific prices') . ' (' . $counter . ')');
        } else {
            $counter = $this->countExpiredSpecificPrices();
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete expired specific prices');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Expired specific prices found and deleted:') . ' ' . $counter . '<br/>';
        }
        return true;
    }

    protected function countExpiredSpecificPrices() {
        $query = 'SELECT COUNT(`id_specific_price`) FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `id_specific_price_rule` = 0 AND'
                . ' `to` > 0 AND `to` < NOW()';
        return Db::getInstance()->getValue($query);
    }

    public function deleteExpiredVouchers($verbose = false, $preview = false) {
        $output = '';
        $counter = 0;
        $query = 'SELECT `id_cart_rule`, `code` FROM `' . _DB_PREFIX_ . 'cart_rule` '
                . ' WHERE `date_to` > 0 AND `date_to` < NOW()';
        $result = Db::getInstance()->executeS($query);
        if ($result) {
            foreach ($result as $row) {
                $counter++;
                if ($verbose) {
                    $output .= $this->l('Cart rule:') . ' ' . $row['id_cart_rule'] . ' (' . $row['code'] . ')<br/>';
                }
                if (!$preview) {
                    $cartRuleId = (int) $row['id_cart_rule'];
                    $cartRule = new CartRule($cartRuleId);
                    $cartRule->delete();
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete expired cart rules');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Expired cart rules found and deleted: ') . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete expired cart rules') . ' (' . $counter . ')');
        }
        return true;
    }

    protected function countExpiredVouchers() {
        $query = 'SELECT COUNT(`id_cart_rule`) FROM `' . _DB_PREFIX_ . 'cart_rule` '
                . ' WHERE `date_to` > 0 AND `date_to` < NOW()';
        return Db::getInstance()->getValue($query);
    }

    public function fixCovers($verbose = false, $preview = false) {
        //set covers for products without covers
        $output = '';
        $query = 'SELECT `id_product`, MIN(`id_image`) as `id_image`, SUM(`cover`) as `sum` FROM `' . _DB_PREFIX_ . 'image` GROUP BY `id_product`';
        $result = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($result) {
            foreach ($result as $row) {
                $productId = $row['id_product'];
                $imageId = $row['id_image'];
                $sum = (int) $row['sum'];
                if ($sum < 1) {
                    $counter++;
                    $output .= $this->l('Prod. ID:') . ' ' . $productId . '<br/>';
                    if (!$preview) {
                        Image::deleteCover($productId);
                        $img = new Image($imageId);
                        $img->cover = 1;
                        $img->update();
                    }
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Set first image as cover for products without cover');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Products without cover found and fixed: ') . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Set first image as cover for products without cover') . ' (' . $counter . ')');
        }
        return true;
    }

    public function deleteBrokenImages($verbose = false, $preview = false) {
        //delete images from the database, for which the image files don't exist
        $allImages = Image::getAllImages();
        $output = '';
        $counter = 0;
        foreach ($allImages as $row) {
            $imageId = $row['id_image'];
            $imgFolder = _PS_PROD_IMG_DIR_ . Image::getImgFolderStatic($imageId);
            $jpgPath = $imgFolder . $imageId . '.jpg';
            $jpegPath = $imgFolder . $imageId . '.jpeg';
            $pngPath = $imgFolder . $imageId . '.png';
            if (file_exists($jpgPath) || file_exists($jpegPath) || file_exists($pngPath)) {
                //do nothing
                //$output .= $this->l(' - existst') . '<br/>';
            } else {
                $counter++;
                $output .= $imageId . ' - ' . $this->l('not found, deleting') . '<br/>';
                if (!$preview) {
                    $image = new Image($imageId);
                    Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'image_shop` WHERE `id_image` = ' . $imageId);
                    $image->delete();
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete broken images');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Broken images found and deleted: ') . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete broken images') . ' (' . $counter . ')');
        }
        return true;
    }

    public function deleteUnusedImages($verbose = false, $subpath = null, $preview = false) {
        $allImages = Image::getAllImages();
        $imagesTypes = ImageType::getImagesTypes();
        $typeNames = ['watermark'];
        foreach ($imagesTypes as $type) {
            $typeNames[] = $type['name'];
        }
        $imageIds = array();
        $output = '';
        foreach ($allImages as $row) {
            $imageId = (int) $row['id_image'];
            $imageIds[] = $imageId;
        }
        if ($subpath) {
            $imagesDir = _PS_PROD_IMG_DIR_ . DIRECTORY_SEPARATOR . $subpath;
        } else {
            $imagesDir = _PS_PROD_IMG_DIR_;
        }
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($imagesDir));
        $counter = 0;
        foreach ($rii as $file) {
            if (!$file->isDir()) {
                $path = $file->getPathname();
                $imageId = $this->getImageIdFromPath($path);
                if ($imageId && !in_array($imageId, $imageIds)) {
                    $counter++;
                    $output .= $path . ' - ' . $this->l('unused') . '<br/>';
                    if (!$preview) {
                        unlink($path);
                    }
                }
                $imageType = rtrim($this->getImageTypeFromPath($path), '2x');
                if ($imageId && strlen($imageType) && !in_array($imageType, $typeNames)) {
                    $counter++;
                    $output .= $path . ' - ' . $this->l('unused type') . '<br/>';
                    if (!$preview) {
                        unlink($path);
                    }
                }
            }
        }
        if (!$preview) {
            self::removeEmptySubFolders($imagesDir);
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete unused image files');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Unused images found and deleted:') . ' ' . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete unused image files') . ' (' . $counter . ')');
        }
        return true;
    }

    public static function removeEmptySubFolders($path) {
        $empty = true;
        foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $file) {
            $empty &= is_dir($file) && self::removeEmptySubFolders($file);
        }
        return $empty && rmdir($path);
    }

    protected function getImageTypeFromPath($path) {
        $pathInfo = pathinfo($path);
        $fileName = $pathInfo['filename'];
        $parts = explode('-', $fileName);
        $name = '';
        if (is_array($parts) && (count($parts) > 1)) {
            $name = $parts[1];
        }
        return $name;
    }

    protected function getImageIdFromPath($path) {
        $pathInfo = pathinfo($path);
        $dirs = substr($pathInfo['dirname'], strlen(_PS_PROD_IMG_DIR_));
        $name = '';
        if (strlen($dirs) > 0) {
            $name = trim(str_replace('/', '', $dirs));
        }
        return $name;
    }

    public function deleteOldEmailLogs($verbose = false, $preview = false) {
        $counter = 0;
        if (!$preview) {
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'mail` '
                    . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            if (Db::getInstance()->execute($query)) {
                $counter = Db::getInstance()->Affected_Rows();
            }
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old email logs') . ' (' . $counter . ')');
        } else {
            $counter = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'mail` '
                            . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)');
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old email logs');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Old email logs found and deleted: ') . $counter . '<br/>';
        }
        return true;
    }

    public function deleteUnusedFeatureValues($verbose = false, $preview = false) {
        $langId = Configuration::get('PS_LANG_DEFAULT');
        $output = '';
        $query = 'SELECT `id_feature_value` FROM `' . _DB_PREFIX_ . 'feature_value` '
                . ' WHERE `id_feature_value` NOT IN (SELECT DISTINCT `id_feature_value` FROM `' . _DB_PREFIX_ . 'feature_product`)';
        $res = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($res) {
            $counter++;
            foreach ($res as $row) {
                $fv = new FeatureValue($row['id_feature_value']);
                $output .= $this->l('Unused feature value:') . ' ' . $row['id_feature_value'] . ' (' . $fv->value[$langId] . ')<br/>';
                if (!$preview) {
                    $fv->delete();
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete unused feature values');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete unused feature values') . ' (' . $counter . ')');
        }
        return true;
    }

    public function deleteEmptyFeatures($verbose = false, $preview = false) {
        $langId = Configuration::get('PS_LANG_DEFAULT');
        $output = '';
        $query = 'SELECT `id_feature` FROM `' . _DB_PREFIX_ . 'feature` '
                . ' WHERE `id_feature` NOT IN (SELECT DISTINCT `id_feature` FROM `' . _DB_PREFIX_ . 'feature_value`)';
        $res = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($res) {
            foreach ($res as $row) {
                $counter++;
                $f = new Feature($row['id_feature']);
                $output .= $this->l('Empty feature:') . ' ' . $row['id_feature'] . ' (' . $f->name[$langId] . ')<br/>';
                if (!$preview) {
                    $f->delete();
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete empty features');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete empty features') . ' (' . $counter . ')');
        }
        return true;
    }

    protected function countGuestsWithNoAddresses() {
        $query = 'SELECT COUNT(`id_customer`) FROM `' . _DB_PREFIX_ . 'customer` '
                . ' WHERE `is_guest` = 1 AND '
                . ' `id_customer` NOT IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'address`)';
        return Db::getInstance()->getValue($query);
    }

    protected function countCustomersWithNoAddresses() {
        $query = 'SELECT COUNT(`id_customer`) FROM `' . _DB_PREFIX_ . 'customer` '
                . ' WHERE `is_guest` = 0 AND '
                . ' `id_customer` NOT IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'address`)';
        return Db::getInstance()->getValue($query);
    }

    protected function countGuestsWithNoOrders() {
        $query = 'SELECT COUNT(`id_customer`) FROM `' . _DB_PREFIX_ . 'customer` '
                . ' WHERE `is_guest` = 1 AND '
                . ' `id_customer` NOT IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'orders`)';
        return Db::getInstance()->getValue($query);
    }

    protected function countCustomersWithNoOrders() {
        $query = 'SELECT COUNT(`id_customer`) FROM `' . _DB_PREFIX_ . 'customer` '
                . ' WHERE `is_guest` = 0 AND '
                . ' `id_customer` NOT IN (SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'orders`)';
        return Db::getInstance()->getValue($query);
    }

    protected function countEmailLogs() {
        $query = 'SELECT COUNT(`id_mail`) FROM `' . _DB_PREFIX_ . 'mail` ';
        return Db::getInstance()->getValue($query);
    }

    public function deleteOldLogs($verbose = false, $preview = false) {
        $counter = 0;
        if (!$preview) {
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'log` '
                    . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            if (Db::getInstance()->execute($query)) {
                $counter = Db::getInstance()->Affected_Rows();
            }
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old logs') . ' (' . $counter . ')');
        } else {
            $counter = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'log` '
                            . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)');
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old logs');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Old logs found and deleted: ') . $counter . '<br/>';
        }
        return true;
    }

    public function deleteOldCustomerThreads($verbose = false, $preview = false) {
        $output = '';
        $query = 'SELECT `id_customer_thread`, `email` FROM `' . _DB_PREFIX_ . 'customer_thread` '
                . ' WHERE `date_upd` <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
        $res = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($res) {
            foreach ($res as $row) {
                $counter++;
                if ($verbose) {
                    $output .= $this->l('Thread:') . ' ' . $row['id_customer_thread'] . ' (' . $row['email'] . ')<br/>';
                }
                if (!$preview) {
                    $thread = new CustomerThread($row['id_customer_thread']);
                    $thread->delete();
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old customer threads');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Old customers threads found and deleted: ') . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old customer threads') . ' (' . $counter . ')');
        }
        return true;
    }

    protected function countLogs() {
        $query = 'SELECT COUNT(`id_log`) FROM `' . _DB_PREFIX_ . 'log` ';
        return Db::getInstance()->getValue($query);
    }

    public function deleteOldSearchStats($verbose = false, $preview = false) {
        $counter = 0;
        if (!$preview) {
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'statssearch` '
                    . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            if (Db::getInstance()->execute($query)) {
                $counter = Db::getInstance()->Affected_Rows();
            }
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old search stats') . ' (' . $counter . ')');
        } else {
            $counter = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'statssearch` '
                            . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)');
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old search stats');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Search stats found and deleted: ') . $counter . '<br/>';
        }
        return true;
    }

    protected function countSearchStats() {
        $query = 'SELECT COUNT(`id_statssearch`) FROM `' . _DB_PREFIX_ . 'statssearch` ';
        return Db::getInstance()->getValue($query);
    }

    protected function countUnusedFeatureValues() {
        $query = 'SELECT COUNT(`id_feature_value`) FROM `' . _DB_PREFIX_ . 'feature_value` '
                . ' WHERE `id_feature_value` NOT IN (SELECT DISTINCT `id_feature_value` FROM `' . _DB_PREFIX_ . 'feature_product`)';
        return Db::getInstance()->getValue($query);
    }

    protected function countEmptyFeatures() {
        $query = 'SELECT COUNT(`id_feature`) FROM `' . _DB_PREFIX_ . 'feature` '
                . ' WHERE `id_feature` NOT IN (SELECT DISTINCT `id_feature` FROM `' . _DB_PREFIX_ . 'feature_value`)';
        return Db::getInstance()->getValue($query);
    }

    protected function countProductsWithNoTaxGroups() {
        $query = 'SELECT COUNT(`id_product`) FROM `' . _DB_PREFIX_ . 'product` WHERE `id_tax_rules_group` = 0';
        return Db::getInstance()->getValue($query);
    }

    protected function countCustomerThreads() {
        $query = 'SELECT COUNT(`id_customer_thread`) FROM `' . _DB_PREFIX_ . 'customer_thread`';
        return Db::getInstance()->getValue($query);
    }

    public function deleteOldConnectionStats($verbose = false, $preview = false) {
        $counter = 0;
        if (!$preview) {
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'connections_page`
			WHERE time_start <= LAST_DAY(DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY))';
            if (Db::getInstance()->execute($query)) {
                $counter += Db::getInstance()->Affected_Rows();
            }
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'connections` '
                    . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            if (Db::getInstance()->execute($query)) {
                $counter += Db::getInstance()->Affected_Rows();
            }
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'guest` WHERE `id_guest` NOT IN '
                    . ' (SELECT `id_guest` FROM `' . _DB_PREFIX_ . 'connections`) '
                    . ' AND `id_customer` = 0';
            if (Db::getInstance()->execute($query)) {
                $counter += Db::getInstance()->Affected_Rows();
            }
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'pagenotfound` '
                    . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            if (Db::getInstance()->execute($query)) {
                $counter += Db::getInstance()->Affected_Rows();
            }
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'connections_source` '
                    . ' WHERE `date_add` <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            if (Db::getInstance()->execute($query)) {
                $counter += Db::getInstance()->Affected_Rows();
            }
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'date_range` '
                    . ' WHERE time_start <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            if (Db::getInstance()->execute($query)) {
                $counter += Db::getInstance()->Affected_Rows();
            }
            $query = 'DELETE FROM `' . _DB_PREFIX_ . 'page_viewed` '
                    . ' WHERE `id_date_range` NOT IN (SELECT `dr`.`id_date_range` FROM `' . _DB_PREFIX_ . 'date_range` `dr`)';
            if (Db::getInstance()->execute($query)) {
                $counter += Db::getInstance()->Affected_Rows();
            }
            $query = 'OPTIMIZE TABLE `' . _DB_PREFIX_ . 'connections_page`,'
                    . '`' . _DB_PREFIX_ . 'connections`, '
                    . '`' . _DB_PREFIX_ . 'guest`, '
                    . '`' . _DB_PREFIX_ . 'pagenotfound`, '
                    . '`' . _DB_PREFIX_ . 'connections_source`, '
                    . '`' . _DB_PREFIX_ . 'date_range`, '
                    . '`' . _DB_PREFIX_ . 'page_viewed`;';
            Db::getInstance()->execute($query);
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old connections stats') . ' (' . $counter . ')');
        } else {
            $query = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'connections_page`
			WHERE time_start <= LAST_DAY(DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY))';
            $counter += Db::getInstance()->getValue($query);
            $query = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'connections` '
                    . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            $counter += Db::getInstance()->getValue($query);
            $query = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'guest` WHERE `id_guest` NOT IN '
                    . ' (SELECT `id_guest` FROM `' . _DB_PREFIX_ . 'connections`) '
                    . ' AND `id_customer` = 0';
            $counter += Db::getInstance()->getValue($query);
            $query = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'pagenotfound` '
                    . ' WHERE date_add <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            $counter += Db::getInstance()->getValue($query);
            $query = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'connections_source` '
                    . ' WHERE `date_add` <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            $counter += Db::getInstance()->getValue($query);
            $query = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'date_range` '
                    . ' WHERE time_start <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)';
            $counter += Db::getInstance()->getValue($query);
            $query = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'page_viewed` '
                    . ' WHERE `id_date_range` NOT IN (SELECT `dr`.`id_date_range` FROM `' . _DB_PREFIX_ . 'date_range` `dr`)';
            $counter += Db::getInstance()->getValue($query);
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old connections stats');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Old connections stats found and deleted: ') . $counter . '<br/>';
        }
        return true;
    }

    protected function countConnections() {
        $result = 0;
        $result += Db::getInstance()->getValue('SELECT COUNT(*) AS `count` FROM `' . _DB_PREFIX_ . 'connections`');
        $result += Db::getInstance()->getValue('SELECT COUNT(*) AS `count` FROM `' . _DB_PREFIX_ . 'connections_page`');
        $result += Db::getInstance()->getValue('SELECT COUNT(*) AS `count` FROM `' . _DB_PREFIX_ . 'guest`');
        $result += Db::getInstance()->getValue('SELECT COUNT(*) AS `count` FROM `' . _DB_PREFIX_ . 'pagenotfound`');
        $result += Db::getInstance()->getValue('SELECT COUNT(*) AS `count` FROM `' . _DB_PREFIX_ . 'connections_source`');
        $result += Db::getInstance()->getValue('SELECT COUNT(*) AS `count` FROM `' . _DB_PREFIX_ . 'date_range`');
        $result += Db::getInstance()->getValue('SELECT COUNT(*) AS `count` FROM `' . _DB_PREFIX_ . 'page_viewed`');
        return $result;
    }

    public function deleteOldAbandonedShoppingCarts($verbose = false, $preview = false) {
        $output = '';
        $query = 'SELECT `c`.`id_cart`, `c`.`date_upd` FROM `' . _DB_PREFIX_ . 'cart` `c` '
                . ' WHERE  `c`.`date_upd` <= DATE_SUB(NOW(), INTERVAL ' . $this->days . ' DAY)'
                . ' AND `c`.`id_cart` NOT IN (SELECT `o`.`id_cart` FROM `' . _DB_PREFIX_ . 'orders` `o`)';
        $result = Db::getInstance()->executeS($query);
        $counter = 0;
        if ($result) {
            foreach ($result as $row) {
                $counter++;
                $output .= $this->l('Cart ID:') . ' ' . $row['id_cart'] . '; ' . $this->l('Update date:') . ' ' . $row['date_upd'] . '<br/>';
                if (!$preview) {
                    $cart = new Cart($row['id_cart']);
                    $cart->delete();
                }
            }
        }
        if (!$preview) {
            $query2 = 'DELETE g FROM `' . _DB_PREFIX_ . 'guest` as g
                    LEFT JOIN `' . _DB_PREFIX_ . 'cart` as c ON g.id_guest = c.id_guest
                    WHERE id_cart IS NULL';
            Db::getInstance()->execute($query2);
            $counter = Db::getInstance()->Affected_Rows();
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Delete old abandoned carts') . '(' . $counter . ')');
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Delete old abandoned carts');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Abandoned carts found and deleted: ') . $counter . '<br/>';
            echo $output;
        }
        return true;
    }

    protected function countAbandonedCarts() {
        $query = 'SELECT COUNT(`c`.`id_cart`) AS `count` FROM `' . _DB_PREFIX_ . 'cart` `c` '
                . ' WHERE `c`.`id_cart` NOT IN (SELECT `o`.`id_cart` FROM `' . _DB_PREFIX_ . 'orders` `o`)';
        $result = Db::getInstance()->getValue($query);
        return $result;
    }

    public function regenerateProductUrls($verbose = false, $preview = false) {
        $output = '';
        $output .= '<table border="1">';
        $output .= '<tr><th>' . $this->l('ID prod.') . '</th><th>' . $this->l('ID shop') . '</th><th>' . $this->l('ID lang') . '</th>'
                . '<th>' . $this->l('Name') . '</th><th>' . $this->l('Link rewrite') . '</th><th>' . $this->l('New link rewrite') . '</th></tr>';
        $query = 'SELECT `id_product`, `name`, `id_shop`, `id_lang`, `link_rewrite` FROM ' . _DB_PREFIX_ . 'product_lang';
        $result = Db::getInstance()->executeS($query);
        if ($result) {
            $counter = 0;
            foreach ($result as $product) {
                $newLink = Tools::link_rewrite($product['name']);
                $newLink = str_replace(['&'], '', $newLink);
                if (strcmp($newLink, $product['link_rewrite']) !== 0) {
                    $counter++;
                    $output .= '<tr><td>' . $product['id_product'] . '</td><td>' . $product['id_shop'] . '</td>'
                            . '<td>' . $product['id_lang'] . '</td><td>' . $product['name'] . '</td>'
                            . '<td>' . $product['link_rewrite'] . '</td><td>' . $newLink . '</td></tr>';
                    if (!$preview) {
                        Db::getInstance()->update('product_lang', array('link_rewrite' => $newLink),
                                '`id_product` = ' . $product['id_product'] . ' AND '
                                . '`id_shop` = ' . $product['id_shop'] . ' AND '
                                . '`id_lang` = ' . $product['id_lang']);
                    }
                }
            }
            if (!$preview) {
                PrestaShopLogger::addLog('Tidy - ' . $this->l('Regenerate friendly URL\'s for products') . ' (' . $counter . ')');
            }
            $output .= '</table>';
            if ($verbose) {
                echo '<h3>' . $this->l('Regenerate friendly URL\'s for products');
                if ($preview) {
                    echo ' (' . $this->l('preview') . ')';
                }
                echo '</h3>';
                echo $this->l('Friendly URL\'s regenerated:') . ' ' . $counter . '<br/>';
                echo $output;
            }
        }
        return true;
    }

    public function regenerateCategoryUrls($verbose = false, $preview = false) {
        $output = '';
        $output .= '<table border="1">';
        $output .= '<tr><th>' . $this->l('ID cat.') . '</th><th>' . $this->l('ID shop') . '</th><th>' . $this->l('ID lang') . '</th>'
                . '<th>' . $this->l('Name') . '</th><th>' . $this->l('Link rewrite') . '</th><th>' . $this->l('New link rewrite') . '</th></tr>';
        $query = 'SELECT `id_category`, `name`, `id_shop`, `id_lang`, `link_rewrite` FROM ' . _DB_PREFIX_ . 'category_lang';
        $result = Db::getInstance()->executeS($query);
        if ($result) {
            $counter = 0;
            foreach ($result as $category) {
                $newLink = Tools::link_rewrite($category['name']);
                $newLink = str_replace('&', '', $newLink);
                if (strcmp($newLink, $category['link_rewrite']) !== 0) {
                    $counter++;
                    $output .= '<tr><td>' . $category['id_category'] . '</td><td>' . $category['id_shop'] . '</td>'
                            . '<td>' . $category['id_lang'] . '</td><td>' . $category['name'] . '</td>'
                            . '<td>' . $category['link_rewrite'] . '</td><td>' . $newLink . '</td></tr>';
                    if (!$preview) {
                        Db::getInstance()->update('category_lang', array('link_rewrite' => $newLink),
                                'id_category = ' . $category['id_category'] . ' AND '
                                . '`id_shop` = ' . $category['id_shop'] . ' AND '
                                . '`id_lang` = ' . $category['id_lang']);
                    }
                }
            }
            if (!$preview) {
                PrestaShopLogger::addLog('Tidy - ' . $this->l('Regenerate friendly URL\'s for categories') . ' (' . $counter . ')');
            }
            $output .= '</table>';
            if ($verbose) {
                echo '<h3>' . $this->l('Regenerate friendly URL\'s for categories');
                if ($preview) {
                    echo ' (' . $this->l('preview') . ')';
                }
                echo '</h3>';
                echo $this->l('Friendly URL\'s regenerated:') . ' ' . $counter . '<br/>';
                echo $output;
            }
        }
        return true;
    }

    public function assignProductsDeepestCategoryAsDefault($verbose = false, $preview = false) {
        $output = '';
        $data = $this->getDefaultCategories();
        $defaultMap = $this->getDefaultCategoriesMap();
        if ($data) {
            foreach ($data as $productId => $categoryId) {
                $output .= $this->l('Prod. ID:') . ' ' . $productId . ' - ' . $this->l('Cat. ID:') . ' ' . $categoryId;
                $currentDefaultCategoryId = $defaultMap[$productId];
                if ($categoryId != $currentDefaultCategoryId) {
                    $query = 'UPDATE `' . _DB_PREFIX_ . 'product` SET `id_category_default` = ' . $categoryId . ' WHERE `id_product` = ' . $productId . '; ';
                    $query .= 'UPDATE `' . _DB_PREFIX_ . 'product_shop` SET `id_category_default` = ' . $categoryId . ' WHERE `id_product` = ' . $productId . ' ';
                    if (!$preview) {
                        Db::getInstance()->execute($query);
                    }
                    $output .= ' - ' . $this->l('updated') . '<br/>';
                } else {
                    $output .= ' - ' . $this->l('no changes') . '<br/>';
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Set product\'s deepest category as default');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Set product\'s deepest category as default'));
        }
        return true;
    }

    public function getDefaultCategoriesMap() {
        $map = [];
        $query = 'SELECT `id_product`, `id_category_default` FROM `' . _DB_PREFIX_ . 'product`';
        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $map[(int) $row['id_product']] = (int) $row['id_category_default'];
            }
        }
        return $map;
    }

    public function assingAllUserGroupsToAllProductCategories($verbose = false, $preview = false) {
        if (!$preview) {
            $langId = Configuration::get('PS_LANG_DEFAULT');
            $shops = Shop::getShops();
            foreach ($shops as $shop) {
                $shopId = $shop['id_shop'];
                $groups = Group::getGroups($langId, $shopId);
                $groupIds = array();
                foreach ($groups as $group) {
                    $groupIds[] = $group['id_group'];
                }
                $categories = Category::getSimpleCategories($langId);
                foreach ($categories as $category) {
                    $id = $category['id_category'];
                    $c = new Category($id);
                    if ($c->getShopID() == $shopId) {
                        $c->updateGroup($groupIds);
                    }
                }
            }
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Assign all customer groups to all categories'));
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Assign all customer groups to all categories');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
        }
        return true;
    }

    public function assignProductsToAllParentCategories($verbose = false, $preview = false) {
        if ($verbose) {
            echo '<h3>' . $this->l('Assign products to all parent categories');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
        }
        $items = Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` ORDER BY `id_product` DESC');
        foreach ($items as $item) {
            $product = new Product($item['id_product']);
            $categories = Product::getProductCategories($item['id_product']);
            $categoriesToAdd = array();
            foreach ($categories as $category) {
                $parentCategories = $this->getParentCategories((int) $category);
                foreach ($parentCategories as $parentCategory) {
                    if (($parentCategory['level_depth'] > 1) && ($parentCategory['is_root_category'] == 0)) {
                        $categoriesToAdd[] = $parentCategory['id_category'];
                    }
                }
            }
            $categoriesToAdd = array_unique($categoriesToAdd);
            if (!$preview) {
                $product->addToCategories($categoriesToAdd);
            }
            if ($verbose) {
                echo $this->l('Prod. ID:') . ' ' . $item['id_product'] . ' - ' . implode(', ', $categoriesToAdd) . '<br/>';
            }
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Assign products to all parent categories'));
        }
        return true;
    }

    protected function getParentCategories($categoryId) {
        if (!array_key_exists($categoryId, $this->parentCategories)) {
            $interval = Category::getInterval($categoryId);
            $sql = new DbQuery();
            $sql->from('category', 'c');
            $sql->where('c.nleft <= ' . (int) $interval['nleft'] . ' AND c.nright >= ' . (int) $interval['nright']);
            $sql->orderBy('c.nleft');
            $this->parentCategories[$categoryId] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
        return $this->parentCategories[$categoryId];
    }

    protected function getDefaultCategories() {
        $query = 'SELECT `p`.`id_product` AS `id`, `cp`.`id_category`, `c`.`level_depth` `level_depth` FROM `' . _DB_PREFIX_ . 'product` `p` '
                . ' JOIN `' . _DB_PREFIX_ . 'category_product` `cp` ON `p`.`id_product` = `cp`.`id_product` '
                . ' JOIN `' . _DB_PREFIX_ . 'category` `c` ON `cp`.`id_category` = `c`.`id_category` '
                . ' WHERE `c`.active = 1 '
                . ' ORDER BY `id` ASC, `level_depth` DESC';
        $result = Db::getInstance()->executeS($query);
        $data = array();
        foreach ($result as $row) {
            $data[$row['id']][] = array(
                'id_category' => $row['id_category'],
                'level_depth' => $row['level_depth']);
        }
        $deepestCategories = array();
        foreach ($data as $productId => $items) {
            $maxLevel = $items[0]['level_depth'];
            $deepestCategories[$productId] = $items[0]['id_category'];
            foreach ($items as $item) {
                if ($item['level_depth'] > $maxLevel) {
                    $maxLevel = $item['level_depth'];
                    $deepestCategories[$productId] = $item['id_category'];
                }
            }
        }
        return $deepestCategories;
    }

    protected function getProductIds() {
        $query = 'SELECT `id_product` AS `id` FROM `' . _DB_PREFIX_ . 'product` ';
        echo $query;
        $result = Db::getInstance()->executeS($query);
        $ids = array();
        foreach ($result as $row) {
            $ids[] = $row['id'];
        }
        return $ids;
    }

    public function deactivateCategoriesWithoutActiveProducts($verbose = false, $preview = false) {
        $categories = implode(',', $this->getCategoriesWithActiveProducts());
        $counter = 0;
        $output = '';
        if (!$preview) {
            $result = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'category` SET `active` = 0, `date_upd` = NOW()'
                    . ' WHERE `id_category` NOT IN (' . $categories . ') AND `active` = 1');
            if ($result) {
                $counter = Db::getInstance()->Affected_Rows();
                PrestaShopLogger::addLog('Tidy - ' . $this->l('Deactivate active categories without active products') . ' (' . $counter . ')');
            }
        } else {
            $result = Db::getInstance()->executeS('SELECT `id_category` FROM `' . _DB_PREFIX_ . 'category` '
                    . ' WHERE `id_category` NOT IN (' . $categories . ') AND `active` = 1');
            $counter = count($result);
            foreach ($result as $row) {
                $output .= $this->l('Category ID:') . ' ' . $row['id_category'] . '<br/>';
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Deactivate active categories without active products');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Categories deactivated:') . ' ' . $counter . '<br/>';
            echo $output;
        }
        return true;
    }

    public function activateCategoriesWithActiveProducts($verbose = false, $preview = false) {
        $categories = implode(',', $this->getCategoriesWithActiveProducts());
        $counter = 0;
        $output = '';
        if (!$preview) {
            $result = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'category` SET `active` = 1, `date_upd` = NOW()'
                    . ' WHERE `id_category` IN (' . $categories . ') AND `active` = 0');
            if ($result) {
                $counter = Db::getInstance()->Affected_Rows();
                PrestaShopLogger::addLog('Tidy - ' . $this->l('Activate inactive categories with active products') . ' (' . $counter . ')');
            }
        } else {
            $result = Db::getInstance()->executeS('SELECT `id_category` FROM `' . _DB_PREFIX_ . 'category` '
                    . ' WHERE `id_category` IN (' . $categories . ') AND `active` = 0');
            if ($result) {
                $counter = count($result);
                foreach ($result as $row) {
                    $output .= $this->l('Category ID:') . ' ' . $row['id_category'] . '<br/>';
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Activate inactive categories with active products');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Categories activated:') . ' ' . $counter . '<br/>';
            echo $output;
        }
        return true;
    }

    protected function getCategoriesWithActiveProducts() {
        $categoryParents = $this->getCategoriesParents();
        $query = 'SELECT DISTINCT `id_category` FROM `' . _DB_PREFIX_ . 'category_product` WHERE '
                . '`id_product` IN (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` WHERE `active` > 0)';
        $result = Db::getInstance()->executeS($query);
        $categoriesWithProducts = array();
        $parentsWithProducts = array();
        foreach ($result as $row) {
            $categoriesWithProducts[] = $row['id_category'];
            $parentsWithProducts[] = $categoryParents[$row['id_category']];
        }

        $list = implode(',', $categoriesWithProducts) . ',' . implode(',', $parentsWithProducts);
        $uniqueList = array_filter(array_unique(explode(',', $list)));
        sort($uniqueList);
        return $uniqueList;
    }

    protected function getCategoriesParents() {
        $result = Db::getInstance()->executeS('SELECT `id_category`, `id_parent` FROM `' . _DB_PREFIX_ . 'category`');
        $categories = array();
        foreach ($result as $row) {
            $categories[$row['id_category']] = $row['id_parent'];
        }
        $categoryParents = array();
        foreach ($categories as $category => $parent) {
            $parentsTree = array($parent);
            while ($parent != 0) {
                //non existing categories protection
                if (!array_key_exists($parent, $categories)) {
                    $parent = 0;
                } else {
                    $parent = $categories[$parent];
                }
                //infinite loop protection
                if (!in_array($parent, $parentsTree)) {
                    $parentsTree[] = $parent;
                } else {
                    $parent = 0;
                    $parentsTree[] = $parent;
                }
            }
            $categoryParents[$category] = implode(',', $parentsTree);
        }
        return $categoryParents;
    }

    public function activateManufacturersWithActiveProducts($verbose = false, $preview = false) {
        $counter = 0;
        $output = '';
        if (!$preview) {
            $result = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'manufacturer` SET `active` = 1, `date_upd` = NOW()'
                    . ' WHERE `id_manufacturer` IN'
                    . ' (SELECT `id` FROM (SELECT `id_manufacturer` AS `id`, SUM(`active`) AS `active_products`'
                    . ' FROM `' . _DB_PREFIX_ . 'product` GROUP BY `id_manufacturer` HAVING `active_products` > 0) AS `alias`)'
                    . ' AND `active` = 0');
            if ($result) {
                $counter = Db::getInstance()->Affected_Rows();
                PrestaShopLogger::addLog('Tidy - ' . $this->l('Activate inactive manufacturers with active products') . ' (' . $counter . ')');
            }
        } else {
            $result = Db::getInstance()->executeS('SELECT `id_manufacturer` FROM `' . _DB_PREFIX_ . 'manufacturer` '
                    . ' WHERE `id_manufacturer` IN'
                    . ' (SELECT `id` FROM (SELECT `id_manufacturer` AS `id`, SUM(`active`) AS `active_products`'
                    . ' FROM `' . _DB_PREFIX_ . 'product` GROUP BY `id_manufacturer` HAVING `active_products` > 0) AS `alias`)'
                    . ' AND `active` = 0');
            if ($result) {
                $counter = count($result);
                foreach ($result as $row) {
                    $output .= $this->l('Manufacturer ID:') . ' ' . $row['id_manufacturer'] . '<br/>';
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Activate inactive manufacturers with active products');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Manufacturers activated:') . ' ' . $counter . '<br/>';
            echo $output;
        }
        return true;
    }

    public function deactivateManufacturersWithoutActiveProducts($verbose = false, $preview = false) {
        $counter = 0;
        $output = '';
        if (!$preview) {
            $result = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'manufacturer` SET `active` = 0, `date_upd` = NOW() '
                    . ' WHERE `id_manufacturer` NOT IN '
                    . ' (SELECT `id` FROM (SELECT `id_manufacturer` AS `id`, SUM(`active`) AS `active_products` '
                    . ' FROM `' . _DB_PREFIX_ . 'product` GROUP BY `id_manufacturer` HAVING `active_products` > 0) AS `alias`) '
                    . ' AND `active` = 1');
            if ($result) {
                $counter = Db::getInstance()->Affected_Rows();
                PrestaShopLogger::addLog('Tidy - ' . $this->l('Deactivate active manufacturers without active products') . ' (' . $counter . ')');
            }
        } else {
            $result = Db::getInstance()->executeS('SELECT `id_manufacturer` FROM `' . _DB_PREFIX_ . 'manufacturer` '
                    . ' WHERE `id_manufacturer` NOT IN '
                    . ' (SELECT `id` FROM (SELECT `id_manufacturer` AS `id`, SUM(`active`) AS `active_products` '
                    . ' FROM `' . _DB_PREFIX_ . 'product` GROUP BY `id_manufacturer` HAVING `active_products` > 0) AS `alias`) '
                    . ' AND `active` = 1');
            if ($result) {
                $counter = count($result);
                foreach ($result as $row) {
                    $output .= $this->l('Manufacturer ID:') . ' ' . $row['id_manufacturer'] . '<br/>';
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Deactivate active manufacturers without active products');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Manufacturers deactivated:') . ' ' . $counter . '<br/>';
            echo $output;
        }
        return true;
    }

    public function deactivateProductsWithoutActiveCategories($verbose = false, $preview = false) {
        $categoryMap = array();
        $query = 'SELECT `id_category`, `active` FROM `' . _DB_PREFIX_ . 'category`';
        $result = Db::getInstance()->executeS($query);
        if ($result) {
            foreach ($result as $row) {
                $categoryMap[$row['id_category']] = $row['active'];
            }
        }
        $query = 'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_shop` WHERE `active` = 1';
        $result = Db::getInstance()->executeS($query);
        $counter = 0;
        $output = '';
        if ($result) {
            foreach ($result as $row) {
                $p = new Product($row['id_product']);
                $productCategories = $p->getCategories();
                $turnOffProduct = true;
                foreach ($productCategories as $categoryId) {
                    if ($categoryMap[$categoryId] > 0) {
                        $turnOffProduct = false;
                        break;
                    }
                }
                if ($turnOffProduct) {
                    $counter++;
                    $output .= $this->l('Prod. ID:') . ' ' . $row['id_product'] . '<br/>';
                    if (!$preview) {
                        $p->toggleStatus();
                    }
                }
            }
        }
        if ($verbose) {
            echo '<h3>' . $this->l('Deactivate active products without active categories');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
            echo $this->l('Products deactivated:') . ' ' . $counter . '<br/>';
            echo $output;
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Deactivate active products without active categories') . ' (' . $counter . ') ');
        }
        return true;
    }

    public function clearAllCache($verbose = false, $preview = false) {
        if (!$preview) {
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                Tools::clearSf2Cache('dev');
                Tools::clearSf2Cache('prod');
                if ($verbose) {
                    echo 'Symfony cache cleared.<br/>';
                }
            }
            Tools::clearSmartyCache();
            $this->clearSmartyCacheDirs($verbose);
            if ($verbose) {
                echo 'Smarty cache cleared.<br/>';
            }
            Tools::clearXMLCache();
            if ($verbose) {
                echo 'XML cache cleared.<br/>';
            }
            Media::clearCache();
            if ($verbose) {
                echo 'Media cache cleared.<br/>';
            }
            Tools::generateIndex();
            if ($verbose) {
                echo 'Class index regenerated.<br/>';
            }
        } else {
            $output = $this->l('This function has no preview') . '<br/>';
        }
        if ($verbose) {
            echo $output;
        }
        return true;
    }

    protected function clearSmartyCacheDirs($verbose = false) {
        try {
            $smarty = Context::getContext()->smarty;
            $compileDir = realpath($smarty->getCompileDir()) . '/';
            $this->clearDir($compileDir);
            $cacheDir = str_replace('cache/smarty/compile/', 'cache/smarty/cache/', $compileDir);
            $this->clearDir($cacheDir);
        } catch (Exception $e) {
            
        }
    }

    protected function clearDir($target) {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK);
            foreach ($files as $file) {
                $this->clearDir($file);
            }
            rmdir($target);
        } elseif (is_file($target)) {
            unlink($target);
        }
    }

    public function setCheapestCombinationsAsDefault($verbose = false, $preview = false) {
        if ($verbose) {
            echo '<h3>' . $this->l('Set cheapest combinations as default');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
        }
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $shopId = $shop['id_shop'];
            if ($verbose) {
                echo $this->l('Shop:') . ' ' . $shopId . '<br/>';
            }
            $productQuery = 'SELECT DISTINCT `id_product` FROM `' . _DB_PREFIX_ . 'product_attribute_shop` '
                    . ' WHERE `id_shop` = ' . $shopId
                    . ' ORDER BY `id_product` ASC ';
            $result = Db::getInstance()->executeS($productQuery);
            if ($result) {
                foreach ($result as $row) {
                    $productId = (int) $row['id_product'];
                    $minimumQuery = 'SELECT `id_product_attribute`, `price`, `default_on` FROM `' . _DB_PREFIX_ . 'product_attribute_shop` '
                            . ' WHERE `id_product` = ' . $productId . ' AND `id_shop` = ' . $shopId
                            . ' ORDER BY `price` ASC';
                    $minimumRow = Db::getInstance()->getRow($minimumQuery);
                    $minAttributeId = (int) $minimumRow['id_product_attribute'];
                    $minPrice = (float) $minimumRow['price'];
                    $defaultOn = (int) $minimumRow['default_on'];
                    if ($verbose) {
                        echo 'ID: ' . $productId . ' ';
                        echo " min attr id: {$minAttributeId}, min price: {$minPrice}";
                    }
                    if ($defaultOn == 1) {
                        //do no changes
                        if ($verbose) {
                            echo ' - already default';
                        }
                    } else {
                        $defaultQuery = 'SELECT `id_product_attribute`, `price`, `default_on` FROM `' . _DB_PREFIX_ . 'product_attribute_shop` '
                                . ' WHERE `id_product` = ' . $productId . ' AND `default_on` = 1' . ' AND `id_shop` = ' . $shopId;
                        $defaultRow = Db::getInstance()->getRow($defaultQuery);
                        $defaultAttributeId = (int) $defaultRow['id_product_attribute'];
                        $defaultPrice = (float) $defaultRow['price'];
                        if ($verbose) {
                            echo ", default attr id: {$defaultAttributeId}, default price: {$defaultPrice}";
                        }
                        if ($defaultPrice >= ($minPrice + 0.01)) {
                            if (!$preview) {
                                Db::getInstance()->update('product_attribute',
                                        array(
                                            'default_on' => NULL
                                        ), '`id_product_attribute` = ' . $defaultAttributeId);
                                Db::getInstance()->update('product_attribute_shop',
                                        array(
                                            'default_on' => NULL
                                        ), '`id_product_attribute` = ' . $defaultAttributeId . ' AND `id_shop` = ' . $shopId);

                                Db::getInstance()->update('product_attribute_shop',
                                        array(
                                            'default_on' => 1
                                        ), '`id_product_attribute` = ' . $minAttributeId . ' AND `id_shop` = ' . $shopId);
                                Db::getInstance()->update('product_attribute',
                                        array(
                                            'default_on' => 1
                                        ), '`id_product_attribute` = ' . $minAttributeId);
                                Db::getInstance()->update('product',
                                        array(
                                            'cache_default_attribute' => $minAttributeId
                                        ), '`id_product` = ' . $productId);
                                Db::getInstance()->update('product_shop',
                                        array(
                                            'cache_default_attribute' => $minAttributeId
                                        ), '`id_product` = ' . $productId . ' AND `id_shop` = ' . $shopId);
                            }
                            if ($verbose) {
                                echo ' - changing from ' . $defaultAttributeId . ' to ' . $minAttributeId;
                            }
                        } else {
                            if ($verbose) {
                                echo ' - default price is the same as minimum';
                            }
                        }
                    }
                    if ($verbose) {
                        echo "<br/>";
                    }
                }
            }
        }
        if (!$preview) {
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Set cheapest combinations as default'));
        }
        return true;
    }

    public function disableOutOfStockProducts($verbose = false, $preview = false) {
        if ($verbose) {
            echo '<h3>' . $this->l('Deactivate active out of stock products');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
        }
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $shopId = $shop['id_shop'];
            if ($verbose) {
                echo $this->l('Shop:') . ' ' . $shopId . '<br/>';
            }
            $query = 'SELECT `ps`.`id_product`, MAX(`sa`.`quantity`) AS `qty`'
                    . ' FROM `' . _DB_PREFIX_ . 'product_shop` `ps` '
                    . ' LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` `sa` ON `sa`.`id_product` = `ps`.`id_product` '
                    . ' WHERE `ps`.active = 1 AND `ps`.`id_shop` = ' . $shopId
                    . ' GROUP BY `ps`.`id_product` '
                    . ' HAVING `qty` < 1';
            $res = Db::getInstance()->executeS($query);
            if ($res) {
                foreach ($res as $row) {
                    $productId = $row['id_product'];
                    $qty = $row['qty'];
                    if ($verbose) {
                        echo $this->l('Product Id:') . ' ' . $productId . ' - ' . $this->l('Maximum quantity:') . ' ' . $qty . '<br/>';
                    }
                    if (!$preview) {
                        Db::getInstance()->update('product_shop', ['active' => 0], '`id_product` = ' . $productId . ' AND `id_shop` = ' . $shopId);
                    }
                }
            }
        }
        if (!$preview) {
            //synchronize product table
            $query = 'UPDATE `' . _DB_PREFIX_ . 'product` SET `active` = 0 WHERE `active` = 1 AND `id_product` IN '
                    . ' (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_shop` GROUP BY `id_product` HAVING SUM(`active`) = 0)';
            Db::getInstance()->execute($query);
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Deactivate active out of stock products'));
        }
        return true;
    }

    public function enableInStockProducts($verbose = false, $preview = false) {
        if ($verbose) {
            echo '<h3>' . $this->l('Activate inactive in stock products');
            if ($preview) {
                echo ' (' . $this->l('preview') . ')';
            }
            echo '</h3>';
        }
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $shopId = $shop['id_shop'];
            if ($verbose) {
                echo $this->l('Shop:') . ' ' . $shopId . '<br/>';
            }
            $query = 'SELECT `ps`.`id_product`, MAX(`sa`.`quantity`) AS `qty`'
                    . ' FROM `' . _DB_PREFIX_ . 'product_shop` `ps` '
                    . ' LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` `sa` ON `sa`.`id_product` = `ps`.`id_product` '
                    . ' WHERE `ps`.active = 0 AND `ps`.`id_shop` = ' . $shopId
                    . ' GROUP BY `ps`.`id_product` '
                    . ' HAVING `qty` > 0';
            $res = Db::getInstance()->executeS($query);
            if ($res) {
                foreach ($res as $row) {
                    $productId = $row['id_product'];
                    $qty = $row['qty'];
                    if ($verbose) {
                        echo $this->l('Product Id:') . ' ' . $productId . ' - ' . $this->l('Maximum quantity:') . ' ' . $qty . '<br/>';
                    }
                    if (!$preview) {
                        Db::getInstance()->update('product_shop', ['active' => 1], '`id_product` = ' . $productId . ' AND `id_shop` = ' . $shopId);
                    }
                }
            }
        }
        if (!$preview) {
            //synchronize product table
            $query = 'UPDATE `' . _DB_PREFIX_ . 'product` SET `active` = 1 WHERE `active` = 0 AND `id_product` IN '
                    . ' (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_shop` GROUP BY `id_product` HAVING MIN(`active`) = 1)';
            Db::getInstance()->execute($query);
            PrestaShopLogger::addLog('Tidy - ' . $this->l('Activate inactive in stock products'));
        }
        return true;
    }

}
