<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    use HasFactory;

    // Tu opravíme názov tabuľky
    protected $table = 'contact_persons';

    protected $fillable = [
        'name',
        'surname',
        'position',
        'email',
        'phone_number',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
