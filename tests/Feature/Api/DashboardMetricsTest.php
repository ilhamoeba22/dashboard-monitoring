<?php

declare(strict_types=1);

use App\DTO\Api\v1\DashboardMetricsDTO;
use App\DTO\Api\v1\DepositoMetricsDTO;
use App\DTO\Api\v1\FinancingMetricsDTO;
use App\DTO\Api\v1\GrowthDTO;
use App\DTO\Api\v1\SavingMetricsDTO;
use App\Services\Mci\DashboardRepository;

// ============================================================================
// FEATURE TESTS — Dashboard Metrics API Endpoints
// ============================================================================

describe('GET /api/v1/health', function () {
    it('returns healthy status', function () {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk()
            ->assertJsonStructure(['status', 'service', 'version', 'timestamp'])
            ->assertJson(['status' => 'healthy']);
    });
});

describe('GET /api/v1/dashboard/metrics', function () {
    it('returns all metrics with correct structure', function () {
        // Arrange — mock repository agar tidak perlu SQL Server
        $dto = makeDashboardMetricsDTO();

        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('getKeyMetrics')->once()->andReturn($dto);
        $this->app->instance(DashboardRepository::class, $mock);

        // Act
        $response = $this->getJson('/api/v1/dashboard/metrics');

        // Assert
        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data'  => ['periode', 'financing', 'saving', 'deposito'],
                'meta'  => ['generated_at', 'cache_ttl', 'version'],
            ]);
    });

    it('returns 500 when repository throws exception', function () {
        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('getKeyMetrics')->once()->andThrow(new \RuntimeException('DB error'));
        $this->app->instance(DashboardRepository::class, $mock);

        $response = $this->getJson('/api/v1/dashboard/metrics');

        $response->assertStatus(500)
            ->assertJson(['success' => false]);
    });
});

describe('GET /api/v1/dashboard/metrics/financing', function () {
    it('returns financing metrics', function () {
        $dto    = makeFinancingDTO();
        $period = ['year' => 2026, 'month' => 4, 'period' => '202604', 'previous_year' => 2025, 'tgl' => '01042026'];

        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('getCurrentPeriod')->once()->andReturn($period);
        $mock->shouldReceive('getFinancingMetrics')->once()->with('2026', '2025')->andReturn($dto);
        $this->app->instance(DashboardRepository::class, $mock);

        $response = $this->getJson('/api/v1/dashboard/metrics/financing');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['total_os', 'os_formatted', 'total_npf', 'npf_formatted', 'total_noa', 'total_ao', 'growth'],
            ]);
    });
});

describe('GET /api/v1/dashboard/metrics/saving', function () {
    it('returns saving metrics', function () {
        $dto    = makeSavingDTO();
        $period = ['year' => 2026, 'month' => 4, 'period' => '202604', 'previous_year' => 2025, 'tgl' => '01042026'];

        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('getCurrentPeriod')->once()->andReturn($period);
        $mock->shouldReceive('getSavingMetrics')->once()->with('2026', '2025')->andReturn($dto);
        $this->app->instance(DashboardRepository::class, $mock);

        $response = $this->getJson('/api/v1/dashboard/metrics/saving');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['total_saldo', 'saldo_formatted', 'total_noa', 'total_ao', 'growth'],
            ]);
    });
});

describe('GET /api/v1/dashboard/metrics/deposito', function () {
    it('returns deposito metrics', function () {
        $dto    = makeDepositoDTO();
        $period = ['year' => 2026, 'month' => 4, 'period' => '202604', 'previous_year' => 2025, 'tgl' => '01042026'];

        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('getCurrentPeriod')->once()->andReturn($period);
        $mock->shouldReceive('getDepositoMetrics')->once()->with('2026', '2025')->andReturn($dto);
        $this->app->instance(DashboardRepository::class, $mock);

        $response = $this->getJson('/api/v1/dashboard/metrics/deposito');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['total_saldo', 'saldo_formatted', 'total_baghas', 'baghas_formatted', 'total_noa', 'total_ao'],
            ]);
    });
});

describe('GET /api/v1/dashboard/chart/{type}', function () {
    it('returns chart data for valid type', function ($type) {
        $chartData = ['labels' => ['202601', '202602'], 'values' => [1000.0, 1200.0], 'noa' => [10, 12], 'growth' => [0.0, 20.0]];

        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('getChartDataFromWarehouse')
             ->once()
             ->with($type, Mockery::any(), Mockery::any())
             ->andReturn($chartData);
        $this->app->instance(DashboardRepository::class, $mock);

        $response = $this->getJson("/api/v1/dashboard/chart/{$type}");

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['labels', 'values', 'noa', 'growth']]);
    })->with(['financing', 'saving', 'deposito']);

    it('returns 422 for invalid chart type', function () {
        $response = $this->getJson('/api/v1/dashboard/chart/invalid-type');

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });
});

describe('GET /api/v1/dashboard/branches', function () {
    it('returns branch list', function () {
        $branches = [
            ['kdloc' => '01', 'nama' => 'Kantor Pusat'],
            ['kdloc' => '02', 'nama' => 'Cabang A'],
        ];

        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('getBranchList')->once()->andReturn($branches);
        $this->app->instance(DashboardRepository::class, $mock);

        $response = $this->getJson('/api/v1/dashboard/branches');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['meta' => ['total', 'cache_ttl', 'version']]);
    });
});

describe('POST /api/v1/dashboard/clear-cache', function () {
    it('clears cache successfully', function () {
        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('clearCache')->once()->andReturnNull();
        $this->app->instance(DashboardRepository::class, $mock);

        $response = $this->postJson('/api/v1/dashboard/clear-cache');

        $response->assertOk()
            ->assertJson(['success' => true]);
    });
});

// ============================================================================
// UNIT TESTS — DashboardRepository helpers
// ============================================================================

describe('DashboardRepository::calculateGrowth (via base)', function () {
    it('returns zero growth when previous is zero', function () {
        $repo   = new class extends \App\Services\Mci\DashboardRepository {
            public function testGrowth(float $c, float $p): array
            {
                return $this->calculateGrowth($c, $p);
            }
        };

        $result = $repo->testGrowth(1000, 0);

        expect($result['raw'])->toBe(0.0)
            ->and($result['class'])->toBe('text-muted');
    });

    it('calculates positive growth correctly', function () {
        $repo = new class extends \App\Services\Mci\DashboardRepository {
            public function testGrowth(float $c, float $p): array
            {
                return $this->calculateGrowth($c, $p);
            }
        };

        $result = $repo->testGrowth(1200, 1000);

        expect($result['direction'])->toBe('up')
            ->and($result['class'])->toBe('text-success')
            ->and($result['raw'])->toBe(20.0);
    });

    it('calculates negative growth correctly', function () {
        $repo = new class extends \App\Services\Mci\DashboardRepository {
            public function testGrowth(float $c, float $p): array
            {
                return $this->calculateGrowth($c, $p);
            }
        };

        $result = $repo->testGrowth(800, 1000);

        expect($result['direction'])->toBe('down')
            ->and($result['class'])->toBe('text-danger');
    });
});

// ============================================================================
// DTO UNIT TESTS
// ============================================================================

describe('GrowthDTO::fromArray', function () {
    it('creates DTO from array correctly', function () {
        $dto = GrowthDTO::fromArray(['value' => '5,00%', 'class' => 'text-success', 'raw' => 5.0, 'direction' => 'up']);

        expect($dto->value)->toBe('5,00%')
            ->and($dto->cssClass)->toBe('text-success')
            ->and($dto->raw)->toBe(5.0)
            ->and($dto->direction)->toBe('up');
    });

    it('uses safe defaults for missing keys', function () {
        $dto = GrowthDTO::fromArray([]);

        expect($dto->value)->toBe('0%')
            ->and($dto->cssClass)->toBe('text-muted')
            ->and($dto->raw)->toBe(0.0)
            ->and($dto->direction)->toBeNull();
    });
});

describe('FinancingMetricsDTO::jsonSerialize', function () {
    it('serializes all fields', function () {
        $dto  = makeFinancingDTO();
        $json = $dto->jsonSerialize();

        expect($json)->toHaveKeys(['total_os', 'os_formatted', 'total_npf', 'npf_formatted', 'total_noa', 'total_ao', 'growth', 'noa_growth', 'ao_growth', 'npf_growth']);
    });
});

// ============================================================================
// FACTORY HELPERS (menggantikan before-each setup berulang)
// ============================================================================

function makeGrowthDTO(float $raw = 0.0): GrowthDTO
{
    return GrowthDTO::fromArray(['value' => '0%', 'class' => 'text-muted', 'raw' => $raw]);
}

function makeFinancingDTO(): FinancingMetricsDTO
{
    return new FinancingMetricsDTO(
        totalOs:      1_000_000_000.0,
        osFormatted:  'Rp 1.000.000.000',
        totalNpf:     50_000_000.0,
        npfFormatted: 'Rp 50.000.000',
        totalNoa:     500,
        totalAo:      25,
        growth:       makeGrowthDTO(5.0),
        noaGrowth:    makeGrowthDTO(2.0),
        aoGrowth:     makeGrowthDTO(0.0),
        npfGrowth:    makeGrowthDTO(-1.0),
    );
}

function makeSavingDTO(): SavingMetricsDTO
{
    return new SavingMetricsDTO(
        totalSaldo:     500_000_000.0,
        saldoFormatted: 'Rp 500.000.000',
        totalNoa:       300,
        totalAo:        15,
        growth:         makeGrowthDTO(3.0),
        noaGrowth:      makeGrowthDTO(1.0),
        aoGrowth:       makeGrowthDTO(0.0),
    );
}

function makeDepositoDTO(): DepositoMetricsDTO
{
    return new DepositoMetricsDTO(
        totalSaldo:      200_000_000.0,
        saldoFormatted:  'Rp 200.000.000',
        totalBaghas:     10_000_000.0,
        baghasFormatted: 'Rp 10.000.000',
        totalNoa:        100,
        totalAo:         10,
        growth:          makeGrowthDTO(2.0),
        noaGrowth:       makeGrowthDTO(1.0),
        aoGrowth:        makeGrowthDTO(0.0),
        baghasGrowth:    makeGrowthDTO(4.0),
    );
}

function makeDashboardMetricsDTO(): DashboardMetricsDTO
{
    return new DashboardMetricsDTO(
        tgl:         '01042026',
        year:        2026,
        month:       4,
        period:      '202604',
        financing:   makeFinancingDTO(),
        saving:      makeSavingDTO(),
        deposito:    makeDepositoDTO(),
        generatedAt: now()->toIso8601String(),
    );
}
