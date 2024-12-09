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
 * Class GdprCustomScript Holds custom Hook-able scripts
 */
class GdprCustomScript extends CustomObjectModel
{
    public $id_gdpr_custom_script;
    public $active;
    public $keep_inline;
    public $internal_name;
    public $external_css;
    public $external_js;
    public $inline_css;
    public $inline_js;
    public $position;
    public $category;
    public $expiry;
    public $provider;
    public $frontend_name;
    public $description;
    public $date_add;

    public static $definition = array(
        'table'          => 'gdpr_custom_script',
        'primary'        => 'id_gdpr_custom_script',
        'multilang'      => true,
        'multilang_shop' => true,
        'fields'         => array(
            'id_gdpr_custom_script' => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isInt',
                'lang'     => false,
            ),
            'active'                => array(
                'type'     => self::TYPE_BOOL,
                'validate' => 'isBool',
                'db_type'  => 'int',
                'lang'     => false,
            ),
            'keep_inline'           => array(
                'type'     => self::TYPE_BOOL,
                'validate' => 'isBool',
                'db_type'  => 'int',
                'lang'     => false,
            ),
            'internal_name'         => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => false,
            ),
            'external_css'          => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => false,
            ),
            'external_js'           => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => false,
            ),
            'inline_css'            => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => false,
            ),
            'inline_js'             => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => false,
            ),
            'position'              => array(
                'type'    => self::TYPE_INT,
                'db_type' => 'int',
                'lang'    => false,
            ),
            'category'              => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => false,
            ),
            'expiry'                => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => true,
            ),
            'provider'              => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => true,
            ),
            'frontend_name'         => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => true,
            ),
            'description'           => array(
                'type'    => self::TYPE_STRING,
                'db_type' => 'text',
                'lang'    => true,
            ),
            'date_add'              => array(
                'type'     => self::TYPE_DATE,
                'validate' => 'isDate',
                'lang'     => false,
            ),
        ),
    );

    public function getUniqueScriptId()
    {
        return "{$this->id}_" . Tools::str2url($this->internal_name);
    }

    /**
     * Update the blocks position
     *
     * @param $way
     * @param $position
     *
     * @return bool
     */
    public function updatePosition($way, $position)
    {
        try {
            $primaryKey = self::$definition['primary'];
            $tableName = self::$definition['table'];
            $sql = new \DbQuery();
            $sql->select("{$primaryKey}, position");
            $sql->from($tableName);
            $sql->orderBy('position ASC');

            if (!$res = \Db::getInstance()->executeS($sql)) {
                return false;
            }
        } catch (\PrestaShopDatabaseException $e) {
            return false;
        }

        foreach ($res as $block) {
            if ((int)$block[$primaryKey] == (int)$this->{$primaryKey}) {
                $movedBlock = $block;
            }
        }

        if (!isset($movedBlock) || !isset($position)) {
            return false;
        }
        try {
            // < and > statements rather than BETWEEN operator
            // since BETWEEN is treated differently according to databases
            $positions = (\Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . $tableName . '`
			SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . (
                $way
                        ? '> ' . (int)$movedBlock['position'] . ' AND `position` <= ' . (int)$position
                        : '< ' . (int)$movedBlock['position'] . ' AND `position` >= ' . (int)$position
            ))
                && \Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . self::$definition['table'] . '`
			SET `position` = ' . (int)$position . '
			WHERE `' . $primaryKey . '` = ' . (int)$movedBlock[$primaryKey]));
        } catch (\Exception $exception) {
            var_dump($exception);
            die();
        }
        return $positions;
    }

    public static function getNextFreePosition()
    {
        $query = new \DbQuery();
        $query->from('amp_block');
        $query->select('count(*)');
        $result = (int)\Db::getInstance()->getValue($query);

        return $result * 1;
    }


    /**
     * @param null $idLang
     *
     * @return self[]
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getActiveScripts($idLang = null)
    {
        $table = self::$definition['table'];
        $primaryKey = self::$definition['primary'];
        $query = new DbQuery();
        $query->select('*');
        $query->from($table, 'c');
        $query->where('active = 1');
        $query->orderBy('position ASC');
        if ($idLang !== null) {
            $query->innerJoin("{$table}_lang", 'l', "c.{$primaryKey} = l.{$primaryKey} AND l.id_lang = {$idLang}");
        } else {
            $query->innerJoin("{$table}_lang", 'l', "c.{$primaryKey} = l.{$primaryKey}");
        }
        return static::hydrateCollection(
            'GdprCustomScript',
            Db::getInstance()->executeS($query),
            $idLang
        );
    }

    public function toUnHookableArray()
    {
        return array(
            'module_id'     => $this->getUniqueScriptId(),
            'category'      => $this->category,
            'enabled'       => $this->active,
            'keep_inline'   => $this->keep_inline,
            'provider'      => $this->provider,
            'frontend_name' => $this->frontend_name,
            'expiry'        => $this->expiry,
            'description'   => $this->description,
            'inline_js'     => (!empty($this->inline_js)) ? $this->inline_js : false,
            'inline_css'    => (!empty($this->inline_css)) ? $this->inline_css : false,
            'external_js'   => (!empty($this->external_js)) ? $this->external_js : false,
            'external_css'  => (!empty($this->external_css)) ? $this->external_css : false,
        );
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @return array
     */
    public static function getUnHookableScripts()
    {
        $scripts = static::getActiveScripts();

        if (!is_array($scripts) || empty($scripts)) {
            return array();
        }
        $return = array();

        foreach ($scripts as $script) {
            $script = $script->toUnHookableArray();
            $return[$script['module_id']] = $script;
        }

        return $return;
    }
}
