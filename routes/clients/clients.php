<?php

use App\Http\Controllers\Clients\ClientController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->prefix('clients')->group(function () {

    Route::get('/', [ClientController::class, 'index']);

    Route::post('/', [ClientController::class, 'store']);

    Route::get('{client}', [ClientController::class, 'show'])->whereNumber('client')->can('view', 'client');

    Route::patch('{client}', [ClientController::class, 'update'])->whereNumber('client');

    Route::delete('{client}', [ClientController::class, 'destroy'])->whereNumber('client')->can('delete', 'client');

    Route::get('{client}/contacts', [ClientController::class, 'contacts'])
        ->whereNumber('client')->can('viewContacts', 'client');

    Route::post("{client}/contacts", [ClientController::class, 'ensureClientContacts'])
        ->whereNumber('client')->can('createContact', 'client');

    Route::put('{client}/responsible_manager', [ClientController::class, 'setResponsibleManager'])
        ->whereNumber('client');
});


