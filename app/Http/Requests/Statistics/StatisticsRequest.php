<?php

namespace App\Http\Requests\Statistics;

use App\Models\Bill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatisticsRequest extends FormRequest
{
    /**
     * Xác định user có được phép thực hiện request này không
     */
    public function authorize(): bool
    {
        // Chỉ cho phép user đã đăng nhập và có quyền xem thống kê
        return $this->user() && ($this->user()->hasRole('manager') || $this->user()->hasRole('admin'));
    }

    /**
     * Quy tắc validation cho request
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::in(['daily', 'monthly', 'yearly'])
            ],
            'start_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:end_date'
            ],
            'end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
                'before_or_equal:today'
            ],
            'payment_method' => [
                'nullable',
                'string',
                Rule::in([
                    Bill::PAYMENT_METHOD_CASH,
                    Bill::PAYMENT_METHOD_CARD,
                    Bill::PAYMENT_METHOD_MOMO,
                    Bill::PAYMENT_METHOD_VNPAY,
                    'all'
                ])
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in([
                    Bill::STATUS_PAID,
                    Bill::STATUS_UNPAID,
                    Bill::STATUS_CANCELLED,
                    'all'
                ])
            ]
        ];
    }

    /**
     * Thông báo lỗi tùy chỉnh
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Loại thống kê là bắt buộc',
            'type.in' => 'Loại thống kê phải là daily, monthly hoặc yearly',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc',
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ',
            'start_date.date_format' => 'Ngày bắt đầu phải có định dạng Y-m-d',
            'start_date.before_or_equal' => 'Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc',
            'end_date.required' => 'Ngày kết thúc là bắt buộc',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ',
            'end_date.date_format' => 'Ngày kết thúc phải có định dạng Y-m-d',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
            'end_date.before_or_equal' => 'Ngày kết thúc không được lớn hơn ngày hiện tại',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
            'status.in' => 'Trạng thái hóa đơn không hợp lệ'
        ];
    }

    /**
     * Xử lý sau khi validation thành công
     */
    public function prepareForValidation(): void
    {
        // Xử lý dữ liệu trước khi validate
        $this->merge([
            'payment_method' => $this->payment_method ?: 'all',
            'status' => $this->status ?: Bill::STATUS_PAID,
        ]);
    }

    /**
     * Validation bổ sung
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Kiểm tra khoảng thời gian không quá 1 năm
            if ($this->start_date && $this->end_date) {
                $startDate = \Carbon\Carbon::parse($this->start_date);
                $endDate = \Carbon\Carbon::parse($this->end_date);

                if ($startDate->diffInDays($endDate) > 365) {
                    $validator->errors()->add('end_date', 'Khoảng thời gian thống kê không được vượt quá 1 năm');
                }
            }
        });
    }
}
