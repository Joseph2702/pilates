@extends('layouts.admin')
@section('title', 'Pelanggan')

@section('actions')
<form method="GET" action="{{ route('admin.pelanggan.index') }}" class="flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
    <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
        <x-icon name="search" class="w-4 h-4"/>
    </button>
</form>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">No HP</th>
                    <th class="px-6 py-3 text-left">Tgl Daftar</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pelangganList as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $p->id_pelanggan }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $p->user?->nama ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $p->user?->email ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $p->user?->no_hp ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $p->tanggal_daftar ? \Carbon\Carbon::parse($p->tanggal_daftar)->format('d M Y') : '-' }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.pelanggan.show', $p->id_pelanggan) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="eye" class="w-4 h-4"/></a>
                            <form method="POST" action="{{ route('admin.pelanggan.destroy', $p->id_pelanggan) }}" onsubmit="return confirm('Hapus pelanggan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada pelanggan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $pelangganList->links() }}</div>
</div>
@endsection
