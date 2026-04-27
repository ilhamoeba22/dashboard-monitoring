<?php

declare(strict_types=1);

use App\Repositories\Interfaces\FinancingRepositoryInterface;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

describe('GET /api/v1/financing/nominative', function () {
    it('returns nominative list with cursor pagination', function () {
        $mockPaginator = new CursorPaginator(collect([
            ['nokontrak' => '12345', 'nama' => 'John Doe']
        ]), 50);

        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getNominative')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/financing/nominative');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['nokontrak', 'nama']
                    ],
                    'path',
                    'per_page',
                    'next_cursor',
                    'prev_cursor',
                ]
            ]);
    });
});

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
            ->assertJson([
                'success' => true,
                'data'    => [
                    ['kdao' => 'AO1', 'nmao' => 'AO SATU'],
                    ['kdao' => 'AO2', 'nmao' => 'AO DUA'],
                ]
            ]);
    });
});

describe('GET /api/v1/financing/{nokontrak}/angsuran', function () {
    it('returns angsuran details', function () {
        $mockData = [
            'header'  => ['nama' => 'John Doe'],
            'details' => []
        ];

        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getDetailAngsuran')->with('123')->once()->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/financing/123/angsuran');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data'    => $mockData
            ]);
    });

    it('returns 404 when nokontrak not found', function () {
        $this->mock(FinancingRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getDetailAngsuran')->once()->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException());
        });

        $response = $this->getJson('/api/v1/financing/999/angsuran');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Data kontrak tidak ditemukan'
            ]);
    });
});
