<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class x13ExcludeDiscounts extends Module
{
    public function __construct()
    {
        $this->name = 'x13excludediscounts';
        $this->tab = 'front_office_features';
        $this->version = '1.2.1';
        $this->author = 'X13.pl';
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->is_configurable = 1;

        parent::__construct();
        
        $this->displayName = $this->l('Exclude Discounts for Cart Rules');
        $this->description = $this->l('Allows you to exlude cart rule from discounted products while creating new voucher.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
    }

    public function install()
    {
        Configuration::updateGlobalValue('X13EXLUDEDDISCOUNTS_OVERRIDES_REINSTALLED', 1);

        return parent::install() && $this->registerHook('displayBackOfficeHeader') && $this->installDb() && $this->installBOOverrides();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDb() && $this->deleteBOOverrides();
    }

    public function getBOOverridesPaths()
    {
        return array(
            'from' => _PS_MODULE_DIR_.'x13excludediscounts/override/controllers/admin/templates/cart_rules/',
            'to' => _PS_OVERRIDE_DIR_.'controllers/admin/templates/cart_rules/',
        );
    }

    public function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);

        return true;
    }

    public function installBOOverrides()
    {
        $paths = $this->getBOOverridesPaths();
        return $this->recurseCopy($paths['from'], $paths['to']);
    }

    public function deleteBOOverrides()
    {
        $dir = $this->getBOOverridesPaths()['to'];
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        return rmdir($dir);
    }

    /**
     * Uninstall overrides files for the module
     *
     * @return bool
     */
    public function uninstallOverrides()
    {
        if (Configuration::get('X13EXLUDEDDISCOUNTS_OVERRIDES_REINSTALLED')) {
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                return true;
            }

            return parent::uninstallOverrides();
        }

        if (!is_dir($this->getLocalPath().'override')) {
            return true;
        }

        foreach (Tools::scandir($this->getLocalPath().'override', 'php', '', true) as $file) {
            $class = basename($file, '.php');
            if (PrestaShopAutoload::getInstance()->getClassPath($class.'Core') || Module::getModuleIdByName($class)) {
                $orig_path = PrestaShopAutoload::getInstance()->getClassPath($class.'Core');
                $override_path = _PS_OVERRIDE_DIR_.$orig_path;
                if (file_exists($override_path)) {
                    @copy($override_path, $override_path.'.backup_' . date('d_M_Y'));
                    $backupContent = '<?php // empty and backed up';
                    file_put_contents($override_path, $backupContent);
                }
            }
        }

        Tools::generateIndex();
        return true;
    }

    private function installDb()
    {
        return Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'cart_rule` ADD `exclude_discounts` tinyint(1) NOT NULL DEFAULT 0 AFTER highlight');
    }

    private function uninstallDb()
    {
        return Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'cart_rule` DROP COLUMN `exclude_discounts`');
    }

    public function renderErrorMessage()
    {
        return $this->l('You can not use this voucher on cart with discounted products');
    }

    /**
     * Check for module update
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (!Module::isEnabled($this->name)) {
            return;
        }

        if (defined('_PS_ADMIN_DIR_') && !Tools::getValue('ajax', 0)) {
            if ($this->checkForNewVersion()) {
                $this->context->controller->warnings[] = $this->renderAdminMessage(
                    sprintf($this->l('Nowa wersja modułu %s jest już dostępna! - pobierz ją z x13.pl'), $this->displayName)
                );
            }
        }
    }

    public function checkForNewVersion()
    {
        $upgradeFile = _PS_MODULE_DIR_.$this->name.'/'.$this->name.'.xml';

        $externalUpgradeFile = 'http://dev.x13.pl/update/'.$this->name.'.xml';
        if (!$this->x13isFresh($upgradeFile)) {
            $this->x13refresh($upgradeFile, $externalUpgradeFile);
        }

        $xmlContent = @simplexml_load_string(file_get_contents($upgradeFile));

        if (!$xmlContent) {
            return false;
        }

        if (version_compare((string)$xmlContent->currentVersion, $this->version, '>')) {
            return true;
        }

        return false;
    }

    /**
     * @param string $file
     * @param int $timeout
     * @return bool
     */
    public function x13isFresh($file, $timeout = 60)
    {
        if (!file_exists($file)) {
            return false;
        }

        if (($time = @filemtime($file)) && filesize($file) > 0) {
            return ((time() - $time) < $timeout);
        }

        return false;
    }

    /** @var bool */
    protected static $is_x13_up = true;

    /**
     * @param string $file_to_refresh
     * @param string $external_file
     * @return bool
     */
    public function x13refresh($file_to_refresh, $external_file)
    {
        if (self::$is_x13_up && $content = Tools::file_get_contents($external_file)) {
            return (bool)file_put_contents($file_to_refresh, $content);
        }
        self::$is_x13_up = false;
        return false;
    }

    public function renderAdminMessage($message, $className = 'warning')
    {
        if (is_array($message)) {
            $message = join(', ', $message);
        }

        $content = str_replace($this->displayName, '<span class="badge badge-'.$className .'"><b>'.$this->displayName.'</b></span>', $message);

        return $content;
    }
}
