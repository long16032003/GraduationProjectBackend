<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class IngredientFilter extends ModelFilter
{
    use HasAdvancedFilters;

    protected $likeFields = ['name'];

    protected $numericFields = ['id', 'creator_id', 'unit'];

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
}
