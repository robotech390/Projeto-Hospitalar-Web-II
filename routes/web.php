<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConsultaController;
use App\Http\Controllers\Api\DiagnosticoController;

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

Route::get('/diagnosticos', [DiagnosticoController::class, 'diagnosticos'])->name('diagnosticos.index');//retorna a view com a lista de diagnósticos
Route::get('/diagnosticos/form', [DiagnosticoController::class, 'diagnosticoForm'])->name('diagnosticos.form');//retorna a view com o formulário para criar um novo diagnóstico
Route::get('/diagnosticos/form/{consulta}', [DiagnosticoController::class, 'diagnosticoForm'])->name('diagnosticos.create');//retorna a view com o formulário para criar um novo diagnóstico já vinculado à consulta
Route::post('/diagnosticos', [DiagnosticoController::class, 'diagnosticoStore'])->name('diagnosticos.store');//recebe os dados do formulário e salva o novo diagnóstico no banco de dados
Route::get('/diagnosticos/{diagnostico}/edit', [DiagnosticoController::class, 'diagnosticoEdit'])->name('diagnosticos.edit');//recebe o id do diagnóstico e retorna a view com o formulário para editar o diagnóstico
Route::put('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'diagnosticoUpdate'])->name('diagnosticos.update');//recebe os dados do formulário de edição e atualiza o diagnóstico

Route::get('/prontuario/solicitacoes-exame', [ConsultaController::class, 'solicitacoesExame'])->name('solicitacoesExame.index');//retorna a view com a lista de solicitações de exame
Route::get('/prontuario/solicitacoes-exame/form', [ConsultaController::class, 'solicitacaoExameForm'])->name('solicitacoesExame.form');//retorna a view com o formulário para criar uma nova solicitação de exame
Route::get('/prontuario/solicitacoes-exame/form/{consulta}', [ConsultaController::class, 'solicitacaoExameForm'])->name('solicitacoesExame.create');//retorna a view com o formulário para criar uma nova solicitação de exame já vinculada à consulta
Route::post('/prontuario/solicitacoes-exame', [ConsultaController::class, 'solicitacaoExameStore'])->name('solicitacoesExame.store');//recebe os dados do formulário e  salva a nova solicitação de exame no banco de dados
Route::get('/prontuario/solicitacoes-exame/{solicitacao}/edit', [ConsultaController::class, 'solicitacaoExameEdit'])->name('solicitacoesExame.edit');//recebe o id da solicitação de exame e retorna a view com o formulário para editar a solicitação de exame
Route::put('/prontuario/solicitacoes-exame/{solicitacao}', [ConsultaController::class, 'solicitacaoExameUpdate'])->name('solicitacoesExame.update');//recebe os dados do formulário de edição e atualiza a solicitação de exame no banco de dados
Route::delete('/prontuario/solicitacoes-exame/{solicitacao}', [ConsultaController::class, 'solicitacaoExameDestroy'])->name('solicitacoesExame.destroy');//recebe o id da solicitação de exame a ser deletada e remove do banco de dados