<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\PembelianPackage;
use App\Http\Controllers\Controller;

class PembelianPackageWebController extends Controller
{
    public function index()
    {
        $pembelianList = PembelianPackage::with(['pelanggan.user', 'package', 'promo'])
            ->orderBy('tanggal_pembelian', 'desc')->paginate(15);

        return view('admin.pembelian-package.index', compact('pembelianList'));
    }

    public function show(int $id)
    {
        $pembelian = PembelianPackage::with(['pelanggan.user', 'package', 'promo', 'transaksi'])->findOrFail($id);
        return view('admin.pembelian-package.show', compact('pembelian'));
    }
}
