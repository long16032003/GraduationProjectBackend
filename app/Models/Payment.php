<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_SUCCESS,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
    ];

    // VNPay Response Codes
    const VNPAY_SUCCESS = '00';
    const VNPAY_PENDING = '01';
    const VNPAY_FAILED = '02';
    const VNPAY_CANCELLED = '24';

    protected $fillable = [
        'bill_id',
        'vnp_TxnRef',
        'vnp_TransactionNo',
        'vnp_Amount',
        'vnp_OrderInfo',
        'vnp_ResponseCode',
        'vnp_TransactionStatus',
        'vnp_PayDate',
        'vnp_BankCode',
        'vnp_BankTranNo',
        'vnp_CardType',
        'status',
        'vnpay_response',
    ];

    protected $casts = [
        'vnp_Amount' => 'decimal:2',
        'vnpay_response' => 'array',
    ];

    /**
     * Relationship with Bill
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Generate unique transaction reference
     */
    public static function generateTxnRef(): string
    {
        return 'BILL_' . time() . '_' . random_int(1000, 9999);
    }

    /**
     * Check if payment is successful
     */
    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS &&
               $this->vnp_ResponseCode === self::VNPAY_SUCCESS;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Update payment status based on VNPay response code
     */
    public function updateStatusFromVnpay(string $responseCode): void
    {
        $this->vnp_ResponseCode = $responseCode;

        $this->status = match($responseCode) {
            self::VNPAY_SUCCESS => self::STATUS_SUCCESS,
            self::VNPAY_PENDING => self::STATUS_PENDING,
            self::VNPAY_CANCELLED => self::STATUS_CANCELLED,
            default => self::STATUS_FAILED,
        };

        $this->save();
    }

    /**
     * Scope for successful payments
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
