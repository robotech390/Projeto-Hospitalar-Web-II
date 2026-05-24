<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConsultaController;
use App\Http\Controllers\Api\DiagnosticoController;
use App\Http\Controllers\Api\SolicitacaoExameController;
use App\Http\Controllers\Api\ReceitaController;

Route::get('/', function () {
    return view('welcome');
});

// ========== CONSULTAS ==========
Route::get('/consultas', [ConsultaController::class, 'lista'])->name('consultas.index');
Route::get('/consultas/form', [ConsultaController::class, 'formulario'])->name('consultas.form');
Route::get('/consultas/form/{consulta}', [ConsultaController::class, 'formulario'])->name('consultas.create');
Route::post('/consultas', [ConsultaController::class, 'salvar'])->name('consultas.store');
Route::delete('/consultas/{consulta}', [ConsultaController::class, 'remover'])->name('consultas.destroy');
Route::get('/consultas/{consulta}', [ConsultaController::class, 'mostrar'])->name('consultas.show');
Route::get('/consultas/{consulta}/edit', [ConsultaController::class, 'editar'])->name('consultas.edit');
Route::put('/consultas/{consulta}', [ConsultaController::class, 'atualizar'])->name('consultas.update');

// ========== DIAGNÓSTICOS ==========
Route::get('/diagnosticos', [DiagnosticoController::class, 'lista'])->name('diagnosticos.index');
Route::get('/diagnosticos/form', [DiagnosticoController::class, 'formulario'])->name('diagnosticos.form');
Route::get('/diagnosticos/form/{consulta}', [DiagnosticoController::class, 'formulario'])->name('diagnosticos.create');
Route::post('/diagnosticos', [DiagnosticoController::class, 'salvar'])->name('diagnosticos.store');
Route::delete('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'remover'])->name('diagnosticos.destroy');
Route::get('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'mostrar'])->name('diagnosticos.show');
Route::get('/diagnosticos/{diagnostico}/edit', [DiagnosticoController::class, 'editar'])->name('diagnosticos.edit');
Route::put('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'atualizar'])->name('diagnosticos.update');

// ========== RECEITAS ==========
Route::get('/receitas', [ReceitaController::class, 'lista'])->name('receitas.index');
Route::get('/receitas/form', [ReceitaController::class, 'formulario'])->name('receitas.form');
Route::get('/receitas/form/{consulta}', [ReceitaController::class, 'formulario'])->name('receitas.create');
Route::post('/receitas', [ReceitaController::class, 'salvar'])->name('receitas.store');
Route::delete('/receitas/{receita}', [ReceitaController::class, 'remover'])->name('receitas.destroy');
Route::get('/receitas/{receita}', [ReceitaController::class, 'mostrar'])->name('receitas.show');
Route::get('/receitas/{receita}/edit', [ReceitaController::class, 'editar'])->name('receitas.edit');
Route::put('/receitas/{receita}', [ReceitaController::class, 'atualizar'])->name('receitas.update');

// ========== SOLICITAÇÕES DE EXAME ==========
Route::get('/solicitacoes-exame', [SolicitacaoExameController::class, 'lista'])->name('solicitacoesExame.index');
Route::get('/solicitacoes-exame/form', [SolicitacaoExameController::class, 'formulario'])->name('solicitacoesExame.form');
Route::get('/solicitacoes-exame/form/{consulta}', [SolicitacaoExameController::class, 'formulario'])->name('solicitacoesExame.create');
Route::post('/solicitacoes-exame', [SolicitacaoExameController::class, 'salvar'])->name('solicitacoesExame.store');
Route::delete('/solicitacoes-exame/{solicitacao}', [SolicitacaoExameController::class, 'remover'])->name('solicitacoesExame.destroy');
Route::get('/solicitacoes-exame/{solicitacao}', [SolicitacaoExameController::class, 'mostrar'])->name('solicitacoesExame.show');
Route::get('/solicitacoes-exame/{solicitacao}/edit', [SolicitacaoExameController::class, 'editar'])->name('solicitacoesExame.edit');
Route::put('/solicitacoes-exame/{solicitacao}', [SolicitacaoExameController::class, 'atualizar'])->name('solicitacoesExame.update');