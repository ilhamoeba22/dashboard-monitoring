<?php

declare(strict_types=1);

use App\Repositories\Interfaces\FinancingRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\CursorPaginator;
use Mockery\MockInterface;

// ============================================================================
// FINANCING NOMINATIVE
// ============================================================================
describe('GET /api/v1/financing/nominative', function () {
    it('returns nominative list with cursor pagination', function () {
        $mockPaginator = new CursorPaginator(collect([
            ['nokontrak' => '12345', 'nama' => 'John Doe'],
        ]), 50);

        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getNominative')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/financing/nominative');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'data' => ['*' => ['nokontrak', 'nama']],
                    'path', 'per_page', 'next_cursor', 'prev_cursor',
                ],
            ]);
    });

    it('supports filter by kdao', function () {
        $mockPaginator = new CursorPaginator(collect([
            ['nokontrak' => '99999', 'nama' => 'Jane Doe'],
        ]), 50);

        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getNominative')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/financing/nominative?kdao=AO01');
        $response->assertOk()->assertJson(['success' => true]);
    });
});

// ============================================================================
// FINANCING AOS
// ============================================================================
describe('GET /api/v1/financing/aos', function () {
    it('returns unique AO list', function () {
        $mockCollection = collect([
            ['kdao' => 'AO1', 'nmao' => 'AO SATU'],
            ['kdao' => 'AO2', 'nmao' => 'AO DUA'],
        ]);

        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) use ($mockCollection) {
            $mock->shouldReceive('getUniqueAos')->once()->andReturn($mockCollection);
        });

        $response = $this->getJson('/api/v1/financing/aos');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'data');
    });
});

// ============================================================================
// FINANCING REKAPITULASI
// ============================================================================
describe('GET /api/v1/financing/rekapitulasi', function () {
    it('returns rekapitulasi grouped by cabang', function () {
        $mockData = collect([
            ['label' => 'Cabang Pusat', 'total_os' => 100_000_000, 'total_noa' => 50],
        ]);

        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getRekapitulasi')->with('cabang')->once()->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/financing/rekapitulasi?group_by=cabang');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(1, 'data');
    });

    it('returns rekapitulasi grouped by ao by default', function () {
        $mockData = collect([]);

        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getRekapitulasi')->once()->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/financing/rekapitulasi');
        $response->assertOk()->assertJson(['success' => true]);
    });
});

// ============================================================================
// FINANCING JATUH TEMPO
// ============================================================================
describe('GET /api/v1/financing/jatuh-tempo', function () {
    it('returns financing jatuh tempo list', function () {
        $mockPaginator = new CursorPaginator(collect([
            ['nokontrak' => 'JT001', 'tgl_jatuh_tempo' => '2026-05-01'],
        ]), 50);

        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getJatuhTempo')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/financing/jatuh-tempo');

        $response->assertOk()
            ->assertJson(['success' => true]);
    });
});

// ============================================================================
// FINANCING ANGSURAN (DETAIL)
// ============================================================================
describe('GET /api/v1/financing/{nokontrak}/angsuran', function () {
    it('returns angsuran details for valid nokontrak', function () {
        $mockData = [
            'header' => ['nama' => 'John Doe', 'nokontrak' => '123'],
            'details' => [],
        ];

        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getDetailAngsuran')->with('123')->once()->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/financing/123/angsuran');

        $response->assertOk()
            ->assertJson(['success' => true, 'data' => $mockData]);
    });

    it('returns 404 when nokontrak not found', function () {
        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getDetailAngsuran')->once()
                ->andThrow(new ModelNotFoundException);
        });

        $response = $this->getJson('/api/v1/financing/NOTFOUND/angsuran');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    });
});
