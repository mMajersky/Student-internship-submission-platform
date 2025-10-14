<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mews\Purifier\Facades\Purifier;

class Announcement extends Model
{
    protected $fillable = [
        'content',
        'content_sanitized',
        'is_published',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Boot the model and attach event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically sanitize content before saving
        static::saving(function ($announcement) {
            $announcement->content_sanitized = Purifier::clean($announcement->content, 'announcements');
        });
    }

    /**
     * Get the user who created the announcement
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the announcement
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only published announcements
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
