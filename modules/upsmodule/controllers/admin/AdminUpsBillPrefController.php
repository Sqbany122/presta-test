<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsBillPrefController extends CommonController
{
    private $links = array();

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $result = CommonFunction::checkScreenConfig();
        if ($result !== 'DONE'
            && $result !== Tools::getValue('controller')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsBillPref'));
        }

        $this->links = $this->getInformationLink();

        $this->fields_options = $this->createFieldsOption();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/upsmodule.css');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/button.js');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsBillPref');
    }

    private function createFieldsOption()
    {
        return array(
            'indexation' => array(
                'title'  => $this->sdk->t('billing', 'ttlBilling'),
                'icon'   => 'icon-credit-card',
                'info'   => $this->drawHtml(),
                'submit' => array(
                    'title'    => $this->sdk->t('button', 'txtComplete'),
                    'imgclass' => 'ok',
                    'name'     => 'submitConfiguration',
                    'id' => 'btnCompleted',
                ),
            ),
        );
    }

    private function drawHtml()
    {
        $sdk = $this->sdk;
        $getLinkDownloadRegis = Context::getContext()->link->getAdminLink('AdminUpsBillPref') . '&downloadRegis';
        $getLinkDownloadCod   = Context::getContext()->link->getAdminLink('AdminUpsBillPref') . '&downloadCod';

        $linkIndividual = $this->links['individual_shipper'];
        $linkPickupSchedule = $this->links['pickup_schedule'];
        $urlUPSsearch = Constants::SEARCH_ADDRESS_POINT_URL;

        $keys = array();
        if ($this->module->usa()) {
            $keys['txtPickupContent2'] = 'txtPickupContent2US';
            $keys['txtPickupContentSub2'] = 'txtPickupContentSub2US';
            $keys['txtPickupElemnent3'] = 'txtPickupElemnent3US';
            $keys['txtPickupElemnentSub3'] = 'txtPickupElemnentSub3US';
            $keys['txtInfo'] = 'txtInfoUS';
            $keys['txtPickupElement4'] = 'txtPickupElement4US';
            $keys['txtSearchAP'] = 'txtSearchAPUS';
        } else {
            $keys['txtPickupContent2'] = 'txtPickupContent2';
            $keys['txtPickupContentSub2'] = 'txtPickupContentSub2';
            $keys['txtPickupElemnent3'] = 'txtPickupElemnent3';
            $keys['txtPickupElemnentSub3'] = 'txtPickupElemnentSub3';
            $keys['txtInfo'] = 'txtInfo_NotForPL';
            $keys['txtPickupElement4'] = 'txtPickupElement4_NotForPL_sub';
            $keys['txtSearchAP'] = 'txtSearchAP';
        }

        $titleInfo  = $sdk->t('billing', 'txtStart');
        $desc       = $sdk->t('billing', 'txtCompleteDesc');
        $ttlFQAs    = $sdk->t('billing', 'txtFAQs');
        $content1   = $sdk->t('billing', 'txtPickupContent');
        $content1_sub   = $sdk->t('billing', 'txtPickupContentSub');
        $content2       = $sdk->t('billing', $keys['txtPickupContent2']);
        $content2_sub   = $sdk->t('billing', $keys['txtPickupContentSub2']);
        $content3       = $sdk->t('billing', $keys['txtPickupElemnent3']);
        $content3_sub   = $sdk->t('billing', $keys['txtPickupElemnentSub3']);
        $txtSearch      = $sdk->t('ups', $keys['txtSearchAP']);

        $html = "<h5 class='content-bold'>$titleInfo</h5>
        <p class='pad-paragraph'>$desc</p>
        <h5 class='content-bold'>$ttlFQAs</h5>
        <p class='pad-paragraph'>$content1</p>
        <p class='pad-paragraph'>$content1_sub</p>
        <p class='pad-paragraph'>$content2</p>
        <p class='pad-paragraph'>$content2_sub<a target='_blank' href='$linkIndividual'>$linkIndividual</a></p>
        <p class='pad-paragraph'>$content3</p>
        <p class='pad-paragraph'>$content3_sub</p>
        <p class='pad-paragraph'>
            <a target='_blank' href='$urlUPSsearch' class='btn btn-default'>$txtSearch</a></p>
        <p class='pad-paragraph'>"
        . $sdk->t('billing', 'txtInfo')
        . "<a target='_blank' href='$linkPickupSchedule'>$linkPickupSchedule</a><br><br>"
        . $this->getScheduleTexts()
        . '</p>'
        . $this->generateBtnPrintForm($getLinkDownloadRegis)
        . '<p class="pad-paragraph">' . $this->getFQAlinks() . '</p>'
        . $this->generateBtnPrintForm($getLinkDownloadCod);

        return $html;
    }

    public function initProcess()
    {
        if (Tools::getIsset('downloadRegis')) {
            $this->action = 'downloadRegis';
        }

        if (Tools::getIsset('downloadCod')) {
            $this->action = 'downloadCod';
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitConfiguration')) {
            CommonFunction::setDoneConfigScreen(Tools::getValue('controller'));
            if (Configuration::get('UPS_CONFIG_DONE') && Configuration::get('UPS_SHIPING_METHOD_ID') == -1) {
                $this->createCarrier(); // Create UPS shipping carrier
                if (Tools::version_compare(_PS_VERSION_, '1.7.4', '>=')) {
                    InitFunction::enableTab('AdminUpsAccountSuccess');
                    InitFunction::disableTab('AdminUpsAccount');
                }
            }
            
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsOpenOrders'));
        }
        parent::postProcess();
    }

    public function processDownloadRegis()
    {
        $iso_code = $this->context->language->iso_code;
        $fileCodForm = PATH_ASSETS_FOLDER . Constants::NAME_PICKUP_PU_FORM . $iso_code . '.pdf';

        if (file_exists($fileCodForm)) {
            $this->getHeader($fileCodForm);
        } else {
            $fileCodForm = PATH_ASSETS_FOLDER . Constants::NAME_PICKUP_PU_FORM . 'pl.pdf';
            if (file_exists($fileCodForm)) {
                $this->getHeader($fileCodForm);
            }
        }
    }

    public function processDownloadCod()
    {
        $iso_code = $this->context->language->iso_code;
        $fileCodForm = PATH_ASSETS_FOLDER . Constants::NAME_COD_PU_FORM . $iso_code . '.pdf';

        if (file_exists($fileCodForm)) {
            $this->getHeader($fileCodForm);
        } else {
            $fileCodForm = PATH_ASSETS_FOLDER . Constants::NAME_COD_PU_FORM . 'pl.pdf';
            if (file_exists($fileCodForm)) {
                $this->getHeader($fileCodForm);
            }
        }
    }

    public function getHeader($fileCodForm)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fileCodForm) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileCodForm));
        readfile($fileCodForm);
    }

    private function getScheduleTexts()
    {
        switch ($this->module->pluginCountryCode) {
            case Constants::POLAND_ISO:
                return $this->sdk->t('billing', 'txtInfoPL');
            case Constants::UNITED_STATE_ISO:
                return $this->sdk->t('billing', 'txtInfoUS');
            default:
                return $this->sdk->t(
                    'billing',
                    'txtInfo_NotForPL',
                    array('contact_page' => $this->links['contact_page'])
                );
        }
    }

    private function getFQAlinks()
    {
        switch ($this->module->pluginCountryCode) {
            case Constants::POLAND_ISO:
                $txt1 = $this->sdk->t('billing', 'txtPickupElement4PL');
                $txt2 = $this->sdk->t('billing', 'txtPickupElement4PL_sub');
                return "<p class='pad-paragraph'>$txt1</p><p class='pad-paragraph'>$txt2</p>";
            case Constants::UNITED_STATE_ISO:
                $txt1 = $this->sdk->t('billing', 'txtPickupElement4_NotForPL');
                $txt2 = $this->sdk->t('billing', 'txtPickupElement4US');
                return "<p class='pad-paragraph'>$txt1</p><p class='pad-paragraph'>$txt2</p>";
            
            default:
                $txt1 = $this->sdk->t('billing', 'txtPickupElement4_NotForPL');
                $txt2 = $this->sdk->t(
                    'billing',
                    'txtPickupElement4_NotForPL_sub',
                    array('fqa_link' => $this->links['contact_page'])
                );
                return "<p class='pad-paragraph'>$txt1</p><p class='pad-paragraph'>$txt2</p>";
        }
    }

    private function generateBtnPrintForm($link)
    {
        switch ($this->module->pluginCountryCode) {
            case Constants::POLAND_ISO:
                return '<p class="pad-paragraph"><a class="btn btn-default" href="' . $link . '">'
                    . $this->sdk->t('billing', 'txtPrintForm') . '</a></p>';
            
            default:
                return '';
        }
    }

    private function createCarrier()
    {
        $langDefault = Configuration::get('PS_LANG_DEFAULT');

        $carrier                       = new Carrier();
        $carrier->name                 = 'UPS SHIPPING';
        $carrier->active               = Module::isEnabled($this->module->name);
        $carrier->deleted              = 0;
        $carrier->shipping_handling    = false;
        $carrier->range_behavior       = 0;
        $carrier->shipping_method      = Carrier::SHIPPING_METHOD_PRICE;
        $carrier->delay[$langDefault]  = '  '; //Need handle by APIs
        $carrier->shipping_external    = true;
        $carrier->is_module            = true;
        $carrier->external_module_name = $this->module->name;
        $carrier->need_range           = true;
        $carrier->is_free              = false;

        if ($carrier->add()) {
            // Set Gruops for carrier
            $groups = Group::getGroups(true);
            $arrGroupId = array_column($groups, 'id_group');
            $carrier->setGroups($arrGroupId);

            // Set Zones for carrier
            $zones = Zone::getZones(true);
            foreach ($zones as $zone) {
                Db::getInstance()->insert(
                    'carrier_zone',
                    array(
                        'id_carrier' => (int) $carrier->id,
                        'id_zone'    => (int) $zone['id_zone'],
                    )
                );
            }

            // Add Range Price
            $rangePrice             = new RangePrice();
            $rangePrice->id_carrier = $carrier->id;
            $rangePrice->delimiter1 = '0';
            $rangePrice->delimiter2 = '1000000';
            $rangePrice->add();

            // Add logo for carrier
            $pathLogoModule = _PS_MODULE_DIR_ . $this->module->name . '/views/img/' . 'ups_logo_40.jpg';
            $pathShipImg = _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg';
            copy($pathLogoModule, $pathShipImg);

            Configuration::updateValue('PS_CARRIER_DEFAULT', $carrier->id);
            Configuration::updateValue('UPS_SHIPING_METHOD_ID', $carrier->id);
            Configuration::updateValue('UPS_SHIPING_METHOD_REFERENCE_ID', $carrier->id);
        }

        return true;
    }
}
