<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Permission;
use App\Domain\Entity\Role;
use App\Http\Controllers\Controller;
use App\Http\Service\ActivityLogService;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleWebController extends Controller
{
    use PassPermissionsToView;
    
    public function __construct(protected ActivityLogService $activityLog) {}
    
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('id_role')->get();
        $permissions = $this->buildPermissions('roles');
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('nama_permission')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_role' => 'required|string|max:50|unique:roles,nama_role',
            'is_active' => 'nullable|boolean',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['nama_role' => $data['nama_role'], 'is_active' => $data['is_active'] ?? true]);

        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }
        
        $this->activityLog->log(
            Auth::id(),
            'role',
            'create',
            'Membuat role baru: ' . $data['nama_role']
        );

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::orderBy('nama_permission')->get();
        
        // Ensure selectedPermissions is properly set
        $selectedPermissions = $role->permissions->pluck('id_permission')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    public function update(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'nama_role' => 'required|string|max:50|unique:roles,nama_role,'.$id.',id_role',
            'is_active' => 'nullable|boolean',
            'permissions' => 'nullable|array',
        ]);

        $role->update(['nama_role' => $data['nama_role'], 'is_active' => $data['is_active'] ?? true]);

        // Use method that clears cache for all users with this role
        if (isset($data['permissions'])) {
            $role->syncPermissionsWithClearCache($data['permissions']);
        } else {
            $role->permissions()->sync([]);
            $role->users()->get()->each(fn ($user) => $user->clearPermissionCache());
        }
        
        $this->activityLog->log(
            Auth::id(),
            'role',
            'update',
            'Mengupdate role: ' . $data['nama_role']
        );

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diupdate. Perubahan akan langsung berlaku untuk user dengan role ini.');
    }

    public function destroy(int $id)
    {
        $role = Role::findOrFail($id);
        $roleName = $role->nama_role;
        $role->delete();
        
        $this->activityLog->log(
            Auth::id(),
            'role',
            'delete',
            'Menghapus role: ' . $roleName
        );
        
        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
