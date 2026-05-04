<<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Repositories\Mci\FinancingOverviewRepository;
use App\Services\Mci\MciConnectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancingOverviewTest extends TestCase
{
    use RefreshDatabase;

    private FinancingOverviewRepository $repo;
    private MciConnectionService $mciService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mciService = app(MciConnectionService::class);
        $this->repo = app(FinancingOverviewRepository::class);
    }

    /** @test */
    public function realtime_metrics_returns_data(): void
    {
        $data = $this->repo->getRealtimeMetrics();

        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('kolektibilitas', $data);
        $this->assertGreaterThan(0, $data['summary']['total_noa']);
    }

    /** @test */
    public function historical_trend_returns_series(): void
    {
        $data = $this->repo->getHistoricalTrend(3);

        $this->assertArrayHasKey('series', $data);
        $this->assertGreaterThan(0, count($data['series']['noa']));
    }

    /** @test */
    public function api_endpoints_return_success(): void
    {
        $response = $this->getJson('/api/v1/financing/overview/realtime');
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);

        $response = $this->getJson('/api/v1/financing/overview/trend?months=3');
        $response->assertStatus(200);
    }
}
?>
