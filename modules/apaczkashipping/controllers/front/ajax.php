<?php
/**
 * @author    Innovation Software Sp.z.o.o
 * @copyright 2018 Innovation Software Sp.z.o.o
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category  apaczkashipment
 * @package   apaczkashipment
 * @version   1.1
 */

class ApaczkaShippingAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $action = Tools::getValue('action');
        if (! empty($action)
            && method_exists($this, 'ajaxProcess' . Tools::ucfirst(Tools::
                toCamelCase($action)))
        ) {
            return $this->{'ajaxProcess' . Tools::toCamelCase($action)}();
        } elseif (! empty($action)
                  && method_exists($this, 'process' . Tools::ucfirst(Tools::
                toCamelCase($action)))
        ) {
            return $this->{'process' . Tools::toCamelCase($action)}();
        }
    }

    protected function ajaxProcessSaveParcelLockerCodeToOrder()
    {
        $parcel_locker_name = Tools::getValue('parcel_locker_name');

        if (isset($parcel_locker_name) && ! empty($parcel_locker_name)) {
            $this->context->cookie->apaczka_parcel_locker_name
                     = $parcel_locker_name;
            $message = 'Paczkomat ' . $parcel_locker_name . ' został wybrany.';
        } else {
            $message = 'Paczkomat musi posiadać nazwę i kod.';
        }

        die(Tools::jsonEncode(array('message' => $message)));
    }

    protected function ajaxProcessSaveSenderParcelLockerCodeToOrder()
    {
        $parcel_locker_name = Tools::getValue('sender_parcel_locker_name');
        $orderId            = Tools::getValue('order_id');

        if (isset($parcel_locker_name) && ! empty($parcel_locker_name)) {
            $parcelLockerDeliveryObj
                = ParcelLockerDelivery::loadByOrderId($orderId);
            if ($parcelLockerDeliveryObj->id) {
                $parcelLockerDeliveryObj->sender_parcel_locker_code
                    = pSQL($parcel_locker_name);
                if ($parcelLockerDeliveryObj->update()) {
                    $message = 'Paczkomat ' . $parcel_locker_name
                               . ' został wybrany.';
                }
            } else {
                $parcelLockerDeliveryObj->id_order = $orderId;
                $parcelLockerDeliveryObj->sender_parcel_locker_code;
                if ($parcelLockerDeliveryObj->add()) {
                    $message = 'Paczkomat ' . $parcel_locker_name
                               . ' został wybrany.';
                } else {
                    $message = 'Paczkomat musi posiadać nazwę i kod.';
                }
            }
        } else {
            $message = 'Paczkomat musi posiadać nazwę i kod.';
        }

        die(Tools::jsonEncode(array('message' => $message)));
    }

    protected function ajaxProcessUpdateParcelLockerCodeInOrder()
    {
        $parcel_locker_name = Tools::getValue('parcel_locker_name');
        $orderId            = Tools::getValue('order_id');
        $message            = '';
        if (isset($parcel_locker_name) && ! empty($parcel_locker_name)
            && $orderId > 0
        ) {
            $parcelLockerDeliveryObj
                = ParcelLockerDelivery::loadByOrderId($orderId);
            if ($parcelLockerDeliveryObj->id) {
                $parcelLockerDeliveryObj->receiver_parcel_locker_code
                    = pSQL($parcel_locker_name);
                if ($parcelLockerDeliveryObj->update()) {
                    $message = 'Wybór paczkomatu: ' . $parcel_locker_name
                               . ' został zaktualizowany.';
                }
            } else {
                $parcelLockerDeliveryObj->id_order = $orderId;
                $parcelLockerDeliveryObj->receiver_parcel_locker_code;
                if ($parcelLockerDeliveryObj->add()) {
                    $message = 'Paczkomat ' . $parcel_locker_name
                               . ' został wybrany.';
                } else {
                    $message = 'Paczkomat musi posiadać nazwę i kod.';
                }
            }
        } else {
            $message = 'Paczkomat musi posiadać nazwę i kod.';
        }

        die(Tools::jsonEncode(array('message' => $message)));
    }
}
