<?php

namespace App\Models\ModelFilters;

use EloquentFilter\ModelFilter;

class PostFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function setup()
    {
        // Mặc định sắp xếp theo created_at giảm dần nếu không có sort
        if (!$this->input('sort')) {
            $this->orderBy('created_at', 'desc');
        }
    }

    public function id($value)
    {
        return $this->where('id', $value);
    }

    public function search($value)
    {
        return $this->where('title', 'LIKE', "%{$value}%");
    }

    public function sort($value)
    {
        $direction = $this->input('order', 'asc');
        return $this->orderBy($value, $direction);
    }

    public function title($value)
    {
        return $this->where('title', 'LIKE', "%{$value}%");
    }
}
