<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // Ak sa tabuľka volá "companies", toto netreba odkomentovať
    // protected $table = 'companies';

    protected $fillable = [
        'name',
        'user_id',
        'state',
        'region',
        'city',
        'postal_code',
        'street',
        'house_number',
    ];


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
}
