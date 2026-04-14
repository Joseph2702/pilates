<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // Check if user has admin role that is active
        $hasAdminRole = $user->roles()
            ->where('nama_role', 'admin')
            ->wherePivot('is_active', true)
            ->exists();

        if (!$hasAdminRole) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke admin panel. Hanya admin yang diizinkan.');
        }

        return $next($request);
    }
}
