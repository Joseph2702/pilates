<?php

namespace Tests\Unit;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\MutasiKredit;
use App\Http\Repository\MutasiKreditRepository;
use App\Http\Repository\PembelianPackageRepository;
use App\Http\Service\CreditService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit test untuk CreditService.
 *
 * Konsep: kita "mock" repository sehingga tidak perlu DB.
 * Test ini berjalan sangat cepat (<1ms per test).
 */
class CreditServiceTest extends TestCase
{
    private CreditService $service;

    private MutasiKreditRepository $mutasiRepo;

    private PembelianPackageRepository $pembelianRepo;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat mock (objek palsu) untuk repository
        $this->mutasiRepo = Mockery::mock(MutasiKreditRepository::class);
        $this->pembelianRepo = Mockery::mock(PembelianPackageRepository::class);

        $this->service = new CreditService($this->mutasiRepo, $this->pembelianRepo);
    }

    #[Test]
    public function it_returns_saldo_from_repository(): void
    {
        // Arrange: mock repository mengembalikan saldo 10
        $this->mutasiRepo
            ->shouldReceive('totalSaldo')
            ->once()
            ->with(1)
            ->andReturn(10);

        // Act
        $saldo = $this->service->getSaldo(1);

        // Assert
        $this->assertEquals(10, $saldo);
    }

    #[Test]
    public function it_throws_exception_when_debit_exceeds_balance(): void
    {
        // Saldo hanya 3 kredit
        $this->mutasiRepo->shouldReceive('totalSaldo')->andReturn(3);

        // Expect exception saat coba debit 5
        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Insufficient credit balance');

        $this->service->debit(idPelanggan: 1, jumlah: 5, sumber: 'booking');
    }

    #[Test]
    public function it_creates_debit_ledger_entry_when_balance_sufficient(): void
    {
        $fakeMutasi = new MutasiKredit([
            'id_pelanggan' => 1,
            'jenis_mutasi' => 'debit',
            'jumlah_kredit' => 1,
            'sumber_mutasi' => 'booking',
        ]);

        // Saldo cukup (5 kredit, debit 1)
        $this->mutasiRepo->shouldReceive('totalSaldo')->andReturn(5);
        $this->mutasiRepo->shouldReceive('create')->once()->andReturn($fakeMutasi);

        $result = $this->service->debit(idPelanggan: 1, jumlah: 1, sumber: 'booking');

        $this->assertEquals('debit', $result->jenis_mutasi);
    }

    #[Test]
    public function it_creates_credit_ledger_entry(): void
    {
        $fakeMutasi = new MutasiKredit([
            'id_pelanggan' => 1,
            'jenis_mutasi' => 'credit',
            'jumlah_kredit' => 10,
            'sumber_mutasi' => 'pembelian_package',
        ]);

        $this->mutasiRepo->shouldReceive('create')->once()->andReturn($fakeMutasi);

        $result = $this->service->credit(idPelanggan: 1, jumlah: 10, sumber: 'pembelian_package');

        $this->assertEquals('credit', $result->jenis_mutasi);
        $this->assertEquals(10, $result->jumlah_kredit);
    }
}
