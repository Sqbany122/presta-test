<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'classes' . DIRECTORY_SEPARATOR . 'AvailabilityEnum.php');

class AvailabilityStatusProvider
{
    public static function GetAvailabilityStatus($quantity, $out_of_stock)
    {
        if($quantity > 0)
        {
            return AvailabilityEnum::Available;
        }
        else if($quantity <= 0 && $out_of_stock == 1)
        {
            return AvailabilityEnum::LackOfInformation;
        }
        
        return AvailabilityEnum::Unavailable;
    }
}