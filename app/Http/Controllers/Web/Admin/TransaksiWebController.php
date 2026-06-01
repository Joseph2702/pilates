<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Transaksi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransaksiWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with('pembelianPackage.pelanggan.user');

        if ($status = $request->get('status')) {
            $query->where('status_internal', $status);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $transaksiList = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.transaksi.index', compact('transaksiList'));
    }

    public function show(int $id)
    {
        $transaksi = Transaksi::with('pembelianPackage.pelanggan.user', 'pembelianPackage.package')->findOrFail($id);
        return view('admin.transaksi.show', compact('transaksi'));
    }
}
