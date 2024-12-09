<?php
/**
 * @author    Innovation Software Sp.z.o.o
 * @copyright 2018 Innovation Software Sp.z.o.o
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category  apaczkashipment
 * @package   apaczkashipment
 * @version   1.1
 */

class ApaczkaOrder
{

    public $notificationDelivered = array();
    public $notificationException = array();
    public $notificationNew = array();
    public $notificationSent = array();

    // cash on delivery
    public $accountNumber = "";
    public $codAmount = "";

    public $orderPickupType = "SELF";
    public $pickupTimeFrom = "";
    public $pickupTimeTo = "";
    public $pickupDate = "";

    public $sender_parcel_locker;
    public $receiver_parcel_locker;

    public $options = "";

    private $address_receiver = array();
    private $address_sender = array();

    public $referenceNumber = '';
    public $serviceCode = "";
    public $isDomestic = "true";
    public $contents = "";

    public $shipments = array();

    private static $dictServiceCode
        = array(
            'UPS_Z_STANDARD',
            'UPS_K_STANDARD',
            'UPS_K_EX_SAV',
            'UPS_Z_EX_SAV',
            'DPD_CLASSIC',
            'DHLSTD',
            'DPD_CLASSIC_FOREIGN',
            'TNT_Z',
            'POCZTA_POLSKA_E24',
            'DHL12',
            'KEX_EXPRESS',
            'FEDEX',
            'PACZKOMAT',
            'APACZKA_DE',
            'INPOST'
        );
    private static $dictOrderPickupType
        = array(
            'COURIER',
            'SELF',
            'EVERYDAY',
            'PHONE',
            'BOX_MACHINE'
        );
    private static $dictOrderOptions
        = array(
            'POBRANIE',
            'ZWROT_DOK',
            'DOR_OSOBA_PRYW',
            'DOST_SOB',
            'PODPIS_DOROS'
        );

    public function __construct()
    {
        $this->notificationDelivered = $this->emptyNotification();
        $this->notificationException = $this->emptyNotification();
        $this->notificationNew       = $this->emptyNotification();
        $this->notificationSent      = $this->emptyNotification();
    }

    public function setPobranie($accountNumber, $codAmount)
    {
        $this->accountNumber = $accountNumber;
        $this->codAmount     = $codAmount;
        $this->addOrderOption('POBRANIE');
    }


    public function createNotification(
        $isReceiverEmail,
        $isReceiverSms,
        $isSenderEmail,
        $isSenderSms
    ) {
        $notification                    = array();
        $notification['isReceiverEmail'] = $isReceiverEmail;
        $notification['isReceiverSms']   = $isReceiverSms;
        $notification['isSenderEmail']   = $isSenderEmail;
        $notification['isSenderSms']     = $isSenderSms;

        return $notification;
    }

    public function emptyNotification()
    {
        $notification                    = array();
        $notification['isReceiverEmail'] = '';
        $notification['isReceiverSms']   = '';
        $notification['isSenderEmail']   = '';
        $notification['isSenderSms']     = '';

        return $notification;
    }

    public function setPickup(
        $orderPickupType,
        $pickupTimeFrom,
        $pickupTimeTo,
        $pickupDate
    ) {
        if (! in_array($orderPickupType, self::$dictOrderPickupType)) {
            throw new Exception('UNSUPPORTED order pickup type: ['
                                . $orderPickupType . '] must be one of: '
                                . print_r(self::$dictOrderPickupType, 1));
        }

        $this->orderPickupType = $orderPickupType;
        if ($orderPickupType == 'COURIER') {
            $this->pickupTimeFrom = $pickupTimeFrom;
            $this->pickupDate     = $pickupDate;
            $this->pickupTimeTo   = $pickupTimeTo;
        }
    }

    public function setServiceCode($serviceCode)
    {
        if (! in_array($serviceCode, self::$dictServiceCode)) {
            throw new Exception('UNSUPPORTED service code: [' . $serviceCode
                                . '] must be one of: '
                                . print_r(self::$dictServiceCode, 1));
        }

        $this->serviceCode = $serviceCode;
    }

    public function addOrderOption($option)
    {
        if (! in_array($option, self::$dictOrderOptions)) {
            throw new Exception('UNSUPPORTED order option: [' . $option
                                . '] must be one of: '
                                . print_r(self::$dictOrderOptions, 1));
        }

        if ($this->options == "") {
            $this->options = array('string' => $option);
        } elseif (! is_array($this->options['string'])) {
            $tmp_option = $this->options['string'];

            if ($tmp_option != $option) {
                $this->options['string'] = array($tmp_option, $option);
            }
        } else {
            if (in_array($option, self::$dictOrderOptions)) {
                $this->options['string'][] = $option;
            }
        }
    }

    public function setReceiverAddress(
        $name = '',
        $contactName = '',
        $addressLine1 = '',
        $addressLine2 = '',
        $city = '',
        $countryId = '',
        $postalCode = '',
        $stateCode = '',
        $email = '',
        $phone = ''
    ) {
        $this->address_receiver = $this->createAddress(
            $name,
            $contactName,
            $addressLine1,
            $addressLine2,
            $city,
            $countryId,
            $postalCode,
            $stateCode,
            $email,
            $phone
        );
    }

    public function setSenderAddress(
        $name = '',
        $contactName = '',
        $addressLine1 = '',
        $addressLine2 = '',
        $city = '',
        $countryId = '',
        $postalCode = '',
        $stateCode = '',
        $email = '',
        $phone = ''
    ) {
        $this->address_sender = $this->createAddress(
            $name,
            $contactName,
            $addressLine1,
            $addressLine2,
            $city,
            $countryId,
            $postalCode,
            $stateCode,
            $email,
            $phone
        );
    }

    public function createAddress(
        $name = '',
        $contactName = '',
        $addressLine1 = '',
        $addressLine2 = '',
        $city = '',
        $countryId = '',
        $postalCode = '',
        $stateCode = '',
        $email = '',
        $phone = ''
    ) {

        $address                = array();
        $address['name']        = Tools::substr($name, 0, 50);
        $address['contactName'] = $contactName;

        $address['addressLine1'] = $addressLine1;
        $address['addressLine2'] = $addressLine2;
        $address['city']         = $city;
        $address['countryId']    = $countryId;
        $address['postalCode']   = $postalCode;

        if ($stateCode != '') {
            $address['stateCode'] = $stateCode;
        }

        $address['email'] = $email;
        $address['phone'] = $phone;

        return $address;
    }

    public function addShipment(ApaczkaOrderShipment $shipment)
    {
        $this->shipments[] = $shipment;

        return;

//        if ($this->shipments == "") {
//            $this->shipments[] = $shipment;
//        } elseif (is_array($this->shipments) && count($this->shipments) == 1) {
//            $tmp = $this->shipments;
//
//            $this->shipments   = array();
//            $this->shipments[] = $tmp;
//            $this->shipments[] = $shipment;
//        } else {
//            $this->shipments[] = $shipment;
//        }
    }

    public function createShipment()
    {
        $return   = array();
        $position = 0;
        $t_tmp    = $this->shipments;

        if (! is_array($t_tmp)) {
            $t_tmp = array($t_tmp);
        }

        foreach ($t_tmp as $a) {
            $ship                     = array();
            $ship['dimension1']       = $a->dimension1;
            $ship['dimension2']       = $a->dimension2;
            $ship['dimension3']       = $a->dimension3;
            $ship['weight']           = $a->weight;
            $ship['shipmentTypeCode'] = $a->getShipmentTypeCode();
            $ship['position']         = $position;

            if ($a->getShipmentValue() > 0) {
                $ship['shipmentValue'] = $a->getShipmentValue();
            }

            $ship['options'] = $a->getOptions();

            $return[] = $ship;

            $position++;
        }

        if ($position === 1) {
            return array('Shipment' => $ship);
        }

        return array('Shipment' => $return);
    }

    public function getOrder()
    {
        $order = array();

        if (! ($this->accountNumber == "" || $this->codAmount == "")) {
            $order['accountNumber'] = $this->accountNumber;
            $order['codAmount']     = $this->codAmount;
        }

        $order['notificationDelivered'] = $this->notificationDelivered;
        $order['notificationException'] = $this->notificationException;
        $order['notificationNew']       = $this->notificationNew;
        $order['notificationSent']      = $this->notificationSent;

        $order['orderPickupType'] = $this->orderPickupType;

        if ($this->pickupTimeFrom != '' and $this->pickupTimeTo != '') {
            $order['pickupTimeFrom'] = $this->pickupTimeFrom;
            $order['pickupTimeTo']   = $this->pickupTimeTo;
            $order['pickupDate']     = $this->pickupDate;
        }

        $order['options'] = $this->options;

        $order['serviceCode']     = $this->serviceCode;
        $order['referenceNumber'] = $this->referenceNumber;
        $order['isDomestic']      = $this->isDomestic;
        $order['contents']        = $this->contents;

        $order['receiver'] = $this->address_receiver;
        $order['sender']   = $this->address_sender;

        $order['shipments'] = $this->createShipment();

        return $order;
    }
}
