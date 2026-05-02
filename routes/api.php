<?php

use Illuminate\Support\Facades\Route;

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
});
