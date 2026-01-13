<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get all users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions);
    }

    /**
     * Get role by name
     */
    public static function getByName(string $name): ?self
    {
        return static::where('name', $name)->where('is_active', true)->first();
    }

    /**
     * Role constants
     */
    public const GARANT = 'GARANT';
    public const COMPANY = 'COMPANY';
    public const STUDENT = 'STUDENT';
    public const ANONYMOUS = 'ANONYMOUS';
}
