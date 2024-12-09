<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

class AdminKbBackRecaptchaController extends ModuleAdminController
{

    //Class Constructor
    public function __construct()
    {
        $this->name = 'KbBackRecaptcha';
        $this->context = Context::getContext();
        $this->bootstrap = true;

        parent::__construct();
    }

    //Set JS and CSS
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS($this->getModuleDirUrl() . 'backinstock/views/css/admin/kb_admin.css');
        $this->addJS($this->getModuleDirUrl() . 'backinstock/views/js/recaptcha.js');
        $this->addJS($this->getModuleDirUrl() . 'backinstock/views/js/velovalidation.js');
    }

    //Function definition to render a form
    public function initContent()
    {

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('V3 Recaptcha Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enabled'),
                        'name' => 'KB_BACKINSTOCK_RECAPTCHA_ENABLE',
                        'desc' => $this->l('Toggle to enable or disable v3 Recaptcha'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'switch_value_on',
                                'value' => 1,
                                'label' => $this->l('Yes')),
                            array(
                                'id' => 'switch_value_off',
                                'value' => 0,
                                'label' => $this->l('No')),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('V3 Site Key'),
                        'desc' => $this->l('Enter V3 Site Key'),
                        'name' => 'KB_BACKINSTOCK_RECAPTCHA_SITE_KEY',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('V3 Secret Key'),
                        'desc' => $this->l('Enter V3 Secret Key'),
                        'name' => 'KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY',
                        'required' => true
                    ),
                ),
                'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right validation_google_recaptcha'
                )
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = $this->context->language->id;
        $helper->fields_value['KB_BACKINSTOCK_RECAPTCHA_ENABLE'] = Configuration::get('KB_BACKINSTOCK_RECAPTCHA_ENABLE');
        $helper->fields_value['KB_BACKINSTOCK_RECAPTCHA_SITE_KEY'] = Configuration::get('KB_BACKINSTOCK_RECAPTCHA_SITE_KEY');
        $helper->fields_value['KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY'] = Configuration::get('KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY');
        
        $this->content .= $helper->generateForm(array($fields_form));
        $validation_script = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'backinstock/views/templates/admin/velovalidation.tpl');
        $this->content .= $validation_script;
        parent::initContent();
    }
    //Function definition to handle Form Submission
    public function postProcess()
    {
        parent::postProcess();
        //Handle form submission
        if (Tools::isSubmit('submitAddconfiguration')) {
            Configuration::updateGlobalValue('KB_BACKINSTOCK_RECAPTCHA_ENABLE', Tools::getValue('KB_BACKINSTOCK_RECAPTCHA_ENABLE'));
            Configuration::updateGlobalValue('KB_BACKINSTOCK_RECAPTCHA_SITE_KEY', Tools::getValue('KB_BACKINSTOCK_RECAPTCHA_SITE_KEY'));
            Configuration::updateGlobalValue('KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY', Tools::getValue('KB_BACKINSTOCK_RECAPTCHA_SECRET_KEY'));
            Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBackRecaptcha'));
        }
    }

    private function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }

    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;

        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }

        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
}
