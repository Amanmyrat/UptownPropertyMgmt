<?php

namespace App\Http\Controllers;

use App\Library\Reports\EmployeeReport;
use App\Library\Reports\PropertyCollectionReport;
use App\Library\Reports\VacancyReport;
use App\Library\Services\ApiRepository;
use App\Library\Services\PythonHelper;
use App\Models\Property;
use Illuminate\Support\Facades\File;

class DownloadReportsController extends Controller
{
    public $pyHelper;

    public function __construct(PythonHelper $pyHelper)
    {
        $this->pyHelper = $pyHelper;
    }

    public function collectionVacancyReport(ApiRepository $api, PropertyCollectionReport $collectionReport, VacancyReport $vacancyReport)
    {
        
        $collection = $api->getJsonReportCollections($collectionReport);
        $excelCollectionsFileName = $this->getCollections($collection[0], 'collection', 'propertyCollections');
        $headersFileName = $this->getCollections($collection[1], 'collection', 'propertyCollectionsHeaders');
        
        $vacancyCollection = $api->getVacancyReport($vacancyReport);
        $excelVacancyCollectionsFileName = $this->getCollections($vacancyCollection[1], 'vacancy', 'propertyCollections');
        $vacancyheadersFileName = $this->getCollections($vacancyCollection[0], 'vacancy', 'propertyCollectionsHeaders');

        $result = $this->pyHelper->getCollectionVacancyReport(
            'getCollectionVacancyReport.py',
            $excelCollectionsFileName,
            $headersFileName,
            $excelVacancyCollectionsFileName,
            $vacancyheadersFileName,
        );

        if ($result[0] == "success") {
            $this->downloadExcel("COLLECTIONS & VACANCY REPORT");
        }
        
    }

    public function propertyReport(
        $propertyID,
        ApiRepository $apiRepository,
        PropertyReport $propertyReport,
        VacancyReport $report,
        EmployeeReport $emploeeReport,
        ExcelHelper $excelHelper,
        DownloadReportsController $downloadReportsController,
        PropertyVacancyMapReport $vacancyMapReport,
        PropertyCollectionReport $propertyCollectionReport
    ) {

        $excelFileName = $excelHelper->CopyAndGetFilename($propertyID);

        if ($excelFileName != null) {
            $vacancyReport = $vacancyMapReport->Get($propertyID);
            $propertyReport = $apiRepository->GetPropertyReport($propertyReport, $propertyID);

            $delinquency = [];
            foreach ($propertyReport[1] as $report) {
                if ($report[9] > 0) {
                    array_push($delinquency, $report);
                }
            }

            $propertyCollections = $apiRepository->GetJsonReportCollections($propertyCollectionReport);
            $propertyName = $propertyReport[2]->ShortName;

            $propertyMonthsReport = [];
            foreach($propertyCollections[0] as $property){
                if(count($property) > 0 && $property[1] == $propertyName){
                    $propertyMonthsReport = $property;
                }
            }
            $propertyMonthsReport = array_splice($propertyMonthsReport, 0, -7);
            $propertyMonthsReport = array_slice($propertyMonthsReport, -4);

            $result = $downloadReportsController->downloadPropertyReport($propertyID, $vacancyReport, $propertyReport, 
                $delinquency, $emploeeReport, $excelFileName, $propertyMonthsReport);

            if ($result[0] === "success") {
                $this->downloadExcel($result[1]);
            }
        } else {
            echo ('excel file not found ');
            return "failed";
        }
    }

    public function getCollections($collection, $folder, $filename)
    {

        $propertyCollectionsFileName = storage_path('temp') . '/' . $folder . '/' . $filename . '.json';
        File::ensureDirectoryExists(storage_path('temp') . '/' . $folder);
        $myFile = fopen($propertyCollectionsFileName, "w+");
        $data  = json_encode($collection);
        fwrite($myFile, $data);

        return $propertyCollectionsFileName;
    }

    private function downloadExcel($title)
    {
        if ($title) {
            echo "<script>window.open('" . asset('storage/' . $title . '.xlsx') . "', '_blank')</script>";
            echo "<script>window.close();</script>";
        } else {
            echo "<script>window.close();</script>";
        }
    }
    

}
