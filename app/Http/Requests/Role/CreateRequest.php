<?php

namespace App\Http\Requests\Role;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Request $request): bool
    {
        return (bool) $request->user()?->hasPermission('role:create');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'permissions' => $this->collect('permissions')
                ->filter(static function ($value, $permission) {
                    if (!in_array($permission, config('permission.flat', []), true)) {
                        return false;
                    }
                    return (bool) $value;
                })->all(),
        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(Request $request): array
    {
        return [
            'name' => ['required', 'string', 'max:128'],
            'level' => ['required', 'numeric', 'min:0', 'max:10'],
            'status' => ['required', 'boolean'],
            'permissions' => 'required|array:' . implode(',', config('permission.flat')),
        ];
    }
}
