<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1\Concerns;

use App\Services\Mci\MciConnectionService;
use Illuminate\Http\Request;

trait UsesMciSnapshotDatabase
{
    /**
     * Jalankan callback pada database snapshot yang diminta tanpa mengubah active database permanen.
     *
     * @template T
     *
     * @param  callable(string|null): T  $callback
     * @return T
     */
    private function withRequestedMciDatabase(Request $request, callable $callback): mixed
    {
        $database = trim((string) $request->query('database', ''));

        if ($database === '') {
            return $callback(null);
        }

        $mci = app(MciConnectionService::class);
        $available = $mci->listDatabases();

        if (! $mci->isValidDatabaseName($database) || ! in_array($database, $available, true)) {
            throw new \InvalidArgumentException("Database snapshot tidak tersedia: {$database}");
        }

        $active = $mci->getActiveDatabase();
        $mci->switchToDatabase($database);

        try {
            return $callback($database);
        } finally {
            if ($active !== '') {
                $mci->switchToDatabase($active);
            } else {
                $mci->getConnection();
            }
        }
    }
}
