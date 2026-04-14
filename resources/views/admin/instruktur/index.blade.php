@extends('layouts.admin')
@section('title', 'Instruktur')

@section('actions')
@if($permissions['canCreate'])
<a href="{{ route('admin.instruktur.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
    <x-icon name="plus" class="w-4 h-4"/> Tambah Instruktur
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
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Spesialisasi</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($instrukturList as $ins)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $ins->id_instruktur }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $ins->user?->nama ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $ins->user?->email ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $ins->spesialisasi ?? '-' }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($permissions['canUpdate'])
                            <a href="{{ route('admin.instruktur.edit', $ins->id_instruktur) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="edit" class="w-4 h-4"/></a>
                            @endif
                            @if($permissions['canDelete'])
                            <form method="POST" action="{{ route('admin.instruktur.destroy', $ins->id_instruktur) }}" onsubmit="return confirm('Hapus instruktur ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada instruktur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $instrukturList->links() }}</div>
</div>
@endsection
