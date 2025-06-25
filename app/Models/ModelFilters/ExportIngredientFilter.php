<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class ExportIngredientFilter extends ModelFilter
{
    use HasAdvancedFilters;

    protected $likeFields = ['ingredient_id'];

    protected $numericFields = ['id', 'quantity', 'creator_id'];

    protected $dateFields = ['created_at', 'updated_at'];

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
}
