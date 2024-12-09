<?php

class ProductCodeTypeProvider
{
    public static function GetCodeType($code)
    {
        if(self::IfBloz7($code))
        {
          return 'BLOZ_7';
        }
        elseif(self::IfBloz12($code))
        {
          return 'BLOZ_12';
        }
        elseif(self::IfIsbn($code))
        {
          return 'ISBN';
        }
        else
        {
          return 'EAN';
        } 
    }

    private static function IfIsbn($code)
    {
        return (strlen($code) == 13 && (strrpos($code, "978") === 0 || 
                strrpos($code, "979") === 0)) || strlen($code) == 10;
    }

    private static function IfBloz7($code)
    {
        return strlen($code) == 7;
    }

    private static function IfBloz12($code)
{
        return strlen($code) == 12;
    }
}