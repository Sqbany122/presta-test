<?php

class PrestaShopHelper
{
    public static function GetPSVersionBasePart($full_version)
    {
        $version_parts = explode(".", $full_version);
        
        return $version_parts[0] . (isset($version_parts[1])?  '.' . $version_parts[1] : '');
    }
}