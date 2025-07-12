<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use Carbon\Carbon;
use EloquentFilter\ModelFilter;

class ReservationFilter extends ModelFilter
{
    use HasAdvancedFilters;

    /**
    * Các Model liên quan có ModelFilters cũng như method trong ModelFilter
    * Dưới dạng [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    /**
     * Danh sách các field hỗ trợ tìm kiếm LIKE
     */
    protected $likeFields = ['name', 'phone', 'notes', 'status'];

    /**
     * Danh sách các field số/có thể so sánh
     */
    protected $numericFields = ['id', 'table_id', 'customer_id', 'number_of_guests'];

    /**
     * Danh sách các field ngày tháng
     */
    protected $dateFields = ['reservation_date', 'created_at', 'updated_at'];

    /**
     * Danh sách các field boolean
     */
    protected $booleanFields = [];

    /**
     * Danh sách các field có thể sắp xếp
     */
    protected $sortable = [
        'reservation_date',
        'created_at',
        'updated_at',
        'name',
        'phone',
        'status',
        'number_of_guests'
    ];

    /**
     * Lọc theo ngày đặt bàn
     */
    public function date($date)
    {
        $date = Carbon::parse($date);
        return $this->whereBetween('reservation_date', [
            $date->startOfDay(),
            $date->copy()->endOfDay()
        ]);
    }
}
