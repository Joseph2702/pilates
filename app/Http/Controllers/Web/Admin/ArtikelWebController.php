<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Artikel;
use App\Http\Controllers\Controller;
use App\Http\Service\ActivityLogService;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtikelWebController extends Controller
{
    use PassPermissionsToView;
    
    public function __construct(protected ActivityLogService $activityLog) {}
    
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
        $request->validate([
            'judul_artikel'  => 'required|string|max:255',
            'konten_artikel' => 'required|string',
            'gambar_file'    => 'nullable|image|max:2048',
            'gambar_artikel' => 'nullable|string',
            'tanggal_publish' => 'nullable|date',
        ]);

        $gambar = null;
        if ($request->hasFile('gambar_file')) {
            $path = $request->file('gambar_file')->store('artikel', 'public');
            $gambar = Storage::url($path);
        } elseif ($request->filled('gambar_artikel')) {
            $gambar = $request->gambar_artikel;
        }

        Artikel::create([
            'id_user'        => auth()->user()->id_user,
            'judul_artikel'  => $request->judul_artikel,
            'konten_artikel' => $request->konten_artikel,
            'gambar_artikel' => $gambar,
            'tanggal_publish' => $request->tanggal_publish,
        ]);
        
        $this->activityLog->log(
            Auth::id(),
            'artikel',
            'create',
            'Membuat artikel baru: ' . $request->judul_artikel
        );

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

        $request->validate([
            'judul_artikel'  => 'required|string|max:255',
            'konten_artikel' => 'required|string',
            'gambar_file'    => 'nullable|image|max:2048',
            'gambar_artikel' => 'nullable|string',
            'tanggal_publish' => 'nullable|date',
        ]);

        $gambar = $artikel->gambar_artikel; // keep existing by default
        if ($request->hasFile('gambar_file')) {
            $path = $request->file('gambar_file')->store('artikel', 'public');
            $gambar = Storage::url($path);
        } elseif ($request->filled('gambar_artikel')) {
            $gambar = $request->gambar_artikel;
        } elseif ($request->input('gambar_artikel') === '') {
            $gambar = null;
        }

        $artikel->update([
            'judul_artikel'  => $request->judul_artikel,
            'konten_artikel' => $request->konten_artikel,
            'gambar_artikel' => $gambar,
            'tanggal_publish' => $request->tanggal_publish,
        ]);
        
        $this->activityLog->log(
            Auth::id(),
            'artikel',
            'update',
            'Mengupdate artikel: ' . $request->judul_artikel
        );

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        $artikel = Artikel::findOrFail($id);
        $judul = $artikel->judul_artikel;
        $artikel->delete();
        
        $this->activityLog->log(
            Auth::id(),
            'artikel',
            'delete',
            'Menghapus artikel: ' . $judul
        );
        
        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
