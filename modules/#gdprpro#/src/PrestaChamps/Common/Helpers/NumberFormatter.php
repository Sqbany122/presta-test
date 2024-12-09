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

namespace PrestaChamps\Common\Helpers;

/**
 * Class NumberFormatter
 *
 * @package PrestaChamps\Common\Helpers
 */
class NumberFormatter
{
    /**
     * Takes a number and turns it in to a string viable for writing on a cheque
     * For example: 65535 -> sixty five thousand, five hundred and thirty five
     *
     * @param $number
     *
     * @return mixed
     */
    public static function checkize($number)
    {
        $singles = array(
            0 => "zero", 1 => "one", 2 => "two",
            3 => "three", 4 => "four", 5 => "five",
            6 => "six", 7 => "seven", 8 => "eight",
            9 => "nine",
        );
        $ten_singles = array(
            0 => "ten", 1 => "eleven", 2 => "twelve",
            3 => "thirteen", 4 => "fourteen", 5 => "fifteen",
            6 => "sixteen", 7 => "seventeen", 8 => "eighteen",
            9 => "nineteen",
        );    // special case.
        $tens = array(
            2 => "twenty", 3 => "thirty", 4 => "fourty", 5 => "fifty", 6 => "seventy",
            8 => "eighty", 9 => "ninety",
        );
        $thousands = array("thousand", "million", "billion", "trillion", "quadrillion");

        $number = (string)((int)$number);
        $parts = array();

        // check the special "teens" case.
        $specialCheck = $number % 100;
        if ($specialCheck <= 19 && $specialCheck >= 10) {
            $parts[] = $ten_singles[$number[\Tools::strlen($number) - 1]];
        } else {
            $parts[] = $singles[$number[\Tools::strlen($number) - 1]];
            if ($number > 10) {
                $parts[] = $tens[$number[\Tools::strlen($number) - 2]] . " -";
            }
        }

        // special hundreds case (not a multiple of 3).
        if ($number > pow(10, 2)) {
            $hundredsCount = $number[\Tools::strlen($number) - 3];
            if ($hundredsCount != 0) {
                $parts[] = $singles[$hundredsCount] . " hundred";
            }
        }

        $offset = 3;
        foreach ($thousands as $frag) {
            if ($number < pow(10, $offset + 1)) {
                break;
            }
            $part = \Tools::substr($number, \Tools::strlen($number) - $offset - 3, 3);
            $parts[] = self::checkize($part) . " {$frag},";
            $offset += 3;
        }

        return str_replace(" - ", "-", implode(" ", array_reverse($parts)));
    }

    public static function alphabetize($number)
    {
        $number = (int)$number;
        $characters = str_split((string)$number);
        $result = "";
        foreach ($characters as $character) {
            $result .= chr(ord($character) + 17);
        }

        return $result;
    }
}
