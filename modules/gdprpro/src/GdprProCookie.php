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
 * Class GdprProCookie Holds the gdprpro user configuration
 */
class GdprProCookie
{
    // Hold the class instance.
    private static $instance = null;

    public $performance   = false;
    public $social        = false;
    public $clickedAccept = false;
    public $under16       = false;

    public $content = array();

    protected $context;

    const COOKIE_NAME = 'gdpr_conf';
    const COOKIE_PATH = "/";
    const COOKIE_DAYS = 365;

    private function __construct()
    {
        $cookieName = self::COOKIE_NAME;
        $this->context = Context::getContext();
        $rawCookie = json_decode($this->context->cookie->{$cookieName}, true);
        if ($rawCookie == null) {
            $rawCookie = self::getDefaultModules();
        }
        $this->content = $rawCookie;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function getDefaultModules()
    {
        if (!defined('_PS_ADMIN_DIR_')) {
            $allowAll = Configuration::get(GdprProConfig::ALLOW_ALL_MODULES_BY_DEFAULT);
            $modules = GdprProConfig::getModulesToUnload(true);
            $return = array();
            foreach ($modules as $moduleName => $moduleDetails) {
                if ($allowAll) {
                    $return[$moduleName] = 'true';
                } else {
                    $return[$moduleName] =
                        !($moduleDetails['enabled'] == "1" && $moduleDetails['category'] != 'necessary');
                }
            }
            return $return;
        }

        return array();
    }

    public static function getNecessaryModules()
    {
        $modules = GdprProConfig::getModulesToUnload(true);
        $return = array();
        foreach ($modules as $moduleName => $moduleDetails) {
            if ($moduleDetails['category'] == 'necessary') {
                $return[] = $moduleName;
            }
        }
        return $return;
    }

    public function isCookieEnabled()
    {
    }
}
