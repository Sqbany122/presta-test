<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

namespace Bean;

class UPSCountry
{
    private $lang;
    private $countries = array();

    function __construct($lang, $countries) {
        $this->lang = $lang;
        $this->countries = $countries;
    }

    function getCountries()
    {
        return $this->countries;
    }

    function getCountriesPair()
    {
        $countries = array();

        foreach ($this->countries as $key => $country) {
            $countries[$key] = $country[$this->lang];
        }

        return $countries;
    }

    function getCountryNameByIso($countryCode)
    {
        $isSupportedCountry = array_key_exists($countryCode, $this->getCountries());

        if ($isSupportedCountry) {
            $nameTranslate = $this->getCountries()[$countryCode];
            return $nameTranslate[$this->lang];
        } else {
            return '';
        }
    }
}