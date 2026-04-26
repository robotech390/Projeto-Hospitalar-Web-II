<?php

use App\Http\Controllers\ProfileController;
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


// Rotas do módulo Laboratório e Exames
Route::get('/lab/dashboard', [\App\Http\Controllers\LabModuleController::class, 'dashboard'])->name('lab.dashboard');
Route::get('/lab/exams', [\App\Http\Controllers\LabModuleController::class, 'examCatalog'])->name('lab.exams');
Route::post('/lab/exams', [\App\Http\Controllers\LabModuleController::class, 'storeExam'])->name('lab.exams.store');
Route::patch('/lab/exams/{id}', [\App\Http\Controllers\LabModuleController::class, 'updateExam'])->name('lab.exams.update');
Route::delete('/lab/exams/{id}', [\App\Http\Controllers\LabModuleController::class, 'deleteExam'])->name('lab.exams.delete');
Route::get('/lab/collection-queue', [\App\Http\Controllers\LabModuleController::class, 'collectionQueue'])->name('lab.collectionQueue');
Route::get('/lab/result-entry', [\App\Http\Controllers\LabModuleController::class, 'resultEntryForm'])->name('lab.resultEntry');
Route::post('/lab/result-entry/{id}', [\App\Http\Controllers\LabModuleController::class, 'updateResultEntry'])->name('lab.resultEntry.update');
Route::get('/lab/exam-status', [\App\Http\Controllers\LabModuleController::class, 'examStatus'])->name('lab.examStatus');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';
