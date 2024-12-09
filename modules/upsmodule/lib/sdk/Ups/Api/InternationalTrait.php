<?php

namespace Ups\Api;
/**
 * Trait for International Shipment
 * Created by UPS
 * Created at 26-08-2018
 */

trait InternationalTrait
{
    public $toCountry;
    public $fromCountry;

    public function setToCountry($country)
    {
        $this->toCountry = $country;
    }

    public function setFromCountry($country)
    {
        $this->fromCountry = $country;
    }

    public function shipFromCountry()
    {
        return $this->fromCountry;
    }

    public function shipToCountry()
    {
        return $this->toCountry;
    }

    public function shipInternational()
    {
        if ($this->shipFromCountry() !== $this->shipToCountry())
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
