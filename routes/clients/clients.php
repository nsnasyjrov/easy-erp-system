<?php

use App\Http\Controllers\Clients\ClientController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->prefix('clients')->group(function () {

    Route::get('/', [ClientController::class, 'index']);

// Create client entity
    Route::post('/', [ClientController::class, 'store']);

// Read client entity
    Route::get('{client}', [ClientController::class, 'show'])->whereNumber('client');

// Update client entity
    Route::patch('{client}', [ClientController::class, 'update'])->whereNumber('client');

// Delete client entity
    Route::delete('{client}', [ClientController::class, 'destroy'])->whereNumber('client');

//Get contacts
    Route::get('{client}/contacts', [ClientController::class, 'contacts'])->whereNumber('client');
// Add contact data
    Route::post("{client}/contacts", [ClientController::class, 'ensureClientContacts'])->whereNumber('client');

    Route::put('{client}/responsible_manager', [ClientController::class, 'setResponsibleManager'])
        ->whereNumber('client')->can('assignResponsibleManager', 'client');
});


