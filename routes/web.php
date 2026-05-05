<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConsultaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/consultas', [ConsultaController::class, 'index'])->name('consultas.index');
Route::get('/consultas/form', [ConsultaController::class, 'create'])->name('consultas.form');
Route::post('/consultas', [ConsultaController::class, 'store'])->name('consultas.store');
Route::delete('/consultas/{consulta}', [ConsultaController::class, 'destroy'])->name('consultas.destroy');
Route::get('/consultas/{consulta}', [ConsultaController::class, 'show'])->name('consultas.show');
Route::get('/consultas/{consulta}/edit', [ConsultaController::class, 'edit'])->name('consultas.edit');
Route::put('/consultas/{consulta}', [ConsultaController::class, 'update'])->name('consultas.update');