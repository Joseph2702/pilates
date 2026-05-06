@extends('layouts.admin')
@section('title', 'Permissions')

@section('actions')
@if($permissions['canCreate'])
<a href="{{ route('admin.permissions.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
    <x-icon name="plus" class="w-4 h-4"/> Tambah Permission
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
                    <th class="px-6 py-3 text-left">Nama Permission</th>
                    <th class="px-6 py-3 text-left">Deskripsi</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($data as $perm)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $perm->id_permission }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $perm->nama_permission }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $perm->deskripsi ?? '-' }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.permissions.edit', $perm->id_permission) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="edit" class="w-4 h-4"/></a>
                            <form method="POST" action="{{ route('admin.permissions.destroy', $perm->id_permission) }}" onsubmit="return confirm('Hapus permission ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada permission.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
