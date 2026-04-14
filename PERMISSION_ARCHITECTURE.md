# Permission System - Visual Architecture

## Permission Lifecycle

```
┌─────────────────────────────────────────────────────────────────┐
│                    PERMISSION LIFECYCLE                         │
└─────────────────────────────────────────────────────────────────┘

1. PLANNING PHASE
   ↓
   Decide what new functionality needed
   Example: "Need ability to publish articles"
   
2. PERMISSION CREATION (Admin Panel or Seeder)
   ↓
   ┌──────────────────────────────────────────┐
   │ CREATE Permission                        │
   ├──────────────────────────────────────────┤
   │ nama_permission: "artikel.publish"       │
   │ deskripsi: "Publikasi artikel"           │
   │ Database: permissions table              │
   └──────────────────────────────────────────┘
   
3. ROLE ASSIGNMENT (Admin Panel)
   ↓
   ┌──────────────────────────────────────────┐
   │ ASSIGN to Role                           │
   ├──────────────────────────────────────────┤
   │ Role: "editor"                           │
   │ Check: artikel.publish ✓                 │
   │ Database: role_permissions table         │
   └──────────────────────────────────────────┘
   
4. ROUTE BINDING (Code: routes/web.php)
   ↓
   ┌──────────────────────────────────────────┐
   │ ADD Middleware to Route                  │
   ├──────────────────────────────────────────┤
   │ Route::post('/artikel/{id}/publish',     │
   │     [ArtikelController::class, 'pub']    │
   │     ->middleware('permission:artikel.pub'│
   └──────────────────────────────────────────┘
   
5. RUNTIME CHECK (When user access route)
   ↓
   ┌──────────────────────────────────────────┐
   │ PermissionMiddleware::handle()           │
   ├──────────────────────────────────────────┤
   │ 1. Get current user                      │
   │ 2. Check: hasPermission('artikel.pub')?  │
   │ 3. Query DB: user roles → permissions   │
   │ 4. Result: Allow OR Redirect with error │
   └──────────────────────────────────────────┘
   
6. ACCESS GRANTED/DENIED
   ↓
   ✅ Permission found → Controller execute → Success
   ❌ Permission not found → Redirect → Error message
```

## Data Flow Diagram

```
┌─────────────┐
│ Admin Panel │
│   (User)    │
└──────┬──────┘
       │
       ├─→ Create Permission
       │   └─→ permissions table ✓
       │
       ├─→ Assign to Role
       │   └─→ role_permissions table ✓
       │
       └─→ TAPI WITHOUT middleware binding
           └─→ Permission ada tapi TIDAK BERFUNGSI ❌


┌─────────────┐
│   Code      │
│  (Developer)│
└──────┬──────┘
       │
       └─→ Add to routes/web.php
           └─→ middleware('permission:artikel.publish')
               └─→ Permission sekarang BERFUNGSI ✅
```

## Permission Check Flow

```
User Access: GET /admin/artikel/1/publish

        ↓

┌──────────────────────────────────┐
│ Route Match                      │
│ GET /admin/artikel/{id}/publish  │
└──────────────────┬───────────────┘
                   ↓
        ┌──────────────────────────┐
        │ Middleware Stack         │
        ├──────────────────────────┤
        │ 1. auth                  │
        │ 2. role.admin            │ ← Check admin role
        │ 3. permission:artikel... │ ← Check permission
        └──────────┬───────────────┘
                   ↓
       ┌───────────────────────────┐
       │ PermissionMiddleware      │
       │ handle()                  │
       ├───────────────────────────┤
       │ 1. $user = auth()->user() │
       │ 2. if (!$user) redirect   │
       │ 3. hasPermission()?       │
       │    └─ Query database      │
       │       user → roles        │
       │       roles → permissions │
       └───────┬─────────────────┬─┘
               │                 │
            YES ✅            NO ❌
               │                 │
               ↓                 ↓
        ┌─────────────┐  ┌────────────────┐
        │ $next()     │  │ redirect()     │
        │ Go to next  │  │ Back/home      │
        │ middleware  │  │ + error msg    │
        └──────┬──────┘  └────────────────┘
               ↓
        ┌─────────────────┐
        │ Controller      │
        │ Action Method   │
        │ (publish)       │
        └──────┬──────────┘
               ↓
        ┌─────────────────┐
        │ Return Response │
        │ Success Message │
        └─────────────────┘
```

## Permission Dependency Chain

```
LEVEL 1: Permission Exists
┌──────────────────────────────────┐
│ Permission in Database           │
│ (permissions table)              │
│ ✓ artikel.publish exists         │
└────────────────┬─────────────────┘
                 │
                 ↓ DEPENDS ON:
                 
LEVEL 2: Role Has Permission
┌──────────────────────────────────┐
│ role_permissions pivot table     │
│ ✓ editor role linked to          │
│   artikel.publish permission     │
└────────────────┬─────────────────┘
                 │
                 ↓ DEPENDS ON:

LEVEL 3: User Has Role
┌──────────────────────────────────┐
│ user_roles pivot table           │
│ ✓ user@editor linked to          │
│   editor role (is_active=true)   │
└────────────────┬─────────────────┘
                 │
                 ↓ DEPENDS ON:

LEVEL 4: Route Has Middleware
┌──────────────────────────────────┐
│ routes/web.php                   │
│ ✓ Route bound with               │
│   middleware('permission:artikel │
│   .publish')                     │
└────────────────┬─────────────────┘
                 │
                 ↓ DEPENDS ON:

LEVEL 5: User Access Route
┌──────────────────────────────────┐
│ User makes request to route      │
│ GET /admin/artikel/1/publish     │
└────────────────┬─────────────────┘
                 │
                 ↓ RESULT:

LEVEL 6: ✅ ACCESS GRANTED
┌──────────────────────────────────┐
│ All conditions met               │
│ Permission check passes          │
│ Controller executes              │
└──────────────────────────────────┘

┌─────────────────────────────────────────┐
│ IF ANY LEVEL MISSING: ❌ ACCESS DENIED  │
├─────────────────────────────────────────┤
│ Missing L1: Permission not in DB        │
│ Missing L2: Role doesn't have perm      │
│ Missing L3: User doesn't have role      │
│ Missing L4: Route no middleware         │
│ Missing L5: User doesn't access route   │
└─────────────────────────────────────────┘
```

## Example: Adding Article Publishing Feature

### Scenario: "We need editors to publish articles"

```
STEP 1: Permission Created ✓
┌─────────────────────────────────┐
│ Admin Panel → Create Permission │
├─────────────────────────────────┤
│ Name: artikel.publish           │
│ Desc: Publikasi artikel         │
└────────────────┬────────────────┘
                 ↓ Database
          [artikel.publish]

STEP 2: Assigned to Role ✓
┌─────────────────────────────────┐
│ Admin Panel → Edit Role (editor)│
├─────────────────────────────────┤
│ ✓ artikel.view                  │
│ ✓ artikel.create                │
│ ✓ artikel.publish ← NEW          │
│ ✓ artikel.delete                │
└────────────────┬────────────────┘
                 ↓ Database
    [editor] has [artikel.publish]

STEP 3: Route Middleware Added ✓
┌─────────────────────────────────┐
│ Developer → Update routes       │
├─────────────────────────────────┤
│ Route::post(                    │
│   '/artikel/{id}/publish',      │
│   [ArticleController, 'pub']    │
│ )->middleware('permission:      │
│    artikel.publish')            │
└────────────────┬────────────────┘
                 ↓ Configuration
    Route bound to permission

STEP 4: NOW Feature Ready ✓
┌─────────────────────────────────┐
│ Editor login and:               │
├─────────────────────────────────┤
│ 1. Click publish button         │
│ 2. POST /artikel/1/publish      │
│ 3. Middleware checks perm       │
│ 4. ✓ Permission found           │
│ 5. Controller executes          │
│ 6. Article published            │
│ 7. Success message              │
└─────────────────────────────────┘


❌ INCOMPLETE: What if we forgot STEP 3?
┌─────────────────────────────────┐
│ Permission created ✓            │
│ Assigned to role ✓              │
│ Route middleware ❌ MISSING      │
├─────────────────────────────────┤
│ Result: Publish button not     │
│ protected! Anyone can access    │
│ the route without permission    │
│ (if route exists without MW)    │
└─────────────────────────────────┘
```

## Permission State Diagram

```
PERMISSION STATES:

State 1: ORPHANED ❌
┌──────────────────────────────────┐
│ ✓ Permission in database         │
│ ✗ Not assigned to any role       │
│ ✗ Not used in any route          │
│ Result: USELESS                  │
└──────────────────────────────────┘

State 2: ASSIGNED BUT UNUSED ❌
┌──────────────────────────────────┐
│ ✓ Permission in database         │
│ ✓ Assigned to role               │
│ ✗ Not used in any route          │
│ Result: USELESS                  │
└──────────────────────────────────┘

State 3: EXPOSED ⚠️
┌──────────────────────────────────┐
│ ✓ Permission in database         │
│ ✗ Not assigned to any role       │
│ ✓ Used in route middleware       │
│ Result: NO ONE HAS ACCESS        │
│ (Feature exists but locked)      │
└──────────────────────────────────┘

State 4: ACTIVE ✅
┌──────────────────────────────────┐
│ ✓ Permission in database         │
│ ✓ Assigned to role               │
│ ✓ Used in route middleware       │
│ Result: WORKS!                   │
└──────────────────────────────────┘

TRANSITION:
Orphaned → Assigned → Active ✓
    ↓         ↓         ↓
   Unused   Exposed   Working!
```

## Summary

```
┌─────────────────────────────────────────────────────────────┐
│             PERMISSION = CONCEPT                            │
│        (Not functional until all 4 levels are ready)       │
├─────────────────────────────────────────────────────────────┤
│  L1: Database Entry        (Admin creates permission)     │
│  L2: Role Assignment       (Admin assigns to role)        │
│  L3: Route Middleware      (Developer adds to routes)     │
│  L4: Runtime Check         (Middleware validates)         │
├─────────────────────────────────────────────────────────────┤
│  If ANY level missing → Permission doesn't work!          │
│  All 4 required for functional permission system          │
└─────────────────────────────────────────────────────────────┘
```
