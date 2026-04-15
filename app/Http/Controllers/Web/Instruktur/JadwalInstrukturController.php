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

        // Group by month for My Schedule view — load bookings inline
        $jadwalList = JadwalKelas::with(['kelas', 'bookings.pelanggan.user'])
            ->withCount(['bookings' => fn ($q) => $q->where('status_booking', '!=', 'canceled')])
            ->where('id_instruktur', $instruktur->id_instruktur)
            ->orderBy('tanggal_kelas', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->paginate(10);

        return view('instruktur.jadwal.index', compact('jadwalList'));
    }

    public function show(int $id)
    {
        $instruktur = Auth::user()->instruktur;

        $jadwal = JadwalKelas::with([
            'kelas',
            'bookings.pelanggan.user',
            'bookings.absensi',
        ])
            ->where('id_instruktur', $instruktur->id_instruktur)
            ->findOrFail($id);

        $bookings = $jadwal->bookings->where('status_booking', '!=', 'canceled');

        return view('instruktur.jadwal.show', compact('jadwal', 'bookings'));
    }
}
