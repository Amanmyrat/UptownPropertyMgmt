<?php

namespace App\Library\Reports;

use App\Library\Services\RentManagerApi;
use App\Models\Property;
use App\Models\UnitAvailability;

class VacancyReport
{
    public $api;

    function __construct(RentManagerApi $api)
    {
        $this->api = $api;
    }

    public $unitAvailabilityStatuses = array("available", "willBeAvailable", "occupiedWithNotice", "occupiedPreLease", "preLeaseVacant",
        "preLeaseNotReady", "downUnit", "vacantAndNotReady", "burned", "notReady");

    public function getUnitAvailability($property_id = -1)
    {
        $response = $this->api->getUnitAvailability($property_id);
        
        UnitAvailability::query()->delete();
        $jsonResponse = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);

        $newUnitAvailabilities = array();
        for ($i = 1; $i < 7; $i++) {
            $grid = $jsonResponse["Grid" . $i];
            
            if ($grid != null) {
                foreach ($grid as $UnitAvailability) {
                    $unit_availability = new UnitAvailability();
                    $unit_availability->fill($UnitAvailability, true);
                    
                    if ($i != 2) {
                        $unit_availability->statusId = $i;
                    } else {
                        if ($UnitAvailability["description"] == "Down Unit") {
                            $unit_availability->statusId = 7;
                        } else if ($UnitAvailability["description"] == "Vacant & Not Ready") {
                            $unit_availability->statusId = 8;
                        }
                        else if ($UnitAvailability["description"] == "Burn Unit") {
                            $unit_availability->statusId = 9;
                        } else {
                            $unit_availability->statusId = 2;
                        }
                    }
                    $unit_availability->save();
                    array_push($newUnitAvailabilities, $unit_availability);
                }
            }
        }

        return $newUnitAvailabilities;
    }

    function getReport($property_id)
    {
        $availability = $this->getUnitAvailability($property_id);
        $collectAvailability = collect($availability);
        $datas = array();
        $columns = array(
            'No', 'PropertyName', 'TotalUnits', 'Total Occupied',
            'Total Vacant', 'Total Unburned Units', 'Occupancy Percentage', 'Vacant Done', 'Vacant Not Done', 'Down Units', 'Burn Units',
            'Projected Vacancy', 'Vacant & PreLeased', 'Occupied with Notice'
        );
        $int = 1;
        
        $singlePropertyDatas = array();

        $property = Property::where("property_id", $property_id)->get()->first();
        $propertyUnits = $collectAvailability->where('entityid', $property_id);
        $totalOccupied = $property->total_units - $propertyUnits->whereNotIn('statusId', [3, 4])->count();
        $totalVacant = $property->total_units - $totalOccupied;
        $totalBurned = $propertyUnits->where('statusId', 9)->count();
        array_push($singlePropertyDatas, $int);
        array_push($singlePropertyDatas, $property->shortname);
        array_push($singlePropertyDatas, $property->total_units);
        array_push($singlePropertyDatas, $totalOccupied);
        //total vacant
        array_push($singlePropertyDatas, $totalVacant);
        //total unburned
        array_push($singlePropertyDatas, $totalVacant - $totalBurned);
        //occupancy percentage
        array_push($singlePropertyDatas, round((($totalOccupied / max(1, $property->total_units)) * 100), 2) . "%");
        //vacant done
        array_push($singlePropertyDatas, $propertyUnits->whereIn('statusId', [1, 5])->count());
        // vacant not done
        array_push($singlePropertyDatas, $propertyUnits->whereIn('statusId', [2, 6, 8, 10])->count());
        //down unit
        array_push($singlePropertyDatas, $propertyUnits->where('statusId', 7)->count());
        //burn unit
        array_push($singlePropertyDatas, $totalBurned);
        //projected vacancy
        array_push($singlePropertyDatas, $property->total_units - $totalOccupied + $propertyUnits->where('statusId', 7)->count() - $propertyUnits->whereIn('statusId', [2, 6, 8, 10])->count());
        //vacant & preleased
        array_push($singlePropertyDatas, $propertyUnits->where('statusId', 5)->count());
        //occupied with notice
        array_push($singlePropertyDatas, $propertyUnits->where('statusId', 3)->count());

        $int++;

        array_push($datas, $singlePropertyDatas);

        return [$columns, $datas];
    }
}
