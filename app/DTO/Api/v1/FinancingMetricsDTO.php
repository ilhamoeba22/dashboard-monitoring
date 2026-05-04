<?php

declare(strict_types=1);

namespace App\DTO\Api\v1;

use JsonSerializable;

/**
 * DTO for Financing Metrics
 */
final readonly class FinancingMetricsDTO implements JsonSerializable
{
    public function __construct(
        public float $totalOs,
        public string $osFormatted,
        public float $totalNpf,
        public string $npfFormatted,
        public int $totalNoa,
        public int $totalAo,
        public GrowthDTO $growth,
        public GrowthDTO $noaGrowth,
        public GrowthDTO $aoGrowth,
        public GrowthDTO $npfGrowth,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalOs: (float) ($data['total_os'] ?? 0),
            osFormatted: $data['os_formatted'] ?? 'Rp 0',
            totalNpf: (float) ($data['total_npf'] ?? 0),
            npfFormatted: $data['npf_formatted'] ?? 'Rp 0',
            totalNoa: (int) ($data['total_noa'] ?? 0),
            totalAo: (int) ($data['total_ao'] ?? 0),
            growth: GrowthDTO::fromArray(is_array($data['growth'] ?? []) ? ($data['growth'] ?? []) : []),
            noaGrowth: GrowthDTO::fromArray(is_array($data['noa_growth'] ?? []) ? ($data['noa_growth'] ?? []) : []),
            aoGrowth: GrowthDTO::fromArray(is_array($data['ao_growth'] ?? []) ? ($data['ao_growth'] ?? []) : []),
            npfGrowth: GrowthDTO::fromArray(is_array($data['npf_growth'] ?? []) ? ($data['npf_growth'] ?? []) : []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'total_os' => $this->totalOs,
            'os_formatted' => $this->osFormatted,
            'total_npf' => $this->totalNpf,
            'npf_formatted' => $this->npfFormatted,
            'total_noa' => $this->totalNoa,
            'total_ao' => $this->totalAo,
            'growth' => $this->growth->jsonSerialize(),
            'noa_growth' => $this->noaGrowth->jsonSerialize(),
            'ao_growth' => $this->aoGrowth->jsonSerialize(),
            'npf_growth' => $this->npfGrowth->jsonSerialize(),
        ];
    }
}
