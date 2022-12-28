<?php

namespace App\Library\Services;

use App\Library\Classes\RedisHelper;
use App\Library\Classes\RedisKeys;
use App\Library\Classes\TimeFunctions;
use Exception;
use Illuminate\Support\Facades\Http;

class RentManagerApi
{
    public $token = '';
    public $isAuthorized = false;
    public $baseAddress = 'https://excpm.api.rentmanager.com/';

    function authorizeRentManager()
    {
        // this is to keep authkey in cache and store it for 10minutes
        $authKey = RedisKeys::getAuthKey();
        $key = RedisHelper::get($authKey);
        if ($key == null) {
            $response = Http::post($this->baseAddress . 'Authentication/AuthorizeUser', [
                'Username' => '',
                'Password' => ''
            ]);
            if ($response->status() == 200) {
                $token = str_replace('"', '', $response->body());
                $this->token = $token;
                $this->isAuthorized = true;

                RedisHelper::set($authKey, $token);
            }
        } else {
            $this->token = $key;
            $this->isAuthorized = true;
        }
    }

    /**
     * startDate format is 10/22/2022
     */
    function getReportInvoices($startDate, $endDate, $property_id = -1)
    {
        $url = "Reports/25/RunReport?parameters=StartDate," . $startDate . ";EndDate," . $endDate;
        if ($property_id != -1) {
            $url = $url . ";PropertyIDs," . $property_id;
        }
        try {
            return $this->getReport($url);
        } catch (Exception $x) {
            var_dump($x);
        }
    }

    function getReport($url)
    {
        if (!$this->isAuthorized) {
            $this->authorizeRentManager();
        }
        try {
            $response = Http::withHeaders(['X-RM12Api-ApiToken' => $this->token])->get($this->baseAddress . $url);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return null;
        }

        return $response;
    }

    function getUnitAvailability($property_id = -1)
    {
        $url = "Reports/10/RunReport";
        if ($property_id != -1) {
            $url = $url . "?parameters=PropertyIDs," . $property_id;
        }

        return $this->getReport($url);
    }

    function getChargedBreakdown($startDate, $endDate, $property_id)
    {
        $url = "Reports/29/RunReport?parameters=StartDate," . $startDate . ";EndDate," . $endDate . ";PropOwnerIDs," . $property_id;
        return $this->getReport($url);
    }

    function getFilteredIssueList($property_id, $startDate = null, $endDate = null)
    {
        if ($startDate == null && $endDate == null) {
            $dates = TimeFunctions::getMonthStartEndDates();
            $startDate = $dates[0];
            $endDate = $dates[1];
        }

        $url = "Reports/112/RunReport?parameters=StartDate," . $startDate . ";EndDate," . $endDate . ";PropertyIDs," . $property_id;
        try {
            return $this->getReport($url);
        } catch (Exception $x) {
            var_dump($x);
        }
    }

    function getInventoryHistories($property_id, $startDate = null, $endDate = null)
    {
        $url = "Reports/148/RunReport?parameters=StartDate,";
        $dates = TimeFunctions::getMonthStartEndDates();
        if ($startDate == null) {
            $startDate = $dates[0];
            $endDate = $dates[1];
        }
        $url .= $startDate;

        if ($endDate != null) {
            $url .= ";EndDate," . $endDate;
        }
        $url .= ";PropertyIDs," . $property_id;
        return $this->getReport($url);
    }

    function getPhysicalInventoryWorksheet($property_id)
    {
        $url = "Reports/560/RunReport?parameters=PropertyIDs,". $property_id;
        return $this->getReport($url);
    }

    function getPurchasesOfOrder($property_id, $startDate = null, $endDate = null)
    {
        if ($startDate == null && $endDate == null) {
            $dates = TimeFunctions::getMonthStartEndDates();
            $startDate = $dates[0];
            $endDate = $dates[1];
        }
        $url = "Reports/147/RunReport?parameters=StartDate,$startDate;EndDate,$endDate;PropertyIDs,$property_id";
        return $this->getReport($url);
    }

    function getProperties()
    {
        return $this->getReport("Properties");
    }

    function getUnits()
    {
        return $this->getReport("Units");
    }

    function getUnitsByPropertyId($PropertyId)
    {
        return $this->getReport("Units?filters=PropertyID,eq," . $PropertyId);
    }

    function getUnitTypes()
    {
        return $this->getReport("UnitTypes");
    }

    function getBalanceDues($propertyId)
    {
        $url = "Reports/82/RunReport?parameters=PropertyIDs," . $propertyId;
        return $this->getReport($url);
    }

    function getTenantHistories($startDate, $propertyId)
    {
        $url = "/Reports/18/RunReport" .
            "?parameters=HISTCATEGORYIDS,(12,18,24,33,34,35,36,37,39,41,42,43);HISTTYPEIDS,1;StartDate," . $startDate . ";PropertyIDs," . $propertyId . ";CustomerStatus,2";
        return $this->getReport($url);
    }
}
