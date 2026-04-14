<?php

namespace App\Http\Controllers\Web\Pelanggan;

use App\Domain\Entity\Pelanggan;
use App\Domain\Entity\User;
use App\Domain\Entity\Role;
use App\Http\Controllers\Controller;
use App\Http\Service\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthWebPelangganController extends Controller
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('profile.index');
        }
        return view('web.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $redirectTo = $request->input('redirect_to', route('profile.index'));

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $this->activityLog->log(
                Auth::id(),
                'auth',
                'login',
                'Login via web'
            );

            return redirect()->to($redirectTo);
        }

        return back()->with('login_error', 'Email atau password salah.');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('profile.index');
        }
        return view('web.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'no_hp'    => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        // Assign pelanggan role to new user
        $pelangganRole = Role::where('nama_role', 'pelanggan')->first();
        if ($pelangganRole) {
            $user->roles()->attach($pelangganRole->id_role, ['is_active' => true]);
        }

        Pelanggan::create([
            'id_user'       => $user->id_user,
            'tanggal_daftar' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $this->activityLog->log(
            $user->id_user,
            'auth',
            'register',
            'Registrasi akun pelanggan baru'
        );

        return redirect()->route('profile.index')->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();

        $this->activityLog->log(
            $userId,
            'auth',
            'logout',
            'Logout via web'
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
