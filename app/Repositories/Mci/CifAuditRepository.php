<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\CifAuditRepositoryInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;

class CifAuditRepository implements CifAuditRepositoryInterface
{
    protected string $connection = 'dashboard_data';

    public function getPembiayaanAudit(array $filters, int $perPage = 50): CursorPaginator
    {
        return $this->buildBaseQuery('TOFLMB', $filters)->cursorPaginate($perPage);
    }

    public function getTabunganAudit(array $filters, int $perPage = 50): CursorPaginator
    {
        return $this->buildBaseQuery('TOFTABB', $filters, 'a.kodeaoh')->cursorPaginate($perPage);
    }

    public function getDepositoAudit(array $filters, int $perPage = 50): CursorPaginator
    {
        return $this->buildBaseQuery('TOFDEP', $filters, 'a.kodeaoh')->cursorPaginate($perPage);
    }

    public function getAuditSummary(): array
    {
            $statusCase = $this->statusCaseSql('b', 'f');

            $rows = DB::connection($this->connection)->select("
                WITH base AS (
                    SELECT DISTINCT
                        b.nocif,
                        ISNULL(d.nama, 'Tanpa Cabang') AS cabang,
                        {$statusCase} AS status_cif
                    FROM mCIF b
                    LEFT JOIN mCIFKLG f ON b.nocif = f.nocif AND f.kdhub IN ('S', 'I')
                    LEFT JOIN CABANG d ON b.kdloc = d.kdloc
                    WHERE b.stsrec = 'A'
                )
                SELECT status_cif, COUNT(*) AS total
                FROM base
                GROUP BY status_cif
            ");

            $distribution = collect($rows)->map(fn ($row) => [
                'status' => (string) $row->status_cif,
                'total' => (int) $row->total,
                'color' => match ((string) $row->status_cif) {
                    'Lengkap' => '#10B981',
                    'Belum Lengkap' => '#EF4444',
                    default => '#F59E0B',
                },
            ])->values();

            $total = (int) $distribution->sum('total');
            $lengkap = (int) ($distribution->firstWhere('status', 'Lengkap')['total'] ?? 0);
            $belumLengkap = (int) ($distribution->firstWhere('status', 'Belum Lengkap')['total'] ?? 0);
            $cekUlang = (int) ($distribution->firstWhere('status', 'Cek Ulang')['total'] ?? 0);
            $anomali = $belumLengkap + $cekUlang;

            $topRows = DB::connection($this->connection)->select("
                WITH base AS (
                    SELECT DISTINCT
                        b.nocif,
                        ISNULL(d.nama, 'Tanpa Cabang') AS cabang,
                        {$statusCase} AS status_cif
                    FROM mCIF b
                    LEFT JOIN mCIFKLG f ON b.nocif = f.nocif AND f.kdhub IN ('S', 'I')
                    LEFT JOIN CABANG d ON b.kdloc = d.kdloc
                    WHERE b.stsrec = 'A'
                )
                SELECT TOP 5 cabang, COUNT(*) AS anomali
                FROM base
                WHERE status_cif <> 'Lengkap'
                GROUP BY cabang
                ORDER BY anomali DESC, cabang ASC
            ");

            $branchRows = DB::connection($this->connection)->select("
                WITH base AS (
                    SELECT DISTINCT
                        b.nocif,
                        ISNULL(d.nama, 'Tanpa Cabang') AS cabang,
                        {$statusCase} AS status_cif
                    FROM mCIF b
                    LEFT JOIN mCIFKLG f ON b.nocif = f.nocif AND f.kdhub IN ('S', 'I')
                    LEFT JOIN CABANG d ON b.kdloc = d.kdloc
                    WHERE b.stsrec = 'A'
                )
                SELECT TOP 12
                    cabang,
                    COUNT(*) AS total,
                    SUM(CASE WHEN status_cif = 'Lengkap' THEN 1 ELSE 0 END) AS lengkap,
                    SUM(CASE WHEN status_cif = 'Belum Lengkap' THEN 1 ELSE 0 END) AS belum_lengkap,
                    SUM(CASE WHEN status_cif = 'Cek Ulang' THEN 1 ELSE 0 END) AS cek_ulang
                FROM base
                GROUP BY cabang
                ORDER BY (SUM(CASE WHEN status_cif <> 'Lengkap' THEN 1 ELSE 0 END) * 1.0 / NULLIF(COUNT(*), 0)) DESC,
                    total DESC,
                    cabang ASC
            ");

            $sampleRows = DB::connection($this->connection)->select("
                WITH base AS (
                    SELECT DISTINCT
                        b.nocif,
                        b.nm AS namanasabah,
                        b.noid AS noktp,
                        b.hp AS nohp,
                        b.nmibu AS nama_ibu,
                        b.kota,
                        ISNULL(d.nama, 'Tanpa Cabang') AS cabang,
                        CASE b.stskawin WHEN 'L' THEN 'Lajang' WHEN 'K' THEN 'Kawin' WHEN 'D' THEN 'Duda/Janda' ELSE 'NULL' END AS ket_stskawin,
                        f.nama AS nama_pasangan,
                        f.noid AS nik_pasangan,
                        {$statusCase} AS status_cif
                    FROM mCIF b
                    LEFT JOIN mCIFKLG f ON b.nocif = f.nocif AND f.kdhub IN ('S', 'I')
                    LEFT JOIN CABANG d ON b.kdloc = d.kdloc
                    WHERE b.stsrec = 'A'
                )
                SELECT TOP 50 *
                FROM base
                WHERE status_cif <> 'Lengkap'
                ORDER BY
                    CASE status_cif WHEN 'Belum Lengkap' THEN 1 ELSE 2 END,
                    cabang ASC,
                    namanasabah ASC
            ");

            return [
                'database' => (string) (DB::connection($this->connection)->getDatabaseName() ?? '-'),
                'summary' => [
                    'total_nasabah' => $total,
                    'persen_lengkap' => $total > 0 ? ($lengkap / $total) * 100 : 0,
                    'persen_belum_lengkap' => $total > 0 ? ($belumLengkap / $total) * 100 : 0,
                    'persen_cek_ulang' => $total > 0 ? ($cekUlang / $total) * 100 : 0,
                    'total_anomali' => $anomali,
                ],
                'status_distribusi' => $distribution->all(),
                'top_anomali_cabang' => collect($topRows)->map(fn ($row) => [
                    'cabang' => (string) $row->cabang,
                    'anomali' => (int) $row->anomali,
                ])->values()->all(),
                'branch_quality' => collect($branchRows)->map(function ($row) {
                    $totalBranch = (int) $row->total;
                    $belumLengkapBranch = (int) $row->belum_lengkap;
                    $cekUlangBranch = (int) $row->cek_ulang;

                    return [
                        'cabang' => (string) $row->cabang,
                        'total' => $totalBranch,
                        'lengkap' => (int) $row->lengkap,
                        'belum_lengkap' => $belumLengkapBranch,
                        'cek_ulang' => $cekUlangBranch,
                        'rasio_anomali' => $totalBranch > 0
                            ? (($belumLengkapBranch + $cekUlangBranch) / $totalBranch) * 100
                            : 0,
                    ];
                })->values()->all(),
                'anomaly_samples' => collect($sampleRows)->map(fn ($row) => [
                    'nocif' => (string) $row->nocif,
                    'namanasabah' => (string) $row->namanasabah,
                    'noktp' => (string) $row->noktp,
                    'nohp' => (string) $row->nohp,
                    'nama_ibu' => (string) $row->nama_ibu,
                    'kota' => (string) $row->kota,
                    'cabang' => (string) $row->cabang,
                    'ket_stskawin' => (string) $row->ket_stskawin,
                    'nama_pasangan' => (string) $row->nama_pasangan,
                    'nik_pasangan' => (string) $row->nik_pasangan,
                    'status_cif' => (string) $row->status_cif,
                ])->values()->all(),
            ];
    }

    private function buildBaseQuery(string $table, array $filters, string $aoCol = 'a.kdaoh')
    {
        $golcust = $filters['golcust'] ?? 'I'; // Default to Individu

        $query = DB::connection($this->connection)
            ->table("$table as a")
            ->leftJoin('mCIF as b', 'a.nocif', '=', 'b.nocif')
            ->leftJoin('mCIFKLG as f', function($join) {
                $join->on('b.nocif', '=', 'f.nocif')
                     ->whereIn('f.kdhub', ['S', 'I']);
            })
            ->leftJoin('AO as c', $aoCol, '=', 'c.kdao')
            ->leftJoin('CABANG as d', 'b.kdloc', '=', 'd.kdloc')
            ->leftJoin('WILAYAH as e', 'a.kdwil', '=', 'e.kodewil');

        if ($table === 'TOFLMB') {
            $query->where('a.stsacc', '<>', 'W');
        }
        $query->where('a.stsrec', 'A');
        $query->where('b.golcust', $golcust);

        // Apply filters
        if (!empty($filters['cabang']) && $filters['cabang'] !== 'ALL') {
            // Using nama cabang or kdloc depends on the filter passed, assuming it's passing name for now based on UI
            $query->where('d.nama', $filters['cabang']);
        }
        if (!empty($filters['status']) && $filters['status'] !== 'ALL') {
            $query->whereRaw('('.$this->statusCaseSql('b', 'f').') = ?', [$filters['status']]);
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('b.nm', 'like', "%{$search}%")
                  ->orWhere('b.noid', 'like', "%{$search}%")
                  ->orWhere('b.nocif', 'like', "%{$search}%");
            });
        }

        // Base Select
        $selects = [
            'b.nocif', 
            'b.nm',
            'b.nm as namanasabah', 
            'b.npwp',
            'b.noid as noktp',
            DB::raw("LEN(ISNULL(b.noid, '')) AS ceknik"),
            DB::raw("CASE WHEN LEN(ISNULL(b.noid, '')) >= 8 AND TRY_CAST(SUBSTRING(b.noid, 7, 2) AS INT) > 31 THEN 'P' ELSE 'L' END AS jk"),
            'b.tmplhr as tempat_lahir',
            DB::raw("CASE WHEN LEN(ISNULL(b.noid, '')) >= 8 AND TRY_CAST(SUBSTRING(b.noid, 7, 2) AS INT) > 31 THEN RIGHT('0' + CAST(TRY_CAST(SUBSTRING(b.noid, 7, 2) AS INT) - 40 AS VARCHAR), 2) ELSE SUBSTRING(ISNULL(b.noid, ''), 7, 2) END AS tgllhr_ktp"),
            DB::raw("CONVERT(VARCHAR(10), TRY_CAST(b.tgllhr AS DATE), 23) AS tgllhr"),
            DB::raw("CASE WHEN TRY_CAST(b.tgllhr AS DATE) IS NULL THEN 0 ELSE DATEDIFF(YEAR, TRY_CAST(b.tgllhr AS DATE), GETDATE()) END AS usia"),
            'b.hp as nohp',
            'b.sandidati as sandi_dati', 
            'b.nmibu as nama_ibu',
            'b.alamat', 
            'b.kelurahan', 
            'b.kecamatan', 
            'b.kota', 
            'b.kdpos as kodepos',
            'c.nmao as nama_marketing',
            'd.nama AS cabang'
        ];

        // Specific selects based on golcust
        if ($golcust === 'I') {
            $selects = array_merge($selects, [
                DB::raw("CASE b.stskawin WHEN 'L' THEN 'Lajang' WHEN 'K' THEN 'Kawin' WHEN 'D' THEN 'Duda/Janda' ELSE 'NULL' END AS ket_stskawin"),
                'f.nama AS nama_pasangan',
                DB::raw("CASE f.kdhub WHEN 'S' THEN 'Suami' WHEN 'I' THEN 'Istri' WHEN 'A' THEN 'Anak' WHEN 'B' THEN 'Bapak' WHEN 'U' THEN 'Ibu' WHEN 'M' THEN 'Bapak Mertua' WHEN 'R' THEN 'Ibu Mertua' WHEN 'K' THEN 'Kakak Kandung' WHEN 'D' THEN 'Adik Kandung' WHEN 'P' THEN 'Kakak Ipar' WHEN 'F' THEN 'Adik Ipar' WHEN 'N' THEN 'Famili Lainnya' WHEN 'X' THEN 'Lainnya' WHEN 'C' THEN 'Cucu' WHEN 'T' THEN 'Pembantu' ELSE 'NULL' END AS ket_kdhub"),
                'f.noid AS nik_pasangan', 
                'f.hp AS hp_pasangan',
                DB::raw("CONVERT(VARCHAR(10), TRY_CAST(f.tgllahir AS DATE), 23) AS tgllhr_pasangan"),
                DB::raw("CASE WHEN TRY_CAST(f.tgllahir AS DATE) IS NULL THEN 0 ELSE DATEDIFF(YEAR, TRY_CAST(f.tgllahir AS DATE), GETDATE()) END AS usia_pasangan"),
                DB::raw($this->statusCaseSql('b', 'f').' AS status_cif')
            ]);
        } else {
            $selects = array_merge($selects, [
                DB::raw("CASE b.stskawin WHEN 'L' THEN 'Lajang' WHEN 'K' THEN 'Kawin' WHEN 'D' THEN 'Duda/Janda' ELSE 'Tidak Diketahui' END AS ket_stskawin"),
                DB::raw("NULL AS nama_pasangan"), 
                DB::raw("NULL AS ket_kdhub"),
                DB::raw("NULL AS nik_pasangan"),
                DB::raw("NULL AS hp_pasangan"),
                DB::raw("NULL AS tgllhr_pasangan"), 
                DB::raw("NULL AS usia_pasangan"),
                DB::raw($this->statusCaseSql('b', 'f').' AS status_cif')
            ]);
        }

        $query->select($selects);

        // Group by all selected columns to distinct records (like legacy code)
        // Note: For simplicity and performance with cursor pagination, 
        // using DISTINCT is better than a massive GROUP BY clause, or avoiding it if not generating duplicates.
        // Legacy used GROUP BY because of potential duplicate AO / mCIFKLG mappings.
        $query->distinct();
        $query->orderBy('b.nm', 'ASC');

        return $query;
    }

    private function statusCaseSql(string $cifAlias = 'b', string $familyAlias = 'f'): string
    {
        $spouseNikValid = "LEN(ISNULL({$familyAlias}.noid, '')) = 16
            AND {$familyAlias}.noid NOT IN (
                '0000000000000000','1111111111111111','2222222222222222','3333333333333333',
                '4444444444444444','5555555555555555','6666666666666666','7777777777777777',
                '8888888888888888','9999999999999999'
            )";

        return "CASE
            WHEN ISNULL({$cifAlias}.stskawin, '') = 'K'
                AND {$spouseNikValid}
                AND NULLIF(LTRIM(RTRIM(ISNULL({$familyAlias}.nama, ''))), '') IS NOT NULL
                AND NULLIF(LTRIM(RTRIM(ISNULL({$familyAlias}.kdhub, ''))), '') IS NOT NULL
                AND TRY_CAST({$familyAlias}.tgllahir AS DATE) IS NOT NULL
            THEN 'Lengkap'
            WHEN ISNULL({$cifAlias}.stskawin, '') = 'K'
                OR NULLIF(LTRIM(RTRIM(ISNULL({$cifAlias}.stskawin, ''))), '') IS NULL
            THEN 'Belum Lengkap'
            ELSE 'Cek Ulang'
        END";
    }
}
