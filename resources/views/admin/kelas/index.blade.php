@extends('layouts.admin')
@section('title', 'Kelas')

@section('actions')
@if($permissions['canCreate'])
<a href="{{ route('admin.kelas.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
    <x-icon name="plus" class="w-4 h-4"/> Tambah Kelas
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
                    <th class="px-6 py-3 text-left">Nama Kelas</th>
                    <th class="px-6 py-3 text-left">Deskripsi</th>
                    <th class="px-6 py-3 text-left">Kapasitas</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($kelasList as $kelas)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $kelas->id_kelas }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $kelas->nama_kelas }}</td>
                    <td class="px-6 py-3 text-gray-500 max-w-xs truncate">{{ $kelas->deskripsi ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $kelas->kapasitas }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($permissions['canUpdate'])
                            <a href="{{ route('admin.kelas.edit', $kelas->id_kelas) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="edit" class="w-4 h-4"/></a>
                            @endif
                            @if($permissions['canDelete'])
                            <form method="POST" action="{{ route('admin.kelas.destroy', $kelas->id_kelas) }}" onsubmit="return confirm('Hapus kelas ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $kelasList->links() }}</div>
</div>
@endsection
