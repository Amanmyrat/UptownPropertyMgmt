<?php

namespace App\Library\Reports;

use App\Library\Classes\TimeFunctions;
use App\Library\Services\ApiRepository;
use App\Library\Services\RentManagerApi;
use App\Models\ChargedBreakdown;

class ChargedBreakdownsReport
{
    public $api;
    public $repo;
    function __construct(RentManagerApi $api, ApiRepository $repo)
    {
        $this->api = $api;
        $this->repo = $repo;
    }

    function saveToDatabase($property_id)
    {
        ChargedBreakdown::query()->delete();
        $dates = TimeFunctions::getMonthStartEndDates();

        $response = $this->api->getChargedBreakdown($dates[0], $dates[1], $property_id);
        $breakdownarrays = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true)['Grid1'];

        foreach ($breakdownarrays as $breakdownarray) {
            $chargedBreakdown = new ChargedBreakdown();
            $chargedBreakdown->fill($breakdownarray, true);
            $chargedBreakdown->save();
        }
        dd($breakdownarrays);
    }
}
