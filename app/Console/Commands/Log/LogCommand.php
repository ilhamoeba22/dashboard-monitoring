<?php

declare(strict_types=1);

namespace App\Console\Commands\Log;

use App\Services\Logging\CentralLogService;
use Illuminate\Console\Command;

class LogCommand extends Command
{
    protected $signature = 'log
                            {action : Action (view|clear|summary)}
                            {--channel= : Filter by channel (error|api|metrics|slow)}
                            {--lines=100 : Number of lines to view}
                            {--force : Force action without confirmation}';

    protected $description = 'Centralized log management for MCI Dashboard';

    private CentralLogService $logService;

    public function __construct(CentralLogService $logService)
    {
        parent::__construct();
        $this->logService = $logService;
    }

    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'view' => $this->viewLogs(),
            'clear' => $this->clearLogs(),
            'summary' => $this->logSummary(),
            default => $this->invalidAction($action),
        };
    }

    private function viewLogs(): int
    {
        $channel = $this->option('channel');
        $lines = (int) $this->option('lines');

        $logs = $this->logService->getRecentLogs($channel, $lines);

        if (empty($logs)) {
            $this->info('No logs found.');

            return self::SUCCESS;
        }

        $this->info("Recent {$lines} lines:");
        $this->line(str_repeat('-', 120));

        foreach ($logs as $log) {
            $this->line($log);
        }

        $this->line(str_repeat('-', 120));
        $this->info('Total: '.count($logs).' lines');

        return self::SUCCESS;
    }

    private function clearLogs(): int
    {
        $force = $this->option('force');

        if (! $force && ! $this->confirm('Are you sure you want to clear all logs?')) {
            $this->info('Cancelled.');

            return self::SUCCESS;
        }

        $this->logService->clearAll();
        $this->info('All logs cleared successfully.');

        return self::SUCCESS;
    }

    private function logSummary(): int
    {
        $summary = $this->logService->getLogSummary();

        if (empty($summary)) {
            $this->info('No log files found.');

            return self::SUCCESS;
        }

        $headers = ['File', 'Size (KB)', 'Lines', 'Modified'];
        $rows = [];

        foreach ($summary as $file => $data) {
            $rows[] = [
                $file,
                $data['size_kb'],
                $data['lines'],
                $data['modified'],
            ];
        }

        $this->table($headers, $rows);

        $totalSize = array_sum(array_column($summary, 'size_kb'));
        $this->info('Total: '.round($totalSize, 2).' KB');

        return self::SUCCESS;
    }

    private function invalidAction(string $action): int
    {
        $this->error("Invalid action: {$action}");
        $this->info('Valid actions: view, clear, summary');

        return self::FAILURE;
    }
}
