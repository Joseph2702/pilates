<?php

namespace Tests\Feature;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Booking;
use App\Domain\Entity\Instruktur;
use App\Domain\Entity\JadwalKelas;
use App\Domain\Entity\Kelas;
use App\Domain\Entity\MutasiKredit;
use App\Domain\Entity\Pelanggan;
use App\Domain\Entity\User;
use App\Domain\Enums\BookingStatus;
use App\Http\Service\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature test untuk BookingService.
 *
 * Konsep: test ini menggunakan DB nyata (SQLite in-memory).
 * RefreshDatabase → setiap test dapat DB bersih (rollback setelah test).
 */
class BookingTest extends TestCase
{
    use RefreshDatabase;

    private Pelanggan $pelanggan;

    private JadwalKelas $jadwal;

    /**
     * Setup data yang dibutuhkan sebelum setiap test.
     * Ini yang disebut "test fixture".
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Buat user + pelanggan
        $user = User::factory()->create(['status' => 'active']);
        $this->pelanggan = Pelanggan::create([
            'id_user' => $user->id_user,
            'tanggal_daftar' => now(),
        ]);

        // Buat instruktur (dummy)
        $instrukturUser = User::factory()->create();
        $instruktur = Instruktur::create([
            'id_user' => $instrukturUser->id_user,
            'spesialisasi' => 'Pilates',
        ]);

        // Buat kelas
        $kelas = Kelas::create([
            'nama_kelas' => 'Pilates Basic',
            'deskripsi' => 'Kelas untuk pemula',
            'kapasitas' => 10,
        ]);

        // Buat jadwal 2 hari ke depan (belum mulai → bisa booking)
        $this->jadwal = JadwalKelas::create([
            'id_kelas' => $kelas->id_kelas,
            'id_instruktur' => $instruktur->id_instruktur,
            'tanggal_kelas' => Carbon::tomorrow(),
            'jam_mulai' => Carbon::now()->addDays(2),
            'jam_selesai' => Carbon::now()->addDays(2)->addHours(1),
            'kuota_maksimal' => 5,
            'kuota_terisi' => 0,
        ]);
    }

    #[Test]
    public function pelanggan_dapat_booking_jika_kredit_cukup(): void
    {
        // Arrange: beri kredit ke pelanggan
        MutasiKredit::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'jenis_mutasi' => 'credit',
            'jumlah_kredit' => 5,
            'sumber_mutasi' => 'pembelian_package',
            'keterangan' => 'Initial credit',
        ]);

        // Act: booking via service
        $service = app(BookingService::class);
        $booking = $service->book($this->pelanggan->id_pelanggan, $this->jadwal->id_jadwal_kelas);

        // Assert: booking berhasil
        $this->assertNotNull($booking->id_booking);
        $this->assertEquals(BookingStatus::BOOKED->value, $booking->status_booking);

        // Assert: kuota bertambah
        $this->jadwal->refresh();
        $this->assertEquals(1, $this->jadwal->kuota_terisi);

        // Assert: kredit berkurang (ada mutasi debit)
        $this->assertDatabaseHas('mutasi_kredit', [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'jenis_mutasi' => 'debit',
            'jumlah_kredit' => 1,
            'sumber_mutasi' => 'booking',
        ]);
    }

    #[Test]
    public function booking_gagal_jika_kredit_kurang(): void
    {
        // Arrange: pelanggan tidak punya kredit (saldo = 0)

        // Assert: expect exception
        $this->expectException(BusinessException::class);

        // Act: coba booking
        $service = app(BookingService::class);
        $service->book($this->pelanggan->id_pelanggan, $this->jadwal->id_jadwal_kelas);
    }

    #[Test]
    public function booking_gagal_jika_kuota_penuh(): void
    {
        // Arrange: jadwal sudah penuh
        $this->jadwal->update(['kuota_terisi' => 5]); // sama dengan kuota_maksimal

        // Beri kredit agar tidak gagal di pengecekan kredit
        MutasiKredit::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'jenis_mutasi' => 'credit',
            'jumlah_kredit' => 10,
            'sumber_mutasi' => 'pembelian_package',
        ]);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Jadwal sudah penuh');

        $service = app(BookingService::class);
        $service->book($this->pelanggan->id_pelanggan, $this->jadwal->id_jadwal_kelas);
    }

    #[Test]
    public function pembatalan_booking_lebih_dari_24_jam_mendapat_refund(): void
    {
        // Arrange: beri kredit dan buat booking
        MutasiKredit::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'jenis_mutasi' => 'credit',
            'jumlah_kredit' => 5,
            'sumber_mutasi' => 'pembelian_package',
        ]);

        $service = app(BookingService::class);
        $booking = $service->book($this->pelanggan->id_pelanggan, $this->jadwal->id_jadwal_kelas);

        // Act: batalkan (jadwal masih 2 hari ke depan → refundable)
        $result = $service->cancel($booking->id_booking);

        // Assert: status cancelled
        $this->assertEquals(BookingStatus::CANCELED->value, $result['booking']->status_booking);
        $this->assertTrue($result['credit_refunded']);

        // Assert: kredit dikembalikan
        $this->assertDatabaseHas('mutasi_kredit', [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'jenis_mutasi' => 'credit',
            'sumber_mutasi' => 'booking_refund',
        ]);

        // Assert: kuota kembali ke 0
        $this->jadwal->refresh();
        $this->assertEquals(0, $this->jadwal->kuota_terisi);
    }

    #[Test]
    public function tidak_bisa_booking_kelas_yang_sama_dua_kali(): void
    {
        // Arrange: beri banyak kredit
        MutasiKredit::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'jenis_mutasi' => 'credit',
            'jumlah_kredit' => 10,
            'sumber_mutasi' => 'pembelian_package',
        ]);

        $service = app(BookingService::class);
        $service->book($this->pelanggan->id_pelanggan, $this->jadwal->id_jadwal_kelas);

        // Expect exception pada booking kedua
        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Anda sudah memesan kelas ini');

        $service->book($this->pelanggan->id_pelanggan, $this->jadwal->id_jadwal_kelas);
    }
}
