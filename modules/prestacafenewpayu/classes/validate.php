<?php
/**
 * 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
 *
 * NOTICE OF LICENSE
 *
 * Lukasz Kowalczyk is the owner of the copyright of this module.
 * All rights of any kind, which are not expressly granted in this
 * License, are entirely and exclusively reserved to and by Lukasz
 * Kowalczyk.
 *
 * You may not rent, lease, transfer, modify or create derivative
 * works based on this module.
 *
 * @author    Lukasz Kowalczyk <lkowalczyk@prestacafe.pl>
 * @copyright Lukasz Kowalczyk
 * @license   LICENSE.txt
 */

class PrestaCafePayuValidate
{

    public static function isSurchargePercentage($value)
    {
        return Validate::isUnsignedFloat($value);
    }

    public static function isSurchargeMin($value)
    {
        return Validate::isUnsignedFloat($value);
    }

    public static function isSurchargeMax($value)
    {
        return Validate::isUnsignedFloat($value);
    }
}
