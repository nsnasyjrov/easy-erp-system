<?php

use App\Http\Controllers\Companies\CompanyController;
use Illuminate\Support\Facades\Route;

Route::prefix('companies')->group(function () {

    //Base GET companies route
    Route::get('/', [CompanyController::class, 'index']);

    // Create company entity
    Route::post('/', [CompanyController::class, 'store']);

    // Read company entity
    Route::get('/{company}', [CompanyController::class, 'show']);

    //Update company entity
    Route::patch('{company}', [CompanyController::class, 'update']);

    // Delete
    Route::delete('{company}', [CompanyController::class, 'destroy']);

    // Get client
    Route::get('{company}/client', [CompanyController::class, 'client']);

    //Ensure client
    Route::post('{company}/client', [CompanyController::class, 'storeClient']);
});


