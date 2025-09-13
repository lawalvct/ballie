<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
        'seventeen', 'eighteen', 'nineteen'
    ];

    private static $tens = [
        '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
    ];

    public static function convert($number)
    {
        if ($number == 0) return 'zero dollars';
        
        $number = number_format($number, 2, '.', '');
        list($dollars, $cents) = explode('.', $number);
        
        $result = self::convertNumber($dollars) . ' dollar' . ($dollars != 1 ? 's' : '');
        
        if ($cents > 0) {
            $result .= ' and ' . self::convertNumber($cents) . ' cent' . ($cents != 1 ? 's' : '');
        }
        
        return $result;
    }

    private static function convertNumber($number)
    {
        if ($number < 20) {
            return self::$ones[$number];
        } elseif ($number < 100) {
            return self::$tens[intval($number / 10)] . (($number % 10 != 0) ? ' ' . self::$ones[$number % 10] : '');
        } elseif ($number < 1000) {
            return self::$ones[intval($number / 100)] . ' hundred' . (($number % 100 != 0) ? ' ' . self::convertNumber($number % 100) : '');
        } elseif ($number < 1000000) {
            return self::convertNumber(intval($number / 1000)) . ' thousand' . (($number % 1000 != 0) ? ' ' . self::convertNumber($number % 1000) : '');
        } else {
            return self::convertNumber(intval($number / 1000000)) . ' million' . (($number % 1000000 != 0) ? ' ' . self::convertNumber($number % 1000000) : '');
        }
    }
}