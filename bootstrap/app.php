<?php

use App\Common\Exception\BusinessException;
use App\Common\Response\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.login');
            }
            return route('web.login');
        });

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'role.admin' => \App\Http\Middleware\RoleAdmin::class,
            'role.pelanggan' => \App\Http\Middleware\RolePelanggan::class,
            'block.admin' => \App\Http\Middleware\BlockAdminFromPublic::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (BusinessException $e) {
            return ApiResponse::error($e->getMessage(), $e->getStatusCode(), $e->getErrors());
        });

        $exceptions->render(function (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), $e->getMessage());
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiResponse::unauthorized($e->getMessage());
            }
            // For web routes, redirect to appropriate login page
            $redirectTo = $request->is('admin/*') || $request->is('admin')
                ? route('admin.login')
                : route('web.login');
            return redirect($redirectTo);
        });

        $exceptions->render(function (ModelNotFoundException $e) {
            return ApiResponse::notFound('Resource not found');
        });
    })->create();
