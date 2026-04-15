@extends('layouts.app')
@section('title', 'Review Booking - Femm Pilates')

@section('content')
<section class="min-h-screen py-16 px-4" style="background-color: #F6F8F7;">
    <div class="max-w-2xl mx-auto">

        {{-- Page Title --}}
        <div class="text-center mb-10">
            <h1 class="text-5xl font-black text-gray-900" style="font-family: 'Manrope', sans-serif; color: #111815;">
                Review Booking
            </h1>
            <p class="mt-3 text-lg font-medium" style="color: #C698C6; font-family: 'Manrope', sans-serif;">
                One step away from your next vital movement session.
            </p>
        </div>

        @if(session('error'))
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        {{-- Card --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #F4F4F5;">

            {{-- Hero Image with overlay --}}
            <div class="relative h-48 overflow-hidden">
                @if($jadwal->kelas?->gambar ?? false)
                <img src="{{ $jadwal->kelas->gambar }}" alt="{{ $jadwal->kelas->nama_kelas }}" class="w-full h-full object-cover">
                @else
                <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=1200&q=80" alt="{{ $jadwal->kelas?->nama_kelas }}" class="w-full h-full object-cover">
                @endif
                <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(17,24,21,0.75) 0%, rgba(17,24,21,0.2) 100%);"></div>
                <div class="absolute bottom-0 left-0 p-5">
                    <span class="inline-block px-3 py-1 text-xs font-bold rounded tracking-widest mb-2"
                        style="background-color: #C698C6; color: #111815; font-family: 'Manrope', sans-serif;">
                        SESSION CONFIRMATION
                    </span>
                    <p class="text-white font-black text-2xl" style="font-family: 'Manrope', sans-serif;">
                        {{ $jadwal->kelas?->nama_kelas ?? 'Class' }}
                    </p>
                </div>
            </div>

            {{-- Detail Info --}}
            <div class="p-6">
                <div class="grid grid-cols-2 gap-5">
                    {{-- Instructor --}}
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center shrink-0" style="background-color: rgba(198,152,198,0.1);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: #C698C6;"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold tracking-widest uppercase" style="color: #C698C6; font-family: 'Manrope', sans-serif;">Instructor</p>
                            <p class="text-lg font-bold mt-0.5" style="color: #111815; font-family: 'Manrope', sans-serif;">
                                {{ $jadwal->instruktur?->user?->nama ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Date & Time --}}
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center shrink-0" style="background-color: rgba(198,152,198,0.1);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: #C698C6;"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold tracking-widest uppercase" style="color: #C698C6; font-family: 'Manrope', sans-serif;">Date & Time</p>
                            <p class="text-lg font-bold mt-0.5 leading-snug" style="color: #111815; font-family: 'Manrope', sans-serif;">
                                {{ $jadwal->tanggal_kelas ? \Carbon\Carbon::parse($jadwal->tanggal_kelas)->format('D, d F Y') : '-' }}<br>
                                {{ $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('h:i A') : '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Studio --}}
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center shrink-0" style="background-color: rgba(198,152,198,0.1);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: #C698C6;"><path d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold tracking-widest uppercase" style="color: #C698C6; font-family: 'Manrope', sans-serif;">Studio</p>
                            <p class="text-lg font-bold mt-0.5" style="color: #111815; font-family: 'Manrope', sans-serif;">Femm Pilates</p>
                        </div>
                    </div>

                    {{-- Credit Usage --}}
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center shrink-0" style="background-color: rgba(198,152,198,0.1);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: #C698C6;"><path d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold tracking-widest uppercase" style="color: #C698C6; font-family: 'Manrope', sans-serif;">Credit Usage</p>
                            <p class="text-lg font-bold mt-0.5" style="color: #8D52A2; font-family: 'Manrope', sans-serif;">
                                1 Credit will be deducted
                            </p>
                            @if($sisaKredit < 1)
                            <p class="text-xs mt-0.5 text-red-500 font-medium">Kredit tidak cukup (sisa: {{ $sisaKredit }})</p>
                            @else
                            <p class="text-xs mt-0.5" style="color: #A1A1AA;">Sisa kredit Anda: {{ $sisaKredit }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Cancellation Policy --}}
                <div class="mt-6 p-4 rounded-lg border flex gap-3" style="background-color: rgba(198,152,198,0.24); border-color: #8D52A2;">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: #8D52A2;"><path d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                    <div>
                        <p class="text-sm font-bold tracking-widest uppercase" style="color: #111815; font-family: 'Manrope', sans-serif;">Cancellation Policy</p>
                        <p class="text-sm mt-1" style="color: #8D52A2; font-family: 'Manrope', sans-serif;">
                            Cancellations must be made at least 24 hours (H-1) before the class start time. Late cancellations will result in a forfeited credit.
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 flex items-center gap-3">
                    @if($sisaKredit >= 1)
                    <form method="POST" action="{{ route('booking.store') }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="id_jadwal_kelas" value="{{ $jadwal->id_jadwal_kelas }}">
                        <button type="submit"
                            class="w-full py-4 text-sm font-black tracking-widest uppercase rounded transition"
                            style="background-color: #111815; color: #C698C6; font-family: 'Manrope', sans-serif;">
                            CONFIRM BOOKING
                        </button>
                    </form>
                    @else
                    <a href="{{ route('packages.index') }}"
                        class="flex-1 block py-4 text-sm font-black tracking-widest uppercase rounded text-center transition"
                        style="background-color: #111815; color: #C698C6; font-family: 'Manrope', sans-serif;">
                        BUY PACKAGE FIRST
                    </a>
                    @endif
                    <a href="{{ url()->previous() }}"
                        class="flex-1 block py-4 text-sm font-bold tracking-widest uppercase rounded border text-center transition hover:bg-gray-50"
                        style="border-color: #C698C6; color: #C698C6; font-family: 'Manrope', sans-serif;">
                        GO BACK
                    </a>
                </div>
            </div>
        </div>

        {{-- Help text --}}
        <p class="text-center mt-6 text-sm" style="color: #A1A1AA; font-family: 'Manrope', sans-serif;">
            Need help? <a href="{{ route('contact') }}" class="hover:underline">Contact Studio Support</a>
        </p>

    </div>
</section>
@endsection
