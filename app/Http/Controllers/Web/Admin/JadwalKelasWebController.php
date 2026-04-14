<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Instruktur;
use App\Domain\Entity\JadwalKelas;
use App\Domain\Entity\Kelas;
use App\Http\Controllers\Controller;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;

class JadwalKelasWebController extends Controller
{
    use PassPermissionsToView;
    
    public function index()
    {
        $jadwalList = JadwalKelas::with(['kelas', 'instruktur.user'])
            ->orderBy('tanggal_kelas', 'desc')->paginate(15);
        
        $permissions = $this->buildPermissions('jadwal_kelas');
        return view('admin.jadwal-kelas.index', compact('jadwalList', 'permissions'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $instrukturList = Instruktur::with('user')->orderBy('id_instruktur')->get();
        return view('admin.jadwal-kelas.create', compact('kelasList', 'instrukturList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_kelas' => 'required|integer|exists:kelas,id_kelas',
            'id_instruktur' => 'required|integer|exists:instruktur,id_instruktur',
            'tanggal_kelas' => 'required|date',
            'jam_mulai' => 'required|date',
            'jam_selesai' => 'required|date|after:jam_mulai',
            'kuota_maksimal' => 'required|integer|min:1',
        ]);

        $data['kuota_terisi'] = 0;
        JadwalKelas::create($data);

        return redirect()->route('admin.jadwal-kelas.index')->with('success', 'Jadwal kelas berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $jadwal = JadwalKelas::findOrFail($id);
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $instrukturList = Instruktur::with('user')->orderBy('id_instruktur')->get();
        return view('admin.jadwal-kelas.edit', compact('jadwal', 'kelasList', 'instrukturList'));
    }

    public function update(Request $request, int $id)
    {
        $jadwal = JadwalKelas::findOrFail($id);

        $data = $request->validate([
            'id_kelas' => 'required|integer|exists:kelas,id_kelas',
            'id_instruktur' => 'required|integer|exists:instruktur,id_instruktur',
            'tanggal_kelas' => 'required|date',
            'jam_mulai' => 'required|date',
            'jam_selesai' => 'required|date|after:jam_mulai',
            'kuota_maksimal' => 'required|integer|min:1',
        ]);

        $jadwal->update($data);

        return redirect()->route('admin.jadwal-kelas.index')->with('success', 'Jadwal kelas berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        JadwalKelas::findOrFail($id)->delete();
        return redirect()->route('admin.jadwal-kelas.index')->with('success', 'Jadwal kelas berhasil dihapus.');
    }
}
