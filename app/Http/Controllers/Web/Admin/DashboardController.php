<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Booking;
use App\Domain\Entity\JadwalKelas;
use App\Domain\Entity\Package;
use App\Domain\Entity\Pelanggan;
use App\Domain\Entity\PembelianPackage;
use App\Domain\Entity\Transaksi;
use App\Domain\Entity\User;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_pelanggan' => Pelanggan::count(),
            'total_packages' => Package::where('status_package', 'active')->count(),
            'total_bookings' => Booking::count(),
            'total_transaksi' => Transaksi::count(),
            'jadwal_upcoming' => JadwalKelas::where('tanggal_kelas', '>=', now()->startOfDay())->count(),
            'pembelian_pending' => PembelianPackage::where('status_pembelian', 'pending')->count(),
            'revenue' => Transaksi::where('status_internal', 'paid')->sum('jumlah_bayar'),
        ];

        $recentBookings = Booking::with(['pelanggan.user', 'jadwalKelas.kelas'])
            ->latest('created_at')->limit(5)->get();

        $recentTransaksi = Transaksi::with('pembelianPackage.pelanggan.user')
            ->latest('created_at')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentTransaksi'));
    }
}
