@extends('layouts.admin')
@section('title', 'Transaksi')

@section('actions')
<form method="GET" action="{{ route('admin.transaksi.index') }}" class="flex items-center gap-2">
    <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
        <option value="">Semua Status</option>
        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
    </select>
</form>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Order ID</th>
                    <th class="px-6 py-3 text-left">Pelanggan</th>
                    <th class="px-6 py-3 text-left">Jumlah Bayar</th>
                    <th class="px-6 py-3 text-left">Payment Type</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transaksiList as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-mono text-xs">{{ $t->order_id }}</td>
                    <td class="px-6 py-3">{{ $t->pembelianPackage?->pelanggan?->user?->nama ?? '-' }}</td>
                    <td class="px-6 py-3 font-medium">Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
                    <td class="px-6 py-3">{{ $t->payment_type ?? '-' }}</td>
                    <td class="px-6 py-3"><x-badge :color="$t->status_internal === 'paid' ? 'green' : ($t->status_internal === 'failed' ? 'red' : ($t->status_internal === 'expired' ? 'gray' : 'yellow'))">{{ $t->status_internal }}</x-badge></td>
                    <td class="px-6 py-3 text-xs text-gray-500">{{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d M Y H:i') : '-' }}</td>
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.transaksi.show', $t->id_transaksi) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="eye" class="w-4 h-4"/></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $transaksiList->links() }}</div>
</div>
@endsection
