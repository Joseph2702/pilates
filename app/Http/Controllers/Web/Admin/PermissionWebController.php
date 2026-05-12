<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Permission;
use App\Http\Controllers\Controller;
use App\Http\Service\ActivityLogService;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionWebController extends Controller
{
    use PassPermissionsToView;
    
    public function __construct(protected ActivityLogService $activityLog) {}

    public function index()
    {
        $data = Permission::orderBy('nama_permission')->get();
        $permissions = $this->buildPermissions('permission');
        return view('admin.permissions.index', compact('data', 'permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_permission' => 'required|string|max:100|unique:permissions,nama_permission',
            'deskripsi' => 'nullable|string',
        ]);

        Permission::create($data);
        
        $this->activityLog->log(
            Auth::id(),
            'permission',
            'create',
            'Membuat permission baru: ' . $data['nama_permission']
        );

        return redirect()->route('admin.permissions.index')->with('success', 'Permission berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, int $id)
    {
        $permission = Permission::findOrFail($id);

        $data = $request->validate([
            'nama_permission' => 'required|string|max:100|unique:permissions,nama_permission,'.$id.',id_permission',
            'deskripsi' => 'nullable|string',
        ]);

        $permission->update($data);
        
        $this->activityLog->log(
            Auth::id(),
            'permission',
            'update',
            'Mengupdate permission: ' . $data['nama_permission']
        );

        return redirect()->route('admin.permissions.index')->with('success', 'Permission berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        $permission = Permission::findOrFail($id);
        $permName = $permission->nama_permission;
        $permission->delete();
        
        $this->activityLog->log(
            Auth::id(),
            'permission',
            'delete',
            'Menghapus permission: ' . $permName
        );
        
        return redirect()->route('admin.permissions.index')->with('success', 'Permission berhasil dihapus.');
    }
}
