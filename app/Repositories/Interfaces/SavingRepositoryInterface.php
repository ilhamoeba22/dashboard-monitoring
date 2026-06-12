<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

interface SavingRepositoryInterface
{
    public function getSummary(): array;

    public function getFilterOptions(): array;

    public function getNominative(array $filters = [], int $perPage = 50): CursorPaginator;

    public function getRekapitulasi(string $groupBy, array $filters = []): Collection;

    public function getDoormant(array $filters = [], int $perPage = 50): CursorPaginator;
}
