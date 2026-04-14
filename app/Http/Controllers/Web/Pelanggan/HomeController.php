<?php

namespace App\Http\Controllers\Web\Pelanggan;

use App\Domain\Entity\Kelas;
use App\Domain\Entity\Package;
use App\Domain\Entity\Pelanggan;
use App\Domain\Entity\PembelianPackage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::limit(4)->get();
        $featuredPackages = Package::where('status_package', 'active')
            ->orderBy('harga')
            ->limit(3)
            ->get();

        $purchasedIds = [];
        if (Auth::check()) {
            $pelanggan = Pelanggan::where('id_user', Auth::id())->first();
            $oneTimePackage = Package::where('status_package', 'active')->orderBy('harga')->first();
            if ($pelanggan && $oneTimePackage) {
                $alreadyBought = PembelianPackage::where('id_pelanggan', $pelanggan->id_pelanggan)
                    ->where('id_package', $oneTimePackage->id_package)
                    ->where('status_pembelian', 'paid')
                    ->exists();
                if ($alreadyBought) {
                    $purchasedIds = [$oneTimePackage->id_package];
                }
            }
        }

        return view('web.home', compact('kelasList', 'featuredPackages', 'purchasedIds'));
    }
}
