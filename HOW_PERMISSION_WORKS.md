# How Permission System Works - Complete Guide

## Overview

Permission system bekerja dengan **3 layers**:

```
Admin Panel (Database)
        ↓
Permission Model
        ↓
Route Middleware
        ↓
User Access Control
```

## Layer 1: Admin Panel Permission Management

### **Create Permission di Admin Panel**
```
/admin/permissions/create
- Input: nama_permission (e.g., "artikel.publish")
- Input: deskripsi
- Save ke database
```

**TAPI:** Permission yang di-create di panel **tidak otomatis** work unless di-link ke routes!

## Layer 2: Permission Database Entry

```
permissions table:
├── id_permission (primary key)
├── nama_permission (string, unique)
└── deskripsi (string)

Contoh entries:
├── artikel.view (Lihat daftar artikel)
├── artikel.create (Tambah artikel baru)
├── artikel.publish (Publikasi artikel)
└── artikel.delete (Hapus artikel)
```

## Layer 3: Route Middleware Binding

### **Permission HARUS di-register di Routes**

```php
// routes/web.php

Route::get('/admin/artikel', [ArtikelController::class, 'index'])
    ->middleware('permission:artikel.view');  // ← Link permission ke route

Route::post('/admin/artikel', [ArtikelController::class, 'store'])
    ->middleware('permission:artikel.create');  // ← Berbeda permission

Route::post('/admin/artikel/{id}/publish', [ArtikelController::class, 'publish'])
    ->middleware('permission:artikel.publish');  // ← Custom permission baru
```

### **How It Works at Runtime**

```
1. User access: GET /admin/artikel
2. Laravel match route dan lihat middleware
3. PermissionMiddleware::handle() dipanggil
4. Check: User::hasPermission('artikel.view')?
5. Query: SELECT FROM role_permissions WHERE permission = 'artikel.view' AND user has this role
6. Result: Permission ada? → Allow : Redirect dengan error
```

## Complete Flow Example

### **Scenario: Admin ingin create permission baru untuk publish artikel**

#### **Step 1: Create permission di admin panel**
```
Admin → /admin/permissions/create
Input:
  - nama_permission: "artikel.publish"
  - deskripsi: "Publikasi artikel"
Click: Save

Result: Permission tersimpan di database
```

#### **Step 2: Tambah permission ke AdminSeeder** (untuk seeding)
```php
// database/seeders/AdminSeeder.php
$permissions = [
    // ... existing permissions ...
    ['nama_permission' => 'artikel.publish', 'deskripsi' => 'Publikasi artikel'],
];
```

#### **Step 3: Assign permission ke role**
```
Admin → /admin/roles/editor/edit
Check checkbox: "Artikel → Publish"
Click: Update

Result: editor role sekarang punya artikel.publish permission
```

#### **Step 4: Add route middleware**
```php
// routes/web.php
Route::post('/admin/artikel/{id}/publish', [ArtikelController::class, 'publish'])
    ->middleware('permission:artikel.publish');  // ← Add this line
```

#### **Step 5: Implement controller logic**
```php
// app/Http/Controllers/Web/Admin/ArtikelController.php
public function publish(Request $request, int $id)
{
    // Permission sudah di-check oleh middleware
    // Jika sampai sini, user pasti punya permission
    
    $artikel = Artikel::findOrFail($id);
    $artikel->update(['status' => 'published', 'published_at' => now()]);
    
    return redirect()->back()->with('success', 'Artikel berhasil dipublikasi');
}
```

#### **Step 6: Test akses**
```
Case 1: Editor dengan permission
- Login as editor
- Go to /admin/artikel/1/publish
- Result: ✅ Article published, success message

Case 2: Author tanpa permission
- Login as author (tidak punya artikel.publish)
- Go to /admin/artikel/1/publish
- Result: ❌ Redirect dengan error message
```

## Permission vs Route Architecture

### **Current Implementation Pattern**

```
CRUD Operations:
├── .view (list, show) → middleware('permission:resource.view')
├── .create (form, store) → middleware('permission:resource.create')
├── .update (form, put) → middleware('permission:resource.update')
└── .delete (destroy) → middleware('permission:resource.delete')

Special Operations:
├── .manage (full control) → middleware('permission:resource.manage')
├── .publish (publish content) → middleware('permission:resource.publish')
└── .approve (approve requests) → middleware('permission:resource.approve')
```

### **Real Examples from Project**

```php
// Packages CRUD
Route::get('packages', [PackageWebController::class, 'index'])
    ->middleware('permission:packages.view');
Route::post('packages', [PackageWebController::class, 'store'])
    ->middleware('permission:packages.create');
Route::put('packages/{package}', [PackageWebController::class, 'update'])
    ->middleware('permission:packages.update');
Route::delete('packages/{package}', [PackageWebController::class, 'destroy'])
    ->middleware('permission:packages.delete');

// Absensi with manage
Route::get('absensi', [AbsensiWebController::class, 'index'])
    ->middleware('permission:absensi.view');
Route::post('absensi', [AbsensiWebController::class, 'store'])
    ->middleware('permission:absensi.manage');
```

## How to Add New Permission

### **Method 1: Via Admin Panel** ✅ (Easy)
```
1. Go to: /admin/permissions/create
2. Input: nama_permission = "reporting.export"
3. Input: deskripsi = "Export laporan ke Excel"
4. Save
5. Assign ke role di /admin/roles/{id}/edit
```

**TAPI HARUS JUGA:**
```php
// Add to routes/web.php
Route::get('/admin/reports/export', [ReportController::class, 'export'])
    ->middleware('permission:reporting.export');
```

### **Method 2: Via Seeder** ✅ (Recommended for deployment)
```php
// database/seeders/AdminSeeder.php
$permissions = [
    // ... existing ...
    ['nama_permission' => 'reporting.export', 'deskripsi' => 'Export laporan ke Excel'],
    ['nama_permission' => 'reporting.schedule', 'deskripsi' => 'Schedule laporan'],
];

// Then assign ke role
$reportingRole->permissions()->sync(
    Permission::whereIn('nama_permission', [
        'reporting.export',
        'reporting.schedule',
    ])->pluck('id_permission')->toArray()
);
```

### **Method 3: Via Code** (Direct)
```php
// In controller atau command
use App\Domain\Entity\Permission;

Permission::firstOrCreate(
    ['nama_permission' => 'reporting.export'],
    ['deskripsi' => 'Export laporan ke Excel']
);
```

## Relationship Diagram

```
users (id_user)
    ↓
    ↓ (many-to-many via user_roles)
    ↓
roles (id_role) ← Admin bisa manage ini
    ↓
    ↓ (many-to-many via role_permissions)
    ↓
permissions (id_permission) ← Admin bisa manage ini
    ↓
    ↓ (referenced in routes/web.php)
    ↓
Route middleware ('permission:xxx')
    ↓
    ↓ (checked at runtime)
    ↓
User access granted/denied
```

## Common Questions

### **Q: Bisa membuat permission tanpa route middleware?**
**A:** Ya bisa, tapi permission tidak akan punya fungsi. Ini untuk:
- Planning future features
- Documentation purposes
- Pre-defining permission structure

Tapi **recommended: jangan buat permission tanpa use case**

### **Q: Bisakah permission check di controller bukan middleware?**
**A:** Ya bisa, gunakan di controller:
```php
public function publish(Request $request, int $id)
{
    if (!auth()->user()->hasPermission('artikel.publish')) {
        return redirect()->back()->with('error', 'Tidak ada permission');
    }
    
    // ... publish logic ...
}
```

Tapi **less secure** karena controller bisa diakses dulu. **Middleware lebih baik**.

### **Q: Bisa share satu permission ke banyak route?**
**A:** Ya, banyak route bisa pakai permission sama:
```php
// All read operations bisa pakai 'artikel.view'
Route::get('/admin/artikel', [...])
    ->middleware('permission:artikel.view');

Route::get('/admin/artikel/{id}', [...])
    ->middleware('permission:artikel.view');

Route::get('/admin/artikel/{id}/comments', [...])
    ->middleware('permission:artikel.view');
```

### **Q: Permission bisa di-override?**
**A:** Tidak, permission middleware hard-block. Tapi bisa:
- Assign permission ke role
- Remove permission dari role
- Create temporary role dengan permission

## Best Practices

### ✅ DO:
1. **Create permission sebelum bikin route** - Think first, code second
2. **Use consistent naming** - `resource.action` format (artikel.publish)
3. **Document permission purposes** - Jelaskan di deskripsi
4. **Assign meaningful permissions** - Sesuai dengan actual functionality
5. **Test permission flow** - Login sebagai different roles dan test

### ❌ DON'T:
1. **Create permission without route** - Orphan permissions
2. **Use vague names** - Avoid "admin.special" atau "user.manage"
3. **Forget to add middleware** - Just creating permission tidak cukup
4. **Mix permission checks** - Jangan mix middleware + controller checks
5. **Create too many permissions** - Simplify, use role-based grouping

## Testing Permission Flow

```bash
# Test dengan admin
1. Login as admin@pilates.com / admin123
2. Access /admin/artikel → ✅ Allowed
3. Try to publish → ✅ Allowed

# Test dengan editor (yang punya artikel.view, artikel.create tapi tidak artikel.publish)
1. Login as editor@test.com
2. Access /admin/artikel → ✅ Allowed (punya artikel.view)
3. Try to publish → ❌ Redirect dengan error

# Test dengan viewer (hanya artikel.view)
1. Login as viewer@test.com
2. Access /admin/artikel → ✅ Allowed (punya artikel.view)
3. Try to create → ❌ Redirect dengan error
```

## Current Permissions in System

### **From AdminSeeder**
- dashboard.view
- packages.* (view, create, update, delete)
- kelas.* (view, create, update, delete)
- instruktur.* (view, create, update, delete)
- pelanggan.* (view, delete)
- promo.* (view, create, update, delete)
- jadwal_kelas.* (view, create, update, delete)
- bookings.view
- absensi.* (view, manage)
- transaksi.view
- pembelian_package.view
- kredit.view
- artikel.* (view, create, update, delete)
- users.* (view, create, update, delete)
- roles.* (view, create, update, delete)
- activity_logs.view

### **From AuthSeeder (Pelanggan)**
- profile.* (view, update, change_password)
- booking.* (create, view, cancel)
- package.* (view, purchase)
- transaction.view

## Summary

| Aspect | Detail |
|--------|--------|
| **Database** | Permissions stored in `permissions` table |
| **Assignment** | Permissions assigned to roles via `role_permissions` pivot |
| **Enforcement** | Routes use `middleware('permission:xxx')` |
| **Check** | `User::hasPermission()` queries database realtime |
| **Flow** | User → Role → Permission → Route Middleware → Access |
| **Admin Panel** | Manage permissions, roles, assignments di `/admin` |

**Key Point:** Permission in database ≠ Permission in use. **Always link permission to routes with middleware!**
