<?php

declare(strict_types=1);

namespace App\DTO\Api\v1;

use JsonSerializable;

/**
 * DTO for Growth Calculation
 */
final readonly class GrowthDTO implements JsonSerializable
{
    public function __construct(
        public string $value,
        public string $cssClass,
        public float $raw,
        public ?string $direction = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'] ?? '0%',
            cssClass: $data['class'] ?? 'text-muted',
            raw: (float) ($data['raw'] ?? 0),
            direction: $data['direction'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'class' => $this->cssClass,
            'raw' => $this->raw,
            'direction' => $this->direction,
        ];
    }
}
