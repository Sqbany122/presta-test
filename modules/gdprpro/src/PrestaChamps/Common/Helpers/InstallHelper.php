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

namespace PrestaChamps\Common\Helpers;

/**
 * Class InstallHelper
 */
class InstallHelper
{
    /**
     * Get a translated default value. Use only at install!
     *
     * @param        $basePath
     * @param        $key
     * @param string $language
     *
     * @return string
     * @throws \Exception
     */
    public static function getDefaultValues($basePath, $key, $language = 'en')
    {
        if (file_exists("{$basePath}/install/default-values-{$language}.php")) {
            $translations = require "{$basePath}/install/default-values-{$language}.php";
            return $translations[$key];
        } elseif (file_exists("{$basePath}/install/default-values.php")) {
            $translations = require "{$basePath}/install/default-values.php";
            return $translations[$key];
        } else {
            throw new \Exception("Unable to find module translation");
        }
    }

    /**
     * @param $basePath
     * @param $languages
     *
     * @return mixed
     * @throws \Exception
     */
    public static function multilangModulesToUnload($basePath, $languages)
    {
        $stuff = array();
        foreach ($languages as $id => $isoCode) {
            if (empty($stuff)) {
                $stuff = self::getModulesToUnloadLanguageArray($basePath, $isoCode, $id);
            } else {
                $newStuff = self::getModulesToUnloadLanguageArray($basePath, $isoCode, $id);
                foreach ($stuff as $moduleName => &$moduleSettings) {
                    foreach (array('provider', 'expiry', 'frontend_name', 'description') as $setting) {
                        $moduleSettings[$setting][$id] = $newStuff[$moduleName][$setting][$id];
                    }
                }
            }
        }

        $stuff = array_filter(
            $stuff,
            function ($key) {
                return \Module::isEnabled($key);
            },
            ARRAY_FILTER_USE_KEY
        );
        return $stuff;
    }

    /**
     * @param $basePath
     * @param $isoCode
     * @param $languageId
     *
     * @return mixed
     * @throws \Exception
     */
    protected static function getModulesToUnloadLanguageArray($basePath, $isoCode, $languageId)
    {
        $find = array('/\s{2,}/', '/[\t\n]/', '/\s+/');
        $replace = array(' ', ' ', ' ');

        if (file_exists("{$basePath}/install/modules-to-unload-{$isoCode}.php")) {
            $translation = require "{$basePath}/install/modules-to-unload-{$isoCode}.php";
        } elseif (file_exists("{$basePath}/install/modules-to-unload.php")) {
            $translation = require "{$basePath}/install/modules-to-unload.php";
        } else {
            throw new \Exception("Unable to find module translation");
        }

        foreach ($translation as &$settings) {
            foreach (array('provider', 'expiry', 'frontend_name', 'description') as $key) {
                $settings[$key] = array($languageId => preg_replace($find, $replace, $settings[$key]));
            }
        }

        return $translation;
    }
}
