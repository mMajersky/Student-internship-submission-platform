<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; // <-- Dôležitý import
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Získa študentský záznam priradený k používateľovi.
     * TOTO JE KĽÚČOVÁ METÓDA, KTORÁ CHÝBALA.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    // ... (všetky ostatné vaše metódy ako hasRole, isAdmin atď. zostávajú nezmenené)
    
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasAnyRole(array $roleNames): bool
    {
        if (!$this->role) {
            return false;
        }
        return in_array($this->role->name, $roleNames);
    }
    
    // ... zvyšok vašich metód
}