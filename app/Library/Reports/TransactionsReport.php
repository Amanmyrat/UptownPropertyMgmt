<?php

namespace App\Library\Reports;

use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TransactionsReport
{
    private $api;
    private $startDate = '2022-11-01';

    function __construct()
    // function __construct(QuickBooksApi $api)
    {
        
        dd("testing");
        $this->api = $api;
    }

    public function getTransactions($from = null, $to = null)
    {
        $transactions = [];
        $columns = [];
        $propertyCollections = [];

        $period = CarbonPeriod::create($from, '1 month', $to);

        foreach ($period as $date) {

            $startDate = $date->startOfMonth()->format("Y-m-d");
            $endDate = $date->endOfMonth()->format("Y-m-d");

            $result = $this->api->getTransactionsList($startDate, $endDate);
            $result = json_decode($result, true);

            $rows = $result["Rows"]["Row"];
            $tempExpenses = [];
            foreach ($rows as $row) {
                $inner_rows = $row["Rows"]["Row"];
                foreach ($inner_rows as $inner_row) {
                    if (
                        $inner_row["ColData"][1]["value"] != "Bill"
                        && (float) $inner_row["ColData"][8]["value"] < 0
                        && $inner_row["ColData"][7]["value"] != "52050 Contractors"  //category
                        && $inner_row["ColData"][7]["value"] != "21000 Accounts Payable (A/P)" //category
                        && $inner_row["ColData"][7]["value"] != "Direct Deposit Payable" //category
                        && $inner_row["ColData"][7]["value"] != "-Split-" //category
                    ) {
                        $vendor = $inner_row["ColData"][4]["value"] != "" ? $inner_row["ColData"][4]["value"] : "Not Specified";
                        $property = $inner_row["ColData"][6]["value"] != "" ? $inner_row["ColData"][6]["value"] : "Not Specified";
                        $property = str_contains($property, "-") ? trim(explode('-', $property)[1]) : $property;
                        $category = $inner_row["ColData"][7]["value"] != "" ? $inner_row["ColData"][7]["value"] : "Not Specified";
                        $object = [
                            'id' => md5($vendor . "---" . $property),
                            'vendor' => $vendor,
                            'property' => $property,
                            'category' => $category,
                            'type' => $inner_row["ColData"][1]["value"],
                            'value' => (float) $inner_row["ColData"][8]["value"],
                            'date' =>  $inner_row["ColData"][0]["value"],
                        ];
                        array_push($tempExpenses, $object);
                    }
                }
            }

            // sum of duplicate values by unique id from vendor and property
            $res  = array();
            foreach ($tempExpenses as $vals) {
                if (array_key_exists($vals['id'], $res)) {
                    $res[$vals['id']]['value']    += $vals['value'];
                    $res[$vals['id']]['vendor']    = $vals['vendor'];
                    $res[$vals['id']]['property']    = $vals['property'];
                    $res[$vals['id']]['category']    = $vals['category'];
                    $res[$vals['id']]['date']    = $vals['date'];
                    $res[$vals['id']]['id']        = $vals['id'];
                } else {
                    $res[$vals['id']]  = $vals;
                }
            }
            $res = array_values($res);

            // create headers
            $headers = array_reverse(array_values(array_column(
                array_reverse($res),
                null,
                'property'
            )));

            $newHeaders = array();
            foreach ($headers as $header) {
                array_push($newHeaders, $header["property"]);
            }

            $key = array_search('Not Specified', $newHeaders);
            $v = $newHeaders[$key];
            unset($newHeaders[$key]);
            $newHeaders[$key] = $v;
            $newHeaders = array_values($newHeaders);

            // create vendors
            $vendors = array_reverse(array_values(array_column(
                array_reverse($res),
                null,
                'vendor'
            )));

            $newVendors = array();
            foreach ($vendors as $vendor) {
                $tempVendor = array();
                array_push($tempVendor, $vendor["vendor"]);
                foreach ($newHeaders as $header) {
                    array_push($tempVendor, "0.00");
                }
                array_push($newVendors, $tempVendor);
            }

            // fill vendors according to header properties
            foreach ($newVendors as $vendorKey => $data) {
                $keys = array_keys(array_column($res, 'vendor'), $data[0]);
                $sum = 0;
                foreach ($keys as $key) {
                    $value = $res[$key]["value"];
                    $index = array_search($res[$key]["property"], $newHeaders);
                    $newVendors[$vendorKey][$index + 1] = strval($value);
                    $sum += $value;
                }
                array_unshift($newVendors[$vendorKey], strval($vendorKey + 1));
                array_push($newVendors[$vendorKey], strval($sum));
                $category = str_contains($res[$key]["category"], ":") ? trim(explode(':', $res[$key]["category"])[1]) : $res[$key]["category"];
                $this->array_insert($newVendors[$vendorKey], 2, $category);
            }

            array_push($newHeaders, "Total");

            count($newHeaders) > 0 ? $columns[$date->format("F Y")] = $newHeaders : null;
            count($newVendors) > 0 ? $transactions[$date->format("F Y")] = $newVendors : null;
        }

        if (count($transactions) < 1) {
            return null;
        }
        if (count($columns) < 1) {
            return null;
        }
        array_push($propertyCollections, $transactions);
        array_push($propertyCollections, $columns);

        return $propertyCollections;
    }

    public function synchronize($startDate = null, $endDate = null)
    {
        $datePeriod = array();
        $transactions = Transaction::all();

        $today = Carbon::now()->subMonth()->format("Y-m-d");

        if ($startDate && $endDate) {
            $startDate = Carbon::parse("01/" . $startDate)->format("Y-d-m");
            $endDate = Carbon::parse("01/" . $endDate)->format("Y-d-m");

            $period = CarbonPeriod::create($startDate, '1 month', $endDate);
        } else {
            $period = CarbonPeriod::create($this->startDate, '1 month', $today);
        }
        $period = array_reverse(iterator_to_array($period));

        foreach ($period as $key => $date) {
            array_push($datePeriod, $date->format("F Y"));
        }

        echo '<button  onclick="location.href = document.referrer; return false;">Back</button></br></br>';

        if (count($transactions) < 1) {
            $end = new Carbon('last day of last month');
            $reports = $this->getTransactions($this->startDate, $end->format("Y-m-d"));

            foreach ($reports[0] as $key => $report) {
                $transactions = new Transaction();
                $transactions->transaction = json_encode($report);
                $transactions->header = json_encode($reports[1][$key]);
                $transactions->date = $key;

                $transactions->save();

                echo ($key . " added");
            }
        } else if (count($transactions) > 0) {
            foreach ($transactions as $item) {
                foreach ($datePeriod as $key => $date) {
                    if ($date == $item["date"]) {
                        unset($datePeriod[$key]);
                    }
                }
            }

            $datePeriod = array_values($datePeriod);
            foreach ($datePeriod as $key => $date) {
                if ($key > 5) {
                    break;
                }
                $formatedDate = Carbon::parse($date)->format('Y-m-d');
                $report = $this->getTransactions($formatedDate, $formatedDate);

                $transactions = new Transaction();
                $transactions->transaction = json_encode($report[0][$date]);
                $transactions->header = json_encode($report[1][$date]);
                $transactions->date = $date;
                $transactions->save();

                echo ($date . " added");
            }
        } else {
            echo ("All updated");
        }
    }

    public function getTransactionsReport()
    {
        $transactions = [];
        $headers = [];
        $propertyCollections = [];

        $this->synchronize();
        $previousTransactions = Transaction::all();

        foreach ($previousTransactions as $item) {
            $transactions[$item->date] = json_decode($item->transaction, true);
            $headers[$item->date] = json_decode($item->header, true);
        }

        $today = Carbon::now()->format('Y-m-d');
        $currentMonthTransactions = $this->getTransactions($today, $today);

        if ($currentMonthTransactions == null) {
            array_push($propertyCollections, $transactions);
            array_push($propertyCollections, $headers);

            return $propertyCollections;
        }
        foreach ($currentMonthTransactions[0] as $key => $item) {
            $transactions[$key] = $item;
        }
        foreach ($currentMonthTransactions[1] as $key => $item) {
            $headers[$key] = $item;
        }

        array_push($propertyCollections, $transactions);
        array_push($propertyCollections, $headers);

        return $propertyCollections;
    }

    function array_insert(&$array, $position, $insert)
    {
        if (is_int($position)) {
            array_splice($array, $position, 0, $insert);
        } else {
            $pos   = array_search($position, array_keys($array));
            $array = array_merge(
                array_slice($array, 0, $pos),
                $insert,
                array_slice($array, $pos)
            );
        }
    }
}
