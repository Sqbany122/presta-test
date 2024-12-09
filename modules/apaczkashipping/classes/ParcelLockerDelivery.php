<?php
/**
 * @author    Innovation Software Sp.z.o.o
 * @copyright 2018 Innovation Software Sp.z.o.o
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category  apaczkashipment
 * @package   apaczkashipment
 * @version   1.1
 */

class ParcelLockerDelivery extends ObjectModel
{
    public $id;

    public $id_apaczka_parcel_locker_delivery;

    public $id_order;

    public $receiver_parcel_locker_code;

    public $sender_parcel_locker_code;

    public $date_upd;

    public static $definition
        = array(
            'table'   => 'apaczka_parcel_locker_delivery',
            'primary' => 'id_apaczka_parcel_locker_delivery',
            'fields'  => array(
                'id_order'                    => array(
                    'type'      => self::TYPE_INT,
                    'validate'  => 'isNullOrUnsignedId',
                    'copy_post' => false
                ),
                'receiver_parcel_locker_code' => array(
                    'type'      => self::TYPE_STRING,
                    'validate'  => 'isCleanHtml',
                    'copy_post' => false
                ),
                'sender_parcel_locker_code'   => array(
                    'type'      => self::TYPE_STRING,
                    'validate'  => 'isCleanHtml',
                    'copy_post' => false
                ),
                'date_upd'                    => array(
                    'type'     => self::TYPE_DATE,
                    'validate' => 'isDateFormat'
                ),
            ),
        );

    public static function loadByOrderId($id_order)
    {
        $collection = new Collection('ParcelLockerDelivery');
        $collection->where('id_order', '=', (int)$id_order);
        if ($collection->getFirst()) {
            return $collection->getFirst();
        } else {
            return new self();
        }
    }
}
