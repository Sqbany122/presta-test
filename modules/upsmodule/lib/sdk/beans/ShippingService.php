<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

namespace Bean;

class ShippingService
{
    const TO_HOME = 20;
    private $servicesAp;
    private $servicesAdd;

    public function __construct($servicesAp, $servicesAdd) {
        $this->servicesAp = $servicesAp;
        $this->servicesAdd = $servicesAdd;
    }

    public function getServicesAp()
    {
        return $this->servicesAp;
    }

    public function getServicesAdd()
    {
        return $this->servicesAdd;
    }

    public function getShippingServices()
    {
        return array_merge($this->servicesAp, $this->servicesAdd);
    }

    public function getServiceNameByKey($key)
    {
        $services = $this->getShippingServices();

        foreach ($services as $service) {
            if ($service['key'] == $key) {
                return $service['name'];
            }
        }

        return false;
    }

    public function getServiceInfo($key)
    {
        $services = $this->getShippingServices();

        foreach ($services as $service) {
            if ($service['key'] == $key) {
                return $service;
            }
        }

        return false;
    }

    public function getServicePairs($type = 'all')
    {
        $tmp = array();
        $service = array();

        if ($type == 'AP') {
            $services = $this->getServicesAp();
        } elseif ($type == 'ADD') {
            $services = $this->getServicesAdd();
        } else {
            $services = $this->getShippingServices();
        }
        
        foreach ($services as $service) {
            $tmp[$service['key']] = $service['name'];
        }

        return $tmp;
    }

    public function isShippingToHome($key)
    {
        $service = $this->getServiceInfo($key);
        if ($service) {
            if ($service['serviceType'] == static::TO_HOME) {
                return true;
            } else {
                return false;
            }
        }
    }
}