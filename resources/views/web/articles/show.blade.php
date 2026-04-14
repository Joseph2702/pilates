@extends('layouts.app')
@section('title', ($artikel->judul_artikel ?? 'Article') . ' - Femm Pilates')

@section('content')

{{-- Article Header --}}
<section class="max-w-7xl mx-auto px-6 pt-10 pb-6">
    <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-xs text-gray-400 hover:text-purple-600 uppercase tracking-widest mb-6 transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to Articles
    </a>
    <h1 class="text-3xl md:text-4xl font-black text-gray-900 leading-tight max-w-3xl">{{ $artikel->judul_artikel }}</h1>
    <div class="flex items-center gap-4 mt-4 text-sm text-gray-400">
        <span>{{ $artikel->tanggal_publish ? \Carbon\Carbon::parse($artikel->tanggal_publish)->format('M d, Y') : \Carbon\Carbon::parse($artikel->created_at)->format('M d, Y') }}</span>
        <span>·</span>
        <span class="flex items-center gap-2">
            <div class="w-6 h-6 bg-purple-500 flex items-center justify-center text-white text-xs font-bold">
                {{ strtoupper(substr($artikel->user?->nama ?? 'A', 0, 2)) }}
            </div>
            {{ $artikel->user?->nama ?? 'Femm Pilates' }}
        </span>
    </div>
</section>

{{-- Hero Image --}}
<section class="max-w-7xl mx-auto px-6 pb-10">
    <div class="w-full h-72 md:h-96 bg-gray-100 overflow-hidden">
        @if($artikel->gambar_artikel)
        <img src="{{ $artikel->gambar_artikel }}" alt="{{ $artikel->judul_artikel }}" class="w-full h-full object-cover">
        @else
        <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=1600&q=80" alt="{{ $artikel->judul_artikel }}" class="w-full h-full object-cover">
        @endif
    </div>
</section>

{{-- Content + Sidebar --}}
<section class="max-w-7xl mx-auto px-6 pb-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        {{-- Article Body --}}
        <div class="lg:col-span-2">
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed
                prose-headings:font-black prose-headings:text-gray-900 prose-headings:mt-8 prose-headings:mb-3
                prose-p:text-gray-600 prose-p:leading-relaxed
                prose-a:text-purple-600 prose-a:no-underline hover:prose-a:underline
                prose-strong:text-gray-900">
                {!! $artikel->konten_artikel !!}
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-6">
                {{-- CTA Box --}}
                <div class="bg-gray-900 text-white p-6">
                    <p class="text-xs font-semibold tracking-widest uppercase text-purple-400 mb-2">Ready to use?</p>
                    <h3 class="text-lg font-black leading-snug mb-1">Stay Training</h3>
                    <p class="text-gray-400 text-xs mb-5 leading-relaxed">Join a class and experience the Femm Pilates difference for yourself.</p>
                    <a href="{{ route('classes.index') }}" class="block text-center bg-purple-500 hover:bg-purple-600 text-white py-2.5 text-xs font-semibold tracking-widest uppercase transition mb-2">
                        BOOK A CLASS
                    </a>
                    <a href="{{ route('packages.index') }}" class="block text-center border border-white/20 text-gray-300 hover:bg-white/10 py-2.5 text-xs font-semibold tracking-widest uppercase transition">
                        VIEW PACKAGES
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Continue Reading --}}
@if($relatedArticles->count())
<section class="bg-gray-50 py-14">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest mb-8">CONTINUE READING</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedArticles as $related)
            <a href="{{ route('articles.show', $related->id_artikel) }}" class="group">
                <div class="aspect-video bg-gray-100 overflow-hidden mb-4">
                    @if($related->gambar_artikel)
                    <img src="{{ $related->gambar_artikel }}" alt="{{ $related->judul_artikel }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                    <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=600&q=80" alt="{{ $related->judul_artikel }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @endif
                </div>
                <p class="text-xs text-gray-400 mb-1">{{ $related->tanggal_publish ? \Carbon\Carbon::parse($related->tanggal_publish)->format('M d, Y') : \Carbon\Carbon::parse($related->created_at)->format('M d, Y') }}</p>
                <p class="text-sm font-black text-gray-900 group-hover:text-purple-600 leading-snug transition">{{ $related->judul_artikel }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
