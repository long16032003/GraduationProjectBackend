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
    const PAYMENT_METHOD_VNPAY = 'vnpay';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';

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

    // ==================== PAYMENT STATUS METHODS ====================

    /**
     * Kiểm tra hóa đơn đã được thanh toán hay chưa
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Kiểm tra hóa đơn chưa thanh toán
     */
    public function isUnpaid(): bool
    {
        return $this->status === self::STATUS_UNPAID;
    }

    /**
     * Kiểm tra hóa đơn đã bị hủy
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Kiểm tra hóa đơn có thể thanh toán được không
     */
    public function canBePaid(): bool
    {
        return $this->status === self::STATUS_UNPAID;
    }

    /**
     * Kiểm tra hóa đơn có thể hủy được không
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_UNPAID, self::STATUS_PAID]);
    }

    /**
     * Kiểm tra hóa đơn có payments hay không
     */
    public function hasPayments(): bool
    {
        return $this->payments()->exists();
    }

    /**
     * Kiểm tra hóa đơn có payment thành công hay không
     */
    public function hasSuccessfulPayment(): bool
    {
        return $this->payments()
            ->where('status', 'success')
            ->exists();
    }

    /**
     * Lấy tổng số tiền đã thanh toán
     */
    public function getTotalPaidAmount(): float
    {
        return $this->payments()
            ->where('status', 'success')
            ->sum('amount');
    }

    /**
     * Kiểm tra hóa đơn đã thanh toán đủ chưa
     */
    public function isFullyPaid(): bool
    {
        $totalPaid = $this->getTotalPaidAmount();
        return $totalPaid >= $this->total_amount;
    }

    /**
     * Kiểm tra hóa đơn thanh toán thiếu
     */
    public function isPartiallyPaid(): bool
    {
        $totalPaid = $this->getTotalPaidAmount();
        return $totalPaid > 0 && $totalPaid < $this->total_amount;
    }

    /**
     * Lấy số tiền còn lại cần thanh toán
     */
    public function getRemainingAmount(): float
    {
        $totalPaid = $this->getTotalPaidAmount();
        return max(0, $this->total_amount - $totalPaid);
    }

    /**
     * Đánh dấu hóa đơn là đã thanh toán
     */
    public function markAsPaid(): bool
    {
        return $this->update(['status' => self::STATUS_PAID]);
    }

    /**
     * Đánh dấu hóa đơn là chưa thanh toán
     */
    public function markAsUnpaid(): bool
    {
        return $this->update(['status' => self::STATUS_UNPAID]);
    }

    /**
     * Hủy hóa đơn
     */
    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    // ==================== QUERY SCOPES ====================

    /**
     * Scope để lấy hóa đơn đã thanh toán
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope để lấy hóa đơn chưa thanh toán
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    /**
     * Scope để lấy hóa đơn đã hủy
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope để lấy hóa đơn có thể thanh toán
     */
    public function scopePayable($query)
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    /**
     * Scope để lấy hóa đơn quá hạn
     */
    public function scopeOverdue($query, int $days = 7)
    {
        return $query->where('status', self::STATUS_UNPAID)
            ->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope để lấy hóa đơn theo phương thức thanh toán
     */
    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope để lấy hóa đơn theo khoảng thời gian
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // ==================== ACCESSORS ====================

    /**
     * Accessor để lấy trạng thái thanh toán dạng text
     */
    public function getPaymentStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PAID => 'Đã thanh toán',
            self::STATUS_UNPAID => 'Chưa thanh toán',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    /**
     * Accessor để lấy trạng thái thanh toán dạng badge class
     */
    public function getPaymentStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PAID => 'success',
            self::STATUS_UNPAID => 'warning',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Accessor để kiểm tra hóa đơn có thể xử lý không
     */
    public function getIsProcessableAttribute(): bool
    {
        return $this->status !== self::STATUS_CANCELLED;
    }
}
