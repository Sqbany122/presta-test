<?php
/**
 * This software is provided "as is" without warranty of any kind.
 *
 * Made by PrestaCraft
 *
 * Visit my website (http://prestacraft.com) for future updates, new articles and other awesome modules.
 *
 * @author     PrestaCraft
 * @copyright  2015-2017 PrestaCraft
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomOrderReference extends Module
{

    public function __construct()
    {
        $this->name = 'customorderreference';
        $this->tab = 'others';
        $this->version = '1.2.0';
        $this->author = 'PrestaCraft';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Custom order reference');
        $this->description = $this->l('Change default order reference generation method.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }


    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'orders MODIFY column reference varchar(26)')) {
            return false;
        }

        if (!parent::install() ||
            !Configuration::updateValue('PC_ORDER_REF', '0') ||
            !Configuration::updateValue('PC_ORDER_REF_SEPARATOR', '-')
            || !$this->registerHook('actionValidateOrder')) {
            return false;
        }

        return true;
    }


    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::updateValue('PC_ORDER_REF', '0')) {
            return false;
        }

        return true;
    }


    public function getContent()
    {
        return $this->postProcess().$this->displayTabs();
    }


    public function postProcess()
    {
        if (Tools::isSubmit('saveSettings')) {
            Configuration::updateValue('PC_ORDER_REF', Tools::getValue('PC_ORDER_REF'));
            return $this->displayConfirmation($this->l('The settings have been updated.'));
        }
    }


    public function displayTabs()
    {
        $head = ' <div role="tabpanel">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-tabs-sticky" role="tablist">
                <li role="presentation" class="active"><a href="#settings" aria-controls="home" role="tab" 
                data-toggle="tab"><i class="icon-cogs"></i>&nbsp;&nbsp;&nbsp;'.$this->l('Settings').'</a></li>
                <li role="presentation"><a href="#about" aria-controls="profile" role="tab" data-toggle="tab">
                <i class="icon-info-circle"></i>&nbsp;&nbsp;&nbsp;'.$this->l('About').'</a></li>
            </ul>

        <!-- Tab panes -->
        <div class="tab-content">
        <div role="tabpanel" class="tab-pane panel active" id="settings">

';

        $footer = '
        '.$this->renderSettings().'

        </div>
        <div role="tabpanel" class="tab-pane panel" id="about">
        <table>
        <tr><td style="text-align:right;font-weight:bold;">'.$this->l('Name').':</td> <td>&nbsp;&nbsp;Custom 
        order reference</td></tr>
        <tr><td style="text-align:right;font-weight:bold;">'.$this->l('Release date').':</td>
        <td>&nbsp;&nbsp;10.08.2016</td></tr>
        <tr><td style="text-align:right;font-weight:bold;">'.$this->l('Module version').':</td> 
        <td>&nbsp;&nbsp;1.1</td></tr>
        <tr><td style="text-align:right;font-weight:bold;">'.$this->l('PrestaShop compatible').':</td>
        <td>&nbsp;&nbsp;1.6</td></tr>
         <tr><td style="text-align:right;font-weight:bold;">'.$this->l('Module website').':</td><td>&nbsp;
        <a href="http://prestacraft.com/free-modules/home/10-custom-order-reference.html" target="_blank">
        '.$this->l('Click').'</a></td></tr>
        </table>
<br /><br />

        '.$this->l('Have a look at my blog with tutorials and modules for PrestaShop').' -
        <a href="http://prestacraft.com" target="_blank">http://prestacraft.com</a>. '.$this->l('Thanks').'.
        <br /><br />'.$this->l('Made with').' <i class="icon-heart"></i> '.$this->l('by').' 
        <a href="http://prestacraft.com" target="_blank">PrestaCraft</a>.
<br /><br /><br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="2NL2KJBLW86SQ">
<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0"
 name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">
</form>
        </div>
        </div>
        </div>
        ';

        return $head.$footer;
    }


    public function renderSettings()
    {
        $fields_form = array(
            'form' => array(

                'submit' => array(
                    'title' => $this->l('Save')
                ),
                'input' => array(
                    array(
                        'label'     => $this->l('Choose Order Reference generation format'),
                        'type'      => 'radio',
                        'name'      => 'PC_ORDER_REF',
                        'required'  => true,
                        'class'     => 't',
                        'is_bool'   => false,
                        'hint'      => $this->l('New choice will not affect existing orders, but only the new ones.'),
                        'values'    => array(
                            array(
                                'value' => 0,
                                'label' => '<strong>'.$this->l('Default PrestaShop').'</strong>
                                <br />'.$this->l('exmple result').': HGAPWFIMJ'
                            ),
                            array(
                                'value' => 5,
                                'label' => '<strong>'.$this->l('Number'). Configuration::get('PC_ORDER_REF_SEPARATOR').
                                    $this->l('Day'). Configuration::get('PC_ORDER_REF_SEPARATOR')  .$this->l('Month').
                                    Configuration::get('PC_ORDER_REF_SEPARATOR').$this->l('Year').' ['.
                                    $this->l('Incrementing number within a day').']</strong> *<br />'.
                                    $this->l('exmple result').': 143'. Configuration::get('PC_ORDER_REF_SEPARATOR') .
                                    '24'. Configuration::get('PC_ORDER_REF_SEPARATOR') .'07'.
                                    Configuration::get('PC_ORDER_REF_SEPARATOR') .'2016'
                            ),
                            array(
                                'value' => 1,
                                'label' => '<strong>'.$this->l('Number'). Configuration::get('PC_ORDER_REF_SEPARATOR').
                                    $this->l('Month').Configuration::get('PC_ORDER_REF_SEPARATOR').$this->l('Year').
                                    ' ['.
                                    $this->l('Incrementing number within a month').']</strong> *<br />'.
                                    $this->l('exmple result').': 143'. Configuration::get('PC_ORDER_REF_SEPARATOR') .
                                    '07'. Configuration::get('PC_ORDER_REF_SEPARATOR') .'2016'
                            ),
                            array(
                                'value' => 2,
                                'label' => '<strong>'.$this->l('Number'). Configuration::get('PC_ORDER_REF_SEPARATOR').
                                    $this->l('Year').' ['.$this->l('Incrementing number within a year').']</strong> *
                                    <br />'.$this->l('exmple result').': 492'.
                                    Configuration::get('PC_ORDER_REF_SEPARATOR') .'2016'
                            ),
                            array(
                                'value' => 3,
                                'label' => '<strong>'.$this->l('Random number').'</strong><br />'.
                                    $this->l('exmple result').': 1422'
                            ),
                            array(
                                'value' => 4,
                                'label' => '<strong>'.$this->l('Incrementing number').'</strong><br />'.
                                    $this->l('exmple result').': 30'
                            ),
                        ),
                    )
                ),
            ),

        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveSettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&pos=2&configure='.
            $this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getSettingsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }


    public function getSettingsValues()
    {
        $fields = array();

        $fields['PC_ORDER_REF'] = Configuration::get('PC_ORDER_REF');
        $fields['PC_ORDER_REF_SEPARATOR'] = Configuration::get('PC_ORDER_REF_SEPARATOR');

        return $fields;
    }

    public function hookActionValidateOrder($params)
    {
        // Init vars
        $day = date('d');
        $month = date('m');
        $year = date('Y');
        $latestNumber = 0;
        $separator = Configuration::get('PC_ORDER_REF_SEPARATOR');
        $type = Configuration::get('PC_ORDER_REF');
        $return = '';

        // Number - Month - Year
        if ($type == 1) {
            $latestRef = Db::getInstance()->getValue('SELECT reference FROM '._DB_PREFIX_.'orders 
                                        WHERE YEAR(date_add)="'.$year.'"
                                        AND MONTH(date_add)="'.$month.'" 
                                        AND reference REGEXP "^[0-9]*'.$separator.'+[0-9]*'.$separator.'+'.$year.'" 
                                        ORDER BY date_add DESC');

            $refNumber = explode($separator, $latestRef, 1);

            if ($latestRef || trim($latestRef) != "") {
                $latestNumber = $refNumber[0];
            }

            $number = $latestNumber+1;

            $return = $number.$separator.$month.$separator.$year;
        }

        // Number - Year
        if ($type == 2) {
            $latestRef = Db::getInstance()->getValue('SELECT reference FROM '._DB_PREFIX_.'orders 
            WHERE YEAR(date_add)="'.$year.'" 
            AND reference REGEXP "^[0-9]*'.$separator.''.$year.'" 
            ORDER BY date_add DESC');

            $refNumber = explode($separator, $latestRef, 1);

            if ($latestRef || trim($latestRef) != "") {
                $latestNumber = $refNumber[0];
            }

            $number = $latestNumber+1;
            $return = $number.$separator.$year;
        }

        // Random number
        if ($type == 3) {
            $rand = rand(100, 99999999);
            $existing = Db::getInstance()->getValue('SELECT reference FROM '._DB_PREFIX_.'orders 
            WHERE reference="'.$rand.'"');

            while (is_int($existing) && $existing > 0) {
                $rand = rand(100, 99999999);
                $existing = Db::getInstance()->getValue('SELECT reference FROM '._DB_PREFIX_.'orders 
                WHERE reference="'.$rand.'"');
            }

            $return = $rand;
        }

        // Incrementing number
        if ($type == 4) {
            $existing = Db::getInstance()->getValue('SELECT max(reference) FROM '._DB_PREFIX_.'orders 
            WHERE reference REGEXP "^[0-9]+"');

            if ($existing && $existing > 0) {
                $return = $existing+1;
            } else {
                $return = 1;
            }
        }

        // Day - Number - Month - Year
        if ($type == 5) {
            $latestRef = Db::getInstance()->getValue('SELECT reference FROM '._DB_PREFIX_.'orders 
                    WHERE YEAR(date_add)="'.$year.'"
                    AND MONTH(date_add)="'.$month.'" 
                    AND DAY(date_add)="'.$day.'" 
                    AND reference REGEXP "^[0-9]*'.$separator.'+[0-9]*'.$separator.'+[0-9]*'.$separator.'+'.$year.'" 
                    ORDER BY date_add DESC');

            $refNumber = explode($separator, $latestRef, 1);
            if ($latestRef || trim($latestRef) != "") {
                $latestNumber = $refNumber[0];
            }

            $number = $latestNumber+1;
            $return = $number.$separator.$day.$separator.$month.$separator.$year;
        }

        if ((int)Configuration::get('PC_ORDER_REF') > 0) {
            $params['order']->reference = $return;
        }
    }
}
