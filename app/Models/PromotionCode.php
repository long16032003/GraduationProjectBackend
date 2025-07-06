<?php

namespace App\Models;

use App\Models\ModelFilters\PromotionCodeFilter;
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
    public function modelFilter()
    {
        return $this->provideFilter(PromotionCodeFilter::class);
    }
}
