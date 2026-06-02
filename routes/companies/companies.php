<?php

use App\Http\Controllers\Companies\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    /**
     * TODO: В дальнейшем команда должна вернуть список компаний, доступных пользователю, в зависимости от уровня досутпа
     */
    echo '<br>Companies:';
});

// Create company entity
Route::post('store_company', [CompanyController::class, 'store']);

//Update company entity
Route::put('update_company', [CompanyController::class, 'update']);

//Become to Client
Route::post('{company_id}/client', [CompanyController::class, 'storeClient']);
