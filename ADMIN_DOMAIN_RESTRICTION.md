# Admin Domain Restriction - Additional Security Layer

## Update Summary

### Problem
Admin users could manually change URL to access public domain (homepage, classes, packages, articles, etc.) and experience the site as a regular user.

### Solution
Created `BlockAdminFromPublic` middleware to enforce that:
1. **Admin users can ONLY access `/admin/*` routes**
2. **Any attempt to access public routes redirects to `/admin/dashboard`**
3. **Admin cannot access pelanggan login/register pages**

### Implementation

**New Middleware:** `app/Http/Middleware/BlockAdminFromPublic.php`
```php
// Checks if authenticated user has 'admin' role
// If yes, redirects to /admin/dashboard
// If no, allows normal flow
```

**Routes Protected:** (via `middleware('block.admin')`)
- `/` (homepage)
- `/classes` and `/classes/{id}/schedule`
- `/packages`, `/packages/{id}/checkout`, `/packages/{id}/process`, `/promo/check`
- `/articles` and `/articles/{id}`
- `/contact`
- `/login` and `/register` (pelanggan auth)

### Behavior

| User Type | Route | Action |
|-----------|-------|--------|
| Admin | `/` | → Redirect to `/admin/dashboard` |
| Admin | `/classes` | → Redirect to `/admin/dashboard` |
| Admin | `/packages` | → Redirect to `/admin/dashboard` |
| Admin | `/login` | → Redirect to `/admin/dashboard` |
| Admin | `/admin/dashboard` | → ✅ Allowed |
| Pelanggan | `/` | → ✅ Allowed |
| Pelanggan | `/admin` | → 403 Forbidden (RoleAdmin middleware) |
| Guest | `/` | → ✅ Allowed |
| Guest | `/admin` | → Redirect to `/admin/login` |

### Files Modified
- `app/Http/Middleware/BlockAdminFromPublic.php` - **CREATED**
- `bootstrap/app.php` - Added middleware alias
- `routes/web.php` - Applied middleware to public routes

### Testing

```bash
# 1. Login as admin@pilates.com / admin123
# 2. Try to visit http://localhost:8000/
#    Expected: Redirect to /admin/dashboard

# 3. Try to visit http://localhost:8000/packages
#    Expected: Redirect to /admin/dashboard

# 4. Try to visit http://localhost:8000/classes
#    Expected: Redirect to /admin/dashboard

# 5. Try to visit http://localhost:8000/login
#    Expected: Redirect to /admin/dashboard

# 6. Visit http://localhost:8000/admin/dashboard
#    Expected: ✅ Works, shows admin panel
```

## Complete Authorization Matrix

After this fix + previous RoleAdmin/RolePelanggan implementation:

```
┌─────────────────┬──────────────────┬──────────────────┬────────────────┐
│ Route           │ Admin User       │ Pelanggan User   │ Guest          │
├─────────────────┼──────────────────┼──────────────────┼────────────────┤
│ /               │ → /admin/dash    │ ✅ Allowed       │ ✅ Allowed     │
│ /classes        │ → /admin/dash    │ ✅ Allowed       │ ✅ Allowed     │
│ /packages       │ → /admin/dash    │ ✅ Allowed       │ ✅ Allowed     │
│ /login          │ → /admin/dash    │ → /admin/dash    │ ✅ Allowed     │
│ /profile        │ → /admin/dash    │ ✅ Allowed       │ → /login       │
│ /booking        │ → /admin/dash    │ ✅ Allowed       │ → /login       │
│ /admin/*        │ ✅ Allowed       │ 403 Forbidden    │ → /admin/login │
└─────────────────┴──────────────────┴──────────────────┴────────────────┘
```

This creates complete role-based domain separation:
- **Admin domain**: Only admin users, only `/admin/*` area
- **Public domain**: Anyone, but admin redirected to admin area
- **Pelanggan area**: Only pelanggan users
