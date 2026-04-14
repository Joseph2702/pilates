@extends('layouts.admin')
@section('title', 'Detail Pembelian Package')

@section('actions')
<a href="{{ route('admin.pembelian-package.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Kembali</a>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Info Pembelian</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Pelanggan</dt><dd class="font-medium text-gray-900">{{ $pembelian->pelanggan?->user?->nama ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Email</dt><dd>{{ $pembelian->pelanggan?->user?->email ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Package</dt><dd>{{ $pembelian->package?->nama_package ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Promo</dt><dd>{{ $pembelian->promo?->kode_promo ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Harga Awal</dt><dd>Rp {{ number_format($pembelian->harga_awal, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Diskon</dt><dd>Rp {{ number_format($pembelian->diskon, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Harga Akhir</dt><dd class="font-semibold text-gray-900">Rp {{ number_format($pembelian->harga_akhir, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd><x-badge :color="$pembelian->status_pembelian === 'paid' ? 'green' : ($pembelian->status_pembelian === 'pending' ? 'yellow' : 'red')">{{ $pembelian->status_pembelian }}</x-badge></dd></div>
            <div><dt class="text-gray-500">Kredit Earned</dt><dd>{{ $pembelian->kredit_earned }}</dd></div>
            <div><dt class="text-gray-500">Sisa Kredit</dt><dd>{{ $pembelian->sisa_kredit }}</dd></div>
            <div><dt class="text-gray-500">Tanggal Pembelian</dt><dd>{{ $pembelian->tanggal_pembelian ? \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d M Y H:i') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Tanggal Kadaluarsa</dt><dd>{{ $pembelian->tanggal_kadaluarsa ? \Carbon\Carbon::parse($pembelian->tanggal_kadaluarsa)->format('d M Y') : '-' }}</dd></div>
        </dl>
    </div>

    @if($pembelian->transaksi)
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Transaksi Terkait</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Order ID</dt><dd class="font-mono text-xs">{{ $pembelian->transaksi->order_id }}</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd><x-badge :color="$pembelian->transaksi->status_internal === 'paid' ? 'green' : 'yellow'">{{ $pembelian->transaksi->status_internal }}</x-badge></dd></div>
            <div><dt class="text-gray-500">Jumlah Bayar</dt><dd>Rp {{ number_format($pembelian->transaksi->jumlah_bayar, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Payment Type</dt><dd>{{ $pembelian->transaksi->payment_type ?? '-' }}</dd></div>
        </dl>
    </div>
    @endif
</div>
@endsection
