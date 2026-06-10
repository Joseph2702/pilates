<?php

namespace App\Http\Controllers\Web\Instruktur;

use App\Http\Controllers\Controller;
use App\Http\Service\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthInstrukturController extends Controller
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->instruktur) {
                return redirect()->route('instruktur.dashboard');
            }
        }

        return view('instruktur.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user->instruktur) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'Akun ini bukan akun instruktur.'])->onlyInput('email');
            }

            $request->session()->regenerate();

            $this->activityLog->log(
                $user->id_user,
                'auth',
                'login',
                'Login via instruktur panel'
            );

            return redirect()->intended(route('instruktur.dashboard'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $this->activityLog->log(
            Auth::id(),
            'auth',
            'logout',
            'Logout via instruktur panel'
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('instruktur.login');
    }
}
