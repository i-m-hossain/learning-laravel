<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get("/blogs", [BlogController::class, 'index']);
Route::post('/payment/{gateway}', [PaymentController::class, 'handlePayment']);
