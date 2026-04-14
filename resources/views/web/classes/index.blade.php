@extends('layouts.app')
@section('title', 'Classes - Femm Pilates')

@section('content')

{{-- Hero --}}
<section class="relative h-64 md:h-80 flex items-center bg-gray-900 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/40 to-gray-900/70 z-10"></div>
    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1600&q=80" alt="Classes" class="absolute inset-0 w-full h-full object-cover opacity-60">
    <div class="relative z-20 max-w-7xl mx-auto px-6 text-center w-full">
        <span class="inline-block bg-white/10 border border-white/20 text-white text-xs font-semibold px-5 py-1.5 tracking-widest uppercase mb-5">LIMITED TIME OFFER</span>
        <h1 class="text-4xl md:text-5xl font-black text-white leading-tight">
            Start Your Journey To A<br>Balanced Life
        </h1>
        <p class="text-gray-300 mt-3 text-sm">
            New members get their <span class="text-purple-400 font-semibold">First Class for only Rp10K</span>. Experience the difference today.
        </p>
    </div>
</section>

{{-- Classes Grid --}}
<section class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($kelasList as $kelas)
        <div class="bg-white border border-gray-100 overflow-hidden hover:border-purple-200 hover:shadow-sm transition group">
            <div class="h-52 bg-gray-100 overflow-hidden relative">
                <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&q=80" alt="{{ $kelas->nama_kelas }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            </div>
            <div class="p-6">
                <h3 class="font-black text-gray-900 text-xl uppercase tracking-tight">{{ $kelas->nama_kelas }}</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">{{ $kelas->deskripsi ?? 'Experience the transformative power of Pilates in this expertly designed class.' }}</p>
                <div class="flex items-center gap-5 mt-4 text-xs text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        60 min
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        {{ $kelas->kapasitas }} max
                    </span>
                </div>
                <a href="{{ route('classes.schedule', $kelas->id_kelas) }}"
                    class="mt-5 block text-center bg-purple-500 hover:bg-purple-600 text-white py-3 text-sm font-semibold tracking-widest uppercase transition">
                    Book Now
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center py-20 text-gray-400">Belum ada kelas tersedia.</div>
        @endforelse
    </div>
</section>

@endsection
