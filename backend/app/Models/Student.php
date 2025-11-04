<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'student_email',
        'alternative_email',
        'phone_number',
        'user_id',
        'study_level',
        'study_field',
        'state',
        'region',
        'city',
        'postal_code',
        'street',
        'house_number',
    ];

    public $timestamps = true;

    protected $casts = [
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Študent patrí jednému používateľovi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Študent má viacero stáží
     */
    public function internships()
    {
        return $this->hasMany(Internship::class, 'student_id');
    }

    /**
     * Pomocný atribút – celé meno
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname}";
    }
}