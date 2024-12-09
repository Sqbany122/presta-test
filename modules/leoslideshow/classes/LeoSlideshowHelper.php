<?php
/**
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */

if (!class_exists('LeoSlideshowHelper')) {

    class LeoSlideshowHelper
    {
        
        public static function getInstance()
        {
            static $instance = null;
            if (!$instance) {
                # validate module
                $instance = new LeoSlideshowHelper();
            }

            return $instance;
        }
        
        /**
         * translate for controller in backend
         * MUST set this name "function l()"
         */
        public static function l($string, $specific = false, $name = '')
        {
            static $obj;
            if (!$obj)
            {
                $obj = new LeoSlideshow();
            }
            return $obj->l2($string, $specific, $name);
        }

        /**
         * Copy js, css to theme folder
         */
        public static function copyToTheme()
        {
            include_once(_PS_MODULE_DIR_.'leoslideshow/libs/phpcopy.php');

            
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $theme_dir = _PS_ROOT_DIR_.'/themes/'.Context::getContext()->shop->theme->getName().'/';
            }else{
                $theme_dir = _PS_ROOT_DIR_.'/themes/'.Context::getContext()->shop->getTheme().'/';
            }
        
            $module_dir = _PS_MODULE_DIR_.self::MODULE_NAME.'/';

            $theme_js_dir = $theme_dir.'js/modules/leoslideshow/views/js';
            $theme_css_dir = $theme_dir.'css/modules/leoslideshow/views/css';

            // Create js folder
            mkdir($theme_js_dir, 0755, true);
            PhpCopy::safeCopy($module_dir.'views/js/', $theme_js_dir);

            // Create css folder
            mkdir($theme_css_dir, 0755, true);
            PhpCopy::safeCopy($module_dir.'views/css/', $theme_css_dir);

            $url = 'index.php?controller=adminmodules&configure=leoslideshow&token='.Tools::getAdminTokenLite('AdminModules')
                    .'&tab_module=front_office_features&module_name=leoslideshow';
            Tools::redirectAdmin($url);
        }
    }
}
