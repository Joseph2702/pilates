# Authorization Fix - Admin vs Pelanggan Role Separation

## Problem
Admin users yang login ke admin panel bisa mengubah URL ke public homepage dan mengakses pelanggan features (myprofile, book class, buy package) seolah-olah mereka adalah user biasa. Ini adalah security risk.

## Root Causes
1. **Pelanggan routes hanya punya `middleware('auth')`** - tidak ada role checking
2. **Admin routes tidak punya role restriction** - hanya punya permission checking
3. **No 'pelanggan' role di database** - seeder tidak membuat role ini
4. **RolePelanggan middleware tidak ada** - tidak ada mekanisme untuk block admin dari pelanggan features

## Solutions Implemented

### 1. Created Two New Middleware

#### RoleAdmin (`app/Http/Middleware/RoleAdmin.php`)
```php
// Protects admin routes
// - Checks if user has 'admin' role with is_active = true
// - If not, returns 403 error
// - Redirects unauthenticated users to admin.login
```

#### RolePelanggan (`app/Http/Middleware/RolePelanggan.php`)
```php
// Protects pelanggan routes  
// - Checks if user has 'pelanggan' role with is_active = true
// - If user is admin, redirects them to admin.dashboard instead
// - If not pelanggan, returns 403 error
// - Redirects unauthenticated users to web.login
```

### 2. Registered Middleware in bootstrap/app.php
```php
'role.admin' => \App\Http\Middleware\RoleAdmin::class,
'role.pelanggan' => \App\Http\Middleware\RolePelanggan::class,
```

### 3. Updated Routes Protection

**Admin Group** (`routes/web.php` line ~74):
```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.admin'])->group(function () {
    // ... admin routes
});
```

**Pelanggan Auth Routes** (`routes/web.php` line ~60):
```php
Route::middleware(['auth', 'role.pelanggan'])->group(function () {
    Route::get('/profile', ...);
    Route::post('/booking', ...);
    // ... pelanggan routes
});
```

### 4. Updated AdminSeeder
Added 'pelanggan' role creation:
```php
// Pelanggan Role — no permissions needed (only access pelanggan features)
Role::firstOrCreate(
    ['nama_role' => 'pelanggan'],
    ['is_active' => true]
);
```

## How It Works Now

### Admin User Flow
1. Admin login → assigned 'admin' role (via AdminSeeder)
2. Access `/admin/dashboard` → RoleAdmin middleware checks admin role ✅
3. Try to access `/profile` → RolePelanggan middleware detects admin role and redirects to `/admin/dashboard` 🚫
4. Try to access `/booking` → RolePelanggan middleware blocks with 403 🚫

### Pelanggan User Flow  
1. Pelanggan login → assigned 'pelanggan' role (via register/auth)
2. Try to access `/admin/dashboard` → RoleAdmin middleware blocks with 403 🚫
3. Access `/profile` → RolePelanggan middleware checks pelanggan role ✅
4. Access `/booking` → RolePelanggan middleware checks pelanggan role ✅

## Key Differences

| Feature | Before | After |
|---------|--------|-------|
| Admin accessing `/profile` | ✅ Allowed | 🚫 Redirected to `/admin/dashboard` |
| Admin accessing `/booking` | ✅ Allowed | 🚫 403 Forbidden |
| Admin accessing `/admin/*` | ✅ Allowed | ✅ Allowed |
| Pelanggan accessing `/admin/*` | ✅ Allowed! (BUG) | 🚫 403 Forbidden |
| Pelanggan accessing `/profile` | ✅ Allowed | ✅ Allowed |

## Testing

```bash
# Run seeder to create 'pelanggan' role
php artisan db:seed --class=AdminSeeder

# Test as admin user
# 1. Login as admin@pilates.com / admin123
# 2. Should go to /admin/dashboard
# 3. Try to visit /profile → redirects to /admin/dashboard
# 4. Try to visit /booking → 403 Forbidden

# Test as pelanggan user  
# 1. Register new account
# 2. Should have 'pelanggan' role
# 3. Visit /profile → works
# 4. Visit /booking → works
# 5. Try to visit /admin → 403 Forbidden
```

## Files Modified
- `app/Http/Middleware/RoleAdmin.php` - **CREATED**
- `app/Http/Middleware/RolePelanggan.php` - **CREATED**
- `bootstrap/app.php` - Added middleware aliases
- `routes/web.php` - Added middleware to routes
- `database/seeders/AdminSeeder.php` - Added pelanggan role creation

## Security Notes
1. **Role-based access control** is now properly enforced
2. **Admin users cannot impersonate pelanggan** by changing URLs
3. **Pelanggan users cannot access admin panel**
4. **All auth-required pelanggan routes** now check for pelanggan role
5. **Redirect vs 403**: Admin trying pelanggan routes are redirected (UX friendly), while pelanggan trying admin routes get 403 (security proper)
