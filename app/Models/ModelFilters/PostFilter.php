<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class PostFilter extends ModelFilter
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
    protected $likeFields = ['title', 'summary', 'content'];

    /**
     * List of numeric/comparable fields
     */
    protected $numericFields = ['id', 'creator_id'];

    /**
     * List of date fields
     */
    protected $dateFields = ['created_at', 'updated_at'];

    protected $booleanFields = [];

    public function setup()
    {
        // Mặc định sắp xếp theo created_at giảm dần nếu không có sort
        if (!$this->input('sort')) {
            $this->orderBy('created_at', 'desc');
        }
    }
}
