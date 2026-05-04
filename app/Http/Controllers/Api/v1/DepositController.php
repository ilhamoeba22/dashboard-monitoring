<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\DepositRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    private DepositRepositoryInterface $repository;

    public function __construct(DepositRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function nominative(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'cabang' => $request->query('cabang'),
                'ao' => $request->query('ao'),
            ];

            $perPage = (int) $request->query('per_page', '50');
            // #region agent log
            $this->debugLog('H5', 'app/Http/Controllers/Api/v1/DepositController.php:33', 'Deposit controller received per_page', [
                'per_page' => $perPage,
                'cursor' => $request->query('cursor'),
            ]);
            // #endregion
            $data = $this->repository->getNominative($filters, $perPage);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat data nominatif deposito'], 500);
        }
    }

    public function rekapitulasi(Request $request): JsonResponse
    {
        try {
            $groupBy = $request->query('group_by', 'cabang');
            $validGroups = ['cabang', 'wilayah', 'ao'];
            if (! in_array($groupBy, $validGroups)) {
                return response()->json(['success' => false, 'message' => 'Invalid group_by'], 422);
            }

            $data = $this->repository->getRekapitulasi($groupBy);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat rekapitulasi deposito'], 500);
        }
    }

    public function jatuhTempo(Request $request): JsonResponse
    {
        try {
            $filters = ['cabang' => $request->query('cabang')];
            $perPage = (int) $request->query('per_page', '50');
            // #region agent log
            $this->debugLog('H5', 'app/Http/Controllers/Api/v1/DepositController.php:64', 'Deposit jatuh-tempo controller received per_page', [
                'per_page' => $perPage,
                'cursor' => $request->query('cursor'),
            ]);
            // #endregion
            $data = $this->repository->getJatuhTempo($filters, $perPage);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat daftar jatuh tempo'], 500);
        }
    }

    // #region agent log
    private function debugLog(string $hypothesisId, string $location, string $message, array $data = []): void
    {
        $payload = [
            'sessionId' => 'f35f8f',
            'runId' => 'pre-fix',
            'hypothesisId' => $hypothesisId,
            'location' => $location,
            'message' => $message,
            'data' => $data,
            'timestamp' => (int) round(microtime(true) * 1000),
        ];

        @file_put_contents(base_path('debug-f35f8f.log'), json_encode($payload, JSON_UNESCAPED_SLASHES).PHP_EOL, FILE_APPEND);
    }
    // #endregion
}
