<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright 2018 PrestaChamps
 * @license   Commercial
 */

/**
 * Class GdprPro Main module class
 *
 * The GDPR module for PrestaShop is an All-In-One solution that,
 * once set-up is completed, will assure compliance with the new
 * GDPR legislation and will allow the online store to function
 * legally while avoiding fines.
 */
class GdprPro extends Module
{
    protected $config_form = false;
    public $gCookie;

    const COOKIE_CATEGORY_NECESSARY    = 'necessary';
    const COOKIE_CATEGORY_PREFERENCES  = 'preferences';
    const COOKIE_CATEGORY_STATISTICS   = 'statistics';
    const COOKIE_CATEGORY_MARKETING    = 'marketing';
    const COOKIE_CATEGORY_UNCLASSIFIED = 'unclassified';

    public static $cookieCategories = array(
        self::COOKIE_CATEGORY_NECESSARY,
        self::COOKIE_CATEGORY_PREFERENCES,
        self::COOKIE_CATEGORY_STATISTICS,
        self::COOKIE_CATEGORY_MARKETING,
        self::COOKIE_CATEGORY_UNCLASSIFIED,
    );

    public $tabs = array(
        array(
            'name'              => 'Data Requests',
            'class_name'        => 'AdminDataRequests',
            'visible'           => true,
            'parent_class_name' => 'AdminParentCustomer',
        ),
        array(
            'name'              => 'GDPR Module Hooks',
            'class_name'        => 'AdminModuleHooks',
            'visible'           => true,
            'parent_class_name' => 'AdminParentModulesSf',
        ),
        array(
            'name'              => 'Data requests',
            'class_name'        => 'AdminDataRequests',
            'visible'           => false,
            'parent_class_name' => 'AdminParentCustomer',
        ),
        array(
            'name'              => 'GDPR Config',
            'class_name'        => 'AdminGdprConfig',
            'visible'           => true,
            'parent_class_name' => 'AdminParentModulesSf',
        ),
        array(
            'name'              => 'Activity log',
            'class_name'        => 'AdminGdprLog',
            'visible'           => true,
            'parent_class_name' => 'AdminParentCustomer',
        ),
    );

    public function __construct()
    {
        $this->name = 'gdprpro';
        $this->tab = 'front_office_features';
        $this->version = '2.1.4';
        $this->author = 'PrestaChamps';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('GDPR Compliance Pro');
        $this->description =
            $this->l('The GDPR module assures compliance with the new GDPR legislation and will allow the online store to operate legally.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this awesome module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->module_key = '98c32f2a35b5ac5387c6ebd938ffa59e';

        require_once $this->getLocalPath() . "vendor/autoload.php";
        $this->gCookie = GdprProCookie::getInstance();
    }

    /**
     * Install and setup the module with default settings
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function install()
    {
        try {
            Configuration::updateValue('GDPR_PRO_LIVE_MODE', false);
            $defaultValues = require_once 'install/default-values.php';
            foreach ($defaultValues as $key => $value) {
                /**
                 * Only insert config key if doesn't exists in the database
                 */
                if (!GdprProConfig::configExists($key)) {
                    /**
                     * If the key is multilang insert properly
                     */
                    if (in_array($key, GdprProConfig::$multiLang, true)) {
                        foreach (Language::getLanguages(true) as $language) {
                            Configuration::updateValue(
                                $key,
                                array(
                                    $language['id_lang'] =>
                                        \PrestaChamps\Common\Helpers\InstallHelper::getDefaultValues(
                                            $this->getLocalPath(),
                                            $key,
                                            $language['iso_code']
                                        ),
                                )
                            );
                        }
                    } else {
                        Configuration::updateValue(
                            $key,
                            $value
                        );
                    }
                }
            }
            if (!GdprProConfig::configExists(GdprProConfig::MODULES_TO_UNLOAD)) {
                $langs = array();
                foreach (Language::getLanguages(true) as $language) {
                    $langs[$language['id_lang']] = $language['iso_code'];
                }
                GdprProConfig::setModulesToUnload(
                    \PrestaChamps\Common\Helpers\InstallHelper::multilangModulesToUnload(
                        $this->local_path,
                        $langs
                    )
                );
            }

            if (!self::isPs17()) {
                /**
                 * Create tabs for retro-compatibility
                 */
                foreach ($this->tabs as $tabItem) {
                    $tab = new Tab();
                    $tab->name = \PrestaChamps\Common\Helpers\MultilangHelper::stringToMultilangArray($tabItem['name']);
                    $tab->class_name = $tabItem['class_name'];
                    $tab->id_parent = Tab::getIdFromClassName($tabItem['parent_class_name']);
                    $tab->module = 'gdprpro';
                    $tab->position = 0;
                    $tab->active = $tabItem['visible'] ? 1 : 0;
                    $tab->save();
                }
            }

            /**
             * Create db structure
             */
            $object = new \PrestaChamps\GdprPro\Models\DataRequest();
            $object->createDatabase();
            $object = new GdprActivityLog();
            $object->createDatabase();
        } catch (Exception $exception) {
            $this->_errors[] = $exception->getMessage();
        }

        return parent::install() &&
            // Ps 1.6  consent hooks
            $this->registerHook('actionBeforeSubmitAccount') &&
            $this->registerHook('createAccountForm') &&
            $this->registerHook('displayCustomerIdentityForm') &&
            // Ps 1.7 consent hooks
            $this->registerHook('additionalCustomerFormFields') &&
            $this->registerHook('validateCustomerFormFields') &&
            // Other hooks
            $this->registerHook('displayAdminCustomers') &&
            $this->registerHook('header') &&
            $this->registerHook('moduleRoutes') &&
            $this->registerHook('displayCustomerAccount') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayFooterAfter') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        foreach ($this->tabs as $tabItem) {
            try {
                while (true) {
                    $tab = Tab::getInstanceFromClassName($tabItem['class_name']);
                    if (!Validate::isLoadedObject($tab)) {
                        break;
                    }
                    $tab->delete();
                }
            } catch (Exception $exception) {
                error_log("Can't delete module tab: " . $exception->getMessage());
            }
        }

        return parent::uninstall();
    }

    /**
     * Redirect to the custom config controller
     */
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminGdprConfig'));
    }

    public function hookDisplayAdminCustomers($params)
    {
        // Select all available extra info tabs
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . GdprActivityLog::$definition['table'];
        $sql .= ' WHERE id_customer = ' . pSQL($params['id_customer']);
        $sql .= ' ORDER BY `id_gdpr_activity_log` DESC LIMIT 50';

        if ($result = Db::getInstance()->ExecuteS($sql)) {
            $helper = new HelperList();
            $helper->shopLinkType = '';
            $helper->simple_header = true;
            $helper->identifier = GdprActivityLog::$definition['primary'];
            $helper->show_toolbar = false;
            $helper->title = $this->l('Customer consents');
            $helper->table = $this->name;
            $helper->token = Tools::getAdminTokenLite('AdminCustomers');
            $helper->currentIndex = AdminController::$currentIndex . '&id_customer=8&viewcustomer';
            return $helper->generateList(
                $result,
                array(
                    'id_gdpr_activity_log' => array(
                        'align' => 'center',
                        'class' => 'fixed-width-xs',
                        'title' => 'ID',
                        'type'  => 'text',
                    ),
                    'activity_subject'     => array(
                        'title'        => $this->l('Subject'),
                        'filter_key'   => 'activity_type',
                        'list'         => array(
                            GdprActivityLog::ACTIVITY_TYPE_COOKIE_ACCEPT  =>
                                $this->l("Cookie accepted"),
                            GdprActivityLog::ACTIVITY_TYPE_REGISTRATION   =>
                                $this->l("Signup form consent accepted"),
                            GdprActivityLog::ACTIVITY_TYPE_PROFILE_UPDATE =>
                                $this->l("Profile update form consent accepted"),
                        ),
                        'havingFilter' => true,
                        'type'         => 'select',
                    ),
                    'activity_data'        => array(
                        'title'        => $this->l('Data'),
                        'havingFilter' => false,
                    ),
                    'date_add'             => array(
                        'title' => $this->l('Created at'),
                        'type'  => 'date',
                    ),
                )
            );
        }
    }

    public function hookHeader()
    {
        Media::addJsDef(array(
            'gdprSettings' => array(
                'gdprEnabledModules'    =>
                    (is_array($this->gCookie->content))
                        ?
                        $this->gCookie->content
                        :
                        GdprProCookie::getDefaultModules(),
                'gdprCookieStoreUrl'    =>
                    Context::getContext()->link->getModuleLink($this->name, 'storecookie'),
                'newsletterConsentText' =>
                    htmlspecialchars((Configuration::get(
                        GdprProConfig::CONSENT_CHKBOX_NEWSLETTER_TEXT,
                        Context::getContext()->language->id
                    )), ENT_QUOTES, 'UTF-8'),
                'checkAllByDefault'     => (bool)Configuration::get(
                    GdprProConfig::CHECK_ALL_MODULES_BY_DEFAULT
                ),
                'showWindow'            => !Context::getContext()->cookie->gdpr_windows_was_opened,
            ),
        ));

        if (Configuration::get(GdprProConfig::CONSENT_CHKBOX_NEWSLETTER_ENABLE)) {
            $this->context->controller->addCSS($this->getLocalPath() . '/views/css/newsletter.css');
            if (self::isPs17()) {
                $this->context->controller->addJS(
                    $this->getLocalPath() . '/views/js/newsletter.js'
                );
            } else {
                $this->context->controller->addJS(
                    $this->getLocalPath() . '/views/js/newsletter-16.js'
                );
            }
        }
        $this->context->controller->addJS($this->getLocalPath() . '/views/js/gdpr-modal.js');
        $this->context->controller->addJS($this->getLocalPath() . '/views/js/gdpr-consent.js');
        $this->context->controller->addCSS($this->getLocalPath() . '/views/css/gdpr-modal.css');
        $this->context->controller->addCSS($this->getLocalPath() . '/views/css/front.css');
    }

    /**
     * Footer hook, displays the `Control your privacy` button in the footer
     *
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function hookDisplayFooter()
    {
        $extraContent = "";

        $this->context->smarty->assign(
            array(
                'modules_to_unload'          => GdprProConfig::getModulesToUnload(true),
                'modules_to_unload_count'    => count(GdprProConfig::getModulesToUnload(true)),
                'languages'                  => Language::getLanguages(),
                'welcomeTabTitle'            => Configuration::get(
                    GdprProConfig::TAB_NAME_WELCOME,
                    Context::getContext()->language->id
                ),
                'welcomeTabText'             => Configuration::get(
                    GdprProConfig::TAB_TEXT_WELCOME,
                    Context::getContext()->language->id
                ),
                'cookieCategoryDescriptions' => array(
                    'necessary'    => Configuration::get(
                        GdprProConfig::TAB_TEXT_NECESSARY,
                        Context::getContext()->language->id
                    ),
                    'preferences'  => Configuration::get(
                        GdprProConfig::TAB_TEXT_PREFERENCES,
                        Context::getContext()->language->id
                    ),
                    'statistics'   => Configuration::get(
                        GdprProConfig::TAB_TEXT_STATISTICS,
                        Context::getContext()->language->id
                    ),
                    'marketing'    => Configuration::get(
                        GdprProConfig::TAB_TEXT_MARKETING,
                        Context::getContext()->language->id
                    ),
                    'unclassified' => Configuration::get(
                        GdprProConfig::TAB_TEXT_UNCLASSIFIED,
                        Context::getContext()->language->id
                    ),
                ),
                'tabNameLink'                => Configuration::get(
                    GdprProConfig::TAB_NAME_LINK,
                    Context::getContext()->language->id
                ),
                'tabContentLink'             => Configuration::get(
                    GdprProConfig::TAB_CONTENT_LINK,
                    Context::getContext()->language->id
                ),
                'footerLinkText'             => Configuration::get(
                    GdprProConfig::FOOTER_LINK_TEXT,
                    Context::getContext()->language->id
                ),
                'saveBtnBgColor'             => Configuration::get(GdprProConfig::SAVE_BTN_BG_COLOR),
                'saveBtnTextColor'           => Configuration::get(GdprProConfig::SAVE_BTN_TEXT_COLOR),
                'acceptAllBtnBgColor'        => Configuration::get(GdprProConfig::ACCEPT_ALL_BTN_BG_COLOR),
                'acceptAllBtnTextColor'      => Configuration::get(GdprProConfig::ACCEPT_ALL_BTN_TEXT_COLOR),
                'popupBgColor'               => Configuration::get(GdprProConfig::POPUP_BG_COLOR),
                'popupTextColor'             => Configuration::get(GdprProConfig::POPUP_TEXT_COLOR),
                'popupPosition'              => Configuration::get(GdprProConfig::POPUP_POSITION),
                'popupStyle'                 => Configuration::get(GdprProConfig::POPUP_STYLE),
                'under16Enable'              => Configuration::get(GdprProConfig::UNDER_16_ENABLE),
                'showPopupTitle'             => Configuration::get(GdprProConfig::POPUP_SHOW_TITLE),
                'popupTemplate'              => Configuration::get(GdprProConfig::POPUP_STYLE),
                'footerLinkBgColor'          => Configuration::get(GdprProConfig::FOOTER_LINK_BG_COLOR),
                'footerLinkTextColor'        => Configuration::get(GdprProConfig::FOOTER_LINK_TEXT_COLOR),
                'footerLinkBorderColor'      => Configuration::get(GdprProConfig::FOOTER_LINK_BORDER_COLOR),

            )
        );

        if (!self::isPs17()) {
            if (get_class($this->context->controller) == 'ContactController' &&
                Configuration::getGlobalValue(GdprProConfig::CONSENT_CHKBOX_CONTACT_ENABLE)) {
                $this->context->controller->addJS($this->getLocalPath() . '/views/js/contact.js');
                $this->context->smarty->assign(
                    array(
                        'label' => Configuration::get(
                            GdprProConfig::CONSENT_CHKBOX_CONTACT_TEXT,
                            Context::getContext()->language->id
                        ),
                    )
                );
                $extraContent .= $this->context->smarty->fetch(
                    $this->getTemplatePath('views/templates/hook/consent-checkbox-16.tpl')
                );
            }
        }
        return $extraContent .
            $this->context->smarty->fetch($this->getTemplatePath('views/templates/hook/footer.tpl'));
    }

    /**
     * Check if the current PrestaShop installation is version 1.7 or below
     *
     * @return bool
     */
    public static function isPs17()
    {
        return (bool)version_compare(_PS_VERSION_, '1.7', '>=');
    }

    /**
     * @return string
     * @throws SmartyException
     */
    public function hookDisplayCustomerAccount()
    {
        return $this->context->smarty->fetch($this->getTemplatePath('views/templates/hook/customer-account.tpl'));
    }

    /**
     * Add custom module routes for nice and clean urls
     *
     * @return array
     */
    public function hookModuleRoutes()
    {
        return array(
            'module-gdprpro-erasemydata'   => array(
                'controller' => 'erasemydata',
                'rule'       => 'my-account/delete-my-data',
                'keywords'   => array(),
                'params'     => array(
                    'fc'     => 'module',
                    'module' => 'gdprpro',
                ),
            ),
            'module-gdprpro-requestmydata' => array(
                'controller' => 'requestmydata',
                'rule'       => 'my-account/request-my-data',
                'keywords'   => array(),
                'params'     => array(
                    'fc'     => 'module',
                    'module' => 'gdprpro',
                ),
            ),
            'module-gdprpro-datarequest'   => array(
                'controller' => 'datarequest',
                'rule'       => 'my-account/data-request/{type}',
                'keywords'   => array(
                    'type' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*', 'param' => 'type'),
                ),
                'params'     => array(
                    'fc'     => 'module',
                    'module' => 'gdprpro',
                ),
            ),
        );
    }


    /**
     * Customer account checkbox, only for 1.7
     *
     * @return array
     */
    public function hookAdditionalCustomerFormFields()
    {
        $controllerName = get_class(Context::getContext()->controller);


        if ($controllerName === 'IdentityController' &&
            Configuration::get(GdprProConfig::CONSENT_CHKBOX_MYACCOUNT_ENABLE)) {
            $formField = (new FormField())
                ->setName("gdpr_consent_chkbox")
                ->setType('checkbox')
                ->setLabel(
                    Configuration::get(
                        GdprProConfig::CONSENT_CHKBOX_MYACCOUNT_TEXT,
                        $this->context->language->id
                    )
                )
                ->setRequired(true);

            return array($formField);
        }

        if ($controllerName === 'AuthController' &&
            Configuration::get(GdprProConfig::CONSENT_CHKBOX_SIGNUP_ENABLE)) {
            $formField = (new FormField())
                ->setName("gdpr_consent_chkbox")
                ->setType('checkbox')
                ->setLabel(
                    Configuration::get(
                        GdprProConfig::CONSENT_CHKBOX_SIGNUP_TEXT,
                        $this->context->language->id
                    )
                )
                ->setRequired(true);

            return array($formField);
        }
        if ($controllerName === 'OrderController' &&
            Configuration::get(GdprProConfig::CONSENT_CHKBOX_SIGNUP_ENABLE)) {
            $formField = (new FormField())
                ->setName("gdpr_consent_chkbox")
                ->setType('checkbox')
                ->setLabel(
                    Configuration::get(
                        GdprProConfig::CONSENT_CHKBOX_SIGNUP_TEXT,
                        $this->context->language->id
                    )
                )
                ->setRequired(true);

            return array($formField);
        }

        return array();
    }

    /**
     * Validate consent checkboxes and add activity log, only for 1.7
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws PrestaShopModuleException
     */
    public function hookValidateCustomerFormFields()
    {
        $controller = get_class(Context::getContext()->controller);
        if ($controller === 'AuthController') {
            \PrestaChamps\GdprPro\Models\ActivityLogFactory::makeSignupFormConsent();
        } elseif ($controller === 'IdentityController') {
            \PrestaChamps\GdprPro\Models\ActivityLogFactory::makeMyAccountFormConsent();
        }

        return true;
    }

    /**
     * Customer account checkbox, only for 1.6
     *
     * @return null|string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws PrestaShopModuleException
     * @throws SmartyException
     */
    public function hookDisplayCustomerIdentityForm()
    {
        if (!self::isPs17()) {
            if (Tools::isSubmit('submitIdentity') && Configuration::get(GdprProConfig::CONSENT_CHKBOX_SIGNUP_ENABLE)) {
                if (Tools::getValue('gdpr_consent_chkbox', false) != '1') {
                    $this->context->controller->errors[] =
                        $this->l('Please indicate that you have read and agree to the Terms and Conditions and Privacy Policy');
                } else {
                    \PrestaChamps\GdprPro\Models\ActivityLogFactory::makeMyAccountFormConsent();
                }
            }
            if (Configuration::get(GdprProConfig::CONSENT_CHKBOX_MYACCOUNT_ENABLE)) {
                $this->context->smarty->assign(array(
                    'label' => Configuration::get(
                        GdprProConfig::CONSENT_CHKBOX_MYACCOUNT_TEXT,
                        $this->context->language->id
                    ),
                ));
                return $this->context->smarty->fetch(
                    $this->getLocalPath() .
                    'views/templates/hook/consent-checkbox-16.tpl'
                );
            }
        }

        return null;
    }

    /**
     * Customer account checkbox, only for 1.6
     *
     * @return null|string
     * @throws SmartyException
     */
    public function hookCreateAccountForm()
    {
        if (!self::isPs17()) {
            if (Configuration::get(GdprProConfig::CONSENT_CHKBOX_SIGNUP_ENABLE)) {
                $this->context->smarty->assign(array(
                    'label' => Configuration::get(
                        GdprProConfig::CONSENT_CHKBOX_SIGNUP_TEXT,
                        $this->context->language->id
                    ),
                ));
                return $this->context->smarty->fetch(
                    $this->getLocalPath() .
                    'views/templates/hook/consent-checkbox-16.tpl'
                );
            }
        }
        return null;
    }

    /**
     * Customer account checkbox, only for 1.6
     *
     * @param $params
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws PrestaShopModuleException
     */
    public function hookActionBeforeSubmitAccount($params)
    {
        if (!self::isPs17()) {
            $controller = get_class($this->context->controller);
            if ($controller == 'AuthController' &&
                Configuration::get(GdprProConfig::CONSENT_CHKBOX_SIGNUP_ENABLE)) {
                if (Tools::getValue('gdpr_consent_chkbox', false) !== '1') {
                    $this->context->controller->errors[] =
                        $this->l('Please indicate that you have read and agree to the Terms and Conditions and Privacy Policy');
                } else {
                    if (count($this->context->controller->errors) == 0) {
                        \PrestaChamps\GdprPro\Models\ActivityLogFactory::makeSignupFormConsent();
                    }
                }
            }
        }

        return true;
    }
}
