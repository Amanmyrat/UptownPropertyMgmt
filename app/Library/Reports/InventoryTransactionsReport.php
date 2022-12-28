<?php

namespace App\Library\Reports;

use App\Library\Classes\TimeFunctions;
use App\Library\Services\RentManagerApi;
use Carbon\Carbon;

/**
 *
 */
class InventoryTransactionsReport
{
    public $api;

    function __construct(RentManagerApi $api)
    {
        $this->api = $api;
    }

    function getReport($property_id, $isPrevMonth = false)
    {
        if (!$isPrevMonth) {
            $dated = TimeFunctions::GetMonthStartEndDates(false, false, true);
        } else {
            $dated = TimeFunctions::GetMonthStartEndDates(true, false, true);
        }

        $prev = $dated[0];
        $now = $dated[1];

        $histCollection = collect(json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->api->getInventoryHistories($property_id)), false)->Grid1);

        $filteredIssuesResponse = $this->api->getFilteredIssueList($property_id);
        
        $filteredIssues = collect(json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $filteredIssuesResponse), true)['Grid1']);
        
        $physicalInventoryWorksheet = collect(json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->api->getPhysicalInventoryWorksheet($property_id)))->Grid1);
        
        $purchasesOfOrder = collect(json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->api->getPurchasesOfOrder($property_id)))->Grid1);

        $inventoryTransactionReport = $this->getInventoryTransactionReport($histCollection, $filteredIssues, $physicalInventoryWorksheet, $purchasesOfOrder);
        $itrColumns = ["No", "Inventory Item Name", "Description", "QuantityUsed", "TransactionType", "IssueID", "Quantity in Storage", "Cost Each", "TotalCost", "PropertyGiven", "PropertyUsed", "Unit", "Vendor", "Assigned Employee", "Date"];
        
        return ["data" => $inventoryTransactionReport, "columns" => $itrColumns, "fromDate" => $prev->toDateString(), "toDate" => $now->toDateString()];
    }

    private function GetInventoryTransactionReport($histCollection, $filteredIssues, $physicalInventoryWorksheet, $purchasesOfOrder)
    {
        $report = array();

        foreach ($histCollection as $hist) {
            $filteredIssue  = $filteredIssues->where('ServiceManagerIssuesServiceManagerIssueID', $hist->ServiceManagerItemsServiceManagerIssueID)->first();
            $row = array();
            array_push($row, "");
            array_push($row, $hist->InventoryItemsName);
            array_push($row, $hist->InventoryItemsDescription);
            array_push($row, $hist->Quantity);
            array_push($row, $hist->TransactionType);
            array_push($row, $hist->ServiceManagerItemsServiceManagerIssueID != 0 ? $hist->ServiceManagerItemsServiceManagerIssueID : '');
            // quantity in storage
            $worksheet = $physicalInventoryWorksheet->where('InventoryItemsInventoryItemID', $hist->InventoryItemsInventoryItemID)->where('EntityID', $hist->EntityID)->first();
            if ($worksheet != null) {
                array_push($row, $worksheet->QtyOnHand);
            } else {
                array_push($row, '');
            }

            // cost each
            array_push($row, property_exists($hist, 'Price') ?  $hist->Price : "-");
            // total price
            array_push($row, property_exists($hist, 'ExtendedTotalPrice') ? $hist->ExtendedTotalPrice : "-");
            array_push($row, $hist->Property);
            // propertyUsed
            if ($filteredIssue != null && array_key_exists('EntitiesName', $filteredIssue)) {
                array_push($row, $filteredIssue['EntitiesName']);
            } else {
                array_push($row, '');
            }
            // unit
            if ($filteredIssue != null && array_key_exists('SubEntitiesName', $filteredIssue)) {
                array_push($row, $filteredIssue['SubEntitiesName']);
            } else {
                array_push($row, '');
            }

            $date = new Carbon($hist->HistoryDate);
            // vendor
            $po = $purchasesOfOrder->where('PurchaseOrdersPurchaseOrderID', $hist->PurchaseOrdersPurchaseOrderID)->first();
            if ($po != null && property_exists($po, 'AccountsName')) {
                array_push($row, $po->AccountsName);
            } else {
                array_push($row, '');
            }
            // employee
            if ($filteredIssue != null && array_key_exists('AccountsName', $filteredIssue)) {
                array_push($row, $filteredIssue['AccountsName']);
            } else {
                array_push($row, '');
            }
            //date
            array_push($row, $date->toDateString());
            array_push($report, $row);
        }
        return $report;
    }
}
