<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConsultaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/consultas', [ConsultaController::class, 'index'])->name('consultas.index');//retorna a view com a lista de consultas
Route::get('/consultas/form', [ConsultaController::class, 'create'])->name('consultas.form');//retorna a view com o formulário para criar uma nova consulta
Route::post('/consultas', [ConsultaController::class, 'store'])->name('consultas.store');//recebe os dados do formulário e salva a nova consulta no banco de dados
Route::delete('/consultas/{consulta}', [ConsultaController::class, 'destroy'])->name('consultas.destroy');//recebe o id da consulta a ser deletada e remove do banco de dados
Route::get('/consultas/{consulta}', [ConsultaController::class, 'show'])->name('consultas.show');//recebe o id da consulta e retorna a view com os detalhes da consulta
Route::get('/consultas/{consulta}/edit', [ConsultaController::class, 'edit'])->name('consultas.edit');//recebe o id da consulta e retorna a view com o formulário para editar a consulta
Route::put('/consultas/{consulta}', [ConsultaController::class, 'update'])->name('consultas.update');//recebe os dados do formulário de edição e atualiza a consulta no banco de dados