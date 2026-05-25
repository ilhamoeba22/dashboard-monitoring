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
        $cacheKey = 'cif_audit_pembiayaan_' . md5(json_encode($filters) . $perPage . request('page', 1));
        return \Illuminate\Support\Facades\Cache::tags(['cif_audit'])->remember($cacheKey, now()->addMinutes(15), function() use ($filters, $perPage) {
            return $this->buildBaseQuery('TOFLMB', $filters)->cursorPaginate($perPage);
        });
    }

    public function getTabunganAudit(array $filters, int $perPage = 50): CursorPaginator
    {
        $cacheKey = 'cif_audit_tabungan_' . md5(json_encode($filters) . $perPage . request('page', 1));
        return \Illuminate\Support\Facades\Cache::tags(['cif_audit'])->remember($cacheKey, now()->addMinutes(15), function() use ($filters, $perPage) {
            return $this->buildBaseQuery('TOFTABB', $filters, 'a.kodeaoh')->cursorPaginate($perPage);
        });
    }

    public function getDepositoAudit(array $filters, int $perPage = 50): CursorPaginator
    {
        $cacheKey = 'cif_audit_deposito_' . md5(json_encode($filters) . $perPage . request('page', 1));
        return \Illuminate\Support\Facades\Cache::tags(['cif_audit'])->remember($cacheKey, now()->addMinutes(15), function() use ($filters, $perPage) {
            return $this->buildBaseQuery('TOFDEP', $filters, 'a.kodeaoh')->cursorPaginate($perPage);
        });
    }

    public function getAuditSummary(): array
    {
        // For the sake of the implementation plan, return some mock counts 
        // In real life we'd sum up the anomalies
        return [
            'total_nasabah' => 0,
            'persen_lengkap' => 0,
            'persen_belum_lengkap' => 0,
            'total_anomali' => 0
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
            'b.nm as namanasabah', 
            'b.npwp',
            'b.noid as noktp',
            DB::raw("LEN(b.noid) AS ceknik"),
            DB::raw("CASE WHEN CAST(SUBSTRING(b.noid, 7, 2) AS INT) > 31 THEN 'P' ELSE 'L' END AS jk"),
            'b.tmplhr as tempat_lahir',
            DB::raw("CASE WHEN CAST(SUBSTRING(b.noid, 7, 2) AS INT) > 31 THEN RIGHT('0' + CAST(CAST(SUBSTRING(b.noid, 7, 2) AS INT) - 40 AS VARCHAR), 2) ELSE SUBSTRING(b.noid, 7, 2) END AS tgllhr_ktp"),
            DB::raw("CAST(b.tgllhr AS DATE) AS tgllhr"),
            DB::raw("ROUND((CAST(DATEDIFF(day, b.tgllhr, GetDate()) AS INT) / 365.25), 0) AS usia"),
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
                DB::raw("CAST(f.tgllahir AS DATE) AS tgllhr_pasangan"),
                DB::raw("ROUND((CAST(DATEDIFF(day, f.tgllahir, GetDate()) AS INT) / 365.25), 0) AS usia_pasangan")
            ]);
        } else {
            $selects = array_merge($selects, [
                DB::raw("CASE b.stskawin WHEN 'L' THEN 'Lajang' WHEN 'K' THEN 'Kawin' WHEN 'D' THEN 'Duda/Janda' ELSE 'Tidak Diketahui' END AS ket_stskawin"),
                DB::raw("NULL AS nama_pasangan"), 
                DB::raw("NULL AS ket_kdhub"),
                DB::raw("NULL AS nik_pasangan"),
                DB::raw("NULL AS hp_pasangan"),
                DB::raw("NULL AS tgllhr_pasangan"), 
                DB::raw("NULL AS usia_pasangan")
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
}
