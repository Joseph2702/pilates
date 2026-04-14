@extends('layouts.instruktur')
@section('title', 'Dashboard')

@section('content')
{{-- Welcome --}}
<div class="mb-8">
    <h3 class="text-xl font-bold text-gray-900">Selamat datang, {{ auth()->user()->nama }}!</h3>
    @if($instruktur->spesialisasi)
    <p class="text-sm text-gray-500 mt-1">Spesialisasi: {{ $instruktur->spesialisasi }}</p>
    @endif
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Total Kelas</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalKelas) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Kelas Hari Ini</p>
        <p class="text-3xl font-bold text-purple-600 mt-1">{{ $kelasHariIni }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500">Total Peserta</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalPeserta) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Jadwal Hari Ini --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Jadwal Hari Ini</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Kelas</th>
                        <th class="px-6 py-3 text-left">Jam</th>
                        <th class="px-6 py-3 text-left">Peserta</th>
                        <th class="px-6 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($todaySchedules as $j)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $j->kelas?->nama_kelas ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '-' }} &mdash; {{ $j->jam_selesai ? \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') : '-' }}</td>
                        <td class="px-6 py-3">{{ $j->kuota_terisi }}/{{ $j->kuota_maksimal }}</td>
                        <td class="px-6 py-3">
                            <a href="{{ route('instruktur.absensi.show', $j->id_jadwal_kelas) }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Absensi</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">Tidak ada kelas hari ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Jadwal Mendatang --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Jadwal Mendatang</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Kelas</th>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Jam</th>
                        <th class="px-6 py-3 text-left">Peserta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($upcomingSchedules as $j)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $j->kelas?->nama_kelas ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $j->tanggal_kelas ? \Carbon\Carbon::parse($j->tanggal_kelas)->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-3">{{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '-' }}</td>
                        <td class="px-6 py-3">{{ $j->kuota_terisi }}/{{ $j->kuota_maksimal }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">Belum ada jadwal mendatang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
