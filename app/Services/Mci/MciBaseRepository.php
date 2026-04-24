<?php

declare(strict_types=1);

namespace App\Services\Mci;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

abstract class MciBaseRepository
{
    protected string $connection = 'dashboard_data';
    protected string $tableName = '';
    protected int $cacheTtl = 60; // 60 detik default

    abstract protected function getTableName(): string;

    public function __construct()
    {
        $this->tableName = $this->getTableName();
    }

    /**
     * Get connection instance
     */
    protected function getConnection(): \Illuminate\Database\Query\Builder
    {
        return DB::connection($this->connection)->table($this->tableName);
    }

    /**
     * Get raw query results
     */
    protected function select(string $query, array $bindings = []): array
    {
        try {
            return DB::connection($this->connection)->select($query, $bindings);
        } catch (\Throwable $e) {
            Log::error("MciBaseRepository Query Error: {$e->getMessage()}", [
                'query' => $query,
                'bindings' => $bindings,
            ]);
            throw $e;
        }
    }

    /**
     * Get data with caching
     */
    protected function remember(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, $this->cacheTtl, $callback);
    }

    /**
     * Get data without cache
     */
    protected function rememberForever(string $key, \Closure $callback): mixed
    {
        return Cache::rememberForever($key, $callback);
    }

    /**
     * Forget cache key
     */
    protected function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Generate cache key
     */
    protected function cacheKey(string $suffix): string
    {
        return "mci:{$this->tableName}:{$suffix}";
    }

    /**
     * Format currency untuk display
     */
    protected function formatRupiah(mixed $value): string
    {
        $num = (float) ($value ?? 0);
        return 'Rp ' . number_format($num, 0, ',', '.');
    }

    /**
     * Format number tanpa decimal
     */
    protected function formatNumber(mixed $value, int $decimals = 0): string
    {
        return number_format((float) ($value ?? 0), $decimals, ',', '.');
    }

    /**
     * Format persentase
     */
    protected function formatPercent(mixed $value, int $decimals = 2): string
    {
        return number_format((float) ($value ?? 0), $decimals, ',', '.') . '%';
    }

    /**
     * Hitung growth percentage
     */
    protected function calculateGrowth(float $current, float $previous): array
    {
        if ($previous == 0) {
            return [
                'value' => '0%',
                'class' => 'text-muted',
                'raw' => 0,
            ];
        }

        $growth = (($current - $previous) / $previous) * 100;
        $isPositive = $growth > 0;
        $isNeutral = abs($growth) < 0.01;

        if ($isNeutral) {
            return [
                'value' => '0%',
                'class' => 'text-muted',
                'raw' => 0,
            ];
        }

        return [
            'value' => number_format($growth, 2, ',', '.') . '%',
            'class' => $isPositive ? 'text-success' : 'text-danger',
            'raw' => $growth,
            'direction' => $isPositive ? 'up' : 'down',
        ];
    }

    /**
     * Validate required parameters
     */
    protected function validateRequired(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (! isset($data[$field]) || $data[$field] === '') {
                throw new InvalidArgumentException("Required field '{$field}' is missing");
            }
        }
    }

    /**
     * Get date range untuk periode
     */
    protected function getPeriodeRange(int $year, int $monthsBack = 12): array
    {
        $periods = [];
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        for ($i = 0; $i < $monthsBack; $i++) {
            $m = ($currentMonth - $i) <= 0 ? $currentMonth - $i + 12 : $currentMonth - $i;
            $y = ($currentMonth - $i) <= 0 ? $currentYear - 1 : $currentYear;
            $periods[] = $y . str_pad($m, 2, '0', STR_PAD_LEFT);
        }

        return array_reverse($periods);
    }
}