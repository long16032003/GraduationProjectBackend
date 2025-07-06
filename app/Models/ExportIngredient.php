<?php

namespace App\Models;

use App\Models\ModelFilters\ExportIngredientFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class ExportIngredient extends Model
{
    use Filterable;
    public $table = "export_ingredients";
    public $timestamps = true;
    public $fillable = [
        'creator_id',
        'note',
        'created_at',
        'updated_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(ExportIngredientDetail::class, 'export_ingredient_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(ExportIngredientFilter::class);
    }
}
