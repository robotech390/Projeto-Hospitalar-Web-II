<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('lab.dashboard');
});


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
Route::get('/lab/solicitations', [\App\Http\Controllers\LabModuleController::class, 'examSolicitations'])->name('lab.solicitations');
Route::post('/lab/solicitations', [\App\Http\Controllers\LabModuleController::class, 'storeSolicitation'])->name('lab.solicitations.store');
Route::post('/lab/solicitations/update/{id}', [\App\Http\Controllers\LabModuleController::class, 'updateSolicitation'])->name('lab.solicitations.update');
Route::post('/lab/solicitations/delete/{id}', [\App\Http\Controllers\LabModuleController::class, 'deleteSolicitation'])->name('lab.solicitations.delete');
