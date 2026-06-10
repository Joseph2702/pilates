<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockAdminFromPublic
{
    /**
     * Middleware to prevent admin users from accessing public routes.
     * Admin users should only access /admin area.
     *
     * If an authenticated user with admin role tries to access public routes,
     * they are redirected to the admin dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is authenticated and has admin role, redirect to admin dashboard
        if ($user && $user->isAdminAreaUser()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
