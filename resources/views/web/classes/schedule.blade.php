@extends('layouts.app')
@section('title', ($kelas->nama_kelas ?? 'Schedule') . ' Schedule - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">

    {{-- Title --}}
    <h1 class="text-3xl md:text-5xl font-black text-purple-500 uppercase tracking-tight mb-8">
        {{ strtoupper($kelas->nama_kelas ?? 'Class') }} SCHEDULE
    </h1>

    {{-- Filter by Instructor --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <span class="text-xs text-gray-400 uppercase tracking-widest mr-1">Filter by instructor</span>
        <a href="{{ route('classes.schedule', ['id' => $kelas->id_kelas]) }}"
            class="px-4 py-1.5 text-xs font-semibold uppercase tracking-wide transition
                {{ !request('instruktur') ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            All Instructors
        </a>
        @foreach($instrukturList as $ins)
        <a href="{{ route('classes.schedule', ['id' => $kelas->id_kelas, 'instruktur' => $ins->id_instruktur]) }}"
            class="px-4 py-1.5 text-xs font-semibold uppercase tracking-wide transition
                {{ request('instruktur') == $ins->id_instruktur ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $ins->user?->nama ?? 'Instruktur' }}
        </a>
        @endforeach
    </div>

    {{-- Date Picker --}}
    <div class="flex items-center gap-1 mb-10 overflow-x-auto pb-1">
        @foreach($dates as $date)
        <a href="{{ route('classes.schedule', ['id' => $kelas->id_kelas, 'date' => $date->format('Y-m-d'), 'instruktur' => request('instruktur')]) }}"
            class="flex flex-col items-center min-w-[52px] py-2.5 px-3 border-b-2 transition shrink-0
                {{ $selectedDate->isSameDay($date) ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:border-gray-300' }}">
            <span class="text-xs text-gray-400 uppercase">{{ $date->format('D') }}</span>
            <span class="text-lg font-black mt-0.5">{{ $date->format('d') }}</span>
        </a>
        @endforeach
    </div>

    <p class="text-xs text-gray-400 uppercase tracking-widest mb-6">{{ $selectedDate->format('l, d F Y') }}</p>

    {{-- Schedule Cards --}}
    @if($jadwalList->isEmpty())
    <div class="text-center py-24 text-gray-400 border border-dashed border-gray-200">
        <p class="text-lg font-medium">No classes scheduled for this date.</p>
        <p class="text-sm mt-1">Please select another date.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($jadwalList as $jadwal)
        @php
            $isFull = $jadwal->kuota_terisi >= $jadwal->kuota_maksimal;
            $sisaSlot = $jadwal->kuota_maksimal - $jadwal->kuota_terisi;
        @endphp
        <div class="border border-gray-200 bg-white p-5 {{ $isFull ? 'opacity-60' : '' }} hover:border-purple-200 hover:shadow-sm transition">
            <p class="text-3xl font-black text-gray-900 tracking-tight">
                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('h:i A') }}
            </p>
            <div class="flex items-center gap-3 mt-4">
                <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold shrink-0">
                    {{ strtoupper(substr($jadwal->instruktur?->user?->nama ?? 'I', 0, 2)) }}
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Instructor</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $jadwal->instruktur?->user?->nama ?? '-' }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between mt-4 text-xs">
                <span class="text-gray-500 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Capacity: {{ $jadwal->kuota_maksimal }}
                </span>
                <span class="font-semibold {{ $isFull ? 'text-red-500' : ($sisaSlot <= 3 ? 'text-amber-500' : 'text-green-600') }}">
                    {{ $isFull ? 'FULLY BOOKED' : $sisaSlot . ' slots left' }}
                </span>
            </div>
            <div class="mt-4">
                @if($isFull)
                <button disabled class="w-full py-2.5 text-sm font-semibold bg-gray-100 text-gray-400 cursor-not-allowed tracking-widest uppercase">FULLY BOOKED</button>
                @elseif(!auth()->check())
                <button onclick="openLoginModal()" class="w-full py-2.5 text-sm font-semibold bg-purple-500 hover:bg-purple-600 text-white tracking-widest uppercase transition">BOOK NOW</button>
                @else
                <form method="POST" action="{{ route('booking.store') }}">
                    @csrf
                    <input type="hidden" name="id_jadwal_kelas" value="{{ $jadwal->id_jadwal_kelas }}">
                    <button type="submit" class="w-full py-2.5 text-sm font-semibold bg-purple-500 hover:bg-purple-600 text-white tracking-widest uppercase transition">BOOK NOW</button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

</section>
@endsection
