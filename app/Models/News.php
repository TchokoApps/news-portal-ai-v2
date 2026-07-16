<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'language',
        'category_id',
        'author_id',
        'title',
        'slug',
        'content',
        'image',
        'meta_title',
        'meta_description',
        'is_breaking_news',
        'show_at_slider',
        'show_at_popular',
        'status',
        'is_approved',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'is_breaking_news' => 'boolean',
            'show_at_slider' => 'boolean',
            'show_at_popular' => 'boolean',
            'is_approved' => 'boolean',
            'views' => 'integer',
            'status' => 'string',
        ];
    }

    /**
     * Get the category associated with this news.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the author associated with this news.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    /**
     * Get tags associated with this news.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('is_approved', true);
    }

    public function scopeForLanguage(Builder $query, string $languageCode): Builder
    {
        return $query->where('language', $languageCode);
    }
}
