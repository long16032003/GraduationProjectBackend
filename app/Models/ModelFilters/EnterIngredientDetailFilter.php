<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class EnterIngredientDetailFilter extends ModelFilter
{
    use HasAdvancedFilters;
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    protected $numericFields = ['id', 'enter_ingredient_id', 'ingredient_id', 'quantity', 'unit_price'];

    protected $dateFields = ['created_at', 'updated_at'];

    protected $likeFields = ['supplier_name'];

}
