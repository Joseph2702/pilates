@extends('layouts.app')
@section('title', 'Akses Ditolak')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <!-- Error Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Icon -->
            <div class="mb-6 inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Akses Ditolak</h1>

            <!-- Message -->
            <p class="text-gray-600 mb-6 leading-relaxed">
                Anda tidak memiliki izin untuk mengakses halaman ini. Hanya pengguna yang diizinkan yang dapat mengakses fitur ini.
            </p>

            <!-- Buttons -->
            <div class="flex gap-3">
                <button onclick="history.back()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                    Kembali
                </button>
                <a href="{{ route('home') }}" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition">
                    Beranda
                </a>
            </div>
        </div>

        <!-- Help Text -->
        <p class="text-center text-gray-500 text-sm mt-6">
            Jika Anda merasa ini adalah kesalahan, hubungi administrator.
        </p>
    </div>
</div>
@endsection
