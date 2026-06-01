@extends('layouts.admin')
@section('title', 'Mutasi Kredit')

@section('actions')
<form method="GET" action="{{ route('admin.kredit.index') }}" class="flex flex-wrap items-center gap-2">
    <select name="jenis" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
        <option value="">Semua Jenis</option>
        <option value="credit" {{ request('jenis') === 'credit' ? 'selected' : '' }}>Credit</option>
        <option value="debit" {{ request('jenis') === 'debit' ? 'selected' : '' }}>Debit</option>
    </select>
    <div class="flex items-center gap-1">
        <label class="text-xs text-gray-500 whitespace-nowrap">Dari</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
    </div>
    <div class="flex items-center gap-1">
        <label class="text-xs text-gray-500 whitespace-nowrap">Sampai</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
    </div>
    <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg hover:bg-gray-700 transition">Cari</button>
    @if(request()->hasAny(['jenis', 'date_from', 'date_to']))
    <a href="{{ route('admin.kredit.index') }}" class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50 transition">Reset</a>
    @endif
</form>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Pelanggan</th>
                    <th class="px-6 py-3 text-left">Jenis</th>
                    <th class="px-6 py-3 text-left">Jumlah</th>
                    <th class="px-6 py-3 text-left">Sumber</th>
                    <th class="px-6 py-3 text-left">Keterangan</th>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($mutasiList as $m)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $m->id_mutasi }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $m->pelanggan?->user?->nama ?? '-' }}</td>
                    <td class="px-6 py-3"><x-badge :color="$m->jenis_mutasi === 'credit' ? 'green' : 'red'">{{ $m->jenis_mutasi }}</x-badge></td>
                    <td class="px-6 py-3">{{ $m->jumlah_kredit }}</td>
                    <td class="px-6 py-3">{{ $m->sumber_mutasi }}</td>
                    <td class="px-6 py-3 text-gray-500 max-w-xs truncate">{{ $m->keterangan ?? '-' }}</td>
                    <td class="px-6 py-3 text-xs text-gray-500">{{ $m->tanggal_mutasi ? \Carbon\Carbon::parse($m->tanggal_mutasi)->format('d M Y H:i') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada mutasi kredit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $mutasiList->links() }}</div>
</div>
@endsection
