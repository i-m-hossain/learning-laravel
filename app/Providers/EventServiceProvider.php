<?php

namespace App\Providers;

use App\Events\LowStockDetected;
use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Listeners\NotifyInventoryManager;
use App\Listeners\NotifyShippingDepartment;
use App\Listeners\SendOrderConfirmationEmail;
use App\Listeners\UpdateInventory;
use App\Models\Order;
use App\Models\Product;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
   protected $listen = [
        OrderCreated::class => [
            SendOrderConfirmationEmail::class,
            UpdateInventory::class,
        ],
        OrderStatusChanged::class => [
            NotifyShippingDepartment::class,
        ],
        LowStockDetected::class => [
            NotifyInventoryManager::class,
        ],
    ];

    public function boot(){}
}
