@extends('layouts.app')
@section('title', 'Absensi - ' . ($jadwal->kelas?->nama_kelas ?? 'Kelas'))

@section('content')
@php
    $classOngoing = \Carbon\Carbon::now()->between(
        \Carbon\Carbon::parse($jadwal->jam_mulai),
        \Carbon\Carbon::parse($jadwal->jam_selesai)
    );
    $classNotStarted = \Carbon\Carbon::now()->lt(\Carbon\Carbon::parse($jadwal->jam_mulai));
@endphp
<section class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <a href="{{ route('instruktur.absensi.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">&larr; Back to Absensi</a>
        <h1 class="text-3xl md:text-4xl font-black text-gray-900 mt-3">{{ $jadwal->kelas?->nama_kelas ?? 'Kelas' }}</h1>
        <p class="text-gray-400 text-sm mt-1">Input kehadiran peserta untuk kelas hari ini.</p>
    </div>

    @if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
        {{ session('success') }}
    </div>
    @endif

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
                    <p class="font-medium text-gray-800">{{ $bookings->count() }} orang</p>
                </div>
            </div>
        </div>

        {{-- Daftar Peserta & Absensi --}}
        <div class="border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <p class="text-sm font-bold text-gray-900 uppercase tracking-widest">Daftar Peserta & Absensi</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">#</th>
                            <th class="px-6 py-3 text-left">Pelanggan</th>
                            <th class="px-6 py-3 text-left">Status Booking</th>
                            <th class="px-6 py-3 text-left">Kehadiran</th>
                            <th class="px-6 py-3 text-left">Aksi</th>
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
                            <td class="px-6 py-3">
                                <x-badge :color="$b->status_booking === 'confirmed' ? 'green' : 'yellow'">{{ $b->status_booking }}</x-badge>
                            </td>
                            <td class="px-6 py-3">
                                @if($b->absensi)
                                    <x-badge :color="$b->absensi->status_kehadiran === 'hadir' ? 'green' : 'red'">{{ $b->absensi->status_kehadiran }}</x-badge>
                                @else
                                    <span class="text-gray-400 text-xs">Belum diisi</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                @if($classOngoing)
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('instruktur.absensi.store') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="id_booking" value="{{ $b->id_booking }}">
                                        <input type="hidden" name="status_kehadiran" value="hadir">
                                        <button type="submit" class="px-3 py-1 text-xs font-semibold {{ $b->absensi?->status_kehadiran === 'hadir' ? 'bg-green-600 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100' }} transition">Hadir</button>
                                    </form>
                                    <form method="POST" action="{{ route('instruktur.absensi.store') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="id_booking" value="{{ $b->id_booking }}">
                                        <input type="hidden" name="status_kehadiran" value="tidak_hadir">
                                        <button type="submit" class="px-3 py-1 text-xs font-semibold {{ $b->absensi?->status_kehadiran === 'tidak_hadir' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }} transition">Tidak Hadir</button>
                                    </form>
                                </div>
                                @else
                                <span class="text-xs text-gray-400">{{ $classNotStarted ? 'Kelas belum dimulai' : 'Kelas sudah selesai' }}</span>
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
