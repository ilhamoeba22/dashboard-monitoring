<?php

declare(strict_types=1);

namespace App\DTO\Api\v1;

use JsonSerializable;

/**
 * DTO for Dashboard Metrics Response
 * Ensures type safety and eliminates array mixing
 */
final readonly class DashboardMetricsDTO implements JsonSerializable
{
    public function __construct(
        public string $tgl,
        public int $year,
        public int $month,
        public string $period,
        public FinancingMetricsDTO $financing,
        public SavingMetricsDTO $saving,
        public DepositoMetricsDTO $deposito,
        public string $generatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tgl: $data['periode']['tgl'] ?? '',
            year: (int) ($data['periode']['year'] ?? 0),
            month: (int) ($data['periode']['month'] ?? 0),
            period: $data['periode']['period'] ?? '',
            financing: FinancingMetricsDTO::fromArray($data['financing'] ?? []),
            saving: SavingMetricsDTO::fromArray($data['saving'] ?? []),
            deposito: DepositoMetricsDTO::fromArray($data['deposito'] ?? []),
            generatedAt: $data['generated_at'] ?? now()->toIso8601String(),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'periode' => [
                'tgl' => $this->tgl,
                'year' => $this->year,
                'month' => $this->month,
                'period' => $this->period,
            ],
            'financing' => $this->financing,
            'saving' => $this->saving,
            'deposito' => $this->deposito,
            'generated_at' => $this->generatedAt,
        ];
    }
}
