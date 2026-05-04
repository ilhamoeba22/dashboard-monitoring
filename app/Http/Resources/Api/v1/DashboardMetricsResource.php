<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardMetricsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'periode' => $this->whenLoaded('periode', fn () => $this->periode),

            // Financing
            'financing' => $this->whenLoaded('financing', fn () => [
                'total_os' => $this->financing['total'] ?? 0,
                'os_formatted' => $this->formatRupiah($this->financing['total'] ?? 0),
                'noa' => $this->financing['noa'] ?? 0,
                'ao' => $this->financing['ao'] ?? 0,
                'growth' => $this->financing['growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
                'noa_growth' => $this->financing['noa_growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
                'ao_growth' => $this->financing['ao_growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
            ], null, true),

            // Saving
            'saving' => $this->whenLoaded('saving', fn () => [
                'total_saldo' => $this->saving['total'] ?? 0,
                'saldo_formatted' => $this->formatRupiah($this->saving['total'] ?? 0),
                'noa' => $this->saving['noa'] ?? 0,
                'ao' => $this->saving['ao'] ?? 0,
                'growth' => $this->saving['growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
                'noa_growth' => $this->saving['noa_growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
                'ao_growth' => $this->saving['ao_growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
            ], null, true),

            // Deposito
            'deposito' => $this->whenLoaded('deposito', fn () => [
                'total_saldo' => $this->deposito['total'] ?? 0,
                'saldo_formatted' => $this->formatRupiah($this->deposito['total'] ?? 0),
                'total_baghas' => $this->deposito['secondary'] ?? 0,
                'baghas_formatted' => $this->formatRupiah($this->deposito['secondary'] ?? 0),
                'noa' => $this->deposito['noa'] ?? 0,
                'ao' => $this->deposito['ao'] ?? 0,
                'growth' => $this->deposito['growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
                'noa_growth' => $this->deposito['noa_growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
                'ao_growth' => $this->deposito['ao_growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
                'baghas_growth' => $this->deposito['secondary_growth'] ?? ['value' => '0%', 'class' => 'text-muted'],
            ], null, true),

            // Chart data
            'chart' => $this->whenLoaded('chart', fn () => $this->chart, null, true),

            // Metadata
            'generated_at' => now()->toIso8601String(),
            'cache_ttl' => 60,
        ];
    }

    /**
     * Format Rupiah
     */
    protected function formatRupiah(float $value): string
    {
        return 'Rp '.number_format($value, 0, ',', '.');
    }

    /**
     * Additional meta information
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Dashboard metrics retrieved successfully',
            'version' => 'v1',
        ];
    }
}
