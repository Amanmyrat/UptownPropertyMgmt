<?php

namespace App\Http\Controllers;

use App\Library\Reports\EmployeeWorkOrdersReport;
use App\Library\Services\ApiRepository;

class EmployeeWorkOrdersReportController extends Controller
{

    public function get(ApiRepository $api, EmployeeWorkOrdersReport $report)
    {
        $response = $api->GetEmployeeWorkOrders($report);

        return  view('employeeWorkOrdersReport', ['report' => $response[0], 'columns' => $response[1], 'dates' => $response[2]]);
    }
}
