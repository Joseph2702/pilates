<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Instruktur;
use App\Domain\Entity\JadwalKelas;
use App\Domain\Entity\Kelas;
use App\Http\Controllers\Controller;
use App\Http\Service\ActivityLogService;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalKelasWebController extends Controller
{
    use PassPermissionsToView;
    
    public function __construct(protected ActivityLogService $activityLog) {}
    
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
            'tanggal_kelas' => 'required|date_format:Y-m-d',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'kuota_maksimal' => 'required|integer|min:1',
        ]);

        // Validate that jam_mulai < jam_selesai
        if ($data['jam_mulai'] >= $data['jam_selesai']) {
            return back()->withErrors(['jam_selesai' => 'Jam selesai harus lebih besar dari jam mulai'])->withInput();
        }

        // Combine tanggal_kelas + jam_mulai into timestamp
        $data['jam_mulai'] = $data['tanggal_kelas'] . ' ' . $data['jam_mulai'];
        $data['jam_selesai'] = $data['tanggal_kelas'] . ' ' . $data['jam_selesai'];
        // tanggal_kelas stays as date only
        $data['tanggal_kelas'] = $data['tanggal_kelas'] . ' 00:00:00';

        $data['kuota_terisi'] = 0;
        $jadwal = JadwalKelas::create($data);
        
        $kelas = Kelas::findOrFail($data['id_kelas']);
        $this->activityLog->log(
            Auth::id(),
            'jadwal_kelas',
            'create',
            'Membuat jadwal kelas baru untuk: ' . $kelas->nama_kelas
        );

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
            'tanggal_kelas' => 'required|date_format:Y-m-d',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'kuota_maksimal' => 'required|integer|min:1',
        ]);

        // Validate that jam_mulai < jam_selesai
        if ($data['jam_mulai'] >= $data['jam_selesai']) {
            return back()->withErrors(['jam_selesai' => 'Jam selesai harus lebih besar dari jam mulai'])->withInput();
        }

        // Combine tanggal_kelas + jam_mulai into timestamp
        $data['jam_mulai'] = $data['tanggal_kelas'] . ' ' . $data['jam_mulai'];
        $data['jam_selesai'] = $data['tanggal_kelas'] . ' ' . $data['jam_selesai'];
        // tanggal_kelas stays as date only
        $data['tanggal_kelas'] = $data['tanggal_kelas'] . ' 00:00:00';

        $jadwal->update($data);
        
        $kelas = Kelas::findOrFail($data['id_kelas']);
        $this->activityLog->log(
            Auth::id(),
            'jadwal_kelas',
            'update',
            'Mengupdate jadwal kelas untuk: ' . $kelas->nama_kelas
        );

        return redirect()->route('admin.jadwal-kelas.index')->with('success', 'Jadwal kelas berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        $jadwal = JadwalKelas::with('kelas')->findOrFail($id);
        $kelasName = $jadwal->kelas->nama_kelas;
        $jadwal->delete();
        
        $this->activityLog->log(
            Auth::id(),
            'jadwal_kelas',
            'delete',
            'Menghapus jadwal kelas untuk: ' . $kelasName
        );
        
        return redirect()->route('admin.jadwal-kelas.index')->with('success', 'Jadwal kelas berhasil dihapus.');
    }
}
