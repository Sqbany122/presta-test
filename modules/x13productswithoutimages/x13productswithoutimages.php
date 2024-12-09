<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('X13_ION_VERSION_PWI')) {
    if (PHP_VERSION_ID >= 70100) {
        $x13IonVer = '-71';
    } else if (PHP_VERSION_ID >= 70000) {
        $x13IonVer = '-7';
    } else {
        $x13IonVer = '';
    }
    define('X13_ION_VERSION_PWI', $x13IonVer);
}

require_once (dirname(__FILE__) . '/classes/XProductsWI.php');


class x13productswithoutimages extends Module
{
    public $bootstrap;

    private static $admin_tabs = array(
        'AdminXProductsWI' => 'X13 Off products without images'
    );

    private static $config_fields = array(
        'X13_PWI_TYPE' => XProductsWI::PWI_DISABLE,
        'X13_PWI_IGNORE_PRODUCTS' => '',
    );

    public function __construct()
    {
        $this->name = 'x13productswithoutimages';
        $this->tab = 'search_filter';
        $this->version = '1.1.1';
        $this->author = 'x13.pl';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7.99.9999');

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->bootstrap = false;
        } else {
            $this->bootstrap = true;
        }

        parent::__construct();

        $this->displayName = $this->l('X13 Off products without images');
        $this->description = $this->l('Turn off the products without images');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        foreach (self::$admin_tabs as $controller => $name)
        {
            $tab = new Tab();
            foreach (Language::getLanguages() as $lang) {
                $tab->name[$lang['id_lang']] = $this->displayName;
            }
            $tab->class_name = $controller;
            $tab->id_parent = -1;
            $tab->active = 1;
            $tab->module = $this->name;
            $tab->add();
        }

        foreach (self::$config_fields as $field => $value) {
            Configuration::updateValue($field, $value);
        }

        $this->registerHook('displayBackOfficeHeader');

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        foreach (Tab::getCollectionFromModule($this->name) as $tab) {
            $tab->delete();
        }

        foreach (self::$config_fields as $field) {
            Configuration::deleteByName($field);
        }

        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminXProductsWI'));
    }

    /**
     * Check for module update
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (defined('_PS_ADMIN_DIR_') && !Tools::getValue('ajax', 0)) {
            if ($this->checkForNewVersion()) {
                $this->context->controller->warnings[] = $this->renderAdminMessage(
                    sprintf($this->l('The new version of the module % s is now available! - download it from x13.pl'), $this->displayName)
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

        $xmlContent = @simplexml_load_string(Tools::file_get_contents($upgradeFile));
        if ($xmlContent) {
            if (version_compare((string)$xmlContent->currentVersion, $this->version, '>')) {
                return true;
            }
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
        if (self::$is_x13_up && $content = Tools::file_get_contents($external_file, false, null, 2)) {
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
