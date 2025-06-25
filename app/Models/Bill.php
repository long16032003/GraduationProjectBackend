<?php

namespace App\Models;

use App\Models\ModelFilters\BillFilter;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

/**
 * @property int $creator_id
 * @property int $customer_id
 * @property string $customer_phone
 * @property int $table_id
 * @property string $payment_method
 * @property float $total_amount
 * @property float $discount_amount
 * @property string $status
 * @property string $notes
 */
class Bill extends Model
{
    use Filterable;
    protected $table = 'bills';
    public $timestamps = true;

    const STATUS_PAID = 'paid';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_CARD = 'card';
    const PAYMENT_METHOD_MOMO = 'momo';
    const PAYMENT_METHOD_VNPAY = 'vnpay';

    protected $fillable = [
        'creator_id',
        'customer_id',
        'customer_phone',
        'customer_name',
        'table_id',
        'payment_method',
        'total_amount',
        'discount_amount',
        'status',
        'notes',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

        /**
     * Get customer by phone (for bills without customer_id)
     */
    public function customerByPhone()
    {
        return $this->belongsTo(Customer::class, 'customer_phone', 'phone');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'bill_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id', 'id');
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'bill_id', 'id')->latest();
    }

    public function modelFilter()
    {
        return $this->provideFilter(BillFilter::class);
    }
}
