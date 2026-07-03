<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
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
    ];

    protected $casts = [
        'is_breaking_news' => 'boolean',
        'show_at_slider' => 'boolean',
        'show_at_popular' => 'boolean',
        'status' => 'string',
    ];

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
}
