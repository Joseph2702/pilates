<?php

namespace App\Http\Controllers\Web\Pelanggan;

use App\Domain\Entity\Booking;
use App\Domain\Entity\Pelanggan;
use App\Domain\Entity\PembelianPackage;
use App\Domain\Entity\Transaksi;
use App\Http\Controllers\Controller;
use App\Http\Service\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileWebController extends Controller
{
    public function __construct(protected CreditService $creditService) {}

    private function getPelanggan(): ?Pelanggan
    {
        return Pelanggan::where('id_user', Auth::user()?->id_user)->first();
    }

    public function index()
    {
        $user = Auth::user();
        $pelanggan = $this->getPelanggan();
        $isInstruktur = $user->roles()->wherePivot('is_active', true)->where('nama_role', 'instruktur')->exists();

        // If instruktur, redirect to instruktur dashboard
        if ($isInstruktur) {
            return redirect()->route('instruktur.dashboard');
        }

        $activePembelian = $pelanggan
            ? PembelianPackage::with('package')
                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('status_pembelian', 'paid')
                ->where('tanggal_kadaluarsa', '>', now())
                ->orderByDesc('tanggal_kadaluarsa')
                ->first()
            : null;

        // Saldo real dari ledger mutasi_kredit, bukan dari sisa_kredit yg stale
        $sisaKredit = $pelanggan
            ? $this->creditService->getSaldo($pelanggan->id_pelanggan)
            : 0;

        $recentBookings = $pelanggan
            ? Booking::with(['jadwalKelas.kelas', 'jadwalKelas.instruktur.user'])
                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                ->latest('created_at')
                ->limit(5)
                ->get()
            : collect();

        // Pass permissions to view
        $permissions = [
            'canViewProfile' => $user->hasPermission('profile.view'),
            'canUpdateProfile' => $user->hasPermission('profile.update'),
            'canChangePassword' => $user->hasPermission('profile.change_password'),
            'canViewBooking' => $user->hasPermission('booking.view'),
            'canCreateBooking' => $user->hasPermission('booking.create'),
            'canViewPackage' => $user->hasPermission('package.view'),
            'canViewTransaction' => $user->hasPermission('transaction.view'),
        ];

        return view('web.profile.index', compact('activePembelian', 'sisaKredit', 'recentBookings', 'permissions'));
    }

    public function schedule(Request $request)
    {
        $pelanggan = $this->getPelanggan();
        $user = Auth::user();

        $query = Booking::with(['jadwalKelas.kelas', 'jadwalKelas.instruktur.user'])
            ->where('id_pelanggan', $pelanggan?->id_pelanggan ?? 0);

        if ($request->status && $request->status !== 'all') {
            $query->where('status_booking', $request->status);
        }

        $bookings = $query->orderByDesc('created_at')->paginate(10);

        // Pass permissions to view
        $permissions = [
            'canViewProfile' => $user->hasPermission('profile.view'),
            'canUpdateProfile' => $user->hasPermission('profile.update'),
            'canChangePassword' => $user->hasPermission('profile.change_password'),
            'canViewBooking' => $user->hasPermission('booking.view'),
            'canCreateBooking' => $user->hasPermission('booking.create'),
            'canViewPackage' => $user->hasPermission('package.view'),
            'canViewTransaction' => $user->hasPermission('transaction.view'),
        ];

        return view('web.profile.schedule', compact('bookings', 'permissions'));
    }

    public function packages()
    {
        $pelanggan = $this->getPelanggan();
        $user = Auth::user();

        $pembelianList = PembelianPackage::with('package')
            ->where('id_pelanggan', $pelanggan?->id_pelanggan ?? 0)
            ->orderByDesc('tanggal_pembelian')
            ->paginate(10);

        // Pass permissions to view
        $permissions = [
            'canViewProfile' => $user->hasPermission('profile.view'),
            'canUpdateProfile' => $user->hasPermission('profile.update'),
            'canChangePassword' => $user->hasPermission('profile.change_password'),
            'canViewBooking' => $user->hasPermission('booking.view'),
            'canCreateBooking' => $user->hasPermission('booking.create'),
            'canViewPackage' => $user->hasPermission('package.view'),
            'canViewTransaction' => $user->hasPermission('transaction.view'),
        ];

        return view('web.profile.packages', compact('pembelianList', 'permissions'));
    }

    public function transactions()
    {
        $pelanggan = $this->getPelanggan();
        $user = Auth::user();

        $transaksiList = Transaksi::with('pembelianPackage.package')
            ->whereHas('pembelianPackage', fn ($q) =>
                $q->where('id_pelanggan', $pelanggan?->id_pelanggan ?? 0)
            )
            ->orderByDesc('created_at')
            ->paginate(15);

        // Pass permissions to view
        $permissions = [
            'canViewProfile' => $user->hasPermission('profile.view'),
            'canUpdateProfile' => $user->hasPermission('profile.update'),
            'canChangePassword' => $user->hasPermission('profile.change_password'),
            'canViewBooking' => $user->hasPermission('booking.view'),
            'canCreateBooking' => $user->hasPermission('booking.create'),
            'canViewPackage' => $user->hasPermission('package.view'),
            'canViewTransaction' => $user->hasPermission('transaction.view'),
        ];

        return view('web.profile.transactions', compact('transaksiList', 'permissions'));
    }

    public function bookingDetail(int $id)
    {
        $pelanggan = $this->getPelanggan();
        $booking = Booking::with(['jadwalKelas.kelas', 'jadwalKelas.instruktur.user', 'absensi'])
            ->where('id_pelanggan', $pelanggan?->id_pelanggan ?? 0)
            ->findOrFail($id);

        return view('web.profile.booking-detail', compact('booking'));
    }

    public function edit()
    {
        $user = Auth::user();

        // Pass permissions to view
        $permissions = [
            'canViewProfile' => $user->hasPermission('profile.view'),
            'canUpdateProfile' => $user->hasPermission('profile.update'),
            'canChangePassword' => $user->hasPermission('profile.change_password'),
            'canViewBooking' => $user->hasPermission('booking.view'),
            'canCreateBooking' => $user->hasPermission('booking.create'),
            'canViewPackage' => $user->hasPermission('package.view'),
            'canViewTransaction' => $user->hasPermission('transaction.view'),
        ];

        return view('web.profile.edit', compact('permissions'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'nama'     => 'required|string|max:100',
            'no_hp'    => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $user->update($request->only('nama', 'no_hp', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir'));

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.'])->withFragment('change-password');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diperbarui.');
    }
}
