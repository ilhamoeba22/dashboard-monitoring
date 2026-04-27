<?php

declare(strict_types=1);

namespace App\DTO\Api\v1;

use JsonSerializable;

/**
 * DTO for Saving Metrics
 */
final readonly class SavingMetricsDTO implements JsonSerializable
{
    public function __construct(
        public float $totalSaldo,
        public string $saldoFormatted,
        public int $totalNoa,
        public int $totalAo,
        public GrowthDTO $growth,
        public GrowthDTO $noaGrowth,
        public GrowthDTO $aoGrowth,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalSaldo: (float) ($data['total_saldo'] ?? 0),
            saldoFormatted: $data['saldo_formatted'] ?? 'Rp 0',
            totalNoa: (int) ($data['total_noa'] ?? 0),
            totalAo: (int) ($data['total_ao'] ?? 0),
            growth: GrowthDTO::fromArray($data['growth'] ?? []),
            noaGrowth: GrowthDTO::fromArray($data['noa_growth'] ?? []),
            aoGrowth: GrowthDTO::fromArray($data['ao_growth'] ?? []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'total_saldo' => $this->totalSaldo,
            'saldo_formatted' => $this->saldoFormatted,
            'total_noa' => $this->totalNoa,
            'total_ao' => $this->totalAo,
            'growth' => $this->growth,
            'noa_growth' => $this->noaGrowth,
            'ao_growth' => $this->aoGrowth,
        ];
    }
}