<?php

use App\Http\Controllers\Clients\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    /**
     * TODO: В дальнейшем команда должна вернуть список компаний, доступных пользователю, в зависимости от уровня досутпа
     */
    echo '<br>Companies:';
});

// Create client entity
Route::post('register_client', [ClientController::class, 'store']);

// Update client entity
Route::put('update_client', [ClientController::class, 'update']);

// Delete client entity
Route::post('delete_client', [ClientController::class, 'destroy']);
