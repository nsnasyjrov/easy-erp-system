<?php

use App\Http\Controllers\Clients\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    /**
     * TODO: В дальнейшем команда должна вернуть список компаний, доступных пользователю, в зависимости от уровня досутпа
     */
    echo '<br>Companies:';
});

Route::post('register_client', [ClientController::class, 'store']);
