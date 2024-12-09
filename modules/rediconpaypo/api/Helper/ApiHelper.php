<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Helper;

// use PrestaShop\Module\Rediconpaypo\Payload\AddressPayload;
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Payload/AddressPayload.php";

class ApiHelper
{
    private static $UPPER_FROM = ['ß', 'ﬀ', 'ﬁ', 'ﬂ', 'ﬃ', 'ﬄ', 'ﬅ', 'ﬆ', 'և', 'ﬓ', 'ﬔ', 'ﬕ', 'ﬖ', 'ﬗ', 'ŉ', 'ΐ', 'ΰ', 'ǰ', 'ẖ', 'ẗ', 'ẘ', 'ẙ', 'ẚ', 'ὐ', 'ὒ', 'ὔ', 'ὖ', 'ᾶ', 'ῆ', 'ῒ', 'ΐ', 'ῖ', 'ῗ', 'ῢ', 'ΰ', 'ῤ', 'ῦ', 'ῧ', 'ῶ'];
    private static $UPPER_TO = ['SS', 'FF', 'FI', 'FL', 'FFI', 'FFL', 'ST', 'ST', 'ԵՒ', 'ՄՆ', 'ՄԵ', 'ՄԻ', 'ՎՆ', 'ՄԽ', 'ʼN', 'Ϊ́', 'Ϋ́', 'J̌', 'H̱', 'T̈', 'W̊', 'Y̊', 'Aʾ', 'Υ̓', 'Υ̓̀', 'Υ̓́', 'Υ̓͂', 'Α͂', 'Η͂', 'Ϊ̀', 'Ϊ́', 'Ι͂', 'Ϊ͂', 'Ϋ̀', 'Ϋ́', 'Ρ̓', 'Υ͂', 'Ϋ͂', 'Ω͂'];

    public static function upper($string)
    {
        $string = mb_strtoupper($string, 'UTF-8');

        if (\PHP_VERSION_ID < 70300) {
            $string = str_replace(self::$UPPER_FROM, self::$UPPER_TO, $string);
        }

        return $string;
    }

    public static function formatPhone(string $phone)
    {
        $phone = preg_replace("/[^\+0-9]/u", '', $phone);

        preg_match("/(^[\+0-9]{3})?([0-9]{9})$/", $phone, $matches);

        return isset($matches[2]) ? $matches[2] : false;
    }

    public static function uuid($serverID = 1)
    {
        $t = explode(" ", microtime());
        return sprintf(
            '%08s-%08s-%08s-%04s-%04x%04x',
            $serverID,
            self::clientIPToHex(),
            \Tools::substr("00000000" . dechex($t[1]), -8),
            \Tools::substr("0000" . dechex(round($t[0] * 65536)), -4),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public static function clientIPToHex($ip = "")
    {
        $hex = "";
        if ($ip == "") {
            $ip = getEnv("REMOTE_ADDR");
        }
        $part = explode('.', $ip);
        for ($i = 0; $i <= count($part) - 1; $i++) {
            $hex .= \Tools::substr("0" . dechex($part[$i]), -2);
        }
        return $hex;
    }

    public static function addressPayload(\Address $address)
    {
        $addressPayload = new AddressPayload();
        $addressPayload->setStreet($address->address1)
            ->setBuilding($address->address2)
            ->setFlat($address->address2)
            ->setZip($address->postcode)
            ->setCity($address->city)
            ->setCountry(\Country::getIsoById($address->id_country)); // dodane w wersji PL/RO

        if (\Country::getIsoById($address->id_country) === "RO") {
            $state = new \State($address->id_state);
            $addressPayload->setCounty($state->name);
        }

        return $addressPayload;
    }

    public static function toArray($object)
    {
        $reflectionClass = new \ReflectionClass($object);

        $properties = $reflectionClass->getProperties();

        $array = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            if (is_object($value)) {
                $array[$property->getName()] = self::toArray($value);
            } else {
                $array[$property->getName()] = $value;
            }
        }
        return $array;
    }

    public static function toJson($object)
    {
        return json_encode(self::toArray($object), true);
    }

    public static function logsDb($date = null, $limit = 500)
    {
        if (!Validate::isDate($date)) {
            return '';
        }

        $between = " BETWEEN '" . bqSQL($date) . " 00:00:00' AND '" . bqSQL($date) . " 23:59:59'";

        $total = Db::getInstance()->getValue("SELECT COUNT(*) FROM `" . _DB_PREFIX_ . "paypo_logs` WHERE date_add $between");

        $pages = ceil($total / $limit);

        for ($i = 0; $i < $pages; $i++) {
            $_limit = " LIMIT $i, $limit";
            if ($rows = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "paypo_logs` WHERE date_add $between $_limit")) {

                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="LOGS - ' . time() . '.csv"');
                if ($i === 0) {
                    echo implode(';', array_keys($rows[0])) . "\n";
                }

                foreach ($rows as $row) {
                    echo implode(';', $row) . "\n";
                }
            }
        }

        exit();
    }

}
