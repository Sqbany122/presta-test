<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

/**
 * Class Hook
 */
class Hook extends HookCore
{
    public static function getHookModuleExecList($hook_name = null)
    {
        if (!defined('_PS_ADMIN_DIR_') && Module::isEnabled('gdprpro')) {
            require_once _PS_MODULE_DIR_ . 'gdprpro/src/GdprProCookie.php';
            require_once _PS_MODULE_DIR_ . 'gdprpro/src/GdprProConfig.php';
            $return = parent::getHookModuleExecList($hook_name);
            if (!$return) {
                return $return;
            }
            $gdprCookieContent = GdprProCookie::getInstance()->content;
            $necessaryModules = GdprProCookie::getNecessaryModules();
            $userModules = ($gdprCookieContent) ? $gdprCookieContent : GdprProCookie::getDefaultModules();

            foreach ($return as $key => &$hook) {
                if (isset($hook['module']) &&
                    isset($userModules[$hook['module']]) &&
                    ($userModules[$hook['module']] == 'false' || $userModules[$hook['module']] == false) &&
                    !in_array($hook['module'], $necessaryModules, true)
                ) {
                    unset($return[$key]);
                }
            }
            return $return;
        }
        return parent::getHookModuleExecList($hook_name);
    }
}
