<?php

declare(strict_types=1);

namespace App\Services\Logging;

use Illuminate\Support\Facades\Log;

/**
 * Centralized Logging Service untuk MCI Dashboard
 *
 *Semua log harus melewati service ini untuk:
 * - Rotasi otomatis berdasarkan ukuran file
 * - Kategorisasi log (error, warning, info, debug, metrics)
 * - Centralized storage
 * - Cleanup otomatis
 */
class CentralLogService
{
    private const MAX_LOG_SIZE_KB = 5120; // 5MB per file

    private const MAX_DAYS = 14;          // Keep logs 14 hari

    /**
     * Log error dengan context
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    /**
     * Log warning
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Log info
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    /**
     * Log debug
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Log metrics (performa query, API response time, dll)
     */
    public function metrics(string $metric, array $data = []): void
    {
        $context = array_merge([
            'metric' => $metric,
            'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ], $data);

        $this->logToFile('metrics', $metric, $context);
    }

    /**
     * Log API request
     */
    public function apiRequest(string $endpoint, array $params = [], float $durationMs = 0): void
    {
        $context = [
            'endpoint' => $endpoint,
            'params' => $params,
            'duration_ms' => round($durationMs, 2),
        ];

        $this->logToFile('api', "[API] {$endpoint}", $context);
    }

    /**
     * Log database query (slow query detection)
     */
    public function slowQuery(string $query, float $durationMs, array $bindings = []): void
    {
        $context = [
            'query' => $query,
            'duration_ms' => round($durationMs, 2),
            'bindings' => $bindings,
        ];

        // Only log if > 100ms
        if ($durationMs > 100) {
            $this->logToFile('slow_query', '[SLOW QUERY]', $context);
        }
    }

    /**
     * Log dengan channel spesifik
     */
    public function log(string $level, string $message, array $context = []): void
    {
        // Rotate if needed
        $this->autoRotate();

        // Add standard context
        $context = array_merge([
            'environment' => app()->environment(),
            'request_id' => request()->header('X-Request-ID', uniqid()),
        ], $context);

        Log::$level($message, $context);
    }

    /**
     * Write to specific log file (bypassing Laravel default)
     */
    public function logToFile(string $channel, string $message, array $context = []): void
    {
        $this->autoRotate();

        $filename = "{$channel}-".date('Y-m-d').'.log';
        $path = storage_path("logs/{$filename}");

        $timestamp = date('Y-m-d H:i:s');
        $contextJson = ! empty($context) ? ' | '.json_encode($context, JSON_UNESCAPED_SLASHES) : '';
        $line = "[{$timestamp}] {$message}{$contextJson}\n";

        file_put_contents($path, $line, FILE_APPEND);
    }

    /**
     * Auto rotate log files based on size
     */
    private function autoRotate(): void
    {
        $logFiles = glob(storage_path('logs/*.log'));

        foreach ($logFiles as $file) {
            $sizeKb = filesize($file) / 1024;

            if ($sizeKb > self::MAX_LOG_SIZE_KB) {
                // Rename to .old and create new
                $basename = basename($file, '.log');
                $dir = dirname($file);
                $timestamp = date('His');

                rename($file, "{$dir}/{$basename}-{$timestamp}.old.log");
            }
        }

        // Cleanup old logs
        $this->cleanupOldLogs();
    }

    /**
     * Cleanup logs older than MAX_DAYS
     */
    private function cleanupOldLogs(): void
    {
        $logFiles = glob(storage_path('logs/*.log'));
        $cutoff = strtotime('-14 days');

        foreach ($logFiles as $file) {
            // Skip laravel.log, metrics-*.log - only clean .old files
            if (! str_ends_with($file, '.old.log')) {
                continue;
            }

            if (filemtime($file) < $cutoff) {
                @unlink($file);
            }
        }
    }

    /**
     * Get recent logs for display
     */
    public function getRecentLogs(?string $channel = null, int $lines = 100): array
    {
        $pattern = $channel
            ? storage_path("logs/{$channel}-*.log")
            : storage_path('logs/*.log');

        $logs = [];
        $files = glob($pattern) ?: [];

        // Sort by modification time (newest first)
        usort($files, fn ($a, $b) => filemtime($b) - filemtime($a));

        foreach ($files as $file) {
            if ($lines <= 0) {
                break;
            }

            $content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            $recent = array_slice($content, -$lines);

            $logs = array_merge($logs, $recent);
            $lines -= count($recent);
        }

        return array_slice($logs, -100); // Return max 100 lines
    }

    /**
     * Clear all logs
     */
    public function clearAll(): void
    {
        $logFiles = glob(storage_path('logs/*.log')) ?: [];

        foreach ($logFiles as $file) {
            @unlink($file);
        }

        // Also clear .old files
        $oldFiles = glob(storage_path('logs/*.old.log')) ?: [];
        foreach ($oldFiles as $file) {
            @unlink($file);
        }
    }

    /**
     * Get log file sizes summary
     */
    public function getLogSummary(): array
    {
        $summary = [];
        $logFiles = glob(storage_path('logs/*.log')) ?: [];

        foreach ($logFiles as $file) {
            $basename = basename($file);
            $summary[$basename] = [
                'size_kb' => round(filesize($file) / 1024, 2),
                'modified' => date('Y-m-d H:i:s', filemtime($file)),
                'lines' => count(file($file) ?: []),
            ];
        }

        return $summary;
    }
}
