# 📍 Lokasi Validasi Kredit Tidak Cukup saat Booking

## 🎯 Alur Flow Booking dengan Credit Validation

```
Client Request (POST /booking)
    ↓
BookingController::store()
    ↓
BookingService::book()
    ↓
CreditService::debit()  ← ⚠️ VALIDASI KREDIT TERJADI DI SINI!
    ↓
Jika kredit < cost:
  → Throw BusinessException('Kredit tidak mencukupi', 422)
    ↓
Exception diterima client sebagai error response
    ↓
Frontend menampilkan alert "Kredit tidak mencukupi"
```

---

## 📁 File-File yang Involved

### 1️⃣ **CreditService.php** (Tempat Validasi Terjadi)
📁 **Lokasi:** `/app/Http/Service/CreditService.php`

**Kode yang mengecek kredit:**
```php
public function debit(
    int $idPelanggan,
    int $jumlah,
    string $sumber,
    ?int $idReferensi = null,
    ?string $keterangan = null,
): MutasiKredit {
    // ⚠️ VALIDASI KREDIT DI SINI!
    if ($this->getSaldo($idPelanggan) < $jumlah) {
        throw new BusinessException('Kredit tidak mencukupi', 422);
    }

    // Jika validasi lulus, create mutasi kredit
    return $this->mutasi->create([
        'id_pelanggan'  => $idPelanggan,
        'jenis_mutasi'  => 'debit',
        'jumlah_kredit' => $jumlah,
        'sumber_mutasi' => $sumber,
        'id_referensi'  => $idReferensi,
        'keterangan'    => $keterangan,
    ]);
}
```

**Yang dilakukan:**
- Line 48: Check jika saldo < jumlah kredit yang ingin di-debit
- Line 49: Jika kurang, throw exception dengan pesan "Kredit tidak mencukupi"
- Line 51-58: Jika cukup, create record di tabel `mutasi_kredit`

---

### 2️⃣ **BookingService.php** (Memanggil CreditService)
📁 **Lokasi:** `/app/Http/Service/BookingService.php`

**Kode yang memanggil debit:**
```php
public function book(int $idPelanggan, int $idJadwalKelas, int $kreditCost = 1): Booking
{
    return DB::transaction(function () use ($idPelanggan, $idJadwalKelas, $kreditCost) {
        // ... validasi jadwal dulu ...

        // ⚠️ PANGGIL DEBIT (dan validasi kredit terjadi di sini)
        $this->credit->debit(
            idPelanggan: $idPelanggan,
            jumlah: $kreditCost,
            sumber: 'booking',
            idReferensi: $idJadwalKelas,
            keterangan: 'Booking jadwal #'.$idJadwalKelas,
        );

        // Jika debit berhasil, baru create booking
        $booking = $this->bookings->create([
            'id_pelanggan'    => $idPelanggan,
            'id_jadwal_kelas' => $idJadwalKelas,
            'status_booking'  => BookingStatus::BOOKED->value,
        ]);

        return $booking;
    });
}
```

**Flow:**
- Line 32: Cek jadwal ada & tidak penuh
- Line 34-40: **DEBIT KREDIT** ← Validasi terjadi di sini
  - Jika kredit tidak cukup → Exception thrown
  - Jika kredit cukup → Lanjut ke bawah
- Line 42-47: Jika debit berhasil, create booking record

---

### 3️⃣ **BookingController.php** (API Endpoint)
📁 **Lokasi:** `/app/Http/Controllers/BookingController.php`

**Endpoint yang menerima request:**
```php
public function store(Request $request): JsonResponse
{
    $data = $request->validate([
        'id_pelanggan'    => ['required', 'integer', 'exists:pelanggan,id_pelanggan'],
        'id_jadwal_kelas' => ['required', 'integer', 'exists:jadwal_kelas,id_jadwal_kelas'],
        'kredit_cost'     => ['nullable', 'integer', 'min:1'],
    ]);

    // Panggil booking service
    $booking = $this->bookings->book(
        idPelanggan: (int) $data['id_pelanggan'],
        idJadwalKelas: (int) $data['id_jadwal_kelas'],
        kreditCost: (int) ($data['kredit_cost'] ?? 1),
    );

    // Jika berhasil, return response
    return ApiResponse::created($booking, 'Booking berhasil');
}
```

**Yang dilakukan:**
- Line 15-21: Validate input
- Line 24-29: Call `BookingService->book()`
- Line 31: Return success response
- **Jika exception terjadi** → Error response dikirim ke client

---

### 4️⃣ **MutasiKreditRepository.php** (Query Saldo)
📁 **Lokasi:** `/app/Http/Repository/MutasiKreditRepository.php`

**Kode yang mengecek saldo:**
```php
public function totalSaldo(int $idPelanggan): int
{
    return (int) MutasiKredit::where('id_pelanggan', $idPelanggan)
        ->selectRaw('COALESCE(SUM(CASE WHEN jenis_mutasi = \'credit\' THEN jumlah_kredit ELSE 0 END) - SUM(CASE WHEN jenis_mutasi = \'debit\' THEN jumlah_kredit ELSE 0 END), 0) as saldo')
        ->value('saldo');
}
```

**Yang dilakukan:**
- Query semua mutasi kredit untuk user
- Hitung: (credit + credit + ...) - (debit + debit + ...)
- Return total saldo

---

## 🔄 Detail Step-by-Step Proses

### Scenario: User Booking dengan Kredit Tidak Cukup

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Client Request (Frontend)                                │
│    POST /api/bookings                                       │
│    Body: {                                                  │
│      id_pelanggan: 5,                                       │
│      id_jadwal_kelas: 12,                                   │
│      kredit_cost: 3  ← User ingin gunakan 3 kredit          │
│    }                                                        │
└─────────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. BookingController::store() (Line 15-31)                  │
│    - Validate request ✅                                    │
│    - Call $bookings->book(5, 12, 3)                         │
└─────────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. BookingService::book() (Line 27-56)                      │
│    START TRANSACTION                                        │
│    - Check jadwal exists ✅                                 │
│    - Check kuota penuh ✅                                   │
│    - Call $credit->debit(5, 3, 'booking', ...)              │
└─────────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. CreditService::debit() (Line 48-59) ← VALIDASI TERJADI   │
│    - Get saldo user: getSaldo(5) = 1 kredit                 │
│    - Check: if (1 < 3) ? YES!                               │
│    - ❌ THROW: BusinessException('Kredit tidak mencukupi')  │
└─────────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Exception Caught by Laravel                              │
│    ROLLBACK TRANSACTION (tidak jadi booking)                │
│    Handle exception sebagai HTTP error response             │
└─────────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. Response ke Client (Error)                               │
│    HTTP 422 Unprocessable Entity                            │
│    {                                                        │
│      "success": false,                                      │
│      "message": "Kredit tidak mencukupi",                   │
│      "errors": null                                         │
│    }                                                        │
└─────────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│ 7. Frontend (Vue/React)                                     │
│    - Terima error response                                  │
│    - Display alert/toast: "Kredit tidak mencukupi"          │
│    - User tidak bisa book class                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 Jika Ingin Ubah Pesan Error

Pesan "Kredit tidak mencukupi" didefinisikan di:

📁 **File:** `/app/Http/Service/CreditService.php` (Line 49)

```php
throw new BusinessException('Kredit tidak mencukupi', 422);
```

Jika mau ubah pesan:
```php
// SEBELUM:
throw new BusinessException('Kredit tidak mencukupi', 422);

// SESUDAH:
throw new BusinessException('Saldo kredit Anda tidak cukup untuk booking class ini', 422);
```

---

## 📋 Ringkasan File & Fungsi

| File | Fungsi | Baris |
|------|--------|-------|
| `BookingController.php` | API endpoint booking | 15-31 |
| `BookingService.php` | Business logic booking | 27-56 |
| **CreditService.php** | **✅ VALIDASI KREDIT** | **48-59** |
| `MutasiKreditRepository.php` | Query saldo | - |
| `BusinessException.php` | Custom exception class | - |

---

## ⚠️ Error Handling

### Exception Flow:
```
CreditService::debit()
  → throw BusinessException()
    → Laravel Exception Handler
      → Return JSON error response
        → Client receives 422 status
          → Frontend displays alert
```

### Exception Class:
📁 **File:** `/app/Common/Exception/BusinessException.php`

```php
class BusinessException extends Exception
{
    public function __construct(
        public string $message,
        public int $statusCode = 400,
        public ?array $errors = null,
    ) {
        parent::__construct($this->message);
    }
}
```

---

## 🧪 Cara Test

### Test 1: Cek Saldo User Terlebih Dahulu
```bash
curl -X GET http://localhost:8000/api/pelanggan/kredit/saldo \
  -H "Authorization: Bearer [TOKEN]"

Response:
{
  "success": true,
  "data": {
    "id_pelanggan": 5,
    "saldo_kredit": 1
  }
}
```

### Test 2: Booking dengan Kredit Tidak Cukup
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer [TOKEN]" \
  -H "Content-Type: application/json" \
  -d '{
    "id_pelanggan": 5,
    "id_jadwal_kelas": 12,
    "kredit_cost": 3
  }'

Response (Error):
{
  "success": false,
  "message": "Kredit tidak mencukupi",
  "errors": null
}
```

---

## 💡 Catatan Penting

1. **Validation terjadi di CreditService**, bukan di controller
2. **Database transaction** memastikan atomicity (semua jadi atau semua tidak jadi)
3. **Pesan error dilempar sebagai BusinessException** dan di-handle oleh Laravel
4. **Saldo dihitung real-time** dari tabel `mutasi_kredit` (single source of truth)
5. **Tidak boleh debit langsung ke `pembelian_package.sisa_kredit`** - harus through CreditService

---

**Kesimpulannya:** Jika user ingin booking tapi kredit tidak cukup, **validasi terjadi di `CreditService::debit()`** (line 48-49) dan throw exception yang kemudian menjadi error response ke frontend! 🎯
