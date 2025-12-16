<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_CREATED = 'created';
    const STATUS_APPROVED = 'approved by garant';
    const STATUS_REJECTED = 'rejected by garant';
    const STATUS_DEFENDED = 'defended by student';
    const STATUS_NOT_DEFENDED = 'not defended by student';
    const STATUS_CONFIRMED = 'confirmed by company';
    const STATUS_NOT_CONFIRMED = 'not confirmed by company';

    protected $table = 'internships';

    protected $fillable = [
        'student_id',
        'company_id',
        'garant_id',
        'status',
        'start_date',
        'end_date',
        'confirmed_date',
        'approved_date',
        'academy_year', // voliteľné, ak je už v DB
        'evaluation', // JSON stĺpec pre hodnotenie od firmy (používa sa pre výkaz praxe)
        'internship_report', // JSON stĺpec pre výkaz praxe
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'confirmed_date' => 'date',
        'approved_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'evaluation' => 'array', // JSON stĺpec pre hodnotenie (používa sa pre výkaz praxe)
        'internship_report' => 'array', // JSON stĺpec pre výkaz praxe
    ];

    /**
     * Get all available status values
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_CREATED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_DEFENDED,
            self::STATUS_NOT_DEFENDED,
            self::STATUS_CONFIRMED,
            self::STATUS_NOT_CONFIRMED,
        ];
    }

    /**
     * Stáž patrí študentovi
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Stáž patrí firme
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Stáž patrí garantovi
     */
    public function garant()
    {
        return $this->belongsTo(Garant::class, 'garant_id');
    }

    /**
     * Stáž má viac dokumentov
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'internship_id');
    }

    /**
     * Stáž môže mať viaceré kontaktné osoby (N:N)
     */
    public function contactPersons()
    {
        return $this->belongsToMany(
            ContactPerson::class,
            'internship_contact_person',
            'internship_id',
            'contact_person_id'
        );
    }

    /**
     * Stáž má viac komentárov od garantov
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'internship_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get latest comment for this internship
     */
    public function latestComment()
    {
        return $this->hasOne(Comment::class, 'internship_id')->latestOfMany();
    }


}
