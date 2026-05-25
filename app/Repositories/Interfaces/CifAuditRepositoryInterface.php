<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

interface CifAuditRepositoryInterface
{
    public function getPembiayaanAudit(array $filters, int $perPage = 50): CursorPaginator;
    
    public function getTabunganAudit(array $filters, int $perPage = 50): CursorPaginator;
    
    public function getDepositoAudit(array $filters, int $perPage = 50): CursorPaginator;
    
    public function getAuditSummary(): array;
}
