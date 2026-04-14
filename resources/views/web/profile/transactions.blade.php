@extends('layouts.app')
@section('title', 'My Transactions - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-gray-900">My Account</h1>
        <p class="text-gray-400 text-sm mt-1">Manage your Precision Pilates journey, view your progress, and schedule your next session.</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        @include('web.profile._sidebar')

        <div class="lg:col-span-3">
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight mb-6">My Transactions</h2>

            @if($transaksiList->isEmpty())
            <div class="border border-dashed border-gray-300 py-20 text-center text-gray-400">
                <p class="text-lg">No transactions yet.</p>
                <p class="text-sm mt-1">Purchase a package to see your transactions here.</p>
            </div>
            @else
            <div class="border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Order ID</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Package</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Date</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksiList as $trx)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="font-mono text-xs text-gray-600">{{ $trx->order_id }}</p>
                                @if($trx->payment_type)
                                <p class="text-xs text-gray-400 mt-0.5">{{ ucwords(str_replace('_', ' ', $trx->payment_type)) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-700">
                                {{ $trx->pembelianPackage?->package?->nama_package ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-gray-500 hidden md:table-cell">
                                {{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-gray-900">
                                Rp{{ number_format($trx->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                @php
                                    $status = $trx->status_internal ?? $trx->transaction_status ?? 'pending';
                                    $colors = [
                                        'paid' => 'bg-green-100 text-green-700',
                                        'settlement' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'failed' => 'bg-red-100 text-red-600',
                                        'expire' => 'bg-gray-100 text-gray-500',
                                        'cancel' => 'bg-gray-100 text-gray-500',
                                    ];
                                @endphp
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 {{ $colors[$status] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($transaksiList->hasPages())
            <div class="mt-8">{{ $transaksiList->links() }}</div>
            @endif
            @endif
        </div>
    </div>
</section>
@endsection
