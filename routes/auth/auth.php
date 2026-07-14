<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1');

    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function() {

        Route::get('profile', [AuthController::class, 'profile']);

        Route::get('logout', [AuthController::class, 'logout']);

        Route::get('logout-all', [AuthController::class, 'logoutAll']);

        Route::get('tokens', [AuthController::class, 'tokens']);

        Route::delete('tokens/{token}', [AuthController::class, 'deleteToken'])->whereNumber('token')->middleware('sanctum');

    });

});
