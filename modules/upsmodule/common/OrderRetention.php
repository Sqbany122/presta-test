<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

class OrderRetention
{
    public function unArchivedOrders($list_order)
    {
        $sql = "UPDATE `" . UPS_OPENORDER . "`
                SET `" . STATUS_COL ."` = " . Constants::STATUS_OPEN_ORDER . " 
                WHERE `id_order` IN ({$list_order})";

        return Db::getInstance()->execute($sql);
    }

    public function archivedOrderMoreThan90Days()
    {
        $sql = "UPDATE `" . UPS_OPENORDER . "`
                SET `" . STATUS_COL ."` = " . Constants::STATUS_ARCHIVED_ORDERS . ", `archived_at` = CURRENT_TIMESTAMP".
                " WHERE `" . STATUS_COL ."` = " . Constants::STATUS_OPEN_ORDER
                . " AND TIMESTAMPDIFF(". 'DAY' .", `created_at`, CURRENT_TIMESTAMP) > " . Constants::TO_BE_ARCHIVED;

        return Db::getInstance()->execute($sql);
    }

    public function deleletOrderMoreThan90Days()
    {
        $sql = new DbQuery();
        $sql->type('DELETE');
        $sql->from(Constants::DB_TABLE_OPENORDER);
        $sql->where(STATUS_COL . " = " . Constants::STATUS_ARCHIVED_ORDERS
            . " AND TIMESTAMPDIFF(" . 'DAY' . ", `archived_at`, CURRENT_TIMESTAMP) > " . Constants::TO_BE_DELETED);

        return Db::getInstance()->execute($sql);
    }

    public function delteShipmentMoreThan90Days()
    {
        $sql = new DbQuery();
        $sql->type('DELETE');
        $sql->from(Constants::DB_TABLE_SHIPMENT);
        $sql->where("TIMESTAMPDIFF(" . 'DAY' . ", `create_date`, CURRENT_TIMESTAMP) > " . Constants::TO_BE_DELETED);

        return Db::getInstance()->execute($sql);
    }
}
