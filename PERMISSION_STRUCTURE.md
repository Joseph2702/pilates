# Permission Structure - CMS vs Pelanggan Domain

## Overview

Permission system di project ini **terdiri dari 2 
domain terpisah**:

### **Domain 1: CMS (Admin)**
- **Scope**: `/admin/*` routes saja
- **Purpose**: Manajemen sistem, data master, operasional
- **Contoh Permissions**: 
  - `dashboard.view`, `packages.view`, `packages.create`, dll
  - `kelas.view`, `instruktur.view`, `pelanggan.view`
  - `jadwal_kelas.manage`, `absensi.manage`, `transaksi.view`
- **Roles dengan akses CMS**:
  - `admin` - Full access ke semua permissions
  - `instruktur` - Limited access (dashboard, jadwal, absensi, bookings)

### **Domain 2: Pelanggan (Public)**
- **Scope**: `/profile`, `/booking`, `/packages` routes di domain publik
- **Purpose**: Fitur yang diakses pelanggan sendiri (bukan data entry)
- **Contoh Permissions**:
  - `profile.view` - Lihat profil pribadi
  - `profile.update` - Edit profil pribadi
  - `booking.create` - Booking kelas
  - `booking.view` - Lihat jadwal booking
  - `booking.cancel` - Batal booking
  - `package.view` - Lihat daftar paket
  - `package.purchase` - Beli paket
  - `transaction.view` - Lihat transaksi pribadi

## Role & Permission Assignment

### **Admin Role**
```
Permissions: ALL (full access)
Routes: /admin/*
```

### **Instruktur Role**
```
Permissions:
- dashboard.view
- jadwal_kelas.view
- absensi.view
- absensi.manage
- bookings.view

Routes: /admin/* (terbatas sesuai permissions)
```

### **Pelanggan Role** ← Assigned saat registrasi
```
Permissions:
- profile.view
- profile.update
- profile.change_password
- booking.create
- booking.view
- booking.cancel
- package.view
- package.purchase
- transaction.view

Routes: /profile, /booking, /packages (public domain)
```

## Role Assignment Flow

### **Admin User**
- Created manually via `AdminSeeder`
- Assigned: `admin` role

### **Instruktur User**
- Created manually via admin panel
- Assigned: `instruktur` role (jika diset saat create)

### **Pelanggan User** ← Auto-assigned saat registrasi
```php
// saat register()
$user = User::create([...]);

// Assign pelanggan role
$pelangganRole = Role::where('nama_role', 'pelanggan')->first();
if ($pelangganRole) {
    $user->roles()->attach($pelangganRole->id_role, ['is_active' => true]);
}
```

## Permission Checking

### **For CMS Routes**
```php
// In routes
Route::middleware('permission:packages.view')->group(function () {
    // Only users with 'packages.view' permission can access
});
```

### **For Pelanggan Routes**
Currently not using permission middleware, only:
- `auth` - Ensure authenticated
- `role.pelanggan` - Ensure has pelanggan role

Could add if needed:
```php
Route::middleware(['auth', 'role.pelanggan', 'permission:booking.create'])->post('/booking', ...);
```

## Complete Permission List

### CMS Permissions (Admin & Instruktur)
**Dashboard**
- `dashboard.view` - View dashboard

**Master Data**
- `packages.*` - View, Create, Update, Delete packages
- `kelas.*` - View, Create, Update, Delete kelas
- `instruktur.*` - View, Create, Update, Delete instruktur
- `pelanggan.view` - View pelanggan list
- `pelanggan.delete` - Delete pelanggan
- `promo.*` - View, Create, Update, Delete promo

**Operasional**
- `jadwal_kelas.*` - View, Create, Update, Delete jadwal
- `bookings.view` - View bookings
- `absensi.view` - View absensi
- `absensi.manage` - Input/update absensi

**Finance**
- `transaksi.view` - View transactions
- `pembelian_package.view` - View package purchases
- `kredit.view` - View credit mutations

**Content**
- `artikel.*` - View, Create, Update, Delete articles

**Access Control**
- `users.*` - View, Create, Update, Delete users
- `roles.*` - View, Create, Update, Delete roles

**System**
- `activity_logs.view` - View activity logs

### Pelanggan Permissions (Self-service features)
**Profile**
- `profile.view` - View own profile
- `profile.update` - Edit own profile
- `profile.change_password` - Change own password

**Booking**
- `booking.create` - Create booking
- `booking.view` - View own bookings
- `booking.cancel` - Cancel own bookings

**Package**
- `package.view` - View available packages
- `package.purchase` - Purchase packages

**Transaction**
- `transaction.view` - View own transactions

## Key Differences

| Aspect | CMS (Admin) | Pelanggan |
|--------|-----------|-----------|
| **Roles** | admin, instruktur | pelanggan |
| **Routes** | `/admin/*` | `/profile`, `/booking`, `/packages` |
| **Permissions** | Full management (CRUD) | Self-service (view, create own) |
| **Assignment** | Manual (admin panel) | Auto (at registration) |
| **Middleware** | `auth` + `permission:xxx` | `auth` + `role.pelanggan` |
| **Purpose** | System management | User features |

## Database Schema

```
users (id_user, nama, email, password, ...)
  ↓
user_roles (id_user, id_role, is_active)
  ↓
roles (id_role, nama_role, is_active)
  ↓
role_permissions (id_role, id_permission)
  ↓
permissions (id_permission, nama_permission, deskripsi)
```

## Future Enhancements

### Option 1: Add Permission Checking for Pelanggan
```php
Route::middleware(['auth', 'role.pelanggan', 'permission:booking.create'])
    ->post('/booking', [BookingController::class, 'store']);
```

### Option 2: Temporary Role Suspension
If pelanggan account needs to be locked temporarily:
```php
// Set is_active = false in user_roles pivot
$user->roles()
    ->wherePivot('is_active', true)
    ->updateExistingPivot($roleId, ['is_active' => false]);
```

This prevents all permission access while keeping role assignment.

### Option 3: Instructor with Pelanggan Role
Could assign both roles to instruktur:
```php
$instruktur->roles()->attach([
    $instrukturRole->id_role => ['is_active' => true],
    $pelangganRole->id_role => ['is_active' => true],
]);
```

This allows instruktur to also use pelanggan features.

## Testing

```bash
# 1. Register new account
# User should automatically get 'pelanggan' role with permissions

# 2. Check in database
SELECT u.email, r.nama_role 
FROM users u
JOIN user_roles ur ON u.id_user = ur.id_user
JOIN roles r ON ur.id_role = r.id_role
WHERE u.email = 'newuser@example.com';

# Should show: email | nama_role
#             newuser@example.com | pelanggan

# 3. Check permissions
SELECT r.nama_role, p.nama_permission
FROM roles r
JOIN role_permissions rp ON r.id_role = rp.id_role
JOIN permissions p ON rp.id_permission = p.id_permission
WHERE r.nama_role = 'pelanggan'
ORDER BY p.nama_permission;

# Should list all pelanggan permissions
```
