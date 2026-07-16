<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminLanguageToggleStatusRequest extends FormRequest
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
                'exists:languages,id',
            ],
            'field' => [
                'required',
                'string',
                Rule::in([
                    'default',
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
