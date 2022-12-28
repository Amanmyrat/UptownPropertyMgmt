<?php

namespace App\Library\Classes;

use DateTime;

class StaticFunctions
{

    /**
     * Returns max value of last 11 elements
     */
    static function getMaxLast12($array)
    {
        $date1 = '2022-11-01';
        $d1 = new DateTime($date1);
        $d2 = new DateTime();
        $months = $d2->diff($d1);
        $howeverManyMonths = (($months->y) * 12) + ($months->m) + 1;
        $last = $howeverManyMonths > 12 ? 12 : $howeverManyMonths;

        $currentMonth = date('m') + 1;
        
        unset($array[count($array) - 1]);
        unset($array[count($array) - $currentMonth]);

        return max(array_slice($array, -$last));
    }
    /**
     * Beautifies arrays of  integers
     * Ex: 100000 -> 100,000
     */
    static function beautifyNumbers($array)
    {
        for ($x = 0; $x < count($array); $x++) {
            if (!is_string($array[$x]))
                $array[$x] = number_format($array[$x]);
        }
        return $array;
    }
}
