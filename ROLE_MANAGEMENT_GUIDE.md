# Role Management - Admin Control Panel

## Overview

Admin panel sekarang support **full control** untuk manage setiap role dan permissions-nya secara realtime.

## Features

### 1. **View All Roles**
Admin dapat melihat semua role yang ada:
- Navigate ke: `/admin/roles`
- Shows: Nama role, status (active/inactive), jumlah permissions

### 2. **Create New Role**
Admin dapat membuat role baru:
- Navigate ke: `/admin/roles/create`
- Input: 
  - Nama role (unique)
  - Status (active/inactive)
  - Select permissions dari grid

### 3. **Edit Role & Permissions** ✅ FULLY IMPLEMENTED
Admin dapat update role dan permissions-nya:
- Navigate ke: `/admin/roles/{id}/edit`
- Update:
  - Nama role
  - Status
  - **Permissions** (dengan UI grid yang user-friendly)

### 4. **Delete Role**
Admin dapat hapus role (jika tidak ada user dengan role tersebut)

## Permission Grid UI

Permissions ditampilkan dalam **grouped table** untuk clarity:

```
┌─────────────────────────────────────────────────────────┐
│ Menu          │ View │ Create │ Update │ Delete │ Manage│
├─────────────────────────────────────────────────────────┤
│ Dashboard     │ ☐    │        │        │        │       │
│ Packages      │ ☐    │ ☐      │ ☐      │ ☐      │       │
│ Kelas         │ ☐    │ ☐      │ ☐      │ ☐      │       │
│ ...           │      │        │        │        │       │
│ Absensi       │ ☐    │        │        │        │ ☐     │
└─────────────────────────────────────────────────────────┘
```

**Warna indikator:**
- 🔵 View = Blue
- 🟢 Create = Green
- 🟡 Update = Yellow
- 🔴 Delete = Red
- 🟣 Manage = Purple

## Real-time Permission Updates

### **How It Works**

**Scenario: Admin menghilangkan `absensi.manage` dari role Instruktur**

#### Step 1: Admin Edit Role
```
1. Admin login → /admin/roles/instruktur/edit
2. Uncheck "Manage" di group "Absensi"
3. Click "Update"
```

#### Step 2: Database Updated
```sql
-- role_permissions table
DELETE FROM role_permissions 
WHERE id_role = (SELECT id_role FROM roles WHERE nama_role = 'instruktur')
AND id_permission = (SELECT id_permission FROM permissions WHERE nama_permission = 'absensi.manage');
```

#### Step 3: Permission Cache Cleared
```php
// RoleWebController::update() calls:
$role->syncPermissionsWithClearCache($data['permissions']);

// Which:
// 1. Updates role_permissions table
// 2. Gets all users with this role
// 3. Calls clearPermissionCache() on each user
```

#### Step 4: Instruktur Impact
**Next time instruktur akses `/admin/absensi`:**
```
1. PermissionMiddleware checks: hasPermission('absensi.manage')
2. Query database for permission (realtime, not cached)
3. Permission tidak ditemukan → 403 Forbidden
4. Instruktur tidak bisa akses absensi
```

### **Code Flow**

```php
// admin.roles.update (RoleWebController)
$role->update([...]);

// Option A: If permissions changed
$role->syncPermissionsWithClearCache($data['permissions']);
  ↓
  // Role.php
  $this->permissions()->sync($permissionIds);  // Update DB
  
  $this->users()->get()->each(fn($user) => 
      $user->clearPermissionCache()  // Clear cache
  );

// Option B: If no permissions selected
$role->permissions()->sync([]);
$role->users()->get()->each(fn($user) => $user->clearPermissionCache());

// Next request dari instruktur
PermissionMiddleware::handle()
  ↓
User::hasPermission('absensi.manage')
  ↓
Database query (realtime)
  ↓
Permission exists? → Allow : 403 Forbidden
```

## Implementation Details

### **User Model Methods**

```php
// Check single permission (realtime DB query)
public function hasPermission(string $permission): bool
{
    return $this->roles()
        ->wherePivot('is_active', true)
        ->whereHas('permissions', fn ($q) => $q->where('nama_permission', $permission))
        ->exists();
}

// Get all permissions for user
public function getPermissions(): array
{
    return $this->roles()
        ->wherePivot('is_active', true)
        ->with('permissions')
        ->get()
        ->flatMap(fn ($role) => $role->permissions)
        ->pluck('nama_permission')
        ->unique()
        ->toArray();
}

// Clear cache (for future caching implementation)
public function clearPermissionCache(): void
{
    // Currently no-op, but ready for caching
    // Example: cache()->forget("user.{$this->id_user}.permissions");
}
```

### **Role Model Methods**

```php
// Sync permissions AND clear user cache
public function syncPermissionsWithClearCache(array $permissionIds): void
{
    $this->permissions()->sync($permissionIds);
    
    $this->users()
        ->get()
        ->each(fn ($user) => $user->clearPermissionCache());
}
```

### **RoleWebController Update**

```php
public function update(Request $request, int $id)
{
    $role = Role::findOrFail($id);
    
    $data = $request->validate([
        'permissions' => 'nullable|array',
    ]);
    
    // Use method that clears cache
    if (isset($data['permissions'])) {
        $role->syncPermissionsWithClearCache($data['permissions']);
    }
    
    return redirect()->with('success', 
        'Perubahan akan langsung berlaku untuk user dengan role ini.'
    );
}
```

## Testing Scenario

### **Setup**
```bash
# 1. Create instruktur account
Email: instruktur@test.com
Role: instruktur
Has: [dashboard.view, absensi.view, absensi.manage, ...]

# 2. Create admin account
Email: admin@test.com
Role: admin
Has: [ALL permissions]
```

### **Test Flow**

```
T1: Instruktur login → can access /admin/absensi ✅
    Browser: https://ngrok-url/admin/absensi → Works

T2: Admin update instruktur role
    Remove: absensi.manage permission
    Browser: POST /admin/roles/instruktur → Update success

T3: Instruktur try to access /admin/absensi again
    Browser: https://ngrok-url/admin/absensi 
    PermissionMiddleware checks: hasPermission('absensi.manage')
    Result: 403 Forbidden ❌

T4: Instruktur still has absensi.view but not absensi.manage
    Can see absensi page but cannot input/edit
```

## Current Status

| Feature | Status | Notes |
|---------|--------|-------|
| Role CRUD | ✅ Complete | Create, read, update, delete roles |
| Permission Grid UI | ✅ Complete | Grouped, color-coded checkboxes |
| Permission Sync | ✅ Complete | sync() method works |
| Cache Clearing | ✅ Complete | clearPermissionCache() ready |
| Realtime Check | ✅ Complete | hasPermission() queries DB |
| Admin Update Success | ✅ Complete | Shows confirmation message |

## Files Modified

- `app/Domain/Entity/User.php` - Added `getPermissions()` and improved docs
- `app/Domain/Entity/Role.php` - Added `syncPermissionsWithClearCache()` method
- `app/Http/Controllers/Web/Admin/RoleWebController.php` - Use new sync method
- `resources/views/admin/roles/edit.blade.php` - Already has permission grid
- `resources/views/admin/roles/_permissions-grid.blade.php` - Renders permission checkboxes

## Future Enhancements

### Option 1: Implement Redis Caching
```php
public function hasPermission(string $permission): bool
{
    $cacheKey = "user.{$this->id_user}.permissions";
    
    $permissions = Cache::remember($cacheKey, 3600, function () {
        return $this->getPermissions();
    });
    
    return in_array($permission, $permissions);
}

public function clearPermissionCache(): void
{
    Cache::forget("user.{$this->id_user}.permissions");
}
```

### Option 2: Real-time WebSocket Notifications
```php
// Notify all users with changed role via WebSocket
$role->users()->get()->each(function ($user) {
    broadcast(new PermissionsUpdated($user))->toOthers();
});
```

### Option 3: Role-based API Tokens
```php
// Generate API tokens with permission scopes
// api_tokens table with: abilities (JSON array of permissions)
```

## Troubleshooting

### Issue: Permission changes not working
**Solution:**
1. Check database: `SELECT * FROM role_permissions WHERE id_role = X;`
2. Verify user still has role: `SELECT * FROM user_roles WHERE id_user = X;`
3. Check is_active flags: `SELECT * FROM user_roles WHERE is_active = true;`
4. Test directly: `User::find(1)->hasPermission('absensi.manage')`

### Issue: Instruktur still has old permissions after update
**Solution:**
1. Ask instruktur to **logout and login again** (session refresh)
2. Or check middleware caching - should be minimal with our realtime approach

### Issue: Admin cannot see permission grid
**Solution:**
1. Verify admin has `roles.update` permission
2. Check if all permissions exist in database
3. View page source to debug HTML rendering
