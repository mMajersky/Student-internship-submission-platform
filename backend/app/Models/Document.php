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
        'company_status',
        'company_rejection_reason',
        'company_validated_at',
    ];

    protected $casts = [
        'company_validated_at' => 'datetime',
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id');
    }
}





