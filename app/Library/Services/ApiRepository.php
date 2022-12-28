<?php

namespace App\Library\Services;

use App\Library\Classes\RedisHelper;
use App\Library\Classes\RedisKeys;
use App\Library\Classes\TimeFunctions;
use App\Library\Reports\EmployeeWorkOrdersReport;
use App\Library\Reports\InventoryPhysicalWorksheetReport;
use App\Library\Reports\InventoryTransactionsReport;
use App\Library\Reports\PropertyCollectionReport;
use App\Library\Reports\PropertyReport;
use App\Library\Reports\TransactionsReport;
use App\Library\Reports\VacancyReport;
use App\Models\Property;
use App\Models\PropertyRentCollection;
use App\Models\ReportInvoice;

class ApiRepository
{
    public $api;

    private $property_id = 52;

    function __construct(RentManagerApi $api)
    {
        $this->api = $api;
    }

    function getJsonReportCollections(PropertyCollectionReport $report)
    {
        $collectionKey = RedisKeys::GetCollectionReportKey();
        $response = RedisHelper::get($collectionKey);

        if ($response != null) {
            return $response;
        }

        $response = $report->getReport($this->property_id);

        RedisHelper::set($collectionKey, $response);
        return $response;
    }

    function setPropertyCollections()
    {
        $properties = $this->getProperties();
        foreach ($properties as $property) {
            $dated = ReportInvoice::orderByDesc('InvoiceDate')->first();
            $reports = ReportInvoice::where('EntitiesEntityID', $property->property_id)->where('InvoiceDate', $dated->InvoiceDate)->get();
            $reportCollection = new PropertyRentCollection();
            $reportCollection->property_id = $property->property_id;
            $reportCollection->date = $dated->InvoiceDate;
            $reportCollection->total = $reports->sum('Total');
            $reportCollection->save();
        }

    }

    function getVacancyReport(VacancyReport $report)
    {
        $collectionKey = RedisKeys::getVacancyKey();
        $response = RedisHelper::get($collectionKey);
        if ($response != null) {
            return $response;
        }

        $response = $report->getReport($this->property_id);

        RedisHelper::set($collectionKey, $response);
        return $response;
    }

    function getThisMonthInvoiceReports()
    {
        //this month dates
        $months = TimeFunctions::getMonthStartEndDates();
  
        $invoiceResponse = $this->api->getReportInvoices($months[0], $months[1], $this->property_id);
        
        $jsonArray = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $invoiceResponse), true);

        $thisMonthsStuff = $jsonArray["Grid1"];

        $thisMonthsStuff = collect($thisMonthsStuff);
        RedisHelper::set(RedisKeys::getThisMonthCollection(), $thisMonthsStuff);
        return $thisMonthsStuff;
    }

    function getLastTwoMonthsInvoiceReports()
    {
        $thisMonthsStuff = $this->getThisMonthInvoiceReports($this->property_id);
        // previous months dates
        $prevMonth = TimeFunctions::getMonthStartEndDates(true, false);

        $invoiceResponse = $this->api->getReportInvoices($prevMonth[0], $prevMonth[1], $this->property_id);
        if ($invoiceResponse != null) {
            $jsonArray = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $invoiceResponse), true);
            $previousMonthsStuff = $jsonArray["Grid1"];
            $previousMonthsStuff = collect($previousMonthsStuff);
        }
        return [$thisMonthsStuff, $previousMonthsStuff ?? null];
    }

    function getInventoryTransactionsReport(InventoryTransactionsReport $report)
    {
        $collectionKey = RedisKeys::GetInventoryTransactionsReport();
        $response = RedisHelper::get($collectionKey);
        if ($response != null) {
            return $response;
        }

        $response = $report->getReport($this->property_id);

        RedisHelper::set($collectionKey, $response);
        return $response;
    }

    function getInventoryPhysicalWorksheet(InventoryPhysicalWorksheetReport $report)
    {
        $key = RedisKeys::GetInventoryPhysicalWorksheetReport();
        $response = RedisHelper::get($key);
        if ($response != null) {
            return $response;
        }
        $response = $report->getReport($this->property_id);
        RedisHelper::set($key, $response);

        return $response;
    }

    function getProperties($isScheduled = false)
    {
        $unitTypes = Property::all();
        if ($unitTypes != null && count($unitTypes) > 0 && !$isScheduled) {
            return $unitTypes;
        } else {
            $unitTypes = $this->api->getProperties();
            if (strlen($unitTypes) > 0) {
                $jsonArray = json_decode($unitTypes, true);
                foreach ($jsonArray as  $jsonProperty) {
                    $property = new Property();
                    $property->fill($jsonProperty);
                    if (Property::where("property_id",$property->property_id) == null) {
                        $property->save();
                    }
                }
                return Property::all();
            }
        }
    }

    function getEmployeeWorkOrders(EmployeeWorkOrdersReport $report)
    {
        $key = RedisKeys::getEmployeeWorkReport();
        $response = RedisHelper::get($key);

        if ($response != null) {
            return $response;
        }

        $response = $report->getReport($this->property_id);
        
        RedisHelper::set($key, $response);

        return $response;
    }

    function getTransactionsReport(TransactionsReport $report)
    {
        $collectionKey = RedisKeys::getTransactionsReport();
        $response = RedisHelper::get($collectionKey);

        if ($response != null) {
            return $this->sortByDate($response);
        }

        $response = $report->getTransactionsReport();

        RedisHelper::set($collectionKey, $response);

        return $this->sortByDate($response);
    }

    function getPropertyReport(PropertyReport $report)
    {
        $collectionKey = RedisKeys::getPropertyReport($this->property_id);

        $response = RedisHelper::get($collectionKey);
        if ($response != null) {
            return $response;
        }

        $response = $report->getReport($this->property_id);

        RedisHelper::set($collectionKey, $response);
        return $response;
    }

    function getBalanceDues()
    {
        $response = $this->api->getBalanceDues($this->property_id);
        $response = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
        $response = $response["Grid1"];
        return collect($response);
    }

    function getTenantHistories()
    {
        $months = TimeFunctions::getMonthStartEndDates(false);
        $response = $this->api->getTenantHistories($months[0], $this->property_id);
        $response = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
        $response = $response["Grid1"];
        return collect($response);
    }

    public function sortByDate($response)
    {
        // sort array keys
        uksort($response[0], function ($dt1, $dt2) {
            return strtotime($dt1) - strtotime($dt2);
        });

        return $response;
    }

}
