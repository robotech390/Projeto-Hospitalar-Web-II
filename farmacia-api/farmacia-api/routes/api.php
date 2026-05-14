<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MedicamentoController;
use App\Http\Controllers\Api\TipoMedicamentoController;
use App\Http\Controllers\Api\DispensacaoController;
use App\Http\Controllers\Api\NotaFiscalController;

Route::post('/notas-fiscais', [NotaFiscalController::class, 'store']);
Route::get('/dashboard', [DashboardController::class, 'index']);

// Rotas do Catálogo
Route::get('/medicamentos', [MedicamentoController::class, 'index']);
Route::post('/medicamentos', [MedicamentoController::class, 'store']);
Route::put('/medicamentos/{id}', [MedicamentoController::class, 'update']);

Route::get('/tipos', [TipoMedicamentoController::class, 'index']);
Route::post('/tipos', [TipoMedicamentoController::class, 'store']);

// ROTAS DE DISPENSAÇÃO (AQUI ESTAVA O SEU ERRO)
Route::get('/lotes-disponiveis', [DispensacaoController::class, 'index']);
Route::get('/lote/{id}', [DispensacaoController::class, 'show']); // <- ESTA LINHA FALTAVA
Route::post('/dispensacao', [DispensacaoController::class, 'store']);   