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

    // Top-level API endpoints (listas gerais)
    Route::get('solicitacoes-exame', [SolicitacaoExameController::class, 'all']);
    Route::get('receitas', [ReceitaController::class, 'all']);
    // -- CONSULTAS -----------------------------------------------------
    Route::get('consultas/fila/hoje',              [ConsultaController::class, 'fila']);
    Route::get('pacientes/{idPaciente}/historico', [ConsultaController::class, 'historico']);
    Route::apiResource('consultas', ConsultaController::class);

    // -- DIAGNÓSTICOS --------------------------------------------------
    Route::prefix('consultas/{idConsulta}')->group(function () {
        Route::get('diagnosticos',         [DiagnosticoController::class, 'index']);
        Route::post('diagnosticos',        [DiagnosticoController::class, 'store']);
        Route::get('diagnosticos/{id}',    [DiagnosticoController::class, 'show']);
        Route::put('diagnosticos/{id}',    [DiagnosticoController::class, 'update']);
        Route::delete('diagnosticos/{id}', [DiagnosticoController::class, 'destroy']);
    });

    // -- RECEITAS -----------------------------------------------------
    Route::prefix('consultas/{idConsulta}')->group(function () {
        Route::get('receitas',                               [ReceitaController::class, 'index']);
        Route::post('receitas',                              [ReceitaController::class, 'store']);
        Route::get('receitas/{id}',                          [ReceitaController::class, 'show']);
        Route::put('receitas/{id}',                          [ReceitaController::class, 'update']);
        Route::delete('receitas/{id}',                       [ReceitaController::class, 'destroy']);
        Route::post('receitas/{id}/medicamentos',            [ReceitaController::class, 'adicionarMedicamento']);
        Route::delete('receitas/{id}/medicamentos/{idItem}', [ReceitaController::class, 'removerMedicamento']);
    });

    // -- SOLICITAÇÕES DE EXAME -----------------------------------------------------
    Route::prefix('consultas/{idConsulta}')->group(function () {
        Route::get('solicitacoes-exame',                          [SolicitacaoExameController::class, 'index']);
        Route::post('solicitacoes-exame',                         [SolicitacaoExameController::class, 'store']);
        Route::get('solicitacoes-exame/{id}',                     [SolicitacaoExameController::class, 'show']);
        Route::put('solicitacoes-exame/{id}',                     [SolicitacaoExameController::class, 'update']);
        Route::delete('solicitacoes-exame/{id}',                  [SolicitacaoExameController::class, 'destroy']);
        Route::post('solicitacoes-exame/{id}/itens',              [SolicitacaoExameController::class, 'adicionarItem']);
        Route::delete('solicitacoes-exame/{id}/itens/{idItem}',   [SolicitacaoExameController::class, 'removerItem']);
    });
    /*
    Entrada: 
        Lê agendamentos do Grupo 2: 
        Lê resultados de exames do Grupo 5:
    Saída: 
        Envia a receita para o Grupo 4:
        pedido de exame para o Grupo 5: 
     * (essas rotas já estão definidas acima, dentro do prefixo 'consultas/{idConsulta}')
    */
});
