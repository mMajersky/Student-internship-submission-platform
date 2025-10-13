<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'surname', 'student_email', 'alternative_email',
        'address_id', 'phone_number', 'user_id'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
