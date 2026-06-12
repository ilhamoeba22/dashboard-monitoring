<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface FundingAnalyticsRepositoryInterface
{
    public function getPerkembangan(): array;

    public function getTarget(): array;

    public function getMutasi(): array;
}
