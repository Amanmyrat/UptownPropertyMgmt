<?php

namespace App\Http\Controllers;

use App\Library\Reports\PropertyCollectionReport;
use App\Library\Reports\PropertyReport;
use App\Library\Reports\PropertyVacancyMapReport;
use App\Library\Reports\VacancyReport;
use App\Library\Services\ApiRepository;
use App\Models\Property;

class HomeController extends Controller
{
    function index(ApiRepository $api, PropertyCollectionReport $report, VacancyReport $vacancyReport)
    {
        $propertyCollections = $api->getJsonReportCollections($report);
        $vacancyReport = $api->getVacancyReport($vacancyReport);
        return view('dashboard', [
            "propertyCollections" => $propertyCollections,
            'vacancyReport' => $vacancyReport
        ]);
    }

    function vacancyMap(PropertyVacancyMapReport $vacancyMapReport)
    {
        $property = Property::where("property_id", 52)->get()->first();
        $vacancyReport = $vacancyMapReport->getReport(52);
        
        return view('vacancyMap', ['vacancyReport' => $vacancyReport, 'property' => $property, 'employeeReport' => null]);
    }

    function rentRoll(PropertyReport $report, ApiRepository $apiRepository){
        $propertyReport = $apiRepository->getPropertyReport($report);

        return view('propertyReport', ['report' => $propertyReport[1], 'columns'=>$propertyReport[0],
            'property'=>$propertyReport[2], 'unitAvailabilities' => $propertyReport[3]]);
    }

    
}
