<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['pelanggan.user', 'jadwalKelas.kelas']);

        $status = $request->get('status');
        if ($status === 'done') {
            // done = booked but class date has passed
            $query->where('status_booking', 'booked')
                ->whereHas('jadwalKelas', fn ($q) => $q->where('tanggal_kelas', '<', now()->startOfDay()));
        } elseif ($status) {
            $query->where('status_booking', $status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(int $id)
    {
        $booking = Booking::with(['pelanggan.user', 'jadwalKelas.kelas', 'jadwalKelas.instruktur.user', 'absensi'])->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }
}
