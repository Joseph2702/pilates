@extends('layouts.admin')
@section('title', 'Edit Promo')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.promo.update', $promo->id_promo) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Promo</label>
                    <input type="text" name="kode_promo" value="{{ old('kode_promo', $promo->kode_promo) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                    @error('kode_promo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Promo</label>
                    <input type="text" name="nama_promo" value="{{ old('nama_promo', $promo->nama_promo) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                    @error('nama_promo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Persentase Diskon (%)</label>
                <input type="number" name="persenan_diskon" value="{{ old('persenan_diskon', $promo->persenan_diskon) }}" min="0" max="100" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                @error('persenan_diskon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="datetime-local" name="tanggal_mulai" value="{{ old('tanggal_mulai', $promo->tanggal_mulai ? \Carbon\Carbon::parse($promo->tanggal_mulai)->format('Y-m-d\TH:i') : '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                    @error('tanggal_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="datetime-local" name="tanggal_selesai" value="{{ old('tanggal_selesai', $promo->tanggal_selesai ? \Carbon\Carbon::parse($promo->tanggal_selesai)->format('Y-m-d\TH:i') : '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                    @error('tanggal_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status_promo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                    <option value="active" {{ old('status_promo', $promo->status_promo) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status_promo', $promo->status_promo) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status_promo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">Update</button>
                <a href="{{ route('admin.promo.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
