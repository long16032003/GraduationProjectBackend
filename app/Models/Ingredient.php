<?php

namespace App\Models;

use App\Models\ModelFilters\IngredientFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use Filterable, SoftDeletes;
    public $table ="ingredients";
    public $timestamps = true;
    public $fillable = [
        'name',
        'unit',
        'creator_id',
        'image_id',
        'quantity',
        'min_quantity',
    ];

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function enterIngredientDetails()
    {
        return $this->hasMany(EnterIngredientDetail::class, 'ingredient_id', 'id');
    }

    public function exportIngredientDetails()
    {
        return $this->hasMany(ExportIngredientDetail::class, 'ingredient_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(IngredientFilter::class);
    }
}
