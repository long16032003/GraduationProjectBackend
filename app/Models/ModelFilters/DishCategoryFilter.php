<?php

namespace App\Models\ModelFilters;

use EloquentFilter\ModelFilter;

class DishCategoryFilter extends ModelFilter
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
        'created_at',
        'updated_at'
    ];

    public function name($value)
    {
        return $this->where('name', 'like', "%$value%");
    }

    public function description($value)
    {
        return $this->where('description', 'like', "%$value%");
    }

    public function search($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('name', 'like', "%$value%")
                        ->orWhere('description', 'like', "%$value%");
        });
    }

    public function sort($column)
    {
        if (in_array($column, $this->sortable)) {
            $direction = $this->input('order', 'asc');
            return $this->orderBy($column, $direction);
        }
    }

    public function order($value)
    {
        return $this->orderBy($value, 'asc');
    }

    public function paginate($value)
    {
        return $this->paginate($value);
    }

    public function simplePaginate($value)
    {
        return $this->simplePaginate($value);
    }
}
