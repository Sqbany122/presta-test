<?php
/**
 * install-config_class.php file defines InstallConfig class to install / unistall module configuration
 */


class BT_InstallConfig implements BT_IInstall
{
    /**
     * install of module
     *
     * @param mixed $mParam
     * @return bool $bReturn : true => validate install, false => invalidate install
     */
    public static function install($mParam = null)
    {
        // declare return
        $bReturn = true;

        // log jam to debug appli
        if (defined('_GACT_LOG_JAM_CONFIG') && _GACT_LOG_JAM_CONFIG) {
            $bReturn = _GACT_LOG_JAM_CONFIG;
        } else {
            if (empty($mParam['bHookOnly'])) {
                // update each constant used in module admin & display
                foreach ($GLOBALS['GACT_CONFIGURATION'] as $sKeyName => $mVal) {
                    if (!Configuration::updateValue($sKeyName, $mVal)) {
                        $bReturn = false;
                    }
                }
            }
            if (empty($mParam['bConfigOnly'])) {
                // register each hooks
                foreach ($GLOBALS['GACT_HOOKS'] as $aHook) {
                    if (!self::isHookInstalled($aHook['name'], GAdwordsTracking::$oModule->id)) {
                        if (!GAdwordsTracking::$oModule->registerHook($aHook['name'])) {
                            $bReturn = false;
                        }
                    }
                }
            }
        }

        return $bReturn;
    }

    /**
     * uninstall of module
     *
     * @param mixed $mParam
     * @return bool $bReturn : true => validate uninstall, false => invalidate uninstall / uninstall admin tab
     */
    public static function uninstall($mParam = null)
    {
        // set return execution
        $bReturn = true;

        // log jam to debug appli
        if (defined('_GACT_LOG_JAM_CONFIG') && _GACT_LOG_JAM_CONFIG) {
            $bReturn = _GACT_LOG_JAM_CONFIG;
        } else {
            // delete global config
            foreach ($GLOBALS['GACT_CONFIGURATION'] as $sKeyName => $mVal) {
                if (!Configuration::deleteByName($sKeyName)) {
                    $bReturn = false;
                }
            }
        }

        return $bReturn;
    }

    /**
     * check if specific module is hooked to a specific hook
     *
     * @param string $sHookName
     * @param int $iModuleId
     * @return int
     */
    public static function isHookInstalled($sHookName, $iModuleId)
    {
        $bReturn = false;

        if (version_compare(_PS_VERSION_, '1.3.6', '<')) {
            $sQuery = 'SELECT COUNT(*)
				FROM `' . _DB_PREFIX_ . 'hook_module` hm
				LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON (h.`id_hook` = hm.`id_hook`)
				WHERE h.`name` = \'' . pSQL($sHookName) . '\' AND hm.`id_module` = ' . (int)$iModuleId;

            $bReturn = Db::getInstance()->getValue($sQuery);
        } else {
            $bReturn = GAdwordsTracking::$oModule->isRegisteredInHook($sHookName);
        }

        return $bReturn;
    }
}
