<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users'; // názov tabuľky v DB

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

    /**
     * Get the role that owns the user
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        if (!$this->role) {
            return false;
        }

        return in_array($this->role->name, $roleNames);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role && $this->role->hasPermission($permission);
    }

    /**
     * Get user's role name
     */
    public function getRoleName(): ?string
    {
        return $this->role ? $this->role->name : null;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    /**
     * Check if user is garant
     */
    public function isGarant(): bool
    {
        return $this->hasRole(Role::GARANT);
    }

    /**
     * Check if user is company
     */
    public function isCompany(): bool
    {
        return $this->hasRole(Role::COMPANY);
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->hasRole(Role::STUDENT);
    }

    /**
     * Check if user can manage announcements (admin or garant)
     */
    public function canManageAnnouncements(): bool
    {
        return $this->hasAnyRole([Role::ADMIN, Role::GARANT]);
    }

    /**
     * Check if user can manage internships (admin or garant)
     */
    public function canManageInternships(): bool
    {
        return $this->hasAnyRole([Role::ADMIN, Role::GARANT]);
    }

    /**
     * Check if user can create internships (student)
     */
    public function canCreateInternships(): bool
    {
        return $this->hasRole(Role::STUDENT);
    }
}
