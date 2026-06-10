<?php

namespace App\Http\Controllers\Web\Instruktur;

use App\Domain\Entity\JadwalKelas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileInstrukturController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $instruktur = $user->instruktur;

        $todayClass = $instruktur
            ? JadwalKelas::with('kelas')
                ->where('id_instruktur', $instruktur->id_instruktur)
                ->whereDate('tanggal_kelas', today())
                ->orderBy('jam_mulai')
                ->first()
            : null;

        return view('instruktur.profile.index', compact('instruktur', 'todayClass'));
    }

    public function edit()
    {
        $user = Auth::user();
        $instruktur = $user->instruktur;

        return view('instruktur.profile.edit', compact('instruktur'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20|regex:/^[0-9]*$/',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'spesialisasi' => 'nullable|string|max:50',
        ]);

        $user->update($request->only('nama', 'no_hp', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir'));

        if ($request->spesialisasi !== null) {
            $user->instruktur->update(['spesialisasi' => $request->spesialisasi]);
        }

        return redirect()->route('instruktur.profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function editPassword()
    {
        return view('instruktur.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('instruktur.profile.index')->with('success', 'Password berhasil diperbarui.');
    }
}
