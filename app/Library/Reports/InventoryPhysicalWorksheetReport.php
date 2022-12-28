<?php

namespace App\Library\Reports;

use App\Library\Services\ApiRepository;
use App\Library\Services\RentManagerApi;

class InventoryPhysicalWorksheetReport
{
    public $api;
    public $rep;
    public $property_ids = [52];

    function __construct(RentManagerApi $api, ApiRepository $rep)
    {
        $this->api = $api;
        $this->rep = $rep;
    }

    function getReport($property_id)
    {
        $physicalInventoryWorksheet = collect(json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->api->getPhysicalInventoryWorksheet($property_id)))->Grid1);
        $report = $this->getPhysicalWorksheetReport($physicalInventoryWorksheet);
        $itrColumns = ["No", "Item Name", "Description"];
        $properties  = $this->rep->GetProperties();
        foreach ($this->property_ids as $property_id) {
            array_push($itrColumns, $properties->where('property_id', $property_id)->first()->shortname);
        }
        
        return ["data" => $report, "columns" => $itrColumns];
    }

    private function getPhysicalWorksheetReport($physicalInventoryWorksheet)
    {
        $report = array();
        $grouped = $physicalInventoryWorksheet->groupBy('InventoryItemsName');

        foreach ($grouped as $workSheet) {
            $row = array();
            array_push($row, "");
            array_push($row, $workSheet[0]->InventoryItemsName);
            array_push($row, $workSheet[0]->InventoryItemsDescription);
            foreach ($this->property_ids as $id) {
                $pRep = $workSheet->where('EntityID', $id)->first();

                if ($pRep != null) {
                    array_push($row, $pRep->QtyOnHand);
                } else {
                    array_push($row, 0);
                }
            }
            array_push($report, $row);
        }
        return $report;
    }
}
