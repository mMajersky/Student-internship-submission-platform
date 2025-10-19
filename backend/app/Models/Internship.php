<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_VYTVORENA = 'vytvorená';
    const STATUS_POTVRDENA = 'potvrdená';
    const STATUS_SCHVALENA = 'schválená';
    const STATUS_ZAMIETNUTA = 'zamietnutá';
    const STATUS_OBHAJENA = 'obhájená';
    const STATUS_NEOBHAJENA = 'neobhájená';

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
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'confirmed_date' => 'date',
        'approved_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all available status values
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_VYTVORENA,
            self::STATUS_POTVRDENA,
            self::STATUS_SCHVALENA,
            self::STATUS_ZAMIETNUTA,
            self::STATUS_OBHAJENA,
            self::STATUS_NEOBHAJENA,
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
            'contact_person_internships',
            'internship_id',
            'contact_person_id'
        );
    }
}
