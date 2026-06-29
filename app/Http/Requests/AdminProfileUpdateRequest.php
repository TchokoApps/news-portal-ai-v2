<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AdminProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $adminId = Auth::guard('admin')->user()->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($adminId),
            ],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('labels.Name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('labels.Name'), 'max' => 255]),
            'email.required' => __('validation.required', ['attribute' => __('labels.Email')]),
            'email.email' => __('validation.email', ['attribute' => __('labels.Email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('labels.Email')]),
            'profile_image.image' => __('validation.image', ['attribute' => 'Profile Image']),
            'profile_image.mimes' => __('validation.mimes', ['attribute' => 'Profile Image', 'values' => 'jpeg,png,jpg,gif']),
            'profile_image.max' => __('validation.max.file', ['attribute' => 'Profile Image', 'max' => '2MB']),
            'password.min' => __('validation.min.string', ['attribute' => __('labels.Password'), 'min' => 8]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('labels.Password')]),
        ];
    }
}
