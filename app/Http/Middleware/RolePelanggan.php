<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePelanggan
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('web.login');
        }

        // Check if user has pelanggan role that is active
        $hasPelangganRole = $user->roles()
            ->where('nama_role', 'pelanggan')
            ->wherePivot('is_active', true)
            ->exists();

        if ($user->isAdminAreaUser()) {
            return redirect()->route('admin.dashboard')
                ->with('info', 'Silahkan akses admin panel.');
        }

        if (! $hasPelangganRole) {
            return redirect()->route('home')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini. Hanya pelanggan yang diizinkan.');
        }

        return $next($request);
    }
}
