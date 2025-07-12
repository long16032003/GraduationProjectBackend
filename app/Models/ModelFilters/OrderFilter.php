<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class OrderFilter extends ModelFilter
{
    use HasAdvancedFilters;

    protected $likeFields = ['note', 'status', 'cancelled_reason'];

    /**
     * Danh sách các field số/có thể so sánh
     */
    protected $numericFields = ['id', 'table_id', 'bill_id', 'creator_id', 'priority', 'cancelled_by'];

    /**
     * Danh sách các field ngày tháng
     */
    protected $dateFields = ['order_time', 'created_at', 'updated_at'];

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [
        // 'order_dishes' => [
        //     'dish' => [
        //         'name',
        //         'price',
        //         'image',
        //     ],
        // ],
        // 'table' => [
        //     'name',
        // ],
        // 'creator' => [
        //     'name',
        // ],
    ];
}
