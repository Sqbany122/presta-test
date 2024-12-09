<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

include_once(_PS_MODULE_DIR_.'backinstock/classes/KbBisCustomFields.php');

class AdminKbBisFieldsController extends ModuleAdminController
{
    public $kb_smarty;
    public $all_languages = array();
    protected $field_type_arr = array();
    protected $kb_module_name = 'backinstock';
    protected $position_identifier = 'id_field';
    protected $ps_shop = array();
    public function __construct()
    {
        $this->bootstrap = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->kb_smarty = new Smarty();
        $this->kb_smarty->registerPlugin('function', 'l', 'smartyTranslate');
        $this->kb_smarty->setTemplateDir(_PS_MODULE_DIR_.$this->kb_module_name.'/views/templates/admin/');

        $this->all_languages = $this->getAllLanguages();
        $this->table = 'kb_bis_fields';
        $this->className = 'KbBisCustomFields';
        $this->identifier = 'id_field';
        $this->lang = false;
        $this->display = 'list';
        parent::__construct();
        
        $this->toolbar_title = $this->module->l('Knowband Back in stock Custom Fields', 'AdminKbBisFieldsController');
        foreach (Shop::getShops(false) as $shop) {
            $this->ps_shop[$shop['id_shop']] = $shop['name'];
        }
        $this->field_type_arr['text'] = $this->module->l('Text', 'AdminKbBisFieldsController');
        $this->field_type_arr['select'] = $this->module->l('Select', 'AdminKbBisFieldsController');
        $this->field_type_arr['radio'] = $this->module->l('Radio', 'AdminKbBisFieldsController');
        $this->field_type_arr['checkbox'] = $this->module->l('Checkbox', 'AdminKbBisFieldsController');
        $this->field_type_arr['textarea'] = $this->module->l('Textarea', 'AdminKbBisFieldsController');
        
        $this->fields_list = array(
            'id_field' => array(
                'title' => $this->module->l('ID', 'AdminKbBisFieldsController'),
                'search' => true,
                'align' => 'text-center',
            ),
            'label' => array(
                'title' => $this->module->l('Label', 'AdminKbBisFieldsController'),
                'search' => true,
                'align' => 'text-center',
            ),
//            'field_name' => array(
//                'title' => $this->module->l('Field Name', 'AdminKbBisFieldsController'),
//                'search' => true,
//                'align' => 'text-center',
//            ),
            'type' => array(
                'title' => $this->module->l('Input Type', 'AdminKbBisFieldsController'),
                'search' => true,
                'align' => 'text-center',
                'type' => 'select',
                  'list' => $this->field_type_arr,
                'align' => 'center',
                'callback' => 'displayRuleTypeArr',
                 'filter_key' => 'a!type',
            ),
            'active' => array(
                'title' => $this->module->l('Status', 'AdminKbBisFieldsController'),
                'align' => 'text-center',
                'active' => 'active',
                'type' => 'bool',
                'order_key' => 'status',
                'search' => true
            ),
            'id_shop' => array(
                'title' => $this->module->l('Shop', 'AdminKbBisFieldsController'),
                'align' => 'center',
                'type' => 'select',
                'list' => $this->ps_shop,
                'filter_key' => 'a!id_shop',
                'callback' => 'psShopList',
//                'search' => false
            ),
            'position' => array(
                'title' => $this->module->l('Priority', 'AdminKbBisFieldsController'),
                'align' => 'text-center',
                'filter_key' => 'position',
                'search' => false,
                'position' => 'position',
            ),
        );

        $this->_select = 'a.*,c.*';
        $this->_join = 'INNER JOIN `' . _DB_PREFIX_ . 'kb_bis_fields_lang` c ON (a.id_field = c.id_field AND c.id_lang='.$this->context->language->id.')';
        $this->_orderBy = 'position';
        $this->_orderWay = 'ASC';
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function displayRuleTypeArr($echo, $tr)
    {
        unset($tr);
        if (isset($this->field_type_arr[$echo])) {
            return $this->field_type_arr[$echo];
        }
    }
    

    /*
     * Function for returning the URL of PrestaShop Root Modules Directory
     */
    protected function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }
    
    /*
     * Function for checking SSL
     */
    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * Function for returning all the languages in the system
     */
    public function getAllLanguages()
    {
        return Language::getLanguages(false);
    }
    
    /**
     * Prestashop Default Function in AdminController.
     * Assign smarty variables for all default views, list and form, then call other init functions
     */
    public function initContent()
    {
        if (isset($this->context->cookie->kb_redirect_error)) {
            $this->errors[] = $this->context->cookie->kb_redirect_error;
            unset($this->context->cookie->kb_redirect_error);
        }

        if (isset($this->context->cookie->kb_redirect_success)) {
            $this->confirmations[] = $this->context->cookie->kb_redirect_success;
            unset($this->context->cookie->kb_redirect_success);
        }
        parent::initContent();
    }
    
    
    /**
     * Prestashop Default Function in AdminController.
     * Init context and dependencies, handles POST and GET
     */
    public function init()
    {
       
        parent::init();
    }
    /**
     * Function used to display Edit field form
     */

    protected function getAllowedFieldType()
    {
        return array(
            array(
                'id' => null,
                'label' => $this->module->l('Select Type', 'AdminKbBisFieldsController'),
            ),
            array(
                'id' => 'text',
                'label' => $this->module->l('Text', 'AdminKbBisFieldsController'),
            ),
            array(
                'id' => 'select',
                'label' => $this->module->l('Select', 'AdminKbBisFieldsController'),
            ),
            array(
                'id' => 'radio',
                'label' => $this->module->l('Radio', 'AdminKbBisFieldsController'),
            ),
//            array(
//                'id' => 'checkbox',
//                'label' => $this->module->l('Checkbox', 'AdminKbBisFieldsController'),
//            ),
            array(
                'id' => 'textarea',
                'label' => $this->module->l('Text Area', 'AdminKbBisFieldsController'),
            ),
        );
    }
    
    /**
     * Function used for assign array for validation field
     */
    protected function getFieldValidation()
    {
        return array(
            array(
                'id' => null,
                'value' => $this->module->l('Select', 'AdminKbBisFieldsController'),
            ),
            array(
                'id' => 'isName',
                'value' => 'isName',
                
            ),
            array(
                'id' => 'isGenericName',
                'value' => 'isGenericName'
            ),
            array(
                'id' => 'isAddress',
                'value' => 'isAddress',
            ),
            array(
                'id' => 'isCityName',
                'value' => 'isCityName'
            ),
            array(
                'id' => 'isMessage',
                'value' => 'isMessage',
            ),
            array(
                'id' => 'isPhoneNumber',
                'value' => 'isPhoneNumber',
            ),
            array(
                'id' => 'isDniLite',
                'value' => 'isDniLite'
            ),
            array(
                'id' => 'isEmail',
                'value' => 'isEmail'
            ),
            array(
                'id' => 'isPasswd',
                'value' => 'isPasswd'
            ),
        );
    }
    /**
     * Function used to render the form for this controller
     *
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function renderForm()
    {
        if ((isset($this->tabAccess['edit']) && !$this->tabAccess['edit'] && Tools::getValue('id_field')) ||
            (isset($this->tabAccess['add']) && !$this->tabAccess['add'] && !Tools::getValue('id_field'))) {
            $this->errors[] = Tools::displayError('You do not have permission to use this form.');
            return false;
        }
        $this->table = 'kb_bis_fields';
        $this->className = 'KbBisCustomFields';
        
        $customizable = false;
        $tpl = $this->kb_smarty->createTemplate('kb_field_form.tpl');
        
        $tpl->assign(array(
            'kb_form_contents' => $this->getAddFieldForm(),
            'edit_field_form' => (Tools::getValue('id_field') && Tools::getIsset('update'.$this->table)) ? 1 : 0,
            'customizable' => $customizable,
            'moduledir_url' => $this->getModuleDirUrl(),
            'allowed_field_type' => $this->getAllowedFieldType(),
        ));

        return $tpl->fetch().parent::renderForm();
    }
    
    protected function getAddFieldForm()
    {
        $this->table = 'kb_bis_fields';
        $this->className = 'KbBisCustomFields';
        $tpl_vars = array();
        $object = new KbBisCustomFields(Tools::getValue('id_field'));
        if ((Tools::getValue('id_field') != '') && Tools::getIsset('update'.$this->table)) {
            $submit_btn = 'update_submit_kb_custom';
        } else {
            $submit_btn = 'add_submit_kb_custom';
        }

        $stores = array();
        foreach (Shop::getShops() as $shop) {
            $stores[] = array('id_shop' => $shop['id_shop'], 'name' => $shop['name']);
        }

        $this->fields_form = array(
            'form' => array(
                'id_form' => 'kbcf_add_custom_field',
                'legend' => array(
                    'title' => $this->module->l('Custom Field', 'AdminKbBisFieldsController'),
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->module->l('Select Field Type', 'AdminKbBisFieldsController'),
                        'hint' => $this->module->l('Select Type of the Field', 'AdminKbBisFieldsController'),
                        'name' => 'type',
                        'options' => array(
                            'query' => $this->getAllowedFieldType(),
                            'id' => 'id',
                            'name' => 'label',
                        ),
                        'default_value' => (Tools::getValue('id_field') && Tools::getIsset('update'.$this->table)) ? $object->type: '',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'type'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Label', 'AdminKbBisFieldsController'),
                        'lang' => true,
                        'name' => 'label',
                        'required' => true,
                        'col' => 4,
                        'hint' => $this->module->l('Add the label of the input field', 'AdminKbBisFieldsController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Placeholder', 'AdminKbBisFieldsController'),
                        'lang' => true,
                        'name' => 'placeholder',
                        'col' => 4,
                        'hint' => $this->module->l('Add the placeholder in the input field', 'AdminKbBisFieldsController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Field Name', 'AdminKbBisFieldsController'),
                        'name' => 'field_name',
                        'col' => 4,
                        'required'=> true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->module->l('Options', 'AdminKbBisFieldsController'),
                        'lang' => true,
                        'desc' => $this->module->l('Enter only one option in 1 line', 'AdminKbBisFieldsController')
                        . '</br>' . $this->module->l('Avoid blank lines.', 'AdminKbBisFieldsController') . '<br/>'
                        . $this->module->l('Accepted format example:', 'AdminKbBisFieldsController') . '<br/>'
                        . 'm|Male' . '<br/>'
                        . 'f|Female',
                        'name' => 'value',
                        'col' => 5,
                        'required' => true
                        
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->module->l('Validation', 'AdminKbBisFieldsController'),
                        'name' => 'validation',
                        'options' => array(
                            'query' => $this->getFieldValidation(),
                            'id' => 'id',
                            'name' => 'value'
                        ),
                        'hint' => $this->module->l('Select the type of validation to validate the field', 'AdminKbBisFieldsController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Error Message', 'AdminKbBisFieldsController'),
                        'name' => 'error_msg',
                        'lang' => true,
                        'col' => 4,
                         'hint' => $this->module->l('Display the error message if there is any error in the field', 'AdminKbBisFieldsController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('HTML ID', 'AdminKbBisFieldsController'),
                        'name' => 'html_id',
                        'col' => 4,
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('HTML Class', 'AdminKbBisFieldsController'),
                        'name' => 'html_class',
                        'col' => 4,
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Minimum Length', 'AdminKbBisFieldsController'),
                        'name' => 'min_length',
                        'col' => 3,
                        'hint' => $this->module->l('Enter the minimum character for the field', 'AdminKbBisFieldsController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Maximum Length', 'AdminKbBisFieldsController'),
                        'name' => 'max_length',
                        'desc' => $this->module->l('Maximum text should be 50 characters as to avoid default limit in the order table'),
                        'col' => 3,
                        'hint' => $this->module->l('Enter the maximum character for the field', 'AdminKbBisFieldsController'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->module->l('Required', 'AdminKbBisFieldsController'),
                        'name' => 'required',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'value' => 1
                            ),
                            array(
                                'value' => 0
                            )
                        ),
                        'hint' => $this->module->l('Enable if to make field mandatory', 'AdminKbBisFieldsController'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->module->l('Active', 'AdminKbBisFieldsController'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'value' => 1
                            ),
                            array(
                                'value' => 0
                            )
                        ),
                        'default_value' => 0,
                    ),
                    array(
                            'type' => 'select',
                            'label' => $this->module->l('Shops', 'AdminKbBisFieldsController'),
                            'multiple' => true,
                            'name' => 'stores[]',
                            'hint' => $this->module->l('Allow this Rule for the selected stores.', 'AdminKbBisFieldsController'),
                            'desc' => $this->module->l('1. Hold CTRL to select multiple. 2. If no store is selected then Field will be enable for all stores.', 'AdminKbBisFieldsController'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $stores,
                                'id' => 'id_shop',
                                'name' => 'name',
                            ),
                            'size' => 3
                        ),
                ),
                'submit' => array(
                    'title' => $this->module->l('Save', 'AdminKbBisFieldsController'),
                    'class' => 'btn btn-default pull-right ' .$submit_btn
                ),
            ),
        );
        
        $object = '';
        if ((Tools::getValue('id_field') != '') && Tools::getIsset('update'.$this->table)) {
            $field_value = $this->getEditFieldValues();
        } elseif (Tools::getIsset('add'.$this->table)) {
            $field_value = $this->getAddFieldValues();
        }
        
        return $this->renderGenericForm(
            array(
                'form' => $this->fields_form
            ),
            $field_value,
            Tools::getAdminTokenLite('AdminKbBisFields'),
            $tpl_vars
        );
    }
    /**
     * Function used to assign default values
     * to the fields in Add Custom Field form
     */
    protected function getAddFieldValues()
    {
        $languages = $this->all_languages;
        $field_value = array(
            'type' => '',
            'validation' => '',
            'field_name' => 'field_' . time(),
            'html_id' => 'field_' . time(),
            'html_class' => 'field_' . time(),
            'min_length' => 0,
            'max_length' => 50,
            'required' => 0,
            'active' => 0,
            'stores[]' => '',
        );
        foreach ($languages as $lang) {
            $field_value['label'][$lang['id_lang']] = '';
            $field_value['description'][$lang['id_lang']] = '';
            $field_value['placeholder'][$lang['id_lang']] = '';
            $field_value['value'][$lang['id_lang']] = '';
            $field_value['error_msg'][$lang['id_lang']] = '';
        }
        return $field_value;
    }
    
    /**
     * Function used to display Edit field form
     */
    protected function getEditFieldValues()
    {
        $object = new KbBisCustomFields(Tools::getValue('id_field'));
        $languages = $this->all_languages;
        $stores_edit = array();
        $store_id = explode(",", $object->id_shop);
        if (count($this->ps_shop) == count($store_id)) {
            $stores_edit = array();
        } else {
            foreach ($store_id as $store) {
                $stores_edit[] = $store;
            }
        }
        if (is_object($object) && (count(get_object_vars($object)) > 0)) {
            $field_value = array(
                'type' => $object->type,
                'validation' => $object->validation,
                'field_name' => $object->field_name,
                'html_id' => $object->html_id,
                'html_class' => $object->html_class,
                'min_length' => $object->min_length,
                'max_length' => $object->max_length,
                'required' =>  $object->required,
                'active' =>  $object->active,
                'stores[]' => $stores_edit,
            );
            foreach ($languages as $lang) {
                $field_value['label'][$lang['id_lang']] = isset($object->label[$lang['id_lang']]) ?$object->label[$lang['id_lang']] : '';
                $field_value['description'][$lang['id_lang']] = isset($object->description[$lang['id_lang']]) ?$object->description[$lang['id_lang']] : '';
                $field_value['placeholder'][$lang['id_lang']] = isset($object->placeholder[$lang['id_lang']]) ?$object->placeholder[$lang['id_lang']] : '';
                $field_value['value'][$lang['id_lang']] = isset($object->value[$lang['id_lang']]) ?$object->value[$lang['id_lang']] : '';
                $field_value['error_msg'][$lang['id_lang']] = isset($object->error_msg[$lang['id_lang']]) ?$object->error_msg[$lang['id_lang']] : '';
            }
            $array_option_data = array();
            if (is_array($field_value['value']) && !empty($field_value['value'])) {
                foreach ($field_value['value'] as $key => $value_field) {
                    $option_data = '';
                    $field_option = Tools::jsonDecode($value_field, true);
                    if (!empty($field_option) && is_array($field_option)) {
                        foreach ($field_option as $value) {
                            $option_data .= implode('|', $value) . "\n";
                        }
                    }
                    $array_option_data[$key] = $option_data;
                }
            }
            $field_value['value'] = $array_option_data;
        } else {
            $field_value = array(
                'type' => '',
                'validation' => '',
                'field_name' => 'field_' . time(),
                'html_id' => 'field_' . time(),
                'html_class' => 'field_' . time(),
                'min_length' => '',
                'max_length' => '',
                'active' => 0,
                'stores[]' => $stores_edit,
            );
            foreach ($languages as $lang) {
                $field_value['label'][$lang['id_lang']] = '';
                $field_value['description'][$lang['id_lang']] = '';
                $field_value['placeholder'][$lang['id_lang']] = '';
                $field_value['value'][$lang['id_lang']] = '';
                $field_value['error_msg'][$lang['id_lang']] = '';
            }
        }
        return $field_value;
    }
    /**
     * Function used to add custom section
     */
    public function processAdd()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $addKbField = new KbBisCustomFields();
            $label = array();
            $description = array();
            $placeholder = array();
            $field_value = array();
            $field_error_msg = array();
            $languages = $this->all_languages;
            foreach ($languages as $lang) {
                $label[$lang['id_lang']] = trim(Tools::getValue('label_' . $lang['id_lang']));
                $description[$lang['id_lang']] = trim(Tools::getValue('description_' . $lang['id_lang']));
                $placeholder[$lang['id_lang']] = trim(Tools::getValue('placeholder_' . $lang['id_lang']));
                $field_value[$lang['id_lang']] = trim(Tools::getValue('value_' . $lang['id_lang']));
                $field_error_msg[$lang['id_lang']] = trim(Tools::getValue('error_msg_' . $lang['id_lang']));
            }
            $array_option_data_lang = array();
            $array_default_option_data_lang = array();
            if (is_array($field_value) && !empty($field_value)) {
                foreach ($field_value as $key => $value) {
                    $array_option = explode("\n", $value);
                    foreach ($array_option as $op_key => $option) {
                        $kboption = trim($option);
                        if (!empty($kboption)) {
                            $array_option_data = explode('|', trim($option));
                            $array_option_data_lang[$key][$op_key]['option_value'] = $array_option_data[0];
                            $array_option_data_lang[$key][$op_key]['option_label'] = $array_option_data[1];
                        }
                    }
                }
            }
            if (is_array($array_option_data_lang) && !empty($array_option_data_lang)) {
                foreach ($array_option_data_lang as $key => $options) {
                    $array_option_data_lang[$key] = Tools::jsonEncode($options);
                }
            }
            
            $type = Tools::getValue('type');
            $field_name = Tools::getValue('field_name');
            $validation = Tools::getValue('validation');
            $html_id = trim(Tools::getValue('html_id'));
            $html_class = trim(Tools::getValue('html_class'));
            $min_length = trim(Tools::getValue('min_length'));
            $max_length = trim(Tools::getValue('max_length'));
            $required = Tools::getValue('required');
            $active = Tools::getValue('active');
            $stores = Tools::getValue('stores');
            $store_ids = '';
            if (!empty($stores)) {
                foreach ($stores as $value) {
                    $store_ids = $store_ids . $value . ',';
                }
            } else {
                foreach (Shop::getShops(false) as $shop) {
                    $store_ids = $store_ids . $shop['id_shop'] . ',';
                }
            }
            $store_ids = Tools::substr($store_ids, 0, -1);
            if (($type == 'text') || ($type == 'textarea')) {
                $array_option_data_lang = '';
                $array_default_option_data_lang = '';
                if (($type == 'text') && ($validation == 'isEmail')) {
                    $min_length = '';
                    $max_length = '';
                }
            } elseif ($type == 'select') {
                $placeholder = '';
                $min_length = '';
                $max_length = '';
            } elseif ($type == 'radio') {
                $placeholder = '';
                $min_length = '';
                $max_length = '';
            } elseif ($type == 'checkbox') {
                $placeholder = '';
                $min_length = '';
                $max_length = '';
            }
            $addKbField->field_name = $field_name;
            $addKbField->type = $type;
            $addKbField->label = $label;
            $addKbField->description = $description;
            $addKbField->value = $array_option_data_lang;
            $addKbField->validation = $validation;
            $addKbField->error_msg = $field_error_msg;
            $addKbField->placeholder = $placeholder;
            $addKbField->html_id = $html_id;
            $addKbField->html_class = $html_class;
            $addKbField->position = $this->getNextAvailablePosition();
            $addKbField->max_length = $max_length;
            $addKbField->min_length = $min_length;
            $addKbField->required = $required;
            $addKbField->active = $active;
            $addKbField->id_shop = $store_ids;
            
            if ($addKbField->add()) {
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Field added successfully', 'AdminKbBisFieldsController')
                );
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBisFields', true));
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Something went wrong while adding the Field. Please try again.', 'AdminKbBisFieldsController')
                );
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBisFields', true));
            }
        }
    }
    
    /**
     * Function used to update custom section
     */
    public function processUpdate()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $updateKbField = new KbBisCustomFields(Tools::getValue('id_field'));
            $label = array();
            $description = array();
            $placeholder = array();
            $field_value = array();
            $field_error_msg = array();
            $languages = $this->all_languages;
            foreach ($languages as $lang) {
                $label[$lang['id_lang']] = trim(Tools::getValue('label_' . $lang['id_lang']));
                $description[$lang['id_lang']] = trim(Tools::getValue('description_' . $lang['id_lang']));
                $placeholder[$lang['id_lang']] = trim(Tools::getValue('placeholder_' . $lang['id_lang']));
                $field_value[$lang['id_lang']] = trim(Tools::getValue('value_' . $lang['id_lang']));
                $field_error_msg[$lang['id_lang']] = trim(Tools::getValue('error_msg_' . $lang['id_lang']));
            }
            $array_option_data_lang = array();
            $array_default_option_data_lang = array();
            if (is_array($field_value) && !empty($field_value)) {
                foreach ($field_value as $key => $value) {
                    $array_option = explode("\n", $value);
                    foreach ($array_option as $op_key => $option) {
                         //changes by vishal fo rresolving fatal error while installing the module
                        $kboption = trim($option);
                        if (!empty($kboption)) {
                        //changes end
                            $array_option_data = explode('|', trim($option));
                            $array_option_data_lang[$key][$op_key]['option_value'] = $array_option_data[0];
                            $array_option_data_lang[$key][$op_key]['option_label'] = $array_option_data[1];
                        }
                    }
                }
            }
            if (is_array($array_option_data_lang) && !empty($array_option_data_lang)) {
                foreach ($array_option_data_lang as $key => $options) {
                    $array_option_data_lang[$key] = Tools::jsonEncode($options);
                }
            }
            $type = $updateKbField->type;
            $field_name = Tools::getValue('field_name');
            $validation = Tools::getValue('validation');
            $html_id = trim(Tools::getValue('html_id'));
            $html_class = trim(Tools::getValue('html_class'));
            $min_length = trim(Tools::getValue('min_length'));
            $max_length = trim(Tools::getValue('max_length'));
            $required = Tools::getValue('required');
            $active = Tools::getValue('active');
            $stores = Tools::getValue('stores');
            $store_ids = '';
            if (!empty($stores)) {
                foreach ($stores as $value) {
                    $store_ids = $store_ids . $value . ',';
                }
            } else {
                foreach (Shop::getShops(false) as $shop) {
                    $store_ids = $store_ids . $shop['id_shop'] . ',';
                }
            }
            $store_ids = Tools::substr($store_ids, 0, -1);

            if (($type == 'text') || ($type == 'textarea')) {
                $array_option_data_lang = '';
                $array_default_option_data_lang = '';
                if (($type == 'text') && ($validation == 'isEmail')) {
                    $min_length = '';
                    $max_length = '';
                }
            } elseif ($type == 'select') {
                $placeholder = '';
                $min_length = '';
                $max_length = '';
            } elseif ($type == 'radio') {
                $placeholder = '';
                $min_length = '';
                $max_length = '';
            } elseif ($type == 'checkbox') {
                $placeholder = '';
                $min_length = '';
                $max_length = '';
            }
            $updateKbField->field_name = $field_name;
            $updateKbField->label = $label;
            $updateKbField->description = $description;
            $updateKbField->value = $array_option_data_lang;
            $updateKbField->validation = $validation;
            $updateKbField->error_msg = $field_error_msg;
            $updateKbField->placeholder = $placeholder;
            $updateKbField->html_id = $html_id;
            $updateKbField->html_class = $html_class;
            $updateKbField->max_length = $max_length;
            $updateKbField->min_length = $min_length;
            $updateKbField->required = $required;
            $updateKbField->active = $active;
            $updateKbField->id_shop = $store_ids;
            if ($updateKbField->update()) {
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Field updated successfully', 'AdminKbBisFieldsController')
                );
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBisFields', true));
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Something went wrong while updating the Field. Please try again.', 'AdminKbBisFieldsController')
                );
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBisFields', true));
            }
        }
    }
    /*
     * Function for returning the HTML of Helper Form
     */
    public function renderGenericForm($fields_form, $fields_value, $admin_token, $tpl_vars = array())
    {
        $languages = $this->all_languages;
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }
        $helper = new HelperForm($this);
        $this->setHelperDisplay($helper);
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->show_cancel_button = true;
        $helper->languages = $languages;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->fields_form = array();
        $helper->token = $admin_token;
        $helper->tpl_vars = array_merge(array(
                'fields_value' => $fields_value
            ), $tpl_vars);

        return $helper->generateForm($fields_form);
    }
    public function postProcess()
    {
        if (Tools::isSubmit('action') && Tools::getValue('action') == 'updatePositions') {
            $json = $this->ajaxProcessUpdateRulesPositions();
            if (isset($json['success'])) {
                die(true);
            }
        }
        if (Tools::isSubmit('active'.$this->table)) {
            $id_field = Tools::getValue('id_field');
            $kb_custom = new KbBisCustomFields($id_field);
            if ($kb_custom->active == 1) {
                $kb_custom->active = 0;
            } else {
                $kb_custom->active = 1;
            }
            $kb_custom->update();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBisFieldsController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBisFields', true));
        }
        
//        d(Tools::getAllValues());
        if (Tools::isSubmit('submitBulkenableSelection' . $this->table)) {
            $this->processBulkEnableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBisFieldsController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBisFields', true));
        }
        
        if (Tools::isSubmit('submitBulkdisableSelection' . $this->table)) {
            $this->processBulkDisableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbProductFieldsController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBisFields', true));
        }
        
        parent::postProcess();
    }
    // Function to update Rule positions
    public function ajaxProcessUpdateRulesPositions()
    {
        $response_array = array();
        $id_field = (int)Tools::getValue('id');
        $way = (int) Tools::getValue('way');
        $positions = Tools::getValue('field');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);
            if (isset($pos[2]) && (int) $pos[2] === $id_field) {
                if ($bis_cus_obj = new KbBisCustomFields((int) $pos[2])) {
                    if (isset($position) && $bis_cus_obj->updateRulePosition($way, $position)) {
                        $response_array['success'] = true;
                    } else {
                        $response_array['hasError'] = true;
                        $response_array['errors'] = $this->module->l('Position Could not be updated.', 'AdminKbBisFieldsController');
                    }
                } else {
                    $response_array['hasError'] = true;
                    $response_array['errors'] = $this->module->l('Feilds Could not be loaded.', 'AdminKbBisFieldsController');
                }
            }
        }
        return $response_array;
    }
    
    // Function to find the next available position for a particualr image
    public static function getNextAvailablePosition()
    {
        $sql = 'SELECT MAX(position) as max_pos from ' . _DB_PREFIX_ . 'kb_bis_fields';
        $max_pos = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if ((!isset($max_pos) && $max_pos != 0 && empty($max_pos)) || $max_pos == null) {
            $max_pos = 0;
            return ($max_pos);
        }
        return ($max_pos + 1);
    }
    /**
     * assign default action in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items
     *
     */
    public function initToolbar()
    {
        parent::initToolbar();
    }
    
    /*
     * Function for returning the absolute path of the module directory
     */
    protected function getKbModuleDir()
    {
        return _PS_MODULE_DIR_.$this->kb_module_name.'/';
    }
    
    /*
     * Default function, used here to include JS/CSS files for the module.
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addCSS($this->getKbModuleDir() . 'views/css/admin/kb_admin.css');
        $this->context->controller->addJS($this->getKbModuleDir() . 'views/js/admin/kbcustomfield_admin.js');
        $this->context->controller->addJS($this->getKbModuleDir() . 'views/js/velovalidation.js');
        $this->context->controller->addJS($this->getKbModuleDir() . 'views/js/admin/validation_admin.js');
    }
    /**
     * Function used display toolbar in page header
     */
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['back_url'] = array(
            'href' => 'javascript: window.history.back();',
            'desc' => $this->module->l('Back', 'AdminKbBisFieldsController'),
            'icon' => 'process-icon-back'
        );
        if (!Tools::getValue('id_field') && !Tools::isSubmit('add'.$this->table)) {
            $this->page_header_toolbar_btn['new_template'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->module->l('Add new Field', 'AdminKbBisFieldsController'),
                'icon' => 'process-icon-new'
            );
        }
        
        if (Tools::getValue('id_section') || Tools::isSubmit('id_section')) {
            $this->page_header_toolbar_btn['kb_cancel_action'] = array(
                'href' => self::$currentIndex.'&token='.$this->token,
                'desc' => $this->module->l('Cancel', 'AdminKbBisFieldsController'),
                'icon' => 'process-icon-cancel'
            );
        }
        parent::initPageHeaderToolbar();
    }
    
    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }
    /**
     * Function used to update the bulk action selection
     */
    protected function processBulkStatusSelection($status)
    {
        $boxes = Tools::getValue($this->table.'Box');
        $result = true;
        if (is_array($boxes) && !empty($boxes)) {
            foreach ($boxes as $id) {
                $object = new $this->className((int) $id);
                $object->active = (int) $status;
                $result &= $object->update();
            }
        }
        return $result;
    }
    
    public function psShopList($echo, $tr)
    {
        unset($tr);
        $store_id = explode(",", $echo);
        if (count($this->ps_shop) == count($store_id)) {
            return $this->module->l('All Shops', 'AdminKbBisFieldsController');
        } else {
            $shop_name = '';
            foreach ($store_id as $value) {
                $shop_name = $this->ps_shop[$value] . ' ,';
            }
            $shop_name = Tools::substr($shop_name, 0, -1);
            return $shop_name;
        }
    }
}
