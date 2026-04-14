<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Instruktur;
use App\Domain\Entity\Role;
use App\Domain\Entity\User;
use App\Http\Controllers\Controller;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstrukturWebController extends Controller
{
    use PassPermissionsToView;
    
    public function index()
    {
        $instrukturList = Instruktur::with('user')->orderBy('id_instruktur', 'desc')->paginate(15);
        $permissions = $this->buildPermissions('instruktur');
        return view('admin.instruktur.index', compact('instrukturList', 'permissions'));
    }

    public function create()
    {
        return view('admin.instruktur.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'          => 'required|string|max:100',
            'email'         => 'required|email|max:100|unique:users,email',
            'password'      => 'required|string|min:6',
            'no_hp'         => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|string|max:10',
            'spesialisasi'  => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'nama'          => $data['nama'],
                'email'         => $data['email'],
                'password'      => Hash::make($data['password']),
                'no_hp'         => $data['no_hp'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'status'        => 'active',
            ]);

            Instruktur::create([
                'id_user'      => $user->id_user,
                'spesialisasi' => $data['spesialisasi'] ?? null,
            ]);

            $instrukturRole = Role::firstOrCreate(
                ['nama_role' => 'instruktur'],
                ['is_active' => true]
            );
            $user->roles()->syncWithoutDetaching([
                $instrukturRole->id_role => ['is_active' => true],
            ]);
        });

        return redirect()->route('admin.instruktur.index')->with('success', 'Akun instruktur berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $instruktur = Instruktur::with('user')->findOrFail($id);
        $users = User::orderBy('nama')->get();
        return view('admin.instruktur.edit', compact('instruktur', 'users'));
    }

    public function update(Request $request, int $id)
    {
        $instruktur = Instruktur::findOrFail($id);

        $data = $request->validate([
            'spesialisasi' => 'nullable|string|max:50',
        ]);

        $instruktur->update($data);

        return redirect()->route('admin.instruktur.index')->with('success', 'Instruktur berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        Instruktur::findOrFail($id)->delete();
        return redirect()->route('admin.instruktur.index')->with('success', 'Instruktur berhasil dihapus.');
    }
}
