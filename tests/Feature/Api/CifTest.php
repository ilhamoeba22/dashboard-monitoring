<?php

declare(strict_types=1);

use App\Repositories\Interfaces\CifRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\CursorPaginator;
use Mockery\MockInterface;

describe('GET /api/v1/cif', function () {
    it('returns CIF list with cursor pagination', function () {
        $mockPaginator = new CursorPaginator(collect([
            ['nocif' => 'CIF001', 'nm' => 'Budi'],
        ]), 50);

        $this->mock(CifRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getList')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/cif');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['nocif', 'nm'],
                    ],
                    'path',
                    'per_page',
                ],
            ]);
    });
});

describe('GET /api/v1/cif/rekapitulasi', function () {
    it('returns demografi nasabah grouped by cabang', function () {
        $mockCollection = collect([
            ['label' => 'Cabang Pusat', 'id' => '01', 'total_nasabah' => 500],
        ]);

        $this->mock(CifRepositoryInterface::class, function (MockInterface $mock) use ($mockCollection) {
            $mock->shouldReceive('getRekapitulasi')->with('cabang')->once()->andReturn($mockCollection);
        });

        $response = $this->getJson('/api/v1/cif/rekapitulasi?group_by=cabang');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    ['label' => 'Cabang Pusat', 'id' => '01', 'total_nasabah' => 500],
                ],
            ]);
    });

    it('returns 422 for invalid group_by', function () {
        $response = $this->getJson('/api/v1/cif/rekapitulasi?group_by=invalid');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    });
});

describe('GET /api/v1/cif/{nocif}', function () {
    it('returns cif detail', function () {
        $mockData = [
            'nocif' => 'CIF123',
            'nama' => 'Agus',
        ];

        $this->mock(CifRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getDetail')->with('CIF123')->once()->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/cif/CIF123');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => $mockData,
            ]);
    });

    it('returns 404 when nocif not found', function () {
        $this->mock(CifRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getDetail')->once()->andThrow(new ModelNotFoundException);
        });

        $response = $this->getJson('/api/v1/cif/CIF999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Data CIF tidak ditemukan',
            ]);
    });
});
