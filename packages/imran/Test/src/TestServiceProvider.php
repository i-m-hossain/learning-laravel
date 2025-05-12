<?php 
namespace Imran\Test;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes, views, migrations, etc
    }

    public function register()
    {
        $this->app->bind(Test::class, function () {
            return new Test();
        });
    }
}
