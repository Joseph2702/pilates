<aside class="lg:col-span-1 space-y-2">
    <nav class="space-y-1">
        @foreach([
            ['instruktur.profile.index', 'My Profile',
                '<path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>'],
            ['instruktur.jadwal.index', 'My Schedule',
                '<path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>'],
        ] as $item)
        @php
            $isActive = request()->routeIs($item[0])
                || ($item[0] === 'instruktur.jadwal.index' && request()->routeIs('instruktur.absensi.*'));
        @endphp
        <a href="{{ route($item[0]) }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition rounded-sm
                {{ $isActive ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-4 h-4 shrink-0 {{ $isActive ? 'text-purple-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! $item[2] !!}</svg>
            {{ $item[1] }}
        </a>
        @endforeach
    </nav>

    {{-- Today's class card --}}
    @if(isset($todayClass))
    <div class="bg-gray-900 text-white p-5 mt-4">
        <p class="text-xs font-semibold tracking-widest uppercase text-gray-400 mb-3">Next Class Today</p>
        <p class="text-xs font-black uppercase tracking-wide text-purple-400">{{ $todayClass->kelas?->nama_kelas ?? 'Class' }}</p>
        <p class="text-xs text-gray-300 mt-1">
            {{ $todayClass->jam_mulai ? \Carbon\Carbon::parse($todayClass->jam_mulai)->format('H:i') : '--' }} WIB
        </p>
    </div>
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
