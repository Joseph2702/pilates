<?php

namespace App\Http\Controllers\Web\Instruktur;

use App\Domain\Entity\JadwalKelas;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class JadwalInstrukturController extends Controller
{
    public function index()
    {
        $instruktur = Auth::user()->instruktur;

        $jadwalList = JadwalKelas::with('kelas')
            ->where('id_instruktur', $instruktur->id_instruktur)
            ->orderBy('tanggal_kelas', 'desc')
            ->paginate(15);

        return view('instruktur.jadwal.index', compact('jadwalList'));
    }

    public function show(int $id)
    {
        $instruktur = Auth::user()->instruktur;

        $jadwal = JadwalKelas::with(['kelas', 'bookings.pelanggan.user'])
            ->where('id_instruktur', $instruktur->id_instruktur)
            ->findOrFail($id);

        return view('instruktur.jadwal.show', compact('jadwal'));
    }
}
