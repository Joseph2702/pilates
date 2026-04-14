@extends('layouts.admin')
@section('title', 'Activity Logs')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">User</th>
                    <th class="px-6 py-3 text-left">Modul</th>
                    <th class="px-6 py-3 text-left">Aktivitas</th>
                    <th class="px-6 py-3 text-left">Keterangan</th>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $log->id_log }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $log->user?->nama ?? '-' }}</td>
                    <td class="px-6 py-3"><x-badge color="blue">{{ $log->modul }}</x-badge></td>
                    <td class="px-6 py-3">{{ $log->aktivitas }}</td>
                    <td class="px-6 py-3 text-gray-500 max-w-xs truncate">{{ $log->keterangan ?? '-' }}</td>
                    <td class="px-6 py-3 text-xs text-gray-500">{{ $log->tanggal_log ? \Carbon\Carbon::parse($log->tanggal_log)->format('d M Y H:i') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada activity log.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $logs->links() }}</div>
</div>
@endsection
