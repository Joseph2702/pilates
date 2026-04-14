@extends('layouts.app')
@section('title', 'Contact Us - Femm Pilates')

@section('content')

{{-- Hero --}}
<section class="relative h-64 md:h-80 flex items-center bg-gray-900 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/30 to-gray-900/70 z-10"></div>
    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1600&q=80" alt="Contact" class="absolute inset-0 w-full h-full object-cover opacity-60">
    <div class="relative z-20 w-full max-w-7xl mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-6xl font-black text-white leading-tight">Get in Touch</h1>
        <p class="text-gray-300 text-sm mt-3 max-w-md mx-auto">Find your flow. Our studio doors are always open for your Pilates journey.</p>
    </div>
</section>

{{-- Info Cards --}}
<section class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Left: Dark info card --}}
        <div class="bg-gray-900 text-white p-8">
            <p class="text-xs font-semibold tracking-widest text-purple-400 uppercase mb-6">Femm Pilates</p>
            <div class="space-y-5">
                <div class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-purple-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-white">Jl. Abdul Majid Raya No. 33, RT.7/RW.11</p>
                        <p class="text-xs text-gray-400 mt-0.5">Kemang, Cilandak Sel., Cilandak, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12410</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                    <p class="text-sm font-medium">0812-9959-9692</p>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                    <p class="text-sm font-medium">femm.pilates@gmail.com</p>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-white/10">
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-4">Follow Our Journey</p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 border border-white/20 flex items-center justify-center text-gray-400 hover:border-purple-400 hover:text-purple-400 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 border border-white/20 flex items-center justify-center text-gray-400 hover:border-purple-400 hover:text-purple-400 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 border border-white/20 flex items-center justify-center text-gray-400 hover:border-purple-400 hover:text-purple-400 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.27 8.27 0 004.84 1.56V6.78a4.85 4.85 0 01-1.07-.09z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Right: Hours card --}}
        <div class="space-y-4">
            <div class="border border-gray-200 p-8">
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-5">Operating Hours</p>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Mon – Fri</span>
                        <span class="font-semibold text-gray-900">07:00 AM – 10:00 PM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sat – Sun</span>
                        <span class="font-semibold text-gray-900">07:00 AM – 7:00 PM</span>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 p-8">
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Private Sessions</p>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Private sessions are available outside of standard hours by appointment. Please contact the studio directly for personalized scheduling.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Photo + Quote Grid --}}
<section class="max-w-7xl mx-auto px-6 pb-20">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="col-span-1 row-span-2 bg-gray-100 overflow-hidden" style="min-height: 300px;">
            <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=600&q=80" alt="Studio" class="w-full h-full object-cover">
        </div>
        <div class="bg-gray-100 overflow-hidden h-36 md:h-full">
            <img src="https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=600&q=80" alt="Studio" class="w-full h-full object-cover">
        </div>
        <div class="bg-purple-500 p-8 flex items-center justify-center h-36 md:h-full">
            <p class="text-white font-black text-lg leading-snug text-center">"A space designed for precision, vitality, and your best self."</p>
        </div>
        <div class="bg-gray-100 overflow-hidden h-36 md:h-full">
            <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=600&q=80" alt="Equipment" class="w-full h-full object-cover">
        </div>
    </div>
</section>

@endsection
