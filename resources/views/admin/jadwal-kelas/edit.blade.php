@extends('layouts.admin')
@section('title', 'Edit Jadwal Kelas')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.jadwal-kelas.update', $jadwal->id_jadwal_kelas) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="id_kelas" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                        @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id_kelas }}" {{ old('id_kelas', $jadwal->id_kelas) == $kelas->id_kelas ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('id_kelas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instruktur</label>
                    <select name="id_instruktur" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                        @foreach($instrukturList as $ins)
                        <option value="{{ $ins->id_instruktur }}" {{ old('id_instruktur', $jadwal->id_instruktur) == $ins->id_instruktur ? 'selected' : '' }}>{{ $ins->user?->nama ?? 'Instruktur #'.$ins->id_instruktur }}</option>
                        @endforeach
                    </select>
                    @error('id_instruktur') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kelas</label>
                <input type="datetime-local" name="tanggal_kelas" value="{{ old('tanggal_kelas', $jadwal->tanggal_kelas ? \Carbon\Carbon::parse($jadwal->tanggal_kelas)->format('Y-m-d\TH:i') : '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                @error('tanggal_kelas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                    <input type="datetime-local" name="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('Y-m-d\TH:i') : '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                    @error('jam_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                    <input type="datetime-local" name="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('Y-m-d\TH:i') : '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                    @error('jam_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Maksimal</label>
                <input type="number" name="kuota_maksimal" value="{{ old('kuota_maksimal', $jadwal->kuota_maksimal) }}" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                @error('kuota_maksimal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">Update</button>
                <a href="{{ route('admin.jadwal-kelas.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
