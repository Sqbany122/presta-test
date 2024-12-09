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
class Link extends LinkCore
{
    /*
    * module: purls
    * date: 2019-02-21 10:25:06
    * version: 2.6.3
    */
    public function psversion($part = 1)
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
    /*
    * module: purls
    * date: 2019-02-21 10:25:06
    * version: 2.6.3
    */
    public function getCategoryLink($category, $alias = null, $id_lang = null, $selected_filters = null, $id_shop = null, $relative_protocol = false)
    {
        if (Configuration::get('purls_categories') == 1)
        {
            $dispatcher = Dispatcher::getInstance();
            if (!$id_lang)
            {
                $id_lang = Context::getContext()->language->id;
            }
            $url = $this->getBaseLink($id_shop, null, $relative_protocol) . $this->getLangLink($id_lang, null, $id_shop);
            if (!is_object($category))
            {
                $category = new Category($category, $id_lang);
            }
            $params = array();
            $params['id'] = $category->id;
            $params['rewrite'] = (!$alias) ? $category->link_rewrite : $alias;
            if ($this->psversion() == 6 || $this->psversion() == 7)
            {
                $params['meta_keywords'] = Tools::str2url($category->getFieldByLang('meta_keywords'));
                $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));
            }
            else
            {
                $params['meta_keywords'] = Tools::str2url($category->meta_keywords);
                $params['meta_title'] = Tools::str2url($category->meta_title);
            }
            $selected_filters = is_null($selected_filters) ? '' : $selected_filters;
            if (empty($selected_filters))
            {
                $rule = 'category_rule';
            }
            else
            {
                $rule = 'layered_rule';
                $params['selected_filters'] = $selected_filters;
            }
            if ($dispatcher->hasKeyword('category_rule', $id_lang, 'parent_categories'))
            {
                $cats = array();
                foreach ($category->getParentsCategories($id_lang) as $cat)
                {
                    if (!in_array($cat['id_category'], array(
                        1,
                        2,
                        $category->id
                    ))
                    )
                    {
                        $cats[] = $cat['link_rewrite'];
                    }
                }
                $params['parent_categories'] = implode('/', array_reverse($cats));
            }
            return $url . Dispatcher::getInstance()->createUrl($rule, $id_lang, $params, $this->allow);
        }
        else
        {
            if (!$id_lang)
            {
                $id_lang = Context::getContext()->language->id;
            }
            $url = $this->getBaseLink($id_shop, null, $relative_protocol) . $this->getLangLink($id_lang, null, $id_shop);
            if (!is_object($category))
            {
                $category = new Category($category, $id_lang);
            }
            $params = array();
            $params['id'] = $category->id;
            $params['rewrite'] = (!$alias) ? $category->link_rewrite : $alias;
            $params['meta_keywords'] = Tools::str2url($category->getFieldByLang('meta_keywords'));
            $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));
            $selected_filters = is_null($selected_filters) ? '' : $selected_filters;
            if (empty($selected_filters))
            {
                $rule = 'category_rule';
            }
            else
            {
                $rule = 'layered_rule';
                $params['selected_filters'] = $selected_filters;
            }
            return $url . Dispatcher::getInstance()->createUrl($rule, $id_lang, $params, $this->allow, '', $id_shop);
        }
    }
    /*
    * module: purls
    * date: 2019-02-21 10:25:06
    * version: 2.6.3
    */
    public function getPaginationLink($type, $id_object, $nb = false, $sort = false, $pagination = false, $array = false)
    {
        if (!$type && !$id_object)
        {
            $method_name = 'get' . Dispatcher::getInstance()->getController() . 'Link';
            if (method_exists($this, $method_name) && isset($_GET['id_' . Dispatcher::getInstance()->getController()]))
            {
                $type = Dispatcher::getInstance()->getController();
                $id_object = $_GET['id_' . $type];
            }
        }
        if ($type && $id_object)
        {
            $url = $this->{'get' . $type . 'Link'}($id_object, null);
        }
        else
        {
            if (isset(Context::getContext()->controller->php_self))
            {
                $name = Context::getContext()->controller->php_self;
            }
            else
            {
                $name = Dispatcher::getInstance()->getController();
            }
            $url = $this->getPageLink($name);
        }
        $vars = array();
        $vars_nb = array(
            'n',
            'search_query'
        );
        $vars_sort = array(
            'orderby',
            'orderway'
        );
        $vars_pagination = array('p');
        foreach ($_GET as $k => $value)
        {
            if ($k != 'id_' . $type && $k != $type . '_rewrite' && $k != 'controller')
            {
                if (Configuration::get('PS_REWRITING_SETTINGS') && ($k == 'isolang' || $k == 'id_lang'))
                {
                    continue;
                }
                $if_nb = (!$nb || ($nb && !in_array($k, $vars_nb)));
                $if_sort = (!$sort || ($sort && !in_array($k, $vars_sort)));
                $if_pagination = (!$pagination || ($pagination && !in_array($k, $vars_pagination)));
                if ($if_nb && $if_sort && $if_pagination)
                {
                    if (!is_array($value))
                    {
                        $vars[urlencode($k)] = $value;
                    }
                    else
                    {
                        foreach (explode('&', http_build_query(array($k => $value), '', '&')) as $key => $val)
                        {
                            $data = explode('=', $val);
                            $vars[urldecode($data[0])] = $data[1];
                        }
                    }
                }
            }
        }
        if (!$array)
        {
            if (count($vars))
            {
                return $url . (($this->allow == 1 || $url == $this->url) ? '?' : '&') . http_build_query($vars, '', '&');
            }
            else
            {
                return $url;
            }
        }
        $vars['requestUrl'] = $url;
        if ($type && $id_object)
        {
            $vars['id_' . $type] = (is_object($id_object) ? (int)$id_object->id : (int)$id_object);
        }
        if (!$this->allow == 1)
        {
            $vars['controller'] = Dispatcher::getInstance()->getController();
        }
        return $vars;
    }
}