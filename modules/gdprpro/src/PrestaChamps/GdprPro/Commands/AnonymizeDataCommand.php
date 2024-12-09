<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

namespace PrestaChamps\GdprPro\Commands;

use PrestaChamps\Common\Helpers\NumberFormatter;

/**
 * Class AnonymizeDataCommand
 *
 * @package PrestaChamps\GdprPro\Commands
 */
class AnonymizeDataCommand extends DataRequestCommand
{
    public static $prefixLong  = "Anonymized Client";
    public static $prefixShort = "Ac";

    /**
     * @throws \PrestaShopException
     * @throws \PrestaShopModuleException
     */
    public function execute()
    {
        $this->customer->firstname = self::$prefixShort;
        $this->customer->lastname = self::$prefixLong .
            " " .
            NumberFormatter::alphabetize($this->customer->id);
        $this->customer->email = self::$prefixShort .
            $this->customer->id .
            "@" .
            \Context::getContext()->shop->domain .
            ".com";
        $this->customer->birthday = date('Y-m-d');
        $this->customer->note = self::$prefixLong;
        $this->customer->company = self::$prefixLong;

        if (!$this->customer->update()) {
            throw new \PrestaShopModuleException("Can't save Customer after anonymization");
        }

        foreach ($this->addresses as $address) {
            $address->alias = "anonymizedAddress {$this->customer->id} {$address->id}";
            $address->alias = "anonymizedAddress {$this->customer->id} {$address->id}";
            $address->lastname = self::$prefixShort;
            $address->firstname = self::$prefixLong . " " . NumberFormatter::alphabetize($this->customer->id);
            $address->address1 = "anonymizedAddress {$this->customer->id} {$address->id}";
            $address->address2 = "anonymizedAddress {$this->customer->id} {$address->id}";
            $address->postcode = 0000;
            $address->phone = 0000000000;
            $address->phone_mobile = 0000000000;
            $address->vat_number = null;
            $address->dni = null;
            $address->country = "anonymizedCounty";
            $address->city = "anonymizedCity";
            $address->other = null;
            $address->save();
        }

        foreach ($this->connections as $connection) {
            /**
             * @var $connection \Connection
             */
            $connection->ip_address = ip2long("0.0.0.0");
            $connection->id_guest = (empty($this->customer->id_guest)) ? 1 : $this->customer->id_guest;
            $connection->save();
        }
    }
}
