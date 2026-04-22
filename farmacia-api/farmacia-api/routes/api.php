<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MedicamentoController;
use App\Http\Controllers\Api\TipoMedicamentoController;
use App\Http\Controllers\Api\DispensacaoController;

Route::get('/dashboard', [DashboardController::class, 'index']);

// Rotas oficiais do Catálogo
Route::get('/medicamentos', [MedicamentoController::class, 'index']);
Route::post('/medicamentos', [MedicamentoController::class, 'store']);
Route::get('/tipos', [TipoMedicamentoController::class, 'index']);
Route::post('/tipos', [TipoMedicamentoController::class, 'store']);
Route::get('/lotes-disponiveis', [DispensacaoController::class, 'index']);
Route::post('/dispensacao', [DispensacaoController::class, 'store']);
Route::put('/medicamentos/{id}', [MedicamentoController::class, 'update']);