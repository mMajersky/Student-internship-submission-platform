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
        'surname',
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
     * Based on role-permission mapping
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = [
            'admin' => ['manage_users', 'manage_internships', 'manage_announcements'],
            'garant' => ['manage_internships', 'manage_announcements'],
            'company' => ['review_interns'],
            'student' => ['create_internships'],
        ];

        $rolePermissions = $permissions[$this->role] ?? [];
        return in_array($permission, $rolePermissions);
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

    /**
     * Get the student associated with this user
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    /**
     * Get the company associated with this user
     */
    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }

    /**
     * Get the garant associated with this user
     */
    public function garant()
    {
        return $this->hasOne(Garant::class, 'user_id');
    }

    /**
     * Get all notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}
