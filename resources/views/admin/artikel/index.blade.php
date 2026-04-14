@extends('layouts.admin')
@section('title', 'Artikel')

@section('actions')
@if($permissions['canCreate'])
<a href="{{ route('admin.artikel.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
    <x-icon name="plus" class="w-4 h-4"/> Tambah Artikel
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
                    <th class="px-6 py-3 text-left">Judul</th>
                    <th class="px-6 py-3 text-left">Penulis</th>
                    <th class="px-6 py-3 text-left">Tanggal Publish</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($artikels as $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $a->id_artikel }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900 max-w-xs truncate">{{ $a->judul_artikel }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $a->user?->nama ?? '-' }}</td>
                    <td class="px-6 py-3 text-xs text-gray-500">{{ $a->tanggal_publish ? \Carbon\Carbon::parse($a->tanggal_publish)->format('d M Y H:i') : 'Draft' }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($permissions['canUpdate'])
                            <a href="{{ route('admin.artikel.edit', $a->id_artikel) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="edit" class="w-4 h-4"/></a>
                            @endif
                            @if($permissions['canDelete'])
                            <form method="POST" action="{{ route('admin.artikel.destroy', $a->id_artikel) }}" onsubmit="return confirm('Hapus artikel ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada artikel.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $artikels->links() }}</div>
</div>
@endsection
