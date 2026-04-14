<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Femm Pilates')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 font-sans antialiased">

    {{-- Navbar --}}
    <x-web-navbar/>

    {{-- Spacer for fixed navbar --}}
    <div class="h-16"></div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div id="flash-success" class="fixed top-4 right-4 z-50 bg-green-600 text-white text-sm font-medium px-5 py-3 shadow-lg max-w-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div id="flash-error" class="fixed top-4 right-4 z-50 bg-red-600 text-white text-sm font-medium px-5 py-3 shadow-lg max-w-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Page Content --}}
    @yield('content')

    {{-- Footer --}}
    <x-web-footer/>

    {{-- Login Modal --}}
    @guest
    <div id="login-modal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeLoginModal()"></div>
        {{-- Card --}}
        <div class="relative bg-white w-full max-w-sm mx-4 p-8 shadow-2xl">
            <div class="flex items-start justify-between mb-6">
                <h2 class="text-xl font-black text-gray-900">Sign in</h2>
                <button onclick="closeLoginModal()" class="text-gray-400 hover:text-gray-700 transition -mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if(session('login_error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                {{ session('login_error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('web.login.post') }}">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="your@email.com"
                            class="w-full border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest">Password</label>
                        </div>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition">
                    </div>
                    <button type="submit"
                        class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 text-sm font-semibold tracking-widest uppercase transition mt-2">
                        SIGN IN
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-500 mt-5">
                New to Femm Pilates?
                <a href="{{ route('web.register') }}" class="text-gray-900 font-semibold hover:text-purple-600 transition">Create an Account</a>
            </p>
        </div>
    </div>
    @endguest

    <script>
        // Auto-dismiss flash messages
        ['flash-success', 'flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) setTimeout(() => el.remove(), 4000);
        });

        // Login Modal
        function openLoginModal() {
            const modal = document.getElementById('login-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }
        function closeLoginModal() {
            const modal = document.getElementById('login-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }
        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLoginModal();
        });
        // Auto-open if there's a login error or hash
        if (window.location.hash === '#login') openLoginModal();
        @if(session('login_error'))
        openLoginModal();
        @endif
    </script>
</body>
</html>
