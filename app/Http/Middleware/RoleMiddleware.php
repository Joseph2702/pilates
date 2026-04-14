<?php

namespace App\Http\Middleware;

use App\Common\Response\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::unauthorized('Unauthenticated');
        }

        $userRoles = $user->roles()->wherePivot('is_active', true)->pluck('nama_role')->toArray();

        foreach ($roles as $role) {
            if (in_array($role, $userRoles, true)) {
                return $next($request);
            }
        }

        return ApiResponse::forbidden('Anda tidak memiliki akses ke resource ini');
    }
}
