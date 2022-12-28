<?php

namespace App\Library\Classes;

use App\Library\Classes\TimeFunctions;

class RedisKeys
{
    public static function getCollectionReportKey()
    {
        $hourly = TimeFunctions::getDailyTime();
        $key = 'uptownPropertyCollections:' . $hourly;
        return $key;
    }

    public static function getAuthKey()
    {
        $tenMinutly = TimeFunctions::get10MinuteTime();
        $key = "uptownAuthKey:" . $tenMinutly;
        return $key;
    }

    public static function getThisMonthCollection()
    {
        return "uptownThisMonthCollection";
    }

    public static function getVacancyKey()
    {
        $hourly = TimeFunctions::getDailyTime();
        $key = 'uptownVacancyReport:' . $hourly;
        return $key;
    }

    public static function getInventoryTransactionsReport($time = null)
    {
        $hourly = $time ? $time :  TimeFunctions::getDailyTime();
        $key = 'uptownInventoryTransactionsReport:' . $hourly;
        return $key;
    }
    
    public static function getInventoryPhysicalWorksheetReport()
    {
        $daily =  TimeFunctions::getDailyTime();
        $key = 'uptownInventoryPhysicalWorksheetReport:' . $daily;
        return $key;
    }

    public static function getEmployeeWorkReport()
    {
        $hourly = TimeFunctions::getHourlyTime(5);
        $key = 'upwotnEmployeeWorkReport:' . $hourly;
        return $key;
    }

    public static function getFilteredIssuesList()
    {
        $key = 'uptownFilteredIssuesList';
        return $key;
    }

    public static function getTransactionsReport()
    {
        $daily =  TimeFunctions::getDailyTime();
        $key = 'uptownTransactionsReport:' . $daily;
        return $key;
    }

    public static function getUnitAvailability()
    {
        $hourly = TimeFunctions::getDailyTime();
        return "uptownUnitAvailability:" . ":" . $hourly;
    }

    public static function getPropertyReport($propertyID)
    {
        $hourly = TimeFunctions::getDailyTime();
        $key = 'uptownPropertyReport:' . $propertyID . ":" . $hourly;
        return $key;
    }

}
