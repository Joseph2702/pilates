@extends('layouts.app')
@section('title', 'Booking Detail - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <a href="{{ route('profile.schedule') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">&larr; Back to My Schedule</a>
        <h1 class="text-3xl md:text-4xl font-black text-gray-900 mt-3">Booking Detail</h1>
    </div>

    @php
        $jadwal = $booking->jadwalKelas;
        $isPast = $jadwal && \Carbon\Carbon::parse($jadwal->tanggal_kelas)->isPast();
        $isToday = $jadwal && \Carbon\Carbon::parse($jadwal->tanggal_kelas)->isToday();
        $effectiveStatus = ($booking->status_booking === 'booked' && $isPast) ? 'done' : $booking->status_booking;
        $statusClass = match($effectiveStatus) {
            'done'     => 'bg-gray-100 text-gray-600',
            'booked'   => 'bg-green-100 text-green-700',
            'canceled' => 'bg-red-100 text-red-600',
            default    => 'bg-yellow-100 text-yellow-700',
        };
        $canCancel = $booking->status_booking === 'booked' && !$isPast;
        $canRefund = $canCancel && !$isToday;
    @endphp

    <div class="max-w-2xl space-y-5">

        {{-- Status Banner --}}
        <div class="border border-gray-200 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Booking #{{ $booking->id_booking }}</p>
                <p class="text-xs text-gray-400">Booked on {{ $booking->tanggal_booking ? \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y, H:i') : '-' }}</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1.5 {{ $statusClass }}">
                {{ strtoupper($effectiveStatus) }}
            </span>
        </div>

        {{-- Class Info --}}
        <div class="border border-gray-200 p-6">
            <p class="text-xs font-semibold tracking-widest uppercase text-gray-500 mb-4">Class Information</p>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Class</p>
                    <p class="font-semibold text-gray-900">{{ $jadwal?->kelas?->nama_kelas ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Instructor</p>
                    <p class="font-medium text-gray-800">{{ $jadwal?->instruktur?->user?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Date</p>
                    <p class="font-medium text-gray-800">
                        {{ $jadwal?->tanggal_kelas ? \Carbon\Carbon::parse($jadwal->tanggal_kelas)->format('l, d M Y') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Time</p>
                    <p class="font-medium text-gray-800">
                        {{ $jadwal?->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '-' }}
                        &mdash;
                        {{ $jadwal?->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '-' }} WIB
                    </p>
                </div>
            </div>
        </div>

        {{-- Attendance --}}
        <div class="border border-gray-200 p-6">
            <p class="text-xs font-semibold tracking-widest uppercase text-gray-500 mb-4">Attendance</p>
            @if($booking->absensi)
                @php
                    $hadir = $booking->absensi->status_kehadiran === 'hadir';
                @endphp
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center {{ $hadir ? 'bg-green-100' : 'bg-red-100' }}">
                        @if($hadir)
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        @else
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                    </span>
                    <p class="text-sm font-medium text-gray-800">
                        {{ $hadir ? 'Attended' : 'Did not attend' }}
                    </p>
                </div>
            @else
                <p class="text-sm text-gray-400">Attendance not yet recorded.</p>
            @endif
        </div>

        {{-- Refund Policy Notice --}}
        @if($canCancel)
        <div class="border border-gray-200 p-5 bg-gray-50">
            <p class="text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Cancellation Policy</p>
            @if($canRefund)
            <p class="text-sm text-gray-600">You can cancel this booking and receive a <span class="font-semibold text-green-700">full credit refund</span> since it's before the class day.</p>
            @else
            <p class="text-sm text-gray-600">Cancelling on the class day will result in <span class="font-semibold text-red-600">no credit refund</span>.</p>
            @endif
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex items-center gap-4">
            @if($canCancel)
            <form method="POST" action="{{ route('booking.cancel', $booking->id_booking) }}"
                onsubmit="return confirm('{{ $canRefund ? 'Cancel booking? Your credit will be refunded.' : 'Cancel booking? No refund — cancellation is on class day.' }}')">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-6 py-2.5 text-sm font-semibold transition">
                    Cancel Booking
                </button>
            </form>
            @endif
            <a href="{{ route('profile.schedule') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Back to Schedule</a>
        </div>

    </div>
</section>
@endsection
