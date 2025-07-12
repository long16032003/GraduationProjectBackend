<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use Carbon\Carbon;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class TableFilter extends ModelFilter
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
     * Danh sách các field hỗ trợ tìm kiếm LIKE
     */
    protected $likeFields = ['name', 'area'];

    /**
     * Danh sách các field số/có thể so sánh
     */
    protected $numericFields = ['id', 'capacity', 'creator_id'];

    /**
     * Danh sách các field ngày tháng
     */
    protected $dateFields = ['created_at', 'updated_at'];

    /**
     * Danh sách các field boolean
     */
    protected $booleanFields = [];

    /**
     * Danh sách các field có thể sắp xếp
     */
    protected $sortable = [
        'id',
        'name',
        'capacity',
        'area',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * Lọc theo người tạo
     */
    public function creator($creator_id)
    {
        return $this->where('creator_id', $creator_id);
    }

    /**
     * Lọc theo ID
     */
    public function id($id)
    {
        return $this->where('id', $id);
    }

    /**
     * Lọc theo sức chứa với các toán tử khác nhau
     *
     * @param mixed $capacity Có thể là giá trị trực tiếp hoặc array với operators
     * @param string $operator Toán tử mặc định nếu $capacity không phải array
     * @return $this
     */
    // Commented out legacy capacity method...

    /**
     * Lọc theo khu vực
     */
    public function area($area)
    {
        return $this->where('area', $area);
    }

    /**
     * Lọc theo trạng thái
     */
    public function status($status)
    {
        return $this->where('status', $status);
    }

    /**
     * Lọc theo sức chứa với operator
     */
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

    /**
     * Lọc bàn có sẵn (available)
     */
    public function available($active)
    {
        if(!$active) {
            return $this;
        }

        $reservationDateTime = Carbon::parse($this->input('date') . ' ' . $this->input('time'));

        // Thời gian đặt bàn (giả sử mỗi lượt đặt bàn kéo dài 2 giờ)
        $reservationStartTime = $reservationDateTime->copy();
        $reservationEndTime = $reservationDateTime->copy()->addHours(2);

        // Lọc ra các bàn chưa có reservation
        return $this->whereDoesntHave('reservation', function (Builder $query) use ($reservationStartTime, $reservationEndTime) {
            $query->where('status', '!=', 'cancelled')
                ->where('reservation_date', '>=', $reservationStartTime)
                ->where('reservation_date', '<=', $reservationEndTime);

        });
    }

    /**
     * Kiểm tra bàn có sẵn - phiên bản 2
     */
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
                $query->where('status', '!=', 'cancelled')  //Đặt bàn không bị hủy
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
