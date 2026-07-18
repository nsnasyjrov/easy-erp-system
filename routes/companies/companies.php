<?php

use App\Http\Controllers\Companies\CompanyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->prefix('companies')->group(function () {

    //Base GET companies route
    Route::get('/', [CompanyController::class, 'index']);

    // Create company entity
    Route::post('/', [CompanyController::class, 'store']);

    // Read company entity
    Route::get('/{company}', [CompanyController::class, 'show'])->whereNumber('company');

    //Update company entity
    Route::patch('{company}', [CompanyController::class, 'update'])->whereNumber('company');

    // Delete
    Route::delete('{company}', [CompanyController::class, 'destroy'])->whereNumber('company');

    // Get client
    Route::get('{company}/client', [CompanyController::class, 'client'])->whereNumber('company');

    //Ensure client
    Route::post('{company}/client', [CompanyController::class, 'storeClient'])->whereNumber('company');
});


