<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

namespace Bean;

class Validator
{
    public function validAccName($accName, $key)
    {
        if ($this->isBlank($key, $accName)) {
            return $key;
        }

        if (preg_match('/^[a-zA-Z0-9]$/', $accName)) {
            return $key;
        }
    }

    public function validAccNumber($accNumber, $key)
    {
        if ($this->isBlank($key, $accNumber)) {
            return $key;
        }

        if (!preg_match('/^[A-Za-z0-9]{6}$/', $accNumber)) {
            return $key;
        }
    }

    public function validInvoiceAmount($amount, $key)
    {
        if ($this->isBlank($key, $amount)) {
            return $key;
        }

        if (!preg_match('/^[0-9]+\.?[0-9]*$/', $amount)) {
            return $key;
        }
    }

    public function validInvoiceNumber($number, $key)
    {
        if ($this->isBlank($key, $number)) {
            return $key;
        }

        if (strlen($number) > 15) {
            return $key;
        }
    }

    public function validVatNumber($vat, $key = 'vatNumber')
    {
        if ($vat != '') {
            if (!preg_match('/^[a-zA-Z0-9]{1,15}$/', $vat)) {
                return $key;
            }
        }
    }

    public function validPromoCode($code, $key = 'promoCode')
    {
        if ($code != '') {
            if (!preg_match('/^[a-zA-Z0-9]{1,9}$/', $code)) {
                return $key;
            }
        }
    }

    public function validString($key, $str)
    {
        if ($this->isBlank($key, $str)) {
            return $key;
        }

        if (!preg_match('/^[\p{L}\p{N}\s\,\.\-\/\\\]{1,35}$/u', $str)) {
            return $key;
        }
    }

    public function isBlank($key, $str)
    {
        if ($str === '') {
            return $key;
        }
    }

    public function validEmail($key, $accEmail)
    {
        if ($this->isBlank($key, $accEmail)) {
            return $key;
        }

        if (!preg_match('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/', $accEmail)) {
            return $key;
        }
    }
}
