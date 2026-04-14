@extends('layouts.admin')
@section('title', 'Roles')

@section('actions')
@if($permissions['canCreate'])
<a href="{{ route('admin.roles.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
    <x-icon name="plus" class="w-4 h-4"/> Tambah Role
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
                    <th class="px-6 py-3 text-left">Nama Role</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Permissions</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($roles as $role)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $role->id_role }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $role->nama_role }}</td>
                    <td class="px-6 py-3"><x-badge :color="$role->is_active ? 'green' : 'red'">{{ $role->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                    <td class="px-6 py-3">
                        <div class="flex flex-wrap gap-1">
                            @forelse($role->permissions as $perm)
                            <x-badge color="purple">{{ $perm->nama_permission }}</x-badge>
                            @empty
                            <span class="text-gray-400 text-xs">-</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($permissions['canUpdate'])
                            <a href="{{ route('admin.roles.edit', $role->id_role) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="edit" class="w-4 h-4"/></a>
                            @endif
                            @if($permissions['canDelete'])
                            <form method="POST" action="{{ route('admin.roles.destroy', $role->id_role) }}" onsubmit="return confirm('Hapus role ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada role.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
