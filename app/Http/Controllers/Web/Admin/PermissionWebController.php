<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Permission;
use App\Http\Controllers\Controller;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;

class PermissionWebController extends Controller
{
    use PassPermissionsToView;

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

        return redirect()->route('admin.permissions.index')->with('success', 'Permission berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        Permission::findOrFail($id)->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission berhasil dihapus.');
    }
}
