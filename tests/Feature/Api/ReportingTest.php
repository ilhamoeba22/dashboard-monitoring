<?php

declare(strict_types=1);

use App\Repositories\Interfaces\ReportingRepositoryInterface;
use Mockery\MockInterface;

// ============================================================================
// REPORTING — semua jenis laporan yang valid
// ============================================================================
describe('GET /api/v1/reporting/{jenis}', function () {

    $validTypes = ['neraca', 'labarugi', 'jamkrida', 'rasio'];

    it('returns neraca report correctly', function () {
        $mockData = [
            'judul' => 'Laporan Neraca',
            'total_aktiva' => 1_000_000_000,
            'total_pasiva' => 1_000_000_000,
            'rows' => [],
        ];

        $this->mock(ReportingRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getReport')
                ->with('neraca', Mockery::any())
                ->once()
                ->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/reporting/neraca');

        $response->assertOk()
            ->assertJson(['success' => true, 'data' => $mockData]);
    });

    it('returns labarugi report correctly', function () {
        $mockData = [
            'judul' => 'Laporan Laba Rugi',
            'total_pendapatan' => 500_000_000,
            'total_beban' => 300_000_000,
            'laba_bersih' => 200_000_000,
        ];

        $this->mock(ReportingRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getReport')
                ->with('labarugi', Mockery::any())
                ->once()
                ->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/reporting/labarugi');
        $response->assertOk()->assertJson(['success' => true]);
    });

    it('returns jamkrida report correctly', function () {
        $mockData = ['judul' => 'Laporan Jamkrida', 'rows' => []];

        $this->mock(ReportingRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getReport')
                ->with('jamkrida', Mockery::any())
                ->once()
                ->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/reporting/jamkrida');
        $response->assertOk()->assertJson(['success' => true]);
    });

    it('returns rasio report correctly', function () {
        $mockData = ['judul' => 'Rasio Keuangan', 'npf' => 5.2, 'car' => 18.5, 'roe' => 12.1];

        $this->mock(ReportingRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getReport')
                ->with('rasio', Mockery::any())
                ->once()
                ->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/reporting/rasio');
        $response->assertOk()->assertJson(['success' => true]);
    });

    it('returns 404 for unsupported report type', function () {
        $this->mock(ReportingRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getReport')
                ->with('unknown', Mockery::any())
                ->once()
                ->andThrow(new InvalidArgumentException('Jenis laporan tidak didukung: unknown'));
        });

        $response = $this->getJson('/api/v1/reporting/unknown');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    });

    it('returns 500 when repository throws runtime exception', function () {
        $this->mock(ReportingRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getReport')
                ->once()
                ->andThrow(new RuntimeException('DB connection failed'));
        });

        $response = $this->getJson('/api/v1/reporting/neraca');

        $response->assertStatus(500)
            ->assertJson(['success' => false]);
    });
});
