@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Total Users</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_users']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Total Pelanggan</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_pelanggan']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Active Packages</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_packages']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Total Bookings</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_bookings']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Total Transaksi</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_transaksi']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Jadwal Upcoming</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['jadwal_upcoming']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Pembelian Pending</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ number_format($stats['pembelian_pending']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Revenue (Paid)</p>
        <p class="text-3xl font-bold text-green-600 mt-1">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Bookings --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Booking Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Pelanggan</th>
                        <th class="px-6 py-3 text-left">Kelas</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentBookings as $b)
                    <tr>
                        <td class="px-6 py-3">{{ $b->pelanggan?->user?->nama ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $b->jadwalKelas?->kelas?->nama_kelas ?? '-' }}</td>
                        <td class="px-6 py-3"><x-badge :color="$b->status_booking === 'confirmed' ? 'green' : ($b->status_booking === 'canceled' ? 'red' : 'yellow')">{{ $b->status_booking }}</x-badge></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400">Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Transaksi --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Transaksi Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Order ID</th>
                        <th class="px-6 py-3 text-left">Pelanggan</th>
                        <th class="px-6 py-3 text-left">Jumlah</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentTransaksi as $t)
                    <tr>
                        <td class="px-6 py-3 font-mono text-xs">{{ $t->order_id }}</td>
                        <td class="px-6 py-3">{{ $t->pembelianPackage?->pelanggan?->user?->nama ?? '-' }}</td>
                        <td class="px-6 py-3">Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="px-6 py-3"><x-badge :color="$t->status_internal === 'paid' ? 'green' : ($t->status_internal === 'failed' ? 'red' : 'yellow')">{{ $t->status_internal }}</x-badge></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
