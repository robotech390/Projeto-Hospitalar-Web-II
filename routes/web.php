<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/consultas', function () {
    return view('consultas');
});
Route::get('/consultas/form', function () {
    return view('consultaForm');
});