<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Instruktur;
use App\Domain\Entity\Role;
use App\Domain\Entity\User;
use App\Http\Controllers\Controller;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $availableUsers = User::whereNotIn('id_user', Instruktur::pluck('id_user'))->orderBy('nama')->get();
        return view('admin.instruktur.create', compact('availableUsers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_user'      => 'required|integer|exists:users,id_user|unique:instruktur,id_user',
            'spesialisasi' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['id_user']);

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

        return redirect()->route('admin.instruktur.index')->with('success', 'Instruktur berhasil ditambahkan.');
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
