<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConsultaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/consultas', [ConsultaController::class, 'index'])->name('consultas.index');
Route::get('/consultas/form', [ConsultaController::class, 'create'])->name('consultas.form');
Route::post('/consultas', [ConsultaController::class, 'store'])->name('consultas.store');