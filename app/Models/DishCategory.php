<?php

namespace App\Models;

use App\Models\ModelFilters\DishCategoryFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class DishCategory extends Model
{
    use Filterable;
    protected $table = 'dish_categories';
    public $timestamps = true;

    protected $fillable = [
        'creator_id',
        'name',
        'description',
    ];

    protected $attributes = [
        'creator_id' => 0,
        'name' => '',
        'description' => '',
    ];

    protected $casts = [
        'creator_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(DishCategoryFilter::class);
    }
}