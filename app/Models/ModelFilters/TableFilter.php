<?php

namespace App\Models\ModelFilters;

use EloquentFilter\ModelFilter;

class TableFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function creator($creator_id)
    {
        return $this->where('creator_id', $creator_id);
    }

    public function id($id)
    {
        return $this->where('id', $id);
    }

    /**
     * Filter by capacity with various operators
     *
     * @param mixed $capacity Can be a direct value or an array with operators
     * @param string $operator Default operator if $capacity is not an array
     * @return $this
     */
    public function capacity($capacity, $operator = '=')
    {
        // If capacity is an array with operators like ['gte' => 13]
        if (is_array($capacity)) {
            foreach ($capacity as $op => $value) {
                switch ($op) {
                    case 'gte':
                        $this->where('capacity', '>=', $value);
                        break;
                    case 'gt':
                        $this->where('capacity', '>', $value);
                        break;
                    case 'lte':
                        $this->where('capacity', '<=', $value);
                        break;
                    case 'lt':
                        $this->where('capacity', '<', $value);
                        break;
                    case 'eq':
                        $this->where('capacity', '=', $value);
                        break;
                    default:
                        $this->where('capacity', '=', $value);
                }
            }
            return $this;
        }

        // If capacity is a direct value
        return $this->where('capacity', $operator, $capacity);
    }

    /**
     * Filter by area
     */
    public function area($area)
    {
        return $this->where('area', $area);
    }

    /**
     * Filter by status
     */
    public function status($status)
    {
        return $this->where('status', $status);
    }
}
