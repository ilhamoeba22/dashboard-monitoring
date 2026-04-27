<?php

declare(strict_types=1);

namespace App\Services\Mci;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * MciBaseRepository — Optimized Base Repository for MCI Dashboard
 *
 * IMPLEMENTATION RULES (PROJECT_MEMORY.md):
 *  RULE #1  — No N+1 Query              : Gunakan JOIN, bukan loop query
 *  RULE #3  — Server-side Pagination    : chunk() untuk data >1000 rows
 *  RULE #5  — Query Optimization        : CTE + Window Functions
 *  RULE #6  — Cache Strategy (Multi-layer):
 *               L1 = 60s  (metrics)
 *               L2 = 300s (chart)
 *               L3 = 3600s (static: branches, system_date)
 *  RULE #8  — Lazy Loading OFF          : defined on MciBaseModel
 *  RULE #10 — Monitoring & Logging      : logPerformance() on slow queries
 *  RULE #11 — Repository Pattern        : logic stays here, NOT in controller
 */
abstract class MciBaseRepository
{
    /** SQL Server connection name */
    protected string $connection = 'dashboard_data';

    /** Default connection (untuk reset setelah history query) */
    private string $defaultConnection = 'dashboard_data';

    protected string $tableName = '';

    // Cache TTL constants (RULE #6)
    protected const CACHE_SHORT  = 60;    // 1 min  — live metrics
    protected const CACHE_MEDIUM = 300;   // 5 min  — chart data
    protected const CACHE_LONG   = 3600;  // 1 hour — static (branches, system_date)
    protected const CACHE_DAILY  = 86400; // 24 hr  — snapshots

    // Chunk size for large datasets (RULE #3)
    protected int $chunkSize = 1000;

    abstract protected function getTableName(): string;

    public function __construct()
    {
        $this->tableName          = $this->getTableName();
        $this->defaultConnection  = $this->connection;
    }

    /**
     * Switch ke koneksi database tertentu (untuk query history).
     * Panggil resetConnection() di blok finally setelah selesai.
     */
    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * Kembalikan ke koneksi default (dashboard_data = realtime).
     */
    public function resetConnection(): void
    {
        $this->connection = $this->defaultConnection;
    }

    /**
     * Ambil nama koneksi yang sedang aktif.
     */
    public function getConnectionName(): string
    {
        return $this->connection;
    }

    // =========================================================================
    // QUERY HELPERS
    // =========================================================================

    /**
     * Execute raw SELECT query with optional cache (RULE #6).
     *
     * @param  array<int, mixed>  $bindings
     * @return array<int, object>
     */
    protected function select(
        string $query,
        array $bindings = [],
        ?string $cacheKey = null,
        int $ttl = self::CACHE_SHORT
    ): array {
        $start  = microtime(true);
        $memory = memory_get_usage(true);

        try {
            if ($cacheKey !== null) {
                /** @var array<int, object> $result */
                $result = Cache::remember($cacheKey, $ttl, function () use ($query, $bindings): array {
                    /** @var array<int, object> $rows */
                    $rows = DB::connection($this->connection)->select($query, $bindings);

                    return $rows;
                });
            } else {
                /** @var array<int, object> $result */
                $result = DB::connection($this->connection)->select($query, $bindings);
            }

            $this->logPerformance(__METHOD__, $start, $memory);

            return $result;
        } catch (\Throwable $e) {
            Log::error('MciBaseRepository::select failed', [
                'repository' => static::class,
                'error'      => $e->getMessage(),
                'bindings'   => $bindings,
            ]);

            throw $e;
        }
    }

    /**
     * Process large SQL result sets in chunks to prevent memory exhaustion (RULE #3).
     * Uses SQL Server OFFSET/FETCH pagination under the hood.
     *
     * @param  array<int, mixed>  $bindings
     */
    protected function chunk(string $query, array $bindings, callable $callback, ?int $chunkSize = null): void
    {
        $size   = $chunkSize ?? $this->chunkSize;
        $offset = 0;

        do {
            $pagedQuery = $query . " OFFSET {$offset} ROWS FETCH NEXT {$size} ROWS ONLY";
            /** @var array<int, object> $data */
            $data = DB::connection($this->connection)->select($pagedQuery, $bindings);

            $count = count($data);

            if ($count > 0) {
                $callback($data);
                $offset += $size;
            }

            // Help GC free memory between chunks
            unset($data);
        } while ($count >= $size);  // $count captured before unset
    }

    // =========================================================================
    // CACHE HELPERS
    // =========================================================================

    /**
     * Wrap callback in Cache::remember with consistent TTL.
     *
     * @template T
     * @param  \Closure(): T  $callback
     * @return T
     */
    protected function remember(string $key, \Closure $callback, int $ttl = self::CACHE_SHORT): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Forget a single cache key.
     */
    protected function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Forget multiple exact cache keys.
     * NOTE: Pattern-based wildcard flush is intentionally NOT supported here
     * to avoid accidentally flushing the entire cache store. If Redis tags
     * are available, use Cache::tags([...])->flush() instead.
     *
     * @param  array<int, string>  $keys
     */
    protected function forgetMany(array $keys): void
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Build a namespaced cache key.
     * Pattern: mci:{table}:{connection}:{suffix}
     */
    protected function cacheKey(string $suffix): string
    {
        return "mci:{$this->tableName}:{$this->connection}:{$suffix}";
    }

    /**
     * Build a user-scoped cache key.
     * Pattern: mci:user:{userId|global}:{suffix}
     */
    protected function userCacheKey(string $suffix, ?int $userId = null): string
    {
        return 'mci:user:' . ($userId ?? 'global') . ":{$suffix}";
    }

    // =========================================================================
    // SYSTEM DATE / PERIOD  (shared by all repositories)
    // =========================================================================

    /**
     * Ambil tanggal sistem dari tabel TANGGAL di MCI.
     * Cached 1 jam karena tanggal hanya berubah sekali sehari.
     */
    protected function getSystemDateInternal(): string
    {
        $key = $this->cacheKey('system_date');

        return Cache::remember($key, self::CACHE_LONG, function (): string {
            $tgl = DB::connection($this->connection)
                ->table('TANGGAL')
                ->orderBy('tgl', 'desc')
                ->value('tgl');

            return is_string($tgl) ? $tgl : date('dmY');
        });
    }

    /**
     * Pecah tanggal sistem menjadi komponen year, month, period, previous_year.
     * Format tanggal MCI: ddmmYYYY (contoh: 01042026 → Apr 2026)
     *
     * @return array{tgl: string, year: int, month: int, period: string, previous_year: int}
     */
    protected function getCurrentPeriodInternal(): array
    {
        $tgl   = $this->getSystemDateInternal();
        $year  = (int) substr($tgl, -4);
        $month = (int) substr($tgl, 2, 2);

        return [
            'tgl'           => $tgl,
            'year'          => $year,
            'month'         => $month,
            'period'        => $year . str_pad((string) $month, 2, '0', STR_PAD_LEFT),
            'previous_year' => $year - 1,
        ];
    }

    // =========================================================================
    // FORMATTING HELPERS
    // =========================================================================

    /**
     * Format angka ke Rupiah: "Rp 1.234.567"
     */
    protected function formatRupiah(float $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    /**
     * Format angka dengan desimal opsional.
     */
    protected function formatNumber(float $value, int $decimals = 0): string
    {
        return number_format($value, $decimals, ',', '.');
    }

    /**
     * Format persentase: "12,34%"
     */
    protected function formatPercent(float $value, int $decimals = 2): string
    {
        return number_format($value, $decimals, ',', '.') . '%';
    }

    // =========================================================================
    // GROWTH CALCULATION
    // =========================================================================

    /**
     * Hitung growth percentage antara nilai saat ini vs sebelumnya.
     *
     * @return array{value: string, class: string, raw: float, direction: string|null}
     */
    protected function calculateGrowth(float $current, float $previous): array
    {
        if ($previous == 0.0) {
            return ['value' => '0%', 'class' => 'text-muted', 'raw' => 0.0, 'direction' => null];
        }

        $growth = (($current - $previous) / abs($previous)) * 100;

        if (abs($growth) < 0.01) {
            return ['value' => '0%', 'class' => 'text-muted', 'raw' => 0.0, 'direction' => null];
        }

        $isPositive = $growth > 0;

        return [
            'value'     => number_format($growth, 2, ',', '.') . '%',
            'class'     => $isPositive ? 'text-success' : 'text-danger',
            'raw'       => round($growth, 4),
            'direction' => $isPositive ? 'up' : 'down',
        ];
    }

    // =========================================================================
    // MONITORING (RULE #10)
    // =========================================================================

    /**
     * Log query jika durasi >100ms (RULE #10: Monitoring).
     */
    protected function logPerformance(string $method, float $startTime, int $memoryBefore): void
    {
        $durationMs  = (microtime(true) - $startTime) * 1000;
        $memoryAfter = memory_get_usage(true) / 1024 / 1024;

        if ($durationMs > 100) {
            Log::channel('metrics')->warning('Slow repository query', [
                'class'       => static::class,
                'method'      => $method,
                'duration_ms' => round($durationMs, 2),
                'memory_mb'   => round($memoryAfter, 2),
                'delta_mb'    => round($memoryAfter - ($memoryBefore / 1024 / 1024), 2),
            ]);
        }
    }

    // =========================================================================
    // VALIDATION
    // =========================================================================

    /**
     * Validasi field wajib ada dan tidak kosong.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, string>    $requiredFields
     */
    protected function validateRequired(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (! isset($data[$field]) || $data[$field] === '') {
                throw new InvalidArgumentException("Required field '{$field}' is missing or empty.");
            }
        }
    }
}