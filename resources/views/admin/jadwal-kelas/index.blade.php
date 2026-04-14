@extends('layouts.admin')
@section('title', 'Jadwal Kelas')

@section('actions')
@if($permissions['canCreate'])
<a href="{{ route('admin.jadwal-kelas.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
    <x-icon name="plus" class="w-4 h-4"/> Tambah Jadwal
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
                    <th class="px-6 py-3 text-left">Kelas</th>
                    <th class="px-6 py-3 text-left">Instruktur</th>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-left">Jam</th>
                    <th class="px-6 py-3 text-left">Kuota</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($jadwalList as $j)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $j->id_jadwal_kelas }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $j->kelas?->nama_kelas ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $j->instruktur?->user?->nama ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $j->tanggal_kelas ? \Carbon\Carbon::parse($j->tanggal_kelas)->format('d M Y') : '-' }}</td>
                    <td class="px-6 py-3 text-xs">
                        {{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '-' }}
                        &mdash;
                        {{ $j->jam_selesai ? \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') : '-' }}
                    </td>
                    <td class="px-6 py-3">
                        <span class="{{ $j->kuota_terisi >= $j->kuota_maksimal ? 'text-red-600 font-semibold' : '' }}">{{ $j->kuota_terisi }}/{{ $j->kuota_maksimal }}</span>
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($permissions['canUpdate'])
                            <a href="{{ route('admin.jadwal-kelas.edit', $j->id_jadwal_kelas) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="edit" class="w-4 h-4"/></a>
                            @endif
                            @if($permissions['canDelete'])
                            <form method="POST" action="{{ route('admin.jadwal-kelas.destroy', $j->id_jadwal_kelas) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><x-icon name="trash" class="w-4 h-4"/></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada jadwal kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $jadwalList->links() }}</div>
</div>
@endsection
