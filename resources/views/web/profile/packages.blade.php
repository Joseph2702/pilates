@extends('layouts.app')
@section('title', 'My Packages - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-gray-900">My Account</h1>
        <p class="text-gray-400 text-sm mt-1">Manage your Precision Pilates journey, view your progress, and schedule your next session.</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        @include('web.profile._sidebar')

        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">My Packages</h2>
                @if($permissions['canViewPackage'])
                <a href="{{ route('packages.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-5 py-2 text-xs font-semibold tracking-wide transition">
                    + BUY PACKAGE
                </a>
                @endif
            </div>

            @if($pembelianList->isEmpty())
            <div class="border border-dashed border-gray-300 py-20 text-center text-gray-400">
                <p class="text-lg">No packages purchased yet.</p>
                <p class="text-sm mt-1">Browse our packages to get started.</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach($pembelianList as $pembelian)
                @php
                    $isActive = $pembelian->status_pembelian === 'paid' && \Carbon\Carbon::parse($pembelian->tanggal_kadaluarsa)->isFuture();
                    $isExpired = $pembelian->tanggal_kadaluarsa && \Carbon\Carbon::parse($pembelian->tanggal_kadaluarsa)->isPast();
                @endphp
                <div class="border {{ $isActive ? 'border-purple-300 bg-purple-50/30' : 'border-gray-200' }} p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-bold text-gray-900 text-sm">{{ $pembelian->package?->nama_package ?? '-' }}</p>
                                <span class="text-xs font-semibold px-2 py-0.5
                                    {{ $isActive ? 'bg-green-100 text-green-700' : ($isExpired ? 'bg-gray-100 text-gray-500' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $isActive ? 'Active' : ($isExpired ? 'Expired' : ucfirst($pembelian->status_pembelian)) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">
                                Purchased {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d M Y') }}
                                · Expires {{ $pembelian->tanggal_kadaluarsa ? \Carbon\Carbon::parse($pembelian->tanggal_kadaluarsa)->format('d M Y') : '-' }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-2xl font-black {{ $isActive ? 'text-purple-600' : 'text-gray-400' }}">{{ $pembelian->sisa_kredit }}</p>
                            <p class="text-xs text-gray-400">/ {{ $pembelian->kredit_earned }} credits</p>
                        </div>
                    </div>
                    @if($isActive)
                    <div class="mt-3">
                        <div class="w-full bg-purple-100 h-1.5">
                            <div class="bg-purple-500 h-1.5 transition-all" style="width: {{ $pembelian->kredit_earned > 0 ? ($pembelian->sisa_kredit / $pembelian->kredit_earned * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-purple-600 mt-1">{{ $pembelian->sisa_kredit }} credits remaining</p>
                    </div>
                    @endif
                    <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
                        <span>Paid: Rp{{ number_format($pembelian->harga_akhir, 0, ',', '.') }}</span>
                        @if($pembelian->diskon > 0)
                        <span class="text-green-600">Saved Rp{{ number_format($pembelian->diskon, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if($pembelianList->hasPages())
            <div class="mt-8">{{ $pembelianList->links() }}</div>
            @endif
            @endif
        </div>
    </div>
</section>
@endsection
