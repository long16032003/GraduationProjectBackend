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
}
