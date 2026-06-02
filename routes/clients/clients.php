<?php

use App\Http\Controllers\Clients\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClientController::class, 'index']);
Route::get('index', [ClientController::class, 'index']);

Route::get('show/{client_id}', [ClientController::class, 'show']);

// Create client entity
Route::post('register_client', [ClientController::class, 'store']);

// Update client entity
Route::put('update_client', [ClientController::class, 'update']);

// Delete client entity
Route::post('delete_client', [ClientController::class, 'destroy']);
