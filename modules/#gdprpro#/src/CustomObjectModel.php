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
 * Class CustomObjectModel
 */
abstract class CustomObjectModel extends ObjectModel
{
    /**
     * Return informations of the columns that exists in the
     * table relative to the ObjectModel. If the Model has multilang enabled,
     * this method also returns information about the multilang table.
     */
    public function getDatabaseColumns()
    {
        $columns = array();
        $definition = ObjectModel::getDefinition($this);
        $muchTableMuchProtected = pSQL($definition['table']);
        $sql = 'SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA="' .
            _DB_NAME_ .
            '" AND TABLE_NAME="' .
            _DB_PREFIX_ .
            $muchTableMuchProtected . '"';
        $columns['self'] = Db::getInstance()->executeS($sql, true, false);
        $sql = 'SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA="' .
            _DB_NAME_ .
            '" AND TABLE_NAME="' .
            _DB_PREFIX_ .
            $muchTableMuchProtected .
            '_lang"';
        $columns['lang'] = Db::getInstance()->executeS($sql, true, false);
        return $columns;
    }

    /**
     * Add a column in the table relative to the ObjectModel.
     * This method uses the $definition property of the ObjectModel,
     * with some extra properties.
     *
     * Example:
     * 'table'        => 'tablename',
     * 'primary'      => 'id',
     * 'fields'       => [
     *     'id'     => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
     *     'number' => [
     *         'type'     => self::TYPE_STRING,
     *         'db_type'  => 'varchar(20)',
     *         'required' => true,
     *         'default'  => '25'
     *     ],
     * ],
     *
     * The primary column is created automatically as INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT. The other columns
     * require an extra parameter, with the type of the column in the database.
     *
     *
     */
    public function createColumn(
        $field_name,
        $column_definition
    ) {
        $definition = ObjectModel::getDefinition($this);
        //object model has a multilang table
        $multilang = isset($definition['multilang']) && $definition['multilang'];
        $muchTableMuchProtected = pSQL($definition['table']);
        if ($multilang && $column_definition['lang']) {
            $sql = 'ALTER TABLE ' . _DB_PREFIX_ . $muchTableMuchProtected . '_lang';
        } else {
            $sql = 'ALTER TABLE ' . _DB_PREFIX_ . $muchTableMuchProtected;
        }
        $sql .= ' ADD COLUMN ' . pSQL($field_name) . ' ' . pSQL($column_definition['db_type']);
        $field = array();
        if ($field_name === $definition['primary'] && !$column_definition['lang']) {
            $sql .= ' INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT';
        } else {
            if (isset($field['required']) && $field['required']) {
                $sql .= ' NOT NULL';
            }
            if (isset($field['default'])) {
                $sql .= ' DEFAULT "' . pSQL($field['default']) . '"';
            }
        }
        Db::getInstance()->execute($sql);
    }

    /**
     *  Create in the database every column detailed in the $definition property that are
     *  missing in the database.
     */
    public function createMissingColumns()
    {
        $columns = $this->getDatabaseColumns();
        $definition = ObjectModel::getDefinition($this);
        $multilang = isset($definition['multilang']) && $definition['multilang'];
        foreach ($definition['fields'] as $column_name => $column_definition) {
            //column exists in database
            $exists = false;
            if ($multilang && $column_definition['lang']) {
                //column exists in database
                foreach ($columns['lang'] as $column) {
                    if ($column['COLUMN_NAME'] === $column_name) {
                        $exists = true;
                        break;
                    }
                }
            } else {
                foreach ($columns['self'] as $column) {
                    if ($column['COLUMN_NAME'] === $column_name) {
                        $exists = true;
                        break;
                    }
                }
            }
            if (!$exists) {
                $this->createColumn($column_name, $column_definition);
            }
        }
        //verify the foreign keys in the multilang table
        if ($multilang) {
            //id_lang column
            $column_name = 'id_lang';
            $exists = false;
            foreach ($columns['lang'] as $column) {
                if ($column['COLUMN_NAME'] === $column_name) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $column_definition = array('lang' => true, 'db_type' => 'int unsigned');
                $this->createColumn($column_name, $column_definition);
            }
            //foreign key column
            $column_name = $definition['primary'];
            $exists = false;
            foreach ($columns['lang'] as $column) {
                if ($column['COLUMN_NAME'] === $column_name) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $column_definition = array('lang' => true, 'db_type' => 'int unsigned');
                $this->createColumn($column_name, $column_definition);
            }
        }
    }

    /**
     *  Create the database table with its columns. Similar to the createColumn() method.
     */
    public function createDatabase()
    {
        $definition = ObjectModel::getDefinition($this);
        $multilang = isset($definition['multilang']) && $definition['multilang'];
        $muchTableMuchProtected = pSQL($definition['table']);
        $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . $muchTableMuchProtected . ' (';
        $sql .= $definition['primary'] . ' INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,';
        foreach ($definition['fields'] as $field_name => $field) {
            $field = $this->sanitizeColumnDefinition($field);
            if ($field_name === $definition['primary']) {
                continue;
            }
            if ($multilang && $field['lang']) {
                continue;
            }
            $sql .= $field_name . ' ' . $field['db_type'];
            if (isset($field['required']) && $field['required']) {
                $sql .= ' NOT NULL';
            }
            if (isset($field['default'])) {
                $sql .= ' DEFAULT "' . pSQL($field['default']) . '"';
            }
            $sql .= ',';
        }
        $sql = trim($sql, ',');
        $sql .= ')';
        Db::getInstance()->execute($sql);
        //create multilang tables
        if ($multilang) {
            $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . $muchTableMuchProtected . '_lang (';
            $sql .= pSQL($definition['primary']) . ' INTEGER UNSIGNED NOT NULL,';
            $sql .= 'id_lang INTEGER UNSIGNED NOT NULL,';
            if ($definition['multilang_shop']) {
                $sql .= 'id_shop INTEGER UNSIGNED NOT NULL,';
            }
            foreach ($definition['fields'] as $field_name => $field) {
                $field = $this->sanitizeColumnDefinition($field);
                if ($field_name === $definition['primary']) {
                    continue;
                }
                if (!$field['lang']) {
                    continue;
                }
                $sql .= pSQL($field_name) . ' ' . pSQL($field['db_type']);
                if (isset($field['required']) && $field['required']) {
                    $sql .= ' NOT NULL';
                }
                if (isset($field['default'])) {
                    $sql .= ' DEFAULT "' . pSQL($field['default']) . '"';
                }
                $sql .= ',';
            }
            $sql = trim($sql, ',');
            $sql .= ')';
            Db::getInstance()->execute($sql);
        }
    }

    public function dropDatabase()
    {
        $definition = ObjectModel::getDefinition($this);
        $multilang = isset($definition['multilang']) && $definition['multilang'];
        $muchTableMuchProtected = pSQL($definition['table']);
        $sql = 'DROP TABLE ' . _DB_PREFIX_ . $muchTableMuchProtected;
        Db::getInstance()->execute($sql);
        if ($multilang) {
            $sql = 'DROP TABLE ' . _DB_PREFIX_ . $muchTableMuchProtected . '_lang';
            Db::getInstance()->execute($sql);
        }
    }

    /**
     * Sanitize a column definition in order to make sure that all required keys are present
     *
     * @param $column_definition
     *
     * @return mixed
     */
    protected function sanitizeColumnDefinition($column_definition)
    {
        if (!isset($column_definition['db_type'])) {
            if ($column_definition['type'] == self::TYPE_STRING) {
                $column_definition['db_type'] = 'text';
            }
            if ($column_definition['type'] == self::TYPE_INT) {
                $column_definition['db_type'] = 'int';
            }
            if ($column_definition['type'] == self::TYPE_BOOL) {
                $column_definition['db_type'] = 'tinyint(1)';
            }
            if ($column_definition['type'] == self::TYPE_DATE) {
                $column_definition['db_type'] = 'DATE';
            }
            if ($column_definition['type'] == self::TYPE_FLOAT) {
                $column_definition['db_type'] = 'float';
            }
        }

        return $column_definition;
    }
}
