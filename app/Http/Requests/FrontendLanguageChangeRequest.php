<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FrontendLanguageChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'language_code' => [
                'required',
                'string',
                Rule::exists('languages', 'lang')->where(fn ($query) => $query->where('status', true)),
            ],
        ];
    }
}
