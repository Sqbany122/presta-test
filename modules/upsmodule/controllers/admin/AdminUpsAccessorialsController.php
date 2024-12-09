<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsAccessorialsController extends CommonController
{
    private $tempValue              = 0;
    private $listAccessorial = array();

    public function __construct()
    {
        // Redireact to PkgDimension Screen when user try access URL
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsPkgDimension'));
        $this->bootstrap = true;
        parent::__construct();

        $result = CommonFunction::checkScreenConfig();
        if ($result !== 'DONE' &&
            $result !== Tools::getValue('controller')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink($result));
        }

        $this->listAccessorial = $this->module::$accessorials->getServiceKeyPairs();
    }
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/accessorials.js');
    }

    public function renderForm()
    {
        foreach ($this->listAccessorial as $accessorial) {
            $this->fields_value['accessory_' . $accessorial['id_config']] = Configuration::get(
                $accessorial['id_config']
            );
        }

        $this->fields_form = array(
            'tinymce' => true,
            'legend'  => array(
                'title' => $this->sdk->t('accessorial', 'accessorial_Accessorial_Services'),
                'icon'  => 'icon-plus-circle',
            ),
            'input'   => array(
                array(
                    'type' => 'free',
                    'desc' => $this->sdk->t('accessorial', 'accessorial_Service_Select'),
                    'name' => 'description',
                ),
                array(
                    'type'   => 'checkbox',
                    'name'   => 'accessory',
                    'lang'   => true,
                    'values' => array(
                        'query' => $this->listAccessorial,
                        'id'    => 'id_config',
                        'name'  => 'name',
                    ),
                ),
            ),
            'submit'  => array(
                'title' => $this->sdk->t('button', 'txtSave'),
                'name'  => 'submitTabaccessorials',
            ),
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitTabaccessorials')) {
            foreach ($this->listAccessorial as $accessorial) {
                $keyConfig = $accessorial['id_config'];

                if (Tools::getIsset('accessory_' . $keyConfig) && Tools::getValue('accessory_' . $keyConfig) == 'on') {
                    Configuration::updateValue($keyConfig, 1);
                } else {
                    Configuration::updateValue($keyConfig, 0);
                }
            }

            CommonFunction::setDoneConfigScreen(Tools::getValue('controller'));

            return Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsPkgDimension'));
        }
    }

    public function initContent()
    {
        if (Module::isInstalled(Constants::PS_COD_MODULE)) {
            $this->tempValue = 1;
        }

        $this->display                 = 'edit';
        $this->show_form_cancel_button = false;
        parent::initContent();
    }
}
