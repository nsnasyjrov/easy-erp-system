<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerifyController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1');

    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::get('email/verify/{id}/{hash}', [EmailVerifyController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::middleware('auth:sanctum')->group(function() {

        Route::get('profile', [AuthController::class, 'profile']);

        Route::delete('logout', [AuthController::class, 'logout']);

        Route::delete('logout-all', [AuthController::class, 'logoutAll']);

        Route::get('tokens', [AuthController::class, 'tokens']);

        Route::delete('tokens/{token}', [AuthController::class, 'deleteToken'])->whereNumber('token')->middleware('auth:sanctum');

    });

});

