@extends('layouts.admin')
@section('title', 'Packages')

@section('actions')
@if($permissions['canCreate'])
<a href="{{ route('admin.packages.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
    <x-icon name="plus" class="w-4 h-4"/> Tambah Package
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
                    <th class="px-6 py-3 text-left">Nama Package</th>
                    <th class="px-6 py-3 text-left">Kredit</th>
                    <th class="px-6 py-3 text-left">Harga</th>
                    <th class="px-6 py-3 text-left">Masa Berlaku</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($packages as $pkg)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $pkg->id_package }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $pkg->nama_package }}</td>
                    <td class="px-6 py-3">{{ $pkg->jumlah_kredit }}</td>
                    <td class="px-6 py-3">Rp {{ number_format($pkg->harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-3">{{ $pkg->masa_berlaku }} hari</td>
                    <td class="px-6 py-3"><x-badge :color="$pkg->status_package === 'active' ? 'green' : 'red'">{{ $pkg->status_package }}</x-badge></td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($permissions['canUpdate'])
                            <a href="{{ route('admin.packages.edit', $pkg->id_package) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="edit" class="w-4 h-4"/></a>
                            @endif
                            @if($permissions['canDelete'])
                            <form method="POST" action="{{ route('admin.packages.destroy', $pkg->id_package) }}" onsubmit="return confirm('Hapus package ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada package.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $packages->links() }}</div>
</div>
@endsection
