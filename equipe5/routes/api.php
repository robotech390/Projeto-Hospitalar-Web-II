<?php

use App\Http\Controllers\Api\SolicitacaoExameController;
use App\Http\Controllers\Api\TipoExameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('tipos-exame', TipoExameController::class);

Route::apiResource('solicitacoes', SolicitacaoExameController::class);