<?php

namespace App\Models\ModelFilters;

use Carbon\Carbon;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class TableFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function creator($creator_id)
    {
        return $this->where('creator_id', $creator_id);
    }

    public function id($id)
    {
        return $this->where('id', $id);
    }

    /**
     * Filter by capacity with various operators
     *
     * @param mixed $capacity Can be a direct value or an array with operators
     * @param string $operator Default operator if $capacity is not an array
     * @return $this
     */
    // public function capacity($capacity, $operator = '=')
    // {
    //     // If capacity is an array with operators like ['gte' => 13]
    //     if (is_array($capacity)) {
    //         foreach ($capacity as $op => $value) {
    //             switch ($op) {
    //                 case 'gte':
    //                     $this->where('capacity', '>=', $value);
    //                     break;
    //                 case 'gt':
    //                     $this->where('capacity', '>', $value);
    //                     break;
    //                 case 'lte':
    //                     $this->where('capacity', '<=', $value);
    //                     break;
    //                 case 'lt':
    //                     $this->where('capacity', '<', $value);
    //                     break;
    //                 case 'eq':
    //                     $this->where('capacity', '=', $value);
    //                     break;
    //                 default:
    //                     $this->where('capacity', '=', $value);
    //             }
    //         }
    //         return $this;
    //     }

    //     // If capacity is a direct value
    //     return $this->where('capacity', $operator, $capacity);
    // }

    /**
     * Filter by area
     */
    public function area($area)
    {
        return $this->where('area', $area);
    }

    /**
     * Filter by status
     */
    public function status($status)
    {
        return $this->where('status', $status);
    }

    public function capacity($capacity)
    {
        $operator = $this->input('capacity_operator', 'eq');
       return match($operator) {
            'eq' => $this->where('capacity', $capacity),
            'gt' => $this->where('capacity', '>', $capacity),
            'lt' => $this->where('capacity', '<', $capacity),
            'gte' => $this->where('capacity', '>=', $capacity),
            'lte' => $this->where('capacity', '<=', $capacity),
        };
    }

    public function available($active)
    {
        if(!$active) {
            return $this;
        }

        $reservationDateTime = Carbon::parse($this->input('date') . ' ' . $this->input('time'));

        // Thời gian đặt bàn (giả sử mỗi lượt đặt bàn kéo dài 2 giờ)
        $reservationStartTime = $reservationDateTime->copy();
        $reservationEndTime = $reservationDateTime->copy()->addHours(2);

        // loc ra ca ban chua co reservation
        return $this->whereDoesntHave('reservation', function (Builder $query) use ($reservationStartTime, $reservationEndTime) {
            $query->where('status', '!=', 'cancelled')
                ->where('reservation_date', '>=', $reservationStartTime)
                ->where('reservation_date', '<=', $reservationEndTime);

        });
    }

    public function available_v2($active)  // check available table
    {
        if(!$active) {
            return $this;
        }

        $reservationDate = $this->input('date');
        $reservationTime = $this->input('time');
        $reservationDateTime = Carbon::parse($reservationDate . ' ' . $reservationTime);

        // Khung giờ reservation mới (2 tiếng)
        $startTime = $reservationDateTime->copy();
        $endTime = $reservationDateTime->copy()->addHours(2);

        return $this->where('status', 'occupied')
            // Không có bill active
            ->whereDoesntHave('bills', function (Builder $query) {
                $query->whereNotIn('status', ['paid', 'cancelled']);
            })
            // Không có reservation trùng
            ->whereDoesntHave('reservations', function (Builder $query) use ($startTime, $endTime) {
                $query->where('status', '!=', 'cancelled')
                    ->where('reservation_date', $startTime->format('Y-m-d'))
                    ->where(function($q) use ($startTime, $endTime) {
                        // Kiểm tra overlap: reservation khác có thời gian trùng không
                        $q->where(function($overlap) use ($startTime, $endTime) {
                            $overlap->where('reservation_time', '>=', $startTime->format('H:i:s'))
                                ->where('reservation_time', '<', $endTime->format('H:i:s'));
                        })
                        ->orWhere(function($overlap) use ($startTime, $endTime) {
                            // Reservation khác kết thúc sau khi reservation mới bắt đầu
                            $overlap->whereRaw("ADDTIME(reservation_time, '02:00:00') > ?", [$startTime->format('H:i:s')])
                                ->where('reservation_time', '<', $startTime->format('H:i:s'));
                        });
                    });
            });
    }
}
