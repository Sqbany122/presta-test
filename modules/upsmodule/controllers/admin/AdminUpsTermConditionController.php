<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsTermConditionController extends CommonController
{
    private $dataAgreement;
    private $currentLang;
    private $currentCountry;
    private $links;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        if (!Configuration::get('UPS_READY_TO_GET_TC')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsCountry'));
        }

        if (Configuration::get('UPS_TC_AGREED')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsAccount'));
        }

        $this->show_form_cancel_button = false;

        $this->links = $this->getInformationLink();
    }

    public function initContent()
    {
        $this->display = 'edit';
        $this->fields_form = $this->createFieldsForm();
        $this->fields_value = $this->createFieldsValue();
        parent::initContent();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upstermcondition.js');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsTermCondition');
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['print_Term'] = array(
            'desc' => $this->sdk->t('button', 'txtPrint'),
            'icon' => 'process-icon-duplicate',
            'js'   => 'printTerm();',
        );

        parent::initPageHeaderToolbar();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('continueSubmit')) {
            if (Tools::getIsset('checkbox_agreed_checkbox1')
                && Tools::getValue('checkbox_agreed_checkbox1') == 'checked') {
                Configuration::updateValue('UPS_TC_AGREED', 1);
                $this->setupListTab();
                $this->addShippingServicesToConfig();
                $this->addAccessorialToConfig();
                return Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsAccount'));
            }
        }

        parent::postProcess();
    }

    private function addShippingServicesToConfig()
    {
        $arrDefaultRate = array(
            array(
                'MinValue' => 0,
                'DeliRate' => 0,
            ),
        );

        $services = $this->module::$shippingServices->getShippingServices();

        foreach ($services as $service) {
            Configuration::updateValue($service['key'], 0);
            Configuration::updateValue($service['keyDeli'], 'FLAT_RATE');
            Configuration::updateValue($service['keyVal'], serialize($arrDefaultRate));
        }
    }

    private function addAccessorialToConfig()
    {
        foreach ($this->module::$accessorials as $accessorial) {
            Configuration::updateValue($accessorial['key'], 0);
        }
    }

    private function setupListTab()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            InitFunction::enableTab('AdminUpsShipmentParent');
            InitFunction::enableTab('AdminUpsShipmentManage');
            InitFunction::enableTab('AdminUpsConfig');
            InitFunction::enableTab('AdminUpsShipping');
            InitFunction::enableTab('AdminUpsAbout');
        }

        InitFunction::enableTab('AdminUpsArchivedOrders');
        InitFunction::enableTab('AdminUpsShipments');
        InitFunction::enableTab('AdminUpsOpenOrders');
        InitFunction::enableTab('AdminUpsSecurity');
        InitFunction::enableTab('AdminUpsBillPref');
        InitFunction::enableTab('AdminUpsDeliveryRates');
        InitFunction::enableTab('AdminUpsPkgDimension');
        InitFunction::enableTab('AdminUpsCod');
        InitFunction::enableTab('AdminUpsShippingServices');
        InitFunction::enableTab('AdminUpsAccount');
        InitFunction::enableTab('AdminUpsAbout');

        InitFunction::disableTab('AdminUpsCountry');
        InitFunction::disableTab('AdminUpsTermCondition');
    }

    private function createFieldsForm()
    {
        $fieldsForm = array(
            'legend' => array(
                'title' => $this->sdk->t('ups', 'terms_conditions'),
            ),
            'input'  => array(
                array(
                    'type'     => 'textarea',
                    'label'    => $this->sdk->t('ups', 'terms_conditions'),
                    'name'     => 'UPS_MODULE_TERM_CONDITION',
                    'readonly' => true,
                    'cols'     => 40,
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'UPS_MODULE_FLAG_CHECK_ERROR',
                ),
                array(
                    'type'   => 'checkbox',
                    'name'   => 'checkbox_agreed',
                    'lang'   => true,
                    'values' => array(
                        'query' => array(
                            array(
                                'id'   => 'checkbox1',
                                'val'  => 'checked',
                                'name' => $this->getAccepted(),
                            ),
                        ),
                        'id'    => 'id',
                        'name'  => 'name',
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->sdk->t('button', 'txtCon'),
                'icon'  => 'process-icon-next',
                'name'  => 'continueSubmit',
            ),
        );

        return $fieldsForm;
    }

    private function createFieldsValue()
    {
        return array(
            'UPS_MODULE_TERM_CONDITION' => $this->getTermAndCondition(),
            'UPS_MODULE_FLAG_CHECK_ERROR' => ''
        );
    }

    private function getTermAndCondition()
    {
        $txt = Db::getInstance()->getValue(
            "SELECT `ups_value`
            FROM `" . _DB_PREFIX_ . "ups_data`
            WHERE `key_name` = 'Term';"
        );

        if (in_array($this->module->lang, array('de', 'it', 'nl', 'be'))) {
            return iconv("UTF-8", "ISO-8859-1", $txt);
        } elseif ($this->module->lang == 'fr' && Configuration::get('UPS_COUNTRY_SELECTED') != 'FR') {
            return iconv("UTF-8", "ISO-8859-1", $txt); // Except fr-FR
        }

        return $txt;
    }

    public function ajaxProcessPrintTerm()
    {
        $contentTermCondition = array(
            'title_Term'   => $this->sdk->t('ups', 'terms_conditions'),
            'content_Term' => $this->getTermAndCondition()
        );
        $this->ajaxDie(json_encode($contentTermCondition));
    }

    public function initModal()
    {
        $myfile = fopen(_PS_MODULE_DIR_ . $this->module->name . '/assets/termcondition.txt', "r");
        $texts = array(
            'txtPopupTerm' => fread(
                $myfile,
                filesize(_PS_MODULE_DIR_ . $this->module->name . '/assets/termcondition.txt')
            ),
        );
        fclose($myfile);
        $this->context->smarty->assign(
            array(
            'arrtext' => $texts,
            'view_dir' => _MODULE_DIR_ . $this->module->name . '/views'
            )
        );
        $this->modals[] = array(
            'modal_id'      => 'modalShowTerm',
            'modal_class'   => 'modal-md',
            'modal_title'   => $this->sdk->t('ups', 'terms_conditions'),
            'modal_content' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/ups_terms_conditions/modalShowTerm.tpl'
            ),
        );
    }

    private function getAccepted()
    {
        $sdk = $this->sdk;
        switch ($this->module->pluginCountryCode) {
            case Constants::POLAND_ISO:
                return $sdk->t('ups', 'i_read_agreedPL', array('terms_prestashop' => $this->links['terms_prestashop']));

            default:
                return $sdk->t('ups', 'i_read_agreed');
        }
    }
}
