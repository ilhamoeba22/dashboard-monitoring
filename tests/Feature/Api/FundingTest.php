<?php

declare(strict_types=1);

use App\Repositories\Interfaces\DepositRepositoryInterface;
use App\Repositories\Interfaces\SavingRepositoryInterface;
use Illuminate\Pagination\CursorPaginator;
use Mockery\MockInterface;

// ============================================================================
// SAVING — NOMINATIVE
// ============================================================================
describe('GET /api/v1/saving/nominative', function () {
    it('returns saving nominative list with pagination', function () {
        $mockPaginator = new CursorPaginator(collect([
            ['notab' => 'TAB001', 'nama' => 'Budi Santoso', 'sahirrp' => 5_000_000],
        ]), 50);

        $this->mock(SavingRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getNominative')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/saving/nominative');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['data', 'path', 'per_page', 'next_cursor', 'prev_cursor']]);
    });

    it('supports filter by kodeloc', function () {
        $mockPaginator = new CursorPaginator(collect([]), 50);

        $this->mock(SavingRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getNominative')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/saving/nominative?kodeloc=01');
        $response->assertOk()->assertJson(['success' => true]);
    });
});

// ============================================================================
// SAVING — REKAPITULASI
// ============================================================================
describe('GET /api/v1/saving/rekapitulasi', function () {
    it('returns rekapitulasi grouped by cabang', function () {
        $mockCollection = collect([
            ['label' => 'Cabang Pusat', 'total_saldo' => 50_000_000, 'total_noa' => 300],
        ]);

        $this->mock(SavingRepositoryInterface::class, function (MockInterface $mock) use ($mockCollection) {
            $mock->shouldReceive('getRekapitulasi')->with('cabang')->once()->andReturn($mockCollection);
        });

        $response = $this->getJson('/api/v1/saving/rekapitulasi?group_by=cabang');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(1, 'data');
    });

    it('returns rekapitulasi grouped by wilayah', function () {
        $mockCollection = collect([
            ['label' => 'Wilayah 1', 'total_saldo' => 30_000_000, 'total_noa' => 150],
        ]);

        $this->mock(SavingRepositoryInterface::class, function (MockInterface $mock) use ($mockCollection) {
            $mock->shouldReceive('getRekapitulasi')->with('wilayah')->once()->andReturn($mockCollection);
        });

        $response = $this->getJson('/api/v1/saving/rekapitulasi?group_by=wilayah');
        $response->assertOk()->assertJson(['success' => true]);
    });

    it('returns 422 for invalid group_by value', function () {
        $response = $this->getJson('/api/v1/saving/rekapitulasi?group_by=invalid');
        $response->assertStatus(422)->assertJson(['success' => false]);
    });

    it('returns default rekapitulasi without group_by', function () {
        $this->mock(SavingRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getRekapitulasi')->once()->andReturn(collect([]));
        });

        $response = $this->getJson('/api/v1/saving/rekapitulasi');
        $response->assertOk()->assertJson(['success' => true]);
    });
});

// ============================================================================
// SAVING — DOORMANT
// ============================================================================
describe('GET /api/v1/saving/doormant', function () {
    it('returns doormant tabungan list', function () {
        $mockPaginator = new CursorPaginator(collect([
            ['notab' => 'TAB999', 'nama' => 'Nasabah Doormant', 'last_trans' => '2024-01-01'],
        ]), 50);

        $this->mock(SavingRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getDoormant')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/saving/doormant');

        $response->assertOk()
            ->assertJson(['success' => true]);
    });
});

// ============================================================================
// DEPOSIT — NOMINATIVE
// ============================================================================
describe('GET /api/v1/deposit/nominative', function () {
    it('returns deposit nominative list', function () {
        $mockPaginator = new CursorPaginator(collect([
            ['nodep' => 'DEP001', 'nama' => 'Siti Aminah', 'nomrp' => 50_000_000],
        ]), 50);

        $this->mock(DepositRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getNominative')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/deposit/nominative');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['data', 'path', 'per_page', 'next_cursor', 'prev_cursor']]);
    });
});

// ============================================================================
// DEPOSIT — REKAPITULASI
// ============================================================================
describe('GET /api/v1/deposit/rekapitulasi', function () {
    it('returns deposit rekapitulasi grouped by cabang', function () {
        $mockCollection = collect([
            ['label' => 'Cabang Utama', 'total_saldo' => 200_000_000, 'total_noa' => 100],
        ]);

        $this->mock(DepositRepositoryInterface::class, function (MockInterface $mock) use ($mockCollection) {
            $mock->shouldReceive('getRekapitulasi')->with('cabang')->once()->andReturn($mockCollection);
        });

        $response = $this->getJson('/api/v1/deposit/rekapitulasi?group_by=cabang');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(1, 'data');
    });

    it('returns deposit rekapitulasi grouped by ao', function () {
        $mockCollection = collect([
            ['label' => 'AO Deposito Satu', 'total_saldo' => 80_000_000, 'total_noa' => 40],
        ]);

        $this->mock(DepositRepositoryInterface::class, function (MockInterface $mock) use ($mockCollection) {
            $mock->shouldReceive('getRekapitulasi')->with('ao')->once()->andReturn($mockCollection);
        });

        $response = $this->getJson('/api/v1/deposit/rekapitulasi?group_by=ao');
        $response->assertOk()->assertJson(['success' => true]);
    });

    it('returns 422 for invalid group_by value', function () {
        $response = $this->getJson('/api/v1/deposit/rekapitulasi?group_by=invalid');
        $response->assertStatus(422)->assertJson(['success' => false]);
    });
});

// ============================================================================
// DEPOSIT — JATUH TEMPO
// ============================================================================
describe('GET /api/v1/deposit/jatuh-tempo', function () {
    it('returns deposit jatuh tempo list', function () {
        $mockPaginator = new CursorPaginator(collect([
            ['nodep' => 'DEP002', 'nama' => 'Ahmad', 'tgl_jt' => '2026-05-15', 'nomrp' => 25_000_000],
        ]), 50);

        $this->mock(DepositRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getJatuhTempo')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/deposit/jatuh-tempo');

        $response->assertOk()
            ->assertJson(['success' => true]);
    });

    it('supports filter by days range', function () {
        $mockPaginator = new CursorPaginator(collect([]), 50);

        $this->mock(DepositRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getJatuhTempo')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/deposit/jatuh-tempo?days=30');
        $response->assertOk()->assertJson(['success' => true]);
    });
});
