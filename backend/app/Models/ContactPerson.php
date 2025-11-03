<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    use HasFactory;

    protected $table = 'contact_persons';

    protected $fillable = [
        'name',
        'surname',
        'position',
        'email',
        'phone_number',
        'company_id',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Kontakt patrí jednej firme
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Kontakt môže byť priradený k viacerým stážam (N:N)
     */
    public function internships()
    {
        return $this->belongsToMany(
            Internship::class,
            'internship_contact_person', // názov pivot tabuľky
            'contact_person_id',          // FK na contact_persons
            'internship_id'               // FK na internships
        );
    }

    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname}";
    }

}
