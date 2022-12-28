<?php

namespace App\Library\Reports;

use App\Library\Services\ApiRepository;
use App\Library\Classes\RedisHelper;
use App\Library\Classes\RedisKeys;
use App\Library\Classes\TimeFunctions;
use App\Library\Services\DbRepository;
use App\Library\Services\RentManagerApi;
use App\Models\ChargedBreakdown;
use App\Models\Property;
use App\Models\ReportInvoice;
use App\Models\UnitAvailability;
use Carbon\Carbon;

class PropertyReport
{
    public $api;
    public $dbRep;
    public $apiRep;
    
    function __construct(RentManagerApi $api, DbRepository $dbRep, ApiRepository $apiRep)
    {
        $this->api = $api;
        $this->dbRep = $dbRep;
        $this->apiRep = $apiRep;
    }

    public function getLastMonths()
    {
        $start = Carbon::parse("2022-11-01");
        $diff = Carbon::now()->diff($start);
        $monthCount = $diff->m > 3 ? 3 : $diff->m;

        $now = Carbon::now();
        $now->subMonth();
        $now->day = 1;

        $months = array();
        while ($monthCount > 0) {
            array_push($months, $now->toDateString());
            $now->subMonth();
            $monthCount--;
        }
        return array_reverse($months);
    }
    
    public function getReport($propertyID)
    {
        //this doesnt change
        $units = $this->dbRep->getUnits($propertyID);

        $propertyTable = array();
        $no = 1;
        $property = Property::where("property_id", $propertyID)->get()->first();
        $chargedBreakdowns = ChargedBreakdown::where('EntitiesEntityID', $property->property_id)->get();

        $months = $this->getLastMonths();

        $reportInvoices = array();
        foreach ($months as $month) {
            array_push($reportInvoices, ReportInvoice::where('InvoiceDate', $month)->where('EntitiesEntityID', $propertyID)->get());
        }

        // ## THIS MONTHS COLLECTION ##
        $thisMonth = RedisHelper::get(RedisKeys::getThisMonthCollection());
        
        if (count($thisMonth) < 1) {
            $thisMonth = $this->apiRep->getThisMonthInvoiceReports($propertyID);
        }
        //## BALANCE DUES
        $balanceDues = $this->apiRep->getBalanceDues();

        // ## TENANT HISTORIES
        $histories = $this->apiRep->getTenantHistories();
        // ## END ##
        foreach ($units as $unit) {
            $row = array();
            // no
            array_push($row, $no);
            // property name
            array_push($row, $property->shortname);
            // Unit Name
            array_push($row, $unit->Name);
            //unittype
            array_push($row, isset($unit->unitType->Name) ? $unit->unitType->Name : "");
            $currentrent = "";
            $breakdown = $chargedBreakdowns->firstWhere('SubEntitiesName', $unit->Name);
            
            if ($breakdown != null) {
                $currentrent = $breakdown->Total;
            }
            // current rent
            array_push($row, $currentrent);
            // old months
            foreach ($reportInvoices as $invoices) {
                $invoice = $invoices->firstWhere('SubEntitiesSubEntityID', $unit->UnitID);
                if ($invoice != null) {
                    array_push($row, $invoice->Total);
                } else {
                    array_push($row, "");
                }
            }
            //this month
            $invoice = $thisMonth->firstWhere('SubEntitiesSubEntityID', $unit->UnitID);
            if ($invoice != null) {

                array_push($row, $invoice['Total']);
            } else {
                array_push($row, "");
            }
            // balance && delinquency notes
            $balanceDue = $balanceDues->firstWhere('SubEntitiesSubEntityID', $unit->UnitID);
            
            if ($balanceDue != null) {
                array_push($row, $balanceDue['Balance']);
                $history = $histories->where('AccountsAccountID', $balanceDue['AccountsAccountID'])->last();
                $historyNote = "";
                if ($history != null) {
                    $historyNote = $history['HistoryNoteText'];
                }
                array_push($row, $historyNote);
            }
            // empty columns
            else {
                array_push($row, "");
                array_push($row, "");
            }

            array_push($propertyTable, $row);
            $no++;
        }
        $columns = ["No", "ComplexName", "Apt #", "FloorType", "Current Rent"];
        foreach ($months as $month) {
            $date = new Carbon($month);
            array_push($columns, $date->localeMonth . " " . $date->year);
        }
        array_push($columns, TimeFunctions::GetThisMonthString() . " " . $date->year);
        array_push($columns, "Balance");
        array_push($columns, "Comments");

        $unitAvailabilities = UnitAvailability::where('entityid', $property->property_id)->get();

        return [$columns, $propertyTable, $property, $unitAvailabilities];
    }
}
