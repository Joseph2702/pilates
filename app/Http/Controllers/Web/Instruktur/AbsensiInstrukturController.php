<?php

namespace App\Http\Controllers\Web\Instruktur;

use App\Domain\Entity\Absensi;
use App\Domain\Entity\Booking;
use App\Domain\Entity\JadwalKelas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiInstrukturController extends Controller
{
    public function index()
    {
        $instruktur = Auth::user()->instruktur;
        $today = today();

        $jadwalList = JadwalKelas::with('kelas')
            ->withCount(['bookings' => fn ($q) => $q->where('status_booking', '!=', 'canceled')])
            ->where('id_instruktur', $instruktur->id_instruktur)
            ->whereDate('tanggal_kelas', $today)
            ->orderBy('jam_mulai')
            ->get();

        return view('instruktur.absensi.index', compact('jadwalList', 'today'));
    }

    public function show(int $idJadwal)
    {
        $instruktur = Auth::user()->instruktur;

        $jadwal = JadwalKelas::with('kelas')
            ->where('id_instruktur', $instruktur->id_instruktur)
            ->findOrFail($idJadwal);

        $bookings = Booking::with(['pelanggan.user', 'absensi'])
            ->where('id_jadwal_kelas', $idJadwal)
            ->where('status_booking', '!=', 'canceled')
            ->get();

        return view('instruktur.absensi.show', compact('jadwal', 'bookings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_booking' => 'required|integer|exists:booking,id_booking',
            'status_kehadiran' => 'required|string|in:hadir,tidak_hadir',
        ]);

        // Verify this booking belongs to the instruktur's class
        $instruktur = Auth::user()->instruktur;
        $booking = Booking::with('jadwalKelas')->findOrFail($data['id_booking']);

        if ($booking->jadwalKelas->id_instruktur !== $instruktur->id_instruktur) {
            abort(403, 'Anda tidak memiliki akses ke booking ini.');
        }

        Absensi::updateOrCreate(
            ['id_booking' => $data['id_booking']],
            ['status_kehadiran' => $data['status_kehadiran']],
        );

        return back()->with('success', 'Absensi berhasil disimpan.');
    }
}
