<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get news associated with this tag.
     */
    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class);
    }
}
