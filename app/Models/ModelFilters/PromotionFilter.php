<?php

namespace App\Models\ModelFilters;

use EloquentFilter\ModelFilter;

class PromotionFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function name($value)
    {
        return $this->where('name', 'like', "%$value%");
    }

    public function description($value)
    {
        return $this->where('description', 'like', "%$value%");
    }

    public function startDate($value)
    {
        return $this->where('start_date', '>=', $value);
    }

    public function endDate($value)
    {
        return $this->where('end_date', '<=', $value);
    }

    public function discountPercentage($value)
    {
        return $this->where('discount_percentage', '>=', $value);
    }

    public function requiredPoints($value)
    {
        return $this->where('required_points', '>=', $value);
    }

    public function limit($value)
    {
        return $this->where('limit', '>=', $value);
    }

    public function search($value)
    {
        return $this->where('name', 'like', "%$value%")
                    ->orWhere('description', 'like', "%$value%")
                    ->orWhere('start_date', 'like', "%$value%")
                    ->orWhere('end_date', 'like', "%$value%")
                    ->orWhere('discount_percentage', 'like', "%$value%")
                    ->orWhere('required_points', 'like', "%$value%")
                    ->orWhere('limit', 'like', "%$value%");
    }
}
