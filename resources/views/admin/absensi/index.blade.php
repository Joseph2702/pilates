@extends('layouts.admin')
@section('title', 'Absensi')

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
                    <th class="px-6 py-3 text-left">Peserta</th>
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
                    <td class="px-6 py-3">{{ $j->bookings_count }} orang</td>
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.absensi.show', $j->id_jadwal_kelas) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Input Absensi</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada jadwal kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $jadwalList->links() }}</div>
</div>
@endsection
