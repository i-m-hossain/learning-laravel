<?php

namespace Imran\HelloWorld\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class HelloController extends Controller
{
    public function index()
    {
        // Read from config
        $message = Config::get('hello-world.greeting', 'Hello from HelloController!');

        return view('hello::hello', compact('message'));
    }
}
