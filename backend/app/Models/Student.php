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
        'state',
        'region',
        'city',
        'postal_code',
        'street',
        'house_number',
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
