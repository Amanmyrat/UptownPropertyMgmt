<?php

namespace App\Library\Reports;

use App\Library\Classes\StaticFunctions;
use App\Library\Classes\TimeFunctions;

use App\Library\Services\ApiRepository;
use App\Library\Services\RentManagerApi;
use Carbon\Carbon;
use App\Models\ChargedBreakdown;
use App\Models\Property;
use App\Models\PropertyRentCollection;
use App\Models\ReportInvoice;
use Illuminate\Support\Facades\Redis;

class PropertyCollectionReport
{
    public $api;
    public $repo;
    function __construct(RentManagerApi $api, ApiRepository $repo)
    {
        $this->api = $api;
        $this->repo = $repo;
    }

    function getReport($property_id)
    {
        $startMonth = TimeFunctions::getStartMonth(true);
        $prevMonth = TimeFunctions::getPreviousMonth(true);
         
        $dated = ReportInvoice::orderByDesc('InvoiceDate')->first();
 
        if($dated == null){
            if($prevMonth >= $startMonth)
                $this->setLastMonthCollection($property_id);
        }else{
            $carbon = Carbon::parse($dated->InvoiceDate);
            if ($carbon->month != $prevMonth->month) {
                $this->setLastMonthCollection($property_id);
            }
        }
        
        return $this->getPropertyCollectionsReport($property_id);

    }

    function getPropertyCollectionsReport($property_id)
    {
        $lastTwoMonths = $this->repo->getLastTwoMonthsInvoiceReports($property_id);

        $thisMonth = $lastTwoMonths[0];
        
        $previousMonth = $lastTwoMonths[1];
        $propertyCollections = PropertyRentCollection::get();
        
        $dates = PropertyRentCollection::select('Date')->distinct()->get();
        $datas = array();

        $isColumnsSet = false;
        
        $columns = array();
        array_push($columns, 'No');
        array_push($columns, 'PropertyName');

        $number = 1;

        $monthColumn = 0;
        $singlePropertyDatas = array();
 
        $singlePropertyDatas = $this->getPropertyRow($property_id, $propertyCollections, $number, $dates, $thisMonth, $previousMonth, $monthColumn, $isColumnsSet, $columns);

        $today = Carbon::now();

        if ($today->day == 1) {
            $property = Property::where("property_id", $property_id)->get()->first();
            $current_rent = $property->current_rent != null ? json_decode($property->current_rent, true) : [];
            $vacancy_missing = $property->vacancy_missing != null ? json_decode($property->vacancy_missing, true) : [];

            if ($current_rent == null || Carbon::parse(end($current_rent)["date"])->month < $today->month) {
                $today_current_rent = array_slice(StaticFunctions::BeautifyNumbers($singlePropertyDatas), -3)[0];
                array_push($current_rent, [
                    "date" => date("Y-m-d"),
                    "price" => str_replace(',', ' ', $today_current_rent)
                ]);
            }
            if ($vacancy_missing == null || Carbon::parse(end($vacancy_missing)["date"])->month < $today->month) {
                $today_vacancy_missing = StaticFunctions::BeautifyNumbers($singlePropertyDatas)[count($singlePropertyDatas) - 1];
                array_push($vacancy_missing, [
                    "date" => date("Y-m-d"),
                    "price" => str_replace(',', ' ', $today_vacancy_missing)
                ]);
            }

            $property->current_rent = $current_rent;
            $property->vacancy_missing = $vacancy_missing;
            $property->save();
        }

        $singlePropertyDatas = StaticFunctions::BeautifyNumbers($singlePropertyDatas);
        array_push($datas, $singlePropertyDatas);

        // 12 months max collection -- icinden
        // max collection increase 5 --icinden
        // total sq feet --suwagtlykca el bilen goymaly
        // total charged rent to current tenant  - su meseleli
        // total uncollected rent - total chargedrent - last month rent
        // total vacancy missing - total vacant sum
        array_push($columns, "Prev Month Today");
        array_push($columns, "12 Months Max Collection");
        array_push($columns, "Max Collection 5% Increase");
        array_push($columns, "Total SQ Feet");
        array_push($columns, "Total Charged Rent To Current Tenant");
        array_push($columns, "Total Uncollected Rent");
        array_push($columns, "Total Vacancy Missing");

        return array($datas, $columns);
    }

    private function getPropertyRow($property_id, $propertyCollections, &$number, $dates, $thisMonth, $previousMonth, &$monthColumn, &$isColumnsSet, &$columns)
    {
        $singlePropertyDatas = array();

        $property = Property::where("property_id", $property_id)->get()->first();
        $singlePropertyCollections = $propertyCollections->where('property_id', $property_id);

        array_push($singlePropertyDatas, $number);
        array_push($singlePropertyDatas, $property->shortname);
        for ($i = 0; $i < $monthColumn - count($singlePropertyCollections); $i++) {
            array_push($singlePropertyDatas, 0);
        }
        for ($i = 0; $i < intval(($monthColumn - count($singlePropertyCollections)) / 12); $i++) {
            array_push($singlePropertyDatas, 0);
        }

        // each one is new month

        foreach ($singlePropertyCollections as $key => $collections) {
            $date = Carbon::parse($collections->date);
            if (!$isColumnsSet) {
                $monthColumn++;
                if ($date->month == 1 && $date->year != 2022) {
                    array_push($columns, "Total " . ($date->year - 1));
                }
                array_push($columns, $date->localeMonth . " " . Carbon::parse($collections->date)->year);
            }
            if ($date->month == 1 && $date->year != 2022) {
                $from = $date->copy();
                $from->year -= 1;
                $from->startOfYear();
                $to = $date->copy();
                $to->year -= 1;
                $to->endOfYear();

                $totalSum = $singlePropertyCollections->whereBetween('date', [$from, $to])->sum('total');

                array_push($singlePropertyDatas, $totalSum);
            }

            array_push($singlePropertyDatas, intval($collections->total));


            array_push($singlePropertyDatas);
        }
        // this months column data
        if (!$isColumnsSet) {
            array_push($columns, TimeFunctions::getThisMonthString() . " " . Carbon::now()->year);
        }
        // this months data from api
        $thisMonthTotal = $thisMonth->where('EntitiesEntityID', $property->property_id)->sum('Total');
        array_push($singlePropertyDatas, $thisMonthTotal);
        
        // previous month this day
        array_push($singlePropertyDatas, $previousMonth != null ? $previousMonth->where('EntitiesEntityID', $property->property_id)->sum('Total') : "0");
        $isColumnsSet = true;
        
        // max last 12 months
        $max12 = StaticFunctions::GetMaxLast12($singlePropertyDatas);
        array_push($singlePropertyDatas, intval($max12));
        
        // max +5% 12 months
        array_push($singlePropertyDatas, intval($max12 * 1.05));
        
        // total area
        array_push($singlePropertyDatas, $property->total_area);
        
        //total charged rent
        $chargedSum = ChargedBreakdown::where('EntitiesEntityID', $property->property_id)->sum('Total');
        array_push($singlePropertyDatas, $chargedSum);
        
        // total uncollected rent
        array_push($singlePropertyDatas, $chargedSum - $thisMonthTotal);
        
        // total vacancy missing 
        array_push($singlePropertyDatas, $this->getVacancyMissing($property->property_id));

        return $singlePropertyDatas;
    }


    public function getVacancyMissing($property_id)
    {
        $response =  $this->api->getUnitAvailability($property_id);
        $jsonResponse = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
        $grids = [1, 2, 5, 6, 7];

        $totalVacancyMissing = 0;
        foreach ($grids as $i) {
            $grid = $jsonResponse["Grid" . $i];
            if ($grid != null) {
                foreach ($grid as $UnitAvailability) {
                    $totalVacancyMissing += $UnitAvailability["marketrent"];
                }
            }
        }

        Redis::set("VacancyMissing:" . $property_id, $totalVacancyMissing);
        
        return $totalVacancyMissing;
    }

    function setLastMonthCollection($property_id)
    {
        $dates = TimeFunctions::getMonthStartEndDates(true, true, true);
        $startDate = $dates[0];
        $endDate = $dates[1];
        $startStringDate = $startDate->month . "/" . $startDate->day . "/" . $startDate->year;
        $endStringDate = $endDate->month . "/" . $endDate->day . "/" . $endDate->year;

        $invoiceResponse = $this->api->getReportInvoices($startStringDate, $endStringDate, $property_id);
        $jsonArray = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $invoiceResponse), true);

        $cloneDate = clone $endDate;
        foreach ($jsonArray["Grid1"] as $reportInvoice) {

            $invoice = new ReportInvoice;
            $invoice->CustomersCustomerDisplayID = $reportInvoice["CustomersCustomerDisplayID"];
            $invoice->SubEntitiesName = $reportInvoice["SubEntitiesName"];
            $invoice->UnitTypesName = $reportInvoice["UnitTypesName"];
            $invoice->AccountsName = $reportInvoice["AccountsName"];
            $invoice->Total = $reportInvoice["Total"];
            $invoice->CustomersCustomerID = $reportInvoice["CustomersCustomerID"];
            $invoice->EntitiesEntityID = $reportInvoice["EntitiesEntityID"];
            $invoice->SubEntitiesSubEntityID = $reportInvoice["SubEntitiesSubEntityID"];
            $invoice->UnitTypesUnitTypeID = $reportInvoice["UnitTypesUnitTypeID"];
            $invoice->AccountsAccountType = $reportInvoice["AccountsAccountType"];
            $invoice->SubEntitiesSortOrder = $reportInvoice["SubEntitiesSortOrder"];
            $invoice->InvoiceDate = $cloneDate->setDay(1)->toDateString();
            $invoice->save();
        }
        $this->repo->setPropertyCollections();
    }
}
