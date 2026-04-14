<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Promo;
use App\Http\Controllers\Controller;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;

class PromoWebController extends Controller
{
    use PassPermissionsToView;
    
    public function index()
    {
        $promos = Promo::orderBy('created_at', 'desc')->paginate(15);
        $permissions = $this->buildPermissions('promo');
        return view('admin.promo.index', compact('promos', 'permissions'));
    }

    public function create()
    {
        return view('admin.promo.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_promo' => 'required|string|max:50',
            'nama_promo' => 'required|string|max:100',
            'persenan_diskon' => 'required|numeric|min:0|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status_promo' => 'required|string|in:active,inactive',
        ]);

        Promo::create($data);

        return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $promo = Promo::findOrFail($id);
        return view('admin.promo.edit', compact('promo'));
    }

    public function update(Request $request, int $id)
    {
        $promo = Promo::findOrFail($id);

        $data = $request->validate([
            'kode_promo' => 'required|string|max:50',
            'nama_promo' => 'required|string|max:100',
            'persenan_diskon' => 'required|numeric|min:0|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status_promo' => 'required|string|in:active,inactive',
        ]);

        $promo->update($data);

        return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        Promo::findOrFail($id)->delete();
        return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil dihapus.');
    }
}
