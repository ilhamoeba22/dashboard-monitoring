<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\FinancingRestrukturisasiRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use App\Services\Mci\MciConnectionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * FinancingRestrukturisasiRepository
 *
 * Sumber Legacy: FinancingRestrukturisasiController.php & FinancingTopUpController.php
 * Tabel Utama : TOFLMBHP (addendum restrukturisasi), TOFLMB (kontrak aktif)
 *
 * SQL Server 2008 Compatible - No TRY_CONVERT, No EOMONTH, No FORMAT().
 */
class FinancingRestrukturisasiRepository extends MciBaseRepository implements FinancingRestrukturisasiRepositoryInterface
{
    /** @var array<string, mixed> */
    private array $lastPeriodMeta = [];

    protected function getTableName(): string
    {
        return 'TOFLMBHP';
    }

    public function getLastPeriodMeta(): array
    {
        return $this->lastPeriodMeta;
    }

    // =========================================================================
    // G6 - RESTRUKTURISASI / ADDENDUM
    // =========================================================================

    /**
     * Dapatkan daftar pembiayaan yang direstrukturisasi.
     * Sumber: TOFLMBHP JOIN TOFLMB, SETUPLOAN (2x), AO, WILAYAH
     * Legacy ref: FinancingRestrukturisasiController@index
     *
     * @param  array<string, mixed>  $filters
     */
    public function getRestrukturisasi(array $filters = []): Collection
    {
        $start  = microtime(true);
        $memory = memory_get_usage(true);
        $periodContext = $this->resolvePeriodContext($filters);
        $periode = (string) $periodContext['requested_period'];

        $cacheKey = 'financing:restrukturisasi:list:' . md5(serialize($filters).json_encode($periodContext));

        if (! $periodContext['period_available']) {
            return collect([]);
        }

        $result = $this->remember($cacheKey, function () use ($filters, $periode): array {
            $query = DB::connection($this->connection)
                ->table('TOFLMBHP as a')
                ->leftJoin('TOFLMB as b', 'a.nokontrak', '=', 'b.nokontrak')
                ->leftJoin('SETUPLOAN as c', 'a.kdprdo', '=', 'c.kdprd')
                ->leftJoin('SETUPLOAN as d', 'a.kdprdn', '=', 'd.kdprd')
                ->leftJoin('AO as e', 'b.kdaoh', '=', 'e.kdao')
                ->leftJoin('WILAYAH as f', 'b.kdwil', '=', 'f.kodewil')
                ->select([
                    'b.nocif',
                    DB::raw('LTRIM(RTRIM(a.nokontrak)) as nokontrak'),
                    'a.ke',
                    DB::raw("ISNULL((
                        SELECT MAX(CASE WHEN ISNUMERIC(ISNULL(hp.ke, 0)) = 1 THEN CAST(hp.ke AS INT) ELSE 0 END)
                        FROM TOFLMBHP hp
                        WHERE LTRIM(RTRIM(hp.nokontrak)) = LTRIM(RTRIM(a.nokontrak))
                    ), 0) as total_restrukturisasi"),
                    DB::raw("ISNULL((
                        SELECT COUNT(1)
                        FROM TOFLMBHP hp
                        WHERE LTRIM(RTRIM(hp.nokontrak)) = LTRIM(RTRIM(a.nokontrak))
                    ), 0) as jumlah_riwayat_restrukturisasi"),
                    DB::raw('LTRIM(RTRIM(b.nama)) as nama'),
                    
                    DB::raw("CASE WHEN ISDATE(NULLIF(LTRIM(RTRIM(a.tglakado)),'')) = 1 THEN CONVERT(VARCHAR(10), CONVERT(DATE, LTRIM(RTRIM(a.tglakado)), 112), 105) ELSE '-' END as tglakad_lama"),
                    'a.jwo as jw_lama',
                    DB::raw("CASE WHEN ISDATE(NULLIF(LTRIM(RTRIM(a.jto)),'')) = 1 THEN CONVERT(VARCHAR(10), CONVERT(DATE, LTRIM(RTRIM(a.jto)), 112), 105) ELSE '-' END as tglexp_lama"),
                    
                    DB::raw("CASE WHEN ISDATE(NULLIF(LTRIM(RTRIM(a.tglakadn)),'')) = 1 THEN CONVERT(VARCHAR(10), CONVERT(DATE, LTRIM(RTRIM(a.tglakadn)), 112), 105) ELSE '-' END as tglakad_baru"),
                    'a.jwn as jw_baru',
                    DB::raw("CASE WHEN ISDATE(NULLIF(LTRIM(RTRIM(a.jtn)),'')) = 1 THEN CONVERT(VARCHAR(10), CONVERT(DATE, LTRIM(RTRIM(a.jtn)), 112), 105) ELSE '-' END as tglexp_baru"),
                    
                    DB::raw('LTRIM(RTRIM(a.noakado)) as noakad_lama'),
                    DB::raw('LTRIM(RTRIM(a.noakadn)) as noakad_baru'),
                    
                    DB::raw("ISNULL(LTRIM(RTRIM(c.ket)), '-') as akad_lama"),
                    DB::raw("ISNULL(LTRIM(RTRIM(d.ket)), '-') as akad_baru"),
                    
                    DB::raw('LTRIM(RTRIM(a.colold)) as col_sblm_rest'),
                    DB::raw('LTRIM(RTRIM(a.colnew)) as col_stlh_rest'),
                    DB::raw('LTRIM(RTRIM(b.colbaru)) as col_berjalan'),
                    
                    DB::raw("ISNULL(LTRIM(RTRIM(a.ket)), '-') as ket_rest"),
                    DB::raw('CAST(ISNULL(b.mdlawal, 0) AS FLOAT) as mdlawal'),
                    DB::raw('CAST(ISNULL(a.osmdl, 0) AS FLOAT) as osmdl_lama'),
                    DB::raw('CAST(ISNULL(a.osmdln, 0) AS FLOAT) as osmdl_baru'),
                    DB::raw('CAST(ISNULL(b.osmdlc, 0) AS FLOAT) as osmdlc_saat_ini'),
                    DB::raw('CAST(ISNULL(b.osmgnc, 0) AS FLOAT) as osmgnc_saat_ini'),
                    
                    'b.frekmdl as frekmdl_lama', 'a.frekpokn as frekmdl_baru',
                    'b.frekmgn as frekmgn_lama', 'a.frekmgnn as frekmgn_baru',
                    
                    DB::raw('LTRIM(RTRIM(a.inpuser)) as inpuser'),
                    DB::raw("CASE WHEN ISDATE(NULLIF(LTRIM(RTRIM(LEFT(a.inptgl, 8))),'')) = 1 THEN CONVERT(VARCHAR(10), CONVERT(DATE, LEFT(a.inptgl, 8), 112), 105) ELSE '-' END as input_tgl"),
                    
                    'b.kdaoh as kd_ao',
                    DB::raw("ISNULL(LTRIM(RTRIM(e.nmao)), 'TANPA AO') as nama_ao"),
                    DB::raw("ISNULL(LTRIM(RTRIM(f.ket)), 'TANPA WILAYAH') as kantor_pelayanan")
                ])
                ->where('b.stsrec', 'A')
                ->where('b.stsacc', '<>', 'W');

            $query->where(function ($q) use ($periode) {
                $q->whereRaw("LEFT(LTRIM(RTRIM(ISNULL(a.tglakadn, ''))), 6) = ?", [$periode])
                    ->orWhereRaw("LEFT(LTRIM(RTRIM(ISNULL(a.inptgl, ''))), 6) = ?", [$periode]);
            });

            if (!empty($filters['cabang'])) {
                $query->where(DB::raw('LTRIM(RTRIM(f.ket))'), 'LIKE', '%' . $filters['cabang'] . '%');
            }

            if (!empty($filters['ao'])) {
                $query->where('b.kdaoh', $filters['ao']);
            }

            $rawResults = $query->orderBy('b.nama', 'asc')->orderBy('a.ke', 'desc')->get();
            
            return $rawResults->map(function ($row) {
                return [
                    'nocif'           => trim((string) ($row->nocif ?? '')),
                    'nokontrak'       => trim((string) ($row->nokontrak ?? '')),
                    'ke'              => (int) ($row->ke ?? 0),
                    'total_restrukturisasi' => (int) ($row->total_restrukturisasi ?? $row->ke ?? 0),
                    'jumlah_riwayat_restrukturisasi' => (int) ($row->jumlah_riwayat_restrukturisasi ?? 0),
                    'nama'            => (string) ($row->nama ?? ''),
                    'tglakad_lama'    => (string) ($row->tglakad_lama ?? '-'),
                    'jw_lama'         => (int) ($row->jw_lama ?? 0),
                    'tglexp_lama'     => (string) ($row->tglexp_lama ?? '-'),
                    'tglakad_baru'    => (string) ($row->tglakad_baru ?? '-'),
                    'jw_baru'         => (int) ($row->jw_baru ?? 0),
                    'tglexp_baru'     => (string) ($row->tglexp_baru ?? '-'),
                    'noakad_lama'     => (string) ($row->noakad_lama ?? '-'),
                    'noakad_baru'     => (string) ($row->noakad_baru ?? '-'),
                    'akad_lama'       => (string) ($row->akad_lama ?? '-'),
                    'akad_baru'       => (string) ($row->akad_baru ?? '-'),
                    'col_sblm_rest'   => (string) ($row->col_sblm_rest ?? '0'),
                    'col_stlh_rest'   => (string) ($row->col_stlh_rest ?? '0'),
                    'col_berjalan'    => (string) ($row->col_berjalan ?? '0'),
                    'ket_rest'        => (string) ($row->ket_rest ?? '-'),
                    'mdlawal'         => (float) ($row->mdlawal ?? 0),
                    'osmdl_lama'      => (float) ($row->osmdl_lama ?? 0),
                    'osmdl_baru'      => (float) ($row->osmdl_baru ?? 0),
                    'osmdlc_saat_ini' => (float) ($row->osmdlc_saat_ini ?? 0),
                    'osmgnc_saat_ini' => (float) ($row->osmgnc_saat_ini ?? 0),
                    'frekmdl_lama'    => (int) ($row->frekmdl_lama ?? 0),
                    'frekmdl_baru'    => (int) ($row->frekmdl_baru ?? 0),
                    'frekmgn_lama'    => (int) ($row->frekmgn_lama ?? 0),
                    'frekmgn_baru'    => (int) ($row->frekmgn_baru ?? 0),
                    'nama_ao'         => (string) ($row->nama_ao ?? 'TANPA AO'),
                    'kantor_pelayanan'=> (string) ($row->kantor_pelayanan ?? '-'),
                    'inpuser'         => (string) ($row->inpuser ?? '-'),
                    'input_tgl'       => (string) ($row->input_tgl ?? '-'),
                ];
            })->toArray();
        }, self::CACHE_SHORT);

        $this->logPerformance(__METHOD__, $start, $memory);

        return collect($result);
    }

    /**
     * Summary metrics untuk scorecard Restrukturisasi.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getRestrukturisasiSummary(array $filters = []): array
    {
        $data = $this->getRestrukturisasi($filters);

        $uniqueNasabah = $data->pluck('nocif')->unique()->count();
        $avgKe         = $data->avg('total_restrukturisasi') ?? 0;
        $totalKontrak  = $data->count();
        $totalOsBaru   = $data->sum('osmdl_baru');
        $totalOsSaatIni = $data->sum('osmdlc_saat_ini');
        $frekuensiTinggi = $data->filter(fn ($r) => (int) ($r['total_restrukturisasi'] ?? 0) >= 3)->count();
        $osFrekuensiTinggi = $data
            ->filter(fn ($r) => (int) ($r['total_restrukturisasi'] ?? 0) >= 3)
            ->sum('osmdlc_saat_ini');

        // Distribusi perubahan kol (sebelum vs sesudah)
        $membaik  = $data->filter(fn ($r) => (int)$r['col_stlh_rest'] < (int)$r['col_sblm_rest'])->count();
        $memburuk = $data->filter(fn ($r) => (int)$r['col_stlh_rest'] > (int)$r['col_sblm_rest'])->count();
        $tetap    = $totalKontrak - $membaik - $memburuk;

        return [
            'total_kontrak'   => $totalKontrak,
            'total_nasabah'   => $uniqueNasabah,
            'avg_ke'          => (float) $avgKe,
            'kol_membaik'     => $membaik,
            'kol_memburuk'    => $memburuk,
            'kol_tetap'       => $tetap,
            'total_os_baru'   => $totalOsBaru,
            'total_os_saat_ini' => $totalOsSaatIni,
            'frekuensi_tinggi' => $frekuensiTinggi,
            'os_frekuensi_tinggi' => $osFrekuensiTinggi,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function resolvePeriodContext(array $filters): array
    {
        $active = $this->getCurrentPeriodInternal();
        $reqTahun = isset($filters['tahun']) && (int) $filters['tahun'] > 0 ? (int) $filters['tahun'] : (int) $active['year'];
        $reqBulan = isset($filters['bulan']) && (int) $filters['bulan'] > 0 ? max(1, min(12, (int) $filters['bulan'])) : (int) $active['month'];
        $periode = sprintf('%04d%02d', $reqTahun, $reqBulan);
        $isHistorical = ($reqTahun !== (int) $active['year'] || $reqBulan !== (int) $active['month']);
        $snapshotDb = $isHistorical ? $this->resolveMonthlySnapshotDatabase($reqTahun, $reqBulan) : null;
        $periodAvailable = true;

        if ($snapshotDb !== null) {
            app(MciConnectionService::class)->switchToDatabase($snapshotDb);
        } elseif ($isHistorical) {
            $periodAvailable = false;
        }

        $sourceDb = $snapshotDb
            ?: DB::connection($this->connection)->selectOne('SELECT DB_NAME() as database_name')->database_name ?? null;

        $this->lastPeriodMeta = [
            'requested_period' => $periode,
            'active_period' => (string) $active['period'],
            'is_historical' => $isHistorical,
            'period_available' => $periodAvailable,
            'source_table' => 'TOFLMBHP',
            'source_database' => $sourceDb,
            'message' => $periodAvailable
                ? null
                : "Database snapshot untuk periode {$periode} belum dikonfigurasi.",
        ];

        return $this->lastPeriodMeta;
    }

    private function resolveMonthlySnapshotDatabase(int $year, int $month): ?string
    {
        $monthPrefixes = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGT', 'SEP', 'OKT', 'NOV', 'DES'];
        $yearSuffix = substr((string) $year, -2);
        $monthPrefix = $monthPrefixes[$month - 1];
        $envKey = 'MCI_DB_'.$monthPrefix.$yearSuffix;
        $database = env($envKey);

        if (is_string($database) && $database !== '') {
            return $database;
        }

        $endOfMonth = (new \DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))
            ->modify('last day of this month')
            ->format('dmY');

        $cacheKey = "mci:restrukturisasi:snapshot-db:{$year}:{$month}";

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($monthPrefix, $yearSuffix, $endOfMonth): ?string {
            $row = DB::connection($this->connection)->selectOne(
                "
                SELECT TOP 1 name
                FROM sys.databases
                WHERE name LIKE ?
                   OR name LIKE ?
                   OR name LIKE ?
                ORDER BY
                    CASE
                        WHEN name LIKE ? THEN 1
                        WHEN name LIKE ? THEN 2
                        ELSE 3
                    END,
                    name DESC
                ",
                [
                    "MCI_{$monthPrefix}{$yearSuffix}_%",
                    "MCI_{$monthPrefix}_%{$endOfMonth}",
                    "MCI_{$monthPrefix}%{$endOfMonth}",
                    "MCI_{$monthPrefix}{$yearSuffix}_%",
                    "MCI_{$monthPrefix}_%{$endOfMonth}",
                ]
            );

            return is_object($row) && isset($row->name) ? (string) $row->name : null;
        });
    }



    // =========================================================================
    // G6 - TOP-UP
    // =========================================================================

    /**
     * Dapatkan daftar kontrak top-up pada periode (bulan) berjalan CBS.
     * Sumber: TOFLMB self-JOIN via nocif, filter tgllunas sekitar tgleff bulan aktif.
     * Legacy ref: FinancingTopUpController@index
     *
     * @param  array<string, mixed>  $filters
     */
    public function getTopUp(array $filters = []): Collection
    {
        $start  = microtime(true);
        $memory = memory_get_usage(true);
        $periodContext = $this->resolvePeriodContext($filters);
        $this->lastPeriodMeta['source_table'] = 'TOFLMB self-join';
        $periodeYm = (string) $periodContext['requested_period'];

        $cacheKey = 'financing:topup:list:' . md5(serialize($filters).json_encode($periodContext));

        if (! $periodContext['period_available']) {
            return collect([]);
        }

        $result = $this->remember($cacheKey, function () use ($periodeYm, $filters): array {
            $query = DB::connection($this->connection)
                ->table('TOFLMB as a')
                ->join('TOFLMB as b', 'a.nocif', '=', 'b.nocif')
                ->leftJoin('AO as d', 'a.kdaoh', '=', 'd.kdao')
                ->leftJoin('AO as e', 'b.kdaoh', '=', 'e.kdao')
                ->select([
                    'a.nocif',
                    DB::raw('LTRIM(RTRIM(a.nama)) as nama'),
                    DB::raw('LTRIM(RTRIM(a.nokontrak)) as kontrak_lama'),
                    DB::raw('LTRIM(RTRIM(b.nokontrak)) as kontrak_baru'),
                    DB::raw('CAST(ISNULL(a.mdlawal, 0) AS DECIMAL(18,2)) as plafon_lama'),
                    DB::raw('CAST(ISNULL(b.mdlawal, 0) AS DECIMAL(18,2)) as plafon_baru'),
                    DB::raw('CAST(ISNULL(b.mdlawal, 0) - ISNULL(a.mdlawal, 0) AS DECIMAL(18,2)) as selisih_plafon'),
                    DB::raw('CAST(ISNULL(a.osmdlc, 0) AS DECIMAL(18,2)) as os_lama_saat_lunas'),
                    DB::raw('CAST(ISNULL(b.osmdlc, 0) AS DECIMAL(18,2)) as os_baru_saat_ini'),
                    DB::raw('LTRIM(RTRIM(a.colbaru)) as coll_lama'),
                    DB::raw('LTRIM(RTRIM(b.colbaru)) as coll_baru'),

                    // Format Tanggal Lunas
                    DB::raw("CASE WHEN LEN(LTRIM(RTRIM(a.tgllunas))) = 8 THEN SUBSTRING(a.tgllunas,7,2)+'-'+SUBSTRING(a.tgllunas,5,2)+'-'+SUBSTRING(a.tgllunas,1,4) ELSE LTRIM(RTRIM(a.tgllunas)) END as tgl_lunas"),

                    // Format Tanggal Efektif Baru
                    DB::raw("CASE WHEN LEN(LTRIM(RTRIM(b.tgleff))) = 8 THEN SUBSTRING(b.tgleff,7,2)+'-'+SUBSTRING(b.tgleff,5,2)+'-'+SUBSTRING(b.tgleff,1,4) ELSE LTRIM(RTRIM(b.tgleff)) END as tgl_efektif_baru"),

                    'a.jw as jw_lama',
                    'b.jw as jw_baru',

                    // AO Lama & Baru
                    DB::raw("ISNULL(LTRIM(RTRIM(d.nmao)), 'TANPA AO') as nama_ao_lama"),
                    DB::raw("ISNULL(LTRIM(RTRIM(e.nmao)), 'TANPA AO') as nama_ao_baru"),
                    DB::raw('LTRIM(RTRIM(d.kdao)) as kdao_lama'),
                    DB::raw('LTRIM(RTRIM(e.kdao)) as kdao_baru'),

                    // Kalkulasi Analitik
                    DB::raw("DATEDIFF(DAY, CONVERT(DATETIME, LTRIM(RTRIM(a.tgllunas)), 112), CONVERT(DATETIME, LTRIM(RTRIM(b.tgleff)), 112)) as selisih_hari"),

                    DB::raw("CASE
                        WHEN DATEDIFF(DAY, CONVERT(DATETIME, LTRIM(RTRIM(a.tgllunas)), 112), CONVERT(DATETIME, LTRIM(RTRIM(b.tgleff)), 112)) BETWEEN 0 AND 3 THEN 'Top Up'
                        WHEN DATEDIFF(DAY, CONVERT(DATETIME, LTRIM(RTRIM(a.tgllunas)), 112), CONVERT(DATETIME, LTRIM(RTRIM(b.tgleff)), 112)) > 30 THEN 'Retention'
                        ELSE 'Ulangan'
                    END as analisa_nasabah"),

                    DB::raw("CASE
                        WHEN b.mdlawal > a.mdlawal THEN 'Kenaikan'
                        WHEN b.mdlawal < a.mdlawal THEN 'Penurunan'
                        ELSE 'Tetap'
                    END as analisa_limit"),

                    DB::raw("CASE WHEN a.kdaoh <> b.kdaoh THEN 'Pindah AO' ELSE 'AO Tetap' END as status_ao")
                ])
                ->whereRaw("LEN(LTRIM(RTRIM(ISNULL(a.tgllunas, '')))) = 8")
                ->whereRaw("LEN(LTRIM(RTRIM(ISNULL(b.tgleff, '')))) = 8")
                ->whereRaw("ISDATE(LTRIM(RTRIM(a.tgllunas))) = 1")
                ->whereRaw("ISDATE(LTRIM(RTRIM(b.tgleff))) = 1")
                ->whereRaw("SUBSTRING(LTRIM(RTRIM(a.tgllunas)), 1, 6) = ?", [$periodeYm])
                ->whereRaw("SUBSTRING(LTRIM(RTRIM(b.tgleff)), 1, 6) = ?", [$periodeYm])
                ->whereColumn('a.nokontrak', '<>', 'b.nokontrak')
                ->whereColumn('a.tgllunas', '<=', 'b.tgleff')
                ->whereColumn('a.stsrec', '<>', 'b.stsrec');

            if (!empty($filters['ao_baru'])) {
                $query->where(DB::raw('LTRIM(RTRIM(e.kdao))'), $filters['ao_baru']);
            }

            $rawResults = $query->orderBy('b.tgleff', 'desc')->orderBy('a.nama', 'asc')->get();
            
            return $rawResults->map(function ($row) {
                return [
                    'nocif'           => trim((string) ($row->nocif ?? '')),
                    'nama'            => (string) ($row->nama ?? ''),
                    'kontrak_lama'    => trim((string) ($row->kontrak_lama ?? '')),
                    'kontrak_baru'    => trim((string) ($row->kontrak_baru ?? '')),
                    'plafon_lama'     => (float) ($row->plafon_lama ?? 0),
                    'plafon_baru'     => (float) ($row->plafon_baru ?? 0),
                    'selisih_plafon'  => (float) ($row->selisih_plafon ?? 0),
                    'os_lama_saat_lunas' => (float) ($row->os_lama_saat_lunas ?? 0),
                    'os_baru_saat_ini'=> (float) ($row->os_baru_saat_ini ?? 0),
                    'coll_lama'       => (string) ($row->coll_lama ?? '0'),
                    'coll_baru'       => (string) ($row->coll_baru ?? '0'),
                    'tgl_lunas'       => (string) ($row->tgl_lunas ?? '-'),
                    'tgl_efektif_baru'=> (string) ($row->tgl_efektif_baru ?? '-'),
                    'jw_lama'         => (int) ($row->jw_lama ?? 0),
                    'jw_baru'         => (int) ($row->jw_baru ?? 0),
                    'nama_ao_lama'    => (string) ($row->nama_ao_lama ?? 'TANPA AO'),
                    'nama_ao_baru'    => (string) ($row->nama_ao_baru ?? 'TANPA AO'),
                    'kdao_lama'       => trim((string) ($row->kdao_lama ?? '')),
                    'kdao_baru'       => trim((string) ($row->kdao_baru ?? '')),
                    'selisih_hari'    => (int) ($row->selisih_hari ?? 0),
                    'analisa_nasabah' => (string) ($row->analisa_nasabah ?? 'Ulangan'),
                    'analisa_limit'   => (string) ($row->analisa_limit ?? 'Tetap'),
                    'status_ao'       => (string) ($row->status_ao ?? 'AO Tetap'),
                ];
            })->toArray();
        }, self::CACHE_SHORT);

        $this->logPerformance(__METHOD__, $start, $memory);

        return collect($result);
    }

    /**
     * Summary metrics untuk scorecard Top-Up.
     *
     * @return array<string, mixed>
     */
    public function getTopUpSummary(array $filters = []): array
    {
        $data = $this->getTopUp($filters);

        $total        = $data->count();
        $totalVolume  = $data->sum('plafon_baru');
        $totalDelta   = $data->sum('selisih_plafon');
        $totalOsBaru  = $data->sum('os_baru_saat_ini');
        $pindahAo     = $data->where('status_ao', 'Pindah AO')->count();

        // Distribusi Analisa Nasabah
        $countTopUp    = $data->where('analisa_nasabah', 'Top Up')->count();
        $countUlangan  = $data->where('analisa_nasabah', 'Ulangan')->count();
        $countRetention= $data->where('analisa_nasabah', 'Retention')->count();

        // Distribusi Analisa Limit
        $countNaik     = $data->where('analisa_limit', 'Kenaikan')->count();
        $countTurun    = $data->where('analisa_limit', 'Penurunan')->count();
        $countTetap    = $data->where('analisa_limit', 'Tetap')->count();

        return [
            'total_kontrak'     => $total,
            'total_volume'      => $totalVolume,
            'total_delta_plafon'=> $totalDelta,
            'total_os_baru'     => $totalOsBaru,
            'count_topup'       => $countTopUp,
            'count_ulangan'     => $countUlangan,
            'count_retention'   => $countRetention,
            'count_naik'        => $countNaik,
            'count_turun'       => $countTurun,
            'count_tetap'       => $countTetap,
            'count_pindah_ao'   => $pindahAo,
        ];
    }

}