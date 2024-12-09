<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsCountryController extends CommonController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        if (Configuration::get('UPS_TC_AGREED')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsAccount'));
        }

        $this->show_form_cancel_button = false;
        $this->fields_form = $this->createForm();
        $this->fields_value = array(
            'UPS_COUNTRY_SELECTED' => Configuration::get('UPS_COUNTRY_SELECTED'),
        );
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsCountry');
    }

    public function initContent()
    {
        $this->display = 'edit';
        parent::initContent();
    }

    public function createForm()
    {
        return array(
            'legend' => array(
                'title' => $this->sdk->t('ups', 'country_setting'),
                'icon'  => 'icon-globe',
            ),
            'input'  => array(
                array(
                    'type'    => 'select',
                    'label'   => $this->sdk->t('ups', 'country_selected'),
                    'name'    => 'UPS_COUNTRY_SELECTED',
                    'class'   => 'fixed-width-sm',
                    'options' => array(
                        'query' => $this->processCountriesList(),
                        'id'    => 'id_configuration',
                        'name'  => 'name',
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->sdk->t('button', 'txtCon'),
                'name'  => 'submitCountry',
                'icon'  => 'process-icon-next',
            ),
        );
    }

    private function processCountriesList()
    {
        $tmp = array();

        foreach ($this->module->pluginCountryList as $isoCode => $country) {
            $tmp[] = array(
                'id_configuration' => $isoCode,
                'name' => $country
            );
        }

        return $tmp;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitCountry') && Tools::getIsset('UPS_COUNTRY_SELECTED')) {
            $country = Tools::getValue('UPS_COUNTRY_SELECTED');
            Configuration::updateValue('UPS_COUNTRY_SELECTED', $country);
            Configuration::updateValue('UPS_READY_TO_GET_TC', 1);
            $count = 0;
            $this->getTermAndCondition($country, $count);

            return Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsTermCondition'));
        }
    }

    private function getTermAndCondition($country, $count = 0)
    {
        $token = $this->module->getPreTokenKey();
        if (empty($token)) {
            if ($count > 3) {
                $licenseRes['Content'] = $this->sdk->t('ups', 'cannot_get');
            } else {
                $this->module->doHandShake();
                $this->getTermAndCondition($country, $count);
            }
            $count++;
        } else {
            $this->licenseApi = new PluginManager\ToolApi\License();
            $this->licenseApi->setAccessLicenseAgreementRequest();
            $client = $this->licenseApi;
            $licenseRes = $client(array(
                'CountryCode'  => $country,
                'LanguageCode' => Tools::strtoupper($this->context->language->iso_code),
                'preToken' => $token,
                'sdk' => $this->sdk
            ));
            $licenseIsoCode = '';
            if (!empty(Tools::strtoupper($this->context->language->iso_code))) {
                $licenseIsoCode = pSQL(Tools::strtoupper($this->context->language->iso_code));
            }
            if (!empty($licenseRes['Content'])) {
                $licenseContent = pSQL($licenseRes['Content']);
                Db::getInstance()->insert(
                    'ups_data',
                    array(
                        array('key_name' => 'Term', 'ups_value' => $licenseContent),
                        array('key_name' => 'Country', 'ups_value' => pSQL($country)),
                        array(
                            'key_name' => 'Language',
                            'ups_value' => $licenseIsoCode
                        )
                    )
                );
            }
        }
    }
}
