<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface FinancingPenyelesaianRepositoryInterface
{
    public function getPpka(array $filters = []): Collection;
    public function getPpkaSummary(array $filters = []): array;
    
    public function getSettlement(array $filters = []): Collection;
    public function getSettlementSummary(): array;
    
    public function getWriteOff(array $filters = []): Collection;
    public function getWriteOffSummary(array $filters = []): array;
    
    public function getYield(array $filters = []): Collection;
}
