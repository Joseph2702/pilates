<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\PembelianPackage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembelianPackageWebController extends Controller
{
    public function index(Request $request)
    {
        $query = PembelianPackage::with(['pelanggan.user', 'package', 'promo']);

        if ($status = $request->get('status')) {
            $query->where('status_pembelian', $status);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('tanggal_pembelian', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('tanggal_pembelian', '<=', $dateTo);
        }

        $pembelianList = $query->orderBy('tanggal_pembelian', 'desc')->paginate(15)->withQueryString();

        return view('admin.pembelian-package.index', compact('pembelianList'));
    }

    public function show(int $id)
    {
        $pembelian = PembelianPackage::with(['pelanggan.user', 'package', 'promo', 'transaksi'])->findOrFail($id);

        return view('admin.pembelian-package.show', compact('pembelian'));
    }
}
