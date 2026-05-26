<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AutenticacaoController::class, 'login']);
    Route::post('alterar-senha-primeiro-acesso', [AutenticacaoController::class, 'alterarSenhaPrimeiroAcesso']);
    Route::post('esqueci-senha', [AutenticacaoController::class, 'esqueciSenha']);
    Route::post('redefinir-senha', [AutenticacaoController::class, 'redefinirSenha']);
});

Route::middleware('jwt.auth')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('logout',         [AutenticacaoController::class, 'logout']);
        Route::post('alterar-senha',  [AutenticacaoController::class, 'alterarSenha']);
        Route::get('me',              [AutenticacaoController::class, 'me']);
    });

    Route::post('usuarios/registrar', [UsuarioController::class, 'registrar']);
    Route::apiResource('usuarios', UsuarioController::class)->except(['store']);

    Route::apiResource('medicos', MedicoController::class);

    Route::get('medicos/{id}/agenda', [AgendaController::class, 'porMedico']);

    Route::apiResource('agenda', AgendaController::class);

    Route::get('logs',  [LogController::class, 'index']);
    Route::post('logs', [LogController::class, 'store']);
});