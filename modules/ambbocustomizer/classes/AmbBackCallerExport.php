<?php
/**
 *   ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      classes/AmbBackCaller.php
 *    @subject   Contains all specific callbacks
 *
 *            Support by mail: support@ambris.com
 */

class AmbBackCallerExport
{
    public static function errorUnknownExportParam($val, $line)
    {
        $val . $line;
        return __METHOD__;
    }

    public static function simpleValue($value, $line)
    {
        $key = AmbBackCaller::extractFieldName($value);
        $val = self::extractFieldValue($value);

        $value = isset($val) ? $val : "---";

        return $value;
    }

    public static function displayAddress($value, $line)
    {
        $key = AmbBackCaller::extractFieldName($value);

        $id = isset($line[$key . '_address_id']) ? $line[$key . '_address_id'] : "";

        $address = new Address($id);
        return AddressFormat::generateAddress($address, array('avoid' => array('phone', 'phone_mobile')), "\n");
    }

    public static function multipleLinks($value, $line)
    {
        //Expects :
        // _value
        // _url
        $key = self::extractFieldName($value);
        $val = self::extractFieldValue($value);

        $values = isset($val) ? explode(',', $val) : "";

        $return = '';

        for ($i = 0, $nb = count($values); $i < $nb; $i++) {
            $return .= $values[$i];
            $return .= "\n";
        }

        //Value HAS to be set in the select at the same value as the field identifier
        return $return;
    }

    public static function extractFieldName($value)
    {
        return AmbBackCaller::extractFieldName($value);
    }

    public static function extractFieldValue($value)
    {
        return AmbBackCaller::extractFieldValue($value);
    }
}
