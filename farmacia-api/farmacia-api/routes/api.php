<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;

// Rota pública por enquanto. Depois teremos que proteger com o middleware do JWT.
Route::get('/dashboard', [DashboardController::class, 'index']);