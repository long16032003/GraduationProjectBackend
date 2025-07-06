<?php

namespace App\Models;

use App\Models\ModelFilters\DishFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use Filterable;

    protected $table = 'dishes';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'description',
        'image_id',
        'price',
        'category_id',
        'creator_id',
        'is_active',
        'is_featured',
    ];

    public function dishCategories()
    {
        return $this->belongsTo(DishCategory::class, 'category_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(DishFilter::class);
    }
}
