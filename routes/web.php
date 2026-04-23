<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Recepcao\AgendamentoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/faturamento/convenio', function () {
        return Inertia::render('Faturamento/Convenio', ['convenios' => []]);
    })->name('faturamento.convenio');


    Route::prefix('recepcao')->name('recepcao.')->group(function () {
        Route::get('/agendamento', [AgendamentoController::class, 'index'])->name('agendamento');
        Route::get('/medicos', [AgendamentoController::class, 'medicos'])->name('medicos');
        Route::get('/pacientes', [AgendamentoController::class, 'pacientes'])->name('pacientes');
        Route::get('/disponibilidade', [AgendamentoController::class, 'disponibilidade'])->name('disponibilidade');
        Route::post('/agendamento', [AgendamentoController::class, 'store'])->name('agendamento.store');
        Route::delete('/agendamento/{id}', [AgendamentoController::class, 'destroy'])->name('agendamento.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
