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
 *
 * @var $module GdprPro
 */

/**
 * Class AdminGdprConfigController Back-office config controller
 */
class AdminGdprConfigController extends ModuleAdminController
{
    public $bootstrap = true;
    private static $formName  = "gdpr-config-form";

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_title = $this->module->l('GDPR Configuration');
        $this->toolbar_title = $this->module->l('GDPR Configuration');
    }

    public function initToolbar()
    {
        $this->page_header_toolbar_btn['gdpr_manage_modules'] = array(
            'short' => $this->module->l('Back to config'),
            'href'  => $this->context->link->getAdminLink('AdminModuleHooks'),
            'desc'  => $this->module->l('GDPR enabled modules'),
            'class' => 'icon-user-secret',
        );
    }

    /**
     * @throws SmartyException
     */
    public function initContent()
    {
        if (Tools::isSubmit(self::$formName)) {
            $this->processConfiguration();
        }
        $this->informations[] =
            $this->module->l("This is the pop-up displayed to your visitors when they first access your site. 
            Please provide all the information neccessary for your clients to be able to 
            decide what information they want to share on your store.");
        $this->content .=
            Context::getContext()->smarty->fetch(
                $this->module->getTemplatePath(
                    'views/templates/admin/config-toolbar.tpl'
                )
            );
        $this->content .= $this->renderForm();

        parent::initContent();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->module->l('Configuration'),
                    'icon'  => 'icon-envelope',
                ),
                'input'  => array(
                    array(
                        'type'     => 'text',
                        'label'    => $this->module->l('Welcome tab title'),
                        'name'     => GdprProConfig::TAB_NAME_WELCOME,
                        'required' => true,
                        'lang'     => true,
                        'hint'     => $this->module->l('This is the title of the main tab which is open by 
                        default whenever someone accesses your site for the first time'),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('Welcome text'),
                        'name'         => GdprProConfig::TAB_TEXT_WELCOME,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l('This is the content of the default tab and 
                        should be used to describe why certain information is collected on the site'),
                    ),


                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('Necessary tab text'),
                        'name'         => GdprProConfig::TAB_TEXT_NECESSARY,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l('A description of the cookies which are necessary for 
                        the website to function correctly and, if needed, a warning that disabling them through the 
                        browser settings can make certain areas or functions of the site unusable'),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('Performance tab text'),
                        'name'         => GdprProConfig::TAB_TEXT_PREFERENCES,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l('This tab will contain the list of the modules 
                        visitors can choose to disable before continuing on your site. The text entered here will be 
                        displayed below the list of modules and should contain a general description of how these 
                        modules track information and what are the benefits of it. '),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('Statistics tab text'),
                        'name'         => GdprProConfig::TAB_TEXT_STATISTICS,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l('This tab will contain the list of the modules 
                        visitors can choose to disable before continuing on your site. The text entered here will be 
                        displayed below the list of modules and should contain a general description of how these 
                        modules track information and what are the benefits of it. '),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('Marketing tab text'),
                        'name'         => GdprProConfig::TAB_TEXT_MARKETING,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l('This tab will contain the list of the modules 
                        visitors can choose to disable before continuing on your site. The text entered here will be 
                        displayed below the list of modules and should contain a general description of how these 
                        modules track information and what are the benefits of it. '),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('Unclassified tab text'),
                        'name'         => GdprProConfig::TAB_TEXT_UNCLASSIFIED,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l('Unclassified cookies are cookies that we are in 
                        the process of classifying, together with the providers of individual cookies.'),
                    ),
                    array(
                        'type'     => 'text',
                        'label'    => $this->module->l('Additional link tab title'),
                        'name'     => GdprProConfig::TAB_NAME_LINK,
                        'required' => true,
                        'lang'     => true,
                        'hint'     => $this->module->l(
                            'Usually the title of your Cookie Policy or Privacy 
                        Policy page. When clicking on this tab, users will be taken to the URL specified below'
                        ),
                    ),
                    array(
                        'type'         => 'text',
                        'label'        => $this->module->l('Additional link target'),
                        'name'         => GdprProConfig::TAB_CONTENT_LINK,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l('The link to your Cookie or Privacy Policy page'),
                    ),
                    array(
                        'type'     => 'text',
                        'label'    => $this->module->l('Footer link text'),
                        'name'     => GdprProConfig::FOOTER_LINK_TEXT,
                        'required' => true,
                        'lang'     => true,
                        'hint'     => $this->module->l(
                            'A link will be created in your website\'s footer which 
                        allows visitors to reopen the pop-up and further customize their tracking preferences. 
                        This is the title of that link'
                        ),
                    ),
                    array(
                        'type'         => 'color',
                        'label'        => $this->module->l('Footer link background color'),
                        'name'         => GdprProConfig::FOOTER_LINK_BG_COLOR,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                    ),
                    array(
                        'type'         => 'color',
                        'label'        => $this->module->l('Footer link text color'),
                        'name'         => GdprProConfig::FOOTER_LINK_TEXT_COLOR,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                    ),
                    array(
                        'type'         => 'color',
                        'label'        => $this->module->l('Footer link border color'),
                        'name'         => GdprProConfig::FOOTER_LINK_BORDER_COLOR,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                    ),
                    array(
                        'type'    => 'switch',
                        'name'    => GdprProConfig::CONSENT_CHKBOX_SIGNUP_ENABLE,
                        'label'   => $this->module->l('Enable signup form consent'),
                        'hint'    => $this->module->l('A checkbox will be added to the signup form with a checkbox'),
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'signupconsentchkbox_on',
                                'value' => '1',
                                'label' => $this->module->l('Enable'),
                            ),
                            array(
                                'id'    => 'signupconsentchkbox_off',
                                'value' => '0',
                                'label' => $this->module->l('Disable'),
                            ),
                        ),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('Signup form message'),
                        'name'         => GdprProConfig::CONSENT_CHKBOX_SIGNUP_TEXT,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l(
                            'This message will be added to the signup form with a checkbox'
                        ),
                    ),
                    array(
                        'type'    => 'switch',
                        'name'    => GdprProConfig::CONSENT_CHKBOX_MYACCOUNT_ENABLE,
                        'label'   => $this->module->l('Enable my account form consent'),
                        'hint'    => $this->module->l(
                            'A checkbox will be added to the my account form with a checkbox'
                        ),
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'signupconsentchkbox_on',
                                'value' => '1',
                                'label' => $this->module->l('Enable'),
                            ),
                            array(
                                'id'    => 'signupconsentchkbox_off',
                                'value' => '0',
                                'label' => $this->module->l('Disable'),
                            ),
                        ),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('MyAccount form message'),
                        'name'         => GdprProConfig::CONSENT_CHKBOX_MYACCOUNT_TEXT,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l(
                            'This message will be added to the my account form with a checkbox'
                        ),
                    ),
                    array(
                        'type'    => 'switch',
                        'name'    => GdprProConfig::CONSENT_CHKBOX_CONTACT_ENABLE,
                        'label'   => $this->module->l('Enable contact form consent'),
                        'hint'    => $this->module->l(
                            'A checkbox will be added to the contact form with a checkbox'
                        ),
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'contactconsentchkbox_on',
                                'value' => '1',
                                'label' => $this->module->l('Enable'),
                            ),
                            array(
                                'id'    => 'contactconsentchkbox_off',
                                'value' => '0',
                                'label' => $this->module->l('Disable'),
                            ),
                        ),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('Contact consent form message'),
                        'name'         => GdprProConfig::CONSENT_CHKBOX_CONTACT_TEXT,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l(
                            'This message will be added to the contact form with a checkbox'
                        ),
                    ),
                    array(
                        'type'    => 'switch',
                        'name'    => GdprProConfig::CONSENT_CHKBOX_NEWSLETTER_ENABLE,
                        'label'   => $this->module->l('Enable newsletter form consent'),
                        'hint'    => $this->module->l(
                            'A checkbox will be added to the newsletter form with a checkbox'
                        ),
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'newsletterconsentchkbox_on',
                                'value' => '1',
                                'label' => $this->module->l('Enable'),
                            ),
                            array(
                                'id'    => 'newsletterconsentchkbox_off',
                                'value' => '0',
                                'label' => $this->module->l('Disable'),
                            ),
                        ),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->module->l('Newsletter consent form message'),
                        'name'         => GdprProConfig::CONSENT_CHKBOX_NEWSLETTER_TEXT,
                        'required'     => true,
                        'lang'         => true,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l(
                            'This message will be added to the newsletter form with a checkbox'
                        ),
                    ),
                    array(
                        'type'         => 'color',
                        'label'        => $this->module->l('Accept all button background color'),
                        'name'         => GdprProConfig::ACCEPT_ALL_BTN_BG_COLOR,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                    ),
                    array(
                        'type'         => 'color',
                        'label'        => $this->module->l('Accept all button text color'),
                        'name'         => GdprProConfig::ACCEPT_ALL_BTN_TEXT_COLOR,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                    ),
                    array(
                        'type'         => 'color',
                        'label'        => $this->module->l('Save button background color'),
                        'name'         => GdprProConfig::SAVE_BTN_BG_COLOR,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                    ),
                    array(
                        'type'         => 'color',
                        'label'        => $this->module->l('Save button text color'),
                        'name'         => GdprProConfig::SAVE_BTN_TEXT_COLOR,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                    ),
                    array(
                        'type'         => 'color',
                        'label'        => $this->module->l('Popup bg color'),
                        'name'         => GdprProConfig::POPUP_BG_COLOR,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l(
                            'Popup bg color'
                        ),
                    ),
                    array(
                        'type'         => 'color',
                        'label'        => $this->module->l('Popup text color'),
                        'name'         => GdprProConfig::POPUP_TEXT_COLOR,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l(
                            'Popup text color'
                        ),
                    ),
                    array(
                        'type'         => 'select',
                        'label'        => $this->module->l('Popup position'),
                        'name'         => GdprProConfig::POPUP_POSITION,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l(
                            'Popup position'
                        ),
                        'options'      => array(
                            'query' => array(
                                array(
                                    'id'   => 'top',
                                    'name' => $this->module->l('Top'),
                                ),
                                array(
                                    'id'   => 'bottom',
                                    'name' => $this->module->l('Bottom'),
                                ),
                            ),
                            'id'    => 'id',
                            'name'  => 'name',
                        ),
                    ),
                    array(
                        'type'         => 'select',
                        'label'        => $this->module->l('Popup style'),
                        'name'         => GdprProConfig::POPUP_STYLE,
                        'required'     => true,
                        'lang'         => false,
                        'autoload_rte' => true,
                        'hint'         => $this->module->l(
                            'Popup Style'
                        ),
                        'options'      => array(
                            'query' => array(
                                array(
                                    'id'   => 'v1',
                                    'name' => $this->module->l('Old'),
                                ),
                                array(
                                    'id'   => 'v2',
                                    'name' => $this->module->l('New'),
                                ),
                            ),
                            'id'    => 'id',
                            'name'  => 'name',
                        ),
                    ),
                    array(
                        'type'    => 'switch',
                        'name'    => GdprProConfig::UNDER_16_ENABLE,
                        'label'   => $this->module->l('Under 16 enable'),
                        'hint'    => $this->module->l(
                            'Under 16 enable'
                        ),
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'under16_enable_on',
                                'value' => '1',
                                'label' => $this->module->l('Enable'),
                            ),
                            array(
                                'id'    => 'under16_enable_off',
                                'value' => '0',
                                'label' => $this->module->l('Disable'),
                            ),
                        ),
                    ),
                    array(
                        'type'    => 'switch',
                        'name'    => GdprProConfig::POPUP_SHOW_TITLE,
                        'label'   => $this->module->l('Show popup title'),
                        'hint'    => $this->module->l(
                            'Show popup title'
                        ),
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'popup_show_title_on',
                                'value' => '1',
                                'label' => $this->module->l('Enable'),
                            ),
                            array(
                                'id'    => 'popup_show_title_off',
                                'value' => '0',
                                'label' => $this->module->l('Disable'),
                            ),
                        ),
                    ),
                    array(
                        'type'    => 'switch',
                        'name'    => GdprProConfig::CHECK_ALL_MODULES_BY_DEFAULT,
                        'label'   => $this->module->l('Check all modules by default'),
                        'hint'    => $this->module->l(
                            'Check all cookies by default'
                        ),
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'check_all_modules_by_default_on',
                                'value' => '1',
                                'label' => $this->module->l('Enable'),
                            ),
                            array(
                                'id'    => 'check_all_modules_by_default_off',
                                'value' => '0',
                                'label' => $this->module->l('Disable'),
                            ),
                        ),
                    ),
                    array(
                        'type'    => 'switch',
                        'name'    => GdprProConfig::ALLOW_ALL_MODULES_BY_DEFAULT,
                        'label'   => $this->module->l('Allow all modules by default'),
                        'hint'    => $this->module->l(
                            'Allow all cookies by default'
                        ),
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'allow_all_modules_by_default_on',
                                'value' => '1',
                                'label' => $this->module->l('Enable'),
                            ),
                            array(
                                'id'    => 'allow_all_modules_by_default_off',
                                'value' => '0',
                                'label' => $this->module->l('Disable'),
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->module->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?: 0;
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = self::$formName;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminGdprConfig', false);
        $helper->token = Tools::getAdminTokenLite($this->controller_name);
        $helper->tpl_vars = array(
            'fields_value' => GdprProConfig::getConfigurationValues(),
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function processConfiguration()
    {
        foreach ($_REQUEST as $key => $value) {
            $normalizedKey = preg_replace('/_\d{1,}$/', '', $key);
            unset($value);
            $this->saveConfigValue($normalizedKey);
        }
        Configuration::loadConfiguration();

        $this->confirmations[] = $this->module->l("Configuration updated");
    }

    public function saveConfigValue($configKey)
    {
        Configuration::updateValue(
            $configKey,
            Tools::getValue($configKey)
        );

        $languages = Language::getLanguages(false, false, false);
        foreach ($languages as $language) {
            if (Tools::getValue($configKey . "_{$language['id_lang']}", false)) {
                Configuration::updateValue(
                    $configKey,
                    array($language['id_lang'] => (string)Tools::getValue($configKey . "_" . $language['id_lang'], "")),
                    true
                );
            }
        }
    }
}
