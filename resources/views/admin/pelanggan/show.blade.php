@extends('layouts.admin')
@section('title', 'Detail Pelanggan')

@section('actions')
<a href="{{ route('admin.pelanggan.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Kembali</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Info Pelanggan</h3>
        <dl class="space-y-3 text-sm">
            <div><dt class="text-gray-500">Nama</dt><dd class="font-medium text-gray-900">{{ $pelanggan->user?->nama ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Email</dt><dd>{{ $pelanggan->user?->email ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">No HP</dt><dd>{{ $pelanggan->user?->no_hp ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Jenis Kelamin</dt><dd>{{ $pelanggan->user?->jenis_kelamin ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Tanggal Daftar</dt><dd>{{ $pelanggan->tanggal_daftar ? \Carbon\Carbon::parse($pelanggan->tanggal_daftar)->format('d M Y') : '-' }}</dd></div>
        </dl>
    </div>

    {{-- Pembelian Package --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200"><h3 class="font-semibold text-gray-800">Pembelian Package</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">Package</th>
                            <th class="px-6 py-3 text-left">Harga</th>
                            <th class="px-6 py-3 text-left">Kredit</th>
                            <th class="px-6 py-3 text-left">Sisa</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pelanggan->pembelianPackage as $pp)
                        <tr>
                            <td class="px-6 py-3">{{ $pp->package?->nama_package ?? '-' }}</td>
                            <td class="px-6 py-3">Rp {{ number_format($pp->harga_akhir, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">{{ $pp->kredit_earned }}</td>
                            <td class="px-6 py-3">{{ $pp->sisa_kredit }}</td>
                            <td class="px-6 py-3"><x-badge :color="$pp->status_pembelian === 'paid' ? 'green' : ($pp->status_pembelian === 'pending' ? 'yellow' : 'red')">{{ $pp->status_pembelian }}</x-badge></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-400">Belum ada pembelian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Booking History --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200"><h3 class="font-semibold text-gray-800">Riwayat Booking</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">Kelas</th>
                            <th class="px-6 py-3 text-left">Tanggal</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pelanggan->bookings as $b)
                        <tr>
                            <td class="px-6 py-3">{{ $b->jadwalKelas?->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $b->jadwalKelas?->tanggal_kelas ? \Carbon\Carbon::parse($b->jadwalKelas->tanggal_kelas)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-3"><x-badge :color="$b->status_booking === 'confirmed' ? 'green' : ($b->status_booking === 'canceled' ? 'red' : 'yellow')">{{ $b->status_booking }}</x-badge></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400">Belum ada booking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mutasi Kredit --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200"><h3 class="font-semibold text-gray-800">Mutasi Kredit</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">Tanggal</th>
                            <th class="px-6 py-3 text-left">Jenis</th>
                            <th class="px-6 py-3 text-left">Jumlah</th>
                            <th class="px-6 py-3 text-left">Sumber</th>
                            <th class="px-6 py-3 text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pelanggan->mutasiKredit as $mk)
                        <tr>
                            <td class="px-6 py-3">{{ \Carbon\Carbon::parse($mk->tanggal_mutasi)->format('d M Y H:i') }}</td>
                            <td class="px-6 py-3"><x-badge :color="$mk->jenis_mutasi === 'credit' ? 'green' : 'red'">{{ $mk->jenis_mutasi }}</x-badge></td>
                            <td class="px-6 py-3">{{ $mk->jumlah_kredit }}</td>
                            <td class="px-6 py-3">{{ $mk->sumber_mutasi }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $mk->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-400">Belum ada mutasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
