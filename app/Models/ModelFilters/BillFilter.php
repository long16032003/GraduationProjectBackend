<?php

namespace App\Models\ModelFilters;

use App\Models\ModelFilters\Traits\HasAdvancedFilters;
use EloquentFilter\ModelFilter;

class BillFilter extends ModelFilter
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
    protected $likeFields = ['customer_phone', 'customer_name', 'notes'];

    /**
     * Danh sách các field số/có thể so sánh
     */
    protected $numericFields = ['id', 'creator_id', 'customer_id', 'table_id', 'total_amount', 'discount_amount'];

    /**
     * Danh sách các field ngày tháng
     */
    protected $dateFields = ['created_at', 'updated_at'];

    /**
     * Danh sách các field có thể sắp xếp
     */
    protected $sortable = [
        'id',
        'customer_name',
        'customer_phone',
        'table_id',
        'total_amount',
        'discount_amount',
        'status',
        'payment_method',
        'created_at',
        'updated_at'
    ];

        /**
     * Method setup để định nghĩa hành vi mặc định
     */
    public function setup()
    {
        // Tự động include customer relationships
        $this->includeCustomerRelationships();
    }

    /**
     * Include customer relationships - cả theo ID và theo phone
     */
    private function includeCustomerRelationships()
    {
        // Luôn include cả hai customer relationships
        return $this->with([
            'customer',         // Cho bills có customer_id
            'customerByPhone'   // Cho bills có customer_phone (không có customer_id)
        ]);
    }

    // Các method legacy để tương thích ngược
    public function customerId($value)
    {
        return $this->where('customer_id', $value);
    }

    public function customer_id($value)
    {
        return $this->customerId($value);
    }

    public function customerPhone($value)
    {
        return $this->where('customer_phone', 'LIKE', "%{$value}%");
    }

    public function customer_phone($value)
    {
        return $this->customerPhone($value);
    }

    public function customerName($value)
    {
        return $this->where('customer_name', 'LIKE', "%{$value}%");
    }

    public function customer_name($value)
    {
        return $this->customerName($value);
    }

    public function tableId($value)
    {
        return $this->where('table_id', $value);
    }

    public function table_id($value)
    {
        return $this->tableId($value);
    }

    public function creatorId($value)
    {
        return $this->where('creator_id', $value);
    }

    public function creator_id($value)
    {
        return $this->creatorId($value);
    }

    public function status($value)
    {
        return $this->where('status', $value);
    }

    public function paymentMethod($value)
    {
        return $this->where('payment_method', $value);
    }

    public function payment_method($value)
    {
        return $this->paymentMethod($value);
    }

    public function totalAmount($value)
    {
        return $this->where('total_amount', '>=', $value);
    }

    public function total_amount($value)
    {
        return $this->totalAmount($value);
    }

    public function discountAmount($value)
    {
        return $this->where('discount_amount', '>=', $value);
    }

    public function discount_amount($value)
    {
        return $this->discountAmount($value);
    }

    /**
     * Filter bills that have customer (customer_id is not null)
     */
    public function hasCustomer($value = true)
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            return $this->whereNotNull('customer_id');
        } else {
            return $this->whereNull('customer_id');
        }
    }

    /**
     * Filter bills that don't have customer (customer_id is null)
     */
    public function noCustomer($value = true)
    {
        return $this->hasCustomer(!$value);
    }

    /**
     * Force include customer relationship
     */
    public function withCustomer($value = true)
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            return $this->with('customer');
        }
        return $this;
    }

    public function with_customer($value = true)
    {
        return $this->withCustomer($value);
    }
}
