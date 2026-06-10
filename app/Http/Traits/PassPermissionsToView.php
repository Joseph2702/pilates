<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait PassPermissionsToView
{
    /**
     * Build permissions array for a resource with standard CRUD permissions
     *
     * @param  string  $resourceName  - e.g. 'kelas', 'jadwal_kelas', 'instruktur'
     */
    protected function buildPermissions(string $resourceName): array
    {
        $user = Auth::user();

        return [
            'canView' => $user->hasPermission("{$resourceName}.view"),
            'canCreate' => $user->hasPermission("{$resourceName}.create"),
            'canUpdate' => $user->hasPermission("{$resourceName}.update"),
            'canDelete' => $user->hasPermission("{$resourceName}.delete"),
            'canManage' => $user->hasPermission("{$resourceName}.manage"),
        ];
    }

    /**
     * Build permissions array for custom permissions
     *
     * @param  array  $permissionList  - e.g. ['dashboard.view', 'bookings.view']
     */
    protected function buildCustomPermissions(array $permissionList): array
    {
        $user = Auth::user();
        $permissions = [];

        foreach ($permissionList as $permission) {
            $key = 'can'.str_replace(['.', '_'], '', ucwords($permission, '.'));
            $permissions[$key] = $user->hasPermission($permission);
        }

        return $permissions;
    }
}
