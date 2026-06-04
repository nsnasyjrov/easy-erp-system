<?php

use App\Http\Controllers\Companies\CompanyController;
use Illuminate\Support\Facades\Route;

Route::prefix('companies')->group(function () {

//Base GET companies route
    Route::get('/', [CompanyController::class, 'index']);

    Route::get('/{company}', [CompanyController::class, 'show']);

// Create company entity
    Route::post('/', [CompanyController::class, 'store']);

//Update company entity
    Route::patch('{company}', [CompanyController::class, 'update']);

// Get client
    Route::get('{company_id}/client', [CompanyController::class, 'client']);

//Ensure client
    Route::post('{company_id}/client', [CompanyController::class, 'storeClient']);
});


