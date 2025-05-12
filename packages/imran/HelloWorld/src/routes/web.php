<?php

use Illuminate\Support\Facades\Route;
use Imran\HelloWorld\Http\Controllers\HelloController;

Route::get('/hello-package', [HelloController::class, 'index']);