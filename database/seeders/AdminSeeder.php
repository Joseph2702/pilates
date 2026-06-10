<?php

namespace Database\Seeders;

use App\Domain\Entity\Permission;
use App\Domain\Entity\Role;
use App\Domain\Entity\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['nama_permission' => 'dashboard.view', 'deskripsi' => 'Lihat halaman dashboard'],

            // Master Data — Packages
            ['nama_permission' => 'packages.view', 'deskripsi' => 'Lihat daftar package'],
            ['nama_permission' => 'packages.create', 'deskripsi' => 'Tambah package baru'],
            ['nama_permission' => 'packages.update', 'deskripsi' => 'Edit package'],
            ['nama_permission' => 'packages.delete', 'deskripsi' => 'Hapus package'],

            // Master Data — Kelas
            ['nama_permission' => 'kelas.view', 'deskripsi' => 'Lihat daftar kelas'],
            ['nama_permission' => 'kelas.create', 'deskripsi' => 'Tambah kelas baru'],
            ['nama_permission' => 'kelas.update', 'deskripsi' => 'Edit kelas'],
            ['nama_permission' => 'kelas.delete', 'deskripsi' => 'Hapus kelas'],

            // Master Data — Instruktur
            ['nama_permission' => 'instruktur.view', 'deskripsi' => 'Lihat daftar instruktur'],
            ['nama_permission' => 'instruktur.create', 'deskripsi' => 'Tambah instruktur baru'],
            ['nama_permission' => 'instruktur.update', 'deskripsi' => 'Edit instruktur'],
            ['nama_permission' => 'instruktur.delete', 'deskripsi' => 'Hapus instruktur'],

            // Master Data — Pelanggan
            ['nama_permission' => 'pelanggan.view', 'deskripsi' => 'Lihat daftar pelanggan'],
            ['nama_permission' => 'pelanggan.delete', 'deskripsi' => 'Hapus pelanggan'],

            // Master Data — Promo
            ['nama_permission' => 'promo.view', 'deskripsi' => 'Lihat daftar promo'],
            ['nama_permission' => 'promo.create', 'deskripsi' => 'Tambah promo baru'],
            ['nama_permission' => 'promo.update', 'deskripsi' => 'Edit promo'],
            ['nama_permission' => 'promo.delete', 'deskripsi' => 'Hapus promo'],

            // Operasional — Jadwal Kelas
            ['nama_permission' => 'jadwal_kelas.view', 'deskripsi' => 'Lihat jadwal kelas'],
            ['nama_permission' => 'jadwal_kelas.create', 'deskripsi' => 'Tambah jadwal kelas'],
            ['nama_permission' => 'jadwal_kelas.update', 'deskripsi' => 'Edit jadwal kelas'],
            ['nama_permission' => 'jadwal_kelas.delete', 'deskripsi' => 'Hapus jadwal kelas'],

            // Operasional — Bookings
            ['nama_permission' => 'bookings.view', 'deskripsi' => 'Lihat daftar booking'],

            // Operasional — Absensi
            ['nama_permission' => 'absensi.view', 'deskripsi' => 'Lihat daftar absensi'],
            ['nama_permission' => 'absensi.manage', 'deskripsi' => 'Input/update absensi peserta'],

            // Keuangan
            ['nama_permission' => 'transaksi.view', 'deskripsi' => 'Lihat data transaksi'],
            ['nama_permission' => 'pembelian_package.view', 'deskripsi' => 'Lihat pembelian package'],
            ['nama_permission' => 'kredit.view', 'deskripsi' => 'Lihat mutasi kredit'],

            // Konten — Artikel
            ['nama_permission' => 'artikel.view', 'deskripsi' => 'Lihat daftar artikel'],
            ['nama_permission' => 'artikel.create', 'deskripsi' => 'Tambah artikel baru'],
            ['nama_permission' => 'artikel.update', 'deskripsi' => 'Edit artikel'],
            ['nama_permission' => 'artikel.delete', 'deskripsi' => 'Hapus artikel'],

            // Akses — Users
            ['nama_permission' => 'users.view', 'deskripsi' => 'Lihat daftar user'],
            ['nama_permission' => 'users.create', 'deskripsi' => 'Tambah user baru'],
            ['nama_permission' => 'users.update', 'deskripsi' => 'Edit user'],
            ['nama_permission' => 'users.delete', 'deskripsi' => 'Hapus user'],

            // Akses — Roles & Permissions
            ['nama_permission' => 'roles.view', 'deskripsi' => 'Lihat daftar role'],
            ['nama_permission' => 'roles.create', 'deskripsi' => 'Tambah role baru'],
            ['nama_permission' => 'roles.update', 'deskripsi' => 'Edit role'],
            ['nama_permission' => 'roles.delete', 'deskripsi' => 'Hapus role'],

            // Activity Logs
            ['nama_permission' => 'activity_logs.view', 'deskripsi' => 'Lihat activity logs'],

            // Pelanggan Features — Profile
            ['nama_permission' => 'profile.view', 'deskripsi' => 'Lihat profil pribadi'],
            ['nama_permission' => 'profile.update', 'deskripsi' => 'Edit profil pribadi'],
            ['nama_permission' => 'profile.change_password', 'deskripsi' => 'Ganti password'],

            // Pelanggan Features — Booking
            ['nama_permission' => 'booking.create', 'deskripsi' => 'Booking kelas'],
            ['nama_permission' => 'booking.view', 'deskripsi' => 'Lihat jadwal booking'],
            ['nama_permission' => 'booking.cancel', 'deskripsi' => 'Batal booking'],

            // Pelanggan Features — Package
            ['nama_permission' => 'package.view', 'deskripsi' => 'Lihat daftar paket'],
            ['nama_permission' => 'package.purchase', 'deskripsi' => 'Beli paket'],

            // Pelanggan Features — Transaction
            ['nama_permission' => 'transaction.view', 'deskripsi' => 'Lihat transaksi pribadi'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(
                ['nama_permission' => $p['nama_permission']],
                ['deskripsi' => $p['deskripsi']]
            );
        }

        // Remove old permissions not in this list
        $validNames = array_column($permissions, 'nama_permission');
        Permission::whereNotIn('nama_permission', $validNames)->delete();

        // Admin Role — full access
        $adminRole = Role::firstOrCreate(
            ['nama_role' => 'admin'],
            ['is_active' => true]
        );
        $adminRole->permissions()->sync(Permission::pluck('id_permission')->toArray());

        // Instruktur Role — limited access
        $instrukturRole = Role::firstOrCreate(
            ['nama_role' => 'instruktur'],
            ['is_active' => true]
        );
        $instrukturPerms = Permission::whereIn('nama_permission', [
            'dashboard.view',
            'jadwal_kelas.view',
            'absensi.view',
            'absensi.manage',
            'bookings.view',
        ])->pluck('id_permission')->toArray();
        $instrukturRole->permissions()->sync($instrukturPerms);

        // Pelanggan Role — access to pelanggan features only
        $pelangganRole = Role::firstOrCreate(
            ['nama_role' => 'pelanggan'],
            ['is_active' => true]
        );
        $pelangganPerms = Permission::whereIn('nama_permission', [
            'profile.view',
            'profile.update',
            'profile.change_password',
            'booking.create',
            'booking.view',
            'booking.cancel',
            'package.view',
            'package.purchase',
            'transaction.view',
        ])->pluck('id_permission')->toArray();
        $pelangganRole->permissions()->sync($pelangganPerms);

        // Default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@pilates.com'],
            [
                'nama' => 'Admin Pilates',
                'password' => Hash::make('admin123'),
                'no_hp' => '081234567890',
                'status' => 'active',
            ]
        );
        $admin->roles()->syncWithoutDetaching([
            $adminRole->id_role => ['is_active' => true],
        ]);

        $this->command->info('Permissions, roles & admin user seeded successfully!');
    }
}
