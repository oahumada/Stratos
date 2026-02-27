<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'module',
        'action',
        'description',
    ];

    /**
     * Get all permissions for a given system role.
     */
    public static function forRole(string $role): \Illuminate\Support\Collection
    {
        return static::join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', $role)
            ->select('permissions.*')
            ->get();
    }
}
