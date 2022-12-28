<?php

namespace App\Http\Controllers;

use App\Library\Reports\TransactionsReport;
use App\Library\Services\ApiRepository;
use stdClass;

class TransactionController extends Controller
{
    public function getApiReport(ApiRepository $apiRepository, TransactionsReport $transactionsReport)
    {
        $report = $apiRepository->getTransactionsReport($transactionsReport);

        $processed = [];

        foreach ($report[0] as $key => $item) {
            foreach ($item as $data) {
                $slicedArr = array_slice($data, 3);
                array_pop($slicedArr);
                foreach ($slicedArr as $i => $value) {
                    if ($value != "0.00") {
                        $val = new stdClass();
                        $val->date = $key;
                        $val->vendor = $data[1];
                        $val->category = $data[2];
                        $val->property = $report[1][$key][$i];
                        $val->value = $value;

                        array_push($processed, $val);
                    }
                }
            }
        }
        return response()->json($processed);
    }

    public function get(ApiRepository $apiRepository, TransactionsReport $transactionsReport)
    {
        $report = $apiRepository->getTransactionsReport($transactionsReport);

        return view('transactions_report', ["propertyCollections" => $report]);
    }
}
