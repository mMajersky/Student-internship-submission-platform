<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // Comment type constants
    const TYPE_APPROVAL = 'approval';
    const TYPE_REJECTION = 'rejection';
    const TYPE_CORRECTION = 'correction';
    const TYPE_GENERAL = 'general';

    protected $table = 'comments';

    protected $fillable = [
        'internship_id',
        'garant_id',
        'content',
        'comment_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all available comment type values
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_APPROVAL,
            self::TYPE_REJECTION,
            self::TYPE_CORRECTION,
            self::TYPE_GENERAL,
        ];
    }

    /**
     * Comment belongs to an internship
     */
    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id');
    }

    /**
     * Comment belongs to a garant
     */
    public function garant()
    {
        return $this->belongsTo(Garant::class, 'garant_id');
    }

    /**
     * Get the author (garant) with user information
     */
    public function author()
    {
        return $this->garant()->with('user');
    }
}
