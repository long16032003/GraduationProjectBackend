<?php

namespace App\Models\ModelFilters;

use EloquentFilter\ModelFilter;

class OrderFilter extends ModelFilter
{
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
