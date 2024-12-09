<?php
/**
* 2012-2015 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Register Account / Newsletter voucher module © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek <info@prestadev.pl>
* @copyright 2012-2015 Patryk Marek @ PrestaDev.pl
* @link      http://prestadev.pl
* @package   PD Register Account / Newsletter voucher module for PrestaShop 1.5.x and 1.6.x
* @version   1.1.0
* @license   Do not edit, modify or copy this file, if you wish to customize it, contact us at info@prestadev.pl.
* @date      01-09-2014
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class PdRegisterVoucher extends Module
{
    private $_html = '';
    private $_postErrors = array();


    public function __construct()
    {
        $this->name = 'pdregistervoucher';
        $this->tab = 'front_office_features';
        $this->version = '1.1.1';
        $this->author = 'PrestaDev.pl';
        $this->module_key = '80681844fffdb7d41de215a1d3cf5c93';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PD Register Account / Newsletter voucher');
        $this->description = $this->l('Registration voucher for new customers who sign up to newsletter / or just sign up');
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('actionCustomerAccountAdd')
            || !Configuration::updateValue('PD_REG_VOUCHER_WORK_MODE', 0)
            || !Configuration::updateValue('PD_REG_VOUCHER_ACTIVE', 1)
            || !Configuration::updateValue('PD_REG_VOUCHER_PRIORITY', 1)
            || !Configuration::updateValue('PD_REG_VOUCHER_HIGHLIGHT', 1)
            || !Configuration::updateValue('PD_REG_VOUCHER_REDUCTION_TYPE', 'amount')
            || !Configuration::updateValue('PD_REG_VOUCHER_FREEDEL', 0)
            || !Configuration::updateValue('PD_REG_VOUCHER_VALID', 7)
            || !Configuration::updateValue('PD_REG_VOUCHER_VALUE', 50)
            || !Configuration::updateValue('PD_REG_VOUCHER_QUANTITY', 1)
            || !Configuration::updateValue('PD_REG_VOUCHER_A_TAX', 1)
            || !Configuration::updateValue('PD_REG_VOUCHER_MIN_AMT', 0)
            || !Configuration::updateValue('PD_REG_VOUCHER_MIN_AMT_TAX', 1)
            || !Configuration::updateValue('PD_REG_VOUCHER_MIN_AMT_CUR', '')
            || !Configuration::updateValue('PD_REG_VOUCHER_MIN_AMT_SHIP', 1)) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!Configuration::deleteByName('PD_REG_VOUCHER_NAME')
            || !Configuration::deleteByName('PD_REG_VOUCHER_WORK_MODE')
            || !Configuration::deleteByName('PD_REG_VOUCHER_DESCRIPTION')
            || !Configuration::deleteByName('PD_REG_VOUCHER_ACTIVE')
            || !Configuration::deleteByName('PD_REG_VOUCHER_PREFIX')
            || !Configuration::deleteByName('PD_REG_VOUCHER_PRIORITY')
            || !Configuration::deleteByName('PD_REG_VOUCHER_HIGHLIGHT')
            || !Configuration::deleteByName('PD_REG_VOUCHER_REDUCTION_TYPE')
            || !Configuration::deleteByName('PD_REG_VOUCHER_FREEDEL')
            || !Configuration::deleteByName('PD_REG_VOUCHER_CURRENCY')
            || !Configuration::deleteByName('PD_REG_VOUCHER_VALID')
            || !Configuration::deleteByName('PD_REG_VOUCHER_VALUE')
            || !Configuration::deleteByName('PD_REG_VOUCHER_QUANTITY')
            || !Configuration::deleteByName('PD_REG_VOUCHER_A_TAX')
            || !Configuration::deleteByName('PD_REG_VOUCHER_MIN_AMT')
            || !Configuration::deleteByName('PD_REG_VOUCHER_MIN_AMT_TAX')
            || !Configuration::deleteByName('PD_REG_VOUCHER_MIN_AMT_CUR')
            || !Configuration::deleteByName('PD_REG_VOUCHER_MIN_AMT_SHIP')
            || !parent::uninstall()) {
            return false;
        }
        return true;
    }

    
    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } else {
            $this->_html .= '<br />';
        }
        
        $this->_html .= $this->renderForm();

        return $this->_html;
    }


    private function _postValidation()
    {
        $this->context->controller->getLanguages();

        if (Tools::isSubmit('btnSubmit')) {
            foreach ($this->context->controller->_languages as $language) {
                if (!Tools::getValue('PD_REG_VOUCHER_NAME_'.$language['id_lang'])) {
                    $this->_postErrors[] = $this->l('Voucher name are required in all languages.');
                }
            }

            if (!Tools::getValue('PD_REG_VOUCHER_VALID')) {
                $this->_postErrors[] = $this->l('Voucher validity days are required.');
            } elseif (!Tools::getValue('PD_REG_VOUCHER_VALUE')) {
                $this->_postErrors[] = $this->l('Voucher value are required.');
            } elseif (!Tools::getValue('PD_REG_VOUCHER_QUANTITY')) {
                $this->_postErrors[] = $this->l('Voucher quantity are required.');
            } elseif (!Tools::getValue('PD_REG_VOUCHER_PRIORITY')) {
                $this->_postErrors[] = $this->l('Voucher / cart rule priority are required.');
            }
        }
    }


    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $name_trads = array();
            foreach ($_POST as $key => $value) {
                if (preg_match('/PD_REG_VOUCHER_NAME_/i', $key)) {
                    $id_lang = preg_split('/PD_REG_VOUCHER_NAME_/i', $key);
                    $name_trads[(int)$id_lang[1]] = $value;
                }
            }

            Configuration::updateValue('PD_REG_VOUCHER_NAME', $name_trads, true);
            Configuration::updateValue('PD_REG_VOUCHER_WORK_MODE', Tools::getValue('PD_REG_VOUCHER_WORK_MODE'));
            Configuration::updateValue('PD_REG_VOUCHER_DESCRIPTION', Tools::getValue('PD_REG_VOUCHER_DESCRIPTION'));
            Configuration::updateValue('PD_REG_VOUCHER_ACTIVE', Tools::getValue('PD_REG_VOUCHER_ACTIVE'));
            Configuration::updateValue('PD_REG_VOUCHER_PREFIX', Tools::getValue('PD_REG_VOUCHER_PREFIX', ''));
            Configuration::updateValue('PD_REG_VOUCHER_PRIORITY', Tools::getValue('PD_REG_VOUCHER_PRIORITY'));
            Configuration::updateValue('PD_REG_VOUCHER_HIGHLIGHT', Tools::getValue('PD_REG_VOUCHER_HIGHLIGHT'));
            Configuration::updateValue('PD_REG_VOUCHER_REDUCTION_TYPE', Tools::getValue('PD_REG_VOUCHER_REDUCTION_TYPE'));
            Configuration::updateValue('PD_REG_VOUCHER_FREEDEL', Tools::getValue('PD_REG_VOUCHER_FREEDEL'));
            Configuration::updateValue('PD_REG_VOUCHER_CURRENCY', Tools::getValue('PD_REG_VOUCHER_CURRENCY'));
            Configuration::updateValue('PD_REG_VOUCHER_VALID', Tools::getValue('PD_REG_VOUCHER_VALID'));

            $reduction_amount = str_replace(',', '.', Tools::getValue('PD_REG_VOUCHER_VALUE'));
            Configuration::updateValue('PD_REG_VOUCHER_VALUE', $reduction_amount);
            Configuration::updateValue('PD_REG_VOUCHER_QUANTITY', Tools::getValue('PD_REG_VOUCHER_QUANTITY'));
            Configuration::updateValue('PD_REG_VOUCHER_A_TAX', Tools::getValue('PD_REG_VOUCHER_A_TAX'));

            $minimum_amount = str_replace(',', '.', Tools::getValue('PD_REG_VOUCHER_MIN_AMT'));
            Configuration::updateValue('PD_REG_VOUCHER_MIN_AMT', $minimum_amount);
            Configuration::updateValue('PD_REG_VOUCHER_MIN_AMT_TAX', Tools::getValue('PD_REG_VOUCHER_MIN_AMT_TAX'));
            Configuration::updateValue('PD_REG_VOUCHER_MIN_AMT_CUR', Tools::getValue('PD_REG_VOUCHER_MIN_AMT_CUR'));
            Configuration::updateValue('PD_REG_VOUCHER_MIN_AMT_SHIP', Tools::getValue('PD_REG_VOUCHER_MIN_AMT_SHIP'));
        }
        // TESTING HERE //
        //$this->testCase();


        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
    }


    public function renderForm()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Module configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Module work mode'),
                        'name' => 'PD_REG_VOUCHER_WORK_MODE',
                        'desc' => $this->l('Please slect module working mode (voucher for registered user or registered user and newsletter subscriber at same time)'),
                        'values' => array(
                            array(
                                'id' => 'register',
                                'value' => 0,
                                'label' => $this->l('Registered user')
                            ),
                            array(
                                'id' => 'newsletter',
                                'value' => 1,
                                'label' => $this->l('Registered user and newsletter subscriber')
                            ),
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Voucher name'),
                        'lang' => true,
                        'name' => 'PD_REG_VOUCHER_NAME',
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Vourcher description'),
                        'name' => 'PD_REG_VOUCHER_DESCRIPTION',
                        'desc' => $this->l('Brief description of voucher.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Voucher code prefix'),
                        'suffix' => $this->l('prefix'),
                        'desc' => $this->l('Add prefix to voucher code ex. PREFIX-ASV13HD.'),
                        'name' => 'PD_REG_VOUCHER_PREFIX',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Voucher active'),
                        'desc' => $this->l('Set voucher active after registration / subscription? If no You will need to accept them manualy.'),
                        'name' => 'PD_REG_VOUCHER_ACTIVE',
                        'is_bool' => true, //retro-compat
                        'values' => array(
                            array(
                                'id' => 'yes',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'no',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show in basket'),
                        'desc' => $this->l('If the voucher is not yet in the cart, it will be displayed in the cart summary.'),
                        'name' => 'PD_REG_VOUCHER_HIGHLIGHT',
                        'is_bool' => true, //retro-compat
                        'values' => array(
                            array(
                                'id' => 'yes',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'no',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Voucher priority'),
                        'desc' => $this->l('Cart rules are applied by priority. A cart rule with a priority of "1" will be processed before a cart rule with a priority of "2".'),
                        'name' => 'PD_REG_VOUCHER_PRIORITY',
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
                    
        $fields_form_2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Voucher actions'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Voucher reduction type'),
                        'name' => 'PD_REG_VOUCHER_REDUCTION_TYPE',
                        'class' => 'fixed-width-md',
                        'desc' => $this->l('Type of reduction percentage or amount, you can always select none and in below option choose to give customer free shipping.'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'percent',
                                    'name' => $this->l('Precentage')
                                ),
                                array(
                                    'id' => 'amount',
                                    'name' => $this->l('Amount')
                                ),
                                array(
                                    'id' => 'off',
                                    'name' => $this->l('None')
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Free shipping'),
                        'is_bool' => true, //retro-compat
                        'name' => 'PD_REG_VOUCHER_FREEDEL',
                        'desc' => $this->l('Add free shipping to customer voucher or just enable free shipping if no reduction type is selected.'),
                        'values' => array(
                            array(
                                'id' => 'yes',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'no',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Discount value'),
                        'name' => 'PD_REG_VOUCHER_VALUE',
                        'desc' => $this->l('Reduction value does not apply to the shipping costs!'),
                        'required' => true
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Discount value currency'),
                        'name' => 'PD_REG_VOUCHER_CURRENCY',
                        'desc' => $this->l('Determines the discount value currency, applicable only if you uses reduction type amount'),
                        'options' => array(
                            'query' => Currency::getCurrencies(false),
                            'id' => 'id_currency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Discount value tax'),
                        'name' => 'PD_REG_VOUCHER_A_TAX',
                        'class' => 't',
                        'desc' => $this->l('Applicable only if you uses reduction type amount.'),
                        'values' => array(
                            array(
                                'id' => 'yes',
                                'value' => 0,
                                'label' => $this->l('Tax excl.')
                            ),
                            array(
                                'id' => 'no',
                                'value' => 1,
                                'label' => $this->l('Tax inc.')
                            ),
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $fields_form_3 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Voucher conditions'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimal order value'),
                        'name' => 'PD_REG_VOUCHER_MIN_AMT',
                        'desc' => $this->l('Reduction value does not apply to the shipping costs!'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Minimal order value currency'),
                        'name' => 'PD_REG_VOUCHER_MIN_AMT_CUR',
                        'desc' => $this->l('Determines currency of minimal amount restriction.'),
                        'options' => array(
                            'query' => Currency::getCurrencies(false),
                            'id' => 'id_currency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Minimal order value tax'),
                        'name' => 'PD_REG_VOUCHER_MIN_AMT_TAX',
                        'desc' => $this->l('Determines if restriction minimal order amount is with tax or without.'),
                        'values' => array(
                            array(
                                'id' => 'yes',
                                'value' => 1,
                                'label' => $this->l('Tax excl.')
                            ),
                            array(
                                'id' => 'no',
                                'value' => 0,
                                'label' => $this->l('Tax inc.')
                            ),
                        )
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Minimal order value shipping'),
                        'name' => 'PD_REG_VOUCHER_MIN_AMT_SHIP',
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Determines if delivery cost should be included in minimal amount restriction.'),
                        'values' => array(
                            array(
                                'id' => 'yes',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'no',
                                'value' => 0,
                                'label' => $this->l('No.')
                            ),
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Voucher quantity'),
                        'name' => 'PD_REG_VOUCHER_QUANTITY',
                        'suffix' => $this->l('pcs.'),
                        'desc' => $this->l('A customer will only be able to use the cart rule "X" time(s) you set.'),
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Voucher valid (days)'),
                        'name' => 'PD_REG_VOUCHER_VALID',
                        'suffix' => $this->l('days'),
                        'desc' => $this->l('A customer will be able to use the voucher X days you set.'),
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form_1, $fields_form_2, $fields_form_3));
    }
    
    

    public function getConfigFieldsValues()
    {
        $return = array();

        $return['PD_REG_VOUCHER_DESCRIPTION'] = Tools::getValue('PD_REG_VOUCHER_DESCRIPTION', Configuration::get('PD_REG_VOUCHER_DESCRIPTION'));
        $return['PD_REG_VOUCHER_PREFIX'] = Tools::getValue('PD_REG_VOUCHER_PREFIX', Configuration::get('PD_REG_VOUCHER_PREFIX'));
        $return['PD_REG_VOUCHER_ACTIVE'] = Tools::getValue('PD_REG_VOUCHER_ACTIVE', Configuration::get('PD_REG_VOUCHER_ACTIVE'));
        $return['PD_REG_VOUCHER_PRIORITY'] = Tools::getValue('PD_REG_VOUCHER_PRIORITY', Configuration::get('PD_REG_VOUCHER_PRIORITY'));
        $return['PD_REG_VOUCHER_HIGHLIGHT'] = Tools::getValue('PD_REG_VOUCHER_HIGHLIGHT', Configuration::get('PD_REG_VOUCHER_HIGHLIGHT'));
        $return['PD_REG_VOUCHER_REDUCTION_TYPE'] = Tools::getValue('PD_REG_VOUCHER_REDUCTION_TYPE', Configuration::get('PD_REG_VOUCHER_REDUCTION_TYPE'));
        $return['PD_REG_VOUCHER_FREEDEL'] = Tools::getValue('PD_REG_VOUCHER_FREEDEL', Configuration::get('PD_REG_VOUCHER_FREEDEL'));
        $return['PD_REG_VOUCHER_CURRENCY'] = Tools::getValue('PD_REG_VOUCHER_CURRENCY', Configuration::get('PD_REG_VOUCHER_CURRENCY'));
        $return['PD_REG_VOUCHER_VALID'] = Tools::getValue('PD_REG_VOUCHER_VALID', Configuration::get('PD_REG_VOUCHER_VALID'));
        $return['PD_REG_VOUCHER_VALUE'] = Tools::getValue('PD_REG_VOUCHER_VALUE', Configuration::get('PD_REG_VOUCHER_VALUE'));
        $return['PD_REG_VOUCHER_QUANTITY'] = Tools::getValue('PD_REG_VOUCHER_QUANTITY', Configuration::get('PD_REG_VOUCHER_QUANTITY'));
        $return['PD_REG_VOUCHER_A_TAX'] = Tools::getValue('PD_REG_VOUCHER_A_TAX', Configuration::get('PD_REG_VOUCHER_A_TAX'));
        $return['PD_REG_VOUCHER_WORK_MODE'] = Tools::getValue('PD_REG_VOUCHER_WORK_MODE', Configuration::get('PD_REG_VOUCHER_WORK_MODE'));
        $return['PD_REG_VOUCHER_MIN_AMT'] = Tools::getValue('PD_REG_VOUCHER_MIN_AMT', Configuration::get('PD_REG_VOUCHER_MIN_AMT'));
        $return['PD_REG_VOUCHER_MIN_AMT_SHIP'] = Tools::getValue('PD_REG_VOUCHER_MIN_AMT_SHIP', Configuration::get('PD_REG_VOUCHER_MIN_AMT_SHIP'));
        $return['PD_REG_VOUCHER_MIN_AMT_CUR'] = Tools::getValue('PD_REG_VOUCHER_MIN_AMT_CUR', Configuration::get('PD_REG_VOUCHER_MIN_AMT_CUR'));
        $return['PD_REG_VOUCHER_MIN_AMT_TAX'] = Tools::getValue('PD_REG_VOUCHER_MIN_AMT_TAX', Configuration::get('PD_REG_VOUCHER_MIN_AMT_TAX'));

        foreach (Language::getLanguages(false) as $lang) {
            $return['PD_REG_VOUCHER_NAME'][(int)$lang['id_lang']] = Tools::getValue('PD_REG_VOUCHER_NAME_'.(int)$lang['id_lang'], Configuration::get('PD_REG_VOUCHER_NAME', (int)$lang['id_lang']));
        }
            

        //d($return);
        return $return;
    }

    public function generateRandomString($length = 6)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, Tools::strlen($characters) - 1)];
        }

        return $randomString;
    }


    public function testCase()
    {
        $languages = Language::getLanguages(false);
        $name = array();

        foreach ($languages as $lang) {
            $name[$lang['id_lang']] = Configuration::get('PD_REG_VOUCHER_NAME', $lang['id_lang']);
        }


        $this->createDiscount(
            7,
            Configuration::get('PD_REG_VOUCHER_VALUE'),
            $name,
            Configuration::get('PD_REG_VOUCHER_DESCRIPTION'),
            Configuration::get('PD_REG_VOUCHER_REDUCTION_TYPE'),
            Configuration::get('PD_REG_VOUCHER_CURRENCY'),
            Configuration::get('PD_REG_VOUCHER_QUANTITY'),
            Configuration::get('PD_REG_VOUCHER_ACTIVE'),
            Configuration::get('PD_REG_VOUCHER_PREFIX'),
            Configuration::get('PD_REG_VOUCHER_FREEDEL'),
            Configuration::get('PD_REG_VOUCHER_HIGHLIGHT'),
            Configuration::get('PD_REG_VOUCHER_PRIORITY'),
            Configuration::get('PD_REG_VOUCHER_A_TAX'),
            Configuration::get('PD_REG_VOUCHER_VALID'),
            Configuration::get('PD_REG_VOUCHER_MIN_AMT'),
            Configuration::get('PD_REG_VOUCHER_MIN_AMT_SHIP'),
            Configuration::get('PD_REG_VOUCHER_MIN_AMT_CUR'),
            Configuration::get('PD_REG_VOUCHER_MIN_AMT_TAX')
        );
    }

    
    public function hookActionCustomerAccountAdd($params)
    {
        //d($params);
        $newsleter_accept = $params['newCustomer']->newsletter;
        $work_mode = Configuration::get('PD_REG_VOUCHER_WORK_MODE');  // newsletter = 1 register = 0
        $languages = Language::getLanguages(false);
        $name = array();

        foreach ($languages as $lang) {
            $name[$lang['id_lang']] = Configuration::get('PD_REG_VOUCHER_NAME', $lang['id_lang']);
        }

        if ($newsleter_accept == 1 && $work_mode == 1) {
            $this->createDiscount(
                $params['newCustomer']->id,
                Configuration::get('PD_REG_VOUCHER_VALUE'),
                $name,
                Configuration::get('PD_REG_VOUCHER_DESCRIPTION'),
                Configuration::get('PD_REG_VOUCHER_REDUCTION_TYPE'),
                Configuration::get('PD_REG_VOUCHER_CURRENCY'),
                Configuration::get('PD_REG_VOUCHER_QUANTITY'),
                Configuration::get('PD_REG_VOUCHER_ACTIVE'),
                Configuration::get('PD_REG_VOUCHER_PREFIX'),
                Configuration::get('PD_REG_VOUCHER_FREEDEL'),
                Configuration::get('PD_REG_VOUCHER_HIGHLIGHT'),
                Configuration::get('PD_REG_VOUCHER_PRIORITY'),
                Configuration::get('PD_REG_VOUCHER_A_TAX'),
                Configuration::get('PD_REG_VOUCHER_VALID'),
                Configuration::get('PD_REG_VOUCHER_MIN_AMT'),
                Configuration::get('PD_REG_VOUCHER_MIN_AMT_SHIP'),
                Configuration::get('PD_REG_VOUCHER_MIN_AMT_CUR'),
                Configuration::get('PD_REG_VOUCHER_MIN_AMT_TAX')
            );
        } elseif ($work_mode == 0) {
            $this->createDiscount(
                $params['newCustomer']->id,
                Configuration::get('PD_REG_VOUCHER_VALUE'),
                $name,
                Configuration::get('PD_REG_VOUCHER_DESCRIPTION'),
                Configuration::get('PD_REG_VOUCHER_REDUCTION_TYPE'),
                Configuration::get('PD_REG_VOUCHER_CURRENCY'),
                Configuration::get('PD_REG_VOUCHER_QUANTITY'),
                Configuration::get('PD_REG_VOUCHER_ACTIVE'),
                Configuration::get('PD_REG_VOUCHER_PREFIX'),
                Configuration::get('PD_REG_VOUCHER_FREEDEL'),
                Configuration::get('PD_REG_VOUCHER_HIGHLIGHT'),
                Configuration::get('PD_REG_VOUCHER_PRIORITY'),
                Configuration::get('PD_REG_VOUCHER_A_TAX'),
                Configuration::get('PD_REG_VOUCHER_VALID'),
                Configuration::get('PD_REG_VOUCHER_MIN_AMT'),
                Configuration::get('PD_REG_VOUCHER_MIN_AMT_SHIP'),
                Configuration::get('PD_REG_VOUCHER_MIN_AMT_CUR'),
                Configuration::get('PD_REG_VOUCHER_MIN_AMT_TAX')
            );
        }
    }



    private function createDiscount(
        $id_customer,
        $amount,
        $name,
        $description,
                                    $reduction_type,
        $currency,
        $quantity,
        $active,
                                    $voucher_prefix,
        $free_delivery,
        $highlight,
                                    $priority = 1,
        $reduction_tax,
        $valid_days,
                                    $minimum_amount,
        $minimum_amount_tax,
                                    $minimum_amount_currency,
        $minimum_amount_shipping
                                    ) {
        $cartRule = new CartRule();

        // Specific to the customer
        $cartRule->id_customer = (int)$id_customer;
        $now = time();
        $cartRule->date_from = date('Y-m-d H:i:s', $now);
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+'.(int)$valid_days.' day'));

        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = (int)$quantity;
        $cartRule->cart_rule_restriction = 1;
        $cartRule->minimum_amount = 0;
        
        // Types of reductions
        if ($reduction_type == 'amount') {
            $cartRule->reduction_amount = $amount;
            $cartRule->reduction_tax = $reduction_tax;
            $cartRule->reduction_currency = $currency;
        } elseif ($reduction_type == 'percent') {
            $cartRule->reduction_percent = (float)$amount;
        } else {
            $cartRule->reduction_percen = 0;
            $cartRule->reduction_amount = 0;
        } //end

        $cartRule->free_shipping = $free_delivery;
        $cartRule->priority = $priority;
        $cartRule->highlight = $highlight;

        $cartRule->name = $name;
        $cartRule->description = $description;

        if ($minimum_amount && $minimum_amount > 0) {
            $cartRule->minimum_amount = $minimum_amount;
            $cartRule->minimum_amount_tax = $minimum_amount_tax;
            $cartRule->minimum_amount_currency = $minimum_amount_currency;
            $cartRule->minimum_amount_shipping = $minimum_amount_shipping;
        }

        $code = '';
        if ($voucher_prefix && $voucher_prefix != '') {
            $code .= $voucher_prefix.'-';
        }

        $code .= (int)($id_customer).'-';
        $code .= $this->generateRandomString(6);

        $cartRule->code = $code;
        $cartRule->active = $active;
        if (!$cartRule->add()) {
            $this->errors[] = Tools::displayError('You cannot generate a voucher.');
        } else {
            $customer = new Customer((int)$id_customer);
            $this->sendVoucher($customer->email, $code, $customer->firstname, $customer->lastname, $minimum_amount, $minimum_amount_currency, $amount, $currency, $quantity);
            return $cartRule;
        }
    }

    /**
    * Send an email containing a voucher code
    *
    * @param $email
    * @param $code
    *
    * @return bool|int
    */
    protected function sendVoucher($email, $code, $firstname, $lastname, $minimum_amount, $minimum_amount_currency, $amount, $currency, $quantity)
    {
        $work_mode = Configuration::get('PD_REG_VOUCHER_WORK_MODE');  // newsletter = 1 register = 0
        $valid_days = Configuration::get('PD_REG_VOUCHER_VALID');
        $free_delivery = Configuration::get('PD_REG_VOUCHER_FREEDEL');
        $email_subject = $this->l('New voucher for your first order in our store');

        $reduction_type = Configuration::get('PD_REG_VOUCHER_REDUCTION_TYPE');

        $amount_wt_sign = '';
        if ($currency && $amount > 0) {
            if ($reduction_type == 'amount') {
                $currency_amount = Currency::getCurrency((int)$currency);
                $amount_wt_sign = $amount.' '.$currency_amount['sign'];
            } elseif ($reduction_type == 'percent') {
                $amount_wt_sign = $amount.'%';
            } else {
                $amount_wt_sign = $amoun;
            }
        }

        $min_amount_wt_sign = '';
        if ($minimum_amount_currency && $minimum_amount > 0) {
            $currency_min_amount = Currency::getCurrency((int)$minimum_amount_currency);
            $min_amount_wt_sign = $minimum_amount.' '.$currency_min_amount['sign'];
        }

        $quantity_wt_text = '';
        if ($quantity > 1) {
            $quantity_wt_text = $quantity.' '.$this->l('times');
        }


        if ($work_mode == 0) {
            $title = $this->l('Customer account registration');
            $title_sub = $this->l('customer account registration');
        } else {
            $title = $this->l('Customer account registration and newsletter subscription');
            $title_sub = $this->l('customer account registration and newsletter subscription');
        }

        $br_1 = '';
        $minimum_amount_text = '';
        if ($minimum_amount && $minimum_amount > 0) {
            $minimum_amount_text = $this->l('Discount voucher can be used on orders above');
            $br_1 = '<br/>';
        }
        
        $br_2 = '';
        $amount_text = '';
        if ($amount && $amount > 0) {
            $amount_text = $this->l('Discount voucher entitles you to a discount of');
            $br_2 = '<br/>';
        }

        $br_3 = '';
        $free_delivery_text = '';
        if ($free_delivery) {
            $free_delivery_text = $this->l('Discount voucher entitles you to free delivery on Your order.');
            $br_3 = '<br/>';
        }

        $br_4 = '';
        $quantity_text = '';
        if ($quantity > 1) {
            $quantity_text = $this->l('Discount voucher can be used only');
            $br_4 = '<br/>';
        }

        // Send email with voucher details
        return @Mail::Send(
            $this->context->language->id,
            'newsletter_voucher',
            Mail::l(
                $email_subject,
                $this->context->language->id
            ),
                array('{discount}' => $code,
                    '{free_delivery}' => $free_delivery_text,
                    '{minimum_amount_text}' => $minimum_amount_text,
                    '{amount_text}' => $amount_text,
                    '{amount_wt_sign}' => $amount_wt_sign,
                    '{min_amount_wt_sign}' => $min_amount_wt_sign,
                    '{quantity_text}' => $quantity_text,
                    '{quantity_wt_text}' => $quantity_wt_text,
                    '{br_1}' => $br_1,
                    '{br_2}' => $br_2,
                    '{br_3}' => $br_3,
                    '{br_4}' => $br_4,
                    '{valid}' => $valid_days,
                    '{title}' => $title,
                    '{title_sub}' => $title_sub,
                    '{firstname}' => $firstname,
                    '{lastname}' => $lastname),
                $email,
                $firstname.' '.$lastname,
                null,
                null,
                null,
                null,
                dirname(__FILE__).'/mails/',
                false,
                $this->context->shop->id
                );
    }
}
