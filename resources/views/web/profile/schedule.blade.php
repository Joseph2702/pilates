@extends('layouts.app')
@section('title', 'My Schedule - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-gray-900">My Account</h1>
        <p class="text-gray-400 text-sm mt-1">Manage your Precision Pilates journey, view your progress, and schedule your next session.</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- Sidebar --}}
        @include('web.profile._sidebar')

        {{-- Main --}}
        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">My Schedule</h2>
                @if($permissions['canCreateBooking'])
                <a href="{{ route('classes.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-5 py-2 text-xs font-semibold tracking-wide transition">
                    + BOOK A CLASS
                </a>
                @endif
            </div>

            {{-- Filter Tabs --}}
            <div class="flex gap-2 mb-6">
                @foreach(['all' => 'All', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'] as $val => $label)
                <a href="{{ route('profile.schedule', ['status' => $val]) }}"
                    class="px-4 py-1.5 text-xs font-medium transition {{ request('status', 'all') === $val ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            @if($bookings->isEmpty())
            <div class="border border-dashed border-gray-300 py-20 text-center text-gray-400">
                <p class="text-lg">No bookings found.</p>
                <p class="text-sm mt-1">Book a class to get started.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($bookings as $booking)
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
                    $canCancel = $permissions['canCreateBooking'] && $booking->status_booking === 'booked' && !$isPast;
                    $canRefund = $canCancel && !$isToday;
                @endphp
                <div class="border border-gray-200 p-5 flex items-center justify-between gap-4 {{ $isPast ? 'opacity-60' : '' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-50 flex flex-col items-center justify-center text-purple-600 shrink-0">
                            <span class="text-lg font-black leading-none">{{ $jadwal ? \Carbon\Carbon::parse($jadwal->tanggal_kelas)->format('d') : '-' }}</span>
                            <span class="text-xs uppercase">{{ $jadwal ? \Carbon\Carbon::parse($jadwal->tanggal_kelas)->format('M') : '' }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $jadwal?->kelas?->nama_kelas ?? '-' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $jadwal ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '-' }}
                                · {{ $jadwal?->instruktur?->user?->nama ?? 'Instructor' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold px-2.5 py-1 {{ $statusClass }}">
                            {{ strtoupper($effectiveStatus) }}
                        </span>
                        <a href="{{ route('profile.booking.show', $booking->id_booking) }}" class="text-xs text-gray-500 hover:text-purple-600 font-medium transition">Detail</a>
                        @if($canCancel)
                        <form method="POST" action="{{ route('booking.cancel', $booking->id_booking) }}"
                            onsubmit="return confirm('{{ $canRefund ? 'Cancel this booking? Your credit will be refunded.' : 'Cancel this booking? No refund — cancellation is on the class day.' }}')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium transition">Cancel</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($bookings->hasPages())
            <div class="mt-8">{{ $bookings->withQueryString()->links() }}</div>
            @endif
            @endif
        </div>
    </div>
</section>
@endsection
