# 🔐 PERMISSION SYSTEM FLOWCHART

## 1️⃣ FLOW SETUP PERMISSION (First Time - Seeder)

```
┌─────────────────────────────────┐
│ php artisan migrate:fresh --seed│
└────────────┬────────────────────┘
             │
             ↓
┌─────────────────────────────────────────────┐
│ AdminSeeder::run()                          │
│ (database/seeders/AdminSeeder.php)          │
└────────────┬────────────────────────────────┘
             │
             ├─────────────────────────────────────────────────┐
             │                                                 │
             ↓                                                 ↓
   ┌─────────────────────┐                   ┌─────────────────────┐
   │ Create Permissions  │                   │ Create Roles        │
   │                     │                   │                     │
   │ Foreach permission: │                   │ - admin (id: 1)     │
   │ - packages.view     │                   │ - instruktur (id:2) │
   │ - packages.create   │                   │ - pelanggan (id: 3) │
   │ - packages.update   │                   │                     │
   │ - packages.delete   │                   │ INSERT into roles   │
   │ - kelas.view        │                   │ table               │
   │ - kelas.create      │                   │                     │
   │ - ... (41 total)    │                   │                     │
   │                     │                   │                     │
   │ INSERT into         │                   │                     │
   │ permissions table   │                   │                     │
   └────────┬────────────┘                   └────────┬────────────┘
            │                                         │
            └──────────────┬──────────────────────────┘
                           ↓
        ┌──────────────────────────────────────┐
        │ Assign Permissions to Roles          │
        │ (role_permissions table)             │
        │                                      │
        │ ADMIN:                               │
        │ ├─ role_permissions: 1 → [1..41]    │
        │ │ (all permissions)                  │
        │ │                                    │
        │ INSTRUKTUR:                          │
        │ ├─ role_permissions: 2 → [          │
        │ │   dashboard.view,                  │
        │ │   jadwal_kelas.view,               │
        │ │   bookings.view,                   │
        │ │   absensi.view,                    │
        │ │   absensi.manage                   │
        │ │ ]                                  │
        │ │                                    │
        │ PELANGGAN:                           │
        │ └─ role_permissions: 3 → [           │
        │     profile.view,                    │
        │     profile.update,                  │
        │     booking.create,                  │
        │     booking.view,                    │
        │     booking.cancel,                  │
        │     package.view,                    │
        │     package.purchase,                │
        │     transaction.view                 │
        │   ]                                  │
        │                                      │
        └──────────────┬───────────────────────┘
                       ↓
        ┌──────────────────────────────────────┐
        │ Create Default Admin User            │
        │ (users table)                        │
        │                                      │
        │ email: admin@pilates.com             │
        │ password: admin123                   │
        │ status: active                       │
        │                                      │
        │ Assign role:                         │
        │ user_roles: 1 (admin) → is_active:1 │
        └──────────────┬───────────────────────┘
                       ↓
        ┌──────────────────────────────────────┐
        │ ✅ Setup Complete                    │
        │                                      │
        │ Database tables populated:           │
        │ ✓ permissions (41 rows)              │
        │ ✓ roles (3 rows)                     │
        │ ✓ role_permissions (~65 rows)        │
        │ ✓ users (1 admin user)               │
        │ ✓ user_roles (1 row)                 │
        └──────────────────────────────────────┘
```

---

## 2️⃣ FLOW: Admin Create User & Assign Role

```
┌─────────────────────────────────────────────────┐
│ Admin di /admin/users/create                    │
│ Fill form:                                      │
│ - nama: Budi Instruktur                         │
│ - email: budi@pilates.com                       │
│ - password: xxx                                 │
│ - no_hp: 081234567890                           │
│ - role: instruktur                              │
│ Click: CREATE                                   │
└────────────┬────────────────────────────────────┘
             │
             ↓
┌─────────────────────────────────────────────────┐
│ UserWebController::store()                      │
└────────────┬────────────────────────────────────┘
             │
             ├─ Validasi input
             │  ├─ email unique? ✓
             │  ├─ password >= 8 char? ✓
             │  ├─ no_hp unique? ✓
             │  ├─ no_hp only numbers? ✓
             │  └─ ...
             │
             ↓
             ├─ Check: Admin punya permission 'users.create'?
             │  ├─ Yes → lanjut
             │  └─ No → 403 Forbidden
             │
             ↓
┌─────────────────────────────────────────────────┐
│ User::create()                                  │
│ INSERT INTO users:                              │
│ - id_user: 4 (auto)                             │
│ - nama: 'Budi Instruktur'                       │
│ - email: 'budi@pilates.com'                     │
│ - password: bcrypt('xxx')                       │
│ - no_hp: '081234567890'                         │
│ - status: 'active'                              │
└────────────┬────────────────────────────────────┘
             │
             ↓
┌─────────────────────────────────────────────────┐
│ $user->roles()->attach()                        │
│ INSERT INTO user_roles:                         │
│ - id_user: 4                                    │
│ - id_role: 2 (instruktur)                       │
│ - is_active: 1                                  │
└────────────┬────────────────────────────────────┘
             │
             ↓
┌─────────────────────────────────────────────────┐
│ ✅ User Created!                                │
│                                                 │
│ Budi Instruktur sekarang punya:                 │
│ ✓ User account (id: 4)                          │
│ ✓ Role: instruktur (id: 2)                      │
│ ✓ Auto inherit permissions dari role instruktur:│
│   - dashboard.view                              │
│   - jadwal_kelas.view                           │
│   - bookings.view                               │
│   - absensi.view                                │
│   - absensi.manage                              │
│                                                 │
│ Status: Ready to login!                         │
└─────────────────────────────────────────────────┘
```

---

## 3️⃣ FLOW: User Login & Access Protected Route

```
┌──────────────────────────────────┐
│ User: budi@pilates.com (instruktur)
│ Try to access: /admin/packages    │
└────────────┬─────────────────────┘
             │
             ↓
┌──────────────────────────────────────────────┐
│ Laravel Router check route definition:        │
│ (routes/web.php)                             │
│                                              │
│ Route::get('packages',                       │
│   [PackageWebController::class, 'index'])    │
│   ->name('admin.packages.index')             │
│   ->middleware('permission:packages.view')   │
└────────────┬─────────────────────────────────┘
             │
             ↓
┌──────────────────────────────────────────────┐
│ Execute Middleware Stack:                    │
│ 1. auth - Check if user logged in            │
│ 2. role.admin - Check if user is admin       │
│ 3. permission:packages.view - Check permission
│                                              │
└────────────┬─────────────────────────────────┘
             │
   ┌─────────┴──────────┐
   │                    │
   ↓ (1)                ↓ (2)
┌────────────────┐   ┌──────────────────────┐
│ auth middleware │   │ role.admin middleware│
│                │   │                      │
│ auth()->check()│   │ Check user roles:    │
│ ├─ Yes → next │   │ ├─ instruktur → NO   │
│ └─ No → login │   │ │   403 Forbidden! ✗ │
│                │   │ └─ admin → next      │
└────────────────┘   └──────────────────────┘
   │                    │
   └─────────┬──────────┘
             │
             ├─ Budi (instruktur) BLOCKED ✗
             │  - User have auth? YES
             │  - User is admin? NO
             │  - Response: 403 Forbidden
             │
             ├─ Admin user allowed ✓
             │  - User have auth? YES
             │  - User is admin? YES
             │  - Continue to (3)
             │
             ↓ (3)
┌────────────────────────────────────────────────┐
│ PermissionMiddleware::handle()                 │
│ (app/Http/Middleware/PermissionMiddleware.php) │
│                                                │
│ foreach ($permissions as $permission) {        │
│   if ($user->hasPermission($permission)) {     │
│     return $next($request);  // ✓ Allow       │
│   }                                            │
│ }                                              │
│ return 403;  // ✗ Forbidden                   │
│                                                │
└────────────┬───────────────────────────────────┘
             │
             ↓
┌─────────────────────────────────────────────────┐
│ $user->hasPermission('packages.view')           │
│ (app/Domain/Entity/User.php)                    │
│                                                 │
│ public function hasPermission(string $perm)     │
│ {                                               │
│   return $this->roles()                         │
│     ->wherePivot('is_active', true)             │
│     ->whereHas('permissions',                   │
│       fn($q) => $q->where(                      │
│         'nama_permission', $perm                │
│       )                                         │
│     )                                           │
│     ->exists();                                 │
│ }                                               │
│                                                 │
│ Logika:                                         │
│ 1. Get all active roles dari user               │
│ 2. Check apakah ada role yang punya permission  │
│ 3. Return true jika ada, false jika tidak       │
└────────────┬──────────────────────────────────┘
             │
             ↓
┌─────────────────────────────────────────────────┐
│ Database Query:                                 │
│                                                 │
│ SELECT EXISTS (                                 │
│   SELECT 1 FROM roles                           │
│   INNER JOIN user_roles                         │
│     ON roles.id_role = user_roles.id_role       │
│   INNER JOIN role_permissions                   │
│     ON roles.id_role = role_permissions.id_role │
│   INNER JOIN permissions                        │
│     ON role_permissions.id_permission =         │
│        permissions.id_permission                │
│   WHERE user_roles.id_user = 4                  │ (Budi)
│   AND user_roles.is_active = 1                  │
│   AND permissions.nama_permission =             │
│       'packages.view'                           │
│ )                                               │
│                                                 │
└────────────┬──────────────────────────────────┘
             │
             ├─ Query result: Cari role instruktur
             │                (id_role: 2)
             │  ├─ Check: apakah role 2 punya
             │  │           'packages.view'?
             │  │  └─ Cek tabel role_permissions
             │  │     WHERE id_role = 2
             │  │     AND id_permission =
             │  │       (id dari 'packages.view')
             │  │
             │  └─ HASIL: TIDAK ADA ✗
             │            (role instruktur tidak
             │             punya permission ini)
             │
             ↓
┌─────────────────────────────────────────────────┐
│ hasPermission() return: FALSE                   │
│                                                 │
│ PermissionMiddleware response:                  │
│ ❌ 403 Forbidden - Access Denied                │
│                                                 │
│ Return view: 'errors.no-access'                 │
│ With message: "Anda tidak memiliki akses"       │
│                                                 │
│ User Budi (instruktur) BLOCKED ✗               │
│ Cannot access /admin/packages                  │
└─────────────────────────────────────────────────┘

---

┌─────────────────────────────────────────────────┐
│ CONTRAST: Admin Access                          │
│                                                 │
│ Admin user try: /admin/packages                 │
│                                                 │
│ Query:                                          │
│ SELECT EXISTS (                                 │
│   ... WHERE id_user = 1 (admin)                 │
│   AND is_active = 1                             │
│   AND nama_permission = 'packages.view'         │
│ )                                               │
│                                                 │
│ ├─ Role admin (id: 1) has ALL permissions      │
│ │  ✓ Yes → 'packages.view' FOUND                │
│ │                                               │
│ └─ Result: TRUE                                 │
│                                                 │
│ ✅ Admin ALLOWED ✓                              │
│ Route executed → PackageWebController::index()  │
└─────────────────────────────────────────────────┘
```

---

## 4️⃣ FLOW: User Access Sidebar Menu (Dynamic Menu Rendering)

```
┌─────────────────────────────────┐
│ User login & view admin panel    │
│ Browser GET /admin/dashboard     │
└────────────┬────────────────────┘
             │
             ↓
┌─────────────────────────────────────────────┐
│ Laravel render template:                    │
│ resources/views/layouts/admin.blade.php     │
│ (includes sidebar navigation)               │
└────────────┬───────────────────────────────┘
             │
             ↓
┌─────────────────────────────────────────────┐
│ Blade template logic:                       │
│                                             │
│ @php $u = auth()->user(); @endphp           │
│ <!-- Get current authenticated user -->     │
│                                             │
│ <!-- Then iterate menu items with:         │
│ @if($u->hasPermission('permission_name'))  │
│   <!-- Render menu -->                      │
│ @endif                                      │
│                                             │
└────────────┬───────────────────────────────┘
             │
             ├─ Menu Item 1: Dashboard
             │  │
             │  ├─ @if($u->hasPermission('dashboard.view'))
             │  │    ├─ Call hasPermission() query
             │  │    ├─ Result: YES/NO
             │  │    ├─ Yes → <show link>
             │  │    └─ No → <hide>
             │  │
             │  └─ Next item
             │
             ├─ Section: Master Data
             │  │
             │  ├─ @if($u->hasPermission('packages.view') || ...)
             │  │    ├─ Check if any permission exists
             │  │    ├─ Result: YES/NO
             │  │    ├─ Yes → <show section header>
             │  │    └─ No → <skip section>
             │  │
             │  └─ Menu Item: Packages
             │     ├─ @if($u->hasPermission('packages.view'))
             │     │    ├─ Call hasPermission() query
             │     │    ├─ Result: YES/NO
             │     │    ├─ Yes → <show link>
             │     │    └─ No → <hide>
             │     │
             │     └─ Menu Item: Kelas
             │        ├─ @if($u->hasPermission('kelas.view'))
             │        │    ├─ Call hasPermission() query
             │        │    ├─ Result: YES/NO
             │        │    ├─ Yes → <show link>
             │        │    └─ No → <hide>
             │
             ↓
┌─────────────────────────────────────────────┐
│ EXAMPLE: Admin User Sees:                   │
│                                             │
│ Dashboard (✓ have permission)               │
│                                             │
│ MASTER DATA                                 │
│ ├─ Packages (✓ have permission)             │
│ ├─ Kelas (✓ have permission)                │
│ ├─ Instruktur (✓ have permission)           │
│ ├─ Pelanggan (✓ have permission)            │
│ └─ Promo (✓ have permission)                │
│                                             │
│ OPERASIONAL                                 │
│ ├─ Jadwal Kelas (✓ have permission)         │
│ ├─ Bookings (✓ have permission)             │
│ └─ Absensi (✓ have permission)              │
│                                             │
│ KEUANGAN                                    │
│ ├─ Transaksi (✓ have permission)            │
│ ├─ Pembelian (✓ have permission)            │
│ └─ Kredit (✓ have permission)               │
│                                             │
│ KONTEN & AKSES                              │
│ ├─ Artikel (✓ have permission)              │
│ ├─ Users (✓ have permission)                │
│ ├─ Roles (✓ have permission)                │
│ ├─ Permissions (✓ have permission)          │
│ └─ Activity Logs (✓ have permission)        │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ EXAMPLE: Instruktur User Sees:              │
│                                             │
│ Dashboard (✓ have permission)               │
│                                             │
│ OPERASIONAL                                 │
│ ├─ Jadwal Kelas (✓ have permission)         │
│ ├─ Bookings (✓ have permission)             │
│ └─ Absensi (✓ have permission)              │
│                                             │
│ (Sections hidden because no permissions:)  │
│ ✗ MASTER DATA (hidden)                      │
│ ✗ KEUANGAN (hidden)                         │
│ ✗ KONTEN & AKSES (hidden)                   │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ EXAMPLE: Pelanggan User Sees:               │
│                                             │
│ (Mereka tidak akses /admin route sama sekali)
│ Redirect by middleware 'role.admin'         │
│                                             │
│ Mereka akses route berbeda (public):        │
│ /profile, /classes, /packages               │
│ /articles, /booking, dll                    │
│                                             │
│ Permission di role pelanggan:               │
│ ✓ profile.view                              │
│ ✓ profile.update                            │
│ ✓ booking.create                            │
│ ✓ booking.view                              │
│ ✓ booking.cancel                            │
│ ✓ package.view                              │
│ ✓ package.purchase                          │
│ ✓ transaction.view                          │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 5️⃣ PERMISSION CHECK LOGIC (Simplified)

```
┌─────────────────────────────────────┐
│ Kapan Permission Dicek?             │
└─────────────────────────────────────┘
                │
                ├─ (1) Route Middleware ────────────────────┐
                │     (saat akses URL)                       │
                │                                            │
                ├─ (2) Blade Template (View)────────────────┤
                │     (saat render sidebar/menu)             │
                │                                            │
                └─ (3) Custom Logic────────────────────────┘
                      (di controller jika perlu)

┌──────────────────────────────────────────────────────────┐
│ (1) Route Middleware Permission Check                    │
│                                                          │
│ routes/web.php:                                          │
│ Route::post('packages',                                  │
│   [PackageWebController::class, 'store'])                │
│   ->middleware('permission:packages.create')             │
│                                                          │
│ Saat user POST ke route ini:                             │
│ 1. PermissionMiddleware terjalankan                      │
│ 2. Check: $user->hasPermission('packages.create')        │
│ 3. true  → Continue ke controller                        │
│    false → Return 403 Forbidden                          │
│                                                          │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│ (2) Blade Template Permission Check                      │
│                                                          │
│ resources/views/layouts/admin.blade.php:                 │
│                                                          │
│ @if($u->hasPermission('packages.view'))                 │
│   <a href="/admin/packages">Packages</a>                 │
│ @endif                                                   │
│                                                          │
│ Saat blade template di-render:                           │
│ 1. Blade engine check @if condition                      │
│ 2. Call $u->hasPermission('packages.view')               │
│ 3. true  → Render menu item                              │
│    false → Skip (menu hidden)                            │
│                                                          │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│ (3) Custom Controller Check (optional)                   │
│                                                          │
│ app/Http/Controllers/Web/Admin/PackageWebController.php: │
│                                                          │
│ public function store(Request $request) {                │
│   // Double-check di controller (optional)               │
│   if (!auth()->user()->hasPermission('packages.create')) │
│     abort(403);                                          │
│                                                          │
│   // Proceed dengan logic                                │
│   ...                                                    │
│ }                                                        │
│                                                          │
│ Best practice: Rely on middleware jangan di controller   │
│                                                          │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│ Simplified Logic: hasPermission() Query                  │
│                                                          │
│ $user->hasPermission($permission_name)                   │
│    │                                                     │
│    ├─ Get user's active roles                            │
│    │  ├─ SELECT role_id FROM user_roles                  │
│    │  │  WHERE user_id = $user_id                        │
│    │  │  AND is_active = 1                               │
│    │  └─ Result: [admin, instruktur] atau [instruktur]   │
│    │                                                     │
│    ├─ For each role, check permissions                   │
│    │  ├─ SELECT permission_id FROM role_permissions      │
│    │  │  WHERE role_id IN ($role_ids)                    │
│    │  │  AND permission_name = $permission_name          │
│    │  └─ Result: Found or Not Found                      │
│    │                                                     │
│    └─ Return true/false                                  │
│       ├─ Found → true (permission exists)                │
│       └─ Not found → false (permission denied)           │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

---

## 6️⃣ DATABASE RELATIONSHIPS

```
┌──────────────┐
│    users     │ 1 user bisa punya 1+ roles
└──────────────┘
      │
      │ id_user
      │
┌─────┴─────────────┐
│  user_roles       │ (junction table)
└─────┬─────────────┘
      │
      │ id_role
      │
┌─────┴──────────┐
│     roles      │ 1 role bisa punya 1+ permissions
└────────────────┘
      │
      │ id_role
      │
┌─────┴──────────────────┐
│  role_permissions      │ (junction table)
└─────┬──────────────────┘
      │
      │ id_permission
      │
┌─────┴────────────────┐
│    permissions       │
└──────────────────────┘

┌────────────────────────────────────────────┐
│ Example Data                               │
├────────────────────────────────────────────┤
│                                            │
│ users table:                               │
│ id | nama           | email                │
│ 1  | Admin Pilates  | admin@pilates.com    │
│ 2  | Budi Instruktur| budi@pilates.com     │
│                                            │
│ roles table:                               │
│ id | nama_role                             │
│ 1  | admin                                 │
│ 2  | instruktur                            │
│ 3  | pelanggan                             │
│                                            │
│ user_roles table:                          │
│ id_user | id_role | is_active              │
│ 1       | 1       | 1         (admin user)  │
│ 2       | 2       | 1         (budi=inst.)  │
│                                            │
│ permissions table:                         │
│ id | nama_permission  | deskripsi           │
│ 1  | dashboard.view   | Lihat dashboard     │
│ 2  | packages.view    | Lihat packages      │
│ 3  | packages.create  | Buat package        │
│ 4  | packages.update  | Edit package        │
│ 5  | packages.delete  | Hapus package       │
│ 6  | jadwal_kelas.view| Lihat jadwal        │
│ 7  | absensi.manage   | Input absensi       │
│ ...                                        │
│                                            │
│ role_permissions table:                    │
│ id_role | id_permission                    │
│ 1       | 1 (admin → dashboard.view)        │
│ 1       | 2 (admin → packages.view)         │
│ 1       | 3 (admin → packages.create)       │
│ ... (admin has all)                        │
│                                            │
│ 2       | 1 (instruktur → dashboard.view)   │
│ 2       | 6 (instruktur → jadwal_kelas.view)│
│ 2       | 7 (instruktur → absensi.manage)   │
│ ... (instruktur has limited)                │
│                                            │
└────────────────────────────────────────────┘
```

---

## 7️⃣ COMPLETE REQUEST LIFECYCLE

```
┌────────────────────────────────────────────────────────────┐
│ User (instruktur) membuka admin panel                      │
│ Browser GET /admin/packages                                │
└──────────────────┬─────────────────────────────────────────┘
                   │
                   ↓
        ┌──────────────────────┐
        │ 1. Routing           │
        │ (routes/web.php)     │
        │                      │
        │ Router match URL:    │
        │ GET /admin/packages  │
        │ → PackageController  │
        │   ::index()          │
        │                      │
        │ Middleware stack:    │
        │ - auth               │
        │ - role.admin         │
        │ - permission:packages│
        │   .view              │
        └──────────────┬───────┘
                       │
                       ↓
        ┌──────────────────────────────────────────┐
        │ 2. Middleware: auth                      │
        │                                          │
        │ if (auth()->check()) {                   │
        │   return $next($request);                │
        │ }                                        │
        │ return redirect('login');                │
        │                                          │
        │ User login? YES → continue               │
        │                → NO → Redirect login     │
        │                                          │
        │ Budi sudah login ✓ → continue            │
        └──────────────┬───────────────────────────┘
                       │
                       ↓
        ┌──────────────────────────────────────────┐
        │ 3. Middleware: role.admin                │
        │                                          │
        │ $userRoles = $user->roles()              │
        │   ->wherePivot('is_active', 1)           │
        │   ->pluck('nama_role')                   │
        │   ->toArray();                           │
        │                                          │
        │ if (in_array('admin', $userRoles)) {     │
        │   return $next($request);                │
        │ }                                        │
        │ return 403;                              │
        │                                          │
        │ Query:                                   │
        │ SELECT nama_role FROM roles              │
        │ JOIN user_roles ON ...                   │
        │ WHERE id_user = 2 (Budi)                 │
        │ AND is_active = 1                        │
        │                                          │
        │ Result: ['instruktur']                   │
        │                                          │
        │ Is 'admin' in ['instruktur']? NO         │
        │ → 403 Forbidden ✗                        │
        │ Request blocked!                         │
        │                                          │
        │ Budi TIDAK bisa lanjut ✗                │
        └──────────────┬───────────────────────────┘
                       │
                       ↓
        ┌──────────────────────────────────────────┐
        │ 4. Response                              │
        │                                          │
        │ HTTP 403 Forbidden                       │
        │                                          │
        │ Body: "Anda tidak memiliki akses ke      │
        │       halaman ini."                      │
        │                                          │
        │ User di-block di role middleware,        │
        │ tidak sampai ke permission middleware    │
        └──────────────────────────────────────────┘

---

┌────────────────────────────────────────────────────────────┐
│ CONTRAST: Admin user membuka panel yang sama               │
│ Browser GET /admin/packages                                │
└──────────────────┬─────────────────────────────────────────┘
                   │
                   ↓
        ┌──────────────────────┐
        │ 1. Routing           │
        │ Same as above        │
        └──────────────┬───────┘
                       │
                       ↓
        ┌──────────────────────┐
        │ 2. auth middleware   │
        │ Admin logged in? YES  │
        │ → continue           │
        └──────────────┬───────┘
                       │
                       ↓
        ┌──────────────────────────────────────┐
        │ 3. role.admin middleware             │
        │                                      │
        │ $userRoles = ['admin']                │
        │                                      │
        │ Is 'admin' in ['admin']? YES          │
        │ → continue                           │
        │                                      │
        │ Admin passed ✓                       │
        └──────────────┬──────────────────────┘
                       │
                       ↓
        ┌──────────────────────────────────────────┐
        │ 4. PermissionMiddleware                  │
        │                                          │
        │ if ($user->hasPermission(                │
        │       'packages.view'                    │
        │     )) {                                 │
        │   return $next($request);                │
        │ }                                        │
        │ return 403;                              │
        │                                          │
        │ Query:                                   │
        │ SELECT EXISTS (                          │
        │   SELECT 1 FROM roles                    │
        │   JOIN user_roles ON ...                 │
        │   JOIN role_permissions ON ...           │
        │   JOIN permissions ON ...                │
        │   WHERE id_user = 1 (admin)              │
        │   AND is_active = 1                      │
        │   AND nama_permission =                  │
        │       'packages.view'                    │
        │ )                                        │
        │                                          │
        │ Result: EXISTS → true                    │
        │ Admin punya permission ✓                │
        │ → continue                              │
        └──────────────┬───────────────────────────┘
                       │
                       ↓
        ┌──────────────────────────────────────────┐
        │ 5. Controller: PackageWebController      │
        │                                          │
        │ public function index() {                │
        │   $packages = Package::all();             │
        │   return view('admin.packages.index',     │
        │     compact('packages')                  │
        │   );                                     │
        │ }                                        │
        │                                          │
        │ Execute normal logic                    │
        └──────────────┬───────────────────────────┘
                       │
                       ↓
        ┌──────────────────────────────────────────┐
        │ 6. View rendering                        │
        │ (resources/views/admin/packages/index)   │
        │                                          │
        │ @php $u = auth()->user(); @endphp       │
        │                                          │
        │ <!-- Render sidebar with menus -->       │
        │ @if($u->hasPermission('packages.view'))  │
        │   <show packages menu>                   │
        │ @endif                                   │
        │                                          │
        │ Permission check while rendering        │
        │ → Admin punya permission ✓               │
        │ → Packages menu ditampilkan              │
        │                                          │
        │ <!-- Render main content -->             │
        │ <table of packages>                      │
        └──────────────┬───────────────────────────┘
                       │
                       ↓
        ┌──────────────────────────────────────────┐
        │ 7. HTTP Response                         │
        │                                          │
        │ HTTP 200 OK                              │
        │                                          │
        │ Body: HTML page dengan:                  │
        │ - Sidebar menu (Packages ditampilkan)    │
        │ - Packages list table                    │
        │ - Semuanya visible karena punya access   │
        └──────────────────────────────────────────┘
```

---

## 8️⃣ SUMMARY CHECKLIST

```
┌─ SETUP ─────────────────────────────────────────────┐
│ ☐ Permission didefinisikan di AdminSeeder         │
│ ☐ Permission diassign ke Role                     │
│ ☐ Role diassign ke User                           │
│ ☐ Database seeded (composer setup)                │
└─────────────────────────────────────────────────────┘

┌─ ROUTE PROTECTION ──────────────────────────────────┐
│ ☐ Route punya middleware 'auth'                    │
│ ☐ Route punya middleware 'role.{role}'             │
│ ☐ Route punya middleware 'permission:{perm}'       │
│ ☐ Middleware dijalankan dalam order yang benar     │
└─────────────────────────────────────────────────────┘

┌─ PERMISSION CHECKING ───────────────────────────────┐
│ ☐ User::hasPermission() method dijalankan          │
│ ☐ Query check roles + permissions dari database    │
│ ☐ Return true jika ada, false jika tidak           │
└─────────────────────────────────────────────────────┘

┌─ MENU RENDERING ────────────────────────────────────┐
│ ☐ Sidebar check permission sebelum render menu     │
│ ☐ Menggunakan @if($user->hasPermission())          │
│ ☐ Menu hidden jika tidak ada permission            │
└─────────────────────────────────────────────────────┘

┌─ USER EXPERIENCE ───────────────────────────────────┐
│ ☐ User hanya bisa akses route sesuai permission    │
│ ☐ Menu hanya ditampilkan sesuai permission         │
│ ☐ Forbidden page jika akses unauthorized           │
│ ☐ Sidebar dinamis berdasarkan permission user      │
└─────────────────────────────────────────────────────┘
```

