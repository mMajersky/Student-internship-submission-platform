<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
            // Sanitize the HTML content from QuillEditor
            $announcement->content_sanitized = Purifier::clean($announcement->content, 'announcements');
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}


