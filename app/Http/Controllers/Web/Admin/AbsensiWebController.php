<?php

namespace App\Http\Controllers\Web\Admin;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Booking;
use App\Domain\Entity\JadwalKelas;
use App\Http\Controllers\Controller;
use App\Http\Service\AbsensiService;
use App\Http\Service\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiWebController extends Controller
{
    public function __construct(
        protected ActivityLogService $activityLog,
        protected AbsensiService $absensiService,
    ) {}

    public function index()
    {
        $jadwalList = JadwalKelas::with(['kelas', 'instruktur.user'])
            ->withCount(['bookings' => fn ($q) => $q->where('status_booking', '!=', 'canceled')])
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

        try {
            $this->absensiService->markAttendance($data['id_booking'], $data['status_kehadiran']);
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->activityLog->log(
            Auth::id(),
            'absensi',
            'create/update',
            'Mencatat absensi untuk booking ID: '.$data['id_booking']
        );

        return back()->with('success', 'Absensi berhasil disimpan');
    }
}
