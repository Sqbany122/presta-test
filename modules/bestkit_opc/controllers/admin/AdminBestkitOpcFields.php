<?php
/**
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
*
*  @author     BEST-KIT.COM (contact@best-kit.com)
*  @copyright  http://best-kit.com
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once (_PS_MODULE_DIR_ . 'bestkit_opc/includer.php');
require_once _PS_MODULE_DIR_ . 'bestkit_opc/classes/BestkitHelperForm.php';

class AdminBestkitOpcFieldsController extends ModuleAdminController
{	
	public function displayAjaxLoadFieldInfo() {
		$id_field = Tools::getValue('fields_id');
		if ($id_field) {
			$moduleObj = Module::getInstanceByName('bestkit_opc');
			$fieldObj = new BestkitOpcCheckoutFields($id_field);
			
			if ($moduleObj->id && $fieldObj) {
				$helper = new BestkitHelperForm();
				$helper->module = $moduleObj;
				$helper->name_controller = $moduleObj->name;
				$helper->identifier = 'id_bestkit_opc_checkoutfield';
				$helper->id = $fieldObj->id;
				$helper->token = Tools::getAdminTokenLite('AdminModules');
				$helper->currentIndex = AdminController::$currentIndex . '&configure=' . $moduleObj->name;
				$helper->toolbar_scroll = TRUE;
				//$helper->toolbar_btn = $moduleObj->initToolbar();
				$helper->title = $moduleObj->displayName;
				$languages = Language::getLanguages(FALSE);
				foreach ($languages as $k => $language) {
					$languages[$k]['is_default'] = (int)($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
				}
				$helper->languages = $languages;
				$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
				
				//assign fields info
				$step = array();
				$available_steps = array('customer', 'invoice', 'delivery');
				foreach ($available_steps as $available_step) {
					$step[] = array(
						'id' => $available_step,
						'step' => $available_step,
					);
				}
				$validate = array();
				$reflection = new ReflectionClass('Validate');
				$aMethods = $reflection->getMethods();
				foreach ($aMethods as $reflectionMethodObject) {
					$validate[] = array(
						'id' => $reflectionMethodObject->name,
						'validate' => $reflectionMethodObject->name,
					);
				}
				$helper->fields_value['step'] = $fieldObj->step;
				$helper->fields_value['name'] = $fieldObj->name;
				$helper->fields_value['public_name'] = $fieldObj->public_name;
				$helper->fields_value['validate'] = $fieldObj->validate;
				$helper->fields_value['required_new'] = $fieldObj->required;
				$helper->fields_value['default_value'] = $fieldObj->default_value;
				$helper->fields_value['active_new'] = $fieldObj->active;
				
				$this->fields_form['opc_checkout_fields_new']['form'] = array(
					'tinymce' => TRUE,
					'col' => '12',
					'legend' => array(
						'title' => $this->l('Edit field'),
						//'image' => $this->_path . 'logo.gif'
					),
					'input' => array(
						array(
							'type' => 'select',
							'label' => $this->l('Step'),
							'name' => 'step',
							'required' => TRUE,
							'options' => array(
								'query' => $step,
								'id' => 'id',
								'name' => 'step',
							),
						),
						array(
							'type' => 'text',
							'label' => $this->l('Name'),
							'name' => 'name',
							'required' => TRUE,
							'col' => 3,
						),
						array(
							'type' => 'text',
							'label' => $this->l('Public name'),
							'name' => 'public_name',
							'required' => TRUE,
							'lang' => TRUE,
							'col' => 3,
						),
						array(
							'type' => 'select',
							'label' => $this->l('Validator'),
							'name' => 'validate',
							'options' => array(
								'query' => $validate,
								'id' => 'id',
								'name' => 'validate',
							),
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Is required'),
							'name' => 'required_new',
							'required' => false, 
							'class' => 't',
							'is_bool' => true,
							'values' => $moduleObj->getOnOffValues('required'),
						),
						array(
							'type' => 'text',
							'label' => $this->l('Default value'),
							'name' => 'default_value',
							'col' => 3,
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Active'),
							'name' => 'active_new',
							'required' => false, 
							'class' => 't',
							'is_bool' => true,
							'values' => $moduleObj->getOnOffValues('active'),
						),
					),
					'buttons' => array(
						array(
							'type' => 'button',
							'id' => 'cancel_field',
							'name' => 'cancel_field',
							'class' => 'pull-right',
							'icon' => 'process-icon-cancel',
							'title' => $this->l('Cancel'),
						),
						array(
							'type' => 'submit',
							'id' => 'submit_edit_field',
							'name' => 'submit_edit_field',
							'class' => 'pull-right',
							'icon' => 'process-icon-save',
							'title' => $this->l('Save field'),
						),
					)
				);
				
				$opc_checkout_field = $helper->generateForm(array($this->fields_form['opc_checkout_fields_new']));

				echo $opc_checkout_field; 
				die;
			}
			
		}
	}
}