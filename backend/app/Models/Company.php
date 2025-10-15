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
        'statutary',
        'address_id',
        'user_id',
    ];

    /**
     * Vzťah: firma patrí k adrese
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Vzťah: firma patrí používateľovi
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vzťah: firma má viac kontakt­ných osôb
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
