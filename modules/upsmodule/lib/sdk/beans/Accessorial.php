<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

namespace Bean;

class Accessorial
{
    private $services;
    private $lang;

    public function __construct($lang, $services) {
        $this->lang = $lang;
        $this->services = $services;
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getAccessorialName($accessorial)
    {
        return $accessorial[$this->lang];
    }

    public function getAccessorialNameByKey($key)
    {
        foreach ($this->services as $accessorial) {
            if ($key == $accessorial['key']) {
                return $this->getAccessorialName($accessorial);
            }
        }
    }

    public function getServiceKeyPairs()
    {
        $tmp = array();
        foreach ($this->services as $accessorial) {
            if ($accessorial['show_config']) {
                $tmp[] = array(
                    'id_config' => $accessorial['key'],
                    'name' => $this->getAccessorialName($accessorial)
                );
            }
        }

        return $tmp;
    }

    public function getAccessorialsWithoutCod()
    {
        $list = array();

        foreach ($this->services as $accessorial) {
            $list[$accessorial['key']] = $this->getAccessorialName($accessorial);
        }

        unset($list['UPS_ACSRL_ACCESS_POINT_COD']);
        unset($list['UPS_ACSRL_TO_HOME_COD']);

        return $list;
    }

    public function getAccessorialsWithCod($serviceType)
    {
        
    }

    public function getAccessPointCod()
    {
        return 'UPS_ACSRL_ACCESS_POINT_COD';
    }

    public function getToHomeCod()
    {
        return 'UPS_ACSRL_TO_HOME_COD';
    }

    public function getAccessorialByKey($key)
    {
        foreach ($this->services as $accessorial) {
            if ($accessorial['key'] === $key) {
                return $accessorial;
            }
        }

        return array();
    }
}