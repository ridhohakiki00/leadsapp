<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\LeadReport;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/reports', function () {
    return LeadReport::select('id', 'name')->latest()->get();
});
