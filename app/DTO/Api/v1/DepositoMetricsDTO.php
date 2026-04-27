<?php

declare(strict_types=1);

namespace App\DTO\Api\v1;

use JsonSerializable;

/**
 * DTO for Deposito Metrics
 */
final readonly class DepositoMetricsDTO implements JsonSerializable
{
    public function __construct(
        public float $totalSaldo,
        public string $saldoFormatted,
        public float $totalBaghas,
        public string $baghasFormatted,
        public int $totalNoa,
        public int $totalAo,
        public GrowthDTO $growth,
        public GrowthDTO $noaGrowth,
        public GrowthDTO $aoGrowth,
        public GrowthDTO $baghasGrowth,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalSaldo: (float) ($data['total_saldo'] ?? 0),
            saldoFormatted: $data['saldo_formatted'] ?? 'Rp 0',
            totalBaghas: (float) ($data['total_baghas'] ?? 0),
            baghasFormatted: $data['baghas_formatted'] ?? 'Rp 0',
            totalNoa: (int) ($data['total_noa'] ?? 0),
            totalAo: (int) ($data['total_ao'] ?? 0),
            growth: GrowthDTO::fromArray($data['growth'] ?? []),
            noaGrowth: GrowthDTO::fromArray($data['noa_growth'] ?? []),
            aoGrowth: GrowthDTO::fromArray($data['ao_growth'] ?? []),
            baghasGrowth: GrowthDTO::fromArray($data['baghas_growth'] ?? []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'total_saldo' => $this->totalSaldo,
            'saldo_formatted' => $this->saldoFormatted,
            'total_baghas' => $this->totalBaghas,
            'baghas_formatted' => $this->baghasFormatted,
            'total_noa' => $this->totalNoa,
            'total_ao' => $this->totalAo,
            'growth' => $this->growth,
            'noa_growth' => $this->noaGrowth,
            'ao_growth' => $this->aoGrowth,
            'baghas_growth' => $this->baghasGrowth,
        ];
    }
}