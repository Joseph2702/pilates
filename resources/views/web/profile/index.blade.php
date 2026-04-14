@extends('layouts.app')
@section('title', 'My Account - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">

    {{-- Page Title --}}
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-gray-900">My Account</h1>
        <p class="text-gray-400 text-sm mt-1">Manage your Precision Pilates journey, view your progress, and schedule your next session.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- Sidebar --}}
        <aside class="lg:col-span-1 space-y-2">
            <nav class="space-y-1">
                @foreach([
                    ['profile.index', 'My Profile', '<path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>', 'profile'],
                    ['profile.schedule', 'My Schedule', '<path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>', 'booking'],
                    ['profile.packages', 'My Packages', '<path d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>', 'package'],
                    ['profile.transactions', 'My Transactions', '<path d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>', 'transaction'],
                ] as $item)
                @php
                    $permissionMap = [
                        'profile' => $permissions['canViewProfile'] || $permissions['canUpdateProfile'],
                        'booking' => $permissions['canViewBooking'],
                        'package' => $permissions['canViewPackage'],
                        'transaction' => $permissions['canViewTransaction'],
                    ];
                @endphp
                @if($permissionMap[$item[3]] ?? false)
                <a href="{{ route($item[0]) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition rounded-sm
                        {{ request()->routeIs($item[0]) ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs($item[0]) ? 'text-purple-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! $item[2] !!}</svg>
                    {{ $item[1] }}
                </a>
                @endif
                @endforeach
            </nav>

            {{-- Next Session Card --}}
            @if($recentBookings->count())
            @php $nextBooking = $recentBookings->where('status_booking', 'booked')->first(); @endphp
            @if($nextBooking)
            <div class="bg-gray-900 text-white p-5 mt-4">
                <p class="text-xs font-semibold tracking-widest uppercase text-gray-400 mb-3">Next Session</p>
                <p class="text-xs font-black uppercase tracking-wide text-purple-400">{{ $nextBooking->jadwalKelas?->kelas?->nama_kelas ?? 'Class' }}</p>
                <p class="text-xs text-gray-300 mt-1">
                    {{ $nextBooking->jadwalKelas ? \Carbon\Carbon::parse($nextBooking->jadwalKelas->tanggal_kelas)->format('l') : '' }},
                    {{ $nextBooking->jadwalKelas ? \Carbon\Carbon::parse($nextBooking->jadwalKelas->jam_mulai)->format('H.i') : '' }} WIB
                </p>
            </div>
            @endif
            @endif

            {{-- Sign Out --}}
            <form method="POST" action="{{ route('web.logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition rounded-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                    Sign Out
                </button>
            </form>
        </aside>

        {{-- Main Content --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Avatar Card --}}
            <div class="border border-gray-200 p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-purple-100 border-2 border-purple-200 flex items-center justify-center text-purple-600 text-xl font-black shrink-0">
                        {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-lg font-black text-gray-900">{{ auth()->user()->nama }}</p>
                        <p class="text-sm text-gray-400 mt-0.5">Since {{ auth()->user()->created_at ? \Carbon\Carbon::parse(auth()->user()->created_at)->format('F Y') : 'N/A' }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="text-xs font-semibold text-gray-500 hover:text-purple-600 uppercase tracking-widest transition border-b border-gray-300 hover:border-purple-400 pb-0.5">
                    Edit Profile
                </a>
            </div>

            {{-- Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Contact Information --}}
                <div class="border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        <p class="text-xs font-semibold tracking-widest uppercase text-gray-500">Contact Information</p>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Email Address</p>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Phone Number</p>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->no_hp ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Security</p>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-800">Password</p>
                                @if($permissions['canChangePassword'])
                                <a href="{{ route('profile.edit') }}#change-password" class="text-xs text-purple-500 hover:text-purple-700 transition">Change Password</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Personal Details --}}
                <div class="border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                        <p class="text-xs font-semibold tracking-widest uppercase text-gray-500">Personal Details</p>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Date of Birth</p>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->tanggal_lahir ? \Carbon\Carbon::parse(auth()->user()->tanggal_lahir)->format('M d, Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Gender</p>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->jenis_kelamin ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Place of Birth</p>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->tempat_lahir ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Remaining Credits --}}
            <div class="border border-gray-200 p-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                    <div>
                        <p class="text-xs font-semibold tracking-widest uppercase text-gray-500">Remaining Credits</p>
                        <p class="text-xs text-gray-400 mt-0.5">Available for any group class or private session.</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    @if($sisaKredit > 0)
                    <p class="text-2xl font-black text-gray-900">{{ $sisaKredit }}</p>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Credits</p>
                @else
                    <a href="{{ route('packages.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 text-xs font-semibold tracking-widest uppercase transition">
                        Buy Package
                    </a>
                @endif
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
