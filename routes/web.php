<?php

use App\Http\Controllers\Web\Admin\AbsensiWebController;
use App\Http\Controllers\Web\Admin\ActivityLogWebController;
use App\Http\Controllers\Web\Admin\ArtikelWebController;
use App\Http\Controllers\Web\Admin\AuthWebController;
use App\Http\Controllers\Web\Admin\BookingWebController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\InstrukturWebController;
use App\Http\Controllers\Web\Admin\JadwalKelasWebController;
use App\Http\Controllers\Web\Admin\KelasWebController;
use App\Http\Controllers\Web\Admin\KreditWebController;
use App\Http\Controllers\Web\Admin\PackageWebController;
use App\Http\Controllers\Web\Admin\PelangganWebController;
use App\Http\Controllers\Web\Admin\PembelianPackageWebController;
use App\Http\Controllers\Web\Admin\PermissionWebController;
use App\Http\Controllers\Web\Admin\PromoWebController;
use App\Http\Controllers\Web\Admin\RoleWebController;
use App\Http\Controllers\Web\Admin\TransaksiWebController;
use App\Http\Controllers\Web\Admin\UserWebController;
use App\Http\Controllers\Web\Pelanggan\ArticlesWebController;
use App\Http\Controllers\Web\Pelanggan\AuthWebPelangganController;
use App\Http\Controllers\Web\Pelanggan\BookingWebController as PelangganBookingController;
use App\Http\Controllers\Web\Pelanggan\ClassesWebController;
use App\Http\Controllers\Web\Pelanggan\ContactController;
use App\Http\Controllers\Web\Pelanggan\HomeController;
use App\Http\Controllers\Web\Pelanggan\PackagesWebController;
use App\Http\Controllers\Web\Pelanggan\ProfileWebController;
use Illuminate\Support\Facades\Route;

// Redirect root to web homepage
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('block.admin');

// ─── Web (Pelanggan) Public Routes ────────────────────────────────────────────
Route::middleware('block.admin')->group(function () {
    Route::get('/classes', [ClassesWebController::class, 'index'])->name('classes.index');
    Route::get('/classes/{id}/schedule', [ClassesWebController::class, 'schedule'])->name('classes.schedule');

    Route::get('/packages', [PackagesWebController::class, 'index'])->name('packages.index');
    Route::get('/packages/{id}/checkout', [PackagesWebController::class, 'checkout'])->middleware('auth')->name('packages.checkout')->middleware('permission:package.view');
    Route::post('/packages/{id}/process', [PackagesWebController::class, 'process'])->middleware('auth')->name('packages.process')->middleware('permission:package.purchase');
    Route::post('/promo/check', [PackagesWebController::class, 'checkPromo'])->middleware('auth')->name('promo.check')->middleware('permission:package.view');

    Route::get('/articles', [ArticlesWebController::class, 'index'])->name('articles.index');
    Route::get('/articles/{id}', [ArticlesWebController::class, 'show'])->name('articles.show');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
});

// ─── Web Auth (Pelanggan) ──────────────────────────────────────────────────────
Route::middleware('block.admin')->group(function () {
    Route::get('/login', [AuthWebPelangganController::class, 'showLogin'])->name('web.login');
    Route::post('/login', [AuthWebPelangganController::class, 'login'])->name('web.login.post');
    Route::get('/register', [AuthWebPelangganController::class, 'showRegister'])->name('web.register');
    Route::post('/register', [AuthWebPelangganController::class, 'register'])->name('web.register.post');
});

Route::post('/logout', [AuthWebPelangganController::class, 'logout'])->name('web.logout');

// ─── Web (Pelanggan) Auth-Required Routes ─────────────────────────────────────
Route::middleware(['auth', 'role.pelanggan'])->group(function () {
    Route::get('/profile', [ProfileWebController::class, 'index'])->name('profile.index')->middleware('permission:profile.view');
    Route::get('/profile/edit', [ProfileWebController::class, 'edit'])->name('profile.edit')->middleware('permission:profile.update');
    Route::put('/profile', [ProfileWebController::class, 'update'])->name('profile.update')->middleware('permission:profile.update');
    Route::put('/profile/password', [ProfileWebController::class, 'updatePassword'])->name('profile.password')->middleware('permission:profile.change_password');
    Route::get('/profile/schedule', [ProfileWebController::class, 'schedule'])->name('profile.schedule')->middleware('permission:booking.view');
    Route::get('/profile/packages', [ProfileWebController::class, 'packages'])->name('profile.packages')->middleware('permission:package.view');
    Route::get('/profile/transactions', [ProfileWebController::class, 'transactions'])->name('profile.transactions')->middleware('permission:transaction.view');

    Route::post('/booking', [PelangganBookingController::class, 'store'])->name('booking.store')->middleware('permission:booking.create');
    Route::patch('/booking/{id}/cancel', [PelangganBookingController::class, 'cancel'])->name('booking.cancel')->middleware('permission:booking.cancel');
});

// ─── Admin Auth ────────────────────────────────────────────────────────────────
Route::get('/admin/login', [AuthWebController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthWebController::class, 'login']);
Route::post('/admin/logout', [AuthWebController::class, 'logout'])->name('admin.logout');

// Admin Panel (auth + admin role required)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    // Master Data — Packages
    Route::get('packages', [PackageWebController::class, 'index'])->name('packages.index')->middleware('permission:packages.view');
    Route::get('packages/create', [PackageWebController::class, 'create'])->name('packages.create')->middleware('permission:packages.create');
    Route::post('packages', [PackageWebController::class, 'store'])->name('packages.store')->middleware('permission:packages.create');
    Route::get('packages/{package}/edit', [PackageWebController::class, 'edit'])->name('packages.edit')->middleware('permission:packages.update');
    Route::put('packages/{package}', [PackageWebController::class, 'update'])->name('packages.update')->middleware('permission:packages.update');
    Route::delete('packages/{package}', [PackageWebController::class, 'destroy'])->name('packages.destroy')->middleware('permission:packages.delete');

    // Master Data — Kelas
    Route::get('kelas', [KelasWebController::class, 'index'])->name('kelas.index')->middleware('permission:kelas.view');
    Route::get('kelas/create', [KelasWebController::class, 'create'])->name('kelas.create')->middleware('permission:kelas.create');
    Route::post('kelas', [KelasWebController::class, 'store'])->name('kelas.store')->middleware('permission:kelas.create');
    Route::get('kelas/{kela}/edit', [KelasWebController::class, 'edit'])->name('kelas.edit')->middleware('permission:kelas.update');
    Route::put('kelas/{kela}', [KelasWebController::class, 'update'])->name('kelas.update')->middleware('permission:kelas.update');
    Route::delete('kelas/{kela}', [KelasWebController::class, 'destroy'])->name('kelas.destroy')->middleware('permission:kelas.delete');

    // Master Data — Instruktur
    Route::get('instruktur', [InstrukturWebController::class, 'index'])->name('instruktur.index')->middleware('permission:instruktur.view');
    Route::get('instruktur/create', [InstrukturWebController::class, 'create'])->name('instruktur.create')->middleware('permission:instruktur.create');
    Route::post('instruktur', [InstrukturWebController::class, 'store'])->name('instruktur.store')->middleware('permission:instruktur.create');
    Route::get('instruktur/{instruktur}/edit', [InstrukturWebController::class, 'edit'])->name('instruktur.edit')->middleware('permission:instruktur.update');
    Route::put('instruktur/{instruktur}', [InstrukturWebController::class, 'update'])->name('instruktur.update')->middleware('permission:instruktur.update');
    Route::delete('instruktur/{instruktur}', [InstrukturWebController::class, 'destroy'])->name('instruktur.destroy')->middleware('permission:instruktur.delete');

    // Master Data — Pelanggan
    Route::get('pelanggan', [PelangganWebController::class, 'index'])->name('pelanggan.index')->middleware('permission:pelanggan.view');
    Route::get('pelanggan/{id}', [PelangganWebController::class, 'show'])->name('pelanggan.show')->middleware('permission:pelanggan.view');
    Route::delete('pelanggan/{id}', [PelangganWebController::class, 'destroy'])->name('pelanggan.destroy')->middleware('permission:pelanggan.delete');

    // Master Data — Promo
    Route::get('promo', [PromoWebController::class, 'index'])->name('promo.index')->middleware('permission:promo.view');
    Route::get('promo/create', [PromoWebController::class, 'create'])->name('promo.create')->middleware('permission:promo.create');
    Route::post('promo', [PromoWebController::class, 'store'])->name('promo.store')->middleware('permission:promo.create');
    Route::get('promo/{promo}/edit', [PromoWebController::class, 'edit'])->name('promo.edit')->middleware('permission:promo.update');
    Route::put('promo/{promo}', [PromoWebController::class, 'update'])->name('promo.update')->middleware('permission:promo.update');
    Route::delete('promo/{promo}', [PromoWebController::class, 'destroy'])->name('promo.destroy')->middleware('permission:promo.delete');

    // Operasional — Jadwal Kelas
    Route::get('jadwal-kelas', [JadwalKelasWebController::class, 'index'])->name('jadwal-kelas.index')->middleware('permission:jadwal_kelas.view');
    Route::get('jadwal-kelas/create', [JadwalKelasWebController::class, 'create'])->name('jadwal-kelas.create')->middleware('permission:jadwal_kelas.create');
    Route::post('jadwal-kelas', [JadwalKelasWebController::class, 'store'])->name('jadwal-kelas.store')->middleware('permission:jadwal_kelas.create');
    Route::get('jadwal-kelas/{jadwal_kela}/edit', [JadwalKelasWebController::class, 'edit'])->name('jadwal-kelas.edit')->middleware('permission:jadwal_kelas.update');
    Route::put('jadwal-kelas/{jadwal_kela}', [JadwalKelasWebController::class, 'update'])->name('jadwal-kelas.update')->middleware('permission:jadwal_kelas.update');
    Route::delete('jadwal-kelas/{jadwal_kela}', [JadwalKelasWebController::class, 'destroy'])->name('jadwal-kelas.destroy')->middleware('permission:jadwal_kelas.delete');

    // Operasional — Bookings
    Route::get('bookings', [BookingWebController::class, 'index'])->name('bookings.index')->middleware('permission:bookings.view');
    Route::get('bookings/{id}', [BookingWebController::class, 'show'])->name('bookings.show')->middleware('permission:bookings.view');

    // Operasional — Absensi
    Route::get('absensi', [AbsensiWebController::class, 'index'])->name('absensi.index')->middleware('permission:absensi.view');
    Route::get('absensi/{id}', [AbsensiWebController::class, 'show'])->name('absensi.show')->middleware('permission:absensi.view');
    Route::post('absensi', [AbsensiWebController::class, 'store'])->name('absensi.store')->middleware('permission:absensi.manage');

    // Keuangan
    Route::get('transaksi', [TransaksiWebController::class, 'index'])->name('transaksi.index')->middleware('permission:transaksi.view');
    Route::get('transaksi/{id}', [TransaksiWebController::class, 'show'])->name('transaksi.show')->middleware('permission:transaksi.view');
    Route::get('pembelian-package', [PembelianPackageWebController::class, 'index'])->name('pembelian-package.index')->middleware('permission:pembelian_package.view');
    Route::get('pembelian-package/{id}', [PembelianPackageWebController::class, 'show'])->name('pembelian-package.show')->middleware('permission:pembelian_package.view');
    Route::get('kredit', [KreditWebController::class, 'index'])->name('kredit.index')->middleware('permission:kredit.view');

    // Konten — Artikel
    Route::get('artikel', [ArtikelWebController::class, 'index'])->name('artikel.index')->middleware('permission:artikel.view');
    Route::get('artikel/create', [ArtikelWebController::class, 'create'])->name('artikel.create')->middleware('permission:artikel.create');
    Route::post('artikel', [ArtikelWebController::class, 'store'])->name('artikel.store')->middleware('permission:artikel.create');
    Route::get('artikel/{artikel}/edit', [ArtikelWebController::class, 'edit'])->name('artikel.edit')->middleware('permission:artikel.update');
    Route::put('artikel/{artikel}', [ArtikelWebController::class, 'update'])->name('artikel.update')->middleware('permission:artikel.update');
    Route::delete('artikel/{artikel}', [ArtikelWebController::class, 'destroy'])->name('artikel.destroy')->middleware('permission:artikel.delete');

    // Akses — Users
    Route::get('users', [UserWebController::class, 'index'])->name('users.index')->middleware('permission:users.view');
    Route::get('users/create', [UserWebController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::post('users', [UserWebController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::get('users/{user}/edit', [UserWebController::class, 'edit'])->name('users.edit')->middleware('permission:users.update');
    Route::put('users/{user}', [UserWebController::class, 'update'])->name('users.update')->middleware('permission:users.update');
    Route::delete('users/{user}', [UserWebController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');

    // Akses — Roles
    Route::get('roles', [RoleWebController::class, 'index'])->name('roles.index')->middleware('permission:roles.view');
    Route::get('roles/create', [RoleWebController::class, 'create'])->name('roles.create')->middleware('permission:roles.create');
    Route::post('roles', [RoleWebController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::get('roles/{role}/edit', [RoleWebController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.update');
    Route::put('roles/{role}', [RoleWebController::class, 'update'])->name('roles.update')->middleware('permission:roles.update');
    Route::delete('roles/{role}', [RoleWebController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');

    // Akses — Permissions (tied to roles permission)
    Route::get('permissions', [PermissionWebController::class, 'index'])->name('permissions.index')->middleware('permission:roles.view');
    Route::get('permissions/create', [PermissionWebController::class, 'create'])->name('permissions.create')->middleware('permission:roles.create');
    Route::post('permissions', [PermissionWebController::class, 'store'])->name('permissions.store')->middleware('permission:roles.create');
    Route::get('permissions/{permission}/edit', [PermissionWebController::class, 'edit'])->name('permissions.edit')->middleware('permission:roles.update');
    Route::put('permissions/{permission}', [PermissionWebController::class, 'update'])->name('permissions.update')->middleware('permission:roles.update');
    Route::delete('permissions/{permission}', [PermissionWebController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:roles.delete');

    // Activity Logs
    Route::get('activity-logs', [ActivityLogWebController::class, 'index'])->name('activity-logs.index')->middleware('permission:activity_logs.view');
});
