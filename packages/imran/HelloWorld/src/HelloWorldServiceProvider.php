<?php 
namespace Imran\HelloWorld;

use Illuminate\Support\ServiceProvider;

class HelloWorldServiceProvider extends ServiceProvider
{
    public function boot()
{
    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    $this->loadViewsFrom(__DIR__.'/../resources/views', 'hello');

    $this->publishes([
        __DIR__.'/../config/hello-world.php' => config_path('hello-world.php'),
    ], 'config');
}

public function register()
{
    $this->mergeConfigFrom(
        __DIR__.'/../config/hello-world.php',
        'hello-world'
    );

    $this->app->bind(HelloWorld::class, function () {
        return new HelloWorld();
    });
}

}
