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
        'required_points',
        'limit_per_user_count',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    public function modelFilter()
    {
        return $this->provideFilter(PromotionFilter::class);
    }
}
