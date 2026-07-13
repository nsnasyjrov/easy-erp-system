<?php

use App\Http\Controllers\Individuals\IndividualController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('individuals')->group(function () {

    Route::get('/', [IndividualController::class, 'index']);

    Route::post("/", [IndividualController::class, 'store']);

    Route::patch('{individual}', [IndividualController::class, 'update'])->whereNumber('individual');

    Route::delete('{individual}', [IndividualController::class, 'destroy'])->whereNumber('individual');

    Route::get('{individual}/client', [IndividualController::class, 'client'])->whereNumber('individual');

    Route::post('{individual}/client', [IndividualController::class, 'ensureClient'])->whereNumber('individual');

    Route::get("{individual}", [IndividualController::class, 'show'])->whereNumber("individual");

});
