<?php

namespace App\Http\Controllers;

use App\Library\Classes\RedisHelper;
use App\Library\Classes\RedisKeys;
use App\Library\Reports\InventoryTransactionsReport;
use App\Library\Services\ApiRepository;
use Illuminate\Http\Request;

class InventoryTransactionsReportController extends Controller
{

    public function get(ApiRepository $api, InventoryTransactionsReport $report, Request $request)
    {

        $date = $request->input('date');
        if ($date != null) {
            $redisReport = RedisHelper::get("inventoryTransactionsReport:" . $date);
        } else if ($request->input('prevMonth') == 1) {
            $redisReport = $report->getReport(true);
        } else {
            $redisReport = RedisHelper::get(RedisKeys::getInventoryTransactionsReport());
        }
        if ($redisReport == null) {
            $redisReport = $api->getInventoryTransactionsReport($report);
        }
        usort($redisReport['data'], function ($a, $b) {
            return $a[14] < $b[14] ? 1 : -1;
        });

        return view('inventoryTransactions', ["report" => $redisReport]);
    }
}
