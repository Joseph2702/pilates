@extends('layouts.app')
@section('title', ($jadwal->kelas?->nama_kelas ?? 'Kelas') . ' — Detail')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <a href="{{ route('instruktur.jadwal.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">&larr; Back to Schedule</a>
        <h1 class="text-3xl md:text-4xl font-black text-gray-900 mt-3">{{ $jadwal->kelas?->nama_kelas ?? 'Kelas' }}</h1>
        <p class="text-gray-400 text-sm mt-1">Detail jadwal dan daftar peserta.</p>
    </div>

    <div class="space-y-5">
        {{-- Jadwal Info --}}
        <div class="border border-gray-200 p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Kelas</p>
                    <p class="font-semibold text-gray-900">{{ $jadwal->kelas?->nama_kelas ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Tanggal</p>
                    <p class="font-medium text-gray-800">{{ $jadwal->tanggal_kelas ? \Carbon\Carbon::parse($jadwal->tanggal_kelas)->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Jam</p>
                    <p class="font-medium text-gray-800">
                        {{ $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '-' }}
                        &mdash;
                        {{ $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Peserta</p>
                    <p class="font-medium text-gray-800">{{ $bookings->count() }}/{{ $jadwal->kuota_maksimal }}</p>
                </div>
            </div>
        </div>

        {{-- Daftar Peserta --}}
        <div class="border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <p class="text-sm font-bold text-gray-900 uppercase tracking-widest">Daftar Peserta</p>
                <span class="text-sm text-gray-500">{{ $bookings->count() }} orang terdaftar</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">#</th>
                            <th class="px-6 py-3 text-left">Nama</th>
                            <th class="px-6 py-3 text-left">No HP</th>
                            <th class="px-6 py-3 text-left">Status Booking</th>
                            <th class="px-6 py-3 text-left">Status Absensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bookings as $i => $b)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($b->pelanggan?->user?->nama ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $b->pelanggan?->user?->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $b->pelanggan?->user?->no_hp ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <x-badge :color="$b->status_booking === 'confirmed' ? 'green' : 'yellow'">
                                    {{ $b->status_booking }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-3">
                                @if($b->absensi)
                                    <x-badge :color="$b->absensi->status_kehadiran === 'hadir' ? 'green' : 'red'">
                                        {{ $b->absensi->status_kehadiran }}
                                    </x-badge>
                                @else
                                    <span class="text-gray-400 text-xs">Belum diisi</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada peserta terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
