<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garant extends Model
{
    use HasFactory;

    protected $table = 'garants';

    protected $fillable = [
        'name',
        'surname',
        'faculty',
        'user_id',
    ];

    public $timestamps = true;

    /**
     * Garant patrí jednému používateľovi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Garant má viac stáží
     */
    public function internships()
    {
        return $this->hasMany(Internship::class, 'garant_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname}";
    }

}
