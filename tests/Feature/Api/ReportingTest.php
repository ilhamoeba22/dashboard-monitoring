<?php

declare(strict_types=1);

use App\Repositories\Interfaces\ReportingRepositoryInterface;
use Mockery\MockInterface;

describe('Reporting API', function () {
    it('returns neraca report', function () {
        $mockData = ['judul' => 'Laporan Neraca', 'total_aktiva' => 1000];
        
        $this->mock(ReportingRepositoryInterface::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('getReport')->with('neraca', \Mockery::any())->once()->andReturn($mockData);
        });

        $response = $this->getJson('/api/v1/reporting/neraca');
        
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => $mockData
            ]);
    });

    it('returns 404 for invalid report type', function () {
        $this->mock(ReportingRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getReport')->with('invalid', \Mockery::any())->once()->andThrow(new \InvalidArgumentException('Not supported'));
        });

        $response = $this->getJson('/api/v1/reporting/invalid');
        
        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    });
});
