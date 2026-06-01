<?php

namespace App\Http\Controllers\Web\Pelanggan;

use App\Domain\Entity\Instruktur;
use App\Domain\Entity\JadwalKelas;
use App\Domain\Entity\Kelas;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassesWebController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::all();
        return view('web.classes.index', compact('kelasList'));
    }

    public function schedule(Request $request, int $id)
    {
        $kelas = Kelas::findOrFail($id);

        $selectedDate = $request->date
            ? Carbon::parse($request->date)->startOfDay()
            : Carbon::today();

        $dates = collect();
        for ($i = 0; $i < 14; $i++) {
            $dates->push(Carbon::today()->addDays($i));
        }

        $query = JadwalKelas::with(['instruktur.user', 'kelas'])
            ->where('id_kelas', $id)
            ->whereDate('tanggal_kelas', $selectedDate);

        if ($selectedDate->isToday()) {
            $query->where('jam_selesai', '>', now());
        }

        if ($request->instruktur) {
            $query->where('id_instruktur', $request->instruktur);
        }

        $jadwalList = $query->orderBy('jam_mulai')->get();

        $instrukturList = Instruktur::with('user')
            ->whereHas('jadwal', fn ($q) => $q->where('id_kelas', $id))
            ->get();

        return view('web.classes.schedule', compact(
            'kelas',
            'jadwalList',
            'instrukturList',
            'selectedDate',
            'dates',
        ));
    }
}
