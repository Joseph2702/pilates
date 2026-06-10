<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'id_role';

    public $timestamps = false;

    protected $fillable = ['nama_role', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles', 'id_role', 'id_user')
            ->withPivot('is_active');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'id_role', 'id_permission');
    }

    /**
     * Sync permissions for this role and clear user permission caches
     */
    public function syncPermissionsWithClearCache(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);

        // Clear permission cache for all users with this role
        $this->users()
            ->get()
            ->each(fn ($user) => $user->clearPermissionCache());
    }
}
