<?php

namespace App\Models\ModelFilters;

use EloquentFilter\ModelFilter;

class DishFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    protected $sortable = [
        'name',
        'price',
        'category_id',
        'created_at',
        'updated_at'
    ];

    public function name($value)
    {
        return $this->where('name', 'like', "%$value%");
    }

    public function categoryId($value)
    {
        return $this->where('category_id', 'like', "%$value%");
    }

    public function price($value)
    {
        return $this->where('price', 'like', "%$value%");
    }

    public function search($value)
    {
        return $this->where('name', 'like', "%$value%")
                    ->orWhere('price', 'like', "%$value%")
                    ->orWhere('category_id', 'like', "%$value%");
    }

}
