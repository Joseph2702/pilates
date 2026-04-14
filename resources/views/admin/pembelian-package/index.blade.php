@extends('layouts.admin')
@section('title', 'Pembelian Package')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Pelanggan</th>
                    <th class="px-6 py-3 text-left">Package</th>
                    <th class="px-6 py-3 text-left">Harga Akhir</th>
                    <th class="px-6 py-3 text-left">Kredit</th>
                    <th class="px-6 py-3 text-left">Sisa</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pembelianList as $pp)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $pp->id_pembelian_package }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $pp->pelanggan?->user?->nama ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $pp->package?->nama_package ?? '-' }}</td>
                    <td class="px-6 py-3">Rp {{ number_format($pp->harga_akhir, 0, ',', '.') }}</td>
                    <td class="px-6 py-3">{{ $pp->kredit_earned }}</td>
                    <td class="px-6 py-3">{{ $pp->sisa_kredit }}</td>
                    <td class="px-6 py-3"><x-badge :color="$pp->status_pembelian === 'paid' ? 'green' : ($pp->status_pembelian === 'pending' ? 'yellow' : 'red')">{{ $pp->status_pembelian }}</x-badge></td>
                    <td class="px-6 py-3 text-xs text-gray-500">{{ $pp->tanggal_pembelian ? \Carbon\Carbon::parse($pp->tanggal_pembelian)->format('d M Y') : '-' }}</td>
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.pembelian-package.show', $pp->id_pembelian_package) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="eye" class="w-4 h-4"/></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-6 py-8 text-center text-gray-400">Belum ada pembelian package.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $pembelianList->links() }}</div>
</div>
@endsection
