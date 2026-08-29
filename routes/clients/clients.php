<?php

use App\Http\Controllers\Clients\ClientController;
use App\Models\Client;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->prefix('clients')->group(function () {

    Route::get('/', [ClientController::class, 'index'])->can('viewAny', Client::class);

    Route::post('/', [ClientController::class, 'store'])->can('create', Client::class);

    Route::get('{client}', [ClientController::class, 'show'])->whereNumber('client')->can('view', 'client');

    Route::patch('{client}', [ClientController::class, 'update'])->whereNumber('client')->can('update', 'client');

    Route::delete('{client}', [ClientController::class, 'destroy'])->whereNumber('client')->can('delete', 'client');

    Route::get('{client}/contacts', [ClientController::class, 'contacts'])
        ->whereNumber('client')->can('viewContacts', 'client');

    Route::post("{client}/contacts", [ClientController::class, 'ensureClientContacts'])
        ->whereNumber('client')->can('createContact', 'client');

    Route::put('{client}/responsible_manager', [ClientController::class, 'setResponsibleManager'])
        ->whereNumber('client')->can('assignResponsibleManager', Client::class);
});


