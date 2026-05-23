<?php

namespace App\Http\Controllers\Web\Instruktur;

use App\Domain\Entity\JadwalKelas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalInstrukturController extends Controller
{
    public function index(Request $request)
    {
        $instruktur = Auth::user()->instruktur;
        $filter = $request->get('filter', 'upcoming');

        $query = JadwalKelas::with(['kelas', 'bookings.pelanggan.user'])
            ->withCount(['bookings' => fn ($q) => $q->where('status_booking', '!=', 'canceled')])
            ->where('id_instruktur', $instruktur->id_instruktur);

        if ($filter === 'done') {
            $query->where('tanggal_kelas', '<', now()->startOfDay());
        } else {
            $query->where('tanggal_kelas', '>=', now()->startOfDay());
        }

        $jadwalList = $query->orderBy('tanggal_kelas', $filter === 'done' ? 'desc' : 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->paginate(10);

        return view('instruktur.jadwal.index', compact('jadwalList', 'filter'));
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
