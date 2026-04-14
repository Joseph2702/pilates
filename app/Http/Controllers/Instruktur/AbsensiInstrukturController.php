<?php

namespace App\Http\Controllers\Instruktur;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Repository\InstrukturRepository;
use App\Http\Service\AbsensiService;
use App\Http\Service\JadwalKelasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AbsensiInstrukturController extends Controller
{
    public function __construct(
        protected AbsensiService $absensiService,
        protected JadwalKelasService $jadwalService,
        protected InstrukturRepository $instrukturRepo,
    ) {}

    public function listByJadwal(Request $request, int $idJadwalKelas): JsonResponse
    {
        $jadwal = $this->jadwalService->getOrFail($idJadwalKelas);
        $instruktur = $this->instrukturRepo->findByUserId($request->user()->id_user);

        if (! $instruktur || $jadwal->id_instruktur !== $instruktur->id_instruktur) {
            return ApiResponse::forbidden('Anda tidak mengajar jadwal ini');
        }

        return ApiResponse::success($this->absensiService->listByJadwal($idJadwalKelas));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_booking' => 'required|integer|exists:booking,id_booking',
            'status_kehadiran' => 'required|string|in:hadir,tidak_hadir',
        ]);

        $absensi = $this->absensiService->markAttendance($data['id_booking'], $data['status_kehadiran']);

        return ApiResponse::success($absensi);
    }
}
