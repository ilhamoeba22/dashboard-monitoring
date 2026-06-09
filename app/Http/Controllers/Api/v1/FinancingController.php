<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\FinancingQualityActionWorkflow;
use App\Repositories\Interfaces\FinancingRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class FinancingController extends Controller
{
    private FinancingRepositoryInterface $repository;

    public function __construct(FinancingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/v1/financing/nominative
     */
    public function nominative(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'cabang' => $request->query('cabang'),
                'ao' => $request->query('ao'),
                'kol' => $request->query('kol'),
                'type' => $request->query('type'),
                'tahun' => $request->query('tahun'),
                'bulan' => $request->query('bulan'),
            ];

            $perPage = (int) $request->query('per_page', '50');
            // #region agent log
            $this->debugLog('H5', 'app/Http/Controllers/Api/v1/FinancingController.php:36', 'Financing controller received per_page', [
                'per_page' => $perPage,
                'cursor' => $request->query('cursor'),
                'search' => $request->query('search'),
            ]);
            // #endregion

            $data = $this->repository->getNominative($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $data, // Berisi links & meta bawaan CursorPaginator
                'period_meta' => $this->repository->getLastNominativePeriodMeta(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('FinancingController::nominative error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json($this->errorPayload('Gagal memuat data nominatif pembiayaan', $e), 500);
        }
    }

    /**
     * GET /api/v1/financing/aos
     */
    public function aos(): JsonResponse
    {
        try {
            $data = $this->repository->getUniqueAos();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data AO',
            ], 500);
        }
    }

    /**
     * GET /api/v1/financing/cabangs
     */
    public function cabangs(): JsonResponse
    {
        try {
            $data = $this->repository->getUniqueCabangs();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data Cabang',
            ], 500);
        }
    }

    /**
     * GET /api/v1/financing/segmens
     */
    public function segmens(): JsonResponse
    {
        try {
            $data = $this->repository->getUniqueSegmens();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data Segmen',
            ], 500);
        }
    }

    /**
     * GET /api/v1/financing/{nokontrak}/angsuran
     */
    public function angsuran(string $nokontrak): JsonResponse
    {
        try {
            $data = $this->repository->getDetailAngsuran($nokontrak);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kontrak tidak ditemukan',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail angsuran',
            ], 500);
        }
    }

    /**
     * GET /api/v1/financing/rekapitulasi
     * Query Params: ?group_by=cabang (cabang|wilayah|ao|produk|segmen|kolektibilitas)
     */
    public function rekapitulasi(Request $request): JsonResponse
    {
        try {
            $groupBy = $request->query('group_by', 'cabang');

            $validGroups = ['cabang', 'wilayah', 'ao', 'produk', 'segmen', 'kolektibilitas'];
            if (! in_array($groupBy, $validGroups)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter group_by tidak valid. Gunakan: '.implode(', ', $validGroups),
                ], 422);
            }

            $data = $this->repository->getRekapitulasi($groupBy);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json($this->errorPayload('Gagal memuat rekapitulasi pembiayaan', $e), 500);
        }
    }

    /**
     * GET /api/v1/financing/rekap-master
     *
     * Master Rekap Console — Single-hit query dengan breakdown Kol1-Kol5 + NPF Ratio.
     * Mendukung 6 dimensi analisis via parameter group_by.
     *
     * Query Params:
     *   group_by : cabang|wilayah|ao|produk|segmen|sekon  (default: cabang)
     *   cabang   : kode cabang untuk filter (opsional, '' = semua)
     */
    public function rekapMaster(Request $request): JsonResponse
    {
        try {
            $groupBy = (string) $request->query('group_by', 'cabang');
            $cabang  = (string) $request->query('cabang', '');
            $tahun   = (int) $request->query('tahun', 0);
            $bulan   = (int) $request->query('bulan', 0);

            $validGroups = ['cabang', 'wilayah', 'ao', 'produk', 'segmen', 'sekon'];
            if (! in_array($groupBy, $validGroups, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter group_by tidak valid.',
                    'valid'   => $validGroups,
                ], 422);
            }

            $result = $this->repository->getRekapMaster($groupBy, $cabang, $tahun, $bulan);

            return response()->json([
                'success' => true,
                'meta'    => $result['meta'],
                'totals'  => $result['totals'],
                'rows'    => $result['rows']->values(),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json($this->errorPayload('Gagal memuat Master Rekap Console.', $e), 500);
        }
    }

    /**
     * Asset Quality & Risk Analytics — Single-hit query untuk dashboard Quality.
     *
     * Query Params:
     *   group_by : cabang|produk|ao (default: cabang)
     *   cabang   : kode cabang untuk filter (opsional)
     */
    public function qualityAnalytics(Request $request): JsonResponse
    {
        try {
            $groupBy = (string) $request->query('group_by', 'cabang');
            $cabang  = (string) $request->query('cabang', '');
            $tahun   = (int) $request->query('tahun', date('Y'));
            $bulan   = (int) $request->query('bulan', date('m'));
            $segmen  = (string) $request->query('segmen', '');

            $data = $this->repository->getQualityAnalytics($groupBy, $cabang, $tahun, $bulan, $segmen);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json($this->errorPayload('Gagal memuat Asset Quality Analytics', $e), 500);
        }
    }

    public function qualityActionWorkflows(Request $request): JsonResponse
    {
        try {
            if (! Schema::hasTable('financing_quality_action_workflows')) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'indexed' => new \stdClass(),
                    'migration_required' => true,
                    'message' => 'Workflow tindakan belum aktif. Jalankan migration aplikasi terlebih dahulu.',
                ]);
            }

            $tahun = (int) $request->query('tahun', date('Y'));
            $bulan = (int) $request->query('bulan', date('m'));

            $rows = FinancingQualityActionWorkflow::query()
                ->where('period_year', $tahun)
                ->where('period_month', $bulan)
                ->orderByRaw("FIELD(status, 'open', 'in_progress', 'waiting', 'done', 'waived')")
                ->orderByDesc('score')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $rows,
                'indexed' => $rows->keyBy('action_key'),
            ]);
        } catch (\Throwable $e) {
            return response()->json($this->errorPayload('Gagal memuat workflow tindakan kualitas.', $e), 500);
        }
    }

    public function saveQualityActionWorkflow(Request $request): JsonResponse
    {
        try {
            if (! Schema::hasTable('financing_quality_action_workflows')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow tindakan belum aktif. Jalankan migration aplikasi terlebih dahulu.',
                ], 503);
            }

            $validated = $request->validate([
                'period_year' => ['required', 'integer', 'min:2020', 'max:2100'],
                'period_month' => ['required', 'integer', 'min:1', 'max:12'],
                'action_key' => ['required', 'string', 'max:160'],
                'nokontrak' => ['nullable', 'string', 'max:50'],
                'nama' => ['nullable', 'string', 'max:180'],
                'source' => ['nullable', 'string', 'max:60'],
                'signals' => ['nullable', 'array'],
                'severity' => ['nullable', 'string', 'max:20'],
                'score' => ['nullable', 'integer', 'min:0', 'max:1000'],
                'exposure' => ['nullable', 'numeric'],
                'status' => ['required', Rule::in(['open', 'in_progress', 'waiting', 'done', 'waived'])],
                'owner' => ['nullable', 'string', 'max:120'],
                'due_date' => ['nullable', 'date'],
                'note' => ['nullable', 'string', 'max:5000'],
                'reviewed_by' => ['nullable', 'string', 'max:120'],
            ]);

            $validated['completed_at'] = $validated['status'] === 'done' ? now() : null;

            $workflow = FinancingQualityActionWorkflow::updateOrCreate(
                [
                    'period_year' => $validated['period_year'],
                    'period_month' => $validated['period_month'],
                    'action_key' => $validated['action_key'],
                ],
                $validated
            );

            return response()->json([
                'success' => true,
                'data' => $workflow->refresh(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input workflow tindakan tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json($this->errorPayload('Gagal menyimpan workflow tindakan kualitas.', $e), 500);
        }
    }

    /**
     * GET /api/v1/financing/quality-diagnostic
     * Diagnostic endpoint: cek data TOFLMBEOM langsung dari DB
     */
    public function qualityDiagnostic(Request $request): JsonResponse
    {
        try {
            $tahun = (string) $request->query('tahun', date('Y'));
            $conn  = 'dashboard_data';

            // 1. Semua periode yang ada di TOFLMBEOM
            $allPeriodes = \Illuminate\Support\Facades\DB::connection($conn)->select("
                SELECT periode, COUNT(*) as total_rows, MIN(stsrec) as sample_stsrec
                FROM TOFLMBEOM
                GROUP BY periode
                ORDER BY periode DESC
            ");

            // 2. Periode untuk tahun yang diminta
            $forYear = \Illuminate\Support\Facades\DB::connection($conn)->select("
                SELECT periode, stsrec, COUNT(*) as rows
                FROM TOFLMBEOM
                WHERE LEFT(periode, 4) = ?
                GROUP BY periode, stsrec
                ORDER BY periode ASC
            ", [$tahun]);

            // 3. Cek apakah kolom segmen ada
            $colCheck = \Illuminate\Support\Facades\DB::connection($conn)->select("
                SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = 'TOFLMBEOM' AND COLUMN_NAME IN ('segmen','stsrec','stsacc','periode','osmdlc','colbaru','ppap')
            ");

            return response()->json([
                'success'     => true,
                'tahun_query' => $tahun,
                'all_periodes' => array_slice($allPeriodes, 0, 20),
                'for_year'    => $forYear,
                'columns_exist' => $colCheck,
            ]);
        } catch (\Throwable $e) {
            return response()->json($this->errorPayload('Gagal memuat diagnostic kualitas pembiayaan.', $e), 500);
        }
    }

    /**
     * GET /api/v1/financing/jatuh-tempo
     */
    public function jatuhTempo(Request $request): JsonResponse
    {
        try {
            $filters = [
                'cabang' => $request->query('cabang'),
                'ao' => $request->query('ao'),
            ];

            $perPage = (int) $request->query('per_page', '50');
            // #region agent log
            $this->debugLog('H5', 'app/Http/Controllers/Api/v1/FinancingController.php:145', 'Financing jatuh-tempo controller received per_page', [
                'per_page' => $perPage,
                'cursor' => $request->query('cursor'),
            ]);
            // #endregion
            $data = $this->repository->getJatuhTempo($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar jatuh tempo pembiayaan',
            ], 500);
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

    private function errorPayload(string $message, \Throwable $e, array $extra = []): array
    {
        $payload = array_merge([
            'success' => false,
            'message' => $message,
        ], $extra);

        if (config('app.debug')) {
            $payload['debug'] = $e->getMessage();
        }

        return $payload;
    }
    // #endregion
}
