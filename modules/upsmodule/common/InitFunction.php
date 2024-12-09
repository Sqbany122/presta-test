<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/Constants.php';

class InitFunction
{
    public function removeCarriers()
    {
        $idReference = Configuration::get('UPS_SHIPING_METHOD_REFERENCE_ID');
        $sql = new DbQuery();
        $sql->select('id_carrier');
        $sql->from('carrier');
        $sql->where('id_reference = ' . (int) $idReference);

        $idCarriers = Db::getInstance()->executeS($sql);

        if (!empty($idCarriers)) {
            foreach ($idCarriers as $id) {
                $this->deleteZoneCarrier($id);
                $this->deleteGroupsCarrier($id);
                $this->deleteDeliveryCarrier($id);
            }

            $this->deletePriceMethod($idCarriers);
        }

        $upsShippingCarrier = Carrier::getCarrierByReference($idReference);
        if ($upsShippingCarrier !== false && $upsShippingCarrier->name != null) {
            $upsShippingCarrier->delete();
        }
    }

    private function deleteZoneCarrier($idCarrier)
    {
        $carrier = new Carrier($idCarrier);

        $zones = Zone::getZones(true);
        foreach ($zones as $z) {
            $carrier->deleteZone($z['id_zone']);
        }
    }

    private function deleteGroupsCarrier($idCarrier)
    {
        $groups = Group::getGroups(true);
        foreach ($groups as $group) {
            $where = 'id_carrier = ' . (int) $idCarrier .
                    ' AND id_group = ' . (int) $group['id_group'];

            Db::getInstance()->delete('carrier_group', $where);
        }
    }

    private function deleteDeliveryCarrier($idCarrier)
    {
        $carrier = new Carrier($idCarrier);
        $carrier->deleteDeliveryPrice('range_weight');
        $carrier->deleteDeliveryPrice('range_price');
    }

    private function deletePriceMethod($idCarriers)
    {
        $strIdCarriers = !empty($idCarriers) ? implode(",", array_map('intval', $idCarriers)) : -1;
        $where = "`id_carrier` IN (" . $strIdCarriers . ")";
        $db = Db::getInstance();
        $db->delete('range_weight', $where);
        $db->delete('range_price', $where);
    }

    public static function disableTab($className)
    {
        $tab = Tab::getInstanceFromClassName($className);
        $tab->active = 0;
        $tab->save();
    }

    public static function enableTab($className)
    {
        $tab = Tab::getInstanceFromClassName($className);
        $tab->active = 1;
        $tab->save();
    }

    public function addTabNone($moduleName, $langId)
    {
        //Fix Version 1.6
        if (!Tab::getIdFromClassName('AdminUpsAccountSuccess')) {
            $tab = new Tab();
            $tab->name[$langId] = 'UPS SHIPPING';

            $tab->class_name = 'AdminUpsAccountSuccess';
            $tab->id_parent = 0;
            $tab->active = 0; // Default tabs be disabled
            $tab->module = $moduleName;
            $tab->add();

            $tab->class_name = 'AdminUpsAddPackageBatch';
            $tab->id_parent = 0;
            $tab->active = 0; // Default tabs be disabled
            $tab->module = $moduleName;
            $tab->add();

            $tab->class_name = 'AdminUpsAddPackage';
            $tab->id_parent = 0;
            $tab->active = 0; // Default tabs be disabled
            $tab->module = $moduleName;
            $tab->add();

            $tab->class_name = 'AdminUpsShowBarcodeImage';
            $tab->id_parent = 0;
            $tab->active = 0; // Default tabs be disabled
            $tab->module = $moduleName;
            $tab->add();
        }
    }
}
