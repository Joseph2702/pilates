<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Absensi;
use App\Domain\Entity\Booking;
use App\Domain\Entity\JadwalKelas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AbsensiWebController extends Controller
{
    public function index()
    {
        $jadwalList = JadwalKelas::with(['kelas', 'instruktur.user'])
            ->withCount('bookings')
            ->orderBy('tanggal_kelas', 'desc')->paginate(15);

        return view('admin.absensi.index', compact('jadwalList'));
    }

    public function show(int $idJadwal)
    {
        $jadwal = JadwalKelas::with(['kelas', 'instruktur.user'])->findOrFail($idJadwal);

        $bookings = Booking::with(['pelanggan.user', 'absensi'])
            ->where('id_jadwal_kelas', $idJadwal)
            ->where('status_booking', '!=', 'canceled')
            ->get();

        return view('admin.absensi.show', compact('jadwal', 'bookings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_booking' => 'required|integer|exists:booking,id_booking',
            'status_kehadiran' => 'required|string|in:hadir,tidak_hadir',
        ]);

        Absensi::updateOrCreate(
            ['id_booking' => $data['id_booking']],
            ['status_kehadiran' => $data['status_kehadiran']],
        );

        return back()->with('success', 'Absensi berhasil disimpan.');
    }
}
