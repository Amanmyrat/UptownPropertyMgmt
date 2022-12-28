<?php
namespace App\Http\Controllers;

use App\Library\Reports\InventoryPhysicalWorksheetReport;
use App\Library\Services\ApiRepository;

class InventoryPhysicalWorksheetReportController extends Controller
{
    public function get(ApiRepository $api, InventoryPhysicalWorksheetReport $report){
        $inventoryPhysicalWorksheet = $api->getInventoryPhysicalWorksheet($report);

        return  view('physicalInventoryWorksheet', ["report" =>$inventoryPhysicalWorksheet]);
    }
}
