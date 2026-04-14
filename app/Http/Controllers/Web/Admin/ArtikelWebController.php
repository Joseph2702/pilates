<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Artikel;
use App\Http\Controllers\Controller;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;

class ArtikelWebController extends Controller
{
    use PassPermissionsToView;
    
    public function index()
    {
        $artikels = Artikel::with('user')->orderBy('created_at', 'desc')->paginate(15);
        $permissions = $this->buildPermissions('artikel');
        return view('admin.artikel.index', compact('artikels', 'permissions'));
    }

    public function create()
    {
        return view('admin.artikel.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul_artikel' => 'required|string|max:255',
            'konten_artikel' => 'required|string',
            'gambar_artikel' => 'nullable|string',
            'tanggal_publish' => 'nullable|date',
        ]);

        $data['id_user'] = auth()->user()->id_user;
        Artikel::create($data);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $artikel = Artikel::findOrFail($id);
        return view('admin.artikel.edit', compact('artikel'));
    }

    public function update(Request $request, int $id)
    {
        $artikel = Artikel::findOrFail($id);

        $data = $request->validate([
            'judul_artikel' => 'required|string|max:255',
            'konten_artikel' => 'required|string',
            'gambar_artikel' => 'nullable|string',
            'tanggal_publish' => 'nullable|date',
        ]);

        $artikel->update($data);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        Artikel::findOrFail($id)->delete();
        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
