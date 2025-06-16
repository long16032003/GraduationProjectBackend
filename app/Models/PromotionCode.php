<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class PromotionCode extends Model
{
    use Filterable;
    protected $table = 'promotion_codes';
    public $timestamps = true;
    protected $fillable = [
        'code',
        'promotion_id',
        'customer_id',
        'used_at',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
