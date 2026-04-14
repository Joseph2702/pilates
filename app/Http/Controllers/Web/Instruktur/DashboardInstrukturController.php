<?php

namespace App\Http\Controllers\Web\Instruktur;

use App\Domain\Entity\JadwalKelas;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardInstrukturController extends Controller
{
    public function index()
    {
        $instruktur = Auth::user()->instruktur;

        $todaySchedules = JadwalKelas::with('kelas')
            ->where('id_instruktur', $instruktur->id_instruktur)
            ->whereDate('tanggal_kelas', today())
            ->orderBy('jam_mulai')
            ->get();

        $upcomingSchedules = JadwalKelas::with('kelas')
            ->where('id_instruktur', $instruktur->id_instruktur)
            ->whereDate('tanggal_kelas', '>', today())
            ->orderBy('tanggal_kelas')
            ->orderBy('jam_mulai')
            ->limit(5)
            ->get();

        $totalKelas = JadwalKelas::where('id_instruktur', $instruktur->id_instruktur)->count();
        $kelasHariIni = $todaySchedules->count();
        $totalPeserta = JadwalKelas::where('id_instruktur', $instruktur->id_instruktur)
            ->sum('kuota_terisi');

        return view('instruktur.dashboard', compact(
            'instruktur',
            'todaySchedules',
            'upcomingSchedules',
            'totalKelas',
            'kelasHariIni',
            'totalPeserta',
        ));
    }
}
