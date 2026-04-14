<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\AbsensiAdminController;
use App\Http\Controllers\Admin\ActivityLogAdminController;
use App\Http\Controllers\Admin\ArtikelAdminController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\Admin\InstrukturAdminController;
use App\Http\Controllers\Admin\JadwalKelasAdminController;
use App\Http\Controllers\Admin\KelasAdminController;
use App\Http\Controllers\Admin\PackageAdminController;
use App\Http\Controllers\Admin\PelangganAdminController;
use App\Http\Controllers\Admin\PembelianPackageAdminController;
use App\Http\Controllers\Admin\PermissionAdminController;
use App\Http\Controllers\Admin\PromoAdminController;
use App\Http\Controllers\Admin\RoleAdminController;
use App\Http\Controllers\Admin\TransaksiAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Instruktur\AbsensiInstrukturController;
use App\Http\Controllers\Instruktur\JadwalInstrukturController;
use App\Http\Controllers\Pelanggan\ArtikelPelangganController;
use App\Http\Controllers\Pelanggan\BookingPelangganController;
use App\Http\Controllers\Pelanggan\JadwalPelangganController;
use App\Http\Controllers\Pelanggan\KreditPelangganController;
use App\Http\Controllers\Pelanggan\PembelianPelangganController;
use App\Http\Controllers\Pelanggan\TransaksiPelangganController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Public  : /auth/register, /auth/login, /webhook/midtrans
| Auth    : semua endpoint lain memerlukan Bearer token (auth:sanctum)
| RBAC    : admin/instruktur/pelanggan prefix dilindungi role middleware
*/

// =============================================
// PUBLIC (no auth required)
// =============================================
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

Route::post('webhook/midtrans', [WebhookController::class, 'midtrans']);

// =============================================
// AUTHENTICATED (any role)
// =============================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth self ---
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // --- Public-read (authenticated) ---
    Route::get('packages',      [PackageController::class, 'index']);
    Route::get('packages/{id}', [PackageController::class, 'show']);

    // =============================================
    // ADMIN
    // =============================================
    Route::prefix('admin')->middleware('role:admin')->group(function () {

        // Master: Package
        Route::get('packages',          [PackageAdminController::class, 'index']);
        Route::post('packages',         [PackageAdminController::class, 'store']);
        Route::get('packages/{id}',     [PackageAdminController::class, 'show']);
        Route::put('packages/{id}',     [PackageAdminController::class, 'update']);
        Route::delete('packages/{id}',  [PackageAdminController::class, 'destroy']);

        // Master: Kelas
        Route::get('kelas',         [KelasAdminController::class, 'index']);
        Route::post('kelas',        [KelasAdminController::class, 'store']);
        Route::get('kelas/{id}',    [KelasAdminController::class, 'show']);
        Route::put('kelas/{id}',    [KelasAdminController::class, 'update']);
        Route::delete('kelas/{id}', [KelasAdminController::class, 'destroy']);

        // Master: Instruktur
        Route::get('instruktur',         [InstrukturAdminController::class, 'index']);
        Route::post('instruktur',        [InstrukturAdminController::class, 'store']);
        Route::get('instruktur/{id}',    [InstrukturAdminController::class, 'show']);
        Route::put('instruktur/{id}',    [InstrukturAdminController::class, 'update']);
        Route::delete('instruktur/{id}', [InstrukturAdminController::class, 'destroy']);

        // Master: Pelanggan
        Route::get('pelanggan',         [PelangganAdminController::class, 'index']);
        Route::get('pelanggan/{id}',    [PelangganAdminController::class, 'show']);
        Route::put('pelanggan/{id}',    [PelangganAdminController::class, 'update']);
        Route::delete('pelanggan/{id}', [PelangganAdminController::class, 'destroy']);

        // Master: Promo
        Route::get('promo',         [PromoAdminController::class, 'index']);
        Route::post('promo',        [PromoAdminController::class, 'store']);
        Route::get('promo/{id}',    [PromoAdminController::class, 'show']);
        Route::put('promo/{id}',    [PromoAdminController::class, 'update']);
        Route::delete('promo/{id}', [PromoAdminController::class, 'destroy']);

        // Jadwal Kelas
        Route::get('jadwal-kelas',         [JadwalKelasAdminController::class, 'index']);
        Route::post('jadwal-kelas',        [JadwalKelasAdminController::class, 'store']);
        Route::get('jadwal-kelas/{id}',    [JadwalKelasAdminController::class, 'show']);
        Route::put('jadwal-kelas/{id}',    [JadwalKelasAdminController::class, 'update']);
        Route::delete('jadwal-kelas/{id}', [JadwalKelasAdminController::class, 'destroy']);

        // Artikel
        Route::get('artikel',         [ArtikelAdminController::class, 'index']);
        Route::post('artikel',        [ArtikelAdminController::class, 'store']);
        Route::get('artikel/{id}',    [ArtikelAdminController::class, 'show']);
        Route::put('artikel/{id}',    [ArtikelAdminController::class, 'update']);
        Route::delete('artikel/{id}', [ArtikelAdminController::class, 'destroy']);

        // Manajemen Akses: Roles
        Route::get('roles',                      [RoleAdminController::class, 'index']);
        Route::post('roles',                     [RoleAdminController::class, 'store']);
        Route::get('roles/{id}',                 [RoleAdminController::class, 'show']);
        Route::put('roles/{id}',                 [RoleAdminController::class, 'update']);
        Route::delete('roles/{id}',              [RoleAdminController::class, 'destroy']);
        Route::put('roles/{id}/permissions',     [RoleAdminController::class, 'syncPermissions']);

        // Manajemen Akses: Permissions
        Route::get('permissions',         [PermissionAdminController::class, 'index']);
        Route::post('permissions',        [PermissionAdminController::class, 'store']);
        Route::get('permissions/{id}',    [PermissionAdminController::class, 'show']);
        Route::put('permissions/{id}',    [PermissionAdminController::class, 'update']);
        Route::delete('permissions/{id}', [PermissionAdminController::class, 'destroy']);

        // Manajemen Akses: Users
        Route::get('users',                  [UserAdminController::class, 'index']);
        Route::get('users/{id}',             [UserAdminController::class, 'show']);
        Route::put('users/{id}',             [UserAdminController::class, 'update']);
        Route::post('users/{id}/deactivate', [UserAdminController::class, 'deactivate']);
        Route::put('users/{id}/roles',       [UserAdminController::class, 'syncRoles']);

        // Monitoring: Transaksi
        Route::get('transaksi',      [TransaksiAdminController::class, 'index']);
        Route::get('transaksi/{id}', [TransaksiAdminController::class, 'show']);

        // Monitoring: Pembelian Package
        Route::get('pembelian-package',      [PembelianPackageAdminController::class, 'index']);
        Route::get('pembelian-package/{id}', [PembelianPackageAdminController::class, 'show']);

        // Monitoring: Booking
        Route::get('bookings',      [BookingAdminController::class, 'index']);
        Route::get('bookings/{id}', [BookingAdminController::class, 'show']);

        // Absensi
        Route::get('absensi/jadwal/{idJadwalKelas}', [AbsensiAdminController::class, 'listByJadwal']);
        Route::post('absensi',                        [AbsensiAdminController::class, 'store']);

        // Activity Log
        Route::get('activity-logs',           [ActivityLogAdminController::class, 'index']);
        Route::get('activity-logs/user/{id}', [ActivityLogAdminController::class, 'byUser']);
    });

    // =============================================
    // INSTRUKTUR
    // =============================================
    Route::prefix('instruktur')->middleware('role:instruktur')->group(function () {

        // Jadwal yang diajar
        Route::get('jadwal',      [JadwalInstrukturController::class, 'index']);
        Route::get('jadwal/{id}', [JadwalInstrukturController::class, 'show']);

        // Absensi
        Route::get('absensi/jadwal/{idJadwalKelas}', [AbsensiInstrukturController::class, 'listByJadwal']);
        Route::post('absensi',                        [AbsensiInstrukturController::class, 'store']);
    });

    // =============================================
    // PELANGGAN
    // =============================================
    Route::prefix('pelanggan')->middleware('role:pelanggan')->group(function () {

        // Jadwal Kelas (lihat)
        Route::get('jadwal-kelas',      [JadwalPelangganController::class, 'index']);
        Route::get('jadwal-kelas/{id}', [JadwalPelangganController::class, 'show']);

        // Artikel (lihat)
        Route::get('artikel',      [ArtikelPelangganController::class, 'index']);
        Route::get('artikel/{id}', [ArtikelPelangganController::class, 'show']);

        // Booking
        Route::get('bookings',              [BookingPelangganController::class, 'index']);
        Route::get('bookings/{id}',         [BookingPelangganController::class, 'show']);
        Route::post('bookings',             [BookingController::class, 'store']);
        Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel']);

        // Pembelian Package
        Route::get('pembelian',      [PembelianPelangganController::class, 'index']);
        Route::get('pembelian/{id}', [PembelianPelangganController::class, 'show']);

        // Transaksi
        Route::get('transaksi', [TransaksiPelangganController::class, 'index']);

        // Kredit
        Route::get('kredit/saldo',   [KreditPelangganController::class, 'saldo']);
        Route::get('kredit/history', [KreditPelangganController::class, 'history']);

        // Checkout (payment)
        Route::post('payments/checkout', [PaymentController::class, 'checkout']);
    });
});
