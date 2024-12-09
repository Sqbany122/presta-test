<?php

class ProductsOffModel extends Module
{
	public static $_confList = array(
		'PRODUCT_OFF_TYPE' => 1,
        'PRODUCT_OFF_IGNORE_PRODUCTS' => '',
        'PRODUCT_OFF_TRESHOLD' => '1',
        'PRODUCT_OFF_AUTOENABLE' => 0,
        'PRODUCT_OFF_AUTOENABLE_TRESHOLD' => '0',
        'PRODUCT_OFF_AUTOENABLE_TYPE' => 1,
	);
	
	public $_ver;

    public function __construct()
    {
        parent::__construct();
		
		$this->_ver = '1_6';
		
		if (version_compare(_PS_VERSION_, '1.5.0', '>=') === true && version_compare(_PS_VERSION_, '1.6.0', '<') === true)
			$this->_ver = '1_5';
    }

    public function install()
    {
        $r = parent::install();

        foreach (self::$_confList as $key => $default) {
            Configuration::updateValue($key, $default);
        }

        $this->registerHook('displayBackOfficeHeader');
			
		if (!$r)
			return false;
		
		return true;
    }

    public function uninstall()
    {		
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return '<p class="alert alert-warning">' .
            $this->l('You cannot manage module from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit', 'ProductsOffModel') .
                '</p>';
        }
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitUpdate'.$this->name)) == true) {
            $this->postProcess();
        }

        if (Tools::isSubmit('resetProducts')) {
            if ($this->resetProducts((int) Tools::getValue('id_shop'))) {
                Tools::redirectAdmin('index.php?controller=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&conf=4');
            }
        }
		
		$module_url = Tools::getProtocol(Tools::usingSecureMode()).$_SERVER['HTTP_HOST'].$this->getPathUri();
		
		$this->context->smarty->assign(array(
			'shop_name' => $this->context->shop->name,
            'shop_id' => (int) $this->context->shop->id,
			'token_check' => substr(Tools::encrypt('x13productsoff/index'), 0, 10),
			'module_url' => $module_url,
            'mode' => (int) Configuration::get('PRODUCT_OFF_TYPE'),
            'reset_products_url' => 'index.php?controller=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&resetProducts=1',
            'lastCronUpdate' => Configuration::get('X13_PRODUCTSOFF_UPDATE_'.(int) $this->context->shop->id)
		));
		
		$this->context->controller->addJs($this->_path.'js/admin.js');
		
       return $this->renderForm().$this->display(dirname(dirname(__FILE__)), 'manage'.($this->_ver != '1_6' ? '_'.$this->_ver : '').'.tpl');
    }
	
    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
		$data = array();
		
		foreach (self::$_confList as $key => $default)
			$data[$key] = Tools::getValue($key, Configuration::get($key));
		
		return $data;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach ($form_values as $key => $default) {
            Configuration::updateValue($key, Tools::getValue($key, $default));
        }
    }

    /**
    * Check if product has attributes combinations
    *
    * @return int Attributes combinations number
    */
    public function hasAttributes($idProduct)
    {
        if (!Combination::isFeatureActive()) {
            return 0;
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'product_attribute` pa
            '.Shop::addSqlAssociation('product_attribute', 'pa').'
            WHERE pa.`id_product` = '.(int) $idProduct
        );
    }

    public function getAttributes($idProduct)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT pa.`id_product_attribute`
            FROM `'._DB_PREFIX_.'product_attribute` pa
            '.Shop::addSqlAssociation('product_attribute', 'pa').'
            WHERE pa.`id_product` = '.(int) $idProduct
        );
    }
	
	public function checkProductQuantity()
	{
		$return = array();
		$id_shop_stock = $id_shop = (int)Tools::getValue('id_shop');
		$get_list = (bool)Tools::getValue('get_list');
		$id_lang = (int)$this->context->language->id; 
		
		$shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$id_shop));

        Shop::setContext(Shop::CONTEXT_SHOP, $id_shop_stock);
		
		if ($shop_group->share_stock) {
			$id_shop_stock = null;
        }

		$PRODUCT_OFF_TYPE = (int)Configuration::get('PRODUCT_OFF_TYPE');
		
        switch ($PRODUCT_OFF_TYPE) {
            case 1:
                $where = 'ps.active = 1';
                $set = 'active = 0';
                break;

            case 2:
                $where = 'ps.`visibility` = \'both\'';
                $set = 'visibility = \'none\'';
                break;

            case 3:
                $where = 'ps.`visibility` = \'both\'';
                $set = 'visibility = \'search\'';
                break;

            case 4:
                $where = 'ps.`visibility` = \'both\'';
                $set = 'visibility = \'catalog\'';
                break;
            
            default:
                // code...
                break;
        }

        $productsToIgnore = array();

        $ignoredProducts = Configuration::get('PRODUCT_OFF_IGNORE_PRODUCTS');
        $ignoredProducts = trim(preg_replace('/\s/', '', $ignoredProducts), ',');
        
        if ($ignoredProducts) {
            $parts = explode(',', $ignoredProducts);
            if (is_array($parts) && count($parts) > 0) {
                $productsToIgnore = $parts;
            }
        }

        /* Disabling / hiding products */
        $turnOffTreshold = Configuration::get('PRODUCT_OFF_TRESHOLD');
        $sql = '
            SELECT p.`id_product`
            FROM '._DB_PREFIX_.'product p
            LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product) 
            WHERE '.$where.'
            '.(count($productsToIgnore) > 0 ? 'AND p.`id_product` NOT IN ('.implode(',', $productsToIgnore).')' : '').'
            AND ps.id_shop = '.$id_shop.'
        ';
		
        $products = Db::getInstance()->ExecuteS($sql);

        $productsWithoutStock = array();
        $product_disabled = array();

		if ($products) {
            foreach ($products as $product) {
                $idProduct = (int) $product['id_product'];
                $hasAttributes = false;
                if (Combination::isFeatureActive()) {
                    $hasAttributes = $this->hasAttributes($idProduct);
                }

                if ($hasAttributes) {
                    $combinationsQty = 0;
                    $combinations = $this->getAttributes($idProduct);

                    if ($combinations) {
                        foreach ($combinations as $combination) {
                            $combinationsQty += StockAvailable::getQuantityAvailableByProduct($idProduct, (int) $combination['id_product_attribute'], $id_shop_stock);
                        }
                    }

                    $quantity = $combinationsQty;

                } else {
                    $quantity = StockAvailable::getQuantityAvailableByProduct($idProduct, null, $id_shop_stock);                
                }

                if ($quantity < $turnOffTreshold) {
                    $productsWithoutStock[] = $idProduct;
                }
            }

            if (count($productsWithoutStock) > 0) {
                Db::getInstance()->Execute('
                    UPDATE '._DB_PREFIX_.'product_shop ps
                    SET '.$set.'
                    WHERE '.$where.'
                    AND id_product IN ('.implode(',', $productsWithoutStock).')
                    AND id_shop = '.$id_shop.'
                ');

                Db::getInstance()->Execute('
                    UPDATE '._DB_PREFIX_.'product ps
                    SET '.$set.'
                    WHERE '.$where.'
                    AND id_product IN ('.implode(',', $productsWithoutStock).')
                    AND id_shop_default = '.$id_shop
                );

                if ($get_list) {
                    $products = Db::getInstance()->executeS('
                        SELECT p.`id_product`, pl.`name`
                        FROM `' . _DB_PREFIX_ . 'product` p
                        JOIN `' . _DB_PREFIX_ . 'product_shop` ps
                            ON (p.`id_product` = ps.`id_product` AND ps.`id_shop` = ' . (int)$id_shop . ')
                        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                            ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . ' AND pl.`id_shop` = '.(int) $id_shop.')
                         WHERE p.`id_product` IN ('.implode(',', $productsWithoutStock).')
                    ');

                    foreach ($products as $product) {
                        $product_disabled[$product['id_product']] = array(
                            'name' => $product['name'],
                            'id_product' => $product['id_product']
                        );
                    }
                }
            }
			
			if ($get_list) {
				$return['products'] = $product_disabled;
			} else {
				$return['updated'] = count($productsWithoutStock);
			}
		}

        /* Enabling / showing products again */
        if (Configuration::get('PRODUCT_OFF_AUTOENABLE')) {
            $turnOnTreshold = Configuration::get('PRODUCT_OFF_AUTOENABLE_TRESHOLD');

            if (Configuration::get('PRODUCT_OFF_AUTOENABLE_TYPE') == 2) {
                $PRODUCT_OFF_TYPE = 5;
            }

            switch ($PRODUCT_OFF_TYPE) {
                case 1:
                    $where = 'ps.active = 0';
                    $set = 'active = 1';
                    break;

                case 2:
                    $where = 'ps.`visibility` = \'none\'';
                    $set = 'visibility = \'both\'';
                    break;

                case 3:
                    $where = 'ps.`visibility` = \'search\'';
                    $set = 'visibility = \'both\'';
                    break;

                case 4:
                    $where = 'ps.`visibility` = \'catalog\'';
                    $set = 'visibility = \'both\'';
                    break;

                case 5:
                    $where = '(ps.visibility != \'both\' OR ps.active != 1)';
                    $set = 'ps.visibility = \'both\', ps.active = 1';
                    break;
                
                default:
                    // code...
                    break;
            }

            $sql = '
                SELECT p.`id_product`
                FROM '._DB_PREFIX_.'product p
                LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product) 
                WHERE '.$where.'
                '.(count($productsToIgnore) > 0 ? 'AND p.`id_product` NOT IN ('.implode(',', $productsToIgnore).')' : '').'
                AND ps.id_shop = '.$id_shop.'
            ';
            
            $products = Db::getInstance()->ExecuteS($sql);

            $productsWithStock = array();
            $products_enabled = array();

            if ($products) {
                foreach ($products as $product) {
                    $idProduct = (int) $product['id_product'];
                    $hasAttributes = false;
                    if (Combination::isFeatureActive()) {
                        $hasAttributes = $this->hasAttributes($idProduct);
                    }

                    if ($hasAttributes) {
                        $combinationsQty = 0;
                        $combinations = $this->getAttributes($idProduct);

                        if ($combinations) {
                            foreach ($combinations as $combination) {
                                $combinationsQty += StockAvailable::getQuantityAvailableByProduct($idProduct, (int) $combination['id_product_attribute']);
                            }
                        }

                        $quantity = $combinationsQty;

                    } else {
                        $quantity = StockAvailable::getQuantityAvailableByProduct($idProduct);                
                    }

                    if ($quantity > $turnOnTreshold) {
                        $productsWithStock[] = $idProduct;
                    }
                }

                if (count($productsWithStock) > 0) {
                    Db::getInstance()->Execute('
                        UPDATE '._DB_PREFIX_.'product_shop ps
                        SET '.$set.'
                        WHERE '.$where.'
                        AND id_product IN ('.implode(',', $productsWithStock).')
                        AND id_shop = '.$id_shop.'
                    ');

                    Db::getInstance()->Execute('
                        UPDATE '._DB_PREFIX_.'product ps
                        SET '.$set.'
                        WHERE '.$where.'
                        AND id_product IN ('.implode(',', $productsWithStock).')
                        AND id_shop_default = '.$id_shop
                    );

                    if ($get_list) {
                        $products = Db::getInstance()->executeS('
                            SELECT p.`id_product`, pl.`name`
                            FROM `' . _DB_PREFIX_ . 'product` p
                            JOIN `' . _DB_PREFIX_ . 'product_shop` ps
                                ON (p.`id_product` = ps.`id_product` AND ps.`id_shop` = ' . (int)$id_shop . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                                ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . ' AND pl.`id_shop` = '.(int) $id_shop.')
                             WHERE p.`id_product` IN ('.implode(',', $productsWithStock).')
                        ');

                        foreach ($products as $product) {
                            $products_enabled[$product['id_product']] = array(
                                'name' => $product['name'],
                                'id_product' => $product['id_product']
                            );
                        }
                    }
                }
                
                if ($get_list) {
                    $return['products_enabled'] = $products_enabled;
                } else {
                    $return['nb_enabled'] = count($productsWithStock);
                }
            }
        }
        $return['mode'] = $PRODUCT_OFF_TYPE;

        Configuration::updateValue('X13_PRODUCTSOFF_UPDATE_'.(int) $id_shop, date('d.m.Y H:i:s'));

        try {
            Hook::exec('updateproduct');
        } catch (Exception $e) {}
		
		die(Tools::jsonEncode($return));
	}

    public function resetProducts($idShop)
    {
        $result = Db::getInstance()->Execute('
            UPDATE '._DB_PREFIX_.'product_shop ps
            SET ps.`visibility` = \'both\', ps.`active` = 1
            WHERE id_shop = '.$idShop.'
        ');

        $result &= Db::getInstance()->Execute('
            UPDATE '._DB_PREFIX_.'product ps
            SET ps.`visibility` = \'both\', ps.`active` = 1
            WHERE 1=1 AND id_shop_default = ' . $idShop
        );

        try {
            Hook::exec('updateproduct');
        } catch (Exception $e) {}

        return $result;
    }

    /**
     * Check for module update
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (defined('_PS_ADMIN_DIR_') && !Tools::getValue('ajax', 0)) {
            if ($this->checkForNewVersion()) {
                $this->context->controller->warnings[] = $this->renderAdminMessage(
                    sprintf($this->l('The new version of the module % s is now available! - download it from x13.pl', 'ProductsOffModel'), $this->displayName)
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
