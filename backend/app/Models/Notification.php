<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Notifikácia patrí jednému používateľovi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope pre neprečítané notifikácie
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Označiť notifikáciu ako prečítanú
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}

