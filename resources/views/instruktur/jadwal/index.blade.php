@extends('layouts.app')
@section('title', 'My Schedule - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-gray-900">My Account</h1>
        <p class="text-gray-400 text-sm mt-1">View and manage your class schedule.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        @include('instruktur._sidebar', ['todayClass' => null])

        <div class="lg:col-span-3 space-y-4">

            {{-- Filter Tabs --}}
            <div class="flex gap-2 mb-4">
                @foreach(['upcoming' => 'Upcoming', 'done' => 'Done'] as $val => $label)
                <a href="{{ route('instruktur.jadwal.index', ['filter' => $val]) }}"
                    class="px-4 py-1.5 text-xs font-medium transition {{ ($filter ?? 'upcoming') === $val ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            @forelse($jadwalList as $j)
            @php
                $isToday = $j->tanggal_kelas && \Carbon\Carbon::parse($j->tanggal_kelas)->isToday();
                $isPast  = $j->tanggal_kelas && \Carbon\Carbon::parse($j->tanggal_kelas)->isPast() && !$isToday;
                $activeBookings = $j->bookings->where('status_booking', '!=', 'canceled');
            @endphp

            <div class="border {{ $isToday ? 'border-purple-300 ring-2 ring-purple-100' : 'border-gray-200' }} overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        {{-- Date badge --}}
                        <div class="text-center {{ $isToday ? 'bg-purple-600 text-white' : ($isPast ? 'bg-gray-100 text-gray-500' : 'bg-gray-900 text-white') }} px-4 py-2 min-w-[64px]">
                            <div class="text-xs font-semibold uppercase">{{ $j->tanggal_kelas ? \Carbon\Carbon::parse($j->tanggal_kelas)->format('M') : '-' }}</div>
                            <div class="text-2xl font-bold leading-none">{{ $j->tanggal_kelas ? \Carbon\Carbon::parse($j->tanggal_kelas)->format('d') : '-' }}</div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-gray-900 text-base">{{ $j->kelas?->nama_kelas ?? '-' }}</p>
                                @if($isToday)
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold">Hari Ini</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '--' }}
                                &mdash;
                                {{ $j->jam_selesai ? \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') : '--' }}
                                &bull; {{ $activeBookings->count() }}/{{ $j->kuota_maksimal }} peserta
                            </p>
                        </div>
                    </div>

                    @if($isToday)
                    <a href="{{ route('instruktur.absensi.show', $j->id_jadwal_kelas) }}"
                       class="bg-purple-600 text-white px-4 py-2 text-sm font-semibold hover:bg-purple-700 transition uppercase tracking-wide">
                        Kelola Absensi
                    </a>
                    @endif
                </div>

                {{-- Peserta list --}}
                @if($activeBookings->isNotEmpty())
                <div class="border-t border-gray-100 px-6 py-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Peserta Terdaftar</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($activeBookings as $b)
                        <div class="flex items-center gap-3 bg-gray-50 px-3 py-2">
                            <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($b->pelanggan?->user?->nama ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $b->pelanggan?->user?->nama ?? '-' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $b->pelanggan?->user?->no_hp ?? '-' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="border-t border-gray-100 px-6 py-4 text-center text-sm text-gray-400">
                    Belum ada peserta terdaftar
                </div>
                @endif
            </div>
            @empty
            <div class="border border-gray-200 p-12 text-center">
                <p class="text-gray-500 font-medium">Belum ada jadwal kelas.</p>
            </div>
            @endforelse

            <div>{{ $jadwalList->withQueryString()->links() }}</div>

        </div>
    </div>
</section>
@endsection
