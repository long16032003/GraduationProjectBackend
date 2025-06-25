<?php

namespace App\Models;

use App\Models\ModelFilters\EnterIngredientDetailFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class EnterIngredientDetail extends Model
{
    use Filterable;
    public $table ="enter_ingredient_details";
    public $timestamps = true;
    public $fillable = [
        'enter_ingredient_id',
        'ingredient_id',
        'quantity',
        'unit_price',
        'supplier_name',
        'created_at',
        'updated_at',
    ];

    public function enterIngredient()
    {
        return $this->belongsTo(EnterIngredient::class, 'enter_ingredient_id', 'id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(EnterIngredientDetailFilter::class);
    }
}
