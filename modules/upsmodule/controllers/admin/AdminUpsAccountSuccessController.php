<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsAccountSuccessController extends CommonController
{
    private static $checkError;
    private static $checkRadio;
    private static $checkRemove;
    private static $selectedStateCode = '';
    private static $listFieldsError = array();
    private $links;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table     = 'configuration';
        parent::__construct();

        $this->links = $this->getInformationLink();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upsaccountsuccess.js');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsAccount');
    }

    public function initContent()
    {
        if ($this->module->usa()) {
            $txtLatestInvoice = $this->sdk->t('account', 'txtLatestInvoiceUS');
        } else {
            $txtLatestInvoice = $this->sdk->t('account', 'txtLatestInvoice');
        }
        $texts = array(
            'txtAccount'            => $this->sdk->t('account', 'txtAccount'),
            'title'                 => $this->sdk->t('account', 'txtProfileTitle'),
            'txtFullName'           => $this->sdk->t('account', 'txtFullName'),
            'txtCompany'            => $this->sdk->t('account', 'txtCompany'),
            'txtEmail'              => $this->sdk->t('colname', 'txtEmail'),
            'txtPhoneNumber'        => $this->sdk->t('colname', 'txtPhoneNumber'),
            'txtAddress'            => $this->sdk->t('address', 'txtPickupAddress'),
            'txtPostalCode'         => $this->sdk->t('address', 'txtPickupPostalCode'),
            'txtCity'               => $this->sdk->t('address', 'txtCity'),
            'txtState'              => $this->sdk->t('address', 'txtState'),
            'txtCountry'            => $this->sdk->t('address', 'txtCountry'),
            'txtAccPaymentAccount'     => $this->sdk->t('account', 'txtAccPaymentAccount'),
            'txtAccountNumber'         => $this->sdk->t('account', 'txtAccountNumber'),
            'txtAccRemove'             => $this->sdk->t('account', 'txtAccRemove'),
            'txtAccAddAnotherAccount'  => $this->sdk->t('account', 'txtAccAddAnotherAccount'),
            'txtAccEnterAddress'       => $this->sdk->t('account', 'txtAccEnterAddress'),
            'txtAddressType'           => $this->sdk->t('account', 'txtAddressType'),
            'txtAccountName'           => $this->sdk->t('account', 'txtAccountName'),
            'txtAddressStreet'         => $this->sdk->t('account', 'txtAddressStreet'),
            'txtAddressTypeEx'         => $this->sdk->t('account', 'txtAddressTypeEx'),
            'txtAddressApartment'      => $this->sdk->t('account', 'txtAddressApartment'),
            'txtAddressDepartment'     => $this->sdk->t('account', 'txtAddressDepartment'),
            'txtHaveAccountUPS'        => $this->sdk->t('account', 'txtHaveAccountUPS'),
            'txtLatestInvoice'         => $txtLatestInvoice,
            'txtInvoiceNumber'         => $this->sdk->t('account', 'txtInvoiceNumber'),
            'txtInvoiceAmount'         => $this->sdk->t('account', 'txtInvoiceAmount'),
            'txtInvoiceDate'           => $this->sdk->t('account', 'txtInvoiceDate'),
            'txtHaveAccountUPSWithout' => $this->sdk->t('account', 'txtHaveAccountUPSWithout'),
            'txtAccVerify'             => $this->sdk->t('account', 'txtAccVerify'),
            'txtAccTTAccountNumber'    => $this->sdk->t('account', 'txtTTAccountNumber'),
            'txtAccTTInvoiceNumber'    => $this->sdk->t('account', 'txtTTInvoiceNumber'),
            'txtAccTTInvoiceAmount'    => $this->sdk->t('account', 'txtAccTTInvoiceAmount'),
            'txtAccTTInvoiceDate'      => $this->sdk->t('account', 'txtTTInvoiceDate'),
            'txtAccTTAccountName'      => $this->sdk->t('account', 'txtAccTTAccountName'),
            'txtAccTTPostalCode'       => $this->sdk->t('account', 'txtAccTTPostalCode'),
            'txtAccTTAddressType'      => $this->sdk->t('account', 'txtAccTTAddressType'),
            'txtLinkText' => $this->sdk->t(
                'account',
                'txtLinkText',
                array('infomation_link' => $this->links['infomation_link'])
            ),
            'txtNext'                  => $this->sdk->t('button', 'txtNext'),
            'txtCurrency'              => $this->sdk->t('ups', 'txtCurrency'),
            'txtAccountNotice' => $this->sdk->t('account', 'txtAccountNotice'),
            'txtAccTTAddress'  => $this->sdk->t('account', 'txtAccTTAddress'),
            'txtControlID'     => $this->sdk->t('account', 'txtControlID')
        );

        $listAccount  = $this->module->getListAccount();
        $primaryInfo  = $this->getPrimaryInfo();
        $displayInfo  = array();
        $varTpl = array();
        foreach ($listAccount as $account) {
            $account['PostalCode'] = $this->formatPostalCode($account['PostalCode'], true);

            // For the Old VERSION - missing isDefaultAccount on the account default
            if (!isset($account['isDefaultAccount'])) {
                $account['default'] = true;
            } elseif ($this->module->checkDefaultAccount($account['isDefaultAccount'])) {
                $account['default'] = true;
            } else {
                $account['default'] = false;
            }

            $displayInfo[] = $account;
        }

        $primaryInfo['PostalCode'] = $this->formatPostalCode($primaryInfo['PostalCode'], true);
        $listState = $this->getListState(Configuration::get('UPS_COUNTRY_SELECTED'));
       
        if (isset($primaryInfo['ProvinceCode'])) {
            foreach ($listState as $code => $name) {
                if ($code == $primaryInfo['ProvinceCode']) {
                    $primaryInfo['ProvinceCode'] = $name;
                    break;
                }
            }
        } else {
            $primaryInfo['ProvinceCode'] = '';
        }
        
        if (static::$checkError == 1) {
            $varTpl = array(
                'primaryInfo'    => $primaryInfo,
                'listAccount'    => $displayInfo,
                'texts'          => $texts,

                'checkError'     => static::$checkError,
                'checkRadio'     => static::$checkRadio,
                'checkRemove'    => static::$checkRemove,
                'AddressType'    => Tools::getValue('AddressType'),
                'BusinessName'   => Tools::getValue('BusinessName'),
                'AddressLine1'   => Tools::getValue('AddressLine1'),
                'AddressLine2'   => Tools::getValue('AddressLine2'),
                'AddressLine3'   => Tools::getValue('AddressLine3'),
                'PostalCode'     => Tools::getValue('PostalCode'),
                'PhoneNumber'    => Tools::getValue('PhoneNumber'),
                'City'           => Tools::getValue('City'),

                'AccountNumber'  => Tools::getValue('AccountNumber'),
                'InvoiceNumber'  => Tools::getValue('InvoiceNumber'),
                'InvoiceAmount'  => Tools::getValue('InvoiceAmount'),
                'AccountNumber1' => Tools::getValue('AccountNumber1'),
                'Currency'       => Tools::getValue('Currency'),
                'InvoiceDate'    => Tools::getValue('InvoiceDate'),
                'ControlID'      => Tools::getValue('ControlID')
            );
        } else {
            $varTpl = array(
                'texts'          => $texts,
                'primaryInfo'    => $primaryInfo,
                'listAccount'    => $displayInfo,
                'checkError'     => static::$checkError,
                'checkRadio'     => static::$checkRadio,
                'checkRemove'    => static::$checkRemove,
                'AddressType'    => '',
                'BusinessName'   => '',
                'AddressLine1'   => '',
                'AddressLine2'   => '',
                'AddressLine3'   => '',
                'PostalCode'     => '',
                'PhoneNumber'    => '',
                'City'           => '',
                'AccountNumber'  => '',
                'InvoiceNumber'  => '',
                'InvoiceAmount'  => '',
                'AccountNumber1' => '',
                'Currency'       => 'EUR',
                'InvoiceDate'    => null,
                'ControlID'    => '',
            );
        }
        
        //Show Error
        $errAPIs = $this->context->cookie->__get('ups_cookie_error_account');
        if (!empty($errAPIs)) {
            $this->context->cookie->__unset('ups_cookie_error_account');
            $this->errors[]   = $errAPIs;
        }

        $varTpl['countries']        = $this->module->pluginCountryList;
        $varTpl['isUSA']            = $this->module->usa();
        $varTpl['countrySelected']  = Configuration::get('UPS_COUNTRY_SELECTED');
        $varTpl['currency']         = Constants::LIST_CURRENCY;
        $varTpl['listFieldsError']  = static::$listFieldsError;
        $varTpl['selectedStateCode']  = static::$selectedStateCode;
        $varTpl['states']           = $listState;
        $path = _PS_MODULE_DIR_
            . $this->module->name
            . '/views/templates/admin/ups_account_success/account_success.tpl';
        $this->content = $this->context->smarty->createTemplate($path, null, null, $varTpl)->fetch();
        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));

        parent::initContent();
    }

    public function getListState($coutryCode)
    {
        $countryId = Country::getByIso($coutryCode);
        $states = State::getStatesByIdCountry($countryId, true);
        $result = array();
        foreach ($states as $item) {
            $result[$item['iso_code']] = $item['name'];
        }

        return $result;
    }

    public function postProcess()
    {
        $merchantInfo = Tools::getAllValues();

        if (Tools::getIsset('deleteAccount') && Tools::getIsset('accountNumber')) {
            $this->deleteAccount();
        }

        if (Tools::isSubmit('submitAccountSuccess')) {
            $this->requiredFields($merchantInfo);
            $this->validateFormOption($merchantInfo, static::$listFieldsError);
            $stateCode = '';
            if (isset($merchantInfo['ProvinceCode'])) {
                $stateCode = $merchantInfo['ProvinceCode'];
            }
            static::$selectedStateCode = $stateCode;
            static::$listFieldsError = $this->reorderList(static::$listFieldsError);

            if (empty(static::$listFieldsError)) {
                if ($this->checkAccountNumber()) {
                    static::$checkError = 1;
                    $this->errors[] = $this->sdk->t('err-msg', 'account_number_was_duplicated');
                    static::$checkRadio = Tools::getValue('optradio');
                    return false;
                }
                $merchantInfo['isDefaultAccount'] = false;
                $accountCreated = $this->createNewUser($merchantInfo);
                if ($accountCreated) {
                    $hasMerchantInfo = Configuration::get('UPS_MERCHANTINFO_EXIST');
                    if ($hasMerchantInfo) {
                        $this->transferMerchantInfo(
                            $this->collectShopConfigurations($hasMerchantInfo, $accountCreated)
                        );
                    }
                    Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsAccountSuccess'));
                } else {
                    // Display error message, cannot make the progress license key and registration. 22:25 12-07
                    $this->errors[] = isset($this->fault['err']) ?
                        $this->fault['err'] : $this->sdk->t('ups', 'error_create');
                    static::$checkError = 1;
                    static::$checkRadio = Tools::getValue('optradio');
                    return false;
                }
            } else {
                static::$checkError = 1;
                $message = $this->sdk->t('err-msg', 'notValid');
                if ($this->module->usa()) {
                    $message = $this->sdk->t('err-msg', 'notValidUS');
                }
                $this->errors[]     = $message;
                static::$checkRadio = Tools::getValue('optradio');
            }
        }
        if (Tools::isSubmit('nextAccountSuccess')) {
            CommonFunction::setDoneConfigScreen(Tools::getValue('controller'));
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsShippingServices'));
        }

        parent::postProcess();
    }

    public function checkAccountNumber()
    {
        $listAccount = $this->module->getListAccount();
        foreach ($listAccount as $account) {
            if ($account['AccountNumber'] == Tools::getValue('AccountNumber1')
                || $account['AccountNumber'] == Tools::getValue('AccountNumber')) {
                static::$checkRemove = 1;
                return true;
            }
        }
        return false;
    }

    public function ajaxProcessValidateAccountRemove()
    {
        $accountNumber = Tools::getValue('accountNumber');
        $accountAp     = Configuration::get('UPS_SP_SERV_AP_CHOOSE_ACC');
        $accountAdd    = Configuration::get('UPS_SP_SERV_ADDRESS_CHOOSE_ACC');

        if ($accountAp == $accountNumber || $accountAdd == $accountNumber) {
            $response = true;
        } else {
            $response = false;
        }

        $this->ajaxDie(json_encode($response));
    }

    private function checkAndUpdateShipingService($accountNumber)
    {
        $accounts = $this->module->getListAccount();
        $acountDefault = $this->module->getDefaultAccount($accounts);

        if (Configuration::get('UPS_SP_SERV_AP_CHOOSE_ACC') == $accountNumber) {
            Configuration::updateValue('UPS_SP_SERV_AP_CHOOSE_ACC', $acountDefault['AccountNumber']);
        }

        if (Configuration::get('UPS_SP_SERV_ADDRESS_CHOOSE_ACC') == $accountNumber) {
            Configuration::updateValue('UPS_SP_SERV_ADDRESS_CHOOSE_ACC', $acountDefault['AccountNumber']);
        }
    }

    public function deleteAccount()
    {
        if (Tools::getValue('deleteAccount') != 1) {
            return false;
        }

        $accountNumber = Tools::getValue('accountNumber');

        Db::getInstance()->delete('ups_data', KEY_COL . " = '". pSQL(ACCOUNT_NUM_KEY . $accountNumber) ."'");

        $this->checkAndUpdateShipingService($accountNumber);

        $this->module->transferMerchantStatus(array(
            'merchantKey' => Configuration::get('MERCHANT_KEY'),
            'accountNumber' => $accountNumber,
            'status' => 20
        ));
    }

    private function requiredFields($data)
    {
        $v = new Bean\Validator();
        static::$listFieldsError[] = $v->isBlank('PhoneNumber', $data['PhoneNumber']);
        static::$listFieldsError[] = $v->isBlank('PostalCode', $data['PostalCode']);
        static::$listFieldsError[] = $v->validString('AddressLine1', $data['AddressLine1']);
        static::$listFieldsError[] = $v->isBlank('BusinessName', $data['BusinessName']);
        static::$listFieldsError[] = $v->validString('AddressType', $data['AddressType']);
        static::$listFieldsError[] = $v->validString('City', $data['City']);
        if (isset($data['ProvinceCode'])) {
            static::$listFieldsError[] = $v->validString('ProvinceCode', $data['ProvinceCode']);
        }
    }
}
