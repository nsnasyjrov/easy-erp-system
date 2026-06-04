<?php

use App\Http\Controllers\Clients\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClientController::class, 'index']);
Route::get('index', [ClientController::class, 'index']);


// Create client entity
Route::post('register_client', [ClientController::class, 'store']);

// Read client entity
Route::get('show/{client_id}', [ClientController::class, 'show']);

// Update client entity
Route::put('update_client', [ClientController::class, 'update']);

// Delete client entity
Route::post('delete_client', [ClientController::class, 'destroy']);

//Get contacts
Route::get('{client_id}/contacts', [ClientController::class, 'contacts']);
// Add contact data
Route::post("{client_id}/contacts", [ClientController::class, 'ensureClientContacts']);
