<?php

namespace App\Http\Controllers\Web\Pelanggan;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Booking;
use App\Domain\Entity\JadwalKelas;
use App\Domain\Entity\Pelanggan;
use App\Http\Controllers\Controller;
use App\Http\Service\BookingService;
use App\Http\Service\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingWebController extends Controller
{
    public function __construct(
        protected BookingService $bookings,
        protected CreditService $creditService,
    ) {}

    public function review(int $id)
    {
        $jadwal = JadwalKelas::with(['kelas', 'instruktur.user'])->findOrFail($id);

        if ($jadwal->kuota_terisi >= $jadwal->kuota_maksimal) {
            return redirect()->back()->with('error', 'Jadwal sudah penuh.');
        }

        $pelanggan = Pelanggan::where('id_user', Auth::id())->first();
        $sisaKredit = $pelanggan ? $this->creditService->getSaldo($pelanggan->id_pelanggan) : 0;

        return view('web.booking.review', compact('jadwal', 'sisaKredit'));
    }

    public function store(Request $request)
    {
        $request->validate(['id_jadwal_kelas' => 'required|integer']);

        if (! Auth::check()) {
            return redirect()->route('web.login');
        }

        $pelanggan = Pelanggan::where('id_user', Auth::id())->first();
        if (! $pelanggan) {
            return back()->with('error', 'Akun pelanggan tidak ditemukan.');
        }

        try {
            $this->bookings->book($pelanggan->id_pelanggan, (int) $request->id_jadwal_kelas);
            return back()->with('success', 'Booking berhasil! Kelas telah dikonfirmasi.');
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(int $id)
    {
        $booking = Booking::findOrFail($id);

        $pelanggan = Pelanggan::where('id_user', Auth::id())->first();
        if (! $pelanggan || $booking->id_pelanggan !== $pelanggan->id_pelanggan) {
            abort(403);
        }

        try {
            $this->bookings->cancel($id);
            return redirect()->route('profile.schedule', ['status' => 'canceled'])
                ->with('success', 'Booking berhasil dibatalkan.');
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
