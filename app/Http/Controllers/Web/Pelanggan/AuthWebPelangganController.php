<?php

namespace App\Http\Controllers\Web\Pelanggan;

use App\Domain\Entity\Pelanggan;
use App\Domain\Entity\Role;
use App\Domain\Entity\User;
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
            $user = Auth::user();
            $isInstruktur = $user->roles()->wherePivot('is_active', true)->where('nama_role', 'instruktur')->exists();
            if ($isInstruktur) {
                return redirect()->route('instruktur.dashboard');
            }
            if ($user->isAdminAreaUser()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('profile.index');
        }

        return view('web.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();
            $request->session()->regenerate();

            $this->activityLog->log(
                $user->id_user,
                'auth',
                'login',
                'Login via web'
            );

            // Instruktur: always go to instruktur dashboard
            $isInstruktur = $user->roles()->wherePivot('is_active', true)->where('nama_role', 'instruktur')->exists();
            if ($isInstruktur) {
                return redirect()->route('instruktur.dashboard');
            }

            // Admin-area users (core admin + all sub-admin roles): go to admin dashboard
            if ($user->isAdminAreaUser()) {
                return redirect()->route('admin.dashboard');
            }

            $redirectTo = $request->input('redirect_to', route('profile.index'));

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
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'nullable|string|max:20|regex:/^[0-9]+$/|unique:users,no_hp',
            'password' => 'required|min:8|confirmed',
        ], [
            'no_hp.unique' => 'Nomor telepon sudah terdaftar.',
            'no_hp.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // Assign pelanggan role to new user
        $pelangganRole = Role::where('nama_role', 'pelanggan')->first();
        if ($pelangganRole) {
            $user->roles()->attach($pelangganRole->id_role, ['is_active' => true]);
        }

        Pelanggan::create([
            'id_user' => $user->id_user,
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
