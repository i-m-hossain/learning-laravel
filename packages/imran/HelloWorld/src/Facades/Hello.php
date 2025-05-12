<?php

namespace Imran\HelloWorld\Facades;

use Illuminate\Support\Facades\Facade;
use Imran\HelloWorld\HelloWorld;

class Hello extends Facade
{
    protected static function getFacadeAccessor()
    {
        return HelloWorld::class;// This should match the binding key from your ServiceProvider
    }
}
