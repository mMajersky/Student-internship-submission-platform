<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users'; // názov tabuľky v DB

    protected $fillable = [
        'role',
        'pwd',
        'email'
    ];

    protected $hidden = [
        'pwd',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->pwd;
    }
}
