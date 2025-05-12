<?php


use Illuminate\Support\Facades\Route;

use Imran\Test\Test;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function(){
   
    return app(Test::class)->sayHello();
    
});




