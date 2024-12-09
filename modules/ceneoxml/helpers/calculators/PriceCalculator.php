<?php

class PriceCalculator
{
    public static function CalculateValueWithTax($value, $tax)
    {
        return round($value * ((100 + $tax) / 100), 2);
    }

    public static function CalculateFinalProductPrice($price, $special_price_data, $tax)
    {
        $price_reduction = 0;

        if(!empty($special_price_data))
        {
            if(!is_null($special_price_data['price']))
            {
                $price = $special_price_data['price'];
            }

            if($special_price_data['reduction'] > 0)
            {
                //percent reduction
                if($special_price_data['reduction_type'] == 0)
                {
                    $price_reduction = self::CalculateValueWithTax($price * $special_price_data['reduction'], $tax);
                }
                //value reduction
                else
                {
                    $price_reduction = $special_price_data['reduction_tax'] == 0 ? 
                            self::CalculateValueWithTax($special_price_data['reduction'], $tax) : 
                            $special_price_data['reduction'];
                }
            }
        }	

        return self::CalculateValueWithTax($price, $tax) - $price_reduction;	
    }
}