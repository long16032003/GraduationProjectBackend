<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class CustomerFilter extends ModelFilter
{
    use HasAdvancedFilters;

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    /**
     * List of fields that support LIKE queries
     */
    protected $likeFields = ['name', 'email', 'phone', 'address'];

    /**
     * List of numeric/comparable fields
     */
    protected $numericFields = ['id', 'points'];

    /**
     * List of date fields
     */
    protected $dateFields = ['created_at', 'updated_at'];

    /**
     * List of boolean fields
     */
    protected $booleanFields = ['is_active'];

    // Legacy methods for backward compatibility
    public function name($value)
    {
        return $this->where('name', 'LIKE', "%{$value}%");
    }

    public function email($value)
    {
        return $this->where('email', 'LIKE', "%{$value}%");
    }

    public function phone($value)
    {
        return $this->where('phone', 'LIKE', "%{$value}%");
    }

    public function points($value)
    {
        return $this->where('points', '>=', $value);
    }
}
