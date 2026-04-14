<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Instruktur Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-gray-300 h-screen flex flex-col fixed top-0 left-0 z-20">
        <div class="px-6 py-4 border-b border-gray-800 shrink-0">
            <h1 class="text-lg font-bold text-white tracking-wide">Femm Pilates</h1>
            <p class="text-xs text-purple-400 mt-0.5">Instruktur Panel</p>
        </div>

        <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto text-sm">
            <x-admin-nav-link href="{{ route('instruktur.dashboard') }}" :active="request()->routeIs('instruktur.dashboard')">
                <x-icon name="home"/>Dashboard
            </x-admin-nav-link>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas Saya</p>
            <x-admin-nav-link href="{{ route('instruktur.jadwal.index') }}" :active="request()->routeIs('instruktur.jadwal.*')">
                <x-icon name="calendar"/>Jadwal Kelas
            </x-admin-nav-link>
            <x-admin-nav-link href="{{ route('instruktur.absensi.index') }}" :active="request()->routeIs('instruktur.absensi.*')">
                <x-icon name="check-circle"/>Absensi
            </x-admin-nav-link>
        </nav>

        <div class="px-4 py-3 border-t border-gray-800 text-xs shrink-0">
            <p class="text-gray-400 truncate font-medium">{{ auth()->user()->nama ?? 'Instruktur' }}</p>
            <p class="text-gray-600 text-[10px] truncate">{{ auth()->user()->instruktur?->spesialisasi ?? '' }}</p>
            <form method="POST" action="{{ route('instruktur.logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="text-red-400 hover:text-red-300 transition">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="ml-64 flex-1">
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
            <div class="flex items-center gap-4">
                @yield('actions')
            </div>
        </header>

        @if(session('success'))
        <div class="mx-8 mt-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-8 mt-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            {{ session('error') }}
        </div>
        @endif

        <div class="p-8">
            @yield('content')
        </div>
    </main>

</body>
</html>
