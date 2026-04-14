@extends('layouts.admin')
@section('title', 'Promo')

@section('actions')
@if($permissions['canCreate'])
<a href="{{ route('admin.promo.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
    <x-icon name="plus" class="w-4 h-4"/> Tambah Promo
</a>
@endif
@endsection

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Kode</th>
                    <th class="px-6 py-3 text-left">Nama Promo</th>
                    <th class="px-6 py-3 text-left">Diskon</th>
                    <th class="px-6 py-3 text-left">Periode</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($promos as $promo)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $promo->id_promo }}</td>
                    <td class="px-6 py-3 font-mono text-xs">{{ $promo->kode_promo }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $promo->nama_promo }}</td>
                    <td class="px-6 py-3">{{ $promo->persenan_diskon }}%</td>
                    <td class="px-6 py-3 text-xs text-gray-500">
                        {{ $promo->tanggal_mulai ? \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') : '-' }}
                        &mdash;
                        {{ $promo->tanggal_selesai ? \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-3"><x-badge :color="$promo->status_promo === 'active' ? 'green' : 'red'">{{ $promo->status_promo }}</x-badge></td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($permissions['canUpdate'])
                            <a href="{{ route('admin.promo.edit', $promo->id_promo) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="edit" class="w-4 h-4"/></a>
                            @endif
                            @if($permissions['canDelete'])
                            <form method="POST" action="{{ route('admin.promo.destroy', $promo->id_promo) }}" onsubmit="return confirm('Hapus promo ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada promo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $promos->links() }}</div>
</div>
@endsection
