<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Kelas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasWebController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->paginate(15);
        $user = Auth::user();
        
        $permissions = [
            'canCreate' => $user->hasPermission('kelas.create'),
            'canUpdate' => $user->hasPermission('kelas.update'),
            'canDelete' => $user->hasPermission('kelas.delete'),
        ];
        
        return view('admin.kelas.index', compact('kelasList', 'permissions'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'kapasitas' => 'required|integer|min:1',
        ]);

        Kelas::create($data);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $kelas = Kelas::findOrFail($id);
        return view('admin.kelas.edit', compact('kelas'));
    }

    public function update(Request $request, int $id)
    {
        $kelas = Kelas::findOrFail($id);

        $data = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $kelas->update($data);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        Kelas::findOrFail($id)->delete();
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
