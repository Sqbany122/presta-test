<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Payload;

class AddressPayload
{
    private $street;

    private $building = null;

    private $flat = null;

    private $zip;

    private $city;

    private $county = null;
    private $country = null;

    public function getStreet()
    {
        return $this->street;
    }

    public function setStreet(string $street)
    {
        $value         = mb_strtoupper(trim($street), 'UTF-8');
        $this->street = $value ? (string) $value : $street;

        return $this;
    }

    public function getBuilding()
    {
        return $this->building;
    }

    public function setBuilding(string $building)
    {
        $this->building = is_null($building) ? $building : mb_strtoupper(trim($building), 'UTF-8');

        return $this;
    }

    public function getFlat()
    {
        return $this->flat;
    }

    public function setFlat(string $flat)
    {
        $this->flat = is_null($flat) ? $flat : mb_strtoupper(trim($flat), 'UTF-8');

        return $this;
    }

    public function getZip()
    {
        return $this->zip;
    }

    public function setZip(string $zip)
    {
        $this->zip = $zip;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity(string $city)
    {
        $value         = mb_strtoupper(trim($city), 'UTF-8');
        $this->city = $value ? (string) $value : $city;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(string $country)
    {
        $this->country = is_null($country) ? $country : mb_strtoupper(trim($country), 'UTF-8');

        return $this;
    }

    public function getCounty()
    {
        return $this->county;
    }

    public function setCounty(string $county)
    {
        $this->county = is_null($county) ? $county : mb_strtoupper(trim($county), 'UTF-8');

        return $this;
    }
}
