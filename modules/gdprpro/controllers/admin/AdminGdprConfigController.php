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
 *
 * @property GdprPro $module
 */
class AdminGdprConfigController extends ModuleAdminController
{
    public $bootstrap = true;
    private static $formName  = "gdpr-config-form";

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_title = $this->l('GDPR Configuration');
        $this->toolbar_title = $this->l('GDPR Configuration');
    }

    public function initToolbar()
    {
        $this->page_header_toolbar_btn['gdpr_manage_modules'] = array(
            'short' => $this->l('Back to config'),
            'href' => $this->context->link->getAdminLink('AdminModuleHooks'),
            'desc' => $this->l('GDPR enabled modules'),
            'class' => 'icon-user-secret',
        );

        $this->page_header_toolbar_btn['gdpr_load_configuration'] = array(
            'short' => $this->l('Load default configuration for the module'),
            'href' => $this->context->link->getAdminLink('AdminGdprConfig') . "&debug=true",
            'desc' => $this->l('Load configuration'),
            'class' => 'icon-edit',
        );
    }

    /**
     * @throws SmartyException
     */
    public function initContent()
    {
        if (Tools::getValue("debug") == "true") {
            try {
                $this->module->setDefaultConfigValues(true);
            } catch (Exception $exception) {
                $this->errors[] = $exception->getMessage();
            }
            $this->warnings[] = "Module settings was reset";
        }
        if (Tools::isSubmit(self::$formName)) {
            $this->processConfiguration();
        }
        

        $this->context->controller->addJS(
            $this->module->getLocalPath() . '/views/js/admin/admin-tabs.js'
        );
        $this->context->controller->addCSS(
            $this->module->getLocalPath() . '/views/css/admin/admin-tabs.css'
        );

        $this->context->smarty->assign(array(
            'moduleVersion' => $this->module->version,
            'localPath'     => Media::getCSSPath($this->module->getLocalPath(), false, false)
        ));

        $tabs = array(
            array(
                'id'         	=> 'text-configuration',
                'title'      	=> $this->l('GDPR Text configuration'),
                'content'    	=> $this->renderTextConfigurationForm(),
                'isActive'      => true,
                'icon'       	=> 'icon-wrench',
            ),
            array(
                'id'         	=> 'cookie-policy',
                'title'      	=> $this->l('Cookie policy'),
                'content'    	=> $this->renderCookiePolicyForm(),
                'isActive'      => false,
                'icon'       	=> 'icon-file',
            ),
            array(
                'id'         	=> 'privacy-button-configuration',
                'title'      	=> $this->l('Privacy Consent button configuration'),
                'content'    	=> $this->renderPrivacyConsentButtonConfigurationForm(),
                'isActive'      => false,
                'icon'       	=> 'icon-minus',
            ),
            array(
                'id'         	=> 'forms-consent-configuration',
                'title'      	=> $this->l('Forms consent configuration'),
                'content'    	=> $this->renderFormsConsentConfigurationForm(),
                'isActive'      => false,
                'icon'       	=> 'icon-dot-circle-o',
            ),
            array(
                'id'         	=> 'pop-up-configuration',
                'title'      	=> $this->l('GDPR Pop-up configuration'),
                'content'    	=> $this->renderPopupConfigurationForm(),
                'isActive'      => false,
                'icon'       	=> 'icon-newspaper-o',
            ),
            array(
                'id'         	=> 'pop-up-customization',
                'title'      	=> $this->l('GDPR Pop-up customization'),
                'content'    	=> $this->renderPopupCustomizationForm(),
                'isActive'      => false,
                'icon'       	=> 'icon-paint-brush',
            ),
            array(
                'aHref' 		=> $this->context->link->getAdminLink('AdminGdprCustomScripts'),
                'id'       		=> 'custom-scipts',
                'title'    		=> $this->l('GDPR Custom scripts'),
                'content'       => false,
                'isActive'      => false,
                'icon'     		=> 'icon-file-code-o',
            ),
            array(
                'aHref' 		=> $this->context->link->getAdminLink('AdminDataRequests'),
                'id'       		=> 'data-requests',
                'title'    		=> $this->l('Data Request'),
                'content'       => false,
                'isActive'      => false,
                'icon'     		=> 'icon-user',
            ),
            array(
                'aHref' 		=> $this->context->link->getAdminLink('AdminModuleHooks'),
                'id'       		=> 'manage-modules',
                'title'    		=> $this->l('Manage modules'),
                'content'       => false,
                'isActive'      => false,
                'icon'     		=> 'icon-list-alt',
            ),
            array(
                'id'         	=> 'banner',
                'title'      	=> $this->l('Documentation'),
                'content'    	=> $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/banner.tpl'),
                'isActive'      => false,
                'icon'       	=> 'icon-file-text',
            ),
        );
        
        $this->context->smarty->assign('configTabs', $tabs);
        $this->content .= $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/configure.tpl');
        
        parent::initContent();
    }

    public function renderTextConfigurationForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('GDPR Text configuration'),
                    'icon' 	=> 'icon-wrench',
                ),
                'description' => $this->l('In this section, you will be able to add and modify the text in the GDPR Pop-up. Please provide all the information necessary for your clients to be able to decide if they want to give their consent or not.'),
                'input' => array(
                    array(
                        'type' 		=> 'text',
                        'label' 	=> $this->l('Welcome tab title'),
                        'name' 		=> GdprProConfig::TAB_NAME_WELCOME,
                        'required' 	=> true,
                        'lang' 		=> true,
                        'hint' 		=> $this->l('This is the title of the main tab which is open by
                        default whenever someone accesses your site for the first time'),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('Welcome text'),
                        'name' 			=> GdprProConfig::TAB_TEXT_WELCOME,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l('This is the content of the default tab and
                        should be used to describe why certain information is collected on the site'),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('Necessary tab text'),
                        'name' 			=> GdprProConfig::TAB_TEXT_NECESSARY,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l('A description of the cookies which are necessary for
                        the website to function correctly and, if needed, a warning that disabling them through the
                        browser settings can make certain areas or functions of the site unusable'),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('Performance tab text'),
                        'name' 			=> GdprProConfig::TAB_TEXT_PREFERENCES,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l('This tab will contain the list of the modules
                        visitors can choose to disable before continuing on your site. The text entered here will be
                        displayed below the list of modules and should contain a general description of how these
                        modules track information and what are the benefits of it. '),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('Statistics tab text'),
                        'name' 			=> GdprProConfig::TAB_TEXT_STATISTICS,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l('This tab will contain the list of the modules
                        visitors can choose to disable before continuing on your site. The text entered here will be
                        displayed below the list of modules and should contain a general description of how these
                        modules track information and what are the benefits of it. '),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('Marketing tab text'),
                        'name' 			=> GdprProConfig::TAB_TEXT_MARKETING,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l('This tab will contain the list of the modules
                        visitors can choose to disable before continuing on your site. The text entered here will be
                        displayed below the list of modules and should contain a general description of how these
                        modules track information and what are the benefits of it. '),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('Unclassified tab text'),
                        'name' 			=> GdprProConfig::TAB_TEXT_UNCLASSIFIED,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l('Unclassified cookies are cookies that we are in
                        the process of classifying, together with the providers of individual cookies.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
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
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }
    
    
    public function renderCookiePolicyForm()
    {
        $cmsList = CMS::getLinks($this->context->language->id);
        $defaultValue =  array(array(
            'id_cms' => '#',
            'meta_title' => '#'
        ));
        
        $cmsList = array_merge($defaultValue, $cmsList);
        
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Cookie policy'),
                    'icon' 	=> 'icon-file',
                ),
                'description' => $this->l('At this tab, you can add your Cookie Policy page to the GDPR pop-up. If you haven\'t got a Cookie Policy page, you need to create it at the CMS page section.'),
                'input' => array(
                    array(
                        'type' 		=> 'text',
                        'label' 	=> $this->l('Additional link tab title'),
                        'name' 		=> GdprProConfig::TAB_NAME_LINK,
                        'required' 	=> true,
                        'lang' 		=> true,
                        'hint' 		=> $this->l(
                            'Usually the title of your Cookie Policy or Privacy
                        Policy page. When clicking on this tab, users will be taken to the URL specified below'
                        ),
                    ),
                    /* [
                        'type' 		=> 'text',
                        'label'		=> $this->l('Additional link target'),
                        'name' 		=> GdprProConfig::TAB_CONTENT_LINK,
                        'required' 	=> true,
                        'lang' 		=> true,
                        'hint' 		=> $this->l('The link to your Cookie or Privacy Policy page'),
                    ], */
                    array(
                        'type' 		=> 'select',
                        'label' 	=> $this->l('Additional link target'),
                        'name' 		=> GdprProConfig::TAB_CONTENT_PAGE_ID,
                        'required' 	=> true,
                        'lang' 		=> false,
                        'hint' 		=> $this->l('The link to your Cookie or Privacy Policy page'),
                        'options' => array(
                            'query' => $cmsList,
                            'id' 	=> 'id_cms',
                            'name'	=> 'meta_title',
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
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
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }
    
    
    public function renderPrivacyConsentButtonConfigurationForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Privacy Consent button configuration'),
                    'icon' 	=> 'icon-minus',
                ),
                'description' => $this->l('In this section, you can customize the "Control your Privacy" button which appears in the footer. This button is useful for users who have given their consent but want to change the decision, the GDPR pop-up will be reopened once the button is clicked.'),
                'input' => array(
                    array(
                        'type' 		=> 'text',
                        'label' 	=> $this->l('Footer link text'),
                        'name' 		=> GdprProConfig::FOOTER_LINK_TEXT,
                        'required' 	=> true,
                        'lang' 		=> true,
                        'hint' 		=> $this->l(
                            'A link will be created in your website\'s footer which
                        allows visitors to reopen the pop-up and further customize their tracking preferences.
                        This is the title of that link'
                        ),
                    ),
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Footer link background color'),
                        'name' 		=> GdprProConfig::FOOTER_LINK_BG_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                    ),
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Footer link text color'),
                        'name' 		=> GdprProConfig::FOOTER_LINK_TEXT_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                    ),
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Footer link border color'),
                        'name' 		=> GdprProConfig::FOOTER_LINK_BORDER_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
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
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }
    
    
    public function renderFormsConsentConfigurationForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Forms consent configuration'),
                    'icon' 	=> 'icon-dot-circle-o',
                ),
                'description' => $this->l('At this section, you can enable/disable consent forms from the sign-up, my account, contact, and newsletter section.'),
                'input' => array(
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::CONSENT_CHKBOX_SIGNUP_ENABLE,
                        'label' 	=> $this->l('Enable signup form consent'),
                        'hint' 		=> $this->l('A checkbox will be added to the signup form with a checkbox'),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'signupconsentchkbox_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'signupconsentchkbox_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('Signup form message'),
                        'name' 			=> GdprProConfig::CONSENT_CHKBOX_SIGNUP_TEXT,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l(
                            'This message will be added to the signup form with a checkbox'
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::CONSENT_CHKBOX_MYACCOUNT_ENABLE,
                        'label' 	=> $this->l('Enable my account form consent'),
                        'hint' 		=> $this->l(
                            'A checkbox will be added to the my account form with a checkbox'
                        ),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'signupconsentchkbox_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'signupconsentchkbox_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('MyAccount form message'),
                        'name' 			=> GdprProConfig::CONSENT_CHKBOX_MYACCOUNT_TEXT,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l(
                            'This message will be added to the my account form with a checkbox'
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::CONSENT_CHKBOX_CONTACT_ENABLE,
                        'label' 	=> $this->l('Enable contact form consent'),
                        'hint' 		=> $this->l(
                            'A checkbox will be added to the contact form with a checkbox'
                        ),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'contactconsentchkbox_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'contactconsentchkbox_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('Contact consent form message'),
                        'name' 			=> GdprProConfig::CONSENT_CHKBOX_CONTACT_TEXT,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l(
                            'This message will be added to the contact form with a checkbox'
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::CONSENT_CHKBOX_NEWSLETTER_ENABLE,
                        'label' 	=> $this->l('Enable newsletter form consent'),
                        'hint' 		=> $this->l(
                            'A checkbox will be added to the newsletter form with a checkbox'
                        ),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'newsletterconsentchkbox_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'newsletterconsentchkbox_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 			=> 'textarea',
                        'label' 		=> $this->l('Newsletter consent form message'),
                        'name' 			=> GdprProConfig::CONSENT_CHKBOX_NEWSLETTER_TEXT,
                        'required' 		=> true,
                        'lang' 			=> true,
                        'autoload_rte' 	=> true,
                        'hint' 			=> $this->l(
                            'This message will be added to the newsletter form with a checkbox'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
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
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }
    
    public function renderPopupConfigurationForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('GDPR Pop-up configuration'),
                    'icon' 	=> 'icon-newspaper-o',
                ),
                'description' => $this->l('At this section, you can enable/disable the custom functions of the module.'),
                'input' => array(
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::UNDER_16_ENABLE,
                        'label' 	=> $this->l('Under 16 enable'),
                        'hint' 		=> $this->l(
                            'Under 16 enable'
                        ),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'under16_enable_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'under16_enable_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::CHECK_ALL_MODULES_BY_DEFAULT,
                        'label' 	=> $this->l('Check all modules by default'),
                        'hint' 		=> $this->l(
                            'Check all cookies by default'
                        ),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'check_all_modules_by_default_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'check_all_modules_by_default_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::ALLOW_ALL_MODULES_BY_DEFAULT,
                        'label' 	=> $this->l('Allow all modules by default'),
                        'hint' 		=> $this->l(
                            'Allow all cookies by default'
                        ),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'allow_all_modules_by_default_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'allow_all_modules_by_default_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::RELOAD_AFTER_BTN_SAVE,
                        'label' 	=> $this->l('Reload the page after `Save`'),
                        'hint' 		=> $this->l('Reload the page after the user clicks the `Save` button'),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'reload-after-btn-save_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'reload-after-btn-save_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::RELOAD_AFTER_BTN_ACCEPT_ALL,
                        'label' 	=> $this->l('Reload the page `Accept all`'),
                        'hint' 		=> $this->l('Reload the page after the user clicks the `Accept all` button'),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'reload-after-btn-accept-all_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'reload-after-btn-accept-all_off',
                                'value' => '0',
                            ),
                        ),
                    ),

                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::ENABLE_COOKIE_ACCEPT_CONSENT_LOG,
                        'label' 	=> $this->l('Log cookie accept activities'),
                        'hint' 		=> $this->l('If you disable this feature the cookie accept consents won\'t be logged'),
                        'is_bool' 	=> true,
                        'values'	=> array(
                            array(
                                'id' 	=> 'enable_cookie_accept_consent_log_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'enable_cookie_accept_consent_log_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::UNDER_18_ENABLE,
                        'label' 	=> $this->l('Under 18 enable'),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'under_18_enable_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'under_18_enable_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'text',
                        'label' 	=> $this->l('Under 18 alert text'),
                        'name' 		=> GdprProConfig::UNDER_18_ALERT_TEXT,
                        'required' 	=> true,
                        'lang' 		=> true,
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::CLOSE_MODAL_ONLY_WITH_BUTTONS,
                        'hint' 		=> $this->l('Prevent closing the GDPR modal when the customer clicks outside the modal'),
                        'label' 	=> $this->l('Close modal only with buttons'),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'close_modal_only_with_buttons_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'close_modal_only_with_buttons_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::ENABLE_SCROLL_LOCK,
                        'hint' 		=> $this->l('Prevent scrolling on the site if the user did not accepted the cookies'),
                        'label' 	=> $this->l('Enable scroll lock'),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'scroll_lock_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'scroll_lock_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::ENABLE_ACCEPT_BY_SCROLL,
                        'hint' 		=> $this->l('Accept cookies by scroll'),
                        'label'		=> $this->l('Enable accept by scroll'),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'accept_by_scroll_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'accept_by_scroll_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::HIDE_POPUP_FOR_BOTS,
                        'hint' 		=>
                            $this->l('This option when enabled will hide the GDPR popup and the contents within from search engines bots and crawlers, for example: Google, Bing'),
                        'label' 	=> $this->l('Hide popup for bots/crawlers'),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'hide_popup_for_bots_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'hide_popup_for_bots_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::ENABLE_DO_NOT_TRACK_COMPLIANCE,
                        'hint' 		=>
                            $this->l('In case a browser is set to DoNotTrack mode, the popup will not appear since the browser should not allow tracking cookies. Please note that this is not guaranteed and showing the pop-up in all cases is still recommended.'),
                        'label' 	=> $this->l('Enable do not track compliance'),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'do-not-track_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'do-not-track_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
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
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }
    
    
    public function renderPopupCustomizationForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('GDPR Pop-up customization'),
                    'icon' 	=> 'icon-paint-brush',
                ),
                'description' => $this->l('In this tab, you can make the pop-up design customizations this way the GDPR pop-up will suit your site design.'),
                'input' => array(
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Accept all button background color'),
                        'name' 		=> GdprProConfig::ACCEPT_ALL_BTN_BG_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                    ),
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Accept all button text color'),
                        'name' 		=> GdprProConfig::ACCEPT_ALL_BTN_TEXT_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::REJECT_ALL_BTN_SHOW,
                        'label' 	=> $this->l('Show reject all'),
                        'hint' 		=> $this->l(
                            'Show reject all cookies button'
                        ),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'reject_all_button_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'reject_all_button_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Reject all button background color'),
                        'name' 		=> GdprProConfig::REJECT_ALL_BTN_BG_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                    ),
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Reject all button text color'),
                        'name' 		=> GdprProConfig::REJECT_ALL_BTN_TEXT_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                    ),
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Save button background color'),
                        'name' 		=> GdprProConfig::SAVE_BTN_BG_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                    ),
                    array(
                        'type' 		=> 'color',
                        'label'		=> $this->l('Save button text color'),
                        'name' 		=> GdprProConfig::SAVE_BTN_TEXT_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                    ),
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Popup bg color'),
                        'name' 		=> GdprProConfig::POPUP_BG_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                        'hint' 		=> $this->l(
                            'Popup bg color'
                        ),
                    ),
                    array(
                        'type' 		=> 'color',
                        'label' 	=> $this->l('Popup text color'),
                        'name' 		=> GdprProConfig::POPUP_TEXT_COLOR,
                        'required' 	=> true,
                        'lang' 		=> false,
                        'hint' 		=> $this->l(
                            'Popup text color'
                        ),
                    ),
                    array(
                        'type' 		=> 'select',
                        'label' 	=> $this->l('Popup position'),
                        'name' 		=> GdprProConfig::POPUP_POSITION,
                        'required' 	=> true,
                        'lang' 		=> false,
                        'hint' 		=> $this->l(
                            'Popup position'
                        ),
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' 	=> 'top',
                                    'name' 	=> $this->l('Top'),
                                ),
                                array(
                                    'id' 	=> 'middle',
                                    'name' 	=> $this->l('Middle'),
                                ),
                                array(
                                    'id' 	=> 'bottom',
                                    'name' 	=> $this->l('Bottom'),
                                ),
                            ),
                            'id' 	=> 'id',
                            'name'	=> 'name',
                        ),
                    ),
                    array(
                        'type' 		=> 'switch',
                        'name' 		=> GdprProConfig::POPUP_SHOW_TITLE,
                        'label' 	=> $this->l('Show popup title'),
                        'hint' 		=> $this->l(
                            'Show popup title'
                        ),
                        'is_bool' 	=> true,
                        'values' 	=> array(
                            array(
                                'id' 	=> 'popup_show_title_on',
                                'value' => '1',
                            ),
                            array(
                                'id' 	=> 'popup_show_title_off',
                                'value' => '0',
                            ),
                        ),
                    ),
                    array(
                        'type'      => 'color',
                        'label'     => $this->l('Popup overlay background color'),
                        'name'      => GdprProConfig::POPUP_OVERLAY_BACKGROUND_COLOR,
                        'required'  => true,
                        'lang'      => false,
                        'hint'      => $this->l(
                            'Popup background color'
                        ),
                    ),
                    array(
                        'type'      => 'select',
                        'label'     => $this->l('Popup overlay background opacity'),
                        'name'      => GdprProConfig::POPUP_OVERLAY_BACKGROUND_OPACITY,
                        'required'  => true,
                        'lang'      => false,
                        'hint'      => $this->l(
                            'Popup background opacity'
                        ),
                        'options' => array(
                            'query' => array(
                                array(
                                    'id'    => '0',
                                    'name'  => $this->l('0'),
                                ),
                                array(
                                    'id'    => '0.1',
                                    'name'  => $this->l('0.1'),
                                ),
                                array(
                                    'id'    => '0.2',
                                    'name'  => $this->l('0.2'),
                                ),
                                array(
                                    'id'    => '0.3',
                                    'name'  => $this->l('0.3'),
                                ),
                                array(
                                    'id'    => '0.4',
                                    'name'  => $this->l('0.4'),
                                ),
                                array(
                                    'id'    => '0.5',
                                    'name'  => $this->l('0.5'),
                                ),
                                array(
                                    'id'    => '0.6',
                                    'name'  => $this->l('0.6'),
                                ),
                                array(
                                    'id'    => '0.7',
                                    'name'  => $this->l('0.7'),
                                ),
                                array(
                                    'id'    => '0.8',
                                    'name'  => $this->l('0.8'),
                                ),
                                array(
                                    'id'    => '0.9',
                                    'name'  => $this->l('0.9'),
                                ),
                                array(
                                    'id'    => '1',
                                    'name'  => $this->l('1'),
                                ),
                                
                            ),
                            'id'    => 'id',
                            'name'  => 'name',
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
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
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
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

        $this->confirmations[] = $this->l("Configuration updated");
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
