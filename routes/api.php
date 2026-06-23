<?php

use Illuminate\Http\Request;
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
/*
Entrada: 
    Lê agendamentos do Grupo 2: 
    Lê resultados de exames do Grupo 5: GET/api/itens-exame | GET/api/itens-exame/{id}
Saída: 
    Envia a receita para o Grupo 4: GET/api/receitas | GET/api/receitas/{id}
    pedido de exame para o Grupo 5: GET/api/solicitacoes-exame | GET/api/solicitacoes-exame/{id}
    ?Grupo 4 recebe a Prescrição digital do grupo 3
*/

Route::middleware('auth.jwt')->group(function () {

    Route::get('/user', function (Request $request) {
        return response()->json($request->attributes->get('authenticated_user'));
    });

    // -- PRONTUÁRIO ----------------------------------------------------
    Route::get('receitas', [ReceitaController::class, 'all']);
    Route::get('receitas/{id}', [ReceitaController::class, 'show']);

    Route::get('solicitacoes-exame', [SolicitacaoExameController::class, 'all']);
    Route::get('solicitacoes-exame/{id}', [SolicitacaoExameController::class, 'show']);

    Route::get('diagnosticos', [DiagnosticoController::class, 'all']);
    Route::get('diagnosticos/{id}', [DiagnosticoController::class, 'show']);

    Route::get('consultas/pacientes/hoje', [ConsultaController::class, 'pacientesHoje']);
    Route::get('consultas', [ConsultaController::class, 'all']);
    Route::get('consultas/{id}', [ConsultaController::class, 'show']);
    
});
