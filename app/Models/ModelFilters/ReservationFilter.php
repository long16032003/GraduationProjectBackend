<?php

namespace App\Models\ModelFilters;

use Carbon\Carbon;
use EloquentFilter\ModelFilter;

class ReservationFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function status($status)
    {
        return $this->where('status', $status);
    }

    public function tableId($id)
    {
        return $this->where('table_id', $id);
    }

    public function customerId($id)
    {
        return $this->where('customer_id', $id);
    }

    public function phone($phone)
    {
        return $this->where('phone', 'LIKE', "%$phone%");
    }

    public function name($name)
    {
        return $this->where('name', 'LIKE', "%$name%");
    }

    public function dateFrom($date)
    {
        return $this->where('reservation_date', '>=', Carbon::parse($date));
    }

    public function dateTo($date)
    {
        return $this->where('reservation_date', '<=', Carbon::parse($date)->endOfDay());
    }

    public function date($date)
    {
        $date = Carbon::parse($date);
        return $this->whereBetween('reservation_date', [
            $date->startOfDay(),
            $date->copy()->endOfDay()
        ]);
    }
}
