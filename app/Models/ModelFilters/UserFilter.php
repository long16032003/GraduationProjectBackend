<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class UserFilter extends ModelFilter
{
    use HasAdvancedFilters;

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    protected $booleanFields = ['superadmin'];

    protected $likeFields = ['name', 'email', 'phone', 'role', 'username'];

    protected $numericFields = ['id'];

    protected $dateFields = ['created_at', 'updated_at'];
}
