@extends('layouts.admin')
@section('title', 'Bookings')

@section('actions')
<form method="GET" action="{{ route('admin.bookings.index') }}" class="flex items-center gap-2">
    <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
        <option value="">Semua Status</option>
        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
        <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
    </select>
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
                    <th class="px-6 py-3 text-left">Kelas</th>
                    <th class="px-6 py-3 text-left">Tanggal Booking</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $b)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $b->id_booking }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $b->pelanggan?->user?->nama ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $b->jadwalKelas?->kelas?->nama_kelas ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $b->tanggal_booking ? \Carbon\Carbon::parse($b->tanggal_booking)->format('d M Y H:i') : '-' }}</td>
                    <td class="px-6 py-3"><x-badge :color="$b->status_booking === 'confirmed' ? 'green' : ($b->status_booking === 'canceled' ? 'red' : 'yellow')">{{ $b->status_booking }}</x-badge></td>
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.bookings.show', $b->id_booking) }}" class="text-blue-600 hover:text-blue-800"><x-icon name="eye" class="w-4 h-4"/></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $bookings->links() }}</div>
</div>
@endsection
