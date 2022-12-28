<?php
namespace App\Library\Jobs;

use App\Library\Classes\RedisHelper;
use App\Library\Classes\RedisKeys;
use App\Library\Reports\PropertyCollectionReport;
use Illuminate\Support\Facades\Log;

/**
 * this invokes automatically and saves to Redis cache
 */
class CollectionReportJob{
    public function __construct(PropertyCollectionReport $report) {
        
        $collectionKey = RedisKeys::GetCollectionReportKey();
        $response = $report->getReport(52);

        $file = storage_path()."/app/public/COLLECTIONS REPORT.xlsx";
        if(file_exists($file)){
            unlink($file);
        }

        if($response != null)
        {
            RedisHelper::set($collectionKey, $response);
            // $downloadReportsController->downloadCollectionReport($response[0], $response[1]);
            Log::info('GetCollectionReportKey finished');
        }

    }


}
