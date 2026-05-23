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

        if (!$user->isAdminAreaUser()) {
            return redirect()->route('home')
                ->with('error', 'Anda tidak memiliki akses ke admin panel.');
        }

        return $next($request);
    }
}
