<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface FundingRiskRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getOverview(): array;

    /**
     * @return array<string, mixed>
     */
    public function getConcentrationDetails(): array;
}
