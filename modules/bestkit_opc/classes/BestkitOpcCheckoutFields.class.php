<?php
/**
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
*
*  @author     BEST-KIT.COM (contact@best-kit.com)
*  @copyright  http://best-kit.com
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once _PS_MODULE_DIR_ . 'bestkit_opc/includer.php';

class BestkitOpcCheckoutFields extends ObjectModel
{
    public $id;
    public $id_bestkit_opc_checkoutfield;
    public $step;
    public $name;
    public $public_name;
    public $validate;
    public $required;
    public $default_value;
    public $position;
    public $active;
    public $standard = 0;

    public static $definition = array(
        'table' => 'bestkit_opc_checkoutfield',
        'primary' => 'id_bestkit_opc_checkoutfield',
        'multilang' => TRUE,
        'fields' => array(
			'step' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => TRUE, 'size' => 255),
			'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => TRUE, 'size' => 255),
			'validate' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
			'required' => array('type' => self::TYPE_INT),
            'default_value' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
			'position' => array('type' => self::TYPE_INT),
			'active' => array('type' => self::TYPE_INT),
			'standard' => array('type' => self::TYPE_INT),

			// Lang fields
			'public_name' => array('type' => self::TYPE_STRING, 'lang' => TRUE, 'required' => TRUE, 'validate' => 'isGenericName', 'size' => 255),
        ),
		'associations' => array(
			'bestkit_opc_checkoutfield' => array('type' => self::HAS_ONE, 'table' => 'bestkit_opc_checkoutfield_shop'),
		),
    );
	
	public function __construct($id = NULL, $id_lang = NULL, $id_shop = NULL) {
		Shop::addTableAssociation('bestkit_opc_checkoutfield', array('type' => 'shop'));
		return parent::__construct($id, $id_lang, $id_shop);
	}

	public function add($autodate = true, $null_values = false)
	{
		$this->position = self::getLastPosition(pSql($this->step));
		return parent::add($autodate, $null_values);
	}

	public function update($null_values = false)
	{
		parent::update($null_values);
		/*if (parent::update($null_values))
			return $this->cleanPositions($this->step);
		return false;*/ 				//will be run manually
	}

	public function delete()
	{
	 	if (parent::delete())
			return $this->cleanPositions($this->step);
		return false;
	}

    public static function checkExists($name, $step = FALSE)
    {
        $result = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'bestkit_opc_checkoutfield`
            WHERE `name` = "' . pSQL($name) . '"' . ($step ?  ' AND step = "' . pSQL($step) . '"' : '')
        );
		
		return $result;
    }

	public function updatePosition($way, $position)
	{
        $step = Tools::getValue('step');
		if (!$res = Db::getInstance()->executeS('
			SELECT cp.`id_bestkit_opc_checkoutfield`, cp.`position`, cp.`step`
			FROM `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield` cp
			WHERE `step` = "' . pSql($step) . '"
			ORDER BY cp.`position` ASC'
		)) {
			return FALSE;
		}

		foreach ($res as $field) {
			if ((int)$field['id_bestkit_opc_checkoutfield'] == (int)$this->id) {
				$moved_field = $field;
			}
		}

		if (!isset($moved_field) || !isset($position)) {
			return FALSE;
		}

		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases
		return (Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield` cp
			SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($way
				? '> ' . (int)$moved_field['position'] . ' AND `position` <= ' . (int)$position
				: '< ' . (int)$moved_field['position'] . ' AND `position` >= ' . (int)$position) . '
			AND `step` = "' . pSql($step) . '"'
		&& Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield` cp
			SET `position` = ' . (int)$position.'
			WHERE `step` = "' . (int)$moved_field['step'] . '"
			AND `id_bestkit_opc_checkoutfield`='.(int)$moved_field['id_bestkit_opc_checkoutfield'])));
	}
	
	public static function cleanPositions($step = NULL)
	{
		/*$sql = '
		SELECT `id_bestkit_opc_checkoutfield`
		FROM `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield`
		WHERE `step` = "' . pSql($step) . '"
		ORDER BY `position`';

		$result = Db::getInstance()->executeS($sql);

		for ($i = 0, $total = count($result); $i < $total; ++$i) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield`
					SET `position` = ' . (int)$i . '
					WHERE `step` = "' . pSql($step) . '"
						AND `id_bestkit_opc_checkoutfield` = ' . (int)$result[$i]['id_bestkit_opc_checkoutfield'];
			Db::getInstance()->execute($sql);
		}*/
		
		$result = Db::getInstance()->execute('
			update `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield` cp1 join (
			   select id_bestkit_opc_checkoutfield, @i := @i+1 new_position
			   from `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield`, (select @i:=-1) temp
			   where `step` = "' . pSql($step) . '" order by position asc
			) cp2 on cp1.id_bestkit_opc_checkoutfield = cp2.id_bestkit_opc_checkoutfield set cp1.position = cp2.new_position
		');
		
		return $result;
	}
	
	public static function cleanPositionsManually($step)
	{
		$result = Db::getInstance()->execute('
			update `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield` cp1 join (
			   select id_bestkit_opc_checkoutfield, @i := @i+1 new_position
			   from `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield`, (select @i:=-1) temp
			   where `step` = "' . pSql($step) . '" order by position asc
			) cp2 on cp1.id_bestkit_opc_checkoutfield = cp2.id_bestkit_opc_checkoutfield set cp1.position = cp2.new_position
		');
		
		return $result;
	}
	
	public static function getLastPosition($step)
	{
		$sql = '
		SELECT MAX(position) + 1
		FROM `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield`
		WHERE `step` = "' . pSql($step) . '"';
 
		return (Db::getInstance()->getValue($sql));
	}
	
	public static function getSteps()
	{
		$sql = '
		SELECT step
		FROM `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield`
		GROUP BY `step`';
 
		return (Db::getInstance()->executeS($sql));
	}
	
	public static function getFieldsForStep($step, $id_lang, $id_shop = NULL)
	{
		$sql = '
		SELECT *
		FROM `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield` cf
		JOIN `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield_lang` cfl ON (cf.`id_bestkit_opc_checkoutfield` = cfl.`id_bestkit_opc_checkoutfield` AND cfl.`id_lang` = ' . (int)$id_lang . ')
		' . (Shop::isFeatureActive() && $id_shop ? 'JOIN `' . _DB_PREFIX_ . 'bestkit_opc_checkoutfield_shop` cfs ON (cfs.`id_bestkit_opc_checkoutfield` = cfs.`id_bestkit_opc_checkoutfield` AND cfs.`id_shop` = ' . (int)$id_shop . ')' : '') . '
		WHERE `step` = "' . pSql($step) . '"
		ORDER BY cf.`position`';
 
		return (Db::getInstance()->executeS($sql));
	}
}