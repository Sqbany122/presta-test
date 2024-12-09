<?php
require_once (dirname(__FILE__) . '/../../x13productswithoutimages.php');

class AdminXProductsWIController extends ModuleAdminController
{
    public $bootstrap = true;

    /** @var x13productswithoutimages */
    public $module;

    public function init()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        parent::init();
    }

    public function __construct()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->translator = Context::getContext()->getTranslator();
        }

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->bootstrap = false;
        }

        $this->fields_options = array(
            'general' => array(
                'title' =>  $this->l('General settings'),
                'image' => '../img/t/AdminPreferences.gif',
                'fields' => array(
                    'X13_PWI_TYPE' => array(
                        'title' => $this->l('Disable type'),
                        'type' => 'select',
                        'identifier' => 'id_type',
                        'list' => array(
                            array(
                                'id_type' => XProductsWI::PWI_DISABLE,
                                'name' => $this->l('disable')
                            ),
                            array(
                                'id_type' => XProductsWI::PWI_HIDE,
                                'name'  => $this->l('hide')
                            ),
                            array(
                                'id_type' => XProductsWI::PWI_HIDE_LEFT_CATALOG,
                                'name'  => $this->l('hide, visibility catalog only')
                            ),
                            array(
                                'id_type' => XProductsWI::PWI_HIDE_LEFT_SEARCH,
                                'name'  => $this->l('hide, visibility search only')
                            )
                        )
                    ),
                    'X13_PWI_IGNORE_PRODUCTS' => array(
                        'title' => $this->l('Ignored products'),
                        'hint' => $this->l('Module will not take any actions on products from this list'),
                        'desc' => $this->l('id of products, separated by comma'),
                        'type' => 'text',
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            )
        );

        parent::__construct();

        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminModules'));
            $this->tabAccess['view'] = Module::getPermissionStatic($this->module->id, 'view');

            $configAccess = Module::getPermissionStatic($this->module->id, 'configure');
            $this->tabAccess['add'] = $configAccess;
            $this->tabAccess['edit'] = $configAccess;
            $this->tabAccess['delete'] = $configAccess;
        } else {
            $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminModules'));
        }

        $this->tpl_folder = 'x_pwi_configuration/';
    }

    public function initContent()
    {
        $this->context->smarty->assign('is_bootstrap', $this->bootstrap);

        parent::initContent();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('resetProducts')) {
            if ($this->resetProducts((int) Tools::getValue('id_shop'))) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminXProductsWI') . '&conf=4');
            }
        }

        if (Tools::getIsset('offproducts') && Tools::getIsset('id_shop'))
        {
            $products = XProductsWI::updateProducts((int) Tools::getValue('id_shop'), true);

            if (empty($products)) {
                $this->errors[] = $this->l('There are no products to update');
            }
            else {
                $html = '<table>';
                foreach ($products as $product) {
                    $html .= '<tr><td>'. $product['id_product'] .': ' . $product['name'] . '</td></tr>';
                }
                $html .= '</table>';

                $this->confirmations[] = '<b>' . $this->l('Updated products:') . '</b><br><br>' . $html;
            }
        }

        return parent::postProcess();
    }

    public function renderOptions()
    {
        foreach (Shop::getShops() as $shop)
        {
            $shopObj = new Shop($shop['id_shop']);
            $token = substr(Tools::encrypt('x13productswithoutimages/cron/shop' . $shopObj->id), 0, 10);

            $this->fields_options['shop' . $shopObj->id] = array(
                'title' =>  $shopObj->name,
                'image' => '../img/t/AdminPreferences.gif',
                'fields' => array(
                    'cron_link' => array(
                        'title' => $this->l('CRON link'),
                        'type' => 'cron_link',
                        'cron_link' => $shopObj->getBaseURL(Configuration::get('PS_SSL_ENABLED')) . 'modules/x13productswithoutimages/cron.php?id_shop=' . $shopObj->id . '&token=' . $token
                    ),
                    'update_button' => array(
                        'title' => $this->l('Upgrade'),
                        'button_label' => $this->l('Upgrade now'),
                        'type' => 'update_button',
                        'update_button' => $this->context->link->getAdminLink('AdminXProductsWI') . '&offproducts&id_shop=' . $shopObj->id,
                        'desc' => sprintf($this->l('Last update on: %s'), Configuration::get('X13_PWI_UPDATE_'.$shopObj->id))
                    ),
                    'reset_button' => array(
                        'title' => $this->l('This option will enable and show all products in your store, not only this disabled or hidden because of this module.'),
                        'button_label' => $this->l('Enable and show all'),
                        'type' => 'update_button',
                        'update_button' => $this->context->link->getAdminLink('AdminXProductsWI') . '&resetProducts=1&id_shop=' . $shopObj->id,
                        'confirm' => true,
                    ),
                )
            );
        }

        return parent::renderOptions();
    }

    public function resetProducts($idShop)
    {
        $result = Db::getInstance()->Execute('
            UPDATE '._DB_PREFIX_.'product_shop ps
            SET ps.`visibility` = \'both\', ps.`active` = 1
            WHERE id_shop = '.$idShop.'
        ');


        if (!Shop::isFeatureActive()) {
            $result &= Db::getInstance()->Execute('
                UPDATE '._DB_PREFIX_.'product ps
                SET ps.`visibility` = \'both\', ps.`active` = 1
                WHERE 1=1
            ');
        }

        try {
            Hook::exec('updateproduct');
        } catch (Exception $e) {}

        return $result;
    }
}
