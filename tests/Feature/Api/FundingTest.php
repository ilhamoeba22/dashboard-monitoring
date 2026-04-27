<?php

declare(strict_types=1);

use App\Repositories\Interfaces\SavingRepositoryInterface;
use App\Repositories\Interfaces\DepositRepositoryInterface;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

describe('Saving API', function () {
    it('returns saving nominative list', function () {
        $mockPaginator = new CursorPaginator(collect([['notab' => 'TAB01', 'nama' => 'Test']]), 50);
        $this->mock(SavingRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getNominative')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/saving/nominative');
        $response->assertOk()->assertJson(['success' => true]);
    });

    it('returns saving rekapitulasi', function () {
        $this->mock(SavingRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getRekapitulasi')->with('cabang')->once()->andReturn(collect([]));
        });

        $response = $this->getJson('/api/v1/saving/rekapitulasi?group_by=cabang');
        $response->assertOk()->assertJson(['success' => true]);
    });
});

describe('Deposit API', function () {
    it('returns deposit nominative list', function () {
        $mockPaginator = new CursorPaginator(collect([['nodep' => 'DEP01', 'nama' => 'Test']]), 50);
        $this->mock(DepositRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getNominative')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/deposit/nominative');
        $response->assertOk()->assertJson(['success' => true]);
    });

    it('returns deposit rekapitulasi', function () {
        $this->mock(DepositRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getRekapitulasi')->with('cabang')->once()->andReturn(collect([]));
        });

        $response = $this->getJson('/api/v1/deposit/rekapitulasi?group_by=cabang');
        $response->assertOk()->assertJson(['success' => true]);
    });

    it('returns deposit jatuh tempo', function () {
        $mockPaginator = new CursorPaginator(collect([]), 50);
        $this->mock(DepositRepositoryInterface::class, function (MockInterface $mock) use ($mockPaginator) {
            $mock->shouldReceive('getJatuhTempo')->once()->andReturn($mockPaginator);
        });

        $response = $this->getJson('/api/v1/deposit/jatuh-tempo');
        $response->assertOk()->assertJson(['success' => true]);
    });
});
