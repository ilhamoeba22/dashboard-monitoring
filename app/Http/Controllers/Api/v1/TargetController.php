<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Exports\TargetTemplateExport;
use App\Imports\TargetImport;
use App\Services\Mci\TargetManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TargetController extends Controller
{
    private TargetManagementService $service;

    public function __construct(TargetManagementService $service)
    {
        $this->service = $service;
    }

    /**
     * Get list of annual targets
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $year = $request->get('year', date('Y'));
            $data = $this->service->getTargetList((int) $year);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Enterprise Target vs Realization Dashboard
     * Returns scorecards, pacing chart, and AO leaderboard in one call.
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $year = (int) $request->get('year', date('Y'));
            $month = $request->get('month') ? (int) $request->get('month') : null;

            $data = $this->service->getExecutiveAnalytics($year, $month);

            if (!$data['has_data']) {
                return response()->json([
                    'success' => true,
                    'has_data' => false,
                    'year' => $data['year'],
                    'message' => $data['message'],
                ]);
            }

            return response()->json(array_merge(['success' => true], $data));
        } catch (\Throwable $e) {
            \Log::channel('metrics')->error('TargetDashboard Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error'   => 'Gagal memuat dashboard target: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle error uniformly
     */
    private function handleError(\Throwable $e): JsonResponse
    {
        \Log::channel('metrics')->error('TargetController Error', [
            'error' => $e->getMessage(),
        ]);
        return response()->json([
            'success' => false,
            'error'   => $e->getMessage(),
        ], 500);
    }

    /**
     * Download Excel Template for Target RBB
     */
    public function downloadTemplate()
    {
        try {
            $content = \Maatwebsite\Excel\Facades\Excel::raw(
                new TargetTemplateExport, 
                \Maatwebsite\Excel\Excel::XLSX
            );

            return response($content)
                ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->header('Content-Disposition', 'attachment; filename="Template_Target_RBB.xlsx"');
        } catch (\Throwable $e) {
            \Log::error('Template Download Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Gagal mengunduh template: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Import RBB Target from Excel
     * 
     * IMPORTANT: This method uses manual validation (no framework validators)
     * to prevent 302 redirects that cause MethodNotAllowedHttpException.
     * Laravel's validation throws ValidationException which redirects back
     * for non-XHR requests — even on API routes when OPcache is involved.
     */
    public function importRbb(Request $request): JsonResponse
    {
        try {
            // Guard: Reject GET requests (caused by redirect after failed validation)
            if ($request->isMethod('GET')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Endpoint ini hanya menerima POST request dengan file Excel.',
                ], 405);
            }

            // Manual validation — NO framework Validator to avoid any redirect
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'error' => 'File Excel wajib diunggah.',
                ], 422);
            }

            $file = $request->file('file');
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'error' => 'File upload gagal atau corrupt. Silakan coba lagi.',
                ], 422);
            }

            $year = $request->input('year');
            if (empty($year) || !is_numeric($year) || (int)$year < 2000 || (int)$year > 2100) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tahun target harus berupa angka antara 2000-2100.',
                ], 422);
            }

            $year = (int) $year;

            Excel::import(
                new TargetImport($this->service, $year), 
                $file,
                null,
                \Maatwebsite\Excel\Excel::XLSX
            );

            return response()->json([
                'success' => true,
                'message' => 'Data target RBB berhasil di-import.',
            ]);
        } catch (\Throwable $e) {
            \Log::channel('metrics')->error('Target Import Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal meng-import data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handover/Transfer Target (Anomalous Flow)
     */
    public function transfer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_ao' => 'required|string',
            'to_ao' => 'required|string',
            'effective_month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $this->service->transferTarget(
                $request->input('from_ao'),
                $request->input('to_ao'),
                (int) $request->input('effective_month'),
                (int) $request->input('year')
            );

            return response()->json([
                'success' => true,
                'message' => 'Kewajiban target berhasil dipindahkan.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get target RBB upload history
     */
    public function uploadHistory(): JsonResponse
    {
        try {
            $history = \App\Models\TargetAnnual::select('target_year', \Illuminate\Support\Facades\DB::raw('MAX(updated_at) as last_updated'))
                ->groupBy('target_year')
                ->orderBy('last_updated', 'desc')
                ->get();

            $formatted = $history->map(function ($item) {
                return [
                    'document_name' => 'Dokumen RBB Tahun ' . $item->target_year,
                    'status' => 'Success',
                    'date' => $item->last_updated ? \Carbon\Carbon::parse($item->last_updated)->format('Y-m-d H:i:s') : '-',
                    'year' => $item->target_year
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formatted
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat riwayat unggahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
