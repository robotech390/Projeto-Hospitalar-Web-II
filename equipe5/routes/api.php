<?php

use App\Http\Controllers\Api\ItemExameController;
use App\Http\Controllers\Api\SolicitacaoExameController;
use App\Http\Controllers\Api\TipoExameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth.jwt')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->attributes->get('authenticated_user'));
    });

    Route::apiResource('tipos-exame', TipoExameController::class);
    Route::apiResource('solicitacoes', SolicitacaoExameController::class);
    Route::apiResource('itens-exame', ItemExameController::class);
});