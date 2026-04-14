<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Femm Pilates</title>
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
            <p class="text-xs text-gray-500 mt-0.5">Admin Panel</p>
        </div>

        @php $u = auth()->user(); @endphp
        <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto text-sm scrollbar-thin scrollbar-track-gray-900 scrollbar-thumb-gray-700">
            @if($u->hasPermission('dashboard.view'))
            <x-admin-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                <x-icon name="home"/>Dashboard
            </x-admin-nav-link>
            @endif

            @if($u->hasPermission('packages.view') || $u->hasPermission('kelas.view') || $u->hasPermission('instruktur.view') || $u->hasPermission('pelanggan.view') || $u->hasPermission('promo.view'))
            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Master Data</p>
            @endif
            @if($u->hasPermission('packages.view'))
            <x-admin-nav-link href="{{ route('admin.packages.index') }}" :active="request()->routeIs('admin.packages.*')">
                <x-icon name="package"/>Packages
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('kelas.view'))
            <x-admin-nav-link href="{{ route('admin.kelas.index') }}" :active="request()->routeIs('admin.kelas.*')">
                <x-icon name="grid"/>Kelas
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('instruktur.view'))
            <x-admin-nav-link href="{{ route('admin.instruktur.index') }}" :active="request()->routeIs('admin.instruktur.*')">
                <x-icon name="users"/>Instruktur
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('pelanggan.view'))
            <x-admin-nav-link href="{{ route('admin.pelanggan.index') }}" :active="request()->routeIs('admin.pelanggan.*')">
                <x-icon name="user"/>Pelanggan
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('promo.view'))
            <x-admin-nav-link href="{{ route('admin.promo.index') }}" :active="request()->routeIs('admin.promo.*')">
                <x-icon name="tag"/>Promo
            </x-admin-nav-link>
            @endif

            @if($u->hasPermission('jadwal_kelas.view') || $u->hasPermission('bookings.view') || $u->hasPermission('absensi.view'))
            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Operasional</p>
            @endif
            @if($u->hasPermission('jadwal_kelas.view'))
            <x-admin-nav-link href="{{ route('admin.jadwal-kelas.index') }}" :active="request()->routeIs('admin.jadwal-kelas.*')">
                <x-icon name="calendar"/>Jadwal Kelas
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('bookings.view'))
            <x-admin-nav-link href="{{ route('admin.bookings.index') }}" :active="request()->routeIs('admin.bookings.*')">
                <x-icon name="bookmark"/>Bookings
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('absensi.view'))
            <x-admin-nav-link href="{{ route('admin.absensi.index') }}" :active="request()->routeIs('admin.absensi.*')">
                <x-icon name="check-circle"/>Absensi
            </x-admin-nav-link>
            @endif

            @if($u->hasPermission('transaksi.view') || $u->hasPermission('pembelian_package.view') || $u->hasPermission('kredit.view'))
            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Keuangan</p>
            @endif
            @if($u->hasPermission('transaksi.view'))
            <x-admin-nav-link href="{{ route('admin.transaksi.index') }}" :active="request()->routeIs('admin.transaksi.*')">
                <x-icon name="credit-card"/>Transaksi
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('pembelian_package.view'))
            <x-admin-nav-link href="{{ route('admin.pembelian-package.index') }}" :active="request()->routeIs('admin.pembelian-package.*')">
                <x-icon name="shopping-bag"/>Pembelian
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('kredit.view'))
            <x-admin-nav-link href="{{ route('admin.kredit.index') }}" :active="request()->routeIs('admin.kredit.*')">
                <x-icon name="trending-up"/>Kredit
            </x-admin-nav-link>
            @endif

            @if($u->hasPermission('artikel.view') || $u->hasPermission('users.view') || $u->hasPermission('roles.view') || $u->hasPermission('activity_logs.view'))
            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Konten & Akses</p>
            @endif
            @if($u->hasPermission('artikel.view'))
            <x-admin-nav-link href="{{ route('admin.artikel.index') }}" :active="request()->routeIs('admin.artikel.*')">
                <x-icon name="file-text"/>Artikel
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('users.view'))
            <x-admin-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                <x-icon name="users"/>Users
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('roles.view'))
            <x-admin-nav-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles.*')">
                <x-icon name="shield"/>Roles
            </x-admin-nav-link>
            <x-admin-nav-link href="{{ route('admin.permissions.index') }}" :active="request()->routeIs('admin.permissions.*')">
                <x-icon name="key"/>Permissions
            </x-admin-nav-link>
            @endif
            @if($u->hasPermission('activity_logs.view'))
            <x-admin-nav-link href="{{ route('admin.activity-logs.index') }}" :active="request()->routeIs('admin.activity-logs.*')">
                <x-icon name="activity"/>Activity Logs
            </x-admin-nav-link>
            @endif
        </nav>

        <div class="px-4 py-3 border-t border-gray-800 text-xs shrink-0">
            <p class="text-gray-400 truncate font-medium">{{ auth()->user()->nama ?? 'Admin' }}</p>
            <form method="POST" action="{{ route('admin.logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="text-red-400 hover:text-red-300 transition">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="ml-64 flex-1">
        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
            <div class="flex items-center gap-4">
                @yield('actions')
            </div>
        </header>

        {{-- Flash messages --}}
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

        {{-- Page content --}}
        <div class="p-8">
            @yield('content')
        </div>
    </main>

</body>
</html>
