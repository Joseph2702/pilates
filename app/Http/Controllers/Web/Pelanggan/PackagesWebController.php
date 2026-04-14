<?php

namespace App\Http\Controllers\Web\Pelanggan;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Package;
use App\Domain\Entity\Pelanggan;
use App\Domain\Entity\PembelianPackage;
use App\Domain\Entity\Promo;
use App\Http\Controllers\Controller;
use App\Http\Service\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackagesWebController extends Controller
{
    public function __construct(protected PaymentService $payments) {}

    public function index()
    {
        $packages = Package::where('status_package', 'active')->orderBy('harga')->get();

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

        return view('web.packages.index', compact('packages', 'purchasedIds'));
    }

    public function checkout(int $id)
    {
        $package = Package::where('status_package', 'active')->findOrFail($id);

        if (Auth::check() && $this->isOneTimePackage($id)) {
            $pelanggan = Pelanggan::where('id_user', Auth::id())->first();
            if ($pelanggan && $this->alreadyPurchased($pelanggan->id_pelanggan, $id)) {
                return redirect()->route('packages.index')->with('error', 'Kamu sudah pernah membeli package ini.');
            }
        }

        return view('web.packages.checkout', compact('package'));
    }

    public function process(Request $request, int $id)
    {
        if (! Auth::check()) {
            return redirect()->route('web.login');
        }

        $package = Package::where('status_package', 'active')->findOrFail($id);

        $pelanggan = Pelanggan::where('id_user', Auth::id())->first();
        if (! $pelanggan) {
            return back()->with('error', 'Akun pelanggan tidak ditemukan.');
        }

        if ($this->isOneTimePackage($id) && $this->alreadyPurchased($pelanggan->id_pelanggan, $id)) {
            return redirect()->route('packages.index')->with('error', 'Kamu sudah pernah membeli package ini.');
        }

        $kodePromo = $request->input('kode_promo');
        $idPromo = null;
        $diskon = 0;
        $hargaAkhir = (float) $package->harga;

        if ($kodePromo) {
            $promo = Promo::where('kode_promo', $kodePromo)
                ->where('status_promo', 'active')
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_selesai', '>=', now())
                ->first();

            if ($promo) {
                $alreadyUsed = PembelianPackage::where('id_pelanggan', $pelanggan->id_pelanggan)
                    ->where('id_promo', $promo->id_promo)
                    ->exists();

                if ($alreadyUsed) {
                    return back()->with('error', 'Kode promo ini sudah pernah kamu gunakan.');
                }

                $diskon = $hargaAkhir * ($promo->persenan_diskon / 100);
                $hargaAkhir = $hargaAkhir - $diskon;
                $idPromo = $promo->id_promo;
            }
        }

        try {
            $result = $this->payments->checkoutWithPromo(
                idPelanggan: $pelanggan->id_pelanggan,
                idPackage: $id,
                idPromo: $idPromo,
                diskon: $diskon,
                hargaAkhir: $hargaAkhir,
            );

            return view('web.packages.payment', [
                'snapToken' => $result['snap_token'],
                'orderId'   => $result['order_id'],
                'package'   => $package,
            ]);
        } catch (BusinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkPromo(Request $request)
    {
        $kode = $request->input('kode_promo');
        if (! $kode) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak boleh kosong.']);
        }

        $promo = Promo::where('kode_promo', $kode)
            ->where('status_promo', 'active')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->first();

        if (! $promo) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak valid atau sudah tidak berlaku.']);
        }

        if (Auth::check()) {
            $pelanggan = Pelanggan::where('id_user', Auth::id())->first();
            if ($pelanggan) {
                $alreadyUsed = PembelianPackage::where('id_pelanggan', $pelanggan->id_pelanggan)
                    ->where('id_promo', $promo->id_promo)
                    ->exists();

                if ($alreadyUsed) {
                    return response()->json(['success' => false, 'message' => 'Kode promo ini sudah pernah kamu gunakan.']);
                }
            }
        }

        return response()->json([
            'success' => true,
            'promo'   => [
                'kode_promo'       => $promo->kode_promo,
                'nama_promo'       => $promo->nama_promo,
                'persenan_diskon'  => $promo->persenan_diskon,
            ],
        ]);
    }

    private function isOneTimePackage(int $idPackage): bool
    {
        $cheapest = Package::where('status_package', 'active')->orderBy('harga')->first();
        return $cheapest && $cheapest->id_package === $idPackage;
    }

    private function alreadyPurchased(int $idPelanggan, int $idPackage): bool
    {
        return PembelianPackage::where('id_pelanggan', $idPelanggan)
            ->where('id_package', $idPackage)
            ->where('status_pembelian', 'paid')
            ->exists();
    }
}
