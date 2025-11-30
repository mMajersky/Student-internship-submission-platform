<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // Ak sa tabuľka volá "companies", toto netreba odkomentovať
    // protected $table = 'companies';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'name',
//        'statutary',  //este nevieme ci tu bude, MM
        'user_id',
        'state',
        'region',
        'city',
        'postal_code',
        'street',
        'house_number',
        'status',
        'request_source',
        'reviewed_by_garant_id',
        'rejection_reason',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';

    /**
     * Request source constants
     */
    const SOURCE_PUBLIC = 'public_registration';
    const SOURCE_STUDENT = 'student_request';

    /**
     * Vzťah: firma patrí používateľovi
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vzťah: firma má viac kontaktných osôb
     */
    public function contactPersons()
    {
        return $this->hasMany(ContactPerson::class, 'company_id');
    }

    /**
     * Vzťah: firma má viac stáží
     */
    public function internships()
    {
        return $this->hasMany(Internship::class, 'company_id');
    }

    /**
     * Relationship: Garant who reviewed the request
     */
    public function reviewedByGarant()
    {
        return $this->belongsTo(Garant::class, 'reviewed_by_garant_id');
    }

    /**
     * Scope: Get only pending companies
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Get only accepted companies
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope: Get only declined companies
     */
    public function scopeDeclined($query)
    {
        return $query->where('status', self::STATUS_DECLINED);
    }

    /**
     * Check if company is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if company is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if company is declined
     */
    public function isDeclined(): bool
    {
        return $this->status === self::STATUS_DECLINED;
    }
}
