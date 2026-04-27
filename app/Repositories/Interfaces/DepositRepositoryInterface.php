<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

interface DepositRepositoryInterface
{
    public function getNominative(array $filters = [], int $perPage = 50): CursorPaginator;
    public function getRekapitulasi(string $groupBy): Collection;
    public function getJatuhTempo(array $filters = [], int $perPage = 50): CursorPaginator;
}
