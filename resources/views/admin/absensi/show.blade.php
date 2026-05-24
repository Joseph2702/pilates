@extends('layouts.admin')
@section('title', 'Absensi - ' . ($jadwal->kelas?->nama_kelas ?? 'Kelas'))

@section('actions')
<a href="{{ route('admin.absensi.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Kembali</a>
@endsection

@section('content')
@php
    $classOngoing = \Carbon\Carbon::now()->between(
        \Carbon\Carbon::parse($jadwal->jam_mulai),
        \Carbon\Carbon::parse($jadwal->jam_selesai)
    );
    $classNotStarted = \Carbon\Carbon::now()->lt(\Carbon\Carbon::parse($jadwal->jam_mulai));
@endphp
<div class="max-w-4xl space-y-6">
    {{-- Jadwal Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><dt class="text-gray-500">Kelas</dt><dd class="font-medium text-gray-900">{{ $jadwal->kelas?->nama_kelas ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Instruktur</dt><dd>{{ $jadwal->instruktur?->user?->nama ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Tanggal</dt><dd>{{ $jadwal->tanggal_kelas ? \Carbon\Carbon::parse($jadwal->tanggal_kelas)->format('d M Y') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Jam</dt><dd>{{ $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '-' }} &mdash; {{ $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '-' }}</dd></div>
        </dl>
    </div>

    {{-- Daftar Peserta --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Daftar Peserta</h3>
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
                        <td class="px-6 py-3">{{ $i + 1 }}</td>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $b->pelanggan?->user?->nama ?? '-' }}</td>
                        <td class="px-6 py-3"><x-badge :color="$b->status_booking === 'confirmed' ? 'green' : 'yellow'">{{ $b->status_booking }}</x-badge></td>
                        <td class="px-6 py-3">
                            @if($b->absensi)
                                <x-badge :color="$b->absensi->status_kehadiran === 'hadir' ? 'green' : 'red'">{{ $b->absensi->status_kehadiran }}</x-badge>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if($classOngoing)
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.absensi.store') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="id_booking" value="{{ $b->id_booking }}">
                                    <input type="hidden" name="status_kehadiran" value="hadir">
                                    <button type="submit" class="px-3 py-1 text-xs font-medium rounded-lg {{ $b->absensi?->status_kehadiran === 'hadir' ? 'bg-green-600 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100' }} transition">Hadir</button>
                                </form>
                                <form method="POST" action="{{ route('admin.absensi.store') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="id_booking" value="{{ $b->id_booking }}">
                                    <input type="hidden" name="status_kehadiran" value="tidak_hadir">
                                    <button type="submit" class="px-3 py-1 text-xs font-medium rounded-lg {{ $b->absensi?->status_kehadiran === 'tidak_hadir' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }} transition">Tidak Hadir</button>
                                </form>
                            </div>
                            @else
                            <span class="text-xs text-gray-400">{{ $classNotStarted ? 'Kelas belum dimulai' : 'Kelas sudah selesai' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada peserta terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
