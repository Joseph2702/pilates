# Femm Pilates - User Action Flowchart

## 1. PELANGGAN (Customer) Flow

```
┌─────────────────┐
│   PUBLIC HOME   │
└────────┬────────┘
         │
    ┌────▼────┐
    │ LOGIN?  │
    └────┬────┘
         │
    ┌────▼──────────────────┐
    │ REGISTER / LOGIN PAGE │
    └────┬──────────────────┘
         │
    ┌────▼────────────────────────────────┐
    │     PELANGGAN DASHBOARD/PROFILE      │
    │ - My Profile (View, Edit, Change PW) │
    │ - My Schedule (Bookings, Done)        │
    │ - My Packages (Active, History)       │
    │ - My Transactions (Payment History)   │
    └────┬────────────────────────────────┘
         │
         ├─────────────────┬──────────────┬──────────────┬─────────────┐
         │                 │              │              │             │
    ┌────▼────┐     ┌──────▼───┐   ┌────▼────┐   ┌────▼────┐   ┌────▼────┐
    │  CLASSES │     │ PACKAGES │   │ ARTICLES│   │ BOOKING │   │ PROFILE │
    │ (Browse) │     │(Browse)  │   │ (Read)  │   │ (Detail)│   │ (Manage)│
    └────┬────┘     └──────┬───┘   └────┬────┘   └────┬────┘   └────┬────┘
         │                 │            │             │            │
         │            ┌────▼────┐      │        ┌────▼──────────┐  │
         │            │ CHECKOUT│      │        │ REVIEW BOOKING│  │
         │            │ PAYMENT  │      │        │ - Confirm or  │  │
         │            │ (Midtrans)      │        │   Go Back     │  │
         │            └────┬────┘      │        └────┬──────────┘  │
         │                 │            │             │            │
    ┌────▼──────┐    ┌─────▼──┐       │        ┌────▼────┐        │
    │SELECT DATE│    │SUCCESS? │       │        │ BOOKING │        │
    │ & TIME    │    └────┬────┘       │        │ CONFIRMED       │
    │SCHEDULE   │         │            │        │ + CREDIT DEBIT  │
    └────┬──────┘    ┌────▼─┐         │        └────┬────┘        │
         │           │ PAID │         │             │            │
         │           └──────┘         │        ┌────▼────┐        │
         │                            │        │MY SCHEDULE       │
    ┌────▼──────┐                    │        │ (View Booking)   │
    │BOOK NOW   │                    │        └────┬────┘        │
    │REVIEW     │                    │             │            │
    │BOOKING    │                    │        ┌────▼────┐        │
    └────┬──────┘                    │        │ CANCEL? │        │
         │                           │        └────┬────┘        │
         ├───H-1 or earlier──────────┼────────►   │            │
         │   (with refund)           │            │            │
         │                      ┌────▼───────┐    │            │
         │                      │ BOOKING    │    │            │
         │                      │ CONFIRMED  │    │            │
         │                      │ -1 CREDIT  │    │            │
         │                      └────────────┘    │            │
         │                                        │            │
         │                                   ┌────▼────┐        │
         │                                   │ REFUND  │        │
         │                                   │ IF H-1  │        │
         │                                   └─────────┘        │
         │                                                       │
         └───No refund if same day──────────────────────────────┘
         │
    ┌────▼─────────────────────┐
    │    LOGOUT / EXIT APP     │
    └──────────────────────────┘
```

### Pelanggan Key Actions:
- ✅ **Browse Classes** → View schedule by date & instructor
- ✅ **Book Class** → Review booking → Confirm → Credit deducted
- ✅ **Cancel Class** → H-1 or earlier = REFUND | Same day = NO REFUND
- ✅ **Buy Package** → Select package → Input promo → Checkout (Midtrans) → Payment
- ✅ **View Bookings** → Status: BOOKED, DONE, CANCELLED
- ✅ **Edit Profile** → Nama, No HP, Jenis Kelamin, Tempat Lahir, Tanggal Lahir
- ✅ **Change Password**

---

## 2. INSTRUKTUR (Instructor) Flow

```
┌─────────────────┐
│ PUBLIC LOGIN    │
│/instruktur/login│
└────────┬────────┘
         │
    ┌────▼──────────────┐
    │ INSTRUKTUR LOGIN  │
    │ Validate role     │
    └────┬──────────────┘
         │
    ┌────▼────────────────────────────┐
    │  INSTRUKTUR DASHBOARD/PROFILE    │
    │ (routes: /instruktur/profile/*) │
    │ - My Profile (View, Edit, PW)    │
    │ - My Schedule (All Classes)       │
    └────┬────────────────────────────┘
         │
         ├──────────────┬──────────────┐
         │              │              │
    ┌────▼────┐    ┌────▼─────┐  ┌────▼────┐
    │ PROFILE │    │SCHEDULE   │  │ EXIT    │
    │ (Manage)│    │(My Classes)   │(Logout) │
    └────┬────┘    └────┬─────┘  └────┬────┘
         │              │            │
    ┌────▼────────┐     │       ┌─────▼─────┐
    │ VIEW/EDIT   │     │       │ INVALIDATE│
    │ - Info      │     │       │ SESSION   │
    │ - No HP     │     │       └───────────┘
    │ - Personal  │     │
    │ - Specialization
    └────┬────────┘     │
         │              │
    ┌────▼──────────┐   │
    │ CHANGE PASSWORD    │
    └────┬──────────┘   │
         │              │
    ┌────▼───────────┐  │
    │ SAVE/CANCEL    │  │
    └────┬───────────┘  │
         │              │
         │         ┌────▼──────────────┐
         │         │ VIEW ALL CLASSES  │
         │         │ (Filter by date)  │
         │         └────┬──────────────┘
         │              │
         │         ┌────▼──────────────┐
         │         │ SELECT A CLASS    │
         │         │ (upcoming/today)  │
         │         └────┬──────────────┘
         │              │
         │         ┌────▼──────────┐
         │         │ CLASS DETAIL  │
         │         │ - Info        │
         │         │ - Peserta List│
         │         │ - Absensi     │
         │         └────┬──────────┘
         │              │
         │         ┌────▼──────────┐
         │         │ TODAY'S CLASS?│
         │         └────┬────┬─────┘
         │         TIDAK  │  ADA
         │              │  │
         │              │  ┌────────────────────┐
         │              │  │ KELOLA ABSENSI     │
         │              │  │ (manage attendance)│
         │              │  └────┬───────────────┘
         │              │       │
         │              │  ┌────▼────────────────┐
         │              │  │ INPUT KEHADIRAN PER │
         │              │  │ PESERTA:            │
         │              │  │ - HADIR             │
         │              │  │ - TIDAK HADIR       │
         │              │  │ - SAVE              │
         │              │  └────┬───────────────┘
         │              │       │
         │              │  ┌────▼────────┐
         │              │  │ SUCCESS MSG │
         │              │  │ BACK TO     │
         │              │  │ SCHEDULE    │
         │              │  └────────────┘
         │              │
         └──────────────┴──────────────┘
                 │
         ┌───────▼────────┐
         │  LOGOUT / EXIT │
         └────────────────┘
```

### Instruktur Key Actions:
- ✅ **View Profile** → Nama, Email, No HP, Personal Details, Spesialisasi
- ✅ **Edit Profile** → Update info + specialization
- ✅ **Change Password**
- ✅ **View My Schedule** → All classes taught (with status badges: Done, Hari Ini, Upcoming)
- ✅ **View Class Detail** → Time, Peserta count, Absensi status
- ✅ **Manage Absensi** (Today's class only) → Mark peserta as HADIR or TIDAK HADIR
- ✅ **Logout**

---

## 3. ADMIN Flow

```
┌──────────────┐
│ /ADMIN/LOGIN │
└────────┬─────┘
         │
    ┌────▼────────────────┐
    │   ADMIN LOGIN       │
    │ Validate role + RBAC│
    └────┬───────────────┘
         │
    ┌────▼─────────────────────────────────┐
    │     ADMIN DASHBOARD (HOME)            │
    │ - Overview (Stats, Recent Activity)   │
    │ - Quick Access to all modules         │
    └────┬─────────────────────────────────┘
         │
         ├─────────┬────────┬────────┬────────┬────────┬────────┐
         │         │        │        │        │        │        │
    ┌────▼──┐ ┌───▼───┐ ┌──▼───┐ ┌─▼────┐ ┌─▼────┐ ┌─▼────┐ ┌─▼────┐
    │MASTER │ │JADWAL │ │BOOKING│ │ABSENSI│ │ARTICLE│ │ACTIVITY│ │ROLES &│
    │ DATA  │ │KELAS  │ │       │ │      │ │      │ │ LOG   │ │PERMS │
    └────┬──┘ └───┬───┘ └──┬───┘ └─┬────┘ └─┬────┘ └─┬────┘ └─┬────┘
         │        │        │       │        │        │        │
    ┌────▼────────────┐   │       │        │        │        │
    │ PELANGGAN       │   │       │        │        │        │
    │ - View List     │   │       │        │        │        │
    │ - View Detail   │   │       │        │        │        │
    │ - View Credit   │   │       │        │        │        │
    │ - Search/Filter │   │       │        │        │        │
    └────┬────────────┘   │       │        │        │        │
         │                │       │        │        │        │
    ┌────▼────────────┐   │       │        │        │        │
    │ INSTRUKTUR      │   │       │        │        │        │
    │ - View List     │   │       │        │        │        │
    │ - Create NEW    │   │       │        │        │        │
    │ - Edit Info     │   │       │        │        │        │
    │ - Delete        │   │       │        │        │        │
    └────┬────────────┘   │       │        │        │        │
         │                │       │        │        │        │
    ┌────▼────────────┐   │       │        │        │        │
    │ PACKAGES        │   │       │        │        │        │
    │ - CRUD          │   │       │        │        │        │
    │ - Pricing       │   │       │        │        │        │
    │ - Validity      │   │       │        │        │        │
    └────┬────────────┘   │       │        │        │        │
         │                │       │        │        │        │
    ┌────▼────────────┐   │       │        │        │        │
    │ KELAS/PROMO     │   │       │        │        │        │
    │ - CRUD          │   │       │        │        │        │
    │ - Manage        │   │       │        │        │        │
    └────┬────────────┘   │       │        │        │        │
         │                │       │        │        │        │
         │           ┌────▼────────────┐   │        │        │
         │           │ VIEW LIST       │   │        │        │
         │           │ Jadwal Kelas    │   │        │        │
         │           │ - Kelas         │   │        │        │
         │           │ - Instruktur    │   │        │        │
         │           │ - Tanggal/Jam   │   │        │        │
         │           │ - Kuota         │   │        │        │
         │           │ - Status Badge  │   │        │        │
         │           └────┬────────────┘   │        │        │
         │                │                │        │        │
         │           ┌────▼────────────────────────┐ │        │
         │           │ CREATE/EDIT/DELETE JADWAL   │ │        │
         │           │ - Assign kelas & instruktur │ │        │
         │           │ - Set date/time             │ │        │
         │           │ - Set capacity              │ │        │
         │           └────┬─────────────────────────┘ │        │
         │                │                          │        │
         │                │                     ┌────▼────────────────┐
         │                │                     │ VIEW BOOKINGS LIST  │
         │                │                     │ (Filter by status)  │
         │                │                     │ - BOOKED            │
         │                │                     │ - DONE              │
         │                │                     │ - CANCELLED         │
         │                │                     └────┬───────────────┘
         │                │                          │
         │                │                     ┌────▼───────────────┐
         │                │                     │ VIEW BOOKING DETAIL│
         │                │                     │ - Customer Info    │
         │                │                     │ - Class Info       │
         │                │                     │ - Absensi Status   │
         │                │                     └────┬───────────────┘
         │                │                          │
         │                │                          │
         │                │                     ┌────▼────────────────┐
         │                │                     │ VIEW ABSENSI RECORD │
         │                │                     │ (Per jadwal/booking)│
         │                │                     │ - Status kehadiran  │
         │                │                     └────┬───────────────┘
         │                │                          │
         │                │                          │
         │                │                     ┌────▼───────────────┐
         │                │                     │ CREATE/EDIT ARTIKEL│
         │                │                     │ - Judul            │
         │                │                     │ - URL or Upload    │
         │                │                     │ - Content (long)   │
         │                │                     │ - Publish date     │
         │                │                     └────┬───────────────┘
         │                │                          │
         │                │                          │
         │                │                     ┌────▼──────────────┐
         │                │                     │ ACTIVITY LOG VIEW  │
         │                │                     │ Admin activities   │
         │                │                     │ only (no login log)│
         │                │                     │ - Module/Action    │
         │                │                     │ - Keterangan       │
         │                │                     │ - Timestamp        │
         │                │                     └────┬───────────────┘
         │                │                          │
         │                │                          │
         │                │                     ┌────▼──────────────┐
         │                │                     │ MANAGE ROLES       │
         │                │                     │ & PERMISSIONS      │
         │                │                     │ - View/Edit roles  │
         │                │                     │ - Assign granular  │
         │                │                     │   permissions      │
         │                │                     │ - CRUD matrix      │
         │                │                     └────┬───────────────┘
         │                │                          │
         └────────────────┴──────────────────────────┴──────────────┐
                                                                    │
                                                            ┌───────▼────────┐
                                                            │ LOGOUT / EXIT  │
                                                            └────────────────┘
```

### Admin Key Actions - Master Data:
- ✅ **Pelanggan** → View, View Detail, View Credit Balance
- ✅ **Instruktur** → Create (assign dari existing user), Edit, Delete, View
- ✅ **Packages** → Create, Read, Update, Delete
- ✅ **Kelas** → Create, Read, Update, Delete
- ✅ **Promo** → Create, Read, Update, Delete

### Admin Key Actions - Management:
- ✅ **Jadwal Kelas** → Create/Edit (set kelas, instruktur, date, time, quota), Delete, View with status badges (Done/Today/Upcoming)
- ✅ **Bookings** → View List (filter by status), View Detail (customer + class + absensi info)
- ✅ **Absensi** → View records per jadwal/booking
- ✅ **Artikel** → Create (with URL or file upload), Edit, Delete
- ✅ **Activity Log** → View admin activities only (exclude user login/logout)
- ✅ **Roles & Permissions** → Manage granular CRUD permissions per role

---

## 4. Permission Matrix

| Resource | Permission | Pelanggan | Instruktur | Admin |
|----------|-----------|-----------|-----------|-------|
| **Profile** | view | ✅ | ✅ | ✅ |
| | update | ✅ | ✅ | ❌ |
| | change_password | ✅ | ✅ | ❌ |
| **Booking** | create | ✅ | ❌ | ❌ |
| | view | ✅ | ❌ | ✅ |
| | cancel | ✅ (own only) | ❌ | ❌ |
| **Package** | view | ✅ | ❌ | ✅ |
| | purchase | ✅ | ❌ | ❌ |
| | create/edit/delete | ❌ | ❌ | ✅ |
| **Jadwal Kelas** | view | ✅ (public only) | ✅ (own only) | ✅ |
| | create/edit/delete | ❌ | ❌ | ✅ |
| **Absensi** | manage (today) | ❌ | ✅ | ✅ |
| | view | ✅ (own) | ✅ (own) | ✅ |
| **Artikel** | view | ✅ | ❌ | ✅ |
| | create/edit/delete | ❌ | ❌ | ✅ |
| **Dashboard** | view | ✅ | ✅ | ✅ |
| **Activity Log** | view | ❌ | ❌ | ✅ (admin only) |
| **Roles & Permissions** | manage | ❌ | ❌ | ✅ |

---

## 5. Status & State Machine

### Booking Status Flow
```
BOOKED (just created)
  │
  ├─► DONE (class date passed, if attended)
  │
  ├─► CANCELLED (pelanggan cancel before class)
  │   ├─► REFUND (if H-1 or earlier)
  │   └─► NO REFUND (if same day or after)
```

### Jadwal Status Flow
```
UPCOMING (future class)
  │
  ├─► HARI INI (today's class)
  │
  └─► DONE (past class)
```

### Jadwal Availability
```
AVAILABLE (kuota_terisi < kuota_maksimal)
  │
  └─► FULLY BOOKED (kuota_terisi >= kuota_maksimal)
```

---

## 6. Key Business Rules

| Rule | Enforcement | Check Point |
|------|------------|-------------|
| **Duplicate Booking** | User cannot book same class twice | BookingService::book() |
| **Capacity Check** | Jadwal cannot exceed kuota_maksimal | BookingService::book() |
| **Credit Check** | User must have >= 1 credit to book | CreditService::debit() |
| **Cancellation Refund** | H-1 or earlier = refund; same day or after = no refund | BookingService::cancel() |
| **Phone Validation** | Registration: digits only, unique | AuthWebPelangganController |
| **Activity Log** | Only admin actions logged (exclude user login/logout) | ActivityLogWebController |
| **Absensi Management** | Instruktur can only mark attendance for today's classes | AbsensiInstrukturController |
| **Grade-based Visibility** | Users see only their own bookings/profile | Repository queries |

---

## 7. API-like Endpoints Summary

### Pelanggan
- `GET /` - Home
- `GET /classes` - Browse classes
- `GET /classes/{id}/schedule` - Class schedule
- `GET /booking/review/{id}` - **NEW** Review booking
- `POST /booking` - Create booking
- `PATCH /booking/{id}/cancel` - Cancel booking
- `GET /profile` - Dashboard
- `GET /profile/bookings/{id}` - **NEW** Booking detail
- `GET /profile/schedule` - My schedule
- `PUT /profile` - Edit profile
- `PUT /profile/password` - Change password
- `GET /packages` - Browse packages
- `POST /packages/{id}/process` - Buy package
- `GET /articles` - Articles list
- `GET /articles/{id}` - Article detail

### Instruktur
- `GET /instruktur/` - Redirect to profile
- `GET /instruktur/profile` - Profile
- `PUT /instruktur/profile` - Edit profile
- `PUT /instruktur/profile/password` - Change password
- `GET /instruktur/jadwal` - My schedule
- `GET /instruktur/jadwal/{id}` - Class detail
- `GET /instruktur/absensi/{id}` - Mark attendance
- `POST /instruktur/absensi` - Save attendance

### Admin
- `GET /admin/` - Dashboard
- `GET /admin/pelanggan` - List customers
- `GET /admin/instruktur` - List instructors
- `POST /admin/instruktur` - Create instructor (assign from user)
- `PUT /admin/instruktur/{id}` - Edit instructor
- `DELETE /admin/instruktur/{id}` - Delete instructor
- `GET /admin/packages` - List packages
- `POST/PUT/DELETE /admin/packages` - CRUD packages
- `GET /admin/jadwal-kelas` - List schedules
- `POST/PUT/DELETE /admin/jadwal-kelas` - CRUD schedules
- `GET /admin/bookings` - List bookings
- `GET /admin/bookings/{id}` - Booking detail
- `GET /admin/artikel` - List articles
- `POST/PUT/DELETE /admin/artikel` - CRUD articles
- `GET /admin/activity-logs` - View admin activity log
- `GET /admin/roles` - Manage roles & permissions
