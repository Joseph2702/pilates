@extends('layouts.admin')
@section('title', 'Tambah Artikel')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.artikel.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Artikel</label>
                <input type="text" name="judul_artikel" value="{{ old('judul_artikel') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                @error('judul_artikel') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Image: URL or Upload toggle --}}
            <div x-data="{ mode: '{{ old('gambar_artikel') ? 'url' : 'url' }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Artikel</label>
                <div class="flex gap-2 mb-3">
                    <button type="button" @click="mode='url'"
                        :class="mode==='url' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-1.5 text-xs font-medium rounded-lg transition">URL</button>
                    <button type="button" @click="mode='file'"
                        :class="mode==='file' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-1.5 text-xs font-medium rounded-lg transition">Upload File</button>
                </div>
                <div x-show="mode==='url'">
                    <input type="text" name="gambar_artikel" value="{{ old('gambar_artikel') }}" placeholder="https://..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                    <p class="text-xs text-gray-400 mt-1">Masukkan URL gambar dari internet.</p>
                </div>
                <div x-show="mode==='file'">
                    <input type="file" name="gambar_file" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none text-sm">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, GIF. Maks 2MB.</p>
                </div>
                @error('gambar_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @error('gambar_artikel') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
                <textarea name="konten_artikel" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none resize-y"
                    style="min-height: 300px;"
                    oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'">{{ old('konten_artikel') }}</textarea>
                @error('konten_artikel') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Publish</label>
                <input type="datetime-local" name="tanggal_publish" value="{{ old('tanggal_publish') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 outline-none">
                <p class="text-xs text-gray-400 mt-1">Kosongkan untuk menyimpan sebagai draft.</p>
                @error('tanggal_publish') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">Simpan</button>
                <a href="{{ route('admin.artikel.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
