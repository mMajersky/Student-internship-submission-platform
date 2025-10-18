<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Dôležitý import

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'surname', 'student_email', 'alternative_email',
        'address_id', 'phone_number', 'user_id'
    ];

    public $timestamps = false;

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Získa používateľský účet, ktorý vlastní tento študentský profil.
     * Toto je inverzná relácia k User->student()
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}