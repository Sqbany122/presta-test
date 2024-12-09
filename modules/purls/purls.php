<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2018 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class purls extends Module
{
    public function __construct()
    {
        @ini_set("display_errors", 0);
        @error_reporting(0); //E_ALL
        $this->bootstrap = true;
        $this->name = 'purls';
        $this->version = '2.6.3';
        $this->author = 'MyPresta.eu';
        $this->bootstrap = 1;
        $this->mypresta_link = 'https://mypresta.eu/modules/seo/pretty-clean-urls-pro.html';
        $this->tab = 'seo';
        $this->displayName = $this->l('Pretty Clean URLs');
        $this->description = $this->l('This module generates clean and pretty looking urls in your online store. It increases SEO value of the store.');
        parent::__construct();
        $this->checkforupdates();
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 12 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php'))
        {
            @require_once('../modules/' . $this->name . '/key.php');
        }
        else
        {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php'))
            {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            }
            else
            {
                if (@file_exists('modules/' . $this->name . '/key.php'))
                {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1)
        {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        }
        else
        {
            if (defined('_PS_ADMIN_DIR_'))
            {
                if (Tools::isSubmit('submit_settings_updates'))
                {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') == false)
                {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200))
                    {
                        $actual_version = purlsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (purlsUpdate::version($this->version) < purlsUpdate::version(Configuration::get('updatev_' . $this->name)))
                    {
                        $this->warning = $this->l('New version available, check http://MyPresta.eu for more informations');
                    }
                }
                if ($display_msg == 1)
                {
                    if (purlsUpdate::version($this->version) < purlsUpdate::version(purlsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version)))
                    {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    }
                    else
                    {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        if ($part == 1)
        {
            return $exp[1];
        }
        if ($part == 2)
        {
            return $exp[2];
        }
        if ($part == 3)
        {
            return $exp[3];
        }
    }

    public function makeUnique($type, $id_lang, $id_product = false)
    {
        $all_langauges = Language::getLanguages(true);
        if ($type == 'false' or $id_lang == 'false')
        {
            return;
        }

        if ($type == 'categories')
        {
            $collisions = $this->getAllCategoryCollisions($id_lang);
            foreach ($collisions as $collision)
            {
                $categories = $this->getAllCategoryCollisionsRewrite($collision['link_rewrite'], $id_lang);
                $i = 0;
                foreach ($categories as $cat_collision)
                {
                    $category = new Category($cat_collision['id_category'], null, $this->context->shop->id);
                    if ($i != 0)
                    {
                        foreach ($all_langauges as $language)
                        {
                            if ($language['id_lang'] == $id_lang)
                            {
                                $category->link_rewrite[$id_lang] = $collision['link_rewrite'] . '-' . $i;
                            }
                        }
                    }
                    $i++;
                    $category->save();
                }
            }
        }
        if ($type == 'products')
        {
            $collisions = $this->getAllProductCollisions($id_lang, $id_product);
            foreach ($collisions as $collision)
            {
                $products = $this->getAllProductCollisionsRewrite($collision['link_rewrite'], $id_lang);
                $i = 0;
                foreach ($products as $prod_collision)
                {
                    $product = new Product($prod_collision['id_product'], false, null, $this->context->shop->id);
                    if ($i != 0)
                    {
                        foreach ($all_langauges as $language)
                        {
                            if ($language['id_lang'] == $id_lang)
                            {
                                $product->link_rewrite[$id_lang] = $collision['link_rewrite'].'-'.$i;
                            }
                        }
                    }
                    $i++;
                    $product->save();
                }
            }
        }
    }

    public function getContent()
    {
        if (Tools::getValue('AjaxCollisions', 'false') != 'false' && Tools::getValue('id_product', 'false') != 'false' && Tools::getValue('id_lang', 'false') != 'false')
        {
            $this->makeUnique('products', Tools::getValue('id_lang'), Tools::getValue('id_product'));
            die('1');
        }

        if (Tools::getValue('makeUnique','false') != 'false' && Tools::getValue('makeUniqueLanguage','false') != 'false')
        {
            $this->makeUnique(Tools::getValue('makeUnique','false'), Tools::getValue('makeUniqueLanguage','false'));
        }


        $langs = Language::getLanguages();
        $langs = count($langs);
        //echo '<pre>'; print_r($compare_urls); exit;
        if (Tools::getValue('product_rewrite','false') != 'false')
        {
            $this->context->smarty->assign('exact_coll', purls::getAllProductCollisionsRewrite(Tools::getValue('product_rewrite'), Tools::getValue('id_lang', null)));
        }
        
        if (Tools::getValue('category_rewrite','false') != 'false')
        {
            $this->context->smarty->assign('exact_coll_cat', purls::getAllCategoryCollisionsRewrite(Tools::getValue('category_rewrite'), Tools::getValue('id_lang', null)));
        }

        $this->context->smarty->assign(array(
            'langs_active' => (int)$langs,
            'purls' => $this,
            'purls_url' => $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&token='.Tools::getValue('token')
        ));
        $code_collisions = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'purls/views/purls.tpl');


        $output = '';
        $errors = array();
        if (Tools::isSubmit('submitCategoryFeatured'))
        {
            Configuration::updateValue('purls_products', Tools::getValue('purls_products'));
            Configuration::updateValue('purls_categories', Tools::getValue('purls_categories'));
            Configuration::updateValue('purls_manufacturers', Tools::getValue('purls_manufacturers'));
            Configuration::updateValue('purls_suppliers', Tools::getValue('purls_suppliers'));
            Configuration::updateValue('purls_cms', Tools::getValue('purls_cms'));
            if (isset($errors) && count($errors))
            {
                $output .= $this->displayError(implode('<br />', $errors));
            }
            else
            {
                $output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
            }
        }
        return "<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement(\"iframe\");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src=\"javascript:false\",r.title=\"\",r.role=\"presentation\",(r.frameElement||r).style.cssText=\"display: none\",d=document.getElementsByTagName(\"script\"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src='javascript:var d=document.open();d.domain=\"'+n+'\";void(0);',o=s}o.open()._l=function(){var o=this.createElement(\"script\");n&&(this.domain=n),o.id=\"js-iframe-async\",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload=\"document._l();\">'),o.close()}(\"//assets.zendesk.com/embeddable_framework/main.js\",\"prestasupport.zendesk.com\");/*]]>*/</script>" . $output . $this->renderForm().$code_collisions;
    }

    public function getConfigFieldsValues()
    {
        return array(
            'purls_products' => Tools::getValue('purls_products', Configuration::get('purls_products')),
            'purls_categories' => Tools::getValue('purls_categories', Configuration::get('purls_categories')),
            'purls_manufacturers' => Tools::getValue('purls_manufacturers', Configuration::get('purls_manufacturers')),
            'purls_suppliers' => Tools::getValue('purls_suppliers', Configuration::get('purls_suppliers')),
            'purls_cms' => Tools::getValue('purls_cms', Configuration::get('purls_cms')),
        );
    }

    public function renderForm()
    {
        if ($this->psversion() == 5)
        {
            $fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                        'icon' => 'icon-cogs'
                    ),
                    'description' => $this->l('Select from what kind of URLs you want to remove ID'),
                    'input' => array(
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Products'),
                            'name' => 'purls_products',
                            'is_bool' => true,
                            'class' => 't',
                            'desc' => $this->l('Remove ID from product links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Categories'),
                            'name' => 'purls_categories',
                            'is_bool' => true,
                            'class' => 't',
                            'desc' => $this->l('Remove ID from category links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Manufacturers'),
                            'name' => 'purls_manufacturers',
                            'is_bool' => true,
                            'class' => 't',
                            'desc' => $this->l('Remove ID from manufacturer links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Suppliers'),
                            'name' => 'purls_suppliers',
                            'is_bool' => true,
                            'class' => 't',
                            'desc' => $this->l('Remove ID from supplier links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('CMS'),
                            'name' => 'purls_cms',
                            'is_bool' => true,
                            'class' => 't',
                            'desc' => $this->l('Remove ID from CMS links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                    ),
                    'submit' => array('title' => $this->l('Save'),),
                ),
            );
            $helper = new HelperForm();
            $helper->show_toolbar = false;
            $helper->table = $this->table;
            $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
            $helper->default_form_language = $lang->id;
            $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
            $this->fields_form = array();
            $helper->identifier = $this->identifier;
            $helper->submit_action = 'submitCategoryFeatured';
            $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->tpl_vars = array(
                'fields_value' => $this->getConfigFieldsValues(),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            );
            return $helper->generateForm(array($fields_form)) . $this->checkforupdates(0, true);
        }
        elseif ($this->psversion() == 6 || $this->psversion() == 7)
        {
            $fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                        'icon' => 'icon-cogs'
                    ),
                    'description' => $this->l('Select from what kind of URLs you want to remove ID'),
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Products'),
                            'name' => 'purls_products',
                            'is_bool' => true,
                            'desc' => $this->l('Remove ID from product links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Categories'),
                            'name' => 'purls_categories',
                            'is_bool' => true,
                            'desc' => $this->l('Remove ID from category links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Manufacturers'),
                            'name' => 'purls_manufacturers',
                            'is_bool' => true,
                            'desc' => $this->l('Remove ID from manufacturer links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Suppliers'),
                            'name' => 'purls_suppliers',
                            'is_bool' => true,
                            'desc' => $this->l('Remove ID from supplier links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('CMS'),
                            'name' => 'purls_cms',
                            'is_bool' => true,
                            'desc' => $this->l('Remove ID from CMS links'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                    ),
                    'submit' => array('title' => $this->l('Save'),),
                ),
            );
            $helper = new HelperForm();
            $helper->show_toolbar = false;
            $helper->table = $this->table;
            $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
            $helper->default_form_language = $lang->id;
            $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
            $this->fields_form = array();
            $helper->identifier = $this->identifier;
            $helper->submit_action = 'submitCategoryFeatured';
            $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->tpl_vars = array(
                'fields_value' => $this->getConfigFieldsValues(),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            );
            return $helper->generateForm(array($fields_form)) . $this->checkforupdates(0, true);
        }
    }

    public function inconsistency($var)
    {
        return true;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function install()
    {
        return parent:: install();
    }

    public function getAllProductCollisions($id_lang = null, $id_product = false)
    {
        $where_id_product = '';
        if ($id_lang == null)
        {
            $id_lang = $this->context->language->id;
        }
        if ($id_product != false)
        {
            $product = new Product($id_product, false, $id_lang);
            $where_id_product = 'AND pl.`link_rewrite` = "'.(string)$product->link_rewrite.'"';
        }


        return Db::getInstance()->executeS('
		SELECT  pl.`link_rewrite`, pl.`id_product`, pl.`name`, count(pl.`link_rewrite`) as times
		FROM `'._DB_PREFIX_.'product_lang` pl 
        WHERE pl.id_shop = '.$this->context->shop->id.' AND pl.id_lang = '.$id_lang.' '.$where_id_product.' 
		GROUP BY pl.`link_rewrite`
		HAVING COUNT(pl.`link_rewrite`) >= 2');
    }

    public static function getAllProductCollisionsRewrite($rewrite, $id_lang = null)
    {
        if ($id_lang == null)
        {
            $id_lang = Context::getContext()->language->id;
        }
        return Db::getInstance()->executeS('
		SELECT pl.`link_rewrite`, pl.`id_product`, pl.`name`
		FROM `'._DB_PREFIX_.'product_lang` pl 
        WHERE pl.`link_rewrite` ="'.$rewrite.'" AND pl.`id_shop` = '.Context::getContext()->shop->id.' AND pl.`id_lang` = '.$id_lang.'');
    }

    public function getAllCategoryCollisions($id_lang = null)
    {
        if ($id_lang == null)
        {
            $id_lang = $this->context->language->id;
        }
        return Db::getInstance()->executeS('
		SELECT `link_rewrite`, `id_category`, `name`, count(`link_rewrite`) as times
		FROM `'._DB_PREFIX_.'category_lang`  WHERE id_shop = '.$this->context->shop->id.' AND id_lang = '.$id_lang.'
		GROUP BY `link_rewrite`
		HAVING COUNT(`link_rewrite`) >= 2');
    }

    public function getAllCategoryCollisionsRewrite($rewrite, $id_lang = null)
    {
        if ($id_lang == null)
        {
            $id_lang = Context::getContext()->language->id;
        }
        return Db::getInstance()->executeS('
		SELECT `link_rewrite`, `id_category`, `name`
		FROM `'._DB_PREFIX_.'category_lang`  
        WHERE `id_shop` = '.Context::getContext()->shop->id.' AND 
        `id_lang` = '.Context::getContext()->language->id.' AND 
        `link_rewrite` = "'.$rewrite.'"');
    }

}

class purlsUpdate extends purls
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3)
        {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2)
        {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1)
        {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0)
        {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen"))
        {
            if (function_exists("file_get_contents"))
            {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}