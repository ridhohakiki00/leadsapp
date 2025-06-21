<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadsReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('leads.index');
});


Route::get('/report-schema', [LeadsReportController::class, 'getFields']);
Route::get('/report-options/{field}', [LeadsReportController::class, 'getCriterias']);
Route::post('/report-create', [LeadsReportController::class, 'storeReport']);
Route::get('/report/{id}', [LeadsReportController::class, 'showReport']);
Route::get('/report-excel/{id}', [LeadsReportController::class, 'exportToExcel']);
Route::get('/report-pdf/{id}', [LeadsReportController::class, 'exportToPdf']);
Route::delete('/report/{id}', [LeadsReportController::class, 'deleteReport']);
