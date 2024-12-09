<?php
/**
* 2014 DPD Polska Sp. z o.o.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* prestashop@dpd.com.pl so we can send you a copy immediately.
*
*  @author    JSC INVERTUS www.invertus.lt <help@invertus.lt>
*  @copyright 2014 DPD Polska Sp. z o.o.
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*  International Registered Trademark & Property of DPD Polska Sp. z o.o.
*/

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Class DpdPolandPackage Responsible for DPD packages management
 */
class DpdPolandPackage extends DpdPolandObjectModel
{
	public $id_package;

	public $id_package_ws;

	public $id_order;

	public $sessionId;

	public $sessionType;

	public $payerNumber;

	public $id_address_sender;

	public $id_address_delivery;

	public $cod_amount;

	public $declaredValue_amount;

	public $ref1;

	public $ref2;

	public $additional_info;

	public $labels_printed = 0;

	public $id_sender_address;

	public $cud;

	public $rod;

	public $date_add;

	public $date_upd;

    /**
     * @var object Package WebServices instance
     */
	private $webservice;

    /**
     * @var array Class variables and their validation types
     */
	public static $definition = array(
		'table' => _DPDPOLAND_PACKAGE_DB_,
		'primary' => 'id_package',
		'multilang' => false,
		'multishop' => false,
		'fields' => array(
			'id_package'			=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_package_ws'			=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_order'				=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'sessionId'				=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'sessionType'			=>	array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'payerNumber'			=>	array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'id_address_sender'		=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_address_delivery'	=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'cod_amount'			=>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'declaredValue_amount'	=>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'ref1'					=>	array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'ref2'					=>	array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'additional_info'		=>	array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'labels_printed'		=>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_sender_address'		=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'cud'		            =>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'rod'		            =>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' 				=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' 				=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);

    /**
     * DpdPolandPackage constructor
     * Object identified by id_package_ws rather than id_package
     *
     * @param null|int $id_package_ws Used only as a primary field by ObjectModel
     */
	public function __construct($id_package_ws = null)
	{
		$id_package = $this->getPackageIdByPackageIdWs($id_package_ws);

		parent::__construct($id_package);
	}

    /**
     * Returns package ID according to package ID received from WebServices
     *
     * @param int|string $id_package_ws Package WebServices ID
     * @return false|null|string Package ID
     */
	private function getPackageIdByPackageIdWs($id_package_ws)
	{
		return Db::getInstance()->getValue('
			SELECT `id_package`
			FROM `'._DB_PREFIX_._DPDPOLAND_PACKAGE_DB_.'`
			WHERE `id_package_ws` = "'.(int)$id_package_ws.'"
		');
	}

    /**
     * Removes packages duplicates for order to have only one package
     *
     * @return bool Packages duplicates removed successfully
     */
	public function removeOrderDuplicates()
	{
		$id_last_package_by_order = Db::getInstance()->getValue('
			SELECT `id_package`
			FROM `'._DB_PREFIX_._DPDPOLAND_PACKAGE_DB_.'`
			WHERE `id_order` = "'.(int)$this->id_order.'"
			ORDER BY `id_package` DESC
		');

		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_._DPDPOLAND_PACKAGE_DB_.'`
			WHERE `id_order` = "'.(int)$this->id_order.'"
			AND `id_package` != "'.(int)$id_last_package_by_order.'"
		');
	}

    /**
     * Creates package instance according to order ID
     *
     * @param int $id_order Order ID
     * @return DpdPolandPackage object instance
     */
	public static function getInstanceByIdOrder($id_order)
	{
		$id_package_ws = Db::getInstance()->getValue('
			SELECT `id_package_ws`
			FROM `'._DB_PREFIX_._DPDPOLAND_PACKAGE_DB_.'`
			WHERE `id_order` = "'.(int)$id_order.'"
			ORDER BY `id_package` DESC
		');

		return new DpdPolandPackage($id_package_ws);
	}

    /**
     * Checks if current package has printed labels
     *
     * @return int Printed labels count
     */
	public function isManifestPrinted()
	{
		return (int)Db::getInstance()->getValue('
			SELECT COUNT(`id_manifest_ws`)
			FROM `'._DB_PREFIX_._DPDPOLAND_MANIFEST_DB_.'`
			WHERE `id_package_ws`='.(int)$this->id_package_ws
		);
	}

    /**
     * Returns formatted session type
     *
     * @return string Formatted session type
     */
	public function getSessionType()
	{
		return $this->sessionType == 'international' ? 'INTERNATIONAL' : 'DOMESTIC';
	}

    /**
     * Collects list data and prepares it to be displayed
     *
     * @param string $order_by List order by criteria
     * @param string $order_way List sorting way (ascending, descending)
     * @param string $filter Criteria by which list is filtered
     * @param int $start From which element list will be displayed
     * @param int $pagination How many elements will be displayed in list
     * @return array|false|mysqli_result|null|PDOStatement|resource Collected list data
     */
	public function getList($order_by, $order_way, $filter, $start, $pagination)
	{
		$order_way = Validate::isOrderWay($order_way) ? $order_way : 'ASC';

		$id_shop = (int)Context::getContext()->shop->id;
		$id_lang = (int)Context::getContext()->language->id;

		$list = DB::getInstance()->executeS('
			SELECT
				p.`id_package_ws`							AS `id_package_ws`,
				p.`date_add` 								AS `date_add`,
				p.`id_order` 								AS `id_order`,
				(SELECT COUNT(par.`id_parcel`)
				FROM `'._DB_PREFIX_._DPDPOLAND_PARCEL_DB_.'` par
				WHERE par.`id_package_ws` = p.`id_package_ws`) 	AS `count_parcel`,
				(SELECT parc.`waybill`
				FROM `'._DB_PREFIX_._DPDPOLAND_PARCEL_DB_.'` parc
				WHERE parc.`id_package_ws` = p.`id_package_ws`
				ORDER BY parc.`id_parcel`
				LIMIT 1) 									AS `package_number`,
				CONCAT(a.`firstname`, " ", a.`lastname`) 	AS `receiver`,
				cl.`name` 									AS `country`,
				a.`postcode` 								AS `postcode`,
				a.`city`									AS `city`,
				CONCAT(a.`address1`, " ", a.`address2`)		AS `address`
			FROM `'._DB_PREFIX_._DPDPOLAND_PACKAGE_DB_.'` p
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = p.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'address` a ON (a.`id_address` = p.`id_address_delivery`)
			LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = "'.(int)$id_lang.'")
			WHERE '.(version_compare(_PS_VERSION_, '1.5', '<') ? '' : 'o.`id_shop` = "'.(int)$id_shop.'" AND ').'
				NOT EXISTS(
					SELECT m.`id_manifest_ws`
					FROM `'._DB_PREFIX_._DPDPOLAND_MANIFEST_DB_.'` m
					WHERE m.`id_package_ws` = p.`id_package_ws`
				) '.
			$filter.
			($order_by && $order_way ? ' ORDER BY `'.bqSQL($order_by).'` '.pSQL($order_way) : '').
			($start !== null && $pagination !== null ? ' LIMIT '.(int)$start.', '.(int)$pagination : '')
		);

		if (!$list)
			$list = array();

		return $list;
	}

    /**
     * Splits packages into groups
     * according to session type
     *
     * @param array $ids Packages WebServices IDs
     * @return array Formatted packages groups
     */
	public static function separatePackagesBySession($ids)
	{
		$international_packages = array();
		$domestic_packages = array();

		foreach ($ids as $id_package_ws)
		{
			$package = new DpdPolandPackage((int)$id_package_ws);
			$session_type = $package->getSessionType();
			if ($session_type == 'INTERNATIONAL')
				$international_packages[] = (int)$id_package_ws;
			elseif ($session_type == 'DOMESTIC')
				$domestic_packages[] = (int)$id_package_ws;
		}

		return array('INTERNATIONAL' => $international_packages, 'DOMESTIC' => $domestic_packages);
	}

    /**
     * Assigns parcel for package
     *
     * @param array $parcel Parcel data
     * @param string $additional_info Order additional info
     */
	public function addParcel($parcel, $additional_info)
	{
		if (!$this->webservice)
			$this->webservice = new DpdPolandPackageWS;

		$this->webservice->addParcel($parcel, $additional_info);
	}

    /**
     * Creates package
     *
     * @return bool Package created successfully
     */
	public function create()
	{
		if (!$this->webservice)
			$this->webservice = new DpdPolandPackageWS;

		return $this->webservice->create($this);
	}

    /**
     * Generates multiple labels for selected packages
     *
     * @param array $waybills Packages waybills
     * @param string $outputDocPageFormat Document page format
     * @param string $session_type Session type (DOMESTIC, INTERNATIONAL)
     * @return bool Multiple labels generated successfully
     */
	public function generateMultipleLabels($waybills, $outputDocPageFormat = 'A4', $session_type = 'INTERNATIONAL')
	{
		if (!$this->webservice)
			$this->webservice = new DpdPolandPackageWS;

		return $this->webservice->generateMultipleLabels($waybills, $outputDocPageFormat, $session_type);
	}

    /**
     * Generates package labels
     *
     * @param string $outputDocFormat Document format
     * @param string $outputDocPageFormat Document page format
     * @param string $policy Policy type
     * @return bool Labels generated successfully
     */
	public function generateLabels($outputDocFormat = 'PDF', $outputDocPageFormat = 'A4', $policy = 'STOP_ON_FIRST_ERROR')
	{
		if (!$this->webservice)
			$this->webservice = new DpdPolandPackageWS;

		return $this->webservice->generateLabels($this, $outputDocFormat, $outputDocPageFormat, $policy);
	}

    /**
     * Generates labels for selected packages
     *
     * @param array $package_ids Packages IDs
     * @param string $outputDocFormat Document format
     * @param string $outputDocPageFormat Document page format
     * @param string $policy Policy type
     * @return bool Labels generated successfully
     */
	public function generateLabelsForMultiplePackages($package_ids, $outputDocFormat = 'PDF', $outputDocPageFormat = 'LBL_PRINTER', $policy = 'STOP_ON_FIRST_ERROR')
	{
		if (!$this->webservice)
			$this->webservice = new DpdPolandPackageWS;

		return $this->webservice->generateLabelsForMultiplePackages($package_ids, $outputDocFormat, $outputDocPageFormat, $policy);
	}

    /**
     * Collects sender address according to package
     *
     * @param int|string $package_number Package number
     * @return array Sender address data
     */
	public function getSenderAddress($package_number)
	{
		if (!$this->webservice)
			$this->webservice = new DpdPolandPackageWS;

		return $this->webservice->getSenderAddress($package_number, $this->id_sender_address);
	}

    /**
     * Collects data about order which has no saved shipments
     *
     * @return array Orders which has no saved shipments
     */
    public static function getLabelExceptions()
    {
        $orders = Db::getInstance()->executeS('
			SELECT `id_order`
			FROM `'._DB_PREFIX_.'orders`
		');

        if (empty($orders)) {
            return array();
        }

        $orders_ids = array();

        foreach ($orders as $order) {
            $orders_ids[] = (int)$order['id_order'];
        }

        $packages = Db::getInstance()->executeS('
			SELECT `id_order`
			FROM `'._DB_PREFIX_._DPDPOLAND_PACKAGE_DB_.'`
		');

        if (empty($packages)) {
            return $orders_ids;
        }

        $package_orders_ids = array();

        foreach ($packages as $package) {
            $package_orders_ids[] = $package['id_order'];
        }

        return array_diff($orders_ids, $package_orders_ids);
    }
}