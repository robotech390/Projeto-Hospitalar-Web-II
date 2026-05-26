<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Equipe 1: Autenticação e Gerenciamento de Acesso
|--------------------------------------------------------------------------
|
| Todas as rotas retornam JSON.
| Rotas protegidas exigem: Authorization: Bearer {token}
|
| Fluxo de integração para outras equipes:
|  1. Faça POST /auth/login com email + senha → receba o token
|  2. Use o token no header Authorization para as demais chamadas
|  3. Para criar um usuário, chame POST /usuarios/registrar
|  4. Para registrar uma ação no log, chame POST /logs
|
*/

// ─── Rotas públicas (sem autenticação) ────────────────────────────────────────

Route::prefix('auth')->group(function () {
    // Login — ponto de entrada para todas as equipes
    Route::post('login', [AutenticacaoController::class, 'login']);

    // Troca de senha no primeiro acesso (não exige token, exige senha temporária)
    Route::post('alterar-senha-primeiro-acesso', [AutenticacaoController::class, 'alterarSenhaPrimeiroAcesso']);

    // Fluxo do Grupo 1: Recuperação de senha por código no e-mail
    Route::post('esqueci-senha', [AutenticacaoController::class, 'esqueciSenha']);
    Route::post('redefinir-senha', [AutenticacaoController::class, 'redefinirSenha']);
});

// ─── Rotas protegidas por JWT ──────────────────────────────────────────────────

Route::middleware('jwt.auth')->group(function () {

    // ── Autenticação ──────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('logout',         [AutenticacaoController::class, 'logout']);
        Route::post('alterar-senha',  [AutenticacaoController::class, 'alterarSenha']);
        Route::get('me',              [AutenticacaoController::class, 'me']);
    });

    // ── Usuários ──────────────────────────────────────────────────────────────
    // POST /usuarios/registrar deve vir antes do resource para não conflitar com /usuarios/{id}
    Route::post('usuarios/registrar', [UsuarioController::class, 'registrar']);
    Route::apiResource('usuarios', UsuarioController::class)->except(['store']);

    // ── Médicos ───────────────────────────────────────────────────────────────
    // Modificado para apiResource de médicos
    Route::apiResource('medicos', MedicoController::class);

    // Agenda de um médico específico (atalho para o Grupo 2)
    Route::get('medicos/{id}/agenda', [AgendaController::class, 'porMedico']);

    // ── Agenda (plantões) ─────────────────────────────────────────────────────
    Route::apiResource('agenda', AgendaController::class);

    // ── Logs ──────────────────────────────────────────────────────────────────
    Route::get('logs',  [LogController::class, 'index']);
    Route::post('logs', [LogController::class, 'store']);
});