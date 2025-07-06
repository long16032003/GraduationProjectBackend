<?php

namespace App\Models;

use App\Models\ModelFilters\OrderFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Filterable;
    protected $table = 'orders';
    public $timestamps = true;

    /** Trạng thái khi vừa tạo đơn (NV phục vụ tạo đơn gọi món)*/
    const STATUS_INIT = 'init';
    /** Trạng thái khi đang xử lý (NV bếp bắt đầu xử lý đơn)*/
    const STATUS_PROCESSING = 'processing';
    /** Trạng thái khi đã xử lý xong (NV bếp xử lý xong đơn)*/
    const STATUS_FINISHED_PROCESS = 'finished process';
    /** Trạng thái khi đã hoàn thành nhưng có món không được phục vụ (NV phục vụ hoàn thành đơn nhưng có món không được phục vụ)*/
    const STATUS_NOT_COMPLETED = 'not completed';
    /** Trạng thái khi đã hoàn thành (NV phục vụ xác nhận hoàn thành đơn)*/
    const STATUS_DONE = 'done';
    /** Trạng thái khi đơn bị hủy */
    const STATUS_CANCELLED = 'cancelled';

    const STATUS_LIST = [
        self::STATUS_INIT,
        self::STATUS_PROCESSING,
        self::STATUS_FINISHED_PROCESS,
        self::STATUS_NOT_COMPLETED,
        self::STATUS_DONE,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'table_id',
        'bill_id',
        'creator_id',
        'order_time',
        'note',
        'status',
        'priority',
        'cancelled_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'order_time' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function table(){
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    public function bill(){
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function creator(){
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function cancelledBy(){
        return $this->belongsTo(User::class, 'cancelled_by', 'id');
    }

    public function order_dishes(){
        return $this->hasMany(OrderDish::class, 'order_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(OrderFilter::class);
    }
}
