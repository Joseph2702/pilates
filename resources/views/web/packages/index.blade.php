@extends('layouts.app')
@section('title', 'Packages - Femm Pilates')

@section('content')

{{-- Hero --}}
<section class="relative h-72 md:h-96 flex items-end bg-gray-900 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/20 via-gray-900/50 to-gray-900/80 z-10"></div>
    <img src="https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=1600&q=80" alt="Packages" class="absolute inset-0 w-full h-full object-cover opacity-80">
    <div class="relative z-20 w-full max-w-7xl mx-auto px-6 pb-12 text-center">
        <h1 class="text-4xl md:text-6xl font-black text-white leading-tight">
            Invest in Your<br><span class="text-purple-400">Practice</span>
        </h1>
        <p class="text-gray-300 text-sm mt-4 max-w-lg mx-auto leading-relaxed">
            Designed for those who value discipline and results. Choose a path that aligns with your movement goals, from restorative sessions to high-intensity athletic conditioning.
        </p>
    </div>
</section>

{{-- Packages Grid --}}
<section class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($packages as $i => $pkg)
        <div class="bg-white border border-gray-200 p-8 flex flex-col hover:border-purple-300 hover:shadow-sm transition">
            <div class="flex items-start justify-between mb-3">
                <p class="font-bold text-gray-900 text-lg">{{ $pkg->nama_package }}</p>
            </div>
            <p class="text-4xl font-black text-gray-900 mb-6">Rp{{ number_format($pkg->harga, 0, ',', '.') }}</p>
            <ul class="space-y-3 text-sm text-gray-600 flex-1 mb-8">
                <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    {{ $pkg->jumlah_kredit }} session pilates group class
                </li>
                <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Valid for {{ $pkg->masa_berlaku }} days
                </li>
                <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    All class types
                </li>
            </ul>
            @auth
                <a href="{{ route('packages.checkout', $pkg->id_package) }}"
                    class="block text-center bg-purple-500 hover:bg-purple-600 text-white py-3 text-sm font-semibold tracking-widest uppercase transition">
                    Purchase
                </a>
            @else
            <button onclick="openLoginModal()"
                class="w-full bg-purple-500 hover:bg-purple-600 text-white py-3 text-sm font-semibold tracking-widest uppercase transition">
                Purchase
            </button>
            @endauth
        </div>
        @empty
        <div class="col-span-2 text-center py-20 text-gray-400">Belum ada package tersedia.</div>
        @endforelse
    </div>
</section>

{{-- Package FAQs --}}
<section class="max-w-3xl mx-auto px-6 pb-20">
    <div class="text-center mb-10">
        <h2 class="text-2xl font-black text-gray-900 uppercase tracking-widest">PACKAGE FAQS</h2>
        <div class="w-10 h-0.5 bg-purple-400 mx-auto mt-3"></div>
    </div>
    <div class="divide-y divide-gray-200">
        @foreach([
            ['Do class packs expire?', 'Yes, to encourage regular practice and maintain studio capacity, 5-class packs expire after 3 months and 10-class packs expire after 6 months from the date of purchase.'],
            ['What is the cancellation policy?', 'We require a 24-hour notice for group class cancellations. Late cancellations or no-shows will result in the loss of that class credit for packs or a small fee for unlimited members.'],
            ['Can I share my class pack with a friend?', 'Class packs are assigned to an individual profile and are non-transferable. However, Unlimited members receive 2 guest passes each month.'],
            ['What is the Studio Etiquette?', 'We ask all students to arrive 5-10 minutes early. Grip socks are mandatory for safety and hygiene. Please leave mobile phones in the provided lockers.'],
        ] as $faq)
        <details class="group py-5">
            <summary class="flex items-start gap-3 cursor-pointer list-none">
                <span class="text-purple-500 font-bold text-sm shrink-0 mt-0.5">Q.</span>
                <span class="text-sm font-semibold text-gray-800 flex-1 hover:text-purple-600 transition">{{ $faq[0] }}</span>
                <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
            </summary>
            <p class="mt-3 text-sm text-gray-500 leading-relaxed pl-7">{{ $faq[1] }}</p>
        </details>
        @endforeach
    </div>
</section>

@endsection
