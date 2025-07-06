<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use Carbon\Carbon;
use EloquentFilter\ModelFilter;

class ReservationFilter extends ModelFilter
{
    use HasAdvancedFilters;

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    protected $likeFields = ['name', 'phone', 'notes', 'status'];

    protected $numericFields = ['id', 'table_id', 'customer_id', 'number_of_guests'];

    protected $dateFields = ['reservation_date'];

    protected $sortable = [
        'reservation_date',
        'created_at',
        'updated_at'
    ];

    public function date($date)
    {
        $date = Carbon::parse($date);
        return $this->whereBetween('reservation_date', [
            $date->startOfDay(),
            $date->copy()->endOfDay()
        ]);
    }
}
