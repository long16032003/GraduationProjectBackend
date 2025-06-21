<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class DishFilter extends ModelFilter
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
    protected $likeFields = ['name', 'description'];

    /**
     * List of numeric/comparable fields
     */
    protected $numericFields = ['id', 'price', 'category_id', 'creator_id'];

    /**
     * List of date fields
     */
    protected $dateFields = ['created_at', 'updated_at'];

    /**
     * List of boolean fields
     */
    protected $booleanFields = ['is_active', 'is_featured'];

    protected $sortable = [
        'name',
        'price',
        'category_id',
        'created_at',
        'updated_at'
    ];

    // Legacy methods for backward compatibility
    public function name($value)
    {
        return $this->where('name', 'like', "%$value%");
    }

    public function categoryId($value)
    {
        return $this->where('category_id', $value);
    }

    public function category_id($value)
    {
        return $this->categoryId($value);
    }

    public function price($value)
    {
        return $this->where('price', $value);
    }

    public function isFeatured($value)
    {
        // Convert to boolean for proper comparison
        $featured = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        return $this->where('is_featured', $featured);
    }

    // Keep the old method for backward compatibility
    public function is_featured($value)
    {
        return $this->isFeatured($value);
    }

    public function isActive($value)
    {
        $active = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        return $this->where('is_active', $active);
    }

    public function is_active($value)
    {
        return $this->isActive($value);
    }
}
