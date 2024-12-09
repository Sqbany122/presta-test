<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Payload;

class OrderPayload
{
    const SHIPMENT_TYPES = [
        self::COURIER,
        self::ACCESS_POINT,
        self::PARCEL_LOCKER,
        self::PARCEL_LOCKER_RUCH,
        self::CLICK_AND_COLLECT,
    ];

    const COURIER = 0;

    const ACCESS_POINT = 1;

    const PARCEL_LOCKER = 2;

    const PARCEL_LOCKER_RUCH = 3;

    const CLICK_AND_COLLECT = 4;

    private $referenceId;

    private $providerId = null;

    private $description = null;

    private $additionalInfo = null;

    private $amount;

    private $billingAddress;

   
    private $shippingAddress;

    private $shipment = 0;


    public function getReferenceId()
    {
        return $this->referenceId;
    }

    public function setReferenceId(string $referenceId)
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    public function getProviderId()
    {
        return $this->providerId;
    }

    public function setProviderId(string $providerId)
    {
        $this->providerId = $providerId;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

  
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo(array $additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount(int $amount)
    {
        $this->amount = (int) $amount;

        return $this;
    }

    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(AddressPayload $billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(AddressPayload $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getShipment()
    {
        return $this->shipment;
    }

    public function setShipment(int $shipment)
    {
        $this->shipment = $shipment;

        return $this;
    }
}
