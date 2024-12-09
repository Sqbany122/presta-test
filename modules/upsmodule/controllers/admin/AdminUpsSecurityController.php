<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsSecurityController extends CommonController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsSecurity');
    }

    public function initContent()
    {
        $this->fields_form  = $this->createFieldsForm();
        $this->fields_value = $this->createFieldsValue();
        $this->content .= $this->renderForm();
        parent::initContent();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upssecurity.js');
    }

    private function createFieldsForm()
    {
        $fieldsForm = array(
            'legend' => array(
                'title' => $this->sdk->t('enhancement', 'txtSeEnAc'),
                'icon'  => 'icon-shield',
            ),
            'description' => $this->sdk->t('enhancement', 'txtByInstalling'),
            'input'  => array(
                array(
                    'type'   => 'switch',
                    'label'  => $this->sdk->t('enhancement', 'txtClickjacking'),
                    'desc'   => $this->sdk->t('enhancement', 'txtDescClickjacking'),
                    'name'   => 'UPS_SEC_CLICKJACKING',
                    'values' => array(
                        array(
                            'id_configuration' => 'UPS_SEC_CLICKJACKING',
                            'value'            => 1,
                        ),
                        array(
                            'id_configuration' => 'UPS_SEC_CLICKJACKING',
                            'value'            => 0,
                        ),
                    ),
                ),
                array(
                    'type'   => 'radio',
                    'label'  => $this->sdk->t('enhancement', 'txtUseXFrame'),
                    'name'   => 'UPS_SEC_X_FRAME_OPTIONS',
                    'values' => array(
                        array(
                            'id'    => 'x_frame_not_use',
                            'value' => 0,
                            'label' => $this->sdk->t('enhancement', 'txtDoNotUse'),
                        ),
                        array(
                            'id'    => 'x_frame_deny',
                            'value' => 1,
                            'label' => $this->sdk->t('enhancement', 'txtDeny'),
                        ),
                        array(
                            'id'    => 'x_frame_sameorigin',
                            'value' => 2,
                            'label' => $this->sdk->t('enhancement', 'txtSameOrigin'),
                        ),
                    ),
                ),
                array(
                    'type'   => 'switch',
                    'label'  => $this->sdk->t('enhancement', 'txtUseFrameKill'),
                    'name'   => 'UPS_SEC_FRAME_KILLER',
                    'values' => array(
                        array(
                            'id_configuration' => 'UPS_SEC_FRAME_KILLER',
                            'value'            => 1,
                        ),
                        array(
                            'id_configuration' => 'UPS_SEC_FRAME_KILLER',
                            'value'            => 0,
                        ),
                    ),
                ),
                array(
                    'type'   => 'radio',
                    'label'  => $this->sdk->t('enhancement', 'txtUseContent'),
                    'name'   => 'UPS_SEC_CONTENT_SEC_POLICY',
                    'values' => array(
                        array(
                            'id'    => 'content_not_use',
                            'value' => 0,
                            'label' => $this->sdk->t('enhancement', 'txtDoNotUse'),
                        ),
                        array(
                            'id'    => 'content_none',
                            'value' => 1,
                            'label' => $this->sdk->t('enhancement', 'txtFrameAncestorsNone'),
                        ),
                        array(
                            'id'    => 'content_self',
                            'value' => 2,
                            'label' => $this->sdk->t('enhancement', 'txtFrameAncestorsSefl'),
                        ),
                    ),
                ),
                array(
                    'type'         => 'html',
                    'name'         => 'line',
                    'html_content' => '<hr>',
                ),
                array(
                    'type'   => 'switch',
                    'label'  => $this->sdk->t('enhancement', 'txtContentSniffing'),
                    'desc'   => $this->sdk->t('enhancement', 'txtDesContent'),
                    'name'   => 'UPS_SEC_CONTENT_SNIFFING',
                    'values' => array(
                        array(
                            'id_configuration' => 'UPS_SEC_CONTENT_SNIFFING',
                            'value'            => 1,
                        ),
                        array(
                            'id_configuration' => 'UPS_SEC_CONTENT_SNIFFING',
                            'value'            => 0,
                        ),
                    ),
                ),
                array(
                    'type'         => 'html',
                    'name'         => 'line',
                    'html_content' => '<hr>',
                ),
                array(
                    'type'   => 'switch',
                    'label'  => $this->sdk->t('enhancement', 'txtEnablesCross'),
                    'desc'   => $this->sdk->t('enhancement', 'txtDescCrossScripting'),
                    'name'   => 'UPS_SEC_CROSS_SITE',
                    'values' => array(
                        array(
                            'id_configuration' => 'UPS_SEC_CROSS_SITE',
                            'value'            => 1,
                        ),
                        array(
                            'id_configuration' => 'UPS_SEC_CROSS_SITE',
                            'value'            => 0,
                        ),
                    ),
                ),
                array(
                    'type'         => 'html',
                    'name'         => 'line',
                    'html_content' => '<hr>',
                ),
                array(
                    'type'   => 'switch',
                    'label'  => $this->sdk->t('enhancement', 'txtProtectEshop'),
                    'desc'   => $this->sdk->t('enhancement', 'txtDescStrictTransport'),
                    'name'   => 'UPS_SEC_STRICT_TRANSPORT',
                    'values' => array(
                        array(
                            'id_configuration' => 'UPS_SEC_STRICT_TRANSPORT',
                            'value'            => 1,
                        ),
                        array(
                            'id_configuration' => 'UPS_SEC_STRICT_TRANSPORT',
                            'value'            => 0,
                        ),
                    ),
                ),
                array(
                    'type'         => 'html',
                    'name'         => 'line',
                    'html_content' => '<hr>',
                ),
                array(
                    'type'   => 'switch',
                    'label'  => $this->sdk->t('enhancement', 'txtPrevent'),
                    'desc'   => $this->sdk->t('enhancement', 'txtDescCaching'),
                    'name'   => 'UPS_SEC_FROM_CACHING',
                    'values' => array(
                        array(
                            'id_configuration' => 'UPS_SEC_FROM_CACHING',
                            'value'            => 1,
                        ),
                        array(
                            'id_configuration' => 'UPS_SEC_FROM_CACHING',
                            'value'            => 0,
                        ),
                    ),
                ),
                array(
                    'type'   => 'radio',
                    'label'  => $this->sdk->t('enhancement', 'txtApplytoPages'),
                    'name'   => 'UPS_SEC_APPLY',
                    'values' => array(
                        array(
                            'id'    => 'apply_all',
                            'value' => 0,
                            'label' => $this->sdk->t('enhancement', 'txtAll'),
                        ),
                        array(
                            'id'    => 'checkout_only',
                            'value' => 1,
                            'label' => $this->sdk->t('enhancement', 'txtCheckOnly'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->sdk->t('button', 'txtSave'),
                'icon'  => 'process-icon-save',
                'name'  => 'saveSubmit',
            ),
        );

        return $fieldsForm;
    }

    private function createFieldsValue()
    {
        $fieldsValue = array(
            'UPS_SEC_CLICKJACKING'       => Configuration::get('UPS_SEC_CLICKJACKING'),
            'UPS_SEC_X_FRAME_OPTIONS'    => Configuration::get('UPS_SEC_X_FRAME_OPTIONS'),
            'UPS_SEC_FRAME_KILLER'       => Configuration::get('UPS_SEC_FRAME_KILLER'),
            'UPS_SEC_CONTENT_SEC_POLICY' => Configuration::get('UPS_SEC_CONTENT_SEC_POLICY'),
            'UPS_SEC_CONTENT_SNIFFING'   => Configuration::get('UPS_SEC_CONTENT_SNIFFING'),
            'UPS_SEC_CROSS_SITE'         => Configuration::get('UPS_SEC_CROSS_SITE'),
            'UPS_SEC_STRICT_TRANSPORT'   => Configuration::get('UPS_SEC_STRICT_TRANSPORT'),
            'UPS_SEC_FROM_CACHING'       => Configuration::get('UPS_SEC_FROM_CACHING'),
            'UPS_SEC_APPLY'              => Configuration::get('UPS_SEC_APPLY'),
        );

        return $fieldsValue;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('saveSubmit')) {
            Configuration::updateValue('UPS_SEC_CLICKJACKING', Tools::getValue('UPS_SEC_CLICKJACKING'));
            Configuration::updateValue('UPS_SEC_X_FRAME_OPTIONS', Tools::getValue('UPS_SEC_X_FRAME_OPTIONS'));
            Configuration::updateValue('UPS_SEC_FRAME_KILLER', Tools::getValue('UPS_SEC_FRAME_KILLER'));
            Configuration::updateValue('UPS_SEC_CONTENT_SEC_POLICY', Tools::getValue('UPS_SEC_CONTENT_SEC_POLICY'));
            Configuration::updateValue('UPS_SEC_CONTENT_SNIFFING', Tools::getValue('UPS_SEC_CONTENT_SNIFFING'));
            Configuration::updateValue('UPS_SEC_CROSS_SITE', Tools::getValue('UPS_SEC_CROSS_SITE'));
            Configuration::updateValue('UPS_SEC_STRICT_TRANSPORT', Tools::getValue('UPS_SEC_STRICT_TRANSPORT'));
            Configuration::updateValue('UPS_SEC_FROM_CACHING', Tools::getValue('UPS_SEC_FROM_CACHING'));
            Configuration::updateValue('UPS_SEC_APPLY', Tools::getValue('UPS_SEC_APPLY'));
        }

        return parent::postProcess();
    }
}
