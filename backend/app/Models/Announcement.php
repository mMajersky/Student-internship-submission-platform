<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mews\Purifier\Facades\Purifier;

class Announcement extends Model
{
    protected $table = 'announcements';

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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($announcement) {
            $announcement->content_sanitized = Purifier::clean($announcement->content, 'announcements');
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}


