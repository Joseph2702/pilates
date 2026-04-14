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

    <form method="POST" action="{{ route('web.logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition rounded-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
            Sign Out
        </button>
    </form>
</aside>
