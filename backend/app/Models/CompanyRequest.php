<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyRequest extends Model
{
    use HasFactory;

    protected $table = 'company_requests';

    protected $fillable = [
        'company_name',
        'state',
        'region',
        'city',
        'postal_code',
        'street',
        'house_number',
        'contact_person_name',
        'contact_person_surname',
        'contact_person_email',
        'contact_person_phone',
        'status',
        'request_source',
        'requested_by_user_id',
        'reviewed_by_garant_id',
        'rejection_reason',
        'reviewed_at',
        'company_id',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Request source constants
     */
    const SOURCE_PUBLIC = 'public_registration';
    const SOURCE_STUDENT = 'student_request';

    /**
     * Relationship: Student who requested the company (if from internship form)
     */
    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    /**
     * Relationship: Garant who reviewed the request
     */
    public function reviewedByGarant()
    {
        return $this->belongsTo(Garant::class, 'reviewed_by_garant_id');
    }

    /**
     * Relationship: The created company (when approved)
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Scope: Get only pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Get only approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope: Get only rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get full contact person name
     */
    public function getContactPersonFullNameAttribute(): string
    {
        return "{$this->contact_person_name} {$this->contact_person_surname}";
    }
}
