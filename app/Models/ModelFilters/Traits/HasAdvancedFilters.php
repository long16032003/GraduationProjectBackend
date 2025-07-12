<?php

namespace App\Models\ModelFilters\Traits;

trait HasAdvancedFilters
{
    /**
     * Danh sách các field hỗ trợ tìm kiếm LIKE
     * Định nghĩa trong filter class của bạn
     */
    // protected $likeFields = [];

    /**
     * Danh sách các field số/có thể so sánh
     * Định nghĩa trong filter class của bạn
     */
    // protected $numericFields = ['id'];

    /**
     * Danh sách các field ngày tháng
     * Định nghĩa trong filter class của bạn
     */
    // protected $dateFields = ['created_at', 'updated_at'];

    /**
     * Danh sách các field boolean
     * Định nghĩa trong filter class của bạn
     */
    // protected $booleanFields = [];

    /**
     * Danh sách các field có thể sắp xếp
     * Định nghĩa trong filter class của bạn
     */
    // protected $sortable = ['id', 'created_at', 'updated_at'];

    /**
     * Hỗ trợ parameter filters từ URL
     */
    public function filters($value)
    {
        if (is_string($value)) {
            $filters = json_decode($value, true);
        } else {
            $filters = $value;
        }

        if (!is_array($filters)) {
            return $this;
        }

        // Xử lý filter object đơn
        if (isset($filters['field']) && array_key_exists('value', $filters)) {
            return $this->applyFilter($filters);
        }

        // Xử lý mảng các filters
        foreach ($filters as $filter) {
            if (isset($filter['field']) && array_key_exists('value', $filter)) {
                $this->applyFilter($filter);
            }
        }

        return $this;
    }

    /**
     * Xử lý sắp xếp với validation
     */
    public function sort($field)
    {
        $direction = $this->input('order', $this->input('sort_order', 'asc'));

        // Kiểm tra field có được phép sắp xếp không
        if ($this->isSortableField($field)) {
            return $this->orderBy($field, $direction);
        }

        return $this;
    }

    /**
     * Xử lý mảng sorters (format từ frontend)
     */
    public function sorters($sorters)
    {
        if (is_string($sorters)) {
            $sorters = json_decode($sorters, true);
        }

        if (!is_array($sorters)) {
            return $this;
        }

        foreach ($sorters as $sorter) {
            if (isset($sorter['field']) && isset($sorter['order'])) {
                $field = $sorter['field'];
                $order = strtolower($sorter['order']);

                // Kiểm tra hướng sắp xếp hợp lệ
                if (!in_array($order, ['asc', 'desc'])) {
                    $order = 'asc';
                }

                if ($this->isSortableField($field)) {
                    $this->orderBy($field, $order);
                }
            }
        }

        return $this;
    }

    /**
     * Xử lý parameter sort_field và sort_order
     */
    public function sortField($field)
    {
        $direction = $this->input('sort_order', 'asc');

        if ($this->isSortableField($field)) {
            return $this->orderBy($field, $direction);
        }

        return $this;
    }

    /**
     * Xử lý parameter sort_order
     */
    public function sortOrder($order)
    {
        // Method này được xử lý bởi sortField, nhưng cần để nhận diện parameter
        return $this;
    }

    /**
     * Kiểm tra field có thể sắp xếp được không
     */
    protected function isSortableField($field)
    {
        if (!property_exists($this, 'sortable')) {
            // Nếu không có sortable array, cho phép các field cơ bản
            $defaultSortable = ['id', 'created_at', 'updated_at'];
            return in_array($field, $defaultSortable);
        }

        return in_array($field, $this->sortable);
    }

    /**
     * Áp dụng filter dựa trên field và operator
     */
    private function applyFilter($filter)
    {
        $field = $filter['field'];
        $value = $filter['value'];
        $operator = $filter['operator'] ?? 'eq';

        // Xử lý đặc biệt cho các loại field khác nhau
        if (property_exists($this, 'booleanFields') && in_array($field, $this->booleanFields)) {
            return $this->applyBooleanFilter($field, $value, $operator);
        }

        if (property_exists($this, 'likeFields') && in_array($field, $this->likeFields)) {
            return $this->applyTextFilter($field, $value, $operator);
        }

        if (property_exists($this, 'numericFields') && in_array($field, $this->numericFields)) {
            return $this->applyNumericFilter($field, $value, $operator);
        }

        if (property_exists($this, 'dateFields') && in_array($field, $this->dateFields)) {
            return $this->applyDateFilter($field, $value, $operator);
        }

        // Xử lý mặc định cho các field khác
        return $this->applyGenericFilter($field, $value, $operator);
    }

    /**
     * Áp dụng filter cho các field boolean
     */
    private function applyBooleanFilter($field, $value, $operator)
    {
        // Chuyển đổi thành boolean để so sánh chính xác
        $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return match($operator) {
            'eq' => $this->where($field, $boolValue),
            'ne' => $this->where($field, '!=', $boolValue),
            'null' => $this->whereNull($field),
            'not_null' => $this->whereNotNull($field),
            default => $this->where($field, $boolValue),
        };
    }

    /**
     * Áp dụng filter cho các field text
     */
    private function applyTextFilter($field, $value, $operator)
    {
        return match($operator) {
            'eq' => $this->where($field, $value),
            'ne' => $this->where($field, '!=', $value),
            'like', 'contains' => $this->where($field, 'LIKE', "%{$value}%"),
            'not_like', 'not_contains' => $this->where($field, 'NOT LIKE', "%{$value}%"),
            'starts_with' => $this->where($field, 'LIKE', "{$value}%"),
            'ends_with' => $this->where($field, 'LIKE', "%{$value}"),
            'null' => $this->whereNull($field),
            'not_null' => $this->whereNotNull($field),
            'in' => $this->whereIn($field, is_array($value) ? $value : [$value]),
            'not_in' => $this->whereNotIn($field, is_array($value) ? $value : [$value]),
            default => $this->where($field, 'LIKE', "%{$value}%"),
        };
    }

    /**
     * Áp dụng filter cho các field số
     */
    private function applyNumericFilter($field, $value, $operator)
    {
        return match($operator) {
            'eq' => $this->where($field, $value),
            'ne' => $this->where($field, '!=', $value),
            'gt' => $this->where($field, '>', $value),
            'gte' => $this->where($field, '>=', $value),
            'lt' => $this->where($field, '<', $value),
            'lte' => $this->where($field, '<=', $value),
            'in' => $this->whereIn($field, is_array($value) ? $value : [$value]),
            'not_in' => $this->whereNotIn($field, is_array($value) ? $value : [$value]),
            'null' => $this->whereNull($field),
            'not_null' => $this->whereNotNull($field),
            'between' => $this->applyBetweenFilter($field, $value),
            'not_between' => $this->applyNotBetweenFilter($field, $value),
            default => $this->where($field, $value),
        };
    }

    /**
     * Áp dụng filter cho các field ngày tháng
     */
    private function applyDateFilter($field, $value, $operator)
    {
        // Xử lý giá trị null một cách rõ ràng
        if ($value === null) {
            return match($operator) {
                'eq' => $this->whereNull($field),
                'ne' => $this->whereNotNull($field),
                default => $this->whereNull($field),
            };
        }

        return match($operator) {
            'eq' => $this->whereDate($field, $value),
            'ne' => $this->whereDate($field, '!=', $value),
            'gt' => $this->whereDate($field, '>', $value),
            'gte' => $this->whereDate($field, '>=', $value),
            'lt' => $this->whereDate($field, '<', $value),
            'lte' => $this->whereDate($field, '<=', $value),
            'null' => $this->whereNull($field),
            'not_null' => $this->whereNotNull($field),
            'between' => $this->applyDateBetweenFilter($field, $value),
            'not_between' => $this->applyDateNotBetweenFilter($field, $value),
            'today' => $this->whereDate($field, today()),
            'yesterday' => $this->whereDate($field, now()->yesterday()),
            'this_week' => $this->whereBetween($field, [now()->startOfWeek(), now()->endOfWeek()]),
            'last_week' => $this->whereBetween($field, [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]),
            'this_month' => $this->whereMonth($field, now()->month)->whereYear($field, now()->year),
            'last_month' => $this->whereMonth($field, now()->subMonth()->month)->whereYear($field, now()->subMonth()->year),
            'this_year' => $this->whereYear($field, now()->year),
            'last_year' => $this->whereYear($field, now()->subYear()->year),
            default => $this->whereDate($field, $value),
        };
    }

    /**
     * Áp dụng filter chung cho các field khác
     */
    private function applyGenericFilter($field, $value, $operator)
    {
        return match($operator) {
            'eq' => $this->where($field, $value),
            'ne' => $this->where($field, '!=', $value),
            'gt' => $this->where($field, '>', $value),
            'gte' => $this->where($field, '>=', $value),
            'lt' => $this->where($field, '<', $value),
            'lte' => $this->where($field, '<=', $value),
            'like' => $this->where($field, 'LIKE', "%{$value}%"),
            'not_like' => $this->where($field, 'NOT LIKE', "%{$value}%"),
            'in' => $this->whereIn($field, is_array($value) ? $value : [$value]),
            'not_in' => $this->whereNotIn($field, is_array($value) ? $value : [$value]),
            'null' => $this->whereNull($field),
            'not_null' => $this->whereNotNull($field),
            'starts_with' => $this->where($field, 'LIKE', "{$value}%"),
            'ends_with' => $this->where($field, 'LIKE', "%{$value}"),
            default => $this->where($field, $value),
        };
    }

    /**
     * Áp dụng filter between cho các field số
     */
    private function applyBetweenFilter($field, $value)
    {
        if (is_array($value) && count($value) === 2) {
            return $this->whereBetween($field, $value);
        }
        return $this;
    }

    /**
     * Áp dụng filter not between cho các field số
     */
    private function applyNotBetweenFilter($field, $value)
    {
        if (is_array($value) && count($value) === 2) {
            return $this->whereNotBetween($field, $value);
        }
        return $this;
    }

    /**
     * Áp dụng filter between cho các field ngày tháng
     */
    private function applyDateBetweenFilter($field, $value)
    {
        if (is_array($value) && count($value) === 2) {
            return $this->whereBetween($field, $value);
        }
        return $this;
    }

    /**
     * Áp dụng filter not between cho các field ngày tháng
     */
    private function applyDateNotBetweenFilter($field, $value)
    {
        if (is_array($value) && count($value) === 2) {
            return $this->whereNotBetween($field, $value);
        }
        return $this;
    }

    /**
     * Method tìm kiếm chung - override trong filter class của bạn
     */
    public function search($value)
    {
        if (!property_exists($this, 'likeFields') || empty($this->likeFields)) {
            return $this;
        }

        return $this->where(function($query) use ($value) {
            foreach ($this->likeFields as $field) {
                $query->orWhere($field, 'LIKE', "%{$value}%");
            }
        });
    }
}
