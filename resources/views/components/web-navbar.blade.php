<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-gray-900">
            <svg class="w-5 h-5 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <span class="font-black text-sm tracking-widest uppercase">Femm <span class="text-purple-500">Pilates</span></span>
        </a>

        {{-- Nav Links --}}
        <nav class="hidden md:flex items-center gap-8 text-sm">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 transition font-medium {{ request()->routeIs('home') ? 'text-gray-900 border-b-2 border-purple-500 pb-0.5' : '' }}">Home</a>
            <a href="{{ route('classes.index') }}" class="text-gray-600 hover:text-gray-900 transition font-medium {{ request()->routeIs('classes.*') ? 'text-purple-600 border-b-2 border-purple-500 pb-0.5' : '' }}">Classes</a>
            <a href="{{ route('packages.index') }}" class="text-gray-600 hover:text-gray-900 transition font-medium {{ request()->routeIs('packages.*') ? 'text-purple-600 border-b-2 border-purple-500 pb-0.5' : '' }}">Packages</a>
            <a href="{{ route('articles.index') }}" class="text-gray-600 hover:text-gray-900 transition font-medium {{ request()->routeIs('articles.*') ? 'text-purple-600 border-b-2 border-purple-500 pb-0.5' : '' }}">Article</a>
            <a href="{{ route('contact') }}" class="text-gray-600 hover:text-gray-900 transition font-medium {{ request()->routeIs('contact') ? 'text-purple-600 border-b-2 border-purple-500 pb-0.5' : '' }}">Contact Us</a>
        </nav>

        {{-- Right Actions --}}
        <div class="flex items-center gap-4">
            @auth
            <a href="{{ route('profile.index') }}" class="flex items-center gap-2 hover:opacity-80 transition" title="My Account">
                <div class="w-8 h-8 bg-purple-500 flex items-center justify-center text-white text-xs font-black">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 2)) }}
                </div>
                <span class="hidden md:block text-sm font-medium text-gray-700 max-w-[120px] truncate">{{ auth()->user()->nama }}</span>
            </a>
            @else
            <button onclick="openLoginModal()" class="inline-flex items-center gap-2 bg-purple-500 hover:bg-purple-600 text-white px-5 py-2 text-xs font-semibold tracking-widest uppercase transition">
                SIGN IN
            </button>
            @endauth

            {{-- Mobile hamburger --}}
            <button id="mobile-menu-btn" class="md:hidden text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-6 py-4 space-y-3 text-sm">
        <a href="{{ route('home') }}" class="block text-gray-700 hover:text-gray-900 font-medium">Home</a>
        <a href="{{ route('classes.index') }}" class="block text-gray-700 hover:text-gray-900 font-medium">Classes</a>
        <a href="{{ route('packages.index') }}" class="block text-gray-700 hover:text-gray-900 font-medium">Packages</a>
        <a href="{{ route('articles.index') }}" class="block text-gray-700 hover:text-gray-900 font-medium">Article</a>
        <a href="{{ route('contact') }}" class="block text-gray-700 hover:text-gray-900 font-medium">Contact Us</a>
        @auth
        <a href="{{ route('profile.index') }}" class="block text-purple-600 font-semibold">My Account ({{ auth()->user()->nama }})</a>
        @else
        <button onclick="openLoginModal()" class="block text-purple-600 font-semibold w-full text-left">Sign In</button>
        <a href="{{ route('web.register') }}" class="block text-gray-700 hover:text-gray-900">Register</a>
        @endauth
    </div>
</header>

<script>
document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
});
</script>
