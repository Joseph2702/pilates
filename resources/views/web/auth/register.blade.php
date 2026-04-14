@extends('layouts.app')
@section('title', 'Register - Femm Pilates')

@section('content')
<section class="min-h-[80vh] flex items-center justify-center bg-gray-50 py-16 px-6">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block text-2xl font-black tracking-widest text-gray-900 uppercase mb-6">
                <span class="text-purple-500">FEMM</span> PILATES
            </a>
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Create Your Account</h1>
            <p class="text-gray-500 text-sm mt-2">Join Femm Pilates and start your journey.</p>
        </div>

        <div class="bg-white border border-gray-200 p-8">
            @if(session('error'))
            <div class="mb-5 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('web.register.post') }}">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1.5">Full Name</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required
                            class="w-full border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition @error('nama') border-red-400 @enderror">
                        @error('nama')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition @error('email') border-red-400 @enderror">
                        @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1.5">Phone Number</label>
                        <input type="tel" name="no_hp" value="{{ old('no_hp') }}" inputmode="numeric" pattern="[0-9]*"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                            class="w-full border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1.5">Password</label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition @error('password') border-red-400 @enderror">
                        @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition">
                    </div>
                    <button type="submit"
                        class="w-full bg-purple-500 hover:bg-purple-600 text-white py-3 text-sm font-semibold tracking-widest uppercase transition">
                        CREATE ACCOUNT
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account?
            <a href="#" onclick="event.preventDefault(); openLoginModal();" class="text-purple-600 font-semibold hover:text-purple-700">Sign in</a>
        </p>
    </div>
</section>
@endsection
