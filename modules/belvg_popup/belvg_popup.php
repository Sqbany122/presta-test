<?php
/**
* 2007-2018 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Belvg
*  @copyright  2007-2018 Belvg
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Belvg_popup extends Module
{
    protected $config_form = false;
    public $img_path;

    public function __construct()
    {
        $this->name = 'belvg_popup';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.5';
        $this->author = 'BelVG';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Promo Popup');
        $this->description = $this->l('Promo Popup module. ');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->img_path = $this->_path.'views/img/';
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('BELVG_POPUP_ENABLED', false);
        Configuration::updateValue('BELVG_POPUP_DISPLAY_TYPE', 'newsletter');
        Configuration::updateValue('BELVG_POPUP_DISPLAY_AREA', 'home');
        Configuration::updateValue('BELVG_POPUP_TRIGGER_MECHANISM', 'delay');
        Configuration::updateValue('BELVG_POPUP_DELAY', 3);
        Configuration::updateValue('BELVG_POPUP_NUMBER_OF_APPEARANCE', 10);
        Configuration::updateValue('BELVG_POPUP_COOKIE_NAME_PREFIX', 'test');


        $belvgPopupContent = $this->getExampleTemplateContent();
        Db::getInstance()->insert('configuration', array(
            'name' => 'BELVG_POPUP_CONTENT',
            'value' => pSQL($belvgPopupContent, true),
            'date_upd' => date('Y-m-d H:i:s')
        ));


        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('displayHeader');
    }

    public function upgrade(){

    }

    public function uninstall()
    {
        Configuration::deleteByName('BELVG_POPUP_ENABLED');
        Configuration::deleteByName('BELVG_POPUP_DISPLAY_TYPE');
        Configuration::deleteByName('BELVG_POPUP_CART_RULE');
        Configuration::deleteByName('BELVG_POPUP_DISPLAY_AREA');
        Configuration::deleteByName('BELVG_POPUP_TRIGGER_MECHANISM');
        Configuration::deleteByName('BELVG_POPUP_DELAY');
        Configuration::deleteByName('BELVG_POPUP_SCROLL_COUNT');
        Configuration::deleteByName('BELVG_POPUP_NUMBER_OF_APPEARANCE');
        Configuration::deleteByName('BELVG_POPUP_COOKIE_NAME_PREFIX');
        Configuration::deleteByName('BELVG_POPUP_CONTENT');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitBelvg_popupModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('controller', $this->context->link->getAdminLink('AdminBelvgPopup'));
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/info.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBelvg_popupModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $templates = $this->getDataTemplates();
        $holidays = [];


        foreach ($templates as $template) {
            $holidays[$template['type_id'].'_'.$template['holiday_id']] = [
                'type_id'=>$template['type_id'],
                'value'=>$template['holiday_id'],
                'label'=>$template['holiday'],
            ];
        }
        $this->context->smarty->assign('holidayList', $holidays);
        $this->context->smarty->assign('templateList', $templates);

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    //Enabled Field
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enabled'),
                        'name' => 'BELVG_POPUP_ENABLED',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    //Cookie name
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'label' => $this->trans('Form Id', array(), 'Modules.BelVGPopup.Admin'),
                        'name' => 'BELVG_POPUP_COOKIE_NAME_PREFIX',
                        'desc' => $this->l('Update when you create new popUp.'),
                    ),


                    //Display Type Field
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Display Mode', array(), 'Modules.BelVG_Popup.Admin'),
                        'name' => 'BELVG_POPUP_DISPLAY_TYPE',
                        'required' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'banner',
                                    'name' => $this->trans(
                                        'Banner',
                                        array(),
                                        'Modules.BelvgPopup.Admin'
                                    )
                                ),
                                array(
                                    'id' => 'coupon',
                                    'name' => $this->trans(
                                        'Promo coupon',
                                        array(),
                                        'Modules.BelvgPopup.Admin'
                                    )
                                ),
                                array(
                                    'id' => 'newsletter',
                                    'name' => $this->trans(
                                        'Newsletter',
                                        array(),
                                        'Modules.BelvgPopup.Admin'
                                    )
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    //Cart Rule Field
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Cart rule', array(), 'Modules.BelVG_Popup.Admin'),
                        'name' => 'BELVG_POPUP_CART_RULE',
                        'class' => 'hideField',
                        'required' => false,
                        'options' => array(
                            'query' => $this->getCartRuleOptions(),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    //Display Area Field
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Display Area', array(), 'Modules.BelVG_Popup.Admin'),
                        'name' => 'BELVG_POPUP_DISPLAY_AREA',
                        'required' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'all',
                                    'name' => $this->trans(
                                        'All Pages',
                                        array(),
                                        'Modules.BelvgPopup.Admin'
                                    )
                                ),
                                array(
                                    'id' => 'home',
                                    'name' => $this->trans(
                                        'Home Page',
                                        array(),
                                        'Modules.BelvgPopup.Admin'
                                    )
                                ),
                                array(
                                    'id' => 'checkout',
                                    'name' => $this->trans(
                                        'Checkout Page',
                                        array(),
                                        'Modules.BelvgPopup.Admin'
                                    )
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    //Trigger Mechanism Field
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Display Method', array(), 'Modules.BelVG_Popup.Admin'),
                        'name' => 'BELVG_POPUP_TRIGGER_MECHANISM',
                        'required' => false,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'delay',
                                    'name' => $this->trans(
                                        'Delay',
                                        array(),
                                        'Modules.BelVGPopup.Admin'
                                    )
                                ),
                                array(
                                    'id' => 'scroll',
                                    'name' => $this->trans(
                                        'Scrolling',
                                        array(),
                                        'Modules.BelVGPopup.Admin'
                                    )
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    //Delay Field
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'label' => $this->trans('Delay', array(), 'Modules.BelVGPopup.Admin'),
                        'name' => 'BELVG_POPUP_DELAY',
                        'desc' => $this->trans('Delay before show modal (in second)', array(), 'Modules.BelVGPopup.Admin'),
                        'suffix' => 'second',
                    ),
                    //Scroll Count Field
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'label' => $this->trans('Scroll count', array(), 'Modules.BelVGPopup.Admin'),
                        'name' => 'BELVG_POPUP_SCROLL_COUNT',
                        'desc' => $this->trans('Scroll count before show modal', array(), 'Modules.BelVGPopup.Admin'),
                    ),
                    //Number of appearance Field
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'label' => $this->trans('Number of appearance', array(), 'Modules.BelVGPopup.Admin'),
                        'name' => 'BELVG_POPUP_NUMBER_OF_APPEARANCE',
                    ),

                    array(
                        'col' => 3,
                        'type' => 'html',
                        'label' => $this->trans('Templates', array(), 'Modules.BelVGPopup.Admin'),
                        'name' => 'BELVG_POPUP_TEMPLATES',
                        'html_content'=>$this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl'),
                        'desc' => '',
                    ),

                    //PopUp Content Field
                    array(
                        'type' => 'textarea',
                        'lang' => false,
                        'label' => $this->getTranslator()->trans('Content', array(), 'Admin.Global'),
                        'name' => 'BELVG_POPUP_CONTENT',
                        'autoload_rte' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getCartRuleOptions()
    {
        $cartRuleOptions = [];
        $cartRules = $this->getCartRules();
        $cartRules->where('active', '=', '1');
        $employeeLang = $this->context->employee->id_lang;

        foreach ($cartRules as $cartRule){
            $cartRuleOptions[] = [
                'id' => $cartRule->id,
                'name' => $this->l($cartRule->name[$employeeLang])
            ];
        }

        return $cartRuleOptions;
    }

    /**
     * @return PrestaShopCollection
     */
    protected function getCartRules()
    {
        return new PrestaShopCollection('CartRule');
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'BELVG_POPUP_ENABLED' => Configuration::get('BELVG_POPUP_ENABLED', true),
            'BELVG_POPUP_DISPLAY_TYPE' => Configuration::get('BELVG_POPUP_DISPLAY_TYPE', true),
            'BELVG_POPUP_CART_RULE' => Configuration::get('BELVG_POPUP_CART_RULE', true),
            'BELVG_POPUP_DISPLAY_AREA' => Configuration::get('BELVG_POPUP_DISPLAY_AREA', true),
            'BELVG_POPUP_TRIGGER_MECHANISM' => Configuration::get('BELVG_POPUP_TRIGGER_MECHANISM', true),
            'BELVG_POPUP_DELAY' => Configuration::get('BELVG_POPUP_DELAY', true),
            'BELVG_POPUP_SCROLL_COUNT' => Configuration::get('BELVG_POPUP_SCROLL_COUNT', true),
            'BELVG_POPUP_NUMBER_OF_APPEARANCE' => Configuration::get('BELVG_POPUP_NUMBER_OF_APPEARANCE', true),
            'BELVG_POPUP_COOKIE_NAME_PREFIX' => Configuration::get('BELVG_POPUP_COOKIE_NAME_PREFIX', true),
            'BELVG_POPUP_CONTENT' => Configuration::get('BELVG_POPUP_CONTENT', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            if ($key === 'BELVG_POPUP_CONTENT') {
                Db::getInstance()->update('configuration', array(
                    'value' => pSQL(Tools::getValue($key), true),
                    'date_upd' => date('Y-m-d H:i:s'),
                ), '`name` = \'BELVG_POPUP_CONTENT\'' , 1, true);
                Configuration::set('BELVG_POPUP_CONTENT', Tools::getValue($key), null, null);
            } else {
                Configuration::updateValue($key, Tools::getValue($key), false);
            }
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (
            $this->context->controller->controller_name === 'AdminModules'
            && Tools::getValue('configure') === $this->name
        ) {
            $this->context->controller->addJS($this->_path . '/views/js/belvg_popup_back.js');
            $this->context->controller->addCSS($this->_path.'/views/css/belvg_popup_back.css');
            $this->context->controller->addCSS($this->_path . '/views/css/halloween/styles.css');
        }
    }

    /**
     * @return string
     */
    public function hookDisplayHeader()
    {
        $isEnabled = (bool)Configuration::get('BELVG_POPUP_ENABLED');
        if(!$isEnabled){
            return '';
        }

        $addJs = false;
        $controllerId = Tools::getValue('controller');

        if($this->getDisplayArea() === 'all') {
            $addJs = true;
        }

        if($controllerId === 'index' && $this->getDisplayArea() === 'home')      {
            $addJs = true;
        }

        if($controllerId === 'cart' && $this->getDisplayArea() === 'checkout')      {
            $addJs = true;
        }

        if($addJs) {
            $this->context->controller->registerStylesheet(
                'modules-belvg-popup-halloween',
                'modules/' . $this->name . '/views/css/halloween/styles.css',
                ['media' => 'all', 'priority' => 200]);
            $this->context->controller->addJqueryPlugin(['fancybox']);
        }

        if ($this->getDisplayArea() === 'all') {
            return $this->showPopUp();
        }

        if ($controllerId === 'index' && $this->getDisplayArea() === 'home') {
            return $this->showPopUp();
        }

        if ($controllerId === 'cart' && $this->getDisplayArea() === 'checkout') {
            return $this->showPopUp();
        }

    }

    /**
     * @return string
     */
    protected function showPopUp()
    {
        $data =  $this->getVariablesForTemplate();
        $data['BELVG_POPUP_CONTENT'] = str_replace('<tmp','<link',$data['BELVG_POPUP_CONTENT']);
        $this->context->smarty->assign($data);
        $this->context->smarty->assign(
            'BELVG_POPUP_FRONT_CONTROLLER_LINK',
            Context::getContext()->link->getModuleLink(
                'belvg_popup', 'subscription',
                array('displayType' => Configuration::get('BELVG_POPUP_DISPLAY_TYPE'))
            )
        );

        return $this->display(__FILE__, 'popup.tpl');
    }

    /**
     * @return array
     */
    protected function getVariablesForTemplate()
    {
        $result = [];
        $configurationVariables = $this->getConfigFormValues();
        foreach ($configurationVariables as $key => $val) {
            if ($key === 'BELVG_POPUP_CONTENT') {
                $result[$key] = json_encode($val);
            } else {
                $result[$key] = $val;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getDisplayArea()
    {
        return Configuration::get('BELVG_POPUP_DISPLAY_AREA');
    }

    protected function getDataTemplates() {
        $index = [];
        $options = [];
        $files = $this->getListOfTemplates();
        foreach ($files as $file) {
            $fileParts = explode('_',$file);
            $holidayName = ucwords($fileParts[1]);
            $holidayId =  strtolower(str_replace(['_','.tpl'],[' ',''],$fileParts[1]));
            $typeId = strtolower(str_replace(['_','.tpl'],[' ',''],$fileParts[2]));
            $index[$typeId][$holidayId] = (isset($index[$typeId]) && isset( $index[$typeId][$holidayId])) ? $index[$typeId][$holidayId]+1 : 1;
            $options[] = ['value'=>$file,'label'=>'Template '.$index[$typeId][$holidayId],'holiday'=>$holidayName,'holiday_id'=>$holidayId,'type_id'=>$typeId];
        }
        return $options;
    }

    /**
     * @return array
     */
    protected function getListOfTemplates()
    {
        return $this->dirToArray(_PS_MODULE_DIR_ . 'belvg_popup/views/templates/hook/popUpContent');
    }

    /**
     * @param $dir
     *
     * @return array
     */
    protected function dirToArray($dir)
    {
        return array_slice(scandir($dir, null), 2);
    }

    protected function getExampleTemplateContent()
    {
        $filePath = _PS_MODULE_DIR_ . 'belvg_popup/views/templates/hook/popUpContent/1-1_halloween_newsletter.tpl';
        $imagePath = _MODULE_DIR_ . $this->name;
        $text = file_get_contents($filePath);
        $text = str_replace('{$imageDir}', $imagePath, $text);

        return $text;
    }
}
