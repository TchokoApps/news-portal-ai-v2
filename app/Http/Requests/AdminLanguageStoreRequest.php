<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AdminLanguageStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:255|unique:languages,code',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:languages,slug',
            'default' => 'required|boolean',
            'status' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => __('messages.language_code_required'),
            'code.unique' => __('messages.language_code_already_exists'),
            'name.required' => __('messages.language_name_required'),
            'slug.required' => __('messages.language_slug_required'),
            'slug.unique' => __('messages.language_slug_already_exists'),
            'default.required' => __('messages.default_language_required'),
            'status.required' => __('messages.language_status_required'),
        ];
    }
}
