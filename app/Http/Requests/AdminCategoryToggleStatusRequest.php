<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminCategoryToggleStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'field' => [
                'required',
                'string',
                Rule::in([
                    'show_at_navbar',
                    'status',
                ]),
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ];
    }
}
