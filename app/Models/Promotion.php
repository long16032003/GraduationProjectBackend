<?php

namespace App\Models;

use App\Models\ModelFilters\PromotionFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    //
    use Filterable;
    protected $table = 'promotions';
    public $timestamps = true;
    protected $fillable = [
        'creator_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'discount_percentage',
        'discount_amount',
        'discount_type',
        'min_order_amount',
        'max_discount_amount',
        'required_points',
        'image_id',
        'limit',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
    public function promotion_codes()
    {
        return $this->hasMany(PromotionCode::class);
    }

    public function modelFilter()
    {
        return $this->provideFilter(PromotionFilter::class);
    }
}
