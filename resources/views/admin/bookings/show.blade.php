@extends('layouts.admin')
@section('title', 'Detail Booking')

@section('actions')
<a href="{{ route('admin.bookings.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Kembali</a>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        @php
            $isPastClass = $booking->jadwalKelas && \Carbon\Carbon::parse($booking->jadwalKelas->tanggal_kelas)->isPast();
            $effectiveStatus = ($booking->status_booking === 'booked' && $isPastClass) ? 'done' : $booking->status_booking;
            $statusColor = match($effectiveStatus) {
                'done'     => 'gray',
                'booked'   => 'green',
                'canceled' => 'red',
                default    => 'yellow',
            };
        @endphp
        <h3 class="font-semibold text-gray-800 mb-4">Info Booking</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">ID Booking</dt><dd class="font-medium text-gray-900">{{ $booking->id_booking }}</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd><x-badge :color="$statusColor">{{ strtoupper($effectiveStatus) }}</x-badge></dd></div>
            <div><dt class="text-gray-500">Pelanggan</dt><dd>{{ $booking->pelanggan?->user?->nama ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Email</dt><dd>{{ $booking->pelanggan?->user?->email ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Kelas</dt><dd>{{ $booking->jadwalKelas?->kelas?->nama_kelas ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Instruktur</dt><dd>{{ $booking->jadwalKelas?->instruktur?->user?->nama ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Tanggal Kelas</dt><dd>{{ $booking->jadwalKelas?->tanggal_kelas ? \Carbon\Carbon::parse($booking->jadwalKelas->tanggal_kelas)->format('d M Y') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Jam</dt><dd>{{ $booking->jadwalKelas?->jam_mulai ? \Carbon\Carbon::parse($booking->jadwalKelas->jam_mulai)->format('H:i') : '-' }} &mdash; {{ $booking->jadwalKelas?->jam_selesai ? \Carbon\Carbon::parse($booking->jadwalKelas->jam_selesai)->format('H:i') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Tanggal Booking</dt><dd>{{ $booking->tanggal_booking ? \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y H:i') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Absensi</dt><dd>
                @if($booking->absensi)
                    <x-badge :color="$booking->absensi->status_kehadiran === 'hadir' ? 'green' : 'red'">{{ $booking->absensi->status_kehadiran }}</x-badge>
                @else
                    <span class="text-gray-400">Belum dicatat</span>
                @endif
            </dd></div>
        </dl>
    </div>
</div>
@endsection
