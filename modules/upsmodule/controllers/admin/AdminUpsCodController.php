<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsCodController extends CommonController
{
    public function __construct()
    {
        $this->bootstrap               = true;
        $this->show_form_cancel_button = false;
        parent::__construct();

        $result = CommonFunction::checkScreenConfig();
        if ($result !== 'DONE'
            && $result !== Tools::getValue('controller')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink($result));
        }

        Configuration::updateValue('UPS_MODULE_COD_ENABLE', CommonFunction::isCodModuleEnable());
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsCod');
    }

    public function initContent()
    {
        $this->fields_form  = $this->createFieldsForm();
        $this->fields_value = $this->createFieldsValue();
        $this->content .= $this->renderForm();
        parent::initContent();
    }

    private function createFieldsForm()
    {
        $switchOption = null;
        if (!$this->module->usa()) {
            $switchOption = array(
                'type'   => 'switch',
                'label'  => $this->sdk->t('ups', 'ups_cod_option'),
                'desc'   => $this->sdk->t('ups', 'ups_cod_option_des'),
                'name'   => 'UPS_COD_ENABLE',
                'values' => array(
                    array(
                        'id_configuration' => 'UPS_COD_ENABLE',
                        'value'            => 1,
                    ),
                    array(
                        'id_configuration' => 'UPS_COD_ENABLE',
                        'value'            => 0,
                    ),
                ),
            );
        }

        $fieldsForm = array(
            'legend' => array(
                'title' => $this->sdk->t('ups', 'ttlCod'),
                'icon'  => 'icon-money',
            ),
            'input'  => array(
                array(
                    'type' => 'free',
                    'name' => 'UPS_COD_INTRO',
                ),
                array(
                    'type' => 'free',
                    'name' => 'UPS_COD_NOTE',
                ),
                $switchOption
            ),
            'submit' => array(
                'title' => $this->sdk->t('button', 'txtNext'),
                'icon'  => 'process-icon-next',
                'name'  => 'continueSubmit',
            ),
        );

        return $fieldsForm;
    }

    private function createFieldsValue()
    {
        $sdk = $this->sdk;
        $pathImg = Media::getMediaPath(_PS_MODULE_DIR_ . $this->module->name);
        $style = 'style="width: 18px; margin-right: 7px"';
        
        $str = '';
        $isEnable = Configuration::get('UPS_MODULE_COD_ENABLE');
        if ($isEnable) {
            $mark = '<img src="' . $pathImg . '/views/img/check.jpg" ' . $style . '/>';
            $txt1 = $sdk->t('ups', 'txtCodEnable1');
            $txt2 = $sdk->t('ups', 'txtCodIntro');

            $str = "$mark<strong>$txt1</strong><br/><br/><strong>$txt2</strong>";
        } else {
            $mark = '<img src="' . $pathImg . '/views/img/cross.png" ' . $style . '/>';
            $urlGuide = 'http://doc.prestashop.com/display/PS16/Payment+Settings';
            $txt1 = $sdk->t('ups', 'txtCodDisable1');
            $txt2 = $sdk->t('ups', 'txtCodIntro');
            $txt3 = $sdk->t('ups', 'txtCodDisable3');
            $txt4 = $sdk->t('ups', 'txtCodDisable4');
            $txt5 = $sdk->t('ups', 'txtCodDisable5');
            $txt6 = $sdk->t('ups', 'txtCodDisable6');
            $txt7 = $sdk->t('ups', 'txtCodDisable7');
            $txt8 = $sdk->t('ups', 'txtCodDisable8');
            
            $str = "$mark<strong>$txt1</strong>
                <br/><br/>
                <strong>$txt2</strong>
                <br/><br/>
                $txt3 <strong>$txt4</strong> $txt5 <strong>$txt6</strong>
                <a target=\'_blank\' href=$urlGuide> $txt7 </a>$txt8";
        }

        $fieldsValue = array(
            'UPS_MODULE_COD_ENABLE' => $isEnable,
            'UPS_COD_ENABLE'        => Configuration::get('UPS_COD_ENABLE'),
            'UPS_COD_INTRO'         => $str
        );

        return $fieldsValue;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('continueSubmit')) {
            Configuration::updateValue('UPS_COD_ENABLE', Tools::getValue('UPS_COD_ENABLE'));

            CommonFunction::setDoneConfigScreen(Tools::getValue('controller'));
            return Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminUpsPkgDimension'));
        }
    }
}
