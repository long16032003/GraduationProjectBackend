<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class OrderDish extends Model
{
    use Filterable;
    protected $table = 'order_dishes';
    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'dish_id',
        'quantity',
        'price_at_order_time',
        'cancelled_reason',
        'cancelled_by',
        'cancelled_at',
        'is_available',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'is_available' => 'boolean',
        'price_at_order_time' => 'decimal:2',
    ];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function dish(){
        return $this->belongsTo(Dish::class, 'dish_id', 'id');
    }

    public function cancelledBy(){
        return $this->belongsTo(User::class, 'cancelled_by', 'id');
    }
}
