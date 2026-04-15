@extends('layouts.app')
@section('title', 'Manage Absensi - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-gray-900">My Account</h1>
        <p class="text-gray-400 text-sm mt-1">Manage attendance for today's classes.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        @include('instruktur._sidebar', ['todayClass' => null])

        <div class="lg:col-span-3 space-y-4">

            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500">{{ $today->translatedFormat('l, d F Y') }}</p>
                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold">
                    {{ $jadwalList->count() }} kelas hari ini
                </span>
            </div>

            @if($jadwalList->isEmpty())
            <div class="border border-gray-200 p-12 text-center">
                <p class="text-gray-500 font-medium">Tidak ada kelas hari ini.</p>
                <p class="text-gray-400 text-sm mt-1">Silakan cek jadwal kelas Anda di menu My Schedule.</p>
            </div>
            @else
            @foreach($jadwalList as $j)
            <div class="border border-purple-200 ring-1 ring-purple-100 overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="text-center bg-purple-50 px-3 py-2 min-w-[72px]">
                            <div class="text-lg font-bold text-purple-700">{{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '--' }}</div>
                            <div class="text-xs text-purple-500">{{ $j->jam_selesai ? \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') : '--' }}</div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $j->kelas?->nama_kelas ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $j->bookings_count }} peserta terdaftar &bull; Kapasitas {{ $j->kuota_maksimal }}</p>
                        </div>
                    </div>
                    <a href="{{ route('instruktur.absensi.show', $j->id_jadwal_kelas) }}"
                       class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2 text-sm font-semibold transition uppercase tracking-wide">
                        Kelola Absensi
                    </a>
                </div>
            </div>
            @endforeach
            @endif

        </div>
    </div>
</section>
@endsection
