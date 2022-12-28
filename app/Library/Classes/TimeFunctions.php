<?php

namespace App\Library\Classes;

use Carbon\Carbon;

class TimeFunctions
{
    static function getThisMonthString()
    {
        $now = Carbon::now()->startOfMonth();

        return $now->localeMonth;
    }
    static function get10MinuteTime()
    {
        $now = Carbon::now();
        return intval($now->minute / 10) . ":" . $now->hour . ":" . $now->month . "." . $now->day . "." . $now->year;
    }

    static function getHourlyTime($hour = 1)
    {
        $now = Carbon::now();
        return intval($now->hour / $hour) . ":" . $now->month . "." . $now->day . "." . $now->year;
    }
    static function getDailyTime()
    {
        $now = Carbon::now();
        return $now->toDateString();
    }
    
    static function getMonthStartEndDates($previousMonth = false, $limitedToToday = true, $isCarbon = false)
    {
        // startDate
        $startDate = Carbon::now()->startOfMonth();
        // endDate
        $endDate = Carbon::now()->endOfMonth();
        
        if (!$limitedToToday) {
            $endDate->day = 1;
        }

        //ifPreviousMonth
        if ($previousMonth) {
            $endDate = new Carbon('last day of last month');
            $startDate->subMonth();
        }

        //if string
        if (!$isCarbon) {
            return [$startDate->month . "/" . $startDate->day . "/" . $startDate->year, $endDate->month . "/" . $endDate->day . "/" . $endDate->year];
        }
        // if carbon
        else {
            return [$startDate, $endDate];
        }
    }

    static function getPreviousMonth($isCarbon = false)
    {
        $now = Carbon::now();
        $now->subMonth()->startOfMonth();
        if (!$isCarbon) {
            return str_replace("-", "/", $now->toDateString());
        }
        return $now;
    }

    static function getStartMonth()
    {
        $start = Carbon::parse("2022-11-01");
        return $start;
    }
    
}
