<?php

namespace App\Http\Controllers\Instruktur;

use App\Common\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Repository\InstrukturRepository;
use App\Http\Service\JadwalKelasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalInstrukturController extends Controller
{
    public function __construct(
        protected JadwalKelasService $jadwalService,
        protected InstrukturRepository $instrukturRepo,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $instruktur = $this->instrukturRepo->findByUserId($request->user()->id_user);

        if (! $instruktur) {
            return ApiResponse::forbidden('Profil instruktur tidak ditemukan');
        }

        $jadwal = $this->jadwalService->listByInstruktur($instruktur->id_instruktur);

        return ApiResponse::success($jadwal);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $jadwal = $this->jadwalService->getOrFail($id);
        $instruktur = $this->instrukturRepo->findByUserId($request->user()->id_user);

        if (! $instruktur || $jadwal->id_instruktur !== $instruktur->id_instruktur) {
            return ApiResponse::forbidden('Anda tidak mengajar jadwal ini');
        }

        return ApiResponse::success($jadwal->load(['kelas', 'bookings.pelanggan.user', 'bookings.absensi']));
    }
}
