@extends('layouts.app')
@section('title', 'Articles - Femm Pilates')

@section('content')

{{-- Header --}}
<section class="bg-white border-b border-gray-100 px-6 py-12">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-start justify-between gap-6 flex-wrap">
            <div>
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 uppercase tracking-tight">ARTICLES</h1>
                <p class="text-gray-400 text-sm mt-2">Exploring the intersection of biomechanics, mindfulness, and the Pilates method.</p>
            </div>
            <div class="flex items-center gap-2 border border-gray-200 px-4 py-2.5 bg-white w-full md:w-72">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" placeholder="Search articles..." class="text-sm outline-none flex-1 text-gray-700 placeholder-gray-400">
            </div>
        </div>
    </div>
</section>

{{-- Articles Grid --}}
<section class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($artikelList as $artikel)
        <article class="group">
            <a href="{{ route('articles.show', $artikel->id_artikel) }}" class="block mb-4 overflow-hidden">
                <div class="aspect-video bg-gray-100 overflow-hidden">
                    @if($artikel->gambar_artikel)
                    <img src="{{ $artikel->gambar_artikel }}" alt="{{ $artikel->judul_artikel }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                    <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800&q=80" alt="{{ $artikel->judul_artikel }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @endif
                </div>
            </a>
            <p class="text-xs text-gray-400 mb-2">
                {{ $artikel->tanggal_publish ? \Carbon\Carbon::parse($artikel->tanggal_publish)->format('M d, Y') : \Carbon\Carbon::parse($artikel->created_at)->format('M d, Y') }}
            </p>
            <h2 class="font-black text-gray-900 text-lg leading-snug group-hover:text-purple-600 transition mb-2">
                <a href="{{ route('articles.show', $artikel->id_artikel) }}">{{ $artikel->judul_artikel }}</a>
            </h2>
            @if($artikel->konten_artikel)
            <p class="text-gray-500 text-sm leading-relaxed line-clamp-3">{{ strip_tags($artikel->konten_artikel) }}</p>
            @endif
        </article>
        @empty
        <div class="col-span-3 text-center py-20 text-gray-400">
            <p class="text-lg">No articles available yet.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($artikelList->hasPages())
    <div class="mt-12 flex items-center justify-center gap-1">
        {{-- Previous --}}
        @if($artikelList->onFirstPage())
        <span class="w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-300 text-sm cursor-not-allowed">‹</span>
        @else
        <a href="{{ $artikelList->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-500 hover:border-purple-400 hover:text-purple-500 transition text-sm">‹</a>
        @endif

        @foreach($artikelList->getUrlRange(1, $artikelList->lastPage()) as $page => $url)
        @if($page == $artikelList->currentPage())
        <span class="w-8 h-8 flex items-center justify-center bg-purple-500 text-white text-sm font-semibold">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-600 hover:border-purple-400 hover:text-purple-500 transition text-sm">{{ $page }}</a>
        @endif
        @endforeach

        {{-- Next --}}
        @if($artikelList->hasMorePages())
        <a href="{{ $artikelList->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-500 hover:border-purple-400 hover:text-purple-500 transition text-sm">›</a>
        @else
        <span class="w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-300 text-sm cursor-not-allowed">›</span>
        @endif
    </div>
    @endif
</section>

@endsection
