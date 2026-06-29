<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendResetLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:admins,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'Email address not found in our admin system.',
        ];
    }
}
