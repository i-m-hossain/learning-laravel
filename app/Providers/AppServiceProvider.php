<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use App\Services\Discount\DiscountCalculator;
use App\Services\Discount\Strategies\NoDiscountStrategy;
use App\Services\LoggerService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoggerService::class, function ($app) {
            return LoggerService::getInstance();
        });

        $this->app->bind(DiscountCalculator::class, function ($app) {
            return new DiscountCalculator(new NoDiscountStrategy());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        AliasLoader::getInstance()->alias('Hello', \Imran\HelloWorld\Facades\Hello::class);
    }
}
