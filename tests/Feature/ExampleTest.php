<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test aplikasi merespons dengan benar via API health endpoint.
     * (Route '/' memerlukan Vite manifest yang tidak tersedia di test environment)
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJson(['status' => 'healthy']);
    }
}
