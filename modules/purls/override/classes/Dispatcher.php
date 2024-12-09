<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2017 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class Dispatcher extends DispatcherCore
{

    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
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


    public function __construct()
    {
        $this->original_dispatcher_routes = $this->default_routes;

        $this->use_routes = (bool)Configuration::get('PS_REWRITING_SETTINGS');
        if (defined('_PS_ADMIN_DIR_'))
        {
            $this->front_controller = self::FC_ADMIN;
            $this->controller_not_found = 'adminnotfound';
        }
        elseif (Tools::getValue('fc') == 'module')
        {
            $this->front_controller = self::FC_MODULE;
            $this->controller_not_found = 'pagenotfound';
        }
        else
        {
            $this->front_controller = self::FC_FRONT;
            $this->controller_not_found = 'pagenotfound';
        }
        $this->setRequestUri();
        if (in_array($this->front_controller, array(
            self::FC_FRONT,
            self::FC_MODULE
        )))
        {
            Tools::switchLanguage();
        }
        if (Language::isMultiLanguageActivated())
        {
            $this->multilang_activated = true;
        }
        if (Configuration::get('purls_suppliers') == 1)
        {
            $this->default_routes['supplier_rule'] = array(
                'controller' => 'supplier',
                'rule' => 'supplier/{rewrite}/',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'supplier_rewrite'
                    ),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
            );
        }
        if (Configuration::get('purls_manufacturers') == 1)
        {
            $this->default_routes['manufacturer_rule'] = array(
                'controller' => 'manufacturer',
                'rule' => 'manufacturer/{rewrite}/',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'manufacturer_rewrite'
                    ),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
            );
        }
        if (Configuration::get('purls_cms') == 1)
        {
            $this->default_routes['cms_rule'] = array(
                'controller' => 'cms',
                'rule' => 'info/{rewrite}.html',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'cms_rewrite'
                    ),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
            );
            $this->default_routes['cms_category_rule'] = array(
                'controller' => 'cms',
                'rule' => 'info/{rewrite}',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'cms_category_rewrite'
                    ),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
            );
        }

        // PRODUCTS TURN ON / OFF
        if (Configuration::get('purls_products') == 1)
        {
            if ($this->psversion() == 7)
            {
                $this->default_routes['product_rule'] = array(
                    'controller' => 'product',
                    'rule' => '{category:/}{rewrite}/{id_product_attribute}.html',
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+'),
                        'id_product_attribute' => array(
                            'regexp' => '[0-9]+',
                            'param' => 'id_product_attribute'
                        ),
                        'rewrite' => array(
                            'regexp' => '[_a-zA-Z0-9\pL\pS-]*',
                            'param' => 'product_rewrite'
                        ),
                        'quantity_wanted' => array('regexp' => '[0-9\pL]*'),
                        'ean13' => array('regexp' => '[0-9\pL]*'),
                        'category' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                        'reference' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'manufacturer' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'supplier' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'price' => array('regexp' => '[0-9\.,]*'),
                        'tags' => array('regexp' => '[a-zA-Z0-9-\pL]*'),
                    ),
                );
            }
            else
            {
                $this->default_routes['product_rule'] = array(
                    'controller' => 'product',
                    'rule' => '{category:/}{rewrite}.html',
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+'),
                        'rewrite' => array(
                            'regexp' => '[_a-zA-Z0-9-\pL]*',
                            'param' => 'product_rewrite'
                        ),
                        'ean13' => array('regexp' => '[0-9\pL]*'),
                        'category' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                        'reference' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'manufacturer' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'supplier' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'price' => array('regexp' => '[0-9\.,]*'),
                        'tags' => array('regexp' => '[a-zA-Z0-9-\pL]*'),
                    ),
                );
            }
        }
        // CATEGORIES TURN ON / OFF
        if (Configuration::get('purls_categories') == 1)
        {
            $this->default_routes['category_rule'] = array(
                'controller' => 'category',
                'rule' => '{parent_categories:/}{rewrite}/',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+'),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'category_rewrite'
                    ),
                    'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    'parent_categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                ),
            );
        }
        $this->loadRoutes();
    }


    public static function isProductLink($short_link)
    {
        if (Configuration::get('purls_products') == 1)
        {
            $explode_product_link = explode("/", $short_link);
            $count = count($explode_product_link);
            if ($count > 1){
                if (self::psversion() == 7)
                {
                    $sql = 'SELECT p.`id_product`
        			      FROM `' . _DB_PREFIX_ . 'product` p
                    INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = p.`id_product`)
                    INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (cl.`link_rewrite` = \''. $explode_product_link[$count - 2].'\')
        			WHERE pl.`link_rewrite` = \'' . str_replace('.html', '', $explode_product_link[$count - 1]) . '\'
                      AND pl.`id_lang` = ' . Context::getContext()->language->id .'
                      AND cl.`link_rewrite` = \''. $explode_product_link[$count - 2].'\'
                      AND p.`id_category_default` = cl.`id_category`';
                }
                else
                {
                    $sql = 'SELECT p.`id_product`
        			      FROM `' . _DB_PREFIX_ . 'product` p
                    INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = p.`id_product`)
                    INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (cl.`link_rewrite` = \''. $explode_product_link[$count - 2].'\')
        			WHERE pl.`link_rewrite` = \'' . str_replace('.html', '', $explode_product_link[$count - 1]) . '\'
                      AND pl.`id_lang` = ' . Context::getContext()->language->id .'
                      AND cl.`link_rewrite` = \''. $explode_product_link[$count - 2].'\'
                      AND p.`id_category_default` = cl.`id_category`';
                }
            } else {
                if (self::psversion() == 7)
                {
                    $sql = 'SELECT `id_product`
        			FROM `' . _DB_PREFIX_ . 'product_lang`
        			WHERE `link_rewrite` = \'' . str_replace('.html', '', $explode_product_link[$count - 1]) . '\' AND `id_lang` = ' . Context::getContext()->language->id;
                }
                else
                {
                    $sql = 'SELECT `id_product`
        			FROM `' . _DB_PREFIX_ . 'product_lang` pl
        			WHERE pl.`link_rewrite` = \'' . str_replace('.html', '', $explode_product_link[$count - 1]) . '\' AND pl.`id_lang` = ' . Context::getContext()->language->id;
                }
            }
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
            {
                $sql .= ' AND pl.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            if ($explode_product_link[$count - 1] == "")
            {
                return false;
            }


            $id_product = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if ($id_product > 0)
            {
                $_GET['id_product'] = $id_product;
                //$_POST['id_product'] = $id_product;
            }
            return ($id_product > 0) ? true : false;
        }
        else
        {
            if (Tools::getValue('id_product', 'false') != 'false' && Tools::getValue('controller') == "product")
            {
                return Tools::getValue('id_product');
            }
        }
    }


    public static function isCategoryLink($short_link)
    {
        if (Configuration::get('purls_categories') == 1)
        {
            $short_link = rtrim($short_link, "/");
            $categories = explode("/", $short_link);
            $categories_count = count($categories);
            $where_clause = '';
            $shop_association = 'AND cs.id_shop=' . Context::getContext()->shop->id;
            if ($categories_count > 2)
            {
                $where_clause = ' AND c.`id_parent` IN
                        (SELECT cll.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cll
                        INNER JOIN `' . _DB_PREFIX_ . 'category` cc ON (cc.id_category = cll.id_category)
                        INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cc.id_category = cs.id_category)
                        WHERE 1 ' . $shop_association . ' AND cll.`link_rewrite` = \'' . str_replace('.html', '', $categories[$categories_count - 2]) . '\' AND cll.`id_lang` = ' . Context::getContext()->language->id . ' AND cc.`id_parent` IN
                            (SELECT clll.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` clll
                            INNER JOIN `' . _DB_PREFIX_ . 'category` ccc ON (ccc.id_category = clll.id_category)
                            INNER JOIN `' . _DB_PREFIX_ . 'category_shop` css ON (ccc.id_category = css.id_category)
                            WHERE 1 ' . $shop_association . ' AND clll.`link_rewrite` = \'' . str_replace('.html', '', $categories[$categories_count - 3]) . '\' AND clll.`id_lang` = ' . Context::getContext()->language->id . ')
                        )';
            }
            else
            {
                if ($categories_count > 1)
                {
                    $where_clause = ' AND c.`id_parent` IN
                        (SELECT cll.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cll
                        INNER JOIN `' . _DB_PREFIX_ . 'category` cc ON (cc.id_category = cll.id_category)
                        INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cc.id_category = cs.id_category)
                        WHERE 1 ' . $shop_association . ' AND cll.`link_rewrite` = \'' . str_replace('.html', '', $categories[$categories_count - 2]) . '\' AND cll.`id_lang` = ' . Context::getContext()->language->id . ')';
                }
            }
            $sql = 'SELECT c.`id_category` FROM `' . _DB_PREFIX_ . 'category_lang` cl
                    INNER JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category = cl.id_category)
    				WHERE cl.`link_rewrite` = \'' . str_replace('.html', '', $categories[$categories_count - 1]) . '\' AND cl.`id_lang` = ' . Context::getContext()->language->id . ' ' . $where_clause;
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
            {
                $sql .= ' AND cl.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            $id_category = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if ($id_category > 0)
            {
                //$_POST['id_category'] = $id_category;
                $_GET['id_category'] = $id_category;
            }
            return ($id_category > 0) ? true : false;
        }
        else
        {
            if (Tools::getValue('id_cat', 'false') != 'false' && Tools::getValue('controller') == "category")
            {
                return Tools::getValue('id_cat');
            }
        }
    }


    public static function isCmsCategoryLink($short_link)
    {
        $explode_cms_link = explode("/", $short_link);
        $count = count($explode_cms_link);
        $sql = 'SELECT l.`id_cms_category`
			FROM `' . _DB_PREFIX_ . 'cms_category_lang` l
			WHERE l.`link_rewrite` = \'' . $explode_cms_link[$count - 1] . '\'';
        $id_cms_category = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        if ($id_cms_category > 0)
        {
            //$_POST['id_cms_category'] = $id_cms_category;
            $_GET['id_cms_category'] = $id_cms_category;
        }

        return ($id_cms_category > 0) ? true : false;
    }


    public static function isCmsLink($short_link)
    {
        if (Configuration::get('purls_cms') == 1)
        {
            $explode_cms_link = explode("/", $short_link);
            $count = count($explode_cms_link);
            $sql = 'SELECT l.`id_cms`
    			FROM `' . _DB_PREFIX_ . 'cms_lang` l
    			LEFT JOIN `' . _DB_PREFIX_ . 'cms_shop` s ON (l.`id_cms` = s.`id_cms`)
    			WHERE l.`link_rewrite` = \'' . str_replace(".html", "", $explode_cms_link[$count - 1]) . '\'';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
            {
                $sql .= ' AND s.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            $id_cms = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

            if ($id_cms > 0)
            {
                //$_POST['id_cms'] = $id_cms;
                $_GET['id_cms'] = $id_cms;
            }

            return ($id_cms > 0) ? true : false;
        }
        else
        {
            if (Tools::getValue('id_cms', 'false') != 'false' && Tools::getValue('controller') == "cms")
            {
                return Tools::getValue('id_cms');
            }
        }
    }


    public static function isManufacturerLink($short_link)
    {
        $id_manufacturer = 0;
        if (Configuration::get('purls_manufacturers') == 1)
        {
            $explode_manufacturer_link = explode("/", $short_link);
            if ($explode_manufacturer_link[0] == "supplier")
            {
                return false;
            }
            $count = count($explode_manufacturer_link);
            if ($explode_manufacturer_link[1])
            {
                $name_manufacturer = $explode_manufacturer_link[1];
            }
            $sqlall = 'SELECT * FROM `' . _DB_PREFIX_ . 'manufacturer` m
   			LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` s ON (m.`id_manufacturer` = s.`id_manufacturer`) WHERE 1=1 ';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
            {
                $sqlall .= ' AND s.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            $allmanufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sqlall);
            if ($explode_manufacturer_link[1])
            {
                foreach ($allmanufacturers as $key => $manufacturer)
                {
                    if ($name_manufacturer == Tools::str2url($manufacturer['name']))
                    {
                        $id_manufacturer = $manufacturer['id_manufacturer'];
                    }
                }
            }
            if (isset($id_manufacturer))
            {
                return ($id_manufacturer > 0) ? true : false;
            }
            else
            {
                return false;
            }
        }
        else
        {
            if (Tools::getValue('id_manufacturer', 'false') != 'false' && Tools::getValue('controller') == "manufacturer")
            {
                return Tools::getValue('id_manufacturer');
            }
        }
    }


    public static function isSupplierLink($short_link)
    {
        $id_supplier = 0;
        if (Configuration::get('purls_suppliers') == 1)
        {
            $explode_supplier_link = explode("/", $short_link);
            $count = count($explode_supplier_link);
            if (isset($explode_supplier_link[1]))
            {
                $name_supplier = $explode_supplier_link[1];
            }
            $sql = 'SELECT *
    		FROM `' . _DB_PREFIX_ . 'supplier` sp
    		LEFT JOIN `' . _DB_PREFIX_ . 'supplier_shop` s ON (sp.`id_supplier` = s.`id_supplier`) WHERE 1=1';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
            {
                $sql .= ' AND s.`id_shop` = ' . (int)Shop::getContextShopID();
            }
            $allsuppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
            if (isset($explode_supplier_link[1]))
            {
                foreach ($allsuppliers as $key => $supplier)
                {
                    if ($name_supplier == Tools::str2url($supplier['name']))
                    {
                        $id_supplier = $supplier['id_supplier'];
                    }
                }
            }
            if (isset($id_supplier))
            {
                return ($id_supplier > 0) ? true : false;
            }
            else
            {
                return false;
            }
        }
        else
        {
            if (Tools::getValue('id_supplier', 'false') != 'false' && Tools::getValue('controller') == "supplier")
            {
                return Tools::getValue('id_supplier');
            }
        }
    }

    public static function getIDs($active = true, $id_shop = false)
	{
		return Language::getLanguages($active, $id_shop, true);
	}

    public function getController($id_shop = null)
    {
        if (defined('_PS_ADMIN_DIR_'))
        {
            $_GET['controllerUri'] = Tools::getvalue('controller');
        }

        if ($this->controller)
        {
            $_GET['controller'] = $this->controller;

            return $this->controller;
        }

        if (null === $id_shop)
        {
            $id_shop = (int)Context::getContext()->shop->id;
        }

        $controller = Tools::getValue('controller');

        $curr_lang_id = Context::getContext()->language->id;

        if (isset($controller) && is_string($controller) && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $m))
        {
            $controller = $m[1];
            if (isset($_GET['controller']))
            {
                $_GET[$m[2]] = $m[3];
            }
            elseif (isset($_POST['controller']))
            {
                $_POST[$m[2]] = $m[3];
            }
        }

        if (!Validate::isControllerName($controller))
        {
            $controller = false;
        }

        // Use routes ? (for url rewriting)
        if ($this->use_routes && !$controller && !defined('_PS_ADMIN_DIR_'))
        {
            if (!$this->request_uri)
            {
                return strtolower($this->controller_not_found);
            }
            $controller = $this->controller_not_found;

            // If the request_uri matches a static file, then there is no need to check the routes, we keep "controller_not_found" (a static file should not go through the dispatcher)
            if (!preg_match('/\.(gif|jpe?g|png|css|js|ico)$/i', $this->request_uri))
            {
                // Add empty route as last route to prevent this greedy regexp to match request uri before right time
                if ($this->empty_route)
                {
                    $this->addRoute($this->empty_route['routeID'], $this->empty_route['rule'], $this->empty_route['controller'], $curr_lang_id, array(), array(), $id_shop);
                }

                $context = Context::getContext();
                $language_ids = self::getIDs();
                if (isset($context->language) && !in_array($context->language->id, $language_ids)) {
                    $language_ids[] = (int)$context->language->id;
                }

                foreach ($language_ids as $id_lang) {
                    foreach ($this->original_dispatcher_routes as $id => $route) {
                        $this->addRoute(
                            $id."_original",
                            $route['rule'],
                            $route['controller'],
                            $id_lang,
                            $route['keywords'],
                            isset($route['params']) ? $route['params'] : array(),
                            $id_shop
                        );
                    }
                }

                list($uri) = explode('?', $this->request_uri);

                if (isset($this->routes[$id_shop][$curr_lang_id]))
                {
                    $route = array();

                    // check, whether request_uri is template or not
                    foreach ($this->routes[$id_shop][$curr_lang_id] as $k => $r)
                    {
                        if (preg_match($r['regexp'], $uri, $m) && !isset($isProductLink))
                        {
                            $isTemplate = false;
                            $module = isset($r['params']['module']) ? $r['params']['module'] : '';
                            switch ($r['controller'] . $module)
                            {
                                case 'supplier':
                                    if (Configuration::get('purls_suppliers') == 1){
                                        if (isset($m['id_supplier'])){
                                            Tools::redirect(Context::getContext()->link->getSupplierLink($m['id_supplier']), null, null, 'HTTP/1.1 301 Moved Permanently');
                                            $isTemplate = false;
                                        } else {
                                            $isTemplate = false;
                                        }
                                    } else {
                                        $isTemplate = false;
                                    }
                                    break;
                                case 'manufacturer':
                                    if (Configuration::get('purls_manufacturers') == 1){
                                        if (isset($m['id_manufacturer'])){
                                            Tools::redirect(Context::getContext()->link->getManufacturerLink($m['id_manufacturer']), null, null, 'HTTP/1.1 301 Moved Permanently');
                                            $isTemplate = false;
                                        } else {
                                            $isTemplate = false;
                                        }
                                    } else {
                                        $isTemplate = false;
                                    }
                                    break;
                                case 'cms':
                                    if (Configuration::get('purls_cms') == 1){
                                        if (isset($m['id_cms'])){
                                            Tools::redirect(Context::getContext()->link->getCmsLink($m['id_cms']), null, null, 'HTTP/1.1 301 Moved Permanently');
                                            $isTemplate = false;
                                        } elseif (isset($m['id_cms_category'])) {
                                            Tools::redirect(Context::getContext()->link->getCmsCategoryLink($m['id_cms_category']), null, null, 'HTTP/1.1 301 Moved Permanently');
                                            $isTemplate = false;
                                        } else {
                                            $isTemplate = true;
                                        }
                                    } else {
                                        $isTemplate = false;
                                    }
                                    break;
                                case 'product':
                                    if (Configuration::get('purls_products') == 1)
                                    {
                                        if (self::isProductLink(ltrim(parse_url($uri, PHP_URL_PATH), '/'), $this->routes[$id_shop][$curr_lang_id]['product_rule']) == true)
                                        {
                                            $isProductLink = true;
                                        }
                                        
                                        if (isset($m['id_product'])){
                                            if (self::isProductLink(ltrim(parse_url($uri, PHP_URL_PATH), '/'), $this->routes[$id_shop][$curr_lang_id]['product_rule']) == true)
                                            {

                                                $isTemplate = true;
                                            }
                                            else
                                            {
                                                Tools::redirect(Context::getContext()->link->getProductLink($m['id_product']), null, null, 'HTTP/1.1 301 Moved Permanently');
                                            }
                                        } else {
                                            $isTemplate = true;
                                        }
                                    } else {
                                        $isTemplate = false;
                                    }
                                    break;
                                case 'category':
                                    if (Configuration::get('purls_categories') == 1)
                                    {
                                        if (isset($m['id_category'])){
                                            if (self::isCategoryLink(ltrim(parse_url($uri, PHP_URL_PATH), '/'), $this->routes[$id_shop][$curr_lang_id]['category_rule']) == true)
                                            {
                                                $isTemplate = true;
                                            }
                                            else
                                            {
                                                Tools::redirect(Context::getContext()->link->getCategoryLink($m['id_category']), null, null, 'HTTP/1.1 301 Moved Permanently');
                                            }
                                        } else {
                                            $isTemplate = true;
                                        }
                                    } else {
                                        $isTemplate = false;
                                    }
                                    break;
                            }
                            if (!$isTemplate)
                            {
                                $route = $r;
                                break;
                            }
                        }
                    }

                    // if route is not found, we have to find rewrite link in database
                    if (empty($route))
                    {
                        // get the path from requested URI, and remove "/" at the beginning
                        $short_link = ltrim(parse_url($uri, PHP_URL_PATH), '/');

                        $route = $this->routes[$id_shop][$curr_lang_id]['product_rule'];
                        if (!self::isProductLink($short_link, $route))
                        {
                            $route = $this->routes[$id_shop][$curr_lang_id]['category_rule'];
                            if (!self::isCategoryLink($short_link, $route))
                            {
                                $route = $this->routes[$id_shop][$curr_lang_id]['cms_rule'];
                                if (!self::isCmsLink($short_link, $route))
                                {
                                    $route = $this->routes[$id_shop][$curr_lang_id]['cms_category_rule'];
                                    if (!self::isCmsCategoryLink($short_link, $route))
                                    {
                                        $route = $this->routes[$id_shop][$curr_lang_id]['manufacturer_rule'];
                                        if (!self::isManufacturerLink($short_link, $route))
                                        {
                                            $route = $this->routes[$id_shop][$curr_lang_id]['supplier_rule'];
                                            if (!self::isSupplierLink($short_link, $route))
                                            {
                                                // no route found
                                                $route = array();
                                                $controller = $this->controller_not_found;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (!empty($route['controller']))
                        {
                            $controller = $route['controller'];
                        }
                    }

                    if (!empty($route))
                    {
                        if (preg_match($route['regexp'], $uri, $m))
                        {
                            // Route found! Now fill $_GET with parameters of uri
                            foreach ($m as $k => $v)
                            {
                                if (!is_numeric($k))
                                {
                                    $_GET[$k] = $v;
                                }
                            }

                            $controller = $route['controller'] ? $route['controller'] : $_GET['controller'];
                            if (!empty($route['params']))
                            {
                                foreach ($route['params'] as $k => $v)
                                {
                                    $_GET[$k] = $v;
                                }
                            }

                            // A patch for module friendly urls
                            if (preg_match('#module-([a-z0-9_-]+)-([a-z0-9]+)$#i', $controller, $m))
                            {
                                $_GET['module'] = $m[1];
                                $_GET['fc'] = 'module';
                                $controller = $m[2];
                            }

                            if (isset($_GET['fc']) && $_GET['fc'] == 'module')
                            {
                                $this->front_controller = self::FC_MODULE;
                            }
                        }
                    }
                }
            }

            if ($controller == 'index' || $this->request_uri == '/index.php')
            {
                $controller = $this->default_controller;
            }
            $this->controller = $controller;
        }
        else
        { // Default mode, take controller from url
            $this->controller = $controller;
        }

        $this->controller = str_replace('-', '', $this->controller);
        $_GET['controller'] = $this->controller;

        return $this->controller;
    }
}