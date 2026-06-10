<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\ActivityLogService;
use App\Http\Service\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $auth,
        protected ActivityLogService $activityLog,
    ) {}

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'min:8'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $result = $this->auth->register($data);

        $this->activityLog->log(
            $result['user']->id_user,
            'auth',
            'register',
            'Registrasi via API'
        );

        return ApiResponse::created([
            'user' => [
                'id_user' => $result['user']->id_user,
                'nama' => $result['user']->nama,
                'email' => $result['user']->email,
            ],
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'Registrasi berhasil');
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $result = $this->auth->login($data['email'], $data['password']);

        $this->activityLog->log(
            $result['user']->id_user,
            'auth',
            'login',
            'Login via API'
        );

        return ApiResponse::success([
            'user' => [
                'id_user' => $result['user']->id_user,
                'nama' => $result['user']->nama,
                'email' => $result['user']->email,
            ],
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'Login berhasil');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        $this->activityLog->log(
            $user->id_user,
            'auth',
            'logout',
            'Logout via API'
        );

        $this->auth->logout($user);

        return ApiResponse::success(null, 'Logout berhasil');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return ApiResponse::success([
            'id_user' => $user->id_user,
            'nama' => $user->nama,
            'email' => $user->email,
            'no_hp' => $user->no_hp,
        ]);
    }
}
