<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Pelanggan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PelangganWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with('user');

        if ($search = $request->get('search')) {
            $query->whereHas('user', fn ($q) => $q->where('nama', 'ilike', "%{$search}%")->orWhere('email', 'ilike', "%{$search}%"));
        }

        $pelangganList = $query->orderBy('id_pelanggan', 'desc')->paginate(15)->withQueryString();

        return view('admin.pelanggan.index', compact('pelangganList'));
    }

    public function show(int $id)
    {
        $pelanggan = Pelanggan::with(['user', 'pembelianPackage.package', 'bookings.jadwalKelas.kelas', 'mutasiKredit'])->findOrFail($id);
        return view('admin.pelanggan.show', compact('pelanggan'));
    }

    public function destroy(int $id)
    {
        Pelanggan::findOrFail($id)->delete();
        return redirect()->route('admin.pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
