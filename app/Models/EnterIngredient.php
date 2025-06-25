<?php

namespace App\Models;

use App\Models\ModelFilters\EnterIngredientFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class EnterIngredient extends Model
{
    use Filterable;
    public $table = "enter_ingredients";
    public $timestamps = true;
    public $fillable = [
        'creator_id',
        'total_amount',
        'note',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(EnterIngredientDetail::class, 'enter_ingredient_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(EnterIngredientFilter::class);
    }
}
