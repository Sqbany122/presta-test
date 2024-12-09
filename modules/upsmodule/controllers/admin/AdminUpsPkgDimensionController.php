<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsPkgDimensionController extends CommonController
{
    private $listNumberPkg = array();
    private $getIdPkg;
    private $texts;

    public function __construct()
    {
        $this->bootstrap               = true;
        $this->show_form_cancel_button = false;
        $this->table                   = 'configuration';
        parent::__construct();

        $result = CommonFunction::checkScreenConfig();
        if ($result !== 'DONE' &&
            $result !== Tools::getValue('controller')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink($result));
        }
        
        $selected_weightUnit = 'KGS';
        $selected_lenghtUnit = 'CM';
        
        if ($this->module->usa()) {
            $selected_weightUnit = 'LBS';
            $selected_lenghtUnit = 'IN';
        }
        
        $this->texts = array(
            'txtPkgKg'             => $this->sdk->t('pkgdimension', 'txtPkgKg'),
            'txtPkgx'              => 'x',
            'txtArcOk'             => $this->sdk->t('button', 'txtOk'),
            'btnCancel'             => $this->sdk->t('button', 'txtCancel'),
            'txtPkgCm'             => $this->sdk->t('pkgdimension', 'txtPkgCm'),
            'txtPkgInch'           => $this->sdk->t('pkgdimension', 'txtPkgInch'),
            'txtPkgSave'           => $this->sdk->t('button', 'txtSave'),
            'txtPkgUnit'           => $this->sdk->t('pkgdimension', 'txtPkgUnit'),
            'txtPkgDefault'        => $this->sdk->t('pkgdimension', 'txtPkgDefault'),
            'txtPkgExample'        => $this->sdk->t('pkgdimension', 'txtPkgExample'),
            'txtPkgPounds'         => $this->sdk->t('pkgdimension', 'txtPkgPounds'),
            'txtPkgRemove'         => $this->sdk->t('pkgdimension', 'txtPkgRemove'),
            'txtNext'              => $this->sdk->t('button', 'txtNext'),
            'txtPkgEditing'        => $this->sdk->t('pkgdimension', 'txtPkgEditing'),
            'txtPkgOkToRemove'     => $this->sdk->t('pkgdimension', 'txtPkgOkToRemove'),
            'txtConfirm2'          => $this->sdk->t('pkgdimension', 'txtConfirm2'),
            'txtPkgWeightSize'     => $this->sdk->t('pkgdimension', 'txtPkgWeightSize'),
            'txtPkgWeightSize2'    => $this->sdk->t('pkgdimension', 'txtPkgWeightSize2'),
            'txtPkgPackageName'    => $this->sdk->t('pkgdimension', 'txtPkgPackageName'),
            'txtPkgAddPackage'     => $this->sdk->t('button', 'txtAddPackage'),
            'txtPkgDimension'      => $this->sdk->t('pkgdimension', 'txtPkgDimension'),
            'txtPkgAddNewPackage'  => $this->sdk->t('pkgdimension', 'txtPkgAddNewPackage'),
            'txtAddPackageWeight'  => $this->sdk->t('openorder', 'txtWeight'),
            'txtAddPackageLength'  => $this->sdk->t('openorder', 'txtLength'),
            'txtAddPackageWidth'   => $this->sdk->t('openorder', 'txtWidth'),
            'txtAddPackageHeight'  => $this->sdk->t('openorder', 'txtHeight'),
            'txtPkgDefaultPackage' => $this->sdk->t('pkgdimension', 'txtPkgDefaultPackage'),
            'txtPkgEdit'           => $this->sdk->t('button', 'txtEdit'),
            'txtPkgDelete'         => $this->sdk->t('button', 'txtDelete'),
            'txtPkgUnit'           => $this->sdk->t('pkgdimension', 'txtPkgUnit'),
            'ttlWarning'           => $this->sdk->t('openorder', 'ttlWarning'),

            'lengthUnits'          => $this->sdk->lengthUnitPrototypes,
            'weightUnits'          => $this->sdk->weightUnitPrototypes,
            
            'selected_weightUnit'  => $selected_weightUnit,
            'selected_lenghtUnit'  => $selected_lenghtUnit,
        );
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsPkgDimension');
    }

    public function initContent()
    {
        if (Tools::getValue('ajax')) {
            if (Tools::getValue('pkg_function') && Tools::getValue('pkg_function') == 'edit') {
                $this->handleLoadDataEditPkg();
            }

            if (Tools::getValue('pkg_function_save') && Tools::getValue('pkg_function_save') == 'saveEdit') {
                $this->handleSaveEditPkg();
            }

            if (Tools::getValue('pkg_function_add') && Tools::getValue('pkg_function_add') == 'addPkg') {
                $this->handleAddPackage();
            }

            if (Tools::getValue('pkg_function_delete') && Tools::getValue('pkg_function_delete') == 'delete') {
                $this->handleDeletePkg();
            }
            if (Tools::getValue('pkg_function_next') && Tools::getValue('pkg_function_next') == 'next') {
                $this->handleButtonNext();
            }
        } else {
            $data = array(
                'content' => $this->texts,
            );

            $listPkgs = $this->displayPackageUnits($this->getPackages());

            if (!empty($listPkgs)) {
                $firstPackage                         = key($listPkgs);
                $listPkgs[$firstPackage]['isDefault'] = '1';
                $data['listPkg']                      = $listPkgs;
            }
            $path = _PS_MODULE_DIR_ . $this->module->name .'/views/templates/admin/ups_pkg_dimension/pkg_dimension.tpl';

            $this->content .= $this->context->smarty->createTemplate($path, null, null, $data)->fetch();
            parent::initContent();
        }
    }

    private function handleButtonNext()
    {
        $arrNextReturn = array();
        $arrayListPkg  = unserialize(Configuration::get('UPS_PKG_DIMENSION_COUNT'));

        if (is_array($arrayListPkg) && !empty($arrayListPkg)) {
            $arrNextReturn['error'] = '';
            $arrNextReturn['token'] = Tools::getAdminTokenLite('AdminUpsDeliveryRates');
            CommonFunction::setDoneConfigScreen(Tools::getValue('controller'));
        } else {
            $arrNextReturn['error'] = $this->sdk->t('pkgdimension', 'txtErrorNext');
        }

        $this->ajaxDie(json_encode($arrNextReturn));
    }

    private function handleAddPackage()
    {
        $arrAddReturn = array();

        $checkWeight   = $this->checkWeight(Tools::getValue('add_weight'), Tools::getValue('addWeightUnit'));
        $checkDimesion = $this->checkDimesion(
            Tools::getValue('add_length'),
            Tools::getValue('add_width'),
            Tools::getValue('add_height'),
            Tools::getValue('addLengthUnit')
        );
        if ((empty($checkWeight) || $checkWeight == 2) && (empty($checkDimesion) || $checkDimesion == 2)) {
            if ($this->checkValidateAddPkg()) {
                $nextIncrease = $this->calutateIncrease();
                array_push($this->listNumberPkg, $nextIncrease);

                $inputPackage = array(
                    'name'       => trim(Tools::getValue('addNamePkg')),
                    'weight'     => Tools::getValue('add_weight'),
                    'weightUnit' => Tools::getValue('addWeightUnit'),
                    'lenght'     => Tools::getValue('add_length'),
                    'width'      => Tools::getValue('add_width'),
                    'height'     => Tools::getValue('add_height'),
                    'lenghtUnit' => Tools::getValue('addLengthUnit'),
                    'id'         => $nextIncrease,
                );

                Configuration::updateValue('UPS_PKG_' . $nextIncrease . '_DIMENSION', serialize($inputPackage));
                Configuration::updateValue('UPS_PKG_DIMENSION_COUNT', serialize($this->listNumberPkg));

                $arrAddReturn['error'] = '';
            } elseif ($this->handleExistNamePkg(trim(Tools::getValue('addNamePkg')))) {
                $arrAddReturn['error'] = $this->sdk->t('err-msg', 'pkgExist');
            } else {
                $message = $this->sdk->t('err-msg', 'notValid');
                if ($this->module->usa()) {
                    $message = $this->sdk->t('err-msg', 'notValidUS');
                }
                $arrAddReturn['error'] = $message;
            }
        } else {
            $errorDimension = $this->sdk->t('pkgdimension', 'errorDimension');
            $error = '';
            //(!$checkWeight) ? $error .= $this->sdk->t('pkgdimension', 'errorWeightPackageMaximum') : '';
            //(!$checkDimesion) ? $error .= $errorDimension : '';
            if (empty($checkWeight)) {
                $error .= $this->sdk->t('pkgdimension', 'errorWeightPackageMaximum');
            } elseif ($checkWeight == 3) {
                $error .= $this->sdk->t('pkgdimension', 'errorWeightPackageMaximum3');
            } else {
                $error .= '';
            }
            
            if (empty($checkDimesion)) {
                $error .= $this->sdk->t('pkgdimension', 'errorDimension');
            } elseif ($checkDimesion == 3) {
                $error .= $this->sdk->t('pkgdimension', 'errorDimension3');
            } elseif ($checkDimesion == 4) {
                $error .= $this->sdk->t('pkgdimension', 'errorDimension4');
            } else {
                $error .= '';
            }
            $arrAddReturn['error'] = $error;
        }
        $this->ajaxDie(json_encode($arrAddReturn));
    }

    private function calutateIncrease()
    {
        $arrayCount = unserialize(Configuration::get('UPS_PKG_DIMENSION_COUNT'));
        if (is_array($arrayCount) && !empty($arrayCount)) {
            $this->listNumberPkg = $arrayCount;
            $nextIncrease        = (int) max($this->listNumberPkg) + 1;
            return $nextIncrease;
        }
        return 1;
    }

    public function initModal()
    {
        $this->context->smarty->assign(array(
            'content' => $this->texts,

        ));

        $this->modals[] = array(
            'modal_id'      => 'modalEditPkg',
            'modal_content' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/ups_pkg_dimension/modalEditPkg.tpl'
            ),
        );

        $this->modals[] = array(
            'modal_id'      => 'modalDeletePkg',
            'modal_content' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/ups_pkg_dimension/modalDeletePkg.tpl'
            ),
        );
    }

    public function renderModal()
    {
        $modal_render = '';
        if (is_array($this->modals) && count($this->modals)) {
            foreach ($this->modals as $modal) {
                $this->context->smarty->assign($modal);
                $modal_render .= $this->context->smarty->fetch('modal.tpl');
            }
        }
        return $modal_render;
    }

    public function setMedia($isNewTheme = false)
    {
        $sdk = $this->sdk;
        if ($this->module->usa()) {//Chi ap dung cho US
            $errorDimension = $sdk->t('pkgdimension', 'errorDimensionUS');
        } else {
            $errorDimension = $sdk->t('pkgdimension', 'errorDimension');
        }
        Media::addJsDef(array(
            'errorWeightPackageMaximum'   => $sdk->t('pkgdimension', 'errorWeightPackageMaximum'),
            'errorWeightPackageMaximum3'   => $sdk->t('pkgdimension', 'errorWeightPackageMaximum3'),
            'warningWeightPackageMaximum' => $sdk->t('pkgdimension', 'warningWeightPackageMaximum'),
            'warningWeightPackageMaximum2' => $sdk->t('pkgdimension', 'warningWeightPackageMaximum2'),
            'warningWeightPackageMaximum3' => $sdk->t('pkgdimension', 'warningWeightPackageMaximum3'),
            'warningWeightPackageMaximum4' => $sdk->t('pkgdimension', 'warningWeightPackageMaximum4'),
            'errorDimension'              => $errorDimension,
            'warningDimensionLang'        => $sdk->t('pkgdimension', 'warningDimensionLang'),
            'errorDimension2'        => $sdk->t('pkgdimension', 'errorDimension2'),
            'errorDimension3'        => $sdk->t('pkgdimension', 'errorDimension3'),
            'errorDimension4'        => $sdk->t('pkgdimension', 'errorDimension4'),
            'country'                   => Configuration::get('UPS_COUNTRY_SELECTED')
        ));
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/upspkgdimension.js');
    }

    private function getInfoPkg()
    {
        $getInforPkg = unserialize(Configuration::get('UPS_PKG_' . $this->getIdPkg . '_DIMENSION'));

        return $getInforPkg;
    }

    private static function validateAddPkgName($inputPkg)
    {
        $inputPkgStrlen = Tools::strlen($inputPkg);

        if (($inputPkgStrlen >= 1) && ($inputPkgStrlen <= 50)) {
            return true;
        }
        return false;
    }

    private static function validateAddPkgUnit($inputPkgs)
    {
        
        if (preg_match('/^\d+(\.\d{1,2})?$/', $inputPkgs) && $inputPkgs >= 0.01 && $inputPkgs <= 9999.99) {
            return true;
        }
        return false;
    }

    private static function checkWeight($weight, $unit)
    {
        $weight = (float)$weight;
        $countrySelected = Configuration::get('UPS_COUNTRY_SELECTED');
        switch ($unit) {
            case 'KGS':
                return $weight <= 70 ? 0 : 1;
            case 'LBS':
                $value = 0;
                if ($countrySelected == 'US') {
                    if ($weight > 44 && $weight <= 150) {
                        $value = 2;
                    } elseif ($weight > 150) {
                        $value = 3;
                    } else {
                        $value = 0;
                    }
                } else {
                    if ($weight < 154.32) {
                        $value = 0;
                    } else {
                        $value = 1;
                    }
                    //return $weight < 154.32 ? 0 : 1;
                }
                return $value;
            default:
                return 0;
        }
    }

    private static function checkDimesion($length, $width, $height, $unit)
    {
        $countrySelected = Configuration::get('UPS_COUNTRY_SELECTED');
        $length = (float)$length;
        $width  = (float)$width;
        $height = (float)$height;
        $dimension = $length + ($width * 2) + ($height * 2);
        switch ($unit) {
            case 'CM':
                return $dimension <= 400 ? 0 : 1;
            case 'IN':
                $value = 0;
                if ($countrySelected == 'US') {
                    if ($dimension > 130 && $dimension <= 165) {
                        $value = 2;
                    } elseif ($dimension > 165) {
                        $value = 3;
                    } elseif ($length > 108 || $width > 108 || $height > 108) {
                        $value = 4;
                    } else {
                        $value = 0;
                    }
                } else {
                    if ($dimension < 157.48) {
                        $value = 0;
                    } else {
                        $value = 1;
                    }
                    //return $dimension < 157.48 ? 0 : 1;
                }
                return $value;
            default:
                return 0;
        }
    }

    private function checkValidateAddPkg()
    {
        if ($this->validateAddPkgName(Tools::getValue('addNamePkg'))
            && $this->validateAddPkgUnit(Tools::getValue('add_weight'))
            && $this->validateAddPkgUnit(Tools::getValue('add_length'))
            && $this->validateAddPkgUnit(Tools::getValue('add_width'))
            && $this->validateAddPkgUnit(Tools::getValue('add_height'))
            && !$this->handleExistNamePkg(trim(Tools::getValue('addNamePkg')))
        ) {
            return true;
        }
        return false;
    }

    private function checkValidateEditPkg()
    {
        if ($this->validateAddPkgName(Tools::getValue('namePkg'))
            && $this->validateAddPkgUnit(Tools::getValue('weight'))
            && $this->validateAddPkgUnit(Tools::getValue('lenght'))
            && $this->validateAddPkgUnit(Tools::getValue('width'))
            && $this->validateAddPkgUnit(Tools::getValue('height'))
        ) {
            return true;
        }
        return false;
    }

    private function handleLoadDataEditPkg()
    {
        $packageId = 'UPS_PKG_' . Tools::getValue('package_id') . '_DIMENSION';
        $package   = $this->getPackages(array($packageId))[0];

        $this->ajaxDie(json_encode($package));
    }

    private function handleSaveEditPkg()
    {
        $arrReturn   = array();
        $idUpdatePkg = (int) Tools::getValue('package_id');
        $namePkg     = trim(Tools::getValue('namePkg'));
        
        $checkWeight = $this->checkWeight(Tools::getValue('weight'), Tools::getValue('weightUnit'));
        $checkDimesion = $this->checkDimesion(
            Tools::getValue('lenght'),
            Tools::getValue('width'),
            Tools::getValue('height'),
            Tools::getValue('lenghtUnit')
        );
        
        if ((empty($checkWeight) || $checkWeight == 2) && (empty($checkDimesion) || $checkDimesion == 2)) {
            if ($this->checkValidateEditPkg() && !$this->handleExistNamePkgEdit($namePkg, $idUpdatePkg)) {
                $inputPackage = array(
                    'name'       => $namePkg,
                    'weight'     => Tools::getValue('weight'),
                    'weightUnit' => Tools::getValue('weightUnit'),
                    'lenght'     => Tools::getValue('lenght'),
                    'width'      => Tools::getValue('width'),
                    'height'     => Tools::getValue('height'),
                    'lenghtUnit' => Tools::getValue('lenghtUnit'),
                    'id'         => $idUpdatePkg,
                );
    
                Configuration::updateValue('UPS_PKG_' . $idUpdatePkg . '_DIMENSION', serialize($inputPackage));
    
                $arrReturn['error'] = '';
                if ($idUpdatePkg == 1) {
                    $dataTransfer = array();
                    $dataTransfer['merchantKey'] = Configuration::get('MERCHANT_KEY');
                    // Fix bug Grammar
                    $dataTransfer['name'] = $inputPackage['name'];
                    $dataTransfer['weight'] = $inputPackage['weight'];
                    $dataTransfer['weightUnit'] = $inputPackage['weightUnit'] == 'KGS' ? 'Kg' : 'Pounds';
                    $dataTransfer['length'] = $inputPackage['lenght'];
                    $dataTransfer['width'] = $inputPackage['width'];
                    $dataTransfer['height'] = $inputPackage['height'];
                    $dataTransfer['dimensionUnit'] = $inputPackage['lenghtUnit'] == 'CM' ? 'Cm' : 'Inch';
    
                    $this->transferDefaultPackage($dataTransfer);
                }
            } elseif ($this->handleExistNamePkgEdit($namePkg, $idUpdatePkg)) {
                $arrReturn['error'] = $this->sdk->t('err-msg', 'pkgExist');
                $arrReturn['existName'] = 'exist';
            } else {
                $message = $this->sdk->t('err-msg', 'notValid');
                if ($this->module->usa()) {
                    $message = $this->sdk->t('err-msg', 'notValidUS');
                }
                $arrReturn['error'] = $message;
            }
        } else {
            if ($this->module->usa()) {//Chi ap dung cho US
                $errorDimension = $this->sdk->t('pkgdimension', 'errorDimensionUS');
            } else {
                $errorDimension = $this->sdk->t('pkgdimension', 'errorDimension');
            }
            $error = '';
            //(!$checkWeight) ? $error .= $this->sdk->t('pkgdimension', 'errorWeightPackageMaximum') : '';
            if (empty($checkWeight)) {
                $error .= $this->sdk->t('pkgdimension', 'errorWeightPackageMaximum');
            } elseif ($checkWeight == 3) {
                $error .= $this->sdk->t('pkgdimension', 'errorWeightPackageMaximum3');
            } else {
                $error .= '';
            }
            //(!$checkDimesion) ? $error .= $errorDimension : '';
            if (empty($checkDimesion)) {
                $error .= $this->sdk->t('pkgdimension', 'errorDimension');
            } elseif ($checkDimesion == 3) {
                $error .= $this->sdk->t('pkgdimension', 'errorDimension3');
            } elseif ($checkDimesion == 4) {
                $error .= $this->sdk->t('pkgdimension', 'errorDimension4');
            } else {
                $error .= '';
            }
            $arrReturn['error'] = $error;
        }

        $this->ajaxDie(json_encode($arrReturn));
    }

    private function handleDeletePkg()
    {
        $arrReturn   = array();
        $idDeletePkg = (int) Tools::getValue('delete_packageid');

        if (Configuration::get('UPS_PKG_' . $idDeletePkg . '_DIMENSION')) {
            Configuration::deleteByName('UPS_PKG_' . $idDeletePkg . '_DIMENSION');
            $listIndexPkg = unserialize(Configuration::get('UPS_PKG_DIMENSION_COUNT'));

            $keyIDpKG = array_search($idDeletePkg, $listIndexPkg);
            unset($listIndexPkg[$keyIDpKG]);

            Configuration::updateValue('UPS_PKG_DIMENSION_COUNT', serialize($listIndexPkg));
            $arrReturn['error'] = '';
        }
        $this->ajaxDie(json_encode($arrReturn));
    }

    private function handleExistNamePkg($namePkg)
    {
        $listPkgs = $this->getPackages();

        foreach ($listPkgs as $value) {
            if ($value['name'] == $namePkg) {
                return true;
            }
        }

        return false;
    }

    private function handleExistNamePkgEdit($namePkg, $id_pkg)
    {
        $listPkgs = $this->getPackages();

        foreach ($listPkgs as $value) {
            if (($value['id'] != $id_pkg) && ($value['name'] == $namePkg)) {
                return true;
            }
        }

        return false;
    }
}
