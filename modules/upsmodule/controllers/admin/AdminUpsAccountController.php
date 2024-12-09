<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsAccountController extends CommonController
{
    const PICKUP_ADDRESS_CANDIDATE = '9580101';

    private static $hasErr;
    private static $optChoose;
    private static $errs = array();
    private static $selectedStateCode = "";
    private $links = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table     = 'configuration';
        parent::__construct();

        $result = CommonFunction::checkScreenConfig();
        if ($result !== 'DONE' &&
            $result !== Tools::getValue('controller')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink($result));
        }

        if (Configuration::get('UPS_ACCOUNT_EXIST')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsAccountSuccess'));
        }

        $this->links = $this->getInformationLink();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upsaccount.js');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsAccount');
    }

    public function initContent()
    {
        $sdk = $this->sdk;
        if ($this->module->usa()) { //apply only for US
            $txtLatestInvoice = $sdk->t('account', 'txtLatestInvoiceUS');
        } else {
            $txtLatestInvoice = $sdk->t('account', 'txtLatestInvoice');
        }
        $varTpl = array(
            'txtAccount'               => $sdk->t('account', 'txtAccount'),
            'txtUPSPlugin'             => $sdk->t('account', 'txtUPSPlugin'),
            'txtTitle'                 => $sdk->t('account', 'txtTitle'),
            'txtFullName'              => $sdk->t('account', 'txtFullName'),
            'txtCompany'               => $sdk->t('account', 'txtCompany'),
            'txtEmail'                 => $sdk->t('colname', 'txtEmail'),
            'txtPhoneNumber'           => $sdk->t('colname', 'txtPhoneNumber'),
            'txtAddressType'           => $sdk->t('account', 'txtAddressType'),
            'txtAddressTypeEx'         => $sdk->t('account', 'txtAddressTypeEx'),
            'txtAddressStreet'         => $sdk->t('account', 'txtAddressStreet'),
            'txtAddressApartment'      => $sdk->t('account', 'txtAddressApartment'),
            'txtAddressDepartment'     => $sdk->t('account', 'txtAddressDepartment'),
            'txtAddress'               => $sdk->t('address', 'txtPickupAddress'),
            'txtPostalCode'            => $sdk->t('address', 'txtPickupPostalCode'),
            'txtCity'                  => $sdk->t('address', 'txtCity'),
            'txtCountry'               => $sdk->t('address', 'txtCountry'),
            'txtState'                 => $sdk->t('address', 'txtState'),
            'txtHaveAccountUPS'        => $sdk->t('account', 'txtHaveAccountUPS'),
            'txtLatestInvoice'         => $txtLatestInvoice,
            'txtAccountName'           => $sdk->t('account', 'txtAccountName'),
            'txtAccountNumber'         => $sdk->t('account', 'txtAccountNumber'),
            'txtInvoiceNumber'         => $sdk->t('account', 'txtInvoiceNumber'),
            'txtInvoiceAmount'         => $sdk->t('account', 'txtInvoiceAmount'),
            'txtCurrency'              => $sdk->t('ups', 'txtCurrency'),
            'txtInvoiceDate'           => $sdk->t('account', 'txtInvoiceDate'),
            'txtHaveAccountUPSWithout' => $sdk->t('account', 'txtHaveAccountUPSWithout'),
            'txtHaveNoAccount'         => $this->module->usa() ?
                $sdk->t('account', 'txtHaveNotAccountUS') : $sdk->t('account', 'txtHaveNoAccount'),
            'txtGetStarted'            => $sdk->t('account', 'title'),
            'txtTTAccountNumber'       => $sdk->t('account', 'txtTTAccountNumber'),
            'txtTTInvoiceNumber'       => $sdk->t('account', 'txtTTInvoiceNumber'),
            'txtTInvoiceAmount'        => $sdk->t('account', 'txtTTInvoiceAmount'),
            'txtTTInvoiceDate'         => $sdk->t('account', 'txtTTInvoiceDate'),
            'txtAccTTAccountName'      => $sdk->t('account', 'txtAccTTAccountName'),
            'txtAccTTPostalCode'       => $sdk->t('account', 'txtAccTTPostalCode'),
            'txtVatNumber'             => $sdk->t('account', 'txtVatNumber'),
            'txtPromoCode'             => $sdk->t('account', 'txtPromoCode'),
            'txtAccTTAddressType'      => $sdk->t('account', 'txtAccTTAddressType'),
            'txtPleaseNote'            => $sdk->t('account', 'txtPleaseNote'),
            'txtDescriptionUS'         => $sdk->t('account', 'txtDescriptionUS'),
            'txtAuthorizedUPSAccessPoint' => $sdk->t('account', 'txtAuthorizedUPSAccessPoint'),
            'txtHelpLink' => $this->links['help_link'],
            'txtInformationDangerous' => $sdk->t('account', 'txtInformationDangerous'),
            'txtLinkText' => $sdk->t(
                'account',
                'txtLinkText',
                array('infomation_link' => $this->links['infomation_link'])
            ),
            'txtAccountNotice' => $sdk->t('account', 'txtAccountNotice'),
            'txtAccTTAddress' => $sdk->t('account', 'txtAccTTAddress'),
            'txtBeforeVatNumber' => $sdk->t('account', 'txtBeforeVatNumber'),
            'txtControlID' => $sdk->t('account', 'txtControlID'),
            'txtHaveNotAccountUSLink' => $sdk->t('account', 'txtHaveNotAccountUSLink')
        );
    
        if (static::$hasErr == 1) {
            $varTpl['checkError']   = static::$hasErr;
            $varTpl['optChoose']    = static::$optChoose;
            $varTpl['CustomerName'] = Tools::getValue('CustomerName');
            $varTpl['CompanyName']  = Tools::getValue('CompanyName');
            $varTpl['EmailAddress'] = Tools::getValue('EmailAddress');
            $varTpl['PhoneNumber']  = Tools::getValue('PhoneNumber');
            $varTpl['AddressType']  = Tools::getValue('AddressType');
            $varTpl['AddressLine1'] = Tools::getValue('AddressLine1');
            $varTpl['AddressLine2'] = Tools::getValue('AddressLine2');
            $varTpl['AddressLine3'] = Tools::getValue('AddressLine3');
            $varTpl['PostalCode']   = Tools::getValue('PostalCode');
            $varTpl['City']         = Tools::getValue('City');

            $varTpl['AccountName']   = Tools::getValue('AccountName');
            $varTpl['AccountNumber'] = Tools::getValue('AccountNumber');
            $varTpl['InvoiceNumber'] = Tools::getValue('InvoiceNumber');
            $varTpl['InvoiceAmount'] = Tools::getValue('InvoiceAmount');

            $varTpl['AccountName1']   = Tools::getValue('AccountName1');
            $varTpl['AccountNumber1'] = Tools::getValue('AccountNumber1');
            $varTpl['Currency']       = Tools::getValue('Currency');
            $varTpl['InvoiceDate']    = Tools::getValue('InvoiceDate');
            $varTpl['Title']          = Tools::getValue('Title');
            $varTpl['vatNumber']      = Tools::getValue('vatNumber');
            $varTpl['promoCode']      = Tools::getValue('promoCode');
            $varTpl['ControlID']      = Tools::getValue('ControlID');
        } else {
            $varTpl['checkError']     = static::$hasErr;
            $varTpl['optChoose']     = static::$optChoose;
            $varTpl['CustomerName']   = '';
            $varTpl['CompanyName']    = '';
            $varTpl['EmailAddress']   = '';
            $varTpl['PhoneNumber']    = '';
            $varTpl['AddressType']    = '';
            $varTpl['AddressLine1']   = '';
            $varTpl['AddressLine2']   = '';
            $varTpl['AddressLine3']   = '';
            $varTpl['PostalCode']     = '';
            $varTpl['City']           = '';
            $varTpl['AccountName']    = '';
            $varTpl['AccountNumber']  = '';
            $varTpl['InvoiceNumber']  = '';
            $varTpl['InvoiceAmount']  = '';
            $varTpl['AccountName1']   = '';
            $varTpl['AccountNumber1'] = '';
            $varTpl['vatNumber'] = '';
            $varTpl['promoCode'] = '';
            $varTpl['ControlID'] = '';
            $varTpl['Currency']       = 'EUR';
            $varTpl['InvoiceDate']    = null;
            $varTpl['Title']          = $sdk->t('account', 'txtMr');
        }

        $languageCode = $this->context->language->iso_code;

        if (in_array(Tools::strtolower($languageCode), ['de', 'nl', 'pl'])) {
            $varTpl['title'] = array(
                $sdk->t('account', 'txtMr'),
                $sdk->t('account', 'txtMrs'),
            );
        } elseif (in_array(Tools::strtolower($languageCode), ['es', 'it'])) {
            $varTpl['title'] = array(
                $sdk->t('account', 'txtMr'),
                $sdk->t('account', 'txtMrs'),
                $sdk->t('account', 'txtMs'),
            );
        } else {
            $varTpl['title'] = array(
                $sdk->t('account', 'txtMr'),
                $sdk->t('account', 'txtMiss'),
                $sdk->t('account', 'txtMrs'),
                $sdk->t('account', 'txtMs'),
            );
        }

        $varTpl['countries']        = $this->module->pluginCountryList;
        $varTpl['isUSA'] = $this->module->usa();
        $varTpl['countrySelected'] = Configuration::get('UPS_COUNTRY_SELECTED');
        $varTpl['currency']         = Constants::LIST_CURRENCY;
        $varTpl['listFieldsError']  = static::$errs;
        $varTpl['selectedStateCode']  = static::$selectedStateCode;
        $varTpl['states']           = $this->getListStateCode(Configuration::get('UPS_COUNTRY_SELECTED'));

        $path = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/ups_account/account.tpl';
        $this->content = $this->context->smarty->createTemplate($path, null, null, $varTpl)->fetch();

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));

        parent::initContent();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAccount')) {
            $mInfo = $this->trimFields(Tools::getAllValues());
            $stateCode = '';
            if (isset($mInfo['ProvinceCode'])) {
                $stateCode = $mInfo['ProvinceCode'];
            }
            static::$selectedStateCode = $stateCode;
            
            $this->requiredFields($mInfo);
            $this->validateFormOption($mInfo, static::$errs, true);
            
            static::$errs[] = $this->validateEmail($mInfo['EmailAddress']);
            static::$errs = $this->reorderList(static::$errs);
            //Promo Code US
            $countrySelected = Configuration::get('UPS_COUNTRY_SELECTED');
            if (Tools::strtolower($countrySelected) == 'us') {
                $mInfo['promoCode'] = Constants::PROMO_CODE;
            }

            //validate finish
            if (empty(static::$errs)) {
                $mInfo['isDefaultAccount'] = 'Yes';
                //call api
                $result = $this->createNewUser($mInfo, true);

                if ($result) {
                    static::$hasErr = 0;
                    Configuration::updateValue('UPS_ACCOUNT_EXIST', 1);
                    CommonFunction::setDoneConfigScreen($mInfo['controller']);
                    $this->updateTabName();
                    Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsAccountSuccess'));
                } else {
                    // Display error message, cannot make the progress license key and registration. 22:25 12-07
                    $this->errors[] = isset($this->fault['err']) ? $this->fault['err'] :
                        $this->sdk->t('ups', 'Cannot Create');
                    static::$hasErr = 1;
                    static::$optChoose = $mInfo['optradio'];

                    if (isset($this->fault['code']) && $this->fault['code'] == SELF::PICKUP_ADDRESS_CANDIDATE) {
                        static::$errs[] = 'PostalCode';
                        static::$errs[] = 'City';
                    }
                }
            } else {
                static::$hasErr = 1;
                $message = $this->sdk->t('err-msg', 'notValid');
                if ($this->module->usa()) {
                    $message = $this->sdk->t('err-msg', 'notValidUS');
                }
                $this->errors[] = $message;
                static::$optChoose = $mInfo['optradio'];
            }
        }
    }

    private function requiredFields($data)
    {
        $v = new Bean\Validator();
        static::$errs[] = $v->isBlank('PhoneNumber', $data['PhoneNumber']);
        static::$errs[] = $v->isBlank('PostalCode', $data['PostalCode']);
        static::$errs[] = $v->validString('CustomerName', $data['CustomerName']);
        static::$errs[] = $v->validString('CompanyName', $data['CompanyName']);
        static::$errs[] = $v->validString('AddressType', $data['AddressType']);
        static::$errs[] = $v->validString('AddressLine1', $data['AddressLine1']);
        static::$errs[] = $v->validEmail('EmailAddress', $data['EmailAddress']);
        static::$errs[] = $v->validString('City', $data['City']);
        if (isset($data['ProvinceCode'])) {
            static::$errs[] = $v->validString('ProvinceCode', $data['ProvinceCode']);
        }
    }

    private function trimFields($arr = array())
    {
        $tmp = array();
        foreach ($arr as $key => $val) {
            $tmp[$key] = trim($val);
        }
        return $tmp;
    }

    // Update tab name language for AccountSucces tab
    private function updateTabName()
    {
        $langIsoIds = Language::getIsoIds();
        $tabClassName = 'AdminUpsAccountSuccess';

        $idTab = Tab::getIdFromClassName($tabClassName);

        $tabs = $this->module->getUpsTabs();
        // Get this tab to update
        foreach ($tabs as $tab) {
            if ($tab['class_name'] == 'AdminUpsAccount') {
                $accountTab = $tab;
                break;
            }
        }

        foreach ($langIsoIds as $lang) {
            if (array_key_exists($lang['iso_code'], $accountTab)) {
                $nameDisplayTab = $accountTab[$lang['iso_code']];
                Db::getInstance()->update(
                    'tab_lang',
                    array('name' => pSQL($nameDisplayTab)),
                    '`id_tab` = ' . (int) $idTab . ' AND `id_lang` = ' . (int) $lang['id_lang']
                );
            }
        }
    }
}
