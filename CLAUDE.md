# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack
- **Laravel 13** on **PHP 8.3**
- **PostgreSQL** (primary DB). A `database/database.sqlite` exists from Laravel scaffolding but is not the target DB — use Postgres.
- **Midtrans** (`midtrans/midtrans-php` ^2.6) as payment gateway (Snap).
- Frontend tooling: Vite (`npm run dev` / `npm run build`).
- Tests: PHPUnit 12 via `php artisan test`.
- Lint/format: Laravel Pint.



## Commands

```bash
# First-time setup (installs deps, copies .env, generates key, migrates, builds assets)
composer setup

# Full dev loop — runs `artisan serve`, queue worker, pail log tailer, and vite concurrently
composer dev

# Tests (clears config first, then runs artisan test)
composer test

# Single test file / filter
php artisan test --filter=SomeTestName
php artisan test tests/Feature/AuthTest.php

# Lint / format
./vendor/bin/pint                 # fix
./vendor/bin/pint --test          # check only

# Migrations / DB
php artisan migrate
php artisan migrate:fresh --seed
php artisan tinker
```

## Environment
- Postgres runs on **localhost:5432**, user `linkit`, no password (managed via DBeaver locally).
- `.env` already exists; `composer setup` copies from `.env.example` if missing.

## 🧠 Architecture Overview

Project ini menggunakan **Layered Architecture** dengan semua layer berada di bawah `app/`. Struktur aktual:

```
app/
├── Http/                          # Presentation + Service + Repository + Infrastructure
│   ├── Controllers/               # Thin controllers (validate → call service → ApiResponse)
│   │   ├── AuthController.php
│   │   ├── BookingController.php
│   │   ├── PackageController.php
│   │   ├── PaymentController.php
│   │   └── WebhookController.php
│   ├── Service/                   # Business logic utama
│   │   ├── AuthService.php
│   │   ├── BookingService.php
│   │   ├── CreditService.php
│   │   ├── PackageService.php
│   │   └── PaymentService.php
│   ├── Repository/                # Akses database (Eloquent)
│   │   ├── BookingRepository.php
│   │   ├── JadwalKelasRepository.php
│   │   ├── MutasiKreditRepository.php
│   │   ├── PackageRepository.php
│   │   ├── PaymentLogRepository.php
│   │   ├── PembelianPackageRepository.php
│   │   ├── TransaksiRepository.php
│   │   └── UserRepository.php
│   ├── Midtrans/                  # Midtrans SDK wrapper (infrastructure)
│   │   ├── MidtransClient.php
│   │   └── MidtransConfig.php
│   └── Config/                    # App config markers (infrastructure)
│       ├── DatabaseConfig.php
│       └── SecurityConfig.php
├── Domain/                        # Model bisnis murni (tidak depend ke Http)
│   ├── Entity/                    # 17 Eloquent models (custom PK: id_xxx)
│   └── Enums/                     # PaymentStatus, BookingStatus, RoleType
├── Common/                        # Shared utilities
│   ├── Response/ApiResponse.php   # JSON envelope (success, error, validationError, dll)
│   └── Exception/BusinessException.php
└── Providers/
```

### Layer responsibilities

| Layer | Namespace | Tanggung jawab |
|-------|-----------|----------------|
| Presentation | `App\Http\Controllers` | HTTP request/response, validasi input, mapping DTO |
| Service | `App\Http\Service` | Business logic, orkestrasi repository & external service |
| Repository | `App\Http\Repository` | Query & persistence logic via Eloquent |
| Domain | `App\Domain\Entity`, `App\Domain\Enums` | Representasi model bisnis, Entity & Enum |
| Infrastructure | `App\Http\Midtrans`, `App\Http\Config` | Integrasi Midtrans, konfigurasi aplikasi |
| Common | `App\Common` | Exception handling global, standard response wrapper |

## 🔄 Flow Singkat

```
Controller → Service → Repository → Database
                ↓
         Midtrans (Http/Midtrans/)
```

### Layering conventions
- **Business logic belongs in a Service layer**, not in controllers. Controllers should be thin: validate request → call service → return `ApiResponse`.
- All API responses use `App\Common\Response\ApiResponse` — reuse this wrapper, don't roll per-controller formats.
- **All API endpoints require authentication** except the `/auth/*` group (login, register, refresh, etc.) and `/webhook/midtrans`.
- Domain entities live in `app/Domain/Entity/` (bukan `app/Models/`). Auth config references `App\Domain\Entity\User`.

---

## 👥 Aktor & Use Case

Sistem bernama **Sistem Informasi Layanan Femm Pilates**. Ada 3 aktor utama:

### Admin
Akses penuh ke semua fitur setelah login:
- **Kelola Master Data**: data pelanggan, instruktur, package, kelas, promo, transaksi
- **Kelola Manajemen Akses Akun**: roles & users (RBAC)
- **Monitoring Riwayat**: riwayat jadwal kelas, transaksi, booking, pembelian package, absensi
- **Kelola Laporan**: laporan transaksi, laporan pembelian package, laporan booking
- **Kelola Jadwal Kelas**: penjadwalan kelas + pembatalan kelas + lihat jadwal + absensi
- **Kelola Artikel**: CRUD artikel/konten
- *Note: semua aksi Admin harus melalui proses login*

### Instruktur / Coach
Akses terbatas setelah login:
- **Lihat Jadwal Kelas** → extend: **Absensi** (instruktur input kehadiran peserta)
- **Monitoring Riwayat** → extend: riwayat jadwal kelas yang diajar, riwayat absensi

### Pelanggan
Dapat registrasi akun lalu login:
- **Registrasi Akun** → include: Login
- **Monitoring Riwayat** → extend: riwayat pembelian package, riwayat booking
- **Lihat Jadwal Kelas**
- **Lihat Artikel**
- **Pembelian Package** → include: **Booking Kelas** → extend: **Pembatalan Kelas**

---

## 🔄 Business Flow Utama

### 1. Alur Pemesanan (As-Is → To-Be)

**As-Is (manual, sebelum sistem):**
1. Pelanggan hubungi admin via WhatsApp
2. Admin kirim Google Form / info kelas
3. Pelanggan isi form & lakukan payment manual
4. Pelanggan kirim bukti pembayaran
5. Admin verifikasi → jika gagal: transaksi gagal
6. Pelanggan booking kelas
7. Admin cek ketersediaan slot → jika penuh: informasikan ke pelanggan
8. Jika slot tersedia: admin catat booking di Google Sheet
9. Admin konfirmasi booking ke pelanggan
10. Pelanggan datang ke kelas → admin catat kehadiran manual

**To-Be (via sistem ini):**
- Seluruh alur di atas diotomasi via web app. Booking, pembayaran, verifikasi, dan absensi dilakukan in-system.

### 2. Alur Pembelian Package & Payment (Midtrans Snap)
Pelanggan pilih Package
→ [opsional] input kode promo
→ pembelian_package dibuat (status: pending)
→ transaksi dibuat: order_id unik + snap_token dari Midtrans
→ Pelanggan bayar via Midtrans Snap
→ Midtrans kirim webhook → WebhookController
→ raw payload disimpan ke payment_logs
→ transaksi & pembelian_package diupdate (status_internal)
→ jika sukses: mutasi_kredit (jenis: credit, sumber: pembelian_package)
sisa_kredit di pembelian_package ter-top-up

### 3. Alur Booking Kelas
Pelanggan lihat jadwal_kelas
→ pilih jadwal (cek kuota: kuota_terisi < kuota_maksimal)
→ sistem cek sisa_kredit pelanggan (dari pembelian_package aktif)
→ jika kredit cukup:
→ booking dibuat (status: confirmed)
→ kuota_terisi++ (dalam DB transaction + row lock)
→ mutasi_kredit (jenis: debit, sumber: booking)
→ jika kredit tidak cukup / kuota penuh: return error dengan pesan jelas

### 4. Alur Pembatalan Kelas
Pelanggan / Admin batalkan booking
→ status_booking → cancelled
→ kuota_terisi-- pada jadwal_kelas
→ mutasi_kredit refund (jenis: credit, sumber: pembatalan_booking)

### 5. Alur Absensi
Instruktur login → lihat jadwal kelas yang diajar
→ input kehadiran peserta per jadwal (hadir / tidak hadir)
→ absensi row dibuat/diupdate per id_booking
Admin juga bisa monitor riwayat absensi


### Domain concepts (important for any feature work)
- **Credit-based booking**: customers buy a `package` → a `pembelian_package` row is created with `kredit_earned` and `sisa_kredit`. Booking a `jadwal_kelas` consumes credit and writes a `mutasi_kredit` ledger entry (`jenis_mutasi` = debit/credit, `sumber_mutasi` identifies origin). Always mutate credit through `mutasi_kredit` — do not silently decrement `sisa_kredit` without a ledger row.
- **Payment flow (Midtrans Snap)**:
  1. `pembelian_package` created as pending.
  2. `transaksi` row written with internal `order_id` + Midtrans `snap_token`; `status_internal` tracks our view, `transaction_status` / `fraud_status` mirror Midtrans.
  3. Midtrans webhook hits `WebhookController` → raw payload dumped to `payment_logs` → `transaksi` + `pembelian_package` updated → on success, `mutasi_kredit` credit entry created to grant `kredit_earned` to the `pelanggan`.
  4. `order_id` is the join key between our side and Midtrans — it's indexed (`idx_transaksi_order_id`) and must be unique.
- **Users vs Pelanggan vs Instruktur**: `users` is the auth/account table. `pelanggan` and `instruktur` are 1:1 profile rows keyed by `id_user`. Roles are many-to-many via `user_roles` (+ `role_permissions` for RBAC). When creating a customer, insert into both `users` and `pelanggan`.
- **Schedule capacity**: `jadwal_kelas.kuota_maksimal` / `kuota_terisi` must be kept in sync with `booking` rows — guard against race conditions (DB transaction + row lock) when booking.
- **Naming**: schema, columns, and domain terms are in **Indonesian** (`pelanggan`, `jadwal_kelas`, `mutasi_kredit`, etc.). Keep that convention in models, migrations, and API payloads — don't translate to English.

## Database schema (authoritative)

PostgreSQL. This is the source of truth for the domain — migrations should match it. Custom PK names (`id_user`, `id_pelanggan`, …) mean Eloquent models need `protected $primaryKey = 'id_xxx';` and often `public $timestamps = false;` on tables without `updated_at`.

```sql
-- =========================
-- LEVEL 1 (MASTER)
-- =========================
CREATE TABLE users (
  id_user SERIAL PRIMARY KEY,
  nama VARCHAR(100),
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  no_hp VARCHAR(20),
  jenis_kelamin VARCHAR(10),
  tempat_lahir VARCHAR(100),
  tanggal_lahir DATE,
  foto_profile TEXT,
  status VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
  id_role SERIAL PRIMARY KEY,
  nama_role VARCHAR(50) UNIQUE NOT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE permissions (
  id_permission SERIAL PRIMARY KEY,
  nama_permission VARCHAR(100) UNIQUE NOT NULL,
  deskripsi TEXT
);

CREATE TABLE package (
  id_package SERIAL PRIMARY KEY,
  nama_package VARCHAR(100),
  jumlah_kredit INT,
  harga DECIMAL(12,2),
  masa_berlaku INT,
  status_package VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE promo (
  id_promo SERIAL PRIMARY KEY,
  kode_promo VARCHAR(50),
  nama_promo VARCHAR(100),
  persenan_diskon DECIMAL(5,2),
  tanggal_mulai TIMESTAMP,
  tanggal_selesai TIMESTAMP,
  status_promo VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE kelas (
  id_kelas SERIAL PRIMARY KEY,
  nama_kelas VARCHAR(100),
  deskripsi TEXT,
  kapasitas INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- LEVEL 2
-- =========================
CREATE TABLE instruktur (
  id_instruktur SERIAL PRIMARY KEY,
  id_user INT REFERENCES users(id_user) ON DELETE CASCADE,
  spesialisasi VARCHAR(50)
);

CREATE TABLE pelanggan (
  id_pelanggan SERIAL PRIMARY KEY,
  id_user INT REFERENCES users(id_user) ON DELETE CASCADE,
  tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_roles (
  id_user INT REFERENCES users(id_user) ON DELETE CASCADE,
  id_role INT REFERENCES roles(id_role) ON DELETE CASCADE,
  is_active BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (id_user, id_role)
);

CREATE TABLE role_permissions (
  id_role INT REFERENCES roles(id_role) ON DELETE CASCADE,
  id_permission INT REFERENCES permissions(id_permission) ON DELETE CASCADE,
  PRIMARY KEY (id_role, id_permission)
);

CREATE TABLE artikel (
  id_artikel SERIAL PRIMARY KEY,
  id_user INT REFERENCES users(id_user),
  judul_artikel VARCHAR(255),
  gambar_artikel TEXT,
  konten_artikel TEXT,
  tanggal_publish TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE activity_log (
  id_log SERIAL PRIMARY KEY,
  id_user INT REFERENCES users(id_user),
  modul VARCHAR(100),
  keterangan TEXT,
  aktivitas VARCHAR(100),
  tanggal_log TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- LEVEL 3
-- =========================
CREATE TABLE jadwal_kelas (
  id_jadwal_kelas SERIAL PRIMARY KEY,
  id_kelas INT REFERENCES kelas(id_kelas),
  id_instruktur INT REFERENCES instruktur(id_instruktur),
  tanggal_kelas TIMESTAMP,
  jam_mulai TIMESTAMP,
  jam_selesai TIMESTAMP,
  kuota_maksimal INT,
  kuota_terisi INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pembelian_package (
  id_pembelian_package SERIAL PRIMARY KEY,
  id_pelanggan INT REFERENCES pelanggan(id_pelanggan),
  id_package INT REFERENCES package(id_package),
  id_promo INT REFERENCES promo(id_promo),
  harga_awal DECIMAL(12,2),
  diskon DECIMAL(12,2),
  harga_akhir DECIMAL(12,2),
  status_pembelian VARCHAR(20),
  kredit_earned INT,
  sisa_kredit INT,
  tanggal_pembelian TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  tanggal_kadaluarsa TIMESTAMP
);

-- =========================
-- LEVEL 4
-- =========================
CREATE TABLE booking (
  id_booking SERIAL PRIMARY KEY,
  id_pelanggan INT REFERENCES pelanggan(id_pelanggan),
  id_jadwal_kelas INT REFERENCES jadwal_kelas(id_jadwal_kelas),
  status_booking VARCHAR(20),
  tanggal_booking TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transaksi (
  id_transaksi SERIAL PRIMARY KEY,
  order_id VARCHAR(100) UNIQUE NOT NULL,
  id_pembelian_package INT REFERENCES pembelian_package(id_pembelian_package),
  jumlah_bayar DECIMAL(12,2),
  midtrans_order_id VARCHAR(100),
  snap_token TEXT,
  transaction_status VARCHAR(50),
  fraud_status VARCHAR(50),
  payment_type VARCHAR(50),
  payment_response JSONB,
  status_internal VARCHAR(50),
  expired_at TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE mutasi_kredit (
  id_mutasi SERIAL PRIMARY KEY,
  id_pelanggan INT REFERENCES pelanggan(id_pelanggan),
  jenis_mutasi VARCHAR(20),
  jumlah_kredit INT,
  sumber_mutasi VARCHAR(50),
  id_referensi INT,
  keterangan TEXT,
  tanggal_mutasi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- LEVEL 5
-- =========================
CREATE TABLE absensi (
  id_absensi SERIAL PRIMARY KEY,
  id_booking INT REFERENCES booking(id_booking),
  status_kehadiran VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE payment_logs (
  id SERIAL PRIMARY KEY,
  order_id VARCHAR(100),
  raw_response JSONB,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_transaksi_order_id ON transaksi(order_id);
```
