<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role === $roleName;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return in_array($this->role, $roleNames);
    }

    /**
     * Check if user has a specific permission
     * (ak nepoužívaš permission systém, môžeš to dočasne vypnúť)
     */
    public function hasPermission(string $permission): bool
    {
        return false; // alebo podľa potreby
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isGarant(): bool
    {
        return $this->hasRole('garant');
    }

    public function isCompany(): bool
    {
        return $this->hasRole('company');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function canManageAnnouncements(): bool
    {
        return $this->hasAnyRole(['admin', 'garant']);
    }

    public function canManageInternships(): bool
    {
        return $this->hasAnyRole(['admin', 'garant']);
    }

    public function canCreateInternships(): bool
    {
        return $this->hasRole('student');
    }
}
