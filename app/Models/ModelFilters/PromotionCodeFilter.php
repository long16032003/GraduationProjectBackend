<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class PromotionCodeFilter extends ModelFilter
{
    use HasAdvancedFilters;

    protected $likeFields = ['code'];

    protected $numericFields = ['id', 'promotion_id', 'customer_id'];

    protected $dateFields = ['used_at'];
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    /**
     * Filter by promotion_id
     */
    public function promotionId($value)
    {
        return $this->where('promotion_id', $value);
    }

    /**
     * Filter by code
     */
    public function code($value)
    {
        return $this->where('code', 'LIKE', "%{$value}%");
    }

    /**
     * Filter by used status
     */
    public function used($value)
    {
        if ($value == '1' || $value === true) {
            return $this->whereNotNull('used_at');
        } else {
            return $this->whereNull('used_at');
        }
    }
}
