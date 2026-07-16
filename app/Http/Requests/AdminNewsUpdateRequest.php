<?php

namespace App\Http\Requests;

use App\Models\News;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminNewsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $news = $this->route('news');
        $newsId = $news instanceof News ? $news->id : (int) $news;

        return [
            'language' => ['required', 'exists:languages,code'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(
                    fn ($query) => $query->where('language', $this->input('language'))
                ),
            ],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('news', 'title')->ignore($newsId),
            ],
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_breaking_news' => ['nullable', 'boolean'],
            'show_at_slider' => ['nullable', 'boolean'],
            'show_at_popular' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ];
    }
}
