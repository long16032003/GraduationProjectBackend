<?php

namespace App\Models;

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

    const STATUS_LIST = [
        self::STATUS_INIT,
        self::STATUS_PROCESSING,
        self::STATUS_FINISHED_PROCESS,
        self::STATUS_NOT_COMPLETED,
        self::STATUS_DONE,
    ];

    protected $fillable = [
        'table_id',
        'bill_id',
        'creator_id',
        'order_time',
        'note',
        'status',
    ];

    public function table(){
        return $this->belongsTo(Table::class);
    }

    public function bill(){
        return $this->belongsTo(Bill::class);
    }

    public function creator(){
        return $this->belongsTo(User::class);
    }

    public function orderDishes(){
        return $this->hasMany(OrderDish::class);
    }
}
