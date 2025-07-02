<?php

namespace App\Models\ModelFilters\Traits;

trait HasAdvancedFilters
{
    /**
     * List of fields that support LIKE queries
     * Define this in your filter class
     */
    // protected $likeFields = [];

    /**
     * List of numeric/comparable fields
     * Define this in your filter class
     */
    // protected $numericFields = ['id'];

    /**
     * List of date fields
     * Define this in your filter class
     */
    // protected $dateFields = ['created_at', 'updated_at'];

    /**
     * List of boolean fields
     * Define this in your filter class
     */
    // protected $booleanFields = [];

    /**
     * Support for filters parameter from URL
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

        // Handle single filter object
        if (isset($filters['field']) && array_key_exists('value', $filters)) {
            return $this->applyFilter($filters);
        }

        // Handle array of filters
        foreach ($filters as $filter) {
            if (isset($filter['field']) && array_key_exists('value', $filter)) {
                $this->applyFilter($filter);
            }
        }

        return $this;
    }

    /**
     * Apply individual filter based on field and operator
     */
    private function applyFilter($filter)
    {
        $field = $filter['field'];
        $value = $filter['value'];
        $operator = $filter['operator'] ?? 'eq';

        // Special handling for different field types
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

        // Default handling for other fields
        return $this->applyGenericFilter($field, $value, $operator);
    }

    /**
     * Apply filter for boolean fields
     */
    private function applyBooleanFilter($field, $value, $operator)
    {
        // Convert to boolean for proper comparison
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
     * Apply filter for text fields
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
     * Apply filter for numeric fields
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
     * Apply filter for date fields
     */
    private function applyDateFilter($field, $value, $operator)
    {
        // Handle null values explicitly
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
     * Apply generic filter for other fields
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
     * Apply between filter for numeric fields
     */
    private function applyBetweenFilter($field, $value)
    {
        if (is_array($value) && count($value) === 2) {
            return $this->whereBetween($field, $value);
        }
        return $this;
    }

    /**
     * Apply not between filter for numeric fields
     */
    private function applyNotBetweenFilter($field, $value)
    {
        if (is_array($value) && count($value) === 2) {
            return $this->whereNotBetween($field, $value);
        }
        return $this;
    }

    /**
     * Apply between filter for date fields
     */
    private function applyDateBetweenFilter($field, $value)
    {
        if (is_array($value) && count($value) === 2) {
            return $this->whereBetween($field, $value);
        }
        return $this;
    }

    /**
     * Apply not between filter for date fields
     */
    private function applyDateNotBetweenFilter($field, $value)
    {
        if (is_array($value) && count($value) === 2) {
            return $this->whereNotBetween($field, $value);
        }
        return $this;
    }

    /**
     * Generic sort method
     */
    public function sort($value)
    {
        $direction = $this->input('order', 'asc');
        return $this->orderBy($value, $direction);
    }

    /**
     * Generic search method - override this in your filter class
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
