<?php

use App\Http\Controllers\DownloadReportsController;
use App\Http\Controllers\EmployeeWorkOrdersReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryPhysicalWorksheetReportController;
use App\Http\Controllers\InventoryTransactionsReportController;
use App\Http\Controllers\TransactionController;
use App\Library\Reports\ChargedBreakdownsReport;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/vacancyMapReport', [HomeController::class,'vacancyMap'])->name('vacancyReport');
Route::get('/propertyReport/{name}', [HomeController::class,'rentRoll'])->name("propertyReport");

Route::get('/getChargedBreakdown', function (ChargedBreakdownsReport $rep) {
    return $rep->SaveToDatabase(52);
});
Route::get('/inventoryTransactions', [InventoryTransactionsReportController::class,'get']);
Route::get('/inventoryPhysicalWorksheet', [InventoryPhysicalWorksheetReportController::class,'get']);
Route::get('/employeeWorkOrdersReport', [EmployeeWorkOrdersReportController::class,'get']);

Route::get('/transactionsReport', [TransactionController::class, 'get'])->name("transactionsReport");




Route::get('excelReport/collection-vacancy', [DownloadReportsController::class, 'collectionVacancyReport'])->name("report_download");
Route::get('excelReport/property', [DownloadReportsController::class, 'propertyReport'])->name("report_download.property");
