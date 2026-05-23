<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Pelanggan;
use App\Domain\Entity\Role;
use App\Domain\Entity\User;
use App\Http\Controllers\Controller;
use App\Http\Service\ActivityLogService;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserWebController extends Controller
{
    use PassPermissionsToView;
    
    public function __construct(protected ActivityLogService $activityLog) {}
    
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($search = $request->get('search')) {
            $query->where(fn ($q) => $q->where('nama', 'ilike', "%{$search}%")->orWhere('email', 'ilike', "%{$search}%"));
        }

        $users = $query->orderBy('id_user', 'desc')->paginate(15)->withQueryString();
        $permissions = $this->buildPermissions('users');

        return view('admin.users.index', compact('users', 'permissions'));
    }

    public function create()
    {
        $roles = Role::orderBy('nama_role')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|max:20|regex:/^[0-9]*$/',
            'jenis_kelamin' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:20',
            'roles' => 'nullable|array',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create(collect($data)->except('roles')->toArray());

        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);

            $pelangganRole = Role::where('nama_role', 'pelanggan')->first();
            $hasPelangganRole = $pelangganRole && in_array($pelangganRole->id_role, array_map('intval', $data['roles']));
            if ($hasPelangganRole && !Pelanggan::where('id_user', $user->id_user)->exists()) {
                Pelanggan::create(['id_user' => $user->id_user, 'tanggal_daftar' => now()]);
            }
        }

        $this->activityLog->log(
            Auth::id(),
            'user',
            'create',
            'Membuat user baru: ' . $data['nama']
        );

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::orderBy('nama_role')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,'.$id.',id_user',
            'password' => 'nullable|string|min:6',
            'no_hp' => 'nullable|string|max:20|regex:/^[0-9]*$/',
            'jenis_kelamin' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:20',
            'roles' => 'nullable|array',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update(collect($data)->except('roles')->toArray());

        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);

            $pelangganRole = Role::where('nama_role', 'pelanggan')->first();
            $hasPelangganRole = $pelangganRole && in_array($pelangganRole->id_role, array_map('intval', $data['roles']));
            if ($hasPelangganRole && !Pelanggan::where('id_user', $user->id_user)->exists()) {
                Pelanggan::create(['id_user' => $user->id_user, 'tanggal_daftar' => now()]);
            }
        }

        $this->activityLog->log(
            Auth::id(),
            'user',
            'update',
            'Mengupdate user: ' . $data['nama']
        );

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $userName = $user->nama;
        $user->delete();
        
        $this->activityLog->log(
            Auth::id(),
            'user',
            'delete',
            'Menghapus user: ' . $userName
        );
        
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
