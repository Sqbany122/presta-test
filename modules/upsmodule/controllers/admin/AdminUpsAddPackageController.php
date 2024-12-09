<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsAddPackageController extends CommonController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->table = 'configuration';
    }

    public function initContent()
    {
        $this->ajax    = true;
        $this->display = 'view';
        $this->renderView();
    }

    public function renderView()
    {
        $texts = array(
            'txtAddPackageWeight' => $this->sdk->t('openorder', 'txtWeight'),
            'txtAddPackageLength' => $this->sdk->t('openorder', 'txtLength'),
            'txtAddPackageWidth'  => $this->sdk->t('openorder', 'txtWidth'),
            'txtAddPackageHeight' => $this->sdk->t('openorder', 'txtHeight'),
            'txtOpenPackage'      => $this->sdk->t('openorder', 'txtOpenPackage'),
        );

        $arrWeight = array(
            array(
                'id'   => 1,
                'name' => 'Kg',
            ),
            array(
                'id_'  => 2,
                'name' => 'Pound',
            ),
        );

        $arrLength = array(
            array(
                'id'   => 1,
                'name' => 'cm',
            ),
            array(
                'id_'  => 2,
                'name' => 'm',
            ),
        );

        $packageOptions = $this->displayPackageInfo();

        $this->context->smarty->assign('numberPackage', Tools::getValue('id') + 1);
        $this->context->smarty->assign('isUSA', $this->module->usa());
        $this->context->smarty->assign('js_dir', _PS_JS_DIR_);
        $this->context->smarty->assign('packageOptions', $packageOptions);
        $this->context->smarty->assign('arrWeight', $arrWeight);
        $this->context->smarty->assign('arrLength', $arrLength);
        $this->context->smarty->assign('packageID', 1);
        $this->context->smarty->assign('texts', $texts);

        $tpl  = $this->getTemplatePath() . '/ups_package_list/packageList.tpl';
        $html = $this->context->smarty->fetch($tpl);
        return $html;
    }

    public function displayAjax()
    {
        $this->ajaxDie($this->renderView());
    }
}
