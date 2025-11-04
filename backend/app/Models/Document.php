<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'internship_id',
        'type',
        'status',
        'file_path',
        'name',
    ];

    public $timestamps = true;

    protected $casts = [
        'internship_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id');
    }
}





