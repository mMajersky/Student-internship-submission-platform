<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    /* =====================================================
     | STATUS CONSTANTS (internship workflow states)
     |=====================================================*/
    const STATUS_CREATED = 'created';
    const STATUS_APPROVED = 'approved by garant';
    const STATUS_REJECTED = 'rejected by garant';
    const STATUS_DEFENDED = 'defended by student';
    const STATUS_NOT_DEFENDED = 'not defended by student';
    const STATUS_CONFIRMED = 'confirmed by company';
    const STATUS_NOT_CONFIRMED = 'not confirmed by company';

    /* =====================================================
     | PRACTICE TYPE CONSTANTS (internship classification)
     |=====================================================*/
    const TYPE_PAID = 'paid';
    const TYPE_UNPAID = 'unpaid';
    const TYPE_SCHOOL_PROJECT = 'school_project';

    protected $table = 'internships';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'student_id',
        'company_id',
        'garant_id',

        'status',
        'type',

        'start_date',
        'end_date',
        'confirmed_date',
        'approved_date',
        'academy_year',

        'evaluation',
        'internship_report',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'confirmed_date' => 'date',
        'approved_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

        'evaluation' => 'array',
        'internship_report' => 'array',
    ];

    /* =====================================================
     | HELPER METHODS
     |=====================================================*/

    /**
     * Get all available internship statuses
     */
    public static function getStatuses(): array
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
     * Get all available internship practice types
     */
    public static function getPracticeTypes(): array
    {
        return [
            self::TYPE_PAID,
            self::TYPE_UNPAID,
            self::TYPE_SCHOOL_PROJECT,
        ];
    }

    /* =====================================================
     | RELATIONSHIPS
     |=====================================================*/

    /**
     * Internship belongs to a student
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Internship belongs to a company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Internship belongs to a guarantor
     */
    public function garant()
    {
        return $this->belongsTo(Garant::class, 'garant_id');
    }

    /**
     * Internship has many documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'internship_id');
    }

    /**
     * Internship may have multiple contact persons (many-to-many)
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
     * Internship has many guarantor comments
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'internship_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest comment for the internship
     */
    public function latestComment()
    {
        return $this->hasOne(Comment::class, 'internship_id')
            ->latestOfMany();
    }
}
