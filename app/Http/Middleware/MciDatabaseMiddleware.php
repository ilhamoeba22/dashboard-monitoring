<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Mci\MciConnectionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MciDatabaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Middleware Autopilot: Memastikan koneksi database MCI diset otomatis 
     * sebelum Controller atau Repository bekerja.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Trigger service untuk mendapatkan database aktif (dari cache, env, atau auto-detect)
        // dan melakukan Config::set() secara dinamis.
        app(MciConnectionService::class)->getConnection();

        return $next($request);
    }
}
