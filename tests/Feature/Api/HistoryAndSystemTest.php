<?php

declare(strict_types=1);

use App\DTO\Api\v1\DashboardMetricsDTO;
use App\DTO\Api\v1\DepositoMetricsDTO;
use App\DTO\Api\v1\FinancingMetricsDTO;
use App\DTO\Api\v1\GrowthDTO;
use App\DTO\Api\v1\SavingMetricsDTO;
use App\Services\Mci\DashboardRepository;

// ============================================================================
// HELPER FACTORIES — local scope (tidak duplikasi dengan DashboardMetricsTest)
// ============================================================================
function makeGrowthH(float $raw = 0.0): GrowthDTO
{
    return GrowthDTO::fromArray(['value' => '0%', 'class' => 'text-muted', 'raw' => $raw, 'direction' => null]);
}
function makeFinancingH(): FinancingMetricsDTO
{
    return new FinancingMetricsDTO(
        totalOs: 228_000_000_000.0, osFormatted: 'Rp 228M',
        totalNpf: 15_000_000_000.0, npfFormatted: 'Rp 15M',
        totalNoa: 1011, totalAo: 11,
        growth: makeGrowthH(-2.68), noaGrowth: makeGrowthH(-69.5),
        aoGrowth: makeGrowthH(-31.25), npfGrowth: makeGrowthH(-21.44),
    );
}
function makeSavingH(): SavingMetricsDTO
{
    return new SavingMetricsDTO(
        totalSaldo: 58_000_000_000.0, saldoFormatted: 'Rp 58M',
        totalNoa: 9705, totalAo: 57,
        growth: makeGrowthH(0.0), noaGrowth: makeGrowthH(-0.52), aoGrowth: makeGrowthH(-1.72),
    );
}
function makeDepositoH(): DepositoMetricsDTO
{
    return new DepositoMetricsDTO(
        totalSaldo: 203_000_000_000.0, saldoFormatted: 'Rp 203M',
        totalBaghas: 684_000_000.0, baghasFormatted: 'Rp 684M',
        totalNoa: 445, totalAo: 6,
        growth: makeGrowthH(0.0), noaGrowth: makeGrowthH(0.0),
        aoGrowth: makeGrowthH(0.0), baghasGrowth: makeGrowthH(0.0),
    );
}
function makeFullDTO(): DashboardMetricsDTO
{
    return new DashboardMetricsDTO(
        tgl: '01032026', year: 2026, month: 3, period: '202603',
        financing: makeFinancingH(), saving: makeSavingH(),
        deposito: makeDepositoH(), generatedAt: now()->toIso8601String(),
    );
}

// ============================================================================
// HEALTH CHECKS
// ============================================================================
describe('GET /api/v1/health', function () {
    it('returns healthy status with correct structure', function () {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk()
            ->assertJsonStructure(['status', 'service', 'version', 'timestamp'])
            ->assertJson(['status' => 'healthy', 'version' => 'v1']);
    });
});

describe('GET /api/v1/info', function () {
    it('returns API info with name and endpoints list', function () {
        $response = $this->getJson('/api/v1/info');

        // api/v1/info returns flat JSON (no success wrapper)
        $response->assertOk()
            ->assertJsonStructure(['name', 'version', 'description', 'endpoints', 'rate_limit', 'cache_ttl'])
            ->assertJson(['version' => 'v1', 'name' => 'MCI Dashboard REST API']);
    });
});

describe('GET /api/v2/health', function () {
    it('returns v2 health response with version v2', function () {
        $response = $this->getJson('/api/v2/health');

        $response->assertOk()
            ->assertJson(['status' => 'healthy', 'version' => 'v2']);
    });
});

// ============================================================================
// DASHBOARD — HISTORY METRICS (semua menggunakan mock)
// ============================================================================
describe('GET /api/v1/dashboard/history/{db}/metrics', function () {
    it('returns all history metrics with correct structure via mock', function () {
        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('setConnection')->once()->with('dashboard_feb')->andReturnSelf();
        $mock->shouldReceive('getKeyMetrics')->once()->andReturn(makeFullDTO());
        $mock->shouldReceive('resetConnection')->once()->andReturnNull();
        app()->instance(DashboardRepository::class, $mock);

        $response = $this->getJson('/api/v1/dashboard/history/feb/metrics');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['periode', 'financing', 'saving', 'deposito'], 'meta']);
    });

    it('returns 500 or error when unknown db code given', function () {
        // Unknown db → resolves to 'dashboard_data' → real connection fails in test env
        $response = $this->getJson('/api/v1/dashboard/history/zzz99/metrics');
        // Must not return 200 (no valid data source)
        expect($response->status())->toBeIn([404, 500]);
    });
});

describe('GET /api/v1/dashboard/history/{db}/metrics/financing', function () {
    it('returns financing history metrics', function () {
        $period = ['year' => 2026, 'month' => 3, 'period' => '202603', 'previous_year' => 2025, 'tgl' => '01032026'];

        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('setConnection')->once()->with('dashboard_feb')->andReturnSelf();
        $mock->shouldReceive('getCurrentPeriod')->once()->andReturn($period);
        $mock->shouldReceive('getFinancingMetrics')->with('2026', '2025')->once()->andReturn(makeFinancingH());
        $mock->shouldReceive('resetConnection')->once()->andReturnNull();
        app()->instance(DashboardRepository::class, $mock);

        $response = $this->getJson('/api/v1/dashboard/history/feb/metrics/financing');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['total_os', 'total_npf', 'total_noa', 'total_ao', 'growth']]);
    });
});

describe('GET /api/v1/dashboard/history/{db}/metrics/saving', function () {
    it('returns saving history metrics', function () {
        $period = ['year' => 2026, 'month' => 3, 'period' => '202603', 'previous_year' => 2025, 'tgl' => '01032026'];

        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('setConnection')->once()->with('dashboard_feb')->andReturnSelf();
        $mock->shouldReceive('getCurrentPeriod')->once()->andReturn($period);
        $mock->shouldReceive('getSavingMetrics')->with('2026', '2025')->once()->andReturn(makeSavingH());
        $mock->shouldReceive('resetConnection')->once()->andReturnNull();
        app()->instance(DashboardRepository::class, $mock);

        $response = $this->getJson('/api/v1/dashboard/history/feb/metrics/saving');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['total_saldo', 'total_noa', 'total_ao', 'growth']]);
    });
});

describe('GET /api/v1/dashboard/history/{db}/metrics/deposito', function () {
    it('returns deposito history metrics', function () {
        $period = ['year' => 2026, 'month' => 3, 'period' => '202603', 'previous_year' => 2025, 'tgl' => '01032026'];

        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('setConnection')->once()->with('dashboard_feb')->andReturnSelf();
        $mock->shouldReceive('getCurrentPeriod')->once()->andReturn($period);
        $mock->shouldReceive('getDepositoMetrics')->with('2026', '2025')->once()->andReturn(makeDepositoH());
        $mock->shouldReceive('resetConnection')->once()->andReturnNull();
        app()->instance(DashboardRepository::class, $mock);

        $response = $this->getJson('/api/v1/dashboard/history/feb/metrics/deposito');

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['total_saldo', 'total_baghas', 'total_noa', 'total_ao', 'growth']]);
    });
});

// ============================================================================
// DATABASES ENDPOINT
// ============================================================================
describe('GET /api/v1/databases', function () {
    it('responds to request (200 or structured error)', function () {
        $response = $this->getJson('/api/v1/databases');
        $this->assertContains($response->status(), [200, 500]);
        // If 500, must have structured JSON
        if ($response->status() === 500) {
            expect($response->json())->toBeArray();
        }
    });
});

describe('GET /api/v1/databases/test-all', function () {
    it('attempts database connection tests', function () {
        $response = $this->getJson('/api/v1/databases/test-all');
        $this->assertContains($response->status(), [200, 500]);
    });
});

// ============================================================================
// POST /api/v1/dashboard/clear-cache
// ============================================================================
describe('POST /api/v1/dashboard/clear-cache', function () {
    it('clears dashboard cache and returns success', function () {
        $mock = Mockery::mock(DashboardRepository::class);
        $mock->shouldReceive('clearCache')->once()->andReturnNull();
        app()->instance(DashboardRepository::class, $mock);

        $response = $this->postJson('/api/v1/dashboard/clear-cache');

        $response->assertOk()
            ->assertJson(['success' => true]);
    });
});
