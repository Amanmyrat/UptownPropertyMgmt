<?php

namespace App\Library\Services;

use App\Models\Unit;
use App\Models\UnitType;

class DbRepository
{
    public $api;
    function __construct(RentManagerApi $api)
    {
        $this->api = $api;
    }

    function getUnits($propertyID)
    {
        $units = Unit::where('PropertyID', $propertyID)->get();
        
        if (count($units) == 0) {
            $response =  $this->api->getUnitsByPropertyId($propertyID);
            $jsonResponse = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
            foreach ($jsonResponse as $unitarray) {
                $unit = new Unit();
                $unit->fill($unitarray, true);
                $unit->save();
            }
            $unitTypeCounts = UnitType::count();
            
            if ($unitTypeCounts == 0) {
                $this->GetUnitTypes();
            }
            return Unit::where('PropertyID', $propertyID)->get();
        }
        return $units;;
    }

    function getUnitTypes()
    {
        $response =  $this->api->getUnitTypes();

        $jsonResponse = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
        foreach ($jsonResponse as $unitarray) {
            $unit = new UnitType();
            $unit->fill($unitarray, true);
            $unit->save();
        }
    }
}
