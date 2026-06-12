<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface FundingBaghasRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getOverview(): array;
}
