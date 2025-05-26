<?php

use App\Http\Controllers\Web\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/orders', [OrderController::class, 'index']);




