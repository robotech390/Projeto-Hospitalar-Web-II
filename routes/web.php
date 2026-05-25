<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConvenioController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\TipoCobrancaController;
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/faturamento/convenio', [ConvenioController::class, 'index'])->name('convenio');
    Route::post('/faturamento/convenio', [ConvenioController::class, 'store']);
    Route::put('/faturamento/convenio/{convenio}', [ConvenioController::class, 'update']);
    Route::delete('/faturamento/convenio/{convenio}', [ConvenioController::class, 'destroy']);

    Route::get('/faturamento/plano', [PlanoController::class, 'index'])->name('plano');
    Route::post('/faturamento/plano', [PlanoController::class, 'store']);
    Route::put('/faturamento/plano/{plano}', [PlanoController::class, 'update']);
    Route::delete('/faturamento/plano/{plano}', [PlanoController::class, 'destroy']);

    Route::get('/faturamento/tipo-cobranca', [TipoCobrancaController::class, 'index'])->name('tipo-cobranca');
    Route::post('/faturamento/tipo-cobranca', [TipoCobrancaController::class, 'store']);
    Route::put('/faturamento/tipo-cobranca/{tipoCobranca}', [TipoCobrancaController::class, 'update']);
    Route::delete('/faturamento/tipo-cobranca/{tipoCobranca}', [TipoCobrancaController::class, 'destroy']);
});

require __DIR__ . '/auth.php';