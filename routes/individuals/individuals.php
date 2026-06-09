<?php

use App\Http\Controllers\Individuals\IndividualController;
use Illuminate\Support\Facades\Route;

Route::prefix('individuals')->group(function () {

    Route::get('/', [IndividualController::class, 'index']);

    Route::post("/", [IndividualController::class, 'store']);

    Route::get("{individual}", [IndividualController::class, 'show'])->whereNumber("individual");

});
