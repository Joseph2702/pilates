# Femm Pilates - Project Flow & Architecture Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Role & Permission System](#role--permission-system)
4. [Database Schema](#database-schema)
5. [API Routes & Flows](#api-routes--flows)
6. [Key Features & Flows](#key-features--flows)
7. [Key Functions & Services](#key-functions--services)
8. [Authentication Flow](#authentication-flow)

---

## Project Overview

**Femm Pilates** adalah aplikasi manajemen pilates yang memungkinkan:
- **Admin**: Mengelola kelas, jadwal, instruktur, paket, promo, pengguna, dan laporan
- **Instruktur**: Melihat jadwal kelas dan absensi (akses terbatas)
- **Pelanggan**: Memesan kelas, melihat jadwal, membeli paket, melihat transaksi

**Tech Stack:**
- Laravel 13.4.0
- PostgreSQL
- Laravel Sanctum (Authentication)
- Blade Templating
- Tailwind CSS

---

## System Architecture

```
┌─────────────────────────────────────────┐
│         PUBLIC DOMAIN                   │
│  (/, /classes, /packages, /articles)   │
└──────────────┬──────────────────────────┘
               │
       ┌───────┴─────────┐
       │                 │
       ▼                 ▼
┌──────────────┐  ┌──────────────┐
│   ADMIN      │  │  PELANGGAN   │
│   PANEL      │  │  DASHBOARD   │
│  (/admin/*)  │  │ (/profile/*) │
└──────────────┘  └──────────────┘
       │                 │
       └────────┬────────┘
                ▼
        ┌──────────────────┐
        │   DATABASE       │
        │  (PostgreSQL)    │
        └──────────────────┘
```

---

## Role & Permission System

### 3 Roles dalam Sistem:

#### 1. **ADMIN** (Full Access to /admin/*)
Permissions (38 total):
- **Master Data**: packages, kelas, instruktur, pelanggan, promo
- **Operasional**: jadwal_kelas, bookings, absensi
- **Keuangan**: transaksi, pembelian_package, kredit
- **Konten**: artikel
- **Sistem**: users, roles, activity_logs, dashboard

**Can Create/Update/Delete/Manage**: semua resource CMS

#### 2. **INSTRUKTUR** (Limited Admin Access)
Permissions (10 total):
- dashboard.view, jadwal_kelas.view, bookings.view, absensi.view, kredit.view
- users (create, update, delete), roles.create, activity_logs.view

**Can**: Lihat jadwal kelas dan absensi, mengelola user terbatas

#### 3. **PELANGGAN** (Public Domain Access)
Permissions (9 total):
- profile.view, profile.update, profile.change_password
- booking.create, booking.view, booking.cancel
- package.view, package.purchase
- transaction.view

**Can**: Manage profil, booking kelas, membeli paket

### Permission Architecture:

```
User → many-to-many → UserRoles (pivot dengan is_active) 
                          ↓
                        Role
                          ↓
                  many-to-many → RolePermissions
                                      ↓
                                  Permission
```

**Key Files:**
- `app/Domain/Entity/User.php` - `hasPermission($permission)` method
- `app/Domain/Entity/Role.php` - Relationship dengan permissions
- `app/Http/Middleware/PermissionMiddleware.php` - Permission checking

---

## Database Schema

### Core Tables:

#### **users**
```sql
- id_user (PK)
- nama
- email (unique)
- password (hashed)
- no_hp
- jenis_kelamin
- tempat_lahir
- tanggal_lahir
- status (active/inactive)
- created_at, updated_at
```

#### **user_roles** (Pivot)
```sql
- id_user (FK)
- id_role (FK)
- is_active (boolean, default: true)
- created_at
```

#### **roles**
```sql
- id_role (PK)
- nama_role (admin, instruktur, pelanggan)
- deskripsi
- is_active
- created_at, updated_at
```

#### **permissions**
```sql
- id_permission (PK)
- nama_permission (e.g., 'kelas.create', 'booking.view')
- deskripsi
- domain (cms, pelanggan)
```

#### **role_permissions** (Pivot)
```sql
- id_role (FK)
- id_permission (FK)
```

#### **kelas** (Classes)
```sql
- id_kelas (PK)
- nama_kelas
- deskripsi
- kapasitas
- created_at, updated_at
```

#### **jadwal_kelas** (Class Schedules)
```sql
- id_jadwal_kelas (PK)
- id_kelas (FK → kelas)
- id_instruktur (FK → instruktur)
- tanggal_kelas
- jam_mulai, jam_selesai
- kuota_maksimal, kuota_terisi
- created_at, updated_at
```

#### **bookings**
```sql
- id_booking (PK)
- id_pelanggan (FK → pelanggan)
- id_jadwal_kelas (FK → jadwal_kelas)
- status_booking (booked, cancelled, attended, no_show)
- catatan
- created_at, updated_at
```

#### **packages** (Paket Kredit)
```sql
- id_package (PK)
- nama_package
- jumlah_kredit
- harga
- masa_berlaku (hari)
- status_package (active/inactive)
```

#### **pembelian_package**
```sql
- id_pembelian (PK)
- id_pelanggan (FK → pelanggan)
- id_package (FK → packages)
- kredit_earned
- sisa_kredit
- harga_akhir
- diskon
- tanggal_pembelian
- tanggal_kadaluarsa
- status_pembelian (pending, paid, cancelled)
```

#### **transaksi**
```sql
- id_transaksi (PK)
- id_pembelian (FK → pembelian_package)
- jumlah_pembayaran
- metode_pembayaran (midtrans, cash, etc)
- status_transaksi (pending, completed, failed)
- external_id (midtrans reference)
```

#### **kreditmutasi_ledger** (Credit Ledger)
```sql
- id_mutasi (PK)
- id_pelanggan (FK)
- tipe_mutasi (in, out)
- jumlah_mutasi
- deskripsi
- created_at
```

#### **activity_logs**
```sql
- id_activity (PK)
- id_user (FK)
- action (create, update, delete)
- resource_type (kelas, jadwal, dll)
- resource_id
- perubahan (JSON)
- tanggal_log
```

---

## API Routes & Flows

### 1. **Authentication Routes**

#### Login (Admin)
```
POST /admin/login
├─ Middleware: none
├─ Input: email, password
├─ Flow:
│  ├─ Validate credentials (Auth::attempt)
│  ├─ Check if user has 'admin' role
│  └─ Redirect to /admin/dashboard or back with error
└─ Files: AuthWebController.php
```

#### Login (Pelanggan)
```
POST /login
├─ Middleware: none
├─ Input: email, password
├─ Flow:
│  ├─ Validate credentials
│  ├─ Check if user has 'pelanggan' role
│  └─ Redirect to /profile or home
└─ Files: AuthWebPelangganController.php
```

#### Register (Pelanggan)
```
POST /register
├─ Middleware: none
├─ Input: nama, email, password, no_hp
├─ Flow:
│  ├─ Validate input
│  ├─ Hash password
│  ├─ Create user
│  ├─ Auto-assign 'pelanggan' role with permissions
│  └─ Redirect to login
└─ Files: AuthWebPelangganController.php
```

### 2. **Admin Routes** (/admin/*)

#### Dashboard
```
GET /admin
├─ Middleware: auth, role.admin, permission:dashboard.view
├─ Flow:
│  ├─ Get statistics (users, bookings, revenue)
│  ├─ Get recent bookings
│  └─ Render dashboard view
└─ File: DashboardController.php
```

#### Kelas Management
```
GET    /admin/kelas
POST   /admin/kelas
GET    /admin/kelas/create
GET    /admin/kelas/{id}/edit
PUT    /admin/kelas/{id}
DELETE /admin/kelas/{id}

├─ Middleware: auth, role.admin, permission:kelas.view/create/update/delete
├─ Flow:
│  ├─ Index: Fetch all kelas dengan pagination
│  ├─ Create: Show form
│  ├─ Store: Validate, insert ke DB, redirect dengan success message
│  ├─ Edit: Fetch kelas, render form dengan data
│  ├─ Update: Validate, update DB, log activity
│  └─ Delete: Soft/hard delete, log activity
├─ Controller: KelasWebController.php
├─ Model: Kelas.php
└─ View Permissions: Pass $permissions array ke view untuk conditional button rendering
```

#### Jadwal Kelas Management
```
GET    /admin/jadwal-kelas
POST   /admin/jadwal-kelas
GET    /admin/jadwal-kelas/create
GET    /admin/jadwal-kelas/{id}/edit
PUT    /admin/jadwal-kelas/{id}
DELETE /admin/jadwal-kelas/{id}

├─ Middleware: auth, role.admin, permission:jadwal_kelas.view/create/update/delete
├─ Flow:
│  ├─ Index: Fetch dengan relasi kelas & instruktur, order by tanggal_kelas DESC
│  ├─ Store: 
│  │  ├─ Validate kelas & instruktur exists
│  │  ├─ Create jadwal entry
│  │  └─ Log activity
│  ├─ Update: Update jadwal data, recalculate kuota jika perlu
│  └─ Delete: Check if bookings exist, prevent delete if ada
├─ Controller: JadwalKelasWebController.php
├─ Services: BookingService.php (untuk kuota management)
└─ Activity Log: Triggered on create/update/delete
```

#### Package Management
```
GET    /admin/packages
POST   /admin/packages
GET    /admin/packages/create
GET    /admin/packages/{id}/edit
PUT    /admin/packages/{id}
DELETE /admin/packages/{id}

├─ Middleware: auth, role.admin, permission:packages.view/create/update/delete
├─ Flow:
│  ├─ Index: List all packages dengan status
│  ├─ Store: Create paket, set status default 'active'
│  ├─ Update: Update harga, kredit, masa berlaku
│  └─ Delete: Prevent delete if ada pembelian terkait
└─ Controller: PackageWebController.php
```

#### Promo Management
```
GET    /admin/promo
POST   /admin/promo
GET    /admin/promo/create
GET    /admin/promo/{id}/edit
PUT    /admin/promo/{id}
DELETE /admin/promo/{id}

├─ Middleware: auth, role.admin, permission:promo.view/create/update/delete
├─ Flow:
│  ├─ Index: List promo dengan date range validation
│  ├─ Store: Set tanggal_mulai, tanggal_selesai, persenan_diskon
│  ├─ Update: Validate date ranges, prevent past promos update
│  └─ Delete: Prevent delete if promo active
└─ Controller: PromoWebController.php
```

#### User Management
```
GET    /admin/users
POST   /admin/users
GET    /admin/users/{id}/edit
PUT    /admin/users/{id}
DELETE /admin/users/{id}

├─ Middleware: auth, role.admin, permission:users.view/create/update/delete
├─ Flow:
│  ├─ Index: List users dengan role badges, searchable by nama/email
│  ├─ Create: Assign roles (admin, instruktur, pelanggan)
│  ├─ Update: 
│  │  ├─ Update user data
│  │  ├─ Sync user roles
│  │  └─ Log activity
│  └─ Delete: Soft delete atau hard delete
├─ Controller: UserWebController.php
└─ Seeder: AdminSeeder.php (create admin user on migration)
```

#### Role Management
```
GET    /admin/roles
POST   /admin/roles
GET    /admin/roles/{id}/edit
PUT    /admin/roles/{id}
DELETE /admin/roles/{id}

├─ Middleware: auth, role.admin, permission:roles.view/create/update/delete
├─ Flow:
│  ├─ Index: List roles dengan permission count
│  ├─ Create: Show form dengan permission checkboxes (organized by domain)
│  ├─ Store: Create role & sync permissions
│  ├─ Edit: 
│  │  ├─ Fetch role dengan permissions
│  │  ├─ Pass $selectedPermissions array ke view
│  │  └─ Show permission grid dengan checked/unchecked boxes
│  └─ Update:
│  │  ├─ Validate permissions exist
│  │  ├─ Sync permissions: syncPermissionsWithClearCache()
│  │  └─ Clear user permission cache
├─ Controller: RoleWebController.php
├─ View: admin/roles/index.blade.php, edit.blade.php
└─ Permission Grid: admin/roles/_permissions-grid.blade.php (50+ permissions organized by domain)
```

#### Booking Management (View Only)
```
GET /admin/bookings
GET /admin/bookings/{id}

├─ Middleware: auth, role.admin, permission:bookings.view
├─ Flow:
│  ├─ Index: List all bookings dengan status, customer, jadwal
│  ├─ Show: Detail booking dengan jadwal & customer info
│  └─ No edit/delete (managed by pelanggan)
└─ Controller: BookingWebController.php
```

#### Transaksi & Kredit (View Only)
```
GET /admin/transaksi
GET /admin/transaksi/{id}
GET /admin/pembelian-package
GET /admin/pembelian-package/{id}
GET /admin/kredit

├─ Middleware: auth, role.admin, permission:transaksi.view/pembelian_package.view/kredit.view
├─ Flow:
│  ├─ Transaksi: List semua transaksi dengan status pembayaran
│  ├─ Pembelian: List package purchases dengan expiry tracking
│  └─ Kredit: Show kredit ledger semua pelanggan
└─ Controllers: TransaksiWebController.php, PembelianPackageWebController.php, KreditWebController.php
```

#### Activity Logs
```
GET /admin/activity-logs

├─ Middleware: auth, role.admin, permission:activity_logs.view
├─ Flow:
│  ├─ Fetch all activity logs dengan user & resource info
│  ├─ Show perubahan dalam format readable
│  └─ Filter by date range
└─ Controller: ActivityLogWebController.php
```

### 3. **Pelanggan Routes** (/profile/*, /booking/*, /packages/*)

#### Profile Management
```
GET    /profile
GET    /profile/edit
PUT    /profile
PUT    /profile/password

├─ Middleware: auth, role.pelanggan
├─ Permissions checked:
│  ├─ profile.view (can view profile)
│  ├─ profile.update (can edit & update)
│  └─ profile.change_password (can update password)
├─ Flow:
│  ├─ Index: 
│  │  ├─ Fetch user data
│  │  ├─ Get remaining credits dari kreditmutasi_ledger
│  │  ├─ Get recent bookings
│  │  └─ Pass $permissions untuk conditional UI rendering
│  ├─ Edit:
│  │  ├─ Show form hanya jika hasPermission('profile.update')
│  │  └─ Show password change section jika hasPermission('profile.change_password')
│  ├─ Update Profile:
│  │  ├─ Validate nama, no_hp, jenis_kelamin, tanggal_lahir
│  │  ├─ Update user record
│  │  └─ Redirect dengan success message
│  └─ Update Password:
│  │  ├─ Verify current password match
│  │  ├─ Hash & update password
│  │  └─ Logout user untuk re-login
├─ Controller: ProfileWebController.php
├─ Service: CreditService.php (untuk getSaldo)
└─ View Conditional Rendering: 
   └─ Tombol/form hanya muncul jika user punya permission
```

#### Schedule/Booking View
```
GET /profile/schedule?status=all|confirmed|cancelled

├─ Middleware: auth, role.pelanggan, permission:booking.view
├─ Flow:
│  ├─ Fetch user's bookings dengan jadwal & kelas info
│  ├─ Filter by status dari query string
│  ├─ Check if class is past (untuk styling/actions)
│  ├─ Show "Book a Class" button hanya jika hasPermission('booking.create')
│  └─ Show "Cancel" button hanya jika booking.create permission & belum lewat
├─ Controller: ProfileWebController.php (schedule method)
└─ View: resources/views/web/profile/schedule.blade.php
```

#### Package Booking & Purchase
```
GET  /classes
GET  /classes/{id}/details
GET  /packages
GET  /packages/{id}/checkout
POST /packages/{id}/process

├─ Middleware: auth, role.pelanggan, permission:package.view/package.purchase
├─ Flow:
│  ├─ Classes List:
│  │  ├─ Show available kelas dengan jadwal
│  │  ├─ Show kuota available
│  │  └─ Button "Book Now" hanya jika booking.create permission
│  ├─ Package List:
│  │  ├─ Show packages dengan pricing & kredit
│  │  ├─ Filter by status active
│  │  └─ Button "Buy" hanya jika package.purchase permission
│  ├─ Checkout:
│  │  ├─ Fetch package & apply promo if any
│  │  ├─ Calculate final price & diskon
│  │  └─ Show Midtrans payment form
│  └─ Process:
│  │  ├─ Validate package exists
│  │  ├─ Initiate Midtrans transaction
│  │  ├─ Store transaksi with pending status
│  │  └─ Redirect to Midtrans payment page
├─ Controllers: ClassWebController.php, PackageWebController.php
└─ Service: MidtransService.php (payment processing)
```

#### Booking Management
```
POST   /booking
PATCH  /booking/{id}/cancel

├─ Middleware: auth, role.pelanggan, permission:booking.create/booking.cancel
├─ Flow:
│  ├─ Create Booking:
│  │  ├─ Validate jadwal_kelas exists & has kuota available
│  │  ├─ Check pelanggan has enough credits (if applicable)
│  │  ├─ Create booking record with status 'booked'
│  │  ├─ Increment kuota_terisi in jadwal_kelas
│  │  └─ Create activity log
│  └─ Cancel Booking:
│  │  ├─ Verify booking belongs to user
│  │  ├─ Verify jadwal hasn't started yet
│  │  ├─ Update status to 'cancelled'
│  │  ├─ Decrement kuota_terisi
│  │  ├─ Refund credits if deducted
│  │  └─ Log activity
├─ Controller: BookingController.php
├─ Service: BookingService.php
└─ Models: Booking.php, Jadwal_Kelas.php
```

#### Transaction View
```
GET /profile/transactions
GET /profile/packages

├─ Middleware: auth, role.pelanggan, permission:transaction.view/package.view
├─ Flow:
│  ├─ Transactions:
│  │  ├─ Fetch user's transaksi dengan payment method & status
│  │  ├─ Show pembayaran details & invoice
│  │  └─ Link to package details
│  └─ Packages:
│  │  ├─ Fetch user's pembelian_package
│  │  ├─ Show expired/active status dengan progress bar
│  │  └─ Show "Buy More" button jika package.purchase permission
├─ Controller: ProfileWebController.php
└─ View: resources/views/web/profile/transactions.blade.php, packages.blade.php
```

### 4. **Public Routes** (No Authentication)

```
GET /                       (Homepage)
GET /classes                (Browse classes)
GET /packages               (Browse packages)
GET /articles               (Read articles)
GET /article/{id}           (Article detail)
GET /contact                (Contact form)

├─ Middleware: block.admin (prevent admin from accessing)
├─ Flow:
│  ├─ Homepage: Show featured classes & packages
│  ├─ Classes: List all jadwal kelas with search & filter
│  ├─ Packages: Show active packages dengan pricing
│  ├─ Articles: List published articles
│  └─ Contact: Contact form (optional authentication)
└─ Controllers: PublicWebController.php
```

---

## Key Features & Flows

### 1. **Booking Flow**

```
Pelanggan Views Class Schedule
         ↓
  Clicks "Book Class"
         ↓
  System checks:
  ├─ Permission: booking.create ✓
  ├─ Kuota available ✓
  └─ Time hasn't passed ✓
         ↓
  Create Booking record
  - status: 'booked'
  - id_jadwal_kelas: selected schedule
         ↓
  Increment jadwal.kuota_terisi
         ↓
  Create ActivityLog
         ↓
  Redirect with success message
         ↓
  Show in profile/schedule
```

### 2. **Package Purchase Flow**

```
Pelanggan Views Packages
         ↓
  Clicks "Buy Package"
         ↓
  System checks:
  ├─ Permission: package.purchase ✓
  └─ Package is active ✓
         ↓
  Show checkout page with:
  ├─ Package details
  ├─ Applicable promos
  ├─ Final price after diskon
  └─ Midtrans payment form
         ↓
  User clicks "Pay"
         ↓
  Initiate Midtrans transaction
  - Store transaksi with:
    ├─ external_id: midtrans ref
    ├─ status: 'pending'
    └─ jumlah_pembayaran: price
         ↓
  Redirect to Midtrans payment page
         ↓
  [User completes payment]
         ↓
  Midtrans webhook notification
         ↓
  Update transaksi status to 'completed'
         ↓
  Create pembelian_package:
  ├─ kredit_earned: from package
  ├─ sisa_kredit: same as earned
  ├─ tanggal_kadaluarsa: now + masa_berlaku
  └─ status: 'paid'
         ↓
  Create kreditmutasi entry (tipe: 'in')
         ↓
  Show in profile/packages
```

### 3. **Permission Update Flow**

```
Admin edits Role permissions
         ↓
  Select/deselect permission checkboxes
         ↓
  Submit form
         ↓
  RoleWebController@update validates permissions
         ↓
  Call Role.syncPermissionsWithClearCache():
  ├─ Sync role_permissions table
  └─ Clear user permission cache
         ↓
  Affected users' cached permissions cleared
         ↓
  Next hasPermission() check queries fresh from DB
         ↓
  UI elements re-render on next page load
         ↓
  Log activity with role_id & permission changes
```

### 4. **Activity Logging Flow**

```
Admin creates/updates/deletes resource
         ↓
  Controller dispatches ActivityLog::create()
         ↓
  ActivityLog::creating() event triggered:
  ├─ Get authenticated user
  ├─ Store action type
  ├─ Store resource type & ID
  ├─ Store perubahan (JSON diff if update)
  ├─ Store tanggal_log
  └─ Store user_id
         ↓
  Logged to activity_logs table
         ↓
  Admin can view in /admin/activity-logs
```

### 5. **Credit System Flow**

```
Payment Received
         ↓
  Create pembelian_package:
  ├─ id_package
  ├─ kredit_earned: 10 (example)
  ├─ sisa_kredit: 10
  └─ tanggal_kadaluarsa
         ↓
  Create kreditmutasi entry:
  ├─ tipe_mutasi: 'in'
  ├─ jumlah_mutasi: 10
  └─ deskripsi: "Pembelian paket 10 kredit"
         ↓
  CreditService.getSaldo() calculates:
  ├─ SUM where tipe_mutasi = 'in' (inflow)
  ├─ MINUS SUM where tipe_mutasi = 'out' (outflow)
  └─ Returns final saldo
         ↓
  User books class
         ↓
  Create kreditmutasi entry:
  ├─ tipe_mutasi: 'out'
  ├─ jumlah_mutasi: 1 (1 credit per booking)
  └─ deskripsi: "Booking kelas: nama_kelas"
         ↓
  sisa_kredit updated in pembelian_package
         ↓
  Show available balance to user
```

---

## Key Functions & Services

### 1. **CreditService.php**

```php
// Calculate real-time balance from ledger
public function getSaldo($id_pelanggan): int
├─ Query kreditmutasi_ledger
├─ Sum inflow (tipe = 'in')
├─ Subtract outflow (tipe = 'out')
└─ Return balance

// Add credit entry
public function addCredit($id_pelanggan, $jumlah, $deskripsi): void
├─ Create kreditmutasi entry with tipe = 'in'
└─ Clear user cache

// Deduct credit entry
public function deductCredit($id_pelanggan, $jumlah, $deskripsi): void
├─ Create kreditmutasi entry with tipe = 'out'
└─ Clear user cache
```

### 2. **BookingService.php**

```php
// Create booking with automatic credit deduction
public function createBooking($id_pelanggan, $id_jadwal_kelas): Booking
├─ Validate jadwal exists & has kuota
├─ Check pelanggan exists
├─ Deduct 1 credit via CreditService
├─ Create Booking record
├─ Increment jadwal.kuota_terisi
├─ Create ActivityLog
└─ Return booking

// Cancel booking with credit refund
public function cancelBooking($id_booking): void
├─ Find booking
├─ Verify not past date
├─ Update booking status to 'cancelled'
├─ Decrement jadwal.kuota_terisi
├─ Refund 1 credit via CreditService
└─ Log activity
```

### 3. **User.php (Entity/Model)**

```php
// Check if user has permission (realtime check)
public function hasPermission($permission): bool
├─ Check user roles via user_roles pivot
├─ Check each role's permissions via role_permissions
├─ Return true if found, false otherwise

// Get all permissions for user
public function getPermissions(): array
├─ Query user_roles → roles → role_permissions
├─ Collect all permission names
└─ Return array

// Clear permission cache (when role permissions change)
public function clearPermissionCache(): void
├─ Delete from cache (if caching implemented)
└─ Used after role permission sync
```

### 4. **Role.php (Entity/Model)**

```php
// Sync permissions with user cache clear
public function syncPermissionsWithClearCache(array $permissionIds): void
├─ Sync role_permissions with pivot table
└─ Clear all user caches for this role's users

// Get permissions grouped by domain
public function getPermissionsByDomain(): Collection
├─ Eager load permissions
├─ Group by domain (cms, pelanggan)
└─ Return grouped collection
```

### 5. **PassPermissionsToView Trait**

```php
// Build standard CRUD permissions
protected function buildPermissions($resourceName): array
├─ Check: $resourceName.view
├─ Check: $resourceName.create
├─ Check: $resourceName.update
├─ Check: $resourceName.delete
├─ Check: $resourceName.manage
└─ Return permissions array

// Build custom permissions
protected function buildCustomPermissions(array $permissionList): array
├─ Iterate permission list
├─ Check each permission
├─ Create camelCase key for each
└─ Return permissions array
```

### 6. **PermissionMiddleware.php**

```php
// Middleware to check permissions
public function handle(Request $request, Closure $next, string ...$permissions): Response
├─ Get authenticated user
├─ For each required permission:
│  └─ If user.hasPermission($permission) → allow
├─ If no permission matches:
│  ├─ AJAX requests → return JSON 403
│  └─ Regular requests → render error view
└─ Return response
```

---

## Authentication Flow

### Admin Login:

```
User submits login form at /admin/login
         ↓
AuthWebController.login() validates:
├─ Email exists
└─ Password matches (via Hash::check)
         ↓
Check if user has 'admin' role:
├─ Query user_roles with nama_role = 'admin'
└─ Verify is_active = true
         ↓
If valid:
├─ Auth::loginUsingId() - Laravel session/token
├─ Redirect to /admin/dashboard
└─ Show success message
         ↓
If invalid:
├─ Redirect back to /admin/login
└─ Show error message
         ↓
Future requests checked by 'role.admin' middleware:
├─ Verify user is authenticated
├─ Verify has active admin role
└─ Proceed to route
```

### Pelanggan Registration:

```
New user submits registration form
         ↓
AuthWebPelangganController.register() validates:
├─ Email unique
├─ Password confirmed
└─ Required fields present
         ↓
Create User record:
├─ Hash password
├─ Set status = 'active'
└─ Save to DB
         ↓
Auto-assign 'pelanggan' role:
├─ Find pelanggan role by nama_role
├─ Attach via user_roles pivot:
│  ├─ is_active = true
│  └─ Created at timestamp
└─ Return to controller
         ↓
Sync pelanggan permissions:
├─ Fetch all pelanggan permissions
├─ Sync to role_permissions
└─ User now has profile.view, booking.create, etc.
         ↓
Redirect to login page
         ↓
User logs in (same as admin but checks 'pelanggan' role)
```

---

## Middleware Stack

### Admin Routes:
```
Route → auth → role.admin → block.admin → permission:xxx
```

### Pelanggan Routes:
```
Route → auth → role.pelanggan → permission:xxx
```

### Public Routes:
```
Route → block.admin (redirect admin to /admin/dashboard)
```

### Global Middleware:
- `block.admin`: Checks if admin accessing public domain, redirects to admin dashboard
- `role.admin`: Ensures only admin role can access /admin/*
- `role.pelanggan`: Ensures pelanggan can't access admin routes
- `permission:XXX`: Checks specific permission on resource, denies access if missing

---

## Database Triggers & Events

### Model Events:

#### User Model:
- `creating()`: Auto-set status if not provided
- `updated()`: Log activity if sensitive fields changed

#### Booking Model:
- `created()`: Increment jadwal.kuota_terisi, create activity log
- `updated()`: Log status changes

#### ActivityLog Model:
- `creating()`: Auto-capture current user & timestamp

#### Role Model:
- `updated()`: When permissions synced, clear user caches

---

## Error Handling

### Permission Denied (403):
- **Admin routes**: Redirect to /admin/dashboard with error message
- **Pelanggan routes**: Render errors/no-access.blade.php view
- **AJAX requests**: Return JSON 403 response

### Validation Errors:
- Redirect back with error messages
- Show validation errors in views
- Log activity only on success

---

## Summary Table

| Feature | Route | Middleware | Permission | Service |
|---------|-------|-----------|-----------|---------|
| Kelas CRUD | /admin/kelas/* | auth, role.admin | kelas.view/create/update/delete | - |
| Jadwal CRUD | /admin/jadwal-kelas/* | auth, role.admin | jadwal_kelas.view/create/update/delete | BookingService |
| Booking | /booking | auth, role.pelanggan | booking.create | BookingService |
| Cancel Booking | /booking/{id}/cancel | auth, role.pelanggan | booking.cancel | BookingService |
| Package Purchase | /packages/{id}/process | auth, role.pelanggan | package.purchase | MidtransService |
| Profile View | /profile | auth, role.pelanggan | profile.view | CreditService |
| Role Management | /admin/roles/* | auth, role.admin | roles.view/create/update/delete | - |
| Activity Logs | /admin/activity-logs | auth, role.admin | activity_logs.view | - |

---

**Last Updated**: April 2026
**Version**: 1.0
**Status**: Documentation Complete
