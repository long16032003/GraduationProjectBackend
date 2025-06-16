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
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function dish(){
        return $this->belongsTo(Dish::class);
    }
}
