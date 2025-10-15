<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'internships';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'company_id',
        'garant_id',
        'status',
        'start_date',
        'end_date',
        'confirmed_date',
        'approved_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'confirmed_date' => 'date',
        'approved_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns the internship.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the company associated with the internship.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the garant associated with the internship.
     */
    public function garant()
    {
        return $this->belongsTo(Garant::class);
    }

    /**
     * Get the documents for the internship.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}