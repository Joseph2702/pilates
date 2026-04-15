@extends('layouts.app')
@section('title', 'My Account - Femm Pilates')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-gray-900">My Account</h1>
        <p class="text-gray-400 text-sm mt-1">Manage your instructor profile and schedule your sessions.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        @include('instruktur._sidebar', ['todayClass' => $todayClass ?? null])

        <div class="lg:col-span-3 space-y-5">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                {{ session('success') }}
            </div>
            @endif

            {{-- Avatar Card --}}
            <div class="border border-gray-200 p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-purple-100 border-2 border-purple-200 flex items-center justify-center text-purple-600 text-xl font-black shrink-0">
                        {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-lg font-black text-gray-900">{{ auth()->user()->nama }}</p>
                        @if($instruktur?->spesialisasi)
                        <p class="text-sm text-purple-500 font-medium mt-0.5">{{ $instruktur->spesialisasi }}</p>
                        @endif
                        <p class="text-sm text-gray-400 mt-0.5">Instruktur</p>
                    </div>
                </div>
                <a href="{{ route('instruktur.profile.edit') }}" class="text-xs font-semibold text-gray-500 hover:text-purple-600 uppercase tracking-widest transition border-b border-gray-300 hover:border-purple-400 pb-0.5">
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
                                <a href="{{ route('instruktur.profile.edit') }}#change-password" class="text-xs text-purple-500 hover:text-purple-700 transition">Change Password</a>
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

            {{-- Specialization Card --}}
            <div class="border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                    <p class="text-xs font-semibold tracking-widest uppercase text-gray-500">Instructor Info</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Specialization</p>
                    <p class="text-sm font-medium text-gray-800">{{ $instruktur?->spesialisasi ?? '-' }}</p>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
