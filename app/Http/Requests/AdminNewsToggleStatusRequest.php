<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminNewsToggleStatusRequest extends FormRequest
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
                'exists:news,id',
            ],

            'field' => [
                'required',
                'string',
                Rule::in([
                    'is_breaking_news',
                    'show_at_slider',
                    'show_at_popular',
                    'status',
                ]),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'id.required' => __('validation.required', ['attribute' => 'ID']),
            'id.integer' => __('validation.integer', ['attribute' => 'ID']),
            'id.exists' => __('validation.exists', ['attribute' => 'News']),
            'field.required' => __('validation.required', ['attribute' => 'Field']),
            'field.string' => __('validation.string', ['attribute' => 'Field']),
            'field.in' => __('validation.in', ['attribute' => 'Field']),
            'status.required' => __('validation.required', ['attribute' => 'Status']),
            'status.boolean' => __('validation.boolean', ['attribute' => 'Status']),
        ];
    }
}
