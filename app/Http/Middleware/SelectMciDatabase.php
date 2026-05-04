<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Mci\MciConnectionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SelectMciDatabase
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini set database MCI aktif berdasarkan:
     * 1. Parameter query ?mci_database=xxx
     * 2. Session 'mci_database'
     * 3. Default dari config (.env)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil instance service
        $mciService = app(MciConnectionService::class);

        // Prioritas 1: Query parameter
        $dbFromQuery = $request->query('mci_database');
        if ($dbFromQuery && $mciService->isValidDatabaseName($dbFromQuery)) {
            $mciService->setActiveDatabase($dbFromQuery);

            // Simpan ke session untuk request berikutnya
            session(['mci_database' => $dbFromQuery]);

            return $next($request);
        }

        // Prioritas 2: Session
        $dbFromSession = session('mci_database');
        if ($dbFromSession && $mciService->isValidDatabaseName($dbFromSession)) {
            $mciService->setActiveDatabase($dbFromSession);

            return $next($request);
        }

        // Prioritas 3: Default dari config (tidak perlu set lagi jika sama)
        // Service sudah init dengan default dari config

        return $next($request);
    }
}
