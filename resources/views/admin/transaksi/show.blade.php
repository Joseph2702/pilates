@extends('layouts.admin')
@section('title', 'Detail Transaksi')

@section('actions')
<a href="{{ route('admin.transaksi.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Kembali</a>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Info Transaksi</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Order ID</dt><dd class="font-mono text-xs font-medium text-gray-900">{{ $transaksi->order_id }}</dd></div>
            <div><dt class="text-gray-500">Status Internal</dt><dd><x-badge :color="$transaksi->status_internal === 'paid' ? 'green' : ($transaksi->status_internal === 'failed' ? 'red' : 'yellow')">{{ $transaksi->status_internal }}</x-badge></dd></div>
            <div><dt class="text-gray-500">Jumlah Bayar</dt><dd class="font-medium">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Payment Type</dt><dd>{{ $transaksi->payment_type ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Transaction Status (Midtrans)</dt><dd>{{ $transaksi->transaction_status ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Fraud Status</dt><dd>{{ $transaksi->fraud_status ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Expired At</dt><dd>{{ $transaksi->expired_at ? \Carbon\Carbon::parse($transaksi->expired_at)->format('d M Y H:i') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Created At</dt><dd>{{ $transaksi->created_at ? \Carbon\Carbon::parse($transaksi->created_at)->format('d M Y H:i') : '-' }}</dd></div>
        </dl>
    </div>

    @if($transaksi->pembelianPackage)
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Pembelian Package</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Pelanggan</dt><dd>{{ $transaksi->pembelianPackage->pelanggan?->user?->nama ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Package</dt><dd>{{ $transaksi->pembelianPackage->package?->nama_package ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Harga Awal</dt><dd>Rp {{ number_format($transaksi->pembelianPackage->harga_awal, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Diskon</dt><dd>Rp {{ number_format($transaksi->pembelianPackage->diskon, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Harga Akhir</dt><dd class="font-medium text-gray-900">Rp {{ number_format($transaksi->pembelianPackage->harga_akhir, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Status Pembelian</dt><dd><x-badge :color="$transaksi->pembelianPackage->status_pembelian === 'paid' ? 'green' : 'yellow'">{{ $transaksi->pembelianPackage->status_pembelian }}</x-badge></dd></div>
        </dl>
    </div>
    @endif
</div>
@endsection
