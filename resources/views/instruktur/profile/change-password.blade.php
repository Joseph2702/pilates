@extends('layouts.app')
@section('title', 'Change Password - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-gray-900">My Account</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        @include('instruktur._sidebar', ['todayClass' => null])

        <div class="lg:col-span-3">

            @if(session('success'))
            <div class="mb-5 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                {{ session('success') }}
            </div>
            @endif

            <div class="border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <p class="text-sm font-bold text-gray-900 uppercase tracking-widest">Change Password</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('instruktur.profile.update-password') }}">
                        @csrf @method('PUT')
                        <div class="space-y-4 max-w-md">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Current Password</label>
                                <input type="password" name="current_password" required
                                    class="w-full border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition @error('current_password') border-red-400 @enderror">
                                @error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">New Password</label>
                                <input type="password" name="password" required
                                    class="w-full border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition @error('password') border-red-400 @enderror">
                                @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Confirm New Password</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition">
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-6 pt-5 border-t border-gray-100">
                            <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-8 py-2.5 text-sm font-semibold tracking-wide transition">
                                Update Password
                            </button>
                            <a href="{{ route('instruktur.profile.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
