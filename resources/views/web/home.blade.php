@extends('layouts.app')
@section('title', 'Femm Pilates - Precision & Vitality')

@section('content')

{{-- HERO --}}
<section class="relative h-[calc(90vh-4rem)] min-h-[580px] flex items-center bg-gray-900 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-gray-900/85 to-gray-900/40 z-10"></div>
    <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=1600&q=80" alt="Pilates Studio" class="absolute inset-0 w-full h-full object-cover opacity-60">
    <div class="relative z-20 max-w-7xl mx-auto px-6 py-20">
        <p class="text-xs tracking-widest uppercase text-purple-400 mb-4">Precision Vitality</p>
        <h1 class="text-6xl md:text-8xl font-black text-white leading-none tracking-tight uppercase">
            PRECISION<br>
            <span class="text-purple-400">VITALITY</span>
        </h1>
        <p class="mt-6 text-gray-300 max-w-md text-base leading-relaxed">
            Experience the transformative power of Femm Pilates. Find your ideal classes among our studio's top Pilates offerings.
        </p>
        <div class="mt-8 flex items-center gap-4">
            <a href="{{ route('classes.index') }}" class="inline-flex items-center gap-2 bg-purple-500 hover:bg-purple-600 text-white px-8 py-3 text-sm font-semibold tracking-widest uppercase transition">
                BOOK A CLASS
            </a>
            <a href="{{ route('packages.index') }}" class="inline-flex items-center gap-2 border border-white/40 text-white hover:bg-white/10 px-8 py-3 text-sm font-semibold tracking-widest uppercase transition">
                VIEW PACKAGES
            </a>
        </div>
    </div>
</section>

{{-- CURATED MOVEMENT --}}
<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="flex items-start justify-between gap-6 mb-10 flex-wrap">
        <div>
            <p class="text-xs font-semibold tracking-widest text-purple-500 uppercase mb-2">Curated Movement</p>
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Find your perfect<br>Pilates practice</h2>
            <p class="text-gray-500 text-sm mt-2 max-w-md">Classes designed to challenge, inspire, and transform your movement practice.</p>
        </div>
        <a href="{{ route('classes.index') }}" class="shrink-0 bg-purple-500 hover:bg-purple-600 text-white px-6 py-2.5 text-sm font-semibold tracking-widest uppercase transition">
            SEE ALL CLASSES
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @forelse($kelasList as $kelas)
        <a href="{{ route('classes.schedule', $kelas->id_kelas) }}" class="group relative overflow-hidden aspect-square bg-gray-800 block">
            <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=600&q=80" alt="{{ $kelas->nama_kelas }}"
                class="absolute inset-0 w-full h-full object-cover opacity-70 group-hover:opacity-50 group-hover:scale-105 transition duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent z-10"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4 z-20">
                <p class="text-white font-bold text-sm uppercase tracking-wide">{{ $kelas->nama_kelas }}</p>
                @if($kelas->deskripsi)
                <p class="text-gray-300 text-xs mt-1 line-clamp-2">{{ $kelas->deskripsi }}</p>
                @endif
            </div>
        </a>
        @empty
        @foreach(['Reformer Foundations', 'Athletic Flow', 'Core Intensity', 'Advanced Mastery'] as $i => $name)
        <div class="group relative overflow-hidden aspect-square bg-gray-800">
            <img src="https://images.unsplash.com/photo-{{ ['1518611012118-696072aa579a','1571019613454-1cb2f99b2d8b','1506629082955-511b1aa562c8','1544367567-0f2fcb009e0b'][$i] }}?w=600&q=80" alt="{{ $name }}"
                class="absolute inset-0 w-full h-full object-cover opacity-70">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent z-10"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4 z-20">
                <p class="text-white font-bold text-sm uppercase tracking-wide">{{ $name }}</p>
            </div>
        </div>
        @endforeach
        @endforelse
    </div>
</section>

{{-- INVEST IN YOURSELF - Packages --}}
<section class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <p class="text-xs font-semibold tracking-widest text-purple-500 uppercase mb-2">Invest in Yourself</p>
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Choose the package<br>that fits your journey</h2>
            <p class="text-gray-500 text-sm mt-3">Flexible options for every level of commitment.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredPackages as $i => $pkg)
            <div class="bg-white border {{ $i === 1 ? 'border-purple-400 ring-2 ring-purple-100 relative' : 'border-gray-200' }} p-8 flex flex-col hover:border-purple-300 hover:shadow-md transition">
                @if($i === 1)
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-purple-500 text-white text-xs font-semibold px-4 py-1 tracking-widest uppercase">MOST POPULAR</span>
                @endif
                <div class="flex items-start justify-between mb-4">
                    <p class="font-bold text-gray-900 text-base">{{ $pkg->nama_package }}</p>
                    @if($i === 0)
                    <span class="text-xs bg-purple-100 text-purple-600 px-2 py-0.5 font-semibold tracking-wide uppercase shrink-0 ml-2">ONE TIME</span>
                    @endif
                </div>
                <p class="text-4xl font-black text-gray-900 mb-6">Rp{{ number_format($pkg->harga, 0, ',', '.') }}</p>
                <ul class="space-y-3 text-sm text-gray-600 flex-1 mb-6">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-purple-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ $pkg->jumlah_kredit }} session pilates group class
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-purple-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Valid {{ $pkg->masa_berlaku }} days
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-purple-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        All class types
                    </li>
                </ul>
                @auth
                    @if(in_array($pkg->id_package, $purchasedIds))
                    <span class="block text-center py-3 text-sm font-semibold tracking-widest uppercase bg-gray-200 text-gray-500 cursor-not-allowed">
                        PURCHASED
                    </span>
                    @else
                    <a href="{{ route('packages.checkout', $pkg->id_package) }}" class="block text-center py-3 text-sm font-semibold tracking-widest uppercase transition bg-purple-500 text-white hover:bg-purple-600">
                        PURCHASE PACKAGE
                    </a>
                    @endif
                @else
                <button onclick="openLoginModal()" class="w-full text-center py-3 text-sm font-semibold tracking-widest uppercase transition bg-purple-500 text-white hover:bg-purple-600">
                    PURCHASE PACKAGE
                </button>
                @endauth
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-gray-400">Belum ada package tersedia.</div>
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('packages.index') }}" class="text-sm text-gray-500 hover:text-gray-900 tracking-widest uppercase transition border-b border-gray-300 hover:border-gray-900 pb-0.5">
                BROWSE ALL PACKAGES
            </a>
        </div>
    </div>
</section>

@endsection
