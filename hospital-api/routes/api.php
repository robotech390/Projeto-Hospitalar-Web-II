<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\MeuPerfilController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Equipe 1: Autenticação e Gerenciamento de Acesso
|--------------------------------------------------------------------------
*/

// ─── Rotas públicas ────────────────────────────────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::post('login',                          [AutenticacaoController::class, 'login']);
    Route::post('alterar-senha-primeiro-acesso',  [AutenticacaoController::class, 'alterarSenhaPrimeiroAcesso']);
    Route::post('esqueci-senha',                  [AutenticacaoController::class, 'esqueciSenha']);
    Route::post('redefinir-senha',                [AutenticacaoController::class, 'redefinirSenha']);
});

// ─── Rotas protegidas por JWT ──────────────────────────────────────────────────

Route::middleware('jwt.auth')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('logout',         [AutenticacaoController::class, 'logout']);
        Route::post('alterar-senha',  [AutenticacaoController::class, 'alterarSenha']);
        Route::get('me',              [AutenticacaoController::class, 'me']);
    });

    // Meu Perfil — endpoints do próprio usuário sobre si mesmo
    Route::prefix('meu-perfil')->group(function () {
        Route::get('/',         [MeuPerfilController::class, 'meusDados']);
        Route::put('/',         [MeuPerfilController::class, 'atualizarMeusDados']);
        Route::get('agenda',    [MeuPerfilController::class, 'minhaAgenda']);
        Route::get('historico', [MeuPerfilController::class, 'meuHistorico']);
    });

    // Usuários
    Route::post('usuarios/registrar',                [UsuarioController::class, 'registrar']);
    Route::post('usuarios/{id}/reenviar-senha',      [UsuarioController::class, 'reenviarSenha']);
    Route::apiResource('usuarios', UsuarioController::class)->except(['store']);

    // Médicos
    Route::apiResource('medicos', MedicoController::class);
    Route::get('medicos/{id}/agenda', [AgendaController::class, 'porMedico']);

    // Agenda
    Route::apiResource('agenda', AgendaController::class);

    // Logs
    Route::get('logs',  [LogController::class, 'index']);
    Route::post('logs', [LogController::class, 'store']);
});