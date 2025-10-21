<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'address'; // Explicitne nastav názov tabuľky

    protected $fillable = [
        'state',
        'region',
        'city',
        'postal_code',
        'street',
        'house_number'
    ];
}