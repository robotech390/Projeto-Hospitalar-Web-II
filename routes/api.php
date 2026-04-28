<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConsultaController;
use App\Http\Controllers\Api\DiagnosticoController;
use App\Http\Controllers\Api\ReceitaController;
use App\Http\Controllers\Api\SolicitacaoExameController;

/*
|--------------------------------------------------------------------------
| Grupo 3 — Prontuário Eletrônico do Paciente
| Todas as rotas exigem o token JWT do Grupo 1 via middleware 'auth.jwt'
|--------------------------------------------------------------------------
*/

Route::middleware('auth.jwt')->group(function () {

    Route::get('home', function () {
        return view('home');
    });

    // ── CONSULTAS ─────────────────────────────────────────────────────────────
    Route::get('consultas/fila/hoje',              [ConsultaController::class, 'fila']);
    Route::get('pacientes/{idPaciente}/historico', [ConsultaController::class, 'historico']);
    Route::apiResource('consultas', ConsultaController::class);

    // ── DIAGNÓSTICOS ──────────────────────────────────────────────────────────
    Route::prefix('consultas/{idConsulta}')->group(function () {
        Route::get('diagnosticos',         [DiagnosticoController::class, 'index']);
        Route::post('diagnosticos',        [DiagnosticoController::class, 'store']);
        Route::get('diagnosticos/{id}',    [DiagnosticoController::class, 'show']);
        Route::put('diagnosticos/{id}',    [DiagnosticoController::class, 'update']);
        Route::delete('diagnosticos/{id}', [DiagnosticoController::class, 'destroy']);
    });

    // ── RECEITAS ──────────────────────────────────────────────────────────────
    Route::prefix('consultas/{idConsulta}')->group(function () {
        Route::get('receitas',                               [ReceitaController::class, 'index']);
        Route::post('receitas',                              [ReceitaController::class, 'store']);
        Route::get('receitas/{id}',                          [ReceitaController::class, 'show']);
        Route::put('receitas/{id}',                          [ReceitaController::class, 'update']);
        Route::delete('receitas/{id}',                       [ReceitaController::class, 'destroy']);
        Route::post('receitas/{id}/medicamentos',            [ReceitaController::class, 'adicionarMedicamento']);
        Route::delete('receitas/{id}/medicamentos/{idItem}', [ReceitaController::class, 'removerMedicamento']);
    });

    // ── SOLICITAÇÕES DE EXAME ─────────────────────────────────────────────────
    Route::prefix('consultas/{idConsulta}')->group(function () {
        Route::get('solicitacoes-exame',                          [SolicitacaoExameController::class, 'index']);
        Route::post('solicitacoes-exame',                         [SolicitacaoExameController::class, 'store']);
        Route::get('solicitacoes-exame/{id}',                     [SolicitacaoExameController::class, 'show']);
        Route::put('solicitacoes-exame/{id}',                     [SolicitacaoExameController::class, 'update']);
        Route::delete('solicitacoes-exame/{id}',                  [SolicitacaoExameController::class, 'destroy']);
        Route::post('solicitacoes-exame/{id}/itens',              [SolicitacaoExameController::class, 'adicionarItem']);
        Route::delete('solicitacoes-exame/{id}/itens/{idItem}',   [SolicitacaoExameController::class, 'removerItem']);
    });

    // ── RESULTADOS DE EXAME (leitura do Grupo 5) ──────────────────────────────
    Route::get('pacientes/{idPaciente}/resultados-exame', [SolicitacaoExameController::class, 'resultadosPaciente']);
});
