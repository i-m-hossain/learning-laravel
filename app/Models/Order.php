<?php

namespace App\Models;

use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id', 
        'total_amount', 
        'status', 
        'payment_status',
        'shipping_address',
        'billing_address',
        'payment_method'
    ];
    
    protected $dispatchesEvents = [
        'created' => OrderCreated::class,
        'updated' => OrderStatusChanged::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

}
