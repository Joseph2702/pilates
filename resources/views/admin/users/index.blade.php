@extends('layouts.admin')
@section('title', 'Users')

@section('actions')
<div class="flex items-center gap-3">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
        <button type="submit" class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
            <x-icon name="search" class="w-4 h-4"/>
        </button>
    </form>
    @if($permissions['canCreate'])
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
        <x-icon name="plus" class="w-4 h-4"/> Tambah User
    </a>
    @endif
</div>
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
                    <th class="px-6 py-3 text-left">Roles</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $u->id_user }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $u->nama }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $u->email }}</td>
                    <td class="px-6 py-3">{{ $u->no_hp ?? '-' }}</td>
                    <td class="px-6 py-3">
                        <div class="flex flex-wrap gap-1">
                            @forelse($u->roles as $role)
                            <x-badge color="blue">{{ $role->nama_role }}</x-badge>
                            @empty
                            <span class="text-gray-400 text-xs">-</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-3"><x-badge :color="$u->status === 'active' ? 'green' : 'red'">{{ $u->status ?? '-' }}</x-badge></td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($permissions['canUpdate'])
                            <a href="{{ route('admin.users.edit', $u->id_user) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="edit" class="w-4 h-4"/></a>
                            @endif
                            @if($permissions['canDelete'])
                            <form method="POST" action="{{ route('admin.users.destroy', $u->id_user) }}" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $users->links() }}</div>
</div>
@endsection
