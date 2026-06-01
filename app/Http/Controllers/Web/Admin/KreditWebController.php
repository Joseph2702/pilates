<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\MutasiKredit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KreditWebController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiKredit::with('pelanggan.user');

        if ($jenis = $request->get('jenis')) {
            $query->where('jenis_mutasi', $jenis);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('tanggal_mutasi', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('tanggal_mutasi', '<=', $dateTo);
        }

        $mutasiList = $query->orderBy('tanggal_mutasi', 'desc')->paginate(20)->withQueryString();

        return view('admin.kredit.index', compact('mutasiList'));
    }
}
