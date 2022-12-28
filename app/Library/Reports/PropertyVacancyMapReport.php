<?php

namespace App\Library\Reports;

use App\Library\Classes\RedisHelper;
use App\Library\Classes\RedisKeys;
use App\Library\Services\RentManagerApi;
use Illuminate\Support\Facades\Redis;

class PropertyVacancyMapReport
{

    public $api;
    public $vacancyReport;
    
    function __construct(RentManagerApi $api, VacancyReport $report)
    {
        $this->api = $api;
        $this->vacancyReport = $report;
    }

    function getReport($propertyId)
    {
        $propertyCollections = RedisHelper::get(RedisKeys::getUnitAvailability($propertyId));
        
        if ($propertyCollections != null) {
            return $propertyCollections;
        }
        
        $unitAvailabilities = $this->vacancyReport->getUnitAvailability($propertyId);

        Redis::set(RedisKeys::getUnitAvailability($propertyId), base64_encode(serialize($unitAvailabilities)));
        return $unitAvailabilities;
    }
}
