<?php

namespace App\Models;

use App\Models\ModelFilters\ExportIngredientDetailFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class ExportIngredientDetail extends Model
{
    use Filterable;
    public $table ="export_ingredient_details";
    public $timestamps = true;
    public $fillable = [
        'export_ingredient_id',
        'ingredient_id',
        'quantity',
        'created_at',
        'updated_at',
    ];

    public function exportIngredient()
    {
        return $this->belongsTo(ExportIngredient::class, 'export_ingredient_id', 'id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(ExportIngredientDetailFilter::class);
    }
}
