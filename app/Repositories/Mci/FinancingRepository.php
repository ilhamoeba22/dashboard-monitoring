<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Models\Mci\Financing\Toflmb;
use App\Models\Mci\Financing\Tofrs;
use App\Models\Mci\Master\Cabang;
use App\Models\Mci\Marketing\Ao;
use App\Repositories\Interfaces\FinancingRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use App\Services\Mci\MciConnectionService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FinancingRepository extends MciBaseRepository implements FinancingRepositoryInterface
{
    /** @var array<string, mixed> */
    private array $lastNominativePeriodMeta = [];

    protected function getTableName(): string
    {
        return 'TOFLMB';
    }

    public function getLastNominativePeriodMeta(): array
    {
        return $this->lastNominativePeriodMeta;
    }

    /**
     * Dapatkan daftar data nominatif nasabah pembiayaan dengan Optimized Joins.
     * Menggunakan standard pagination agar mendukung lompatan halaman di UI.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getNominative(array $filters = [], int $perPage = 50): Paginator
    {
        $activePeriod = $this->getCurrentPeriodInternal();
        $currentYear = (int) $activePeriod['year'];
        $currentMonth = (int) $activePeriod['month'];
        $currentPeriod = (string) $activePeriod['period'];
        $reqTahun = isset($filters['tahun']) && (int) $filters['tahun'] > 0 ? (int) $filters['tahun'] : $currentYear;
        $reqBulan = isset($filters['bulan']) && (int) $filters['bulan'] > 0 ? (int) $filters['bulan'] : $currentMonth;
        $reqBulan = max(1, min(12, $reqBulan));
        $isRequestedHistorical = ($reqTahun !== $currentYear || $reqBulan !== $currentMonth);
        $monthlySnapshotDatabase = $isRequestedHistorical
            ? $this->resolveMonthlySnapshotDatabase($reqTahun, $reqBulan)
            : null;
        $usingMonthlySnapshotDatabase = $monthlySnapshotDatabase !== null;
        $isHistoris = $isRequestedHistorical && ! $usingMonthlySnapshotDatabase;
        $tableName = $isHistoris ? 'TOFLMBEOM' : 'TOFLMB';
        $periode = sprintf('%04d%02d', $reqTahun, $reqBulan);
        $periodAvailable = true;

        if ($usingMonthlySnapshotDatabase) {
            app(MciConnectionService::class)->switchToDatabase($monthlySnapshotDatabase);
        }

        if ($isHistoris) {
            $periodAvailable = DB::connection($this->connection)
                ->table('TOFLMBEOM')
                ->where('periode', $periode)
                ->exists();
        }

        $this->lastNominativePeriodMeta = [
            'requested_period' => $periode,
            'active_period' => $currentPeriod,
            'is_historical' => $isRequestedHistorical,
            'period_available' => $periodAvailable,
            'source_table' => $tableName,
            'source_database' => $usingMonthlySnapshotDatabase
                ? $monthlySnapshotDatabase
                : DB::connection($this->connection)->selectOne('SELECT DB_NAME() as database_name')->database_name ?? null,
            'message' => $periodAvailable
                ? null
                : "Data periode {$periode} belum tersedia di database historis TOFLMBEOM.",
        ];

        if (! $periodAvailable) {
            return new LengthAwarePaginator(
                [],
                0,
                $perPage,
                LengthAwarePaginator::resolveCurrentPage(),
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                ]
            );
        }

        $hasSegmenColumn = $this->tableHasColumn($tableName, 'segmen');

        // G2 Data Entry: Rebuild dengan Logika Proyek Legacy (Murni Query Builder untuk Performa)
        $query = DB::connection($this->connection)->table("{$tableName} as a")
            ->leftJoin('AO as b', 'a.kdaoh', '=', 'b.kdao')
            ->leftJoin('mCIF as c', 'a.nocif', '=', 'c.nocif')
            ->leftJoin('CABANG as e', 'a.kdloc', '=', 'e.kdloc')
            ->leftJoin('WILAYAH as f', 'a.kdwil', '=', 'f.kodewil')
            ->leftJoin('SETUPLOAN as i', 'a.kdprd', '=', 'i.kdprd');

        if (! $isHistoris) {
            $query->leftJoin('TOFTABC as d', 'a.acpok', '=', 'd.notab');
        }

        if ($hasSegmenColumn) {
            $query->leftJoin('SEGMEN as h', 'a.segmen', '=', 'h.kdseg');
        }

        $segmenSelect = $hasSegmenColumn ? 'a.segmen' : DB::raw('NULL as segmen');
        $segmenNameSelect = $hasSegmenColumn ? 'h.ket as nm_segmen' : DB::raw('NULL as nm_segmen');
        $haritgkSelect = $isHistoris
            ? DB::raw('ISNULL(CAST(a.haritgkmdl AS DECIMAL(18,4)), 0) as haritgk')
            : 'a.haritgk';
        $accountSelect = $isHistoris ? DB::raw('NULL as acpok') : 'a.acpok';
        $saldoBlokSelect = $isHistoris ? DB::raw('0 as saldoblok') : 'd.saldoblok';
        $saldoAkhirSelect = $isHistoris ? DB::raw('0 as sahirrp') : 'd.sahirrp';
        $totalBayarSelect = $isHistoris
            ? DB::raw("(
                SELECT COUNT(*)
                FROM TOFRS
                WHERE TOFRS.nokontrak = a.nokontrak
                  AND TOFRS.stsbyr IN ('L', 'LUNAS')
                  AND (
                    LEFT(ISNULL(NULLIF(TOFRS.tglbyrmdl, ''), ISNULL(TOFRS.tglbyrmgn, '')), 6) <= '{$periode}'
                  )
            ) as total_bayar")
            : DB::raw("(SELECT COUNT(*) FROM TOFRS WHERE TOFRS.nokontrak = a.nokontrak AND TOFRS.stsbyr IN ('L', 'LUNAS')) as total_bayar");

        $query->select([
                'a.nokontrak', 'a.nocif', 'a.nama', 'c.tgllhr', $segmenSelect, $segmenNameSelect,
                'a.tgleff', 'a.jw', 'a.kdjw', 'a.tglexp', 'a.noakad', 'a.mdlawal', 'a.osmdlc',
                'a.tgkmdl', 'a.tgkmgn', $haritgkSelect, 'a.tglmacet', 'a.colbaru',
                'a.htgagun', 'a.ppap', 'c.alamat', $accountSelect, $saldoBlokSelect,
                $saldoAkhirSelect, 'a.rateeff', 'a.rateflat', 'a.kdaoh', 'b.nmao',
                'e.nama as nama_cabang', 'f.ket as nama_wilayah', 'i.ket as nama_produk',
                // Rule #11: Subquery untuk total_bayar (menghindari JOIN + GROUP BY massal)
                $totalBayarSelect,
                DB::raw("'{$periode}' as periode_data"),
                DB::raw(($isHistoris ? "'TOFLMBEOM'" : "'TOFLMB'") . ' as sumber_data')
            ])
            ->whereIn('a.stsrec', ['A', 'N'])
            ->where('a.stsacc', '<>', 'W');

        if ($isHistoris) {
            $query->where('a.periode', $periode);
        }

        // Filter Type (Logic from Legacy)
        if (! empty($filters['type'])) {
            if ($filters['type'] === 'sindikasi') {
                $query->where(function ($q) use ($isHistoris) {
                    if (! $isHistoris) {
                        $q->where('a.lb_jnspiutang', '10');
                    } else {
                        $q->where('a.jnspemb', '10');
                    }

                    $q->orWhereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('TOFLMSINDIKASI as s')
                            ->whereColumn('s.nokontrak', 'a.nokontrak')
                            ->whereIn('s.stsrec', ['A', 'N']);
                    });
                });
            } elseif ($filters['type'] === 'karyawan') {
                $query->where('c.stskaryawan', 'Y');
            }
        }

        // Filter: Pencarian Nama atau No Kontrak (Prefix 'a.' untuk menghindari Ambiguous Column)
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('a.nama', 'like', "%{$search}%")
                  ->orWhere('a.nokontrak', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['cabang'])) {
            $query->where('e.nama', 'like', "%{$filters['cabang']}%");
        }

        if (! empty($filters['kol'])) {
            $query->where('a.colbaru', $filters['kol']);
        }

        if (! empty($filters['ao'])) {
            $query->where('a.kdaoh', $filters['ao']);
        }

        // Urutkan default (Nama nasabah)
        $query->orderBy('a.nama', 'asc');

        // Rule #3: Server-Side Pagination
        $result = $query->paginate($perPage);

        // Transformasi koleksi (Logic Business mapping)
        $result->getCollection()->transform(function ($item) use ($isHistoris) {
            $age = $item->tgllhr ? Carbon::parse($item->tgllhr)->age : 0;

            // Saldo Netto logic: sahirrp - (saldoblok + buffer 20k)
            $sahirrp = (float) ($item->sahirrp ?? 0);
            $saldoblok = (float) ($item->saldoblok ?? 0);
            $saldo_netto = $isHistoris ? null : max(0, $sahirrp - ($saldoblok + 20000));
            $tunggakanVsTabungan = $isHistoris ? null : $saldo_netto - ($item->tgkmdl + $item->tgkmgn);

            return [
                'nokontrak' => trim((string)$item->nokontrak),
                'nocif' => trim((string)$item->nocif),
                'nama' => $item->nama,
                'umur' => $age,
                'kelompok_umur' => $this->getKelompokUmur($item->tgllhr),
                'segmen' => $item->nm_segmen ?? 'N/A',
                'noakad' => $item->noakad,
                'tgleff' => $this->formatSafeDate((string)$item->tgleff),
                'jw' => (int)$item->jw,
                'sisajw' => (int)$item->jw - (int)$item->total_bayar,
                'tglexp' => $this->formatSafeDate((string)$item->tglexp),
                'mdlawal' => (float)$item->mdlawal,
                'osmdlc' => (float)$item->osmdlc,
                'tgkmdl' => (float)$item->tgkmdl,
                'tgkmgn' => (float)$item->tgkmgn,
                'haritgk' => (int)$item->haritgk,
                'colbaru' => $item->colbaru,
                'tglmacet' => $this->formatSafeDate((string)$item->tglmacet),
                'saldo_netto' => $saldo_netto,
                'keterangan_debet' => $isHistoris ? 'Tidak tersedia' : (($saldo_netto > ($item->tgkmdl + $item->tgkmgn)) ? 'Cukup' : 'Kurang'),
                'tunggakan_vs_tabungan' => $tunggakanVsTabungan,
                'htgagun' => (float)$item->htgagun,
                'ppap' => (float)$item->ppap,
                'ao' => $item->nmao ?? 'N/A',
                'cabang' => $item->nama_cabang ?? 'N/A',
                'wilayah' => $item->nama_wilayah ?? 'N/A',
                'alamat' => $item->alamat,
                'produk' => $item->nama_produk ?? 'N/A',
                'periode_data' => $item->periode_data ?? null,
                'sumber_data' => $item->sumber_data ?? 'TOFLMB',
            ];
        });

        return $result;
    }

    private function tableHasColumn(string $tableName, string $columnName): bool
    {
        $cacheKey = "mci:column_exists:{$this->connection}:{$tableName}:{$columnName}";

        return Cache::remember($cacheKey, self::CACHE_LONG, function () use ($tableName, $columnName): bool {
            return DB::connection($this->connection)
                ->table('INFORMATION_SCHEMA.COLUMNS')
                ->where('TABLE_NAME', $tableName)
                ->where('COLUMN_NAME', $columnName)
                ->exists();
        });
    }

    private function resolveMonthlySnapshotDatabase(int $year, int $month): ?string
    {
        $monthPrefixes = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGT', 'SEP', 'OKT', 'NOV', 'DES'];
        $yearSuffix = substr((string) $year, -2);
        $envKey = 'MCI_DB_'.$monthPrefixes[$month - 1].$yearSuffix;
        $database = env($envKey);

        return is_string($database) && $database !== '' ? $database : null;
    }

    /**
     * Dapatkan daftar unik nama AO (Marketing) untuk filter dropdown.
     */
    public function getUniqueAos(): Collection
    {
        return Ao::query()
            ->select('kdao', 'nmao')
            ->whereNotNull('nmao')
            ->orderBy('nmao')
            ->get();
    }

    /**
     * Dapatkan daftar unik Cabang untuk filter dropdown.
     */
    public function getUniqueCabangs(): Collection
    {
        return Cabang::query()
            ->select('kdloc', 'nama')
            ->whereNotNull('nama')
            ->orderBy('kdloc')
            ->get();
    }

    /**
     * Dapatkan daftar unik Segmen untuk filter dropdown.
     */
    public function getUniqueSegmens(): Collection
    {
        return DB::connection($this->connection)->table('SEGMEN')
            ->select('kdseg', 'ket')
            ->whereNotNull('ket')
            ->orderBy('ket')
            ->get();
    }

    /**
     * Dapatkan rincian angsuran per kontrak.
     */
    public function getDetailAngsuran(string $nokontrak): array
    {
        // 1. Ambil Header Pembiayaan
        $header = Toflmb::query()
            ->with(['cif:nocif,alamat', 'produk:kdprd,ket', 'tabunganPokok:notab,saldoblok,sahirrp'])
            ->where('nokontrak', $nokontrak)
            ->whereIn('stsrec', ['A', 'N'])
            ->firstOrFail();

        // 2. Ambil List Jadwal Angsuran
        $details = Tofrs::query()
            ->where('nokontrak', $nokontrak)
            ->orderBy('tgltagih', 'asc')
            ->get();

        // 3. Kalkulasi Saldo Netto
        $sahirrp = (float) optional($header->tabunganPokok)->sahirrp;
        $saldoblok = (float) optional($header->tabunganPokok)->saldoblok;
        $saldo_netto = max(0, $sahirrp - ($saldoblok + 20000));

        return [
            'header' => [
                'nama' => ucwords(strtolower((string) $header->nama)),
                'nocif' => $header->nocif,
                'nokontrak' => $header->nokontrak,
                'noakad' => $header->noakad,
                'alamat' => optional($header->cif)->alamat,
                'produk' => optional($header->produk)->ket,
                'colbaru' => $header->colbaru,
                'tenor' => $header->jw,
                'mdlawal' => $header->mdlawal,
                'mgn_awal' => $header->mgnawal,
                'osmdlc' => $header->osmdlc,
                'osmgnc' => $header->osmgnc,
                'saldo_netto' => $saldo_netto,
                'tglakadn' => $this->formatSafeDate((string) $header->tglakadn),
                'tglexp' => $this->formatSafeDate((string) $header->tglexp),
            ],
            'details' => $details->map(function ($item) {
                return [
                    'tgltagih' => $this->formatSafeDate((string) $item->tgltagih),
                    'tagmdl' => (float) $item->tagmdl,
                    'tagmgn' => (float) $item->tagmgn,
                    'byrmdl' => (float) $item->byrmdl,
                    'byrmgn' => (float) $item->byrmgn,
                    'stsbyr' => (trim((string) $item->stsbyr) === 'L' || trim((string) $item->stsbyr) === 'LUNAS') ? 'LUNAS' : '-',
                    'ratiobh' => number_format((float) $item->ratiobh, 2),
                    'tglbyrmdl' => $this->formatSafeDate((string) $item->tglbyrmdl),
                    'tglbyrmgn' => $this->formatSafeDate((string) $item->tglbyrmgn),
                ];
            })->toArray(),
        ];
    }

    /**
     * Helper formatting tanggal Mci
     */
    private function formatSafeDate(string $date): string
    {
        if (empty($date) || str_starts_with($date, '0000') || str_starts_with($date, '1900')) {
            return '-';
        }

        try {
            return Carbon::parse($date)->format('d M Y');
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Kalkulasi kelompok umur berdasarkan tanggal lahir.
     */
    private function getKelompokUmur(?string $tgllhr): string
    {
        if (empty($tgllhr) || str_starts_with($tgllhr, '0000') || str_starts_with($tgllhr, '1900')) {
            return '-';
        }

        try {
            $age = (int) Carbon::parse($tgllhr)->age;

            if ($age <= 20) {
                return '<=20';
            }
            if ($age <= 30) {
                return '21-30';
            }
            if ($age <= 40) {
                return '31-40';
            }
            if ($age <= 50) {
                return '41-50';
            }
            if ($age <= 60) {
                return '51-60';
            }

            return '>60';
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Kalkulasi keterangan debet (Cukup/Kurang).
     */
    private function getKeteranganDebet(float $sahirrp, float $saldoblok, float $tgkmdl, float $tgkmgn): string
    {
        $saldo_netto = max(0, $sahirrp - ($saldoblok + 20000));
        $kebutuhan = $tgkmdl + $tgkmgn;

        return $saldo_netto > $kebutuhan ? 'Cukup' : 'Kurang';
    }

    /**
     * Kalkulasi tunggakan vs tabungan (selisih saldo netto dengan total tunggakan).
     */
    private function getTunggakanVsTabungan(float $sahirrp, float $saldoblok, float $tgkmdl, float $tgkmgn): float
    {
        $saldo_netto = max(0, $sahirrp - ($saldoblok + 20000));

        return $saldo_netto - ($tgkmdl + $tgkmgn);
    }

    /**
     * Transform result untuk menambah field kalkulasi (UMUR, KELOMPOK UMUR, dll).
     */
    private function transformFinancingRecord(object $item): object
    {
        $tgllhr = (string) ($item->tgllhr ?? '');
        $sahirrp = (float) ($item->sahirrp ?? 0);
        $saldoblok = (float) ($item->saldoblok ?? 0);
        $tgkmdl = (float) ($item->tgkmdl ?? 0);
        $tgkmgn = (float) ($item->tgkmgn ?? 0);

        // Kalkulasi UMUR
        if (! empty($tgllhr) && ! str_starts_with($tgllhr, '0000') && ! str_starts_with($tgllhr, '1900')) {
            try {
                $item->umur = (int) Carbon::parse($tgllhr)->age;
            } catch (\Exception $e) {
                $item->umur = 0;
            }
        } else {
            $item->umur = 0;
        }

        // Kalkulasi KELOMPOK UMUR
        $item->kelompok_umur = $this->getKelompokUmur($tgllhr);

        // Kalkulasi SALDO NETTO
        $item->saldo_netto = max(0, $sahirrp - ($saldoblok + 20000));

        // Kalkulasi KETERANGAN DEBET
        $item->keterangan_debet = $this->getKeteranganDebet($sahirrp, $saldoblok, $tgkmdl, $tgkmgn);

        // Kalkulasi TUNGGAKAN VS TABUNGAN
        $item->tunggakan_vs_tabungan = $this->getTunggakanVsTabungan($sahirrp, $saldoblok, $tgkmdl, $tgkmgn);

        return $item;
    }

    /**
     * Dapatkan data rekapitulasi pembiayaan secara dinamis berdasarkan group_by.
     * Menerapkan Rule #6: Cache Strategy (60 detik via Redis/File)
     */
    public function getRekapitulasi(string $groupBy): Collection
    {
        try {
            $query = Toflmb::query()
                ->whereIn('TOFLMB.stsrec', ['A', 'N'])
                ->where('TOFLMB.stsacc', '<>', 'W');

            $aggregates = [
                DB::raw('COUNT(TOFLMB.nokontrak) AS noa'),
                DB::raw('SUM(TOFLMB.mdlawal) AS total_mdlawal'),
                DB::raw('SUM(TOFLMB.mgnawal) AS total_mgnawal'),
                DB::raw('SUM(TOFLMB.osmdlc) AS total_osmdlc'),
                DB::raw('SUM(TOFLMB.osmgnc) AS total_osmgnc'),
                DB::raw('SUM(TOFLMB.tgkmdl) AS total_tgkmdl'),
                DB::raw('SUM(TOFLMB.tgkmgn) AS total_tgkmgn'),
                DB::raw('SUM(TOFLMB.ppap) AS total_ppap'),
                DB::raw('ROUND(AVG(TOFLMB.rateflat) / 12, 2) AS avg_rate'),
            ];

            match ($groupBy) {
                'cabang' => $query->join('CABANG as b', 'TOFLMB.kdloc', '=', 'b.kdloc')
                    ->select(array_merge(['b.nama as label', 'b.kdloc as id'], $aggregates))
                    ->groupBy('b.nama', 'b.kdloc')
                    ->orderBy('b.nama', 'asc'),

                'wilayah' => $query->join('WILAYAH as b', 'TOFLMB.kdwil', '=', 'b.kodewil')
                    ->select(array_merge(['b.ket as label', 'b.kodewil as id'], $aggregates))
                    ->groupBy('b.ket', 'b.kodewil')
                    ->orderBy('b.ket', 'asc'),

                'ao' => $query->join('AO as b', 'TOFLMB.kdaoh', '=', 'b.kdao')
                    ->select(array_merge(['b.nmao as label', 'TOFLMB.kdaoh as id'], $aggregates))
                    ->groupBy('TOFLMB.kdaoh', 'b.nmao')
                    ->orderBy('b.nmao', 'asc'),

                'produk' => $query->join('SETUPLOAN as b', 'TOFLMB.kdprd', '=', 'b.kdprd')
                    ->select(array_merge(['b.ket as label', 'b.kdprd as id'], $aggregates))
                    ->groupBy('b.ket', 'b.kdprd')
                    ->orderBy('b.ket', 'asc'),

                'segmen' => $query->join('SEGMEN as b', 'TOFLMB.segmen', '=', 'b.kdseg')
                    ->select(array_merge(['b.ket as label', 'TOFLMB.segmen as id'], $aggregates))
                    ->groupBy('TOFLMB.segmen', 'b.ket')
                    ->orderBy('b.ket', 'asc'),

                'kolektibilitas' => $query->select(array_merge([
                    DB::raw("CASE TOFLMB.colbaru WHEN '1' THEN '1. LANCAR' WHEN '2' THEN '2. DPK' WHEN '3' THEN '3. KURANG LANCAR' WHEN '4' THEN '4. DIRAGUKAN' WHEN '5' THEN '5. MACET' ELSE 'LAINNYA' END as label"),
                    'TOFLMB.colbaru as id',
                ], $aggregates))
                    ->groupBy('TOFLMB.colbaru')
                    ->orderBy('TOFLMB.colbaru', 'asc'),

                default => throw new \InvalidArgumentException('Invalid group_by parameter')
            };

            return collect($query->get());
        } catch (\Throwable $e) {
            \Log::error('FinancingRepository::getRekapitulasi Error', ['error' => $e->getMessage()]);
            return collect([]);
        }
    }

    /**
     * Dapatkan rekapitulasi master dengan breakdown kolektibilitas Kol1-Kol5 + NPF Ratio.
     *
     * Fitur utama:
     *  - Single-hit SQL (Conditional Aggregation, bukan PIVOT/looping PHP)
     *  - Mendukung 6 dimensi: cabang, wilayah, ao, produk, segmen, sekon
     *  - NPF Formula standar BI/OJK: (Kol3+Kol4+Kol5 OS) / Total OS × 100%
     *  - Cache 60 detik sesuai RULE #6 MciBaseRepository
     *  - Log query lambat >100ms sesuai RULE #10
     *
     * @param  string  $groupBy  Dimensi: cabang|wilayah|ao|produk|segmen|sekon
     * @param  string  $cabang   Filter kode cabang (opsional, '' = semua)
     * @return array{rows: Collection, totals: array<string,mixed>, meta: array<string,mixed>}
     */
    public function getRekapMaster(string $groupBy = 'cabang', string $cabang = '', int $tahun = 0, int $bulan = 0): array
    {
        $validGroups = ['cabang', 'wilayah', 'ao', 'produk', 'segmen', 'sekon'];
        if (! in_array($groupBy, $validGroups, true)) {
            throw new \InvalidArgumentException("Invalid group_by: {$groupBy}. Valid: ".implode(', ', $validGroups));
        }

        $activePeriod = $this->getCurrentPeriodInternal();
        $reqTahun = $tahun > 0 ? $tahun : (int) $activePeriod['year'];
        $reqBulan = $bulan > 0 ? max(1, min(12, $bulan)) : (int) $activePeriod['month'];
        $periode = sprintf('%04d%02d', $reqTahun, $reqBulan);
        $isRequestedHistorical = ($reqTahun !== (int) $activePeriod['year'] || $reqBulan !== (int) $activePeriod['month']);
        $monthlySnapshotDatabase = $isRequestedHistorical ? $this->resolveMonthlySnapshotDatabase($reqTahun, $reqBulan) : null;
        $usingMonthlySnapshotDatabase = $monthlySnapshotDatabase !== null;
        $tableName = $usingMonthlySnapshotDatabase || ! $isRequestedHistorical ? 'TOFLMB' : 'TOFLMBEOM';
        $periodAvailable = true;

        if ($usingMonthlySnapshotDatabase) {
            app(MciConnectionService::class)->switchToDatabase($monthlySnapshotDatabase);
        }

        if ($isRequestedHistorical && ! $usingMonthlySnapshotDatabase) {
            $periodAvailable = DB::connection($this->connection)
                ->table('TOFLMBEOM')
                ->where('periode', $periode)
                ->exists();
        }

        $sourceDatabase = $usingMonthlySnapshotDatabase
            ? $monthlySnapshotDatabase
            : DB::connection($this->connection)->selectOne('SELECT DB_NAME() as database_name')->database_name ?? null;

        $periodMeta = [
            'requested_period' => $periode,
            'active_period' => (string) $activePeriod['period'],
            'is_historical' => $isRequestedHistorical,
            'period_available' => $periodAvailable,
            'source_table' => $tableName,
            'source_database' => $sourceDatabase,
            'message' => $periodAvailable
                ? null
                : "Data periode {$periode} belum tersedia di database snapshot maupun historis TOFLMBEOM.",
        ];

        if (! $periodAvailable) {
            return [
                'rows' => collect([]),
                'totals' => [
                    'noa' => 0,
                    'total_os' => 0.0,
                    'npf_os' => 0.0,
                    'npf_noa' => 0,
                    'total_ppap' => 0.0,
                    'npf_ratio' => 0.0,
                ],
                'meta' => array_merge($periodMeta, [
                    'group_by' => $groupBy,
                    'generated_at' => now()->toIso8601String(),
                    'row_count' => 0,
                ]),
            ];
        }

        $cacheKey = "financing:rekap_master:{$groupBy}:".($cabang ?: 'all').":{$periode}:{$sourceDatabase}:{$tableName}";
        $start    = microtime(true);
        $memory   = memory_get_usage(true);

        /** @var array<string,mixed> $cached */
        $cached = Cache::remember($cacheKey, 60, function () use ($groupBy, $cabang, $tableName, $isRequestedHistorical, $usingMonthlySnapshotDatabase, $periode, $periodMeta): array {
            // ─── Build dimensi config ─────────────────────────────────────
            $dimConfig = $this->resolveDimensionConfig($groupBy);

            // ─── Conditional Aggregation: Kol 1-5 (NOA & OS) + NPF Ratio ─
            $aggregates = "
                COUNT(a.nokontrak)                                            AS noa,
                SUM(a.osmdlc)                                                 AS total_os,
                SUM(a.ppap)                                                   AS total_ppap,
                SUM(CASE WHEN a.colbaru = '1' THEN 1    ELSE 0 END)          AS kol1_noa,
                SUM(CASE WHEN a.colbaru = '2' THEN 1    ELSE 0 END)          AS kol2_noa,
                SUM(CASE WHEN a.colbaru = '3' THEN 1    ELSE 0 END)          AS kol3_noa,
                SUM(CASE WHEN a.colbaru = '4' THEN 1    ELSE 0 END)          AS kol4_noa,
                SUM(CASE WHEN a.colbaru = '5' THEN 1    ELSE 0 END)          AS kol5_noa,
                SUM(CASE WHEN a.colbaru = '1' THEN a.osmdlc ELSE 0 END)      AS kol1_os,
                SUM(CASE WHEN a.colbaru = '2' THEN a.osmdlc ELSE 0 END)      AS kol2_os,
                SUM(CASE WHEN a.colbaru = '3' THEN a.osmdlc ELSE 0 END)      AS kol3_os,
                SUM(CASE WHEN a.colbaru = '4' THEN a.osmdlc ELSE 0 END)      AS kol4_os,
                SUM(CASE WHEN a.colbaru = '5' THEN a.osmdlc ELSE 0 END)      AS kol5_os,
                SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN a.osmdlc ELSE 0 END) AS npf_os,
                SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN 1    ELSE 0 END)     AS npf_noa,
                CASE WHEN SUM(a.osmdlc) > 0
                    THEN ROUND(
                        SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN a.osmdlc ELSE 0 END)
                        / SUM(a.osmdlc) * 100, 2)
                    ELSE 0
                END AS npf_ratio
            ";

            // ─── Build JOIN & SELECT ──────────────────────────────────────
            $joinClause   = $dimConfig['join'];
            $labelSelect  = $dimConfig['label'];
            $idSelect     = $dimConfig['id'];
            $groupByClause = $dimConfig['group_by'];
            $orderByClause = $dimConfig['order_by'];

            // ─── Optional cabang filter ───────────────────────────────────
            $cabangFilter = '';
            $bindings = [];
            if ($cabang !== '') {
                $cabangFilter = "AND a.kdloc = ?";
                $bindings[] = $cabang;
            }

            $sql = "
                SELECT
                    {$labelSelect} AS label,
                    {$idSelect}    AS id,
                    {$aggregates}
                FROM {$tableName} a
                {$joinClause}
                WHERE a.stsrec IN ('A', 'N')
                  AND a.stsacc <> 'W'
                  ".($isRequestedHistorical && ! $usingMonthlySnapshotDatabase ? 'AND a.periode = ?' : '')."
                  {$cabangFilter}
                GROUP BY {$groupByClause}
                ORDER BY {$orderByClause}
            ";

            if ($isRequestedHistorical && ! $usingMonthlySnapshotDatabase) {
                array_unshift($bindings, $periode);
            }

            /** @var array<int,object> $rows */
            $rows = DB::connection($this->connection)->select($sql, $bindings);
            $collection = collect($rows)->map(fn (object $r) => $this->castRekapRow($r));

            // ─── Calculate Totals ─────────────────────────────────────────
            $totals = [
                'noa'        => (int)   $collection->sum('noa'),
                'total_os'   => (float) $collection->sum('total_os'),
                'npf_os'     => (float) $collection->sum('npf_os'),
                'npf_noa'    => (int)   $collection->sum('npf_noa'),
                'total_ppap' => (float) $collection->sum('total_ppap'),
                'npf_ratio'  => 0.0,
            ];

            if ($totals['total_os'] > 0) {
                $totals['npf_ratio'] = ($totals['npf_os'] / $totals['total_os']) * 100;
            }

            return [
                'rows'   => $collection->toArray(),
                'totals' => $totals,
                'meta'   => [
                    'group_by'     => $groupBy,
                    'generated_at' => now()->toIso8601String(),
                    'row_count'    => $collection->count(),
                ] + $periodMeta,
            ];
        });

        $this->logPerformance(__METHOD__, $start, $memory);

        return [
            'rows'   => collect($cached['rows']),
            'totals' => $cached['totals'],
            'meta'   => $cached['meta'],
        ];
    }

    /**
     * Resolve konfigurasi JOIN, SELECT, dan GROUP BY berdasarkan dimensi.
     *
     * @return array{join: string, label: string, id: string, group_by: string, order_by: string}
     */
    private function resolveDimensionConfig(string $groupBy): array
    {
        return match ($groupBy) {
            'cabang' => [
                'join'     => 'LEFT JOIN CABANG b ON a.kdloc = b.kdloc',
                'label'    => 'ISNULL(b.nama, \'(Tanpa Cabang)\')',
                'id'       => 'ISNULL(a.kdloc, \'\')',
                'group_by' => 'a.kdloc, b.nama',
                'order_by' => 'b.nama ASC',
            ],
            'wilayah' => [
                'join'     => 'LEFT JOIN WILAYAH b ON a.kdwil = b.kodewil',
                'label'    => 'ISNULL(b.ket, \'(Tanpa Wilayah)\')',
                'id'       => 'ISNULL(a.kdwil, \'\')',
                'group_by' => 'a.kdwil, b.ket',
                'order_by' => 'b.ket ASC',
            ],
            'ao' => [
                'join'     => 'LEFT JOIN AO b ON a.kdaoh = b.kdao',
                'label'    => 'ISNULL(b.nmao, \'(Tanpa AO)\')',
                'id'       => 'ISNULL(a.kdaoh, \'\')',
                'group_by' => 'a.kdaoh, b.nmao',
                'order_by' => 'b.nmao ASC',
            ],
            'produk' => [
                'join'     => 'LEFT JOIN SETUPLOAN b ON a.kdprd = b.kdprd',
                'label'    => 'ISNULL(b.ket, \'(Produk Tidak Diketahui)\')',
                'id'       => 'ISNULL(a.kdprd, \'\')',
                'group_by' => 'a.kdprd, b.ket',
                'order_by' => 'b.ket ASC',
            ],
            'segmen' => [
                'join'     => 'LEFT JOIN SEGMEN b ON a.segmen = b.kdseg',
                'label'    => 'ISNULL(b.ket, \'(Segmen Tidak Diketahui)\')',
                'id'       => 'ISNULL(a.segmen, \'\')',
                'group_by' => 'a.segmen, b.ket',
                'order_by' => 'b.ket ASC',
            ],
            'sekon' => [
                // Tidak ada tabel referensi SEKON — kode langsung dari TOFLMB
                'join'     => '',
                'label'    => 'ISNULL(a.sekon, \'(Tanpa Sektor)\')',
                'id'       => 'ISNULL(a.sekon, \'\')',
                'group_by' => 'a.sekon',
                'order_by' => 'a.sekon ASC',
            ],
            default => throw new \InvalidArgumentException("Dimensi tidak valid: {$groupBy}"),
        };
    }

    /**
     * Cast raw SQL row ke tipe data yang tepat (hindari string dari SQLSRV).
     *
     * @return array<string, mixed>
     */
    private function castRekapRow(object $row): array
    {
        return [
            'label'      => (string) ($row->label    ?? ''),
            'id'         => (string) ($row->id       ?? ''),
            'noa'        => (int)    ($row->noa       ?? 0),
            'total_os'   => (float)  ($row->total_os  ?? 0),
            'total_ppap' => (float)  ($row->total_ppap ?? 0),
            'kol1_noa'   => (int)    ($row->kol1_noa  ?? 0),
            'kol2_noa'   => (int)    ($row->kol2_noa  ?? 0),
            'kol3_noa'   => (int)    ($row->kol3_noa  ?? 0),
            'kol4_noa'   => (int)    ($row->kol4_noa  ?? 0),
            'kol5_noa'   => (int)    ($row->kol5_noa  ?? 0),
            'kol1_os'    => (float)  ($row->kol1_os   ?? 0),
            'kol2_os'    => (float)  ($row->kol2_os   ?? 0),
            'kol3_os'    => (float)  ($row->kol3_os   ?? 0),
            'kol4_os'    => (float)  ($row->kol4_os   ?? 0),
            'kol5_os'    => (float)  ($row->kol5_os   ?? 0),
            'npf_os'     => (float)  ($row->npf_os    ?? 0),
            'npf_noa'    => (int)    ($row->npf_noa   ?? 0),
            'npf_ratio'  => (float)  ($row->npf_ratio ?? 0),
        ];
    }

    /**
     * Analisis Kualitas Aset & Risiko Komprehensif — G3 Risk Console.
     * Mencakup: Aging, NPF Trend (bulan Indonesia), Top Obligor Stress Test,
     * AO Collectibility Matrix, Sector/Product Concentration, Restructuring Guard.
     * Standar OJK & PSAK 71.
     */
    public function getQualityAnalytics(string $groupBy = 'cabang', string $cabang = '', int $tahun = 0, int $bulan = 0, string $segmen = ''): array
    {
        $validGroups = ['cabang', 'produk', 'ao'];
        if (! in_array($groupBy, $validGroups, true)) {
            $groupBy = 'cabang';
        }

        $tahunKey  = $tahun > 0 ? $tahun : 'all';
        $bulanKey  = $bulan > 0 ? $bulan : 'all';
        $segmenKey = $segmen ?: 'all';
        $cabangKey = $cabang ?: 'all';

        $cacheKey = "financing:quality_analytics:g10-pkr-trend-ppka:{$groupBy}:{$cabangKey}:{$tahunKey}:{$bulanKey}:{$segmenKey}";
        $start    = microtime(true);
        $memory   = memory_get_usage(true);

        $data = Cache::remember($cacheKey, 60, function () use ($groupBy, $cabang, $tahun, $bulan, $segmen): array {
            $dimConfig     = $this->resolveDimensionConfig($groupBy);
            $joinClause    = $dimConfig['join'];
            $labelSelect   = $dimConfig['label'];

            // Mapping bulan angka ? nama Indonesia (safe, tanpa FORMAT())
            $monthNames = [
                '01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei',
                '06'=>'Jun','07'=>'Jul','08'=>'Ags','09'=>'Sep',
                '10'=>'Okt','11'=>'Nov','12'=>'Des',
            ];

            // ── Filter Builder ──
            $bindCabang = [];
            $strCabang = '';
            if ($cabang !== '') {
                $strCabang = " AND a.kdloc = ?";
                $bindCabang[] = $cabang;
            }

            $bindSegmen = [];
            $strSegmen = '';
            if ($segmen !== '') {
                $strSegmen = " AND a.segmen = ?";
                $bindSegmen[] = $segmen;
            }

            $activePeriod = $this->getCurrentPeriodInternal();
            $currentYear = (int) $activePeriod['year'];
            $currentMonth = (int) $activePeriod['month'];

            $reqTahun = $tahun > 0 ? $tahun : $currentYear;
            $reqBulan = $bulan > 0 ? $bulan : $currentMonth;

            $isRequestedHistorical = ($reqTahun !== $currentYear || $reqBulan !== $currentMonth);
            $monthlySnapshotDatabase = $isRequestedHistorical
                ? $this->resolveMonthlySnapshotDatabase($reqTahun, $reqBulan)
                : null;
            $usingMonthlySnapshotDatabase = $monthlySnapshotDatabase !== null;
            $isHistoris = $isRequestedHistorical && ! $usingMonthlySnapshotDatabase;
            $tableName = $isHistoris ? 'TOFLMBEOM' : 'TOFLMB';

            if ($usingMonthlySnapshotDatabase) {
                app(MciConnectionService::class)->switchToDatabase($monthlySnapshotDatabase);
            }

            $bindPeriode = [];
            $strPeriode = '';
            if ($isHistoris) {
                $periodeStr = sprintf('%04d%02d', $reqTahun, $reqBulan);
                $strPeriode = " AND a.periode = ?";
                $bindPeriode[] = $periodeStr;
            }

            // Gabungan Filter Utama (Cabang + Segmen + Periode)
            $mainFilter = $strCabang . $strSegmen . $strPeriode;
            $mainBindings = array_merge($bindCabang, $bindSegmen, $bindPeriode);

            // Filter CabangCompare (Segmen + Periode, tanpa Cabang)
            $cabangCompareFilter = $strSegmen . $strPeriode;
            $cabangCompareBindings = array_merge($bindSegmen, $bindPeriode);

            // 1. Kolektibilitas OJK Aggregation
            $kolRows = DB::connection($this->connection)->select("
                SELECT a.colbaru as kol, SUM(CAST(a.osmdlc AS DECIMAL(18,4))) AS total_os
                FROM {$tableName} a
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                GROUP BY a.colbaru
            ", $mainBindings);

            // 2. Risk Concentration by Akad
            $akadRows = DB::connection($this->connection)->select("
                SELECT ISNULL(p.ket, 'Tanpa Akad') AS akad,
                    SUM(CAST(a.osmdlc AS DECIMAL(18,4))) AS total_os,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) AS npf_os
                FROM {$tableName} a LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                GROUP BY p.ket ORDER BY npf_os DESC
            ", $mainBindings);

            // 3. Aging Buckets
            // TOFLMBEOM tidak memiliki kolom haritgk — gunakan colbaru untuk klasifikasi aging
            if ($isHistoris) {
                // Untuk data historis (TOFLMBEOM): gunakan colbaru sebagai proxy aging
                // Kol 1 = Lancar (0 hari), Kol 2 = DPK (1-90), Kol 3-5 = NPF (>90)
                $agingRows = DB::connection($this->connection)->select("
                    WITH BaseData AS (
                        SELECT {$labelSelect} AS label, a.colbaru,
                            CAST(a.osmdlc AS DECIMAL(18,4)) as osmdlc
                        FROM {$tableName} a {$joinClause}
                        WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                    )
                    SELECT label,
                        SUM(CASE WHEN colbaru = '1' THEN osmdlc ELSE 0 END) AS aging_0,
                        SUM(CASE WHEN colbaru = '2' THEN osmdlc ELSE 0 END) AS aging_1_30,
                        0 AS aging_31_60,
                        0 AS aging_61_90,
                        SUM(CASE WHEN colbaru IN ('3','4','5') THEN osmdlc ELSE 0 END) AS aging_npf,
                        SUM(osmdlc) AS total_os
                    FROM BaseData GROUP BY label HAVING SUM(osmdlc) > 0 ORDER BY total_os DESC
                ", $mainBindings);
            } else {
                // Untuk data real-time (TOFLMB): gunakan haritgk yang akurat
                $agingRows = DB::connection($this->connection)->select("
                    WITH BaseData AS (
                        SELECT {$labelSelect} AS label, a.haritgk, CAST(a.osmdlc AS DECIMAL(18,4)) as osmdlc
                        FROM {$tableName} a {$joinClause}
                        WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                    )
                    SELECT label,
                        SUM(CASE WHEN haritgk = 0 THEN osmdlc ELSE 0 END) AS aging_0,
                        SUM(CASE WHEN haritgk BETWEEN 1 AND 30 THEN osmdlc ELSE 0 END) AS aging_1_30,
                        SUM(CASE WHEN haritgk BETWEEN 31 AND 60 THEN osmdlc ELSE 0 END) AS aging_31_60,
                        SUM(CASE WHEN haritgk BETWEEN 61 AND 90 THEN osmdlc ELSE 0 END) AS aging_61_90,
                        SUM(CASE WHEN haritgk > 90 THEN osmdlc ELSE 0 END) AS aging_npf,
                        SUM(osmdlc) AS total_os
                    FROM BaseData GROUP BY label HAVING SUM(osmdlc) > 0 ORDER BY total_os DESC
                ", $mainBindings);
            }

            // 4. Branch NPF Comparison
            $branchCompareRows = DB::connection($this->connection)->select("
                SELECT c.nama as cabang,
                    SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                    CASE WHEN SUM(CAST(a.osmdlc AS DECIMAL(18,4))) > 0
                        THEN (SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END)
                              / NULLIF(SUM(CAST(a.osmdlc AS DECIMAL(18,4))), 0)) * 100
                        ELSE 0 END as npf_ratio
                FROM {$tableName} a JOIN CABANG c ON a.kdloc = c.kdloc
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$cabangCompareFilter}
                GROUP BY c.nama ORDER BY npf_ratio DESC
            ", $cabangCompareBindings);

            // 5. Top High-Risk Obligors (Enterprise Grid)
            // haritgk tidak ada di TOFLMBEOM — gunakan 0 sebagai fallback untuk data historis
            if ($isHistoris) {
                $alertRows = DB::connection($this->connection)->select("
                    SELECT TOP 15
                        a.nokontrak, a.nama, ISNULL(p.ket,'Unknown') as jenis_akad,
                        CAST(a.osmdlc AS DECIMAL(18,4)) as osmdlc,
                        ISNULL(CAST(a.tgkmdl AS DECIMAL(18,4)), 0) as tgkmdl,
                        0 as haritgk, a.colbaru,
                        ISNULL(CAST(a.htgagun AS DECIMAL(18,4)), 0) as htgagun,
                        ISNULL(CAST(a.ppap AS DECIMAL(18,4)), 0) as ppap
                    FROM {$tableName} a LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                    WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' AND a.colbaru IN ('2','3','4','5') {$mainFilter}
                    ORDER BY a.osmdlc DESC
                ", $mainBindings);
            } else {
                $alertRows = DB::connection($this->connection)->select("
                    SELECT TOP 15
                        a.nokontrak, a.nama, ISNULL(p.ket,'Unknown') as jenis_akad,
                        CAST(a.osmdlc AS DECIMAL(18,4)) as osmdlc,
                        CAST(a.tgkmdl AS DECIMAL(18,4)) as tgkmdl,
                        a.haritgk, a.colbaru,
                        ISNULL(CAST(a.htgagun AS DECIMAL(18,4)), 0) as htgagun,
                        ISNULL(CAST(a.ppap AS DECIMAL(18,4)), 0) as ppap
                    FROM {$tableName} a LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                    WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' AND a.colbaru IN ('2','3','4','5') {$mainFilter}
                    ORDER BY a.osmdlc DESC, a.haritgk DESC
                ", $mainBindings);
            }

            // 6. NPF Trend bulanan — selalu gunakan TOFLMBEOM (tabel histori End-of-Month)
            // Ambil periode maksimum yang tersedia di TOFLMBEOM untuk tahun yang dipilih
            // Ini memastikan grafik berhenti pada data yang benar-benar ada
            $reqBulanStr = str_pad((string)$reqBulan, 2, '0', STR_PAD_LEFT);

            // Deteksi periode MAX yang tersedia di TOFLMBEOM untuk tahun yang dipilih
            $maxPeriodeResult = DB::connection($this->connection)->select("
                SELECT ISNULL(MAX(periode), '') as max_periode
                FROM TOFLMBEOM
                WHERE LEFT(periode, 4) = ?
                  AND stsrec IN ('A', 'N') AND stsacc <> 'W'
            ", [str_pad((string)$reqTahun, 4, '0', STR_PAD_LEFT)]);
            $maxPeriodeDb = isset($maxPeriodeResult[0]->max_periode) ? (string)$maxPeriodeResult[0]->max_periode : '';

            // Jika tidak ada data EOM untuk tahun dimaksud, coba tanpa filter stsrec
            if ($maxPeriodeDb === '') {
                $maxPeriodeResult2 = DB::connection($this->connection)->select("
                    SELECT ISNULL(MAX(periode), '') as max_periode
                    FROM TOFLMBEOM
                    WHERE LEFT(periode, 4) = ?
                ", [str_pad((string)$reqTahun, 4, '0', STR_PAD_LEFT)]);
                $maxPeriodeDb = isset($maxPeriodeResult2[0]->max_periode) ? (string)$maxPeriodeResult2[0]->max_periode : '';
            }

            // Tentukan bulan atas: min dari (bulan filter, bulan maks yang tersedia)
            $dbMaxBulanStr = $maxPeriodeDb !== '' ? substr($maxPeriodeDb, -2) : $reqBulanStr;
            $effectiveBulanStr = $dbMaxBulanStr < $reqBulanStr ? $dbMaxBulanStr : $reqBulanStr;

            // Filter trend: cabang + segmen (tanpa periode - karena kita mau seluruh tren setahun)
            $trendFilter = $strCabang;
            $trendBindings = $bindCabang;
            if ($segmen !== '') {
                $trendFilter .= " AND a.segmen = ?";
                $trendBindings[] = $segmen;
            }

            // Coba dengan filter stsrec dulu
            $trendRows = DB::connection($this->connection)->select("
                SELECT RIGHT(periode, 2) as bulan,
                    SUM(CAST(osmdlc AS DECIMAL(18,4))) as total_os,
                    SUM(CASE WHEN colbaru IN ('3','4','5') THEN CAST(osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                    SUM(ISNULL(CAST(ppap AS DECIMAL(18,4)), 0)) as total_ppap
                FROM TOFLMBEOM a
                WHERE LEFT(periode, 4) = ? AND RIGHT(periode, 2) <= ?
                  AND stsrec IN ('A', 'N') AND stsacc <> 'W' {$trendFilter}
                GROUP BY periode ORDER BY periode ASC
            ", array_merge([str_pad((string)$reqTahun, 4, '0', STR_PAD_LEFT), $effectiveBulanStr], $trendBindings));

            // Fallback: jika tidak ada hasil, coba tanpa filter stsrec (EOM mungkin stsrec berbeda)
            if (empty($trendRows)) {
                $trendRows = DB::connection($this->connection)->select("
                    SELECT RIGHT(periode, 2) as bulan,
                        SUM(CAST(osmdlc AS DECIMAL(18,4))) as total_os,
                        SUM(CASE WHEN colbaru IN ('3','4','5') THEN CAST(osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                        SUM(ISNULL(CAST(ppap AS DECIMAL(18,4)), 0)) as total_ppap
                    FROM TOFLMBEOM a
                    WHERE LEFT(periode, 4) = ? AND RIGHT(periode, 2) <= ? {$trendFilter}
                    GROUP BY periode ORDER BY periode ASC
                ", array_merge([str_pad((string)$reqTahun, 4, '0', STR_PAD_LEFT), $effectiveBulanStr], $trendBindings));
            }

            // Map angka bulan ke nama Indonesia di PHP
            $trendMapped = array_map(function ($row) use ($monthNames) {
                $bStr = str_pad((string)$row->bulan, 2, '0', STR_PAD_LEFT);
                return (object)[
                    'bulan'      => $monthNames[$bStr] ?? "Bln {$bStr}",
                    'total_os'   => $row->total_os,
                    'npf_os'     => $row->npf_os,
                    'total_ppap' => $row->total_ppap,
                ];
            }, $trendRows);

            // 7. ECL / CKPN Staging (PSAK 71)
            $eclRows = DB::connection($this->connection)->select("
                SELECT
                    SUM(CASE WHEN a.colbaru = '1' THEN CAST(a.ppap AS DECIMAL(18,4)) ELSE 0 END) as ckpn_stage_1,
                    SUM(CASE WHEN a.colbaru = '2' THEN CAST(a.ppap AS DECIMAL(18,4)) ELSE 0 END) as ckpn_stage_2,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.ppap AS DECIMAL(18,4)) ELSE 0 END) as ckpn_stage_3
                FROM {$tableName} a WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ", $mainBindings);
            $eclData = $eclRows[0] ?? (object)['ckpn_stage_1'=>0,'ckpn_stage_2'=>0,'ckpn_stage_3'=>0];

            // 8. Top Obligor (BMPK Stress Test) — Top 10 debitur terbesar
            $topObligorSelect = $isHistoris
                ? "0 as haritgk"
                : "ISNULL(CAST(a.haritgk AS DECIMAL(18,4)), 0) as haritgk";
            $topObligorSegmenSelect = $isHistoris
                ? "'(Tidak tersedia di EOM)' as segmen"
                : "ISNULL(s.ket, '(Tanpa Segmen)') as segmen";
            $topObligorRiskSelect = $isHistoris
                ? "NULL as sekon, NULL as gunadeb"
                : "a.sekon, a.gunadeb";
            $topObligorSegmenJoin = $isHistoris
                ? ''
                : 'LEFT JOIN SEGMEN s ON a.segmen = s.kdseg';

            $topObligorRows = DB::connection($this->connection)->select("
                SELECT TOP 10
                    a.nokontrak,
                    a.nocif,
                    LTRIM(RTRIM(a.nama)) as nama,
                    ISNULL(p.ket, 'Unknown') as jenis_akad,
                    ISNULL(c.nama, '(Tanpa Cabang)') as cabang,
                    ISNULL(w.ket, '(Tanpa Wilayah)') as wilayah,
                    ISNULL(ao.nmao, '(Tanpa AO)') as nama_ao,
                    {$topObligorSegmenSelect},
                    a.noakad,
                    a.tgleff,
                    a.tglexp,
                    a.tglakad,
                    a.kdprd,
                    a.kdloc,
                    a.kdaoh,
                    a.kdwil,
                    {$topObligorRiskSelect},
                    a.colbaru,
                    {$topObligorSelect},
                    ISNULL(CAST(a.mdlawal AS DECIMAL(18,4)), 0) as plafon,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(18,4)), 0) as os,
                    ISNULL(CAST(a.osmgnc AS DECIMAL(18,4)), 0) as os_margin,
                    ISNULL(CAST(a.tgkmdl AS DECIMAL(18,4)), 0) as tunggakan_pokok,
                    ISNULL(CAST(a.tgkmgn AS DECIMAL(18,4)), 0) as tunggakan_margin,
                    ISNULL(CAST(a.ppap AS DECIMAL(18,4)), 0) as ppap,
                    ISNULL(CAST(a.htgagun AS DECIMAL(18,4)), 0) as nilai_agunan
                FROM {$tableName} a
                LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                LEFT JOIN CABANG c ON a.kdloc = c.kdloc
                LEFT JOIN WILAYAH w ON a.kdwil = w.kodewil
                LEFT JOIN AO ao ON a.kdaoh = ao.kdao
                {$topObligorSegmenJoin}
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                ORDER BY a.osmdlc DESC
            ", $mainBindings);

            // 9. AO Collectibility Matrix
            $aoMatrixRows = DB::connection($this->connection)->select("
                SELECT ISNULL(b.nmao, '(Tanpa AO)') as nama_ao,
                    SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                    SUM(CASE WHEN a.colbaru='1' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol1_os,
                    SUM(CASE WHEN a.colbaru='2' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol2_os,
                    SUM(CASE WHEN a.colbaru='3' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol3_os,
                    SUM(CASE WHEN a.colbaru='4' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol4_os,
                    SUM(CASE WHEN a.colbaru='5' THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as kol5_os,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                    CASE WHEN SUM(CAST(a.osmdlc AS DECIMAL(18,4))) > 0 THEN
                        ROUND(SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END)
                        / SUM(CAST(a.osmdlc AS DECIMAL(18,4))) * 100, 2)
                    ELSE 0 END as npf_ratio
                FROM {$tableName} a LEFT JOIN AO b ON a.kdaoh = b.kdao
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                GROUP BY a.kdaoh, b.nmao ORDER BY npf_ratio DESC
            ", $mainBindings);

            // 10. Sector Concentration (Sekon)
            // TOFLMBEOM mungkin tidak memiliki kolom 'sekon' — fallback ke empty array untuk histori
            if ($isHistoris) {
                // Coba dengan sekon, jika gagal return empty
                try {
                    $sectorRows = DB::connection($this->connection)->select("
                        SELECT ISNULL(LTRIM(RTRIM(a.sekon)),'(Tanpa Sektor)') as sektor,
                            SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                            SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                            CASE WHEN SUM(CAST(a.osmdlc AS DECIMAL(18,4))) > 0 THEN
                                ROUND(SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END)
                                / SUM(CAST(a.osmdlc AS DECIMAL(18,4))) * 100, 2)
                            ELSE 0 END as npf_ratio
                        FROM {$tableName} a WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                        GROUP BY a.sekon ORDER BY total_os DESC
                    ", $mainBindings);
                } catch (\Throwable) {
                    $sectorRows = []; // kolom sekon tidak ada di TOFLMBEOM
                }
            } else {
                $sectorRows = DB::connection($this->connection)->select("
                    SELECT ISNULL(LTRIM(RTRIM(a.sekon)),'(Tanpa Sektor)') as sektor,
                        SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                        SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os,
                        CASE WHEN SUM(CAST(a.osmdlc AS DECIMAL(18,4))) > 0 THEN
                            ROUND(SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END)
                            / SUM(CAST(a.osmdlc AS DECIMAL(18,4))) * 100, 2)
                        ELSE 0 END as npf_ratio
                    FROM {$tableName} a WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                    GROUP BY a.sekon ORDER BY total_os DESC
                ", $mainBindings);
            }

            // 11. Product Composition (Donut)
            // SETUPLOAN JOIN dan kdprd — coba, fallback ke empty jika tidak ada
            if ($isHistoris) {
                try {
                    $productRows = DB::connection($this->connection)->select("
                        SELECT ISNULL(p.ket,'Lainnya') as produk,
                            SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                            SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os
                        FROM {$tableName} a LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                        WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                        GROUP BY p.ket ORDER BY total_os DESC
                    ", $mainBindings);
                } catch (\Throwable) {
                    $productRows = [];
                }
            } else {
                $productRows = DB::connection($this->connection)->select("
                    SELECT ISNULL(p.ket,'Lainnya') as produk,
                        SUM(CAST(a.osmdlc AS DECIMAL(18,4))) as total_os,
                        SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN CAST(a.osmdlc AS DECIMAL(18,4)) ELSE 0 END) as npf_os
                    FROM {$tableName} a LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                    WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                    GROUP BY p.ket ORDER BY total_os DESC
                ", $mainBindings);
            }

            // 12. Restructuring Guard (Evergreening Detector)
            // TOFLMBHP JOIN mungkin tidak tersedia untuk data historis
            $mainFilterB = str_replace('a.', 'b.', $mainFilter);

            if ($isHistoris) {
                // Data historis: coba TOFLMBHP JOIN, fallback ke 0 jika gagal
                try {
                    $restruTotal = DB::connection($this->connection)->select("
                        SELECT COUNT(DISTINCT a.nokontrak) as total_kontrak,
                            SUM(CAST(b.osmdlc AS DECIMAL(18,4))) as total_os
                        FROM TOFLMBHP a INNER JOIN {$tableName} b ON a.nokontrak = b.nokontrak
                        WHERE b.stsrec IN ('A', 'N') AND b.stsacc <> 'W' {$mainFilterB}
                    ", $mainBindings);
                    $restruFail = DB::connection($this->connection)->select("
                        SELECT COUNT(DISTINCT b.nokontrak) as gagal_kontrak
                        FROM TOFLMBHP a INNER JOIN {$tableName} b ON a.nokontrak = b.nokontrak
                        WHERE b.stsrec IN ('A', 'N') AND b.stsacc <> 'W'
                          AND b.colbaru IN ('3','4','5') AND CAST(a.colnew AS VARCHAR) IN ('1','2') {$mainFilterB}
                    ", $mainBindings);
                } catch (\Throwable) {
                    $restruTotal = [];
                    $restruFail  = [];
                }
            } else {
                $restruTotal = DB::connection($this->connection)->select("
                    SELECT COUNT(DISTINCT a.nokontrak) as total_kontrak,
                        SUM(CAST(b.osmdlc AS DECIMAL(18,4))) as total_os
                    FROM TOFLMBHP a INNER JOIN {$tableName} b ON a.nokontrak = b.nokontrak
                    WHERE b.stsrec IN ('A', 'N') AND b.stsacc <> 'W' {$mainFilterB}
                ", $mainBindings);
                $restruFail = DB::connection($this->connection)->select("
                    SELECT COUNT(DISTINCT b.nokontrak) as gagal_kontrak
                    FROM TOFLMBHP a INNER JOIN {$tableName} b ON a.nokontrak = b.nokontrak
                    WHERE b.stsrec IN ('A', 'N') AND b.stsacc <> 'W'
                      AND b.colbaru IN ('3','4','5') AND CAST(a.colnew AS VARCHAR) IN ('1','2') {$mainFilterB}
                ", $mainBindings);
            }

            // ── Global Metrics ──────────────────────────────────────────────────
            $restruTotalOS      = isset($restruTotal[0]->total_os)     ? (float)$restruTotal[0]->total_os     : 0;
            $restruTotalKontrak = isset($restruTotal[0]->total_kontrak) ? (int)$restruTotal[0]->total_kontrak  : 0;
            $restruFailKontrak  = isset($restruFail[0]->gagal_kontrak)  ? (int)$restruFail[0]->gagal_kontrak   : 0;
            $vintageFailureRate = $restruTotalKontrak > 0
                ? ($restruFailKontrak / $restruTotalKontrak) * 100 : 0;

            $totalOS   = collect($kolRows)->sum('total_os');
            $totalNPF  = collect($kolRows)->whereIn('kol', ['3','4','5'])->sum('total_os');
            $totalFAR  = collect($kolRows)->whereIn('kol', ['2','3','4','5'])->sum('total_os');

            $queryPpap = DB::connection($this->connection)->table($tableName)
                ->where('stsrec','A')->where('stsacc','<>','W');
            if ($cabang !== '') $queryPpap->where('kdloc', $cabang);
            if ($segmen !== '') $queryPpap->where('segmen', $segmen);
            if ($isHistoris) $queryPpap->where('periode', sprintf('%04d%02d', $reqTahun, $reqBulan));
            $totalPpap = $queryPpap->sum('ppap');

            $npfGross      = $totalOS > 0 ? ($totalNPF / $totalOS) * 100                 : 0;
            $npfNetVal     = max(0, $totalNPF - $totalPpap);
            $npfNet        = $totalOS > 0 ? ($npfNetVal / $totalOS) * 100                : 0;
            $coverageRatio = $totalNPF > 0 ? ($totalPpap / $totalNPF) * 100             : 0;
            $farRatio      = $totalOS > 0 ? ($totalFAR / $totalOS) * 100                 : 0;
            $topAkad       = collect($akadRows)->sortByDesc('npf_os')->first();
            $fdrMetrics    = $this->getTksFdrMetrics($tableName, $isHistoris ? sprintf('%04d%02d', $reqTahun, $reqBulan) : null);
            $ckpnModel     = $this->getCkpnModelAnalytics($tableName, $isHistoris, $mainFilter, $mainBindings, $reqTahun, $reqBulan);
            $kapMetrics    = $this->getKapRiskMetrics($tableName, $isHistoris, $mainFilter, $mainBindings, $reqTahun, $reqBulan);
            $pkrMetrics    = $this->getPkrMetrics($tableName, $isHistoris, $mainFilter, $mainBindings, $reqTahun, $reqBulan);
            $sourceDatabase = DB::connection($this->connection)->selectOne('SELECT DB_NAME() AS database_name')->database_name ?? null;
            $trendFilter = $strCabang . $strSegmen;
            $trendBindings = array_merge($bindCabang, $bindSegmen);
            $kapPrudentialTrend = $this->getKapPrudentialTrend($reqTahun, $reqBulan, $trendFilter, $trendBindings, is_string($sourceDatabase) ? $sourceDatabase : null);
            $pkrTrend = $this->getPkrTrend($reqTahun, $reqBulan, $mainFilter, $mainBindings, is_string($sourceDatabase) ? $sourceDatabase : null);
            $pkrMetrics['trend'] = $pkrTrend['trend'];
            $pkrMetrics['trend_meta'] = $pkrTrend['meta'];
            $kapMetrics['prudential_trend'] = $kapPrudentialTrend['trend'];
            $kapMetrics['anomaly_detector'] = $this->buildKapAnomalyDetector($kapMetrics, $kapPrudentialTrend['trend'], $kapPrudentialTrend['meta']);
            $kapMetrics['prudential_trend_meta'] = $kapPrudentialTrend['meta'];
            $kapMetrics['worksheet_reconciliation'] = $this->buildKapWorksheetReconciliation($kapMetrics);

            $kapSummary = $kapMetrics['summary'] ?? [];
            $fdrComponents = $fdrMetrics['components'] ?? [];
            $modalInti = (float) ($fdrComponents['modal_inti'] ?? 0);
            $aydaAmount = (float) ($fdrComponents['ayda_pengurang'] ?? 0);
            $asetBermasalah = (float) $totalNPF;
            $PpapBermasalah = (float) ($kapSummary['ppap_system_npf'] ?? 0);
            $miapbDenominator = $asetBermasalah - $PpapBermasalah;
            $miapbRatio = $miapbDenominator > 0 ? ($modalInti / $miapbDenominator) * 100 : 0;
            $aydaRatio = $totalOS > 0 ? ($aydaAmount / $totalOS) * 100 : 0;
            $qualityRiskIndicators = [
                'miapb' => [
                    'ratio' => $miapbRatio,
                    'modal_inti' => $modalInti,
                    'aset_bermasalah' => $asetBermasalah,
                    'ppap_bermasalah' => $PpapBermasalah,
                    'denominator' => $miapbDenominator,
                    'formula' => 'Modal Inti / (Aset Bermasalah - PPKA Bermasalah)',
                    'interpretation' => $this->interpretMiapbRatio($miapbRatio, $miapbDenominator),
                ],
                'ayda' => [
                    'amount' => $aydaAmount,
                    'ratio' => $aydaRatio,
                    'denominator' => (float) $totalOS,
                    'formula' => 'AYDA / Total Pembiayaan',
                    'interpretation' => $this->interpretAydaRatio($aydaRatio, $aydaAmount),
                ],
                'pkr' => $pkrMetrics['summary'] ?? [],
            ];

            $bagiHasilOS  = collect($akadRows)->filter(fn ($i) =>
                str_contains(strtolower($i->akad), 'mudharabah') ||
                str_contains(strtolower($i->akad), 'musyarakah')
            )->sum('total_os');
            $porsiBagiHasil = $totalOS > 0 ? ($bagiHasilOS / $totalOS) * 100 : 0;

            // Stress test numerics
            $top5OS  = array_sum(array_map(fn ($r) => (float)$r->os, array_slice($topObligorRows, 0, 5)));
            $top10OS = array_sum(array_map(fn ($r) => (float)$r->os, array_slice($topObligorRows, 0, 10)));
            $npfIfTop5Fail  = $totalOS > 0 ? (($totalNPF + $top5OS)  / $totalOS) * 100 : 0;
            $npfIfTop10Fail = $totalOS > 0 ? (($totalNPF + $top10OS) / $totalOS) * 100 : 0;
            $restruToTotal  = $totalOS > 0 ? ($restruTotalOS / $totalOS) * 100          : 0;

            return [
                'kolektibilitas' => $kolRows,
                'akad_risk'      => $akadRows,
                'aging'          => $agingRows,
                'branch_compare' => $branchCompareRows,
                'alerts'         => $alertRows,
                'trend'          => $trendMapped,
                'trend_meta'     => [
                    'data_count'    => count($trendMapped),
                    'last_bulan'    => count($trendMapped) > 0
                        ? $trendMapped[count($trendMapped) - 1]->bulan : null,
                    'filter_tahun'  => $reqTahun,
                    'filter_bulan'  => $reqBulan,
                ],
                'ecl_staging'    => $eclData,
                'ckpn_model'     => $ckpnModel,
                'kap_metrics'    => $kapMetrics,
                'quality_risk_indicators' => $qualityRiskIndicators,
                'pkr_metrics'    => $pkrMetrics,
                'top_obligor'    => $topObligorRows,
                'ao_matrix'      => $aoMatrixRows,
                'sector_data'    => $sectorRows,
                'product_data'   => $productRows,
                'restru_guard'   => [
                    'total_os_restru'       => $restruTotalOS,
                    'total_kontrak_restru'  => $restruTotalKontrak,
                    'restru_to_total_ratio' => $restruToTotal,
                    'gagal_kontrak'         => $restruFailKontrak,
                    'vintage_failure_rate'  => $vintageFailureRate,
                ],
                'stress_test'    => [
                    'top5_os'           => $top5OS,
                    'top10_os'          => $top10OS,
                    'npf_gross_now'     => $npfGross,
                    'npf_if_top5_fail'  => $npfIfTop5Fail,
                    'npf_if_top10_fail' => $npfIfTop10Fail,
                ],
                'summary' => [
                    'total_os'         => (float)$totalOS,
                    'total_npf'        => (float)$totalNPF,
                    'total_ppap'       => (float)$totalPpap,
                    'npf_gross'        => $npfGross,
                    'npf_net'          => $npfNet,
                    'coverage_ratio'   => $coverageRatio,
                    'far_ratio'        => $farRatio,
                    'pkr_ratio'        => (float) ($pkrMetrics['summary']['pkr_ratio'] ?? 0),
                    'pkr_os'           => (float) ($pkrMetrics['summary']['pkr_os'] ?? 0),
                    'miapb_ratio'      => $miapbRatio,
                    'ayda_ratio'       => $aydaRatio,
                    'ayda_amount'      => $aydaAmount,
                    'top_akad_risk'    => $topAkad ? $topAkad->akad : 'N/A',
                    'porsi_bagi_hasil' => $porsiBagiHasil,
                    'fdr'              => $fdrMetrics['fdr'],
                    'fdr_v2'           => $fdrMetrics['fdr_v2'],
                    'fdr_components'   => $fdrMetrics['components'],
                    'composite_score'  => 2,
                    'risk_profile'     => [
                        'Kredit'=>3,'Likuiditas'=>2,'Operasional'=>2,'Kepatuhan'=>1,'Reputasi'=>2
                    ],
                ],
            ];
        });

        $this->logPerformance(__METHOD__, $start, $memory);
        return $data;
    }

    /**
     * KAP/APYD/PPKA WD berbasis data prudential pembiayaan dan ABA.
     *
     * @param  list<mixed>  $mainBindings
     * @return array<string,mixed>
     */
    private function getKapRiskMetrics(string $tableName, bool $isHistoris, string $mainFilter, array $mainBindings, int $reqTahun, int $reqBulan, bool $includePpapDetails = true): array
    {
        $collateralCte = "
            SELECT
                j.nokontrak,
                SUM(CAST(ISNULL(j.nomtaksasi, 0) AS DECIMAL(38,6))) AS collateral_weighted
            FROM TOFJAMIN j
            WHERE j.stsrec = 'A'
            GROUP BY j.nokontrak
        ";

        $summaryRows = DB::connection($this->connection)->select("
            WITH collateral AS ({$collateralCte}),
            base AS (
                SELECT
                    a.nokontrak,
                    a.colbaru,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok,
                    ISNULL(CAST(a.ppap AS DECIMAL(38,6)), 0) AS ppap_system,
                    ISNULL(c.collateral_weighted, ISNULL(CAST(a.htgagun AS DECIMAL(38,6)), 0)) AS collateral_weighted
                FROM {$tableName} a
                LEFT JOIN collateral c ON a.nokontrak = c.nokontrak
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ),
            calc AS (
                SELECT *,
                    CASE
                        WHEN colbaru = '2' THEN os_pokok * 0.25
                        WHEN colbaru = '3' THEN os_pokok * 0.50
                        WHEN colbaru = '4' THEN os_pokok * 0.75
                        WHEN colbaru = '5' THEN os_pokok
                        ELSE 0
                    END AS apyd_all,
                    CASE
                        WHEN colbaru = '3' THEN os_pokok * 0.50
                        WHEN colbaru = '4' THEN os_pokok * 0.75
                        WHEN colbaru = '5' THEN os_pokok
                        ELSE 0
                    END AS apyd_tks,
                    os_pokok - collateral_weighted AS net_exposure_agunan,
                    CASE
                        WHEN colbaru = '1' THEN (os_pokok - collateral_weighted) * 0
                        WHEN colbaru = '2' THEN (os_pokok - collateral_weighted) * 0.25
                        WHEN colbaru = '3' THEN (os_pokok - collateral_weighted) * 0.50
                        WHEN colbaru = '4' THEN (os_pokok - collateral_weighted) * 0.75
                        WHEN colbaru = '5' THEN os_pokok - collateral_weighted
                        ELSE 0
                    END AS ppap_wajib_dibentuk
                FROM base
            ),
            aba_coll AS (
                SELECT
                    RIGHT(nosbb, 7) AS nosbb_key,
                    MAX(coll) AS coll
                FROM TOFABA
                WHERE stsrec = 'A'
                GROUP BY RIGHT(nosbb, 7)
            ),
            gl AS (
                SELECT
                    SUM(CASE
                        WHEN nobb LIKE '50113%'
                        THEN CAST(ISNULL(sahirrp, 0) AS DECIMAL(38,6)) ELSE 0
                    END) AS antar_bank_aktiva_total,
                    SUM(CASE
                        WHEN nobb LIKE '50113%' AND ISNULL(ac.coll, '1') <> '5'
                        THEN CAST(ISNULL(m.sahirrp, 0) AS DECIMAL(38,6)) ELSE 0
                    END) AS antar_bank_aktiva_non_macet,
                    SUM(CASE
                        WHEN nobb LIKE '50113%' AND ac.coll = '5'
                        THEN CAST(ISNULL(m.sahirrp, 0) AS DECIMAL(38,6)) ELSE 0
                    END) AS antar_bank_aktiva_macet,
                    SUM(CASE
                        WHEN nobb LIKE '50113%' AND ac.coll = '3'
                        THEN CAST(ISNULL(m.sahirrp, 0) AS DECIMAL(38,6)) * 0.50
                        WHEN nobb LIKE '50113%' AND ac.coll = '4'
                        THEN CAST(ISNULL(m.sahirrp, 0) AS DECIMAL(38,6)) * 0.75
                        WHEN nobb LIKE '50113%' AND ac.coll = '5'
                        THEN CAST(ISNULL(m.sahirrp, 0) AS DECIMAL(38,6))
                        ELSE 0
                    END) AS antar_bank_aktiva_apyd,
                    SUM(CASE
                        WHEN nobb LIKE '50113%' AND ac.coll IS NULL
                        THEN CAST(ISNULL(m.sahirrp, 0) AS DECIMAL(38,6)) ELSE 0
                    END) AS antar_bank_aktiva_unmapped
                FROM MGL m
                LEFT JOIN aba_coll ac ON RIGHT(m.nosbb, 7) = ac.nosbb_key
            ),
            summary AS (
                SELECT
                    COUNT(*) AS total_noa,
                    SUM(os_pokok) AS total_pembiayaan,
                    SUM(apyd_all) AS apyd_all,
                    SUM(apyd_tks) AS apyd_financing_tks,
                    SUM(collateral_weighted) AS agunan_berbobot,
                    SUM(net_exposure_agunan) AS net_exposure_agunan,
                    SUM(ppap_wajib_dibentuk) AS ppap_wajib_dibentuk_financing,
                    SUM(ppap_system) AS ppap_system,
                    SUM(CASE WHEN colbaru IN ('3','4','5') THEN os_pokok ELSE 0 END) AS npf_gross,
                    SUM(CASE WHEN colbaru IN ('3','4','5') THEN net_exposure_agunan ELSE 0 END) AS net_exposure_npf,
                    SUM(CASE WHEN colbaru IN ('3','4','5') THEN ppap_wajib_dibentuk ELSE 0 END) AS ppap_wd_npf,
                    SUM(CASE WHEN colbaru IN ('3','4','5') THEN ppap_system ELSE 0 END) AS ppap_system_npf
                FROM calc
            )
            SELECT
                s.*,
                CAST(ISNULL(g.antar_bank_aktiva_total, 0) AS DECIMAL(38,6)) AS antar_bank_aktiva_total,
                CAST(ISNULL(g.antar_bank_aktiva_non_macet, 0) AS DECIMAL(38,6)) AS antar_bank_aktiva_lancar,
                CAST(ISNULL(g.antar_bank_aktiva_macet, 0) AS DECIMAL(38,6)) AS antar_bank_aktiva_macet,
                CAST(ISNULL(g.antar_bank_aktiva_apyd, 0) AS DECIMAL(38,6)) AS antar_bank_aktiva_apyd,
                CAST(ISNULL(g.antar_bank_aktiva_unmapped, 0) AS DECIMAL(38,6)) AS antar_bank_aktiva_unmapped,
                s.total_pembiayaan + CAST(ISNULL(g.antar_bank_aktiva_non_macet, 0) AS DECIMAL(38,6)) AS total_aktiva_produktif,
                s.apyd_financing_tks + CAST(ISNULL(g.antar_bank_aktiva_apyd, 0) AS DECIMAL(38,6)) AS apyd,
                s.ppap_wajib_dibentuk_financing + CAST(ISNULL(g.antar_bank_aktiva_non_macet, 0) AS DECIMAL(38,6)) AS ppap_wajib_dibentuk
            FROM summary s CROSS JOIN gl g
        ", $mainBindings);

        $breakdownRows = DB::connection($this->connection)->select("
            WITH collateral AS ({$collateralCte}),
            base AS (
                SELECT
                    a.colbaru,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok,
                    ISNULL(CAST(a.ppap AS DECIMAL(38,6)), 0) AS ppap_system,
                    ISNULL(c.collateral_weighted, ISNULL(CAST(a.htgagun AS DECIMAL(38,6)), 0)) AS collateral_weighted
                FROM {$tableName} a
                LEFT JOIN collateral c ON a.nokontrak = c.nokontrak
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ),
            calc AS (
                SELECT *,
                    CASE
                        WHEN colbaru = '2' THEN os_pokok * 0.25
                        WHEN colbaru = '3' THEN os_pokok * 0.50
                        WHEN colbaru = '4' THEN os_pokok * 0.75
                        WHEN colbaru = '5' THEN os_pokok
                        ELSE 0
                    END AS apyd,
                    os_pokok - collateral_weighted AS net_exposure_agunan,
                    CASE
                        WHEN colbaru = '1' THEN (os_pokok - collateral_weighted) * 0
                        WHEN colbaru = '2' THEN (os_pokok - collateral_weighted) * 0.25
                        WHEN colbaru = '3' THEN (os_pokok - collateral_weighted) * 0.50
                        WHEN colbaru = '4' THEN (os_pokok - collateral_weighted) * 0.75
                        WHEN colbaru = '5' THEN os_pokok - collateral_weighted
                        ELSE 0
                    END AS ppap_wajib_dibentuk
                FROM base
            )
            SELECT
                colbaru,
                COUNT(*) AS noa,
                SUM(os_pokok) AS os_pokok,
                SUM(apyd) AS apyd,
                SUM(collateral_weighted) AS agunan_berbobot,
                SUM(net_exposure_agunan) AS net_exposure_agunan,
                SUM(ppap_wajib_dibentuk) AS ppap_wajib_dibentuk,
                SUM(ppap_system) AS ppap_system
            FROM calc
            GROUP BY colbaru
            ORDER BY colbaru
        ", $mainBindings);

        $contractRows = DB::connection($this->connection)->select("
            WITH collateral AS ({$collateralCte}),
            base AS (
                SELECT
                    a.nokontrak,
                    a.nocif,
                    LTRIM(RTRIM(a.nama)) AS nama,
                    a.kdprd,
                    ISNULL(p.ket, 'Tanpa Produk') AS produk,
                    a.kdloc,
                    ISNULL(cab.nama, '(Tanpa Cabang)') AS cabang,
                    a.kdaoh,
                    ISNULL(ao.nmao, '(Tanpa AO)') AS nama_ao,
                    a.colbaru,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok,
                    ISNULL(CAST(a.ppap AS DECIMAL(38,6)), 0) AS ppap_system,
                    ISNULL(c.collateral_weighted, ISNULL(CAST(a.htgagun AS DECIMAL(38,6)), 0)) AS collateral_weighted
                FROM {$tableName} a
                LEFT JOIN collateral c ON a.nokontrak = c.nokontrak
                LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                LEFT JOIN CABANG cab ON a.kdloc = cab.kdloc
                LEFT JOIN AO ao ON a.kdaoh = ao.kdao
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ),
            calc AS (
                SELECT *,
                    CASE
                        WHEN colbaru = '2' THEN os_pokok * 0.25
                        WHEN colbaru = '3' THEN os_pokok * 0.50
                        WHEN colbaru = '4' THEN os_pokok * 0.75
                        WHEN colbaru = '5' THEN os_pokok
                        ELSE 0
                    END AS apyd_all,
                    CASE
                        WHEN colbaru = '3' THEN os_pokok * 0.50
                        WHEN colbaru = '4' THEN os_pokok * 0.75
                        WHEN colbaru = '5' THEN os_pokok
                        ELSE 0
                    END AS apyd_tks,
                    os_pokok - collateral_weighted AS net_exposure_agunan,
                    CASE
                        WHEN colbaru = '1' THEN (os_pokok - collateral_weighted) * 0
                        WHEN colbaru = '2' THEN (os_pokok - collateral_weighted) * 0.25
                        WHEN colbaru = '3' THEN (os_pokok - collateral_weighted) * 0.50
                        WHEN colbaru = '4' THEN (os_pokok - collateral_weighted) * 0.75
                        WHEN colbaru = '5' THEN os_pokok - collateral_weighted
                        ELSE 0
                    END AS ppap_wajib_dibentuk
                FROM base
            )
            SELECT TOP 25 *,
                ppap_system - ppap_wajib_dibentuk AS ppap_gap,
                CASE
                    WHEN ppap_wajib_dibentuk > 0 THEN ppap_system / NULLIF(ppap_wajib_dibentuk, 0) * 100
                    ELSE 0
                END AS ppap_coverage_to_wd
            FROM calc
            WHERE ppap_system - ppap_wajib_dibentuk < 0
            ORDER BY ABS(ppap_system - ppap_wajib_dibentuk) DESC
        ", $mainBindings);

        $overReservedRows = DB::connection($this->connection)->select("
            WITH collateral AS ({$collateralCte}),
            base AS (
                SELECT
                    a.nokontrak,
                    a.nocif,
                    LTRIM(RTRIM(a.nama)) AS nama,
                    a.kdprd,
                    ISNULL(p.ket, 'Tanpa Produk') AS produk,
                    a.kdloc,
                    ISNULL(cab.nama, '(Tanpa Cabang)') AS cabang,
                    a.kdaoh,
                    ISNULL(ao.nmao, '(Tanpa AO)') AS nama_ao,
                    a.colbaru,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok,
                    ISNULL(CAST(a.ppap AS DECIMAL(38,6)), 0) AS ppap_system,
                    ISNULL(c.collateral_weighted, ISNULL(CAST(a.htgagun AS DECIMAL(38,6)), 0)) AS collateral_weighted
                FROM {$tableName} a
                LEFT JOIN collateral c ON a.nokontrak = c.nokontrak
                LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                LEFT JOIN CABANG cab ON a.kdloc = cab.kdloc
                LEFT JOIN AO ao ON a.kdaoh = ao.kdao
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ),
            calc AS (
                SELECT *,
                    os_pokok - collateral_weighted AS net_exposure_agunan,
                    CASE
                        WHEN colbaru = '1' THEN (os_pokok - collateral_weighted) * 0
                        WHEN colbaru = '2' THEN (os_pokok - collateral_weighted) * 0.25
                        WHEN colbaru = '3' THEN (os_pokok - collateral_weighted) * 0.50
                        WHEN colbaru = '4' THEN (os_pokok - collateral_weighted) * 0.75
                        WHEN colbaru = '5' THEN os_pokok - collateral_weighted
                        ELSE 0
                    END AS ppap_wajib_dibentuk
                FROM base
            )
            SELECT TOP 15 *,
                ppap_system - ppap_wajib_dibentuk AS ppap_gap,
                CASE
                    WHEN ppap_wajib_dibentuk > 0 THEN ppap_system / NULLIF(ppap_wajib_dibentuk, 0) * 100
                    ELSE 0
                END AS ppap_coverage_to_wd
            FROM calc
            WHERE ppap_system - ppap_wajib_dibentuk > 0
            ORDER BY ppap_system - ppap_wajib_dibentuk DESC
        ", $mainBindings);

        $apydContributorRows = DB::connection($this->connection)->select("
            WITH base AS (
                SELECT
                    a.nokontrak,
                    a.nocif,
                    LTRIM(RTRIM(a.nama)) AS nama,
                    a.kdprd,
                    ISNULL(p.ket, 'Tanpa Produk') AS produk,
                    a.kdloc,
                    ISNULL(cab.nama, '(Tanpa Cabang)') AS cabang,
                    a.kdaoh,
                    ISNULL(ao.nmao, '(Tanpa AO)') AS nama_ao,
                    a.colbaru,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok
                FROM {$tableName} a
                LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                LEFT JOIN CABANG cab ON a.kdloc = cab.kdloc
                LEFT JOIN AO ao ON a.kdaoh = ao.kdao
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ),
            calc AS (
                SELECT *,
                    CASE
                        WHEN colbaru = '3' THEN os_pokok * 0.50
                        WHEN colbaru = '4' THEN os_pokok * 0.75
                        WHEN colbaru = '5' THEN os_pokok
                        ELSE 0
                    END AS apyd_tks
                FROM base
            )
            SELECT TOP 25 *
            FROM calc
            WHERE apyd_tks > 0
            ORDER BY apyd_tks DESC
        ", $mainBindings);

        $abaRows = DB::connection($this->connection)->select("
            WITH aba_coll AS (
                SELECT
                    RIGHT(nosbb, 7) AS nosbb_key,
                    MAX(coll) AS coll,
                    MAX(norek) AS norek,
                    MAX(nmbank) AS nmbank,
                    SUM(CAST(ISNULL(ppap, 0) AS DECIMAL(38,6))) AS ppap_aba
                FROM TOFABA
                WHERE stsrec = 'A'
                GROUP BY RIGHT(nosbb, 7)
            )
            SELECT
                m.nobb,
                m.nosbb,
                m.nmsbb,
                m.sandibi,
                ac.norek,
                ac.nmbank,
                ISNULL(ac.coll, 'UNMAPPED') AS coll,
                CAST(ISNULL(m.sahirrp, 0) AS DECIMAL(38,6)) AS sahirrp,
                CAST(ISNULL(ac.ppap_aba, 0) AS DECIMAL(38,6)) AS ppap_aba,
                CASE
                    WHEN ac.coll = '5' THEN 'Macet / dikecualikan dari Aktiva Produktif'
                    WHEN ac.coll IN ('1','2','3','4') THEN 'Kolektibilitas ' + ac.coll
                    ELSE 'Belum terpetakan TOFABA'
                END AS prudential_status
            FROM MGL m
            LEFT JOIN aba_coll ac ON RIGHT(m.nosbb, 7) = ac.nosbb_key
            WHERE m.nobb LIKE '50113%'
              AND CAST(ISNULL(m.sahirrp, 0) AS DECIMAL(38,6)) <> 0
            ORDER BY CAST(ISNULL(m.sahirrp, 0) AS DECIMAL(38,6)) DESC
        ");

        $dataQualityRows = DB::connection($this->connection)->select("
            WITH collateral AS ({$collateralCte}),
            base AS (
                SELECT
                    a.nokontrak,
                    a.nocif,
                    LTRIM(RTRIM(a.nama)) AS nama,
                    ISNULL(p.ket, 'Tanpa Produk') AS produk,
                    ISNULL(cab.nama, '(Tanpa Cabang)') AS cabang,
                    ISNULL(ao.nmao, '(Tanpa AO)') AS nama_ao,
                    a.colbaru,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok,
                    ISNULL(CAST(a.ppap AS DECIMAL(38,6)), 0) AS ppap_system,
                    ISNULL(c.collateral_weighted, 0) AS collateral_from_tofjamin,
                    ISNULL(CAST(a.htgagun AS DECIMAL(38,6)), 0) AS collateral_from_toflmb,
                    CASE
                        WHEN c.nokontrak IS NULL THEN 0 ELSE 1
                    END AS has_tofjamin
                FROM {$tableName} a
                LEFT JOIN collateral c ON a.nokontrak = c.nokontrak
                LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                LEFT JOIN CABANG cab ON a.kdloc = cab.kdloc
                LEFT JOIN AO ao ON a.kdaoh = ao.kdao
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ),
            flagged AS (
                SELECT *,
                    CASE
                        WHEN colbaru NOT IN ('1','2','3','4','5') OR colbaru IS NULL THEN 'Kolektibilitas Tidak Valid'
                        WHEN os_pokok < 0 THEN 'Outstanding Negatif'
                        WHEN ppap_system < 0 THEN 'PPKA Sistem Negatif'
                        WHEN os_pokok > 0 AND ppap_system = 0 AND colbaru IN ('3','4','5') THEN 'PPKA Kosong untuk Kol 3-5'
                        WHEN os_pokok > 0 AND has_tofjamin = 0 AND collateral_from_toflmb = 0 THEN 'Tidak Ada Agunan Terbaca'
                        WHEN os_pokok > 0 AND (ISNULL(collateral_from_tofjamin, 0) > os_pokok * 5 OR ISNULL(collateral_from_toflmb, 0) > os_pokok * 5) THEN 'Agunan Sangat Tinggi terhadap OS'
                        ELSE NULL
                    END AS issue,
                    CASE
                        WHEN colbaru NOT IN ('1','2','3','4','5') OR colbaru IS NULL THEN 'danger'
                        WHEN os_pokok < 0 OR ppap_system < 0 THEN 'danger'
                        WHEN os_pokok > 0 AND ppap_system = 0 AND colbaru IN ('3','4','5') THEN 'danger'
                        WHEN os_pokok > 0 AND has_tofjamin = 0 AND collateral_from_toflmb = 0 THEN 'warning'
                        WHEN os_pokok > 0 AND (ISNULL(collateral_from_tofjamin, 0) > os_pokok * 5 OR ISNULL(collateral_from_toflmb, 0) > os_pokok * 5) THEN 'warning'
                        ELSE 'safe'
                    END AS severity
                FROM base
            )
            SELECT TOP 50 *
            FROM flagged
            WHERE issue IS NOT NULL
            ORDER BY
                CASE severity WHEN 'danger' THEN 1 WHEN 'warning' THEN 2 ELSE 3 END,
                ABS(os_pokok) DESC
        ", $mainBindings);

        $dataQualitySummaryRows = DB::connection($this->connection)->select("
            WITH collateral AS ({$collateralCte}),
            base AS (
                SELECT
                    a.nokontrak,
                    a.colbaru,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok,
                    ISNULL(CAST(a.ppap AS DECIMAL(38,6)), 0) AS ppap_system,
                    ISNULL(c.collateral_weighted, 0) AS collateral_from_tofjamin,
                    ISNULL(CAST(a.htgagun AS DECIMAL(38,6)), 0) AS collateral_from_toflmb,
                    CASE
                        WHEN c.nokontrak IS NULL THEN 0 ELSE 1
                    END AS has_tofjamin
                FROM {$tableName} a
                LEFT JOIN collateral c ON a.nokontrak = c.nokontrak
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ),
            flagged AS (
                SELECT
                    CASE
                        WHEN colbaru NOT IN ('1','2','3','4','5') OR colbaru IS NULL THEN 'danger'
                        WHEN os_pokok < 0 OR ppap_system < 0 THEN 'danger'
                        WHEN os_pokok > 0 AND ppap_system = 0 AND colbaru IN ('3','4','5') THEN 'danger'
                        WHEN os_pokok > 0 AND has_tofjamin = 0 AND collateral_from_toflmb = 0 THEN 'warning'
                        WHEN os_pokok > 0 AND (ISNULL(collateral_from_tofjamin, 0) > os_pokok * 5 OR ISNULL(collateral_from_toflmb, 0) > os_pokok * 5) THEN 'warning'
                        ELSE NULL
                    END AS severity
                FROM base
            )
            SELECT
                COUNT(*) AS issue_count,
                SUM(CASE WHEN severity = 'danger' THEN 1 ELSE 0 END) AS danger_count,
                SUM(CASE WHEN severity = 'warning' THEN 1 ELSE 0 END) AS warning_count
            FROM flagged
            WHERE severity IS NOT NULL
        ", $mainBindings);

        $PpapRekapMetrics = $this->getPpapRekapGapMetrics($tableName, $isHistoris, $mainFilter, $mainBindings, $reqTahun, $reqBulan, $includePpapDetails);

        $row = $summaryRows[0] ?? (object) [];
        $totalAktivaProduktif = (float) ($row->total_aktiva_produktif ?? 0);
        $apyd = (float) ($row->apyd ?? 0);
        $PpapWajibDibentuk = (float) ($row->ppap_wajib_dibentuk ?? 0);
        $PpapSystem = (float) ($row->ppap_system ?? 0);
        $PpapRekapCurrent = (float) ($PpapRekapMetrics['current_ppap'] ?? 0);
        $PpapRekapPrevious = (float) ($PpapRekapMetrics['previous_ppap'] ?? 0);
        $PpapRekapGap = (float) ($PpapRekapMetrics['gap'] ?? 0);
        $netExposureAgunan = (float) ($row->net_exposure_agunan ?? 0);

        $kapRatio = $totalAktivaProduktif > 0 ? (1 - ($apyd / $totalAktivaProduktif)) * 100 : 0;
        $apydRatio = $totalAktivaProduktif > 0 ? ($apyd / $totalAktivaProduktif) * 100 : 0;
        $npfGrossRatio = (float) ($row->total_pembiayaan ?? 0) > 0 ? ((float) ($row->npf_gross ?? 0) / (float) ($row->total_pembiayaan ?? 0)) * 100 : 0;
        $npfNettRatio = (float) ($row->total_pembiayaan ?? 0) > 0 ? (((float) ($row->npf_gross ?? 0) - (float) ($row->ppap_system_npf ?? 0)) / (float) ($row->total_pembiayaan ?? 0)) * 100 : 0;
        $coveragePpapWd = $PpapWajibDibentuk > 0 ? ($PpapSystem / $PpapWajibDibentuk) * 100 : 0;
        $collateralCoverage = $totalAktivaProduktif > 0 ? ((float) ($row->agunan_berbobot ?? 0) / $totalAktivaProduktif) * 100 : 0;
        $netExposureRatio = $totalAktivaProduktif > 0 ? ($netExposureAgunan / $totalAktivaProduktif) * 100 : 0;
        $systemVsWdGap = $PpapSystem - $PpapWajibDibentuk;
        $shortfallCount = count($contractRows);
        $apydContributorCount = count($apydContributorRows);
        $abaMacet = (float) ($row->antar_bank_aktiva_macet ?? 0);
        $abaUnmapped = (float) ($row->antar_bank_aktiva_unmapped ?? 0);
        $dataQualitySummary = $dataQualitySummaryRows[0] ?? (object) [];
        $dataQualityIssueCount = (int) ($dataQualitySummary->issue_count ?? 0);
        $dataQualityDangerCount = (int) ($dataQualitySummary->danger_count ?? 0);
        $dataQualityWarningCount = (int) ($dataQualitySummary->warning_count ?? 0);
        $sourceReconciliation = [
            [
                'component' => 'Pembiayaan / Baki Debet',
                'source_table' => $tableName,
                'source_field' => 'osmdlc',
                'basis' => "stsrec IN ('A','N'), stsacc <> 'W', filter cabang/segmen/periode aktif",
                'amount' => (float) ($row->total_pembiayaan ?? 0),
                'status' => (float) ($row->total_pembiayaan ?? 0) > 0 ? 'matched' : 'warning',
                'note' => 'Menjadi basis total pembiayaan, denominator NPF, dan komponen Aktiva Produktif.',
            ],
            [
                'component' => 'Agunan Dikuasai',
                'source_table' => 'TOFJAMIN fallback '.$tableName,
                'source_field' => 'TOFJAMIN.nomtaksasi / '.$tableName.'.htgagun',
                'basis' => 'TOFJAMIN aktif dijumlahkan per nomor kontrak, fallback ke htgagun jika kontrak tidak punya baris TOFJAMIN',
                'amount' => (float) ($row->agunan_berbobot ?? 0),
                'status' => (float) ($row->agunan_berbobot ?? 0) > 0 ? 'matched' : 'warning',
                'note' => 'Dipakai untuk menghitung jumlah setelah agunan dan PPKA WD.',
            ],
            [
                'component' => 'PPKA Sistem',
                'source_table' => $tableName,
                'source_field' => 'PPKA',
                'basis' => "SUM(ppap) pada kontrak aktif sesuai filter",
                'amount' => $PpapSystem,
                'status' => $PpapSystem >= 0 ? 'matched' : 'danger',
                'note' => 'Menjadi saldo cadangan pembanding untuk coverage terhadap PPKA WD dan rekonsiliasi pencadangan.',
            ],
            [
                'component' => 'PPKA Template Bulan Berjalan',
                'source_table' => $tableName.' + TOFJAMIN',
                'source_field' => 'osmdlc, htgagun, colbaru, jnsjamin, jnsikat, goljamin',
                'basis' => 'Kol1 0,5% OS; Kol2 3%, Kol3 10%, Kol4 50%, Kol5 100% atas shortfall OS terhadap nilai likuidasi agunan',
                'amount' => $PpapRekapCurrent,
                'status' => $PpapRekapCurrent >= 0 ? 'matched' : 'danger',
                'note' => 'Menjadi PPKA bulan berjalan untuk membaca GAP PPKA bulanan.',
            ],
            [
                'component' => 'PPKA Pembanding Bulan Sebelumnya',
                'source_table' => (string) ($PpapRekapMetrics['previous_database'] ?? '-'),
                'source_field' => 'TOFLMB.ppap',
                'basis' => 'SUM(ppap) dari snapshot bulan sebelumnya dengan filter cabang/segmen yang sepadan',
                'amount' => $PpapRekapPrevious,
                'status' => ($PpapRekapMetrics['previous_available'] ?? false) ? 'matched' : 'warning',
                'note' => 'Dipakai sebagai baseline GAP PPKA bulanan; kontrak yang sudah lunas tetap mempengaruhi penurunan cadangan.',
            ],
            [
                'component' => 'Antar Bank Aktiva',
                'source_table' => 'MGL + TOFABA',
                'source_field' => 'MGL.sahirrp akun 50113%, TOFABA.coll',
                'basis' => 'Saldo memakai MGL, klasifikasi kolektibilitas memakai TOFABA melalui RIGHT(nosbb,7)',
                'amount' => (float) ($row->antar_bank_aktiva_total ?? 0),
                'status' => ($abaMacet > 0 || $abaUnmapped > 0) ? 'warning' : 'matched',
                'note' => 'ABA non-macet masuk Aktiva Produktif, ABA macet dikecualikan, ABA unmapped ditandai sebagai issue.',
            ],
            [
                'component' => 'Aktiva Produktif',
                'source_table' => $tableName.' + MGL/TOFABA',
                'source_field' => 'total_pembiayaan + ABA non-macet',
                'basis' => 'Total pembiayaan ditambah ABA non-macet berdasarkan klasifikasi subledger',
                'amount' => $totalAktivaProduktif,
                'status' => $totalAktivaProduktif > 0 ? 'matched' : 'danger',
                'note' => 'Menjadi denominator KAP dan APYD ratio.',
            ],
            [
                'component' => 'APYD',
                'source_table' => $tableName.' + TOFABA',
                'source_field' => 'colbaru / TOFABA.coll',
                'basis' => 'Kol3 50%, Kol4 75%, Kol5 100%, ditambah APYD ABA sesuai kolektibilitas',
                'amount' => $apyd,
                'status' => $apyd >= 0 ? 'matched' : 'danger',
                'note' => 'Menjadi pembilang utama rasio KAP.',
            ],
        ];

        $recommendations = [];
        if ($kapRatio < 90) {
            $recommendations[] = 'Prioritas Manajemen Kualitas Aset: rasio KAP berada pada zona tekanan. Fokuskan agenda komite pembiayaan pada debitur Kol 3-5 penyumbang APYD terbesar, pembatasan ekspansi pada sektor/akad berisiko, dan target penyelesaian yang terukur per Account Officer.';
        } elseif ($kapRatio < 95) {
            $recommendations[] = 'Penguatan Early Warning: rasio KAP masih memerlukan pengawasan ketat. Perkuat monitoring Kol 2, lakukan review mingguan akun dengan tunggakan awal, dan pastikan rencana penagihan terdokumentasi sebelum terjadi migrasi ke NPF.';
        } else {
            $recommendations[] = 'Kualitas Aset Relatif Kuat: pertahankan disiplin early warning, pantau perubahan APYD bulanan, dan gunakan daftar contributor APYD sebagai watchlist agar tren kualitas tidak bergerak memburuk.';
        }

        if ($systemVsWdGap < 0) {
            $recommendations[] = "Tindak Lanjut Pencadangan: terdapat {$shortfallCount} akun prioritas dengan PPKA sistem di bawah PPKA wajib dibentuk. Lakukan rekonsiliasi per kontrak, validasi agunan, dan siapkan memo penyesuaian cadangan untuk akun dengan gap terbesar.";
        } else {
            $recommendations[] = "Kecukupan Cadangan Agregat: PPKA sistem secara total masih menutup PPKA wajib dibentuk. Tetap review {$shortfallCount} akun shortfall terbesar karena surplus agregat dapat menutupi kekurangan pada kontrak individual.";
        }

        if ($PpapRekapGap > 0) {
            $recommendations[] = 'Pergerakan PPKA Bulanan: kebutuhan PPKA bulan berjalan meningkat dibanding baseline bulan sebelumnya. Prioritaskan review kontrak yang naik kolektibilitasnya, validasi nilai likuidasi agunan, dan pastikan pembentukan cadangan sudah masuk rencana pencadangan periode berjalan.';
        } elseif ($PpapRekapGap < 0) {
            $recommendations[] = 'Pergerakan PPKA Bulanan: kebutuhan PPKA bulan berjalan menurun dibanding baseline bulan sebelumnya. Pastikan penurunan berasal dari pelunasan, perbaikan kolektibilitas, atau update agunan yang sah; hindari pelepasan cadangan tanpa bukti pendukung.';
        } else {
            $recommendations[] = 'Pergerakan PPKA Bulanan Stabil: tidak ada perubahan material kebutuhan PPKA terhadap baseline bulan sebelumnya. Tetap lakukan sampling kontrak Kol 2-5 untuk memastikan kualitas data agunan dan kolektibilitas tetap konsisten.';
        }

        if ($npfNettRatio > 5) {
            $recommendations[] = 'Pengendalian NPF Net: rasio NPF Net berada di atas ambang internal yang perlu perhatian. Prioritaskan cure strategy untuk Kol 3, percepat penyelesaian Kol 4-5, dan tetapkan owner remedial per debitur utama.';
        } else {
            $recommendations[] = 'NPF Net Terkendali: pertahankan cadence monitoring dan gunakan tren migrasi kolektibilitas sebagai indikator dini sebelum rasio melewati batas toleransi.';
        }

        if ($netExposureRatio >= 50 || $netExposureAgunan > 0) {
            $recommendations[] = 'Validasi Agunan dan Legalitas: jumlah setelah agunan masih perlu dikendalikan. Prioritaskan update taksasi, kelengkapan pengikatan, dan status eligible agunan pada akun dengan exposure terbesar.';
        } else {
            $recommendations[] = 'Coverage Agunan Mendukung: secara agregat agunan menutup baki debet pembiayaan. Tetap lakukan validasi kualitas hukum, umur taksasi, dan pengikatan agar nilai cover tetap dapat dipakai dalam penilaian prudential.';
        }

        if ($abaMacet > 0 || $abaUnmapped > 0) {
            $recommendations[] = 'Kontrol Antar Bank Aktiva: terdapat ABA macet atau belum terpetakan kolektibilitasnya. Rekonsiliasi saldo MGL dengan subledger TOFABA dan pastikan klasifikasi kolektibilitas diperbarui sebelum pelaporan manajemen.';
        } else {
            $recommendations[] = 'Kontrol Antar Bank Aktiva Aman: seluruh saldo ABA yang masuk perhitungan telah terpetakan ke kolektibilitas subledger dan tidak terdapat ABA macet pada periode/filter ini.';
        }

        if ($apydContributorCount > 0) {
            $recommendations[] = "Action List APYD: gunakan {$apydContributorCount} contributor APYD terbesar sebagai daftar kerja prioritas untuk remedial, penagihan intensif, validasi restrukturisasi, dan eskalasi ke komite risiko.";
        }

        return [
            'methodology' => [
                'kap_formula' => 'Rasio KAP = 1 - (APYD / Aktiva Produktif)',
                'apyd_formula' => 'APYD = Kol3 50% + Kol4 75% + Kol5 100% + APYD ABA berdasarkan kolektibilitas subledger',
                'ppap_wd_formula' => 'PPKA WD = (Baki Debet - Agunan Dikuasai) x tarif prudential: Kol1 0%, Kol2 25%, Kol3 50%, Kol4 75%, Kol5 100%',
                'ppap_gap_formula' => 'GAP PPKA Bulanan = PPKA template bulan berjalan - PPKA snapshot bulan sebelumnya',
                'net_exposure_formula' => 'Jumlah/Net Exposure = Baki Debet - Agunan Dikuasai, tanpa floor dan tanpa pembulatan',
                'collateral_source' => 'Agunan dikuasai dihitung dari TOFJAMIN.nomtaksasi yang dijumlahkan per nomor kontrak',
                'ppap_template_source' => 'PPKA template memakai TOFLMB.osmdlc, TOFLMB.htgagun, TOFLMB.colbaru, TOFLMB.goljamin, dan TOFJAMIN.jnsjamin/jnsikat untuk aturan agunan khusus',
                'aba_source' => 'Antar Bank Aktiva = MGL.sahirrp akun 50113%; kolektibilitas ABA diambil dari TOFABA.coll melalui RIGHT(nosbb,7)',
            ],
            'summary' => [
                'total_noa' => (int) ($row->total_noa ?? 0),
                'total_aktiva_produktif' => $totalAktivaProduktif,
                'total_pembiayaan' => (float) ($row->total_pembiayaan ?? 0),
                'antar_bank_aktiva_total' => (float) ($row->antar_bank_aktiva_total ?? 0),
                'antar_bank_aktiva_lancar' => (float) ($row->antar_bank_aktiva_lancar ?? 0),
                'antar_bank_aktiva_macet' => (float) ($row->antar_bank_aktiva_macet ?? 0),
                'antar_bank_aktiva_apyd' => (float) ($row->antar_bank_aktiva_apyd ?? 0),
                'antar_bank_aktiva_unmapped' => (float) ($row->antar_bank_aktiva_unmapped ?? 0),
                'apyd' => $apyd,
                'apyd_all' => (float) ($row->apyd_all ?? 0),
                'apyd_financing_tks' => (float) ($row->apyd_financing_tks ?? 0),
                'apyd_ratio' => $apydRatio,
                'kap_ratio' => $kapRatio,
                'agunan_berbobot' => (float) ($row->agunan_berbobot ?? 0),
                'net_exposure_agunan' => $netExposureAgunan,
                'net_exposure_npf' => (float) ($row->net_exposure_npf ?? 0),
                'net_exposure_ratio' => $netExposureRatio,
                'collateral_coverage_ratio' => $collateralCoverage,
                'npf_gross' => (float) ($row->npf_gross ?? 0),
                'npf_gross_ratio' => $npfGrossRatio,
                'npf_nett_ratio' => $npfNettRatio,
                'ppap_wajib_dibentuk' => $PpapWajibDibentuk,
                'ppap_wajib_dibentuk_financing' => (float) ($row->ppap_wajib_dibentuk_financing ?? 0),
                'ppap_system' => $PpapSystem,
                'ppap_rekap_current' => $PpapRekapCurrent,
                'ppap_rekap_previous' => $PpapRekapPrevious,
                'ppap_rekap_gap' => $PpapRekapGap,
                'ppap_rekap_previous_available' => (bool) ($PpapRekapMetrics['previous_available'] ?? false),
                'ppap_rekap_previous_database' => $PpapRekapMetrics['previous_database'] ?? null,
                'ppap_gap' => $PpapRekapGap,
                'ppap_system_vs_wd_gap' => $systemVsWdGap,
                'ppap_coverage_to_wd' => $coveragePpapWd,
                'ppap_wd_npf' => (float) ($row->ppap_wd_npf ?? 0),
                'ppap_system_npf' => (float) ($row->ppap_system_npf ?? 0),
            ],
            'breakdown' => $breakdownRows,
            'ppap_shortfall_accounts' => $contractRows,
            'ppap_over_reserved_accounts' => $overReservedRows,
            'ppap_gap_detail' => $PpapRekapMetrics['detail'] ?? ['rows' => [], 'summary' => []],
            'apyd_contributors' => $apydContributorRows,
            'aba_detail' => [
                'rows' => $abaRows,
                'reconciliation' => [
                    'mgl_total' => (float) ($row->antar_bank_aktiva_total ?? 0),
                    'classified_total' => (float) ($row->antar_bank_aktiva_lancar ?? 0) + (float) ($row->antar_bank_aktiva_macet ?? 0),
                    'non_macet_total' => (float) ($row->antar_bank_aktiva_lancar ?? 0),
                    'macet_total' => (float) ($row->antar_bank_aktiva_macet ?? 0),
                    'unmapped_total' => (float) ($row->antar_bank_aktiva_unmapped ?? 0),
                    'source_policy' => 'Saldo ABA memakai MGL akun 50113%; klasifikasi kolektibilitas memakai TOFABA.coll. Jika coll=5, saldo dikecualikan dari Aktiva Produktif.',
                ],
            ],
            'source_reconciliation' => [
                'rows' => $sourceReconciliation,
                'summary' => [
                    'total_components' => count($sourceReconciliation),
                    'matched_count' => count(array_filter($sourceReconciliation, fn ($item) => ($item['status'] ?? '') === 'matched')),
                    'warning_count' => count(array_filter($sourceReconciliation, fn ($item) => ($item['status'] ?? '') === 'warning')),
                    'danger_count' => count(array_filter($sourceReconciliation, fn ($item) => ($item['status'] ?? '') === 'danger')),
                    'policy' => 'Rekonsiliasi ini menunjukkan sumber tabel, field, basis filter, dan nominal final pembentuk rasio prudential.',
                ],
            ],
            'data_quality' => [
                'rows' => $dataQualityRows,
                'summary' => [
                    'issue_count' => $dataQualityIssueCount,
                    'shown_count' => count($dataQualityRows),
                    'danger_count' => $dataQualityDangerCount,
                    'warning_count' => $dataQualityWarningCount,
                    'safe_count' => 0,
                    'policy' => 'Issue diperiksa dari kontrak aktif pada filter berjalan: kolektibilitas invalid, OS/PPKA negatif, PPKA kosong untuk Kol 3-5, agunan tidak terbaca, dan agunan ekstrem.',
                ],
            ],
            'recommendations' => $recommendations,
        ];
    }

    /**
     * GAP PPKA bulanan mengikuti pola REKAP template:
     * PPKA berjalan dihitung dari formula template, baseline memakai PPKA snapshot bulan sebelumnya.
     *
     * @param  list<mixed>  $mainBindings
     * @return array<string,mixed>
     */
    private function getPpapRekapGapMetrics(string $tableName, bool $isHistoris, string $mainFilter, array $mainBindings, int $reqTahun, int $reqBulan, bool $includeDetails = true): array
    {
        $golJaminExpression = $this->tableHasColumn($tableName, 'goljamin')
            ? "ISNULL(NULLIF(LTRIM(RTRIM(a.goljamin)), ''), '0')"
            : "'0'";

        $previousYear = $reqBulan === 1 ? $reqTahun - 1 : $reqTahun;
        $previousMonth = $reqBulan === 1 ? 12 : $reqBulan - 1;
        $previousDatabase = $this->resolveMonthlySnapshotDatabase($previousYear, $previousMonth);
        $previousAvailable = $previousDatabase !== null && ! $isHistoris;
        $previousTable = $previousAvailable
            ? $this->quoteSqlServerIdentifier($previousDatabase).'.dbo.TOFLMB'
            : null;

        $currentRows = DB::connection($this->connection)->select("
            WITH base AS (
                SELECT
                    a.nokontrak,
                    a.colbaru,
                    CAST(ISNULL(a.osmdlc, 0) AS DECIMAL(38,6)) AS os_pokok,
                    CAST(ISNULL(a.htgagun, 0) AS DECIMAL(38,6)) AS htgagun,
                    ISNULL(NULLIF(LTRIM(RTRIM(j.jnsjamin)), ''), ISNULL(NULLIF(LTRIM(RTRIM(a.jnsagun)), ''), '0')) AS jns_agunan,
                    ISNULL(NULLIF(LTRIM(RTRIM(j.jnsikat)), ''), '0') AS jns_ikatan,
                    {$golJaminExpression} AS gol_jamin
                FROM {$tableName} a
                OUTER APPLY (
                    SELECT TOP 1 j.jnsjamin, j.jnsikat
                    FROM TOFJAMIN j
                    WHERE j.nokontrak = a.nokontrak AND j.stsrec = 'A'
                    ORDER BY j.urut
                ) j
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ),
            liquidation AS (
                SELECT *,
                    CASE
                        WHEN jns_agunan = '72' AND jns_ikatan <> '99' AND gol_jamin <> '874' THEN os_pokok * 0.75 * 0.50
                        WHEN jns_agunan = '72' AND jns_ikatan <> '99' AND gol_jamin = '874' THEN os_pokok * 0.50
                        WHEN jns_agunan = '72' AND jns_ikatan = '99' THEN 0
                        ELSE htgagun
                    END AS nilai_likuidasi
                FROM base
            ),
            calc AS (
                SELECT *,
                    CASE
                        WHEN colbaru = '1' THEN ROUND(os_pokok * 0.005, 0)
                        WHEN nilai_likuidasi - os_pokok < 0 AND colbaru = '2' THEN ROUND((os_pokok - nilai_likuidasi) * 0.03, 0)
                        WHEN nilai_likuidasi - os_pokok < 0 AND colbaru = '3' THEN ROUND((os_pokok - nilai_likuidasi) * 0.10, 0)
                        WHEN nilai_likuidasi - os_pokok < 0 AND colbaru = '4' THEN ROUND((os_pokok - nilai_likuidasi) * 0.50, 0)
                        WHEN nilai_likuidasi - os_pokok < 0 AND colbaru = '5' THEN ROUND((os_pokok - nilai_likuidasi) * 1.00, 0)
                        ELSE 0
                    END AS ppap_template
                FROM liquidation
            )
            SELECT
                COUNT(*) AS current_noa,
                SUM(os_pokok) AS current_os,
                SUM(ppap_template) AS current_ppap
            FROM calc
        ", $mainBindings);

        $current = $currentRows[0] ?? (object) [];
        $currentPPKA = (float) ($current->current_ppap ?? 0);
        $previousPPKA = 0.0;
        $previousNoa = 0;

        if ($previousTable !== null) {
            $previousRows = DB::connection($this->connection)->select("
                SELECT
                    COUNT(*) AS previous_noa,
                    SUM(CAST(ISNULL(a.ppap, 0) AS DECIMAL(38,6))) AS previous_ppap
                FROM {$previousTable} a
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ", $mainBindings);

            $previous = $previousRows[0] ?? (object) [];
            $previousPPKA = (float) ($previous->previous_ppap ?? 0);
            $previousNoa = (int) ($previous->previous_noa ?? 0);
        }

        $detail = ['rows' => [], 'summary' => []];

        if ($includeDetails && $previousTable !== null) {
            $detailRows = DB::connection($this->connection)->select("
                WITH current_base AS (
                    SELECT
                        a.nokontrak,
                        a.nocif,
                        LTRIM(RTRIM(a.nama)) AS nama,
                        a.kdprd,
                        ISNULL(p.ket, 'Tanpa Produk') AS produk,
                        a.kdloc,
                        ISNULL(cab.nama, '(Tanpa Cabang)') AS cabang,
                        a.kdaoh,
                        ISNULL(ao.nmao, '(Tanpa AO)') AS nama_ao,
                        a.colbaru,
                        CAST(ISNULL(a.osmdlc, 0) AS DECIMAL(38,6)) AS os_pokok,
                        CAST(ISNULL(a.htgagun, 0) AS DECIMAL(38,6)) AS htgagun,
                        ISNULL(NULLIF(LTRIM(RTRIM(j.jnsjamin)), ''), ISNULL(NULLIF(LTRIM(RTRIM(a.jnsagun)), ''), '0')) AS jns_agunan,
                        ISNULL(NULLIF(LTRIM(RTRIM(j.jnsikat)), ''), '0') AS jns_ikatan,
                        {$golJaminExpression} AS gol_jamin
                    FROM {$tableName} a
                    LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                    LEFT JOIN CABANG cab ON a.kdloc = cab.kdloc
                    LEFT JOIN AO ao ON a.kdaoh = ao.kdao
                    OUTER APPLY (
                        SELECT TOP 1 j.jnsjamin, j.jnsikat
                        FROM TOFJAMIN j
                        WHERE j.nokontrak = a.nokontrak AND j.stsrec = 'A'
                        ORDER BY j.urut
                    ) j
                    WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                ),
                current_calc AS (
                    SELECT *,
                        CASE
                            WHEN jns_agunan = '72' AND jns_ikatan <> '99' AND gol_jamin <> '874' THEN os_pokok * 0.75 * 0.50
                            WHEN jns_agunan = '72' AND jns_ikatan <> '99' AND gol_jamin = '874' THEN os_pokok * 0.50
                            WHEN jns_agunan = '72' AND jns_ikatan = '99' THEN 0
                            ELSE htgagun
                        END AS nilai_likuidasi
                    FROM current_base
                ),
                current_ppap AS (
                    SELECT *,
                        CASE
                            WHEN colbaru = '1' THEN ROUND(os_pokok * 0.005, 0)
                            WHEN nilai_likuidasi - os_pokok < 0 AND colbaru = '2' THEN ROUND((os_pokok - nilai_likuidasi) * 0.03, 0)
                            WHEN nilai_likuidasi - os_pokok < 0 AND colbaru = '3' THEN ROUND((os_pokok - nilai_likuidasi) * 0.10, 0)
                            WHEN nilai_likuidasi - os_pokok < 0 AND colbaru = '4' THEN ROUND((os_pokok - nilai_likuidasi) * 0.50, 0)
                            WHEN nilai_likuidasi - os_pokok < 0 AND colbaru = '5' THEN ROUND((os_pokok - nilai_likuidasi) * 1.00, 0)
                            ELSE 0
                        END AS ppap_current
                    FROM current_calc
                ),
                previous_ppap AS (
                    SELECT
                        a.nokontrak,
                        a.nocif,
                        LTRIM(RTRIM(a.nama)) AS nama,
                        a.kdprd,
                        ISNULL(p.ket, 'Tanpa Produk') AS produk,
                        a.kdloc,
                        ISNULL(cab.nama, '(Tanpa Cabang)') AS cabang,
                        a.kdaoh,
                        ISNULL(ao.nmao, '(Tanpa AO)') AS nama_ao,
                        a.colbaru AS col_previous,
                        CAST(ISNULL(a.osmdlc, 0) AS DECIMAL(38,6)) AS os_previous,
                        CAST(ISNULL(a.ppap, 0) AS DECIMAL(38,6)) AS ppap_previous
                    FROM {$previousTable} a
                    LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                    LEFT JOIN CABANG cab ON a.kdloc = cab.kdloc
                    LEFT JOIN AO ao ON a.kdaoh = ao.kdao
                    WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                ),
                combined AS (
                    SELECT
                        COALESCE(c.nokontrak, p.nokontrak) AS nokontrak,
                        COALESCE(c.nocif, p.nocif) AS nocif,
                        COALESCE(c.nama, p.nama) AS nama,
                        COALESCE(c.kdprd, p.kdprd) AS kdprd,
                        COALESCE(c.produk, p.produk) AS produk,
                        COALESCE(c.kdloc, p.kdloc) AS kdloc,
                        COALESCE(c.cabang, p.cabang) AS cabang,
                        COALESCE(c.kdaoh, p.kdaoh) AS kdaoh,
                        COALESCE(c.nama_ao, p.nama_ao) AS nama_ao,
                        p.col_previous,
                        c.colbaru AS col_current,
                        ISNULL(p.os_previous, 0) AS os_previous,
                        ISNULL(c.os_pokok, 0) AS os_current,
                        ISNULL(c.nilai_likuidasi, 0) AS nilai_likuidasi,
                        ISNULL(c.jns_agunan, '-') AS jns_agunan,
                        ISNULL(c.jns_ikatan, '-') AS jns_ikatan,
                        ISNULL(c.gol_jamin, '-') AS gol_jamin,
                        ISNULL(p.ppap_previous, 0) AS ppap_previous,
                        ISNULL(c.ppap_current, 0) AS ppap_current,
                        ISNULL(c.ppap_current, 0) - ISNULL(p.ppap_previous, 0) AS ppap_gap,
                        CASE
                            WHEN c.nokontrak IS NULL THEN 'Lunas / Keluar Scope'
                            WHEN p.nokontrak IS NULL THEN 'Baru / Masuk Scope'
                            WHEN ISNULL(c.ppap_current, 0) - ISNULL(p.ppap_previous, 0) > 0 THEN 'Pembentukan'
                            WHEN ISNULL(c.ppap_current, 0) - ISNULL(p.ppap_previous, 0) < 0 THEN 'Pengembalian'
                            ELSE 'Stabil'
                        END AS movement_status
                    FROM current_ppap c
                    FULL OUTER JOIN previous_ppap p ON c.nokontrak = p.nokontrak
                )
                SELECT *
                FROM combined
                WHERE ppap_gap <> 0
                ORDER BY ABS(ppap_gap) DESC, nokontrak ASC
            ", array_merge($mainBindings, $mainBindings));

            $positiveGap = 0.0;
            $negativeGap = 0.0;
            $newCount = 0;
            $exitCount = 0;
            foreach ($detailRows as $detailRow) {
                $gapValue = (float) ($detailRow->ppap_gap ?? 0);
                if ($gapValue > 0) {
                    $positiveGap += $gapValue;
                } elseif ($gapValue < 0) {
                    $negativeGap += $gapValue;
                }

                if (($detailRow->movement_status ?? '') === 'Baru / Masuk Scope') {
                    $newCount++;
                } elseif (($detailRow->movement_status ?? '') === 'Lunas / Keluar Scope') {
                    $exitCount++;
                }
            }

            $detail = [
                'rows' => $detailRows,
                'summary' => [
                    'row_count' => count($detailRows),
                    'positive_gap' => $positiveGap,
                    'negative_gap' => $negativeGap,
                    'net_gap' => $positiveGap + $negativeGap,
                    'new_or_in_scope_count' => $newCount,
                    'paid_off_or_out_scope_count' => $exitCount,
                    'previous_database' => $previousDatabase,
                ],
            ];
        }

        return [
            'current_ppap' => $currentPPKA,
            'current_os' => (float) ($current->current_os ?? 0),
            'current_noa' => (int) ($current->current_noa ?? 0),
            'previous_ppap' => $previousPPKA,
            'previous_noa' => $previousNoa,
            'gap' => $currentPPKA - $previousPPKA,
            'previous_database' => $previousDatabase,
            'previous_available' => $previousAvailable,
            'detail' => $detail,
        ];
    }

    private function quoteSqlServerIdentifier(string $identifier): string
    {
        return '['.str_replace(']', ']]', $identifier).']';
    }

    /**
     * Pembiayaan Kualitas Rendah mengikuti pola query operasional:
     * Kol 2-5 memakai seluruh data EOM/snapshot, Kol 1 memakai kontrak yang masih valid dari TOFLMBHP terbaru.
     *
     * @param  list<mixed>  $mainBindings
     * @return array{summary: array<string,mixed>, rows: list<object>, methodology: array<string,string>}
     */
    private function getPkrMetrics(string $tableName, bool $isHistoris, string $mainFilter, array $mainBindings, int $reqTahun, int $reqBulan): array
    {
        $periode = sprintf('%04d%02d', $reqTahun, $reqBulan);
        $hasEomPeriod = false;
        try {
            $hasEomPeriod = DB::connection($this->connection)
                ->table('TOFLMBEOM')
                ->where('periode', $periode)
                ->exists();
        } catch (\Throwable) {
            $hasEomPeriod = false;
        }

        $pkrTableName = $hasEomPeriod ? 'TOFLMBEOM' : $tableName;
        $usesPeriodTable = $hasEomPeriod || $isHistoris;
        $periodeSelect = $usesPeriodTable ? 'e.periode' : "'{$periode}'";
        $periodeFilter = $usesPeriodTable ? ' AND e.periode = ?' : '';
        $periodeBindings = $usesPeriodTable ? [$periode] : [];
        $filterWithoutPeriod = preg_replace('/\s+AND\s+a\.periode\s=\s\?/i', '', $mainFilter) ?? $mainFilter;
        $filterForEom = str_replace('a.', 'e.', $filterWithoutPeriod).$periodeFilter;
        $bindingsForEom = $isHistoris
            ? array_values(array_slice($mainBindings, 0, max(0, count($mainBindings) - 1)))
            : $mainBindings;
        $queryBindings = array_merge($bindingsForEom, $periodeBindings, $bindingsForEom, $periodeBindings);

        try {
            $rows = DB::connection($this->connection)->select("
                WITH DataTerbaru AS (
                    SELECT
                        hp.nokontrak,
                        ROW_NUMBER() OVER(PARTITION BY hp.nokontrak ORDER BY LEFT(hp.inptgl, 8) DESC) AS rn
                    FROM TOFLMB t
                    INNER JOIN TOFLMBHP hp ON t.nokontrak = hp.nokontrak
                    INNER JOIN MCIF c ON c.NOCIF = t.NOCIF
                    WHERE hp.stsrec IN ('A','N') AND t.stsrec IN ('A','N')
                ),
                KontrakValid AS (
                    SELECT nokontrak FROM DataTerbaru WHERE rn = 1
                ),
                QuerySemuaData AS (
                    SELECT
                        {$periodeSelect} AS periode,
                        e.kdloc,
                        t.segmen,
                        s.ket,
                        e.colbaru,
                        SUM(CAST(ISNULL(e.osmdlc, 0) AS DECIMAL(38,6))) AS tot_osmdlc_awal,
                        COUNT(*) AS tot_rec_awal
                    FROM {$pkrTableName} e
                    INNER JOIN TOFLMB t ON e.nokontrak = t.nokontrak
                    LEFT JOIN SEGMEN s ON t.segmen = s.kdseg
                    WHERE e.stsrec IN ('A','N')
                      AND e.pokpby NOT IN ('12','30','18')
                      AND e.stsacc NOT IN ('W','C')
                      AND e.ststrn = '*'
                      {$filterForEom}
                    GROUP BY e.kdloc, t.segmen, s.ket, e.colbaru".($usesPeriodTable ? ', e.periode' : '')."
                ),
                QueryDataFilter AS (
                    SELECT
                        {$periodeSelect} AS periode,
                        e.kdloc,
                        t.segmen,
                        s.ket,
                        e.colbaru,
                        SUM(CAST(ISNULL(e.osmdlc, 0) AS DECIMAL(38,6))) AS tot_osmdlc_filter,
                        COUNT(e.nokontrak) AS tot_rec_filter
                    FROM {$pkrTableName} e
                    INNER JOIN KontrakValid kv ON e.nokontrak = kv.nokontrak
                    INNER JOIN TOFLMB t ON e.nokontrak = t.nokontrak
                    LEFT JOIN SEGMEN s ON t.segmen = s.kdseg
                    WHERE e.stsrec IN ('A','N')
                      AND e.pokpby NOT IN ('12','30','18')
                      AND e.stsacc NOT IN ('W','C')
                      AND e.ststrn = '*'
                      {$filterForEom}
                    GROUP BY e.kdloc, t.segmen, s.ket, e.colbaru".($usesPeriodTable ? ', e.periode' : '')."
                )
                SELECT
                    COALESCE(q1.periode, q2.periode) AS periode,
                    COALESCE(q1.kdloc, q2.kdloc) AS kdloc,
                    COALESCE(q1.segmen, q2.segmen) AS segmen,
                    COALESCE(q1.ket, q2.ket) AS ket,
                    COALESCE(q1.colbaru, q2.colbaru) AS colbaru,
                    ISNULL(q1.tot_osmdlc_awal, 0) AS osmdlc_semua_data,
                    ISNULL(q1.tot_rec_awal, 0) AS rec_semua_data,
                    CASE
                        WHEN COALESCE(q1.colbaru, q2.colbaru) IN ('2','3','4','5') THEN ISNULL(q1.tot_osmdlc_awal, 0)
                        ELSE ISNULL(q2.tot_osmdlc_filter, 0)
                    END AS os_pkr,
                    CASE
                        WHEN COALESCE(q1.colbaru, q2.colbaru) IN ('2','3','4','5') THEN ISNULL(q1.tot_rec_awal, 0)
                        ELSE ISNULL(q2.tot_rec_filter, 0)
                    END AS noa_pkr,
                    ISNULL(q1.tot_osmdlc_awal, 0) - (
                        CASE
                            WHEN COALESCE(q1.colbaru, q2.colbaru) IN ('2','3','4','5') THEN ISNULL(q1.tot_osmdlc_awal, 0)
                            ELSE ISNULL(q2.tot_osmdlc_filter, 0)
                        END
                    ) AS selisih_osmdlc,
                    ISNULL(q1.tot_rec_awal, 0) - (
                        CASE
                            WHEN COALESCE(q1.colbaru, q2.colbaru) IN ('2','3','4','5') THEN ISNULL(q1.tot_rec_awal, 0)
                            ELSE ISNULL(q2.tot_rec_filter, 0)
                        END
                    ) AS selisih_rec
                FROM QuerySemuaData q1
                FULL OUTER JOIN QueryDataFilter q2
                  ON q1.periode = q2.periode
                 AND q1.kdloc = q2.kdloc
                 AND q1.segmen = q2.segmen
                 AND q1.colbaru = q2.colbaru
                ORDER BY segmen ASC, kdloc ASC, colbaru ASC
            ", $queryBindings);
        } catch (\Throwable $exception) {
            return [
                'summary' => [
                    'available' => false,
                    'message' => 'Query PKR belum dapat dieksekusi pada database/filter aktif: '.$exception->getMessage(),
                    'pkr_os' => 0,
                    'pkr_noa' => 0,
                    'pkr_ratio' => 0,
                    'total_scope_os' => 0,
                ],
                'rows' => [],
                'detail_rows' => [],
                'trend' => [],
                'trend_meta' => [],
                'methodology' => [
                    'formula' => 'PKR = OS_PKR Kol 1 restrukturisasi/kontrak valid + Kol 2 + Kol 3 + Kol 4 + Kol 5',
                    'basis' => 'TOFLMBEOM/TOFLMB dengan filter aktif, pokpby NOT IN 12/30/18, stsacc bukan W/C, ststrn=*',
                ],
            ];
        }

        $totalScopeOs = 0.0;
        $pkrOs = 0.0;
        $pkrNoa = 0;
        $pkrNonLancarOs = 0.0;
        $pkrNonLancarNoa = 0;
        $restructuredLancarOs = 0.0;
        $restructuredLancarNoa = 0;
        $watchKol2Os = 0.0;
        $excludedLancarOs = 0.0;

        foreach ($rows as $row) {
            $col = (string) ($row->colbaru ?? '');
            $totalScopeOs += (float) ($row->osmdlc_semua_data ?? 0);
            $pkrOs += (float) ($row->os_pkr ?? 0);
            $pkrNoa += (int) ($row->noa_pkr ?? 0);
            if (in_array($col, ['2','3','4','5'], true)) {
                $pkrNonLancarOs += (float) ($row->os_pkr ?? 0);
                $pkrNonLancarNoa += (int) ($row->noa_pkr ?? 0);
            }
            if ($col === '2') {
                $watchKol2Os += (float) ($row->os_pkr ?? 0);
            }
            if ($col === '1') {
                $restructuredLancarOs += (float) ($row->os_pkr ?? 0);
                $restructuredLancarNoa += (int) ($row->noa_pkr ?? 0);
                $excludedLancarOs += (float) ($row->selisih_osmdlc ?? 0);
            }
        }

        $detailRows = $this->getPkrContractDetails($pkrTableName, $usesPeriodTable, $filterWithoutPeriod, $bindingsForEom, $periode);

        return [
            'summary' => [
                'available' => true,
                'periode' => $periode,
                'source_table' => $pkrTableName,
                'source_policy' => $hasEomPeriod ? 'Menggunakan TOFLMBEOM sesuai periode karena data EOM tersedia.' : 'Fallback ke tabel aktif karena TOFLMBEOM periode terpilih belum tersedia.',
                'pkr_os' => $pkrOs,
                'pkr_noa' => $pkrNoa,
                'pkr_ratio' => $totalScopeOs > 0 ? ($pkrOs / $totalScopeOs) * 100 : 0,
                'total_scope_os' => $totalScopeOs,
                'pkr_non_lancar_os' => $pkrNonLancarOs,
                'pkr_non_lancar_noa' => $pkrNonLancarNoa,
                'restructured_lancar_os' => $restructuredLancarOs,
                'restructured_lancar_noa' => $restructuredLancarNoa,
                'watch_kol2_os' => $watchKol2Os,
                'watch_kol2_ratio' => $totalScopeOs > 0 ? ($watchKol2Os / $totalScopeOs) * 100 : 0,
                'excluded_lancar_os' => $excludedLancarOs,
                'interpretation' => $this->interpretPkrRatio($totalScopeOs > 0 ? ($pkrOs / $totalScopeOs) * 100 : 0, $watchKol2Os),
            ],
            'rows' => $rows,
            'detail_rows' => $detailRows,
            'methodology' => [
                'formula' => 'PKR = OS_PKR Kol 1 restrukturisasi/kontrak valid + Kol 2 + Kol 3 + Kol 4 + Kol 5 terhadap total scope pembiayaan',
                'kol1_policy' => 'Kol 1 masuk PKR hanya sebesar OS_PKR dari QueryDataFilter, yaitu kontrak yang masih valid berdasarkan TOFLMBHP terbaru.',
                'basis' => 'TOFLMBEOM/TOFLMB dengan filter aktif, pokpby NOT IN 12/30/18, stsacc bukan W/C, ststrn=*',
            ],
        ];
    }

    /**
     * Detail kontrak pembentuk PKR: Kol 1 valid/restrukturisasi dan seluruh Kol 2-5.
     *
     * @param  list<mixed>  $bindingsForFilter
     * @return list<object>
     */
    private function getPkrContractDetails(string $pkrTableName, bool $usesPeriodTable, string $mainFilterWithoutPeriod, array $bindingsForFilter, string $periode): array
    {
        $periodeFilter = $usesPeriodTable ? ' AND e.periode = ?' : '';
        $periodeSelect = $usesPeriodTable ? 'e.periode' : "'{$periode}'";
        $filterForEom = str_replace('a.', 'e.', $mainFilterWithoutPeriod).$periodeFilter;
        $bindings = array_merge($bindingsForFilter, $usesPeriodTable ? [$periode] : []);

        try {
            return DB::connection($this->connection)->select("
                WITH DataTerbaru AS (
                    SELECT
                        hp.nokontrak,
                        ROW_NUMBER() OVER(PARTITION BY hp.nokontrak ORDER BY LEFT(hp.inptgl, 8) DESC) AS rn,
                        MAX(CASE WHEN hp.stsrec IN ('A','N') THEN 1 ELSE 0 END) OVER(PARTITION BY hp.nokontrak) AS has_restru_history
                    FROM TOFLMB t
                    INNER JOIN TOFLMBHP hp ON t.nokontrak = hp.nokontrak
                    INNER JOIN MCIF c ON c.NOCIF = t.NOCIF
                    WHERE hp.stsrec IN ('A','N') AND t.stsrec IN ('A','N')
                ),
                KontrakValid AS (
                    SELECT nokontrak, has_restru_history
                    FROM DataTerbaru
                    WHERE rn = 1
                ),
                Base AS (
                    SELECT
                        {$periodeSelect} AS periode,
                        e.nokontrak,
                        e.nocif,
                        LTRIM(RTRIM(e.nama)) AS nama,
                        e.kdloc,
                        ISNULL(cab.nama, '(Tanpa Cabang)') AS cabang,
                        t.segmen,
                        ISNULL(s.ket, '(Tanpa Segmen)') AS segmen_nama,
                        e.kdaoh,
                        ISNULL(ao.nmao, '(Tanpa AO)') AS nama_ao,
                        e.kdprd,
                        ISNULL(p.ket, 'Tanpa Produk') AS produk,
                        e.colbaru,
                        CAST(ISNULL(e.osmdlc, 0) AS DECIMAL(38,6)) AS os_pokok,
                        CAST(ISNULL(e.ppap, 0) AS DECIMAL(38,6)) AS ppap_system,
                        CAST(ISNULL(e.tgkmdl, 0) AS DECIMAL(38,6)) AS tunggakan_pokok,
                        CAST(ISNULL(e.tgkmgn, 0) AS DECIMAL(38,6)) AS tunggakan_margin,
                        ISNULL(kv.has_restru_history, 0) AS has_restru_history,
                        CASE
                            WHEN e.colbaru = '1' AND kv.nokontrak IS NOT NULL THEN 'Kol 1 Restrukturisasi/Valid'
                            WHEN e.colbaru = '2' THEN 'Kol 2 Watch'
                            WHEN e.colbaru IN ('3','4','5') THEN 'Kol 3-5 NPF'
                            ELSE 'Di Luar PKR'
                        END AS pkr_bucket
                    FROM {$pkrTableName} e
                    INNER JOIN TOFLMB t ON e.nokontrak = t.nokontrak
                    LEFT JOIN KontrakValid kv ON e.nokontrak = kv.nokontrak
                    LEFT JOIN SEGMEN s ON t.segmen = s.kdseg
                    LEFT JOIN CABANG cab ON e.kdloc = cab.kdloc
                    LEFT JOIN AO ao ON e.kdaoh = ao.kdao
                    LEFT JOIN SETUPLOAN p ON e.kdprd = p.kdprd
                    WHERE e.stsrec IN ('A','N')
                      AND e.pokpby NOT IN ('12','30','18')
                      AND e.stsacc NOT IN ('W','C')
                      AND e.ststrn = '*'
                      {$filterForEom}
                )
                SELECT TOP 1000 *
                FROM Base
                WHERE colbaru IN ('2','3','4','5')
                   OR (colbaru = '1' AND pkr_bucket = 'Kol 1 Restrukturisasi/Valid')
                ORDER BY
                    CASE pkr_bucket
                        WHEN 'Kol 1 Restrukturisasi/Valid' THEN 1
                        WHEN 'Kol 2 Watch' THEN 2
                        ELSE 3
                    END,
                    os_pokok DESC,
                    nokontrak ASC
            ", $bindings);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Trend PKR bulanan dari snapshot database yang tersedia.
     *
     * @param  list<mixed>  $mainBindings
     * @return array{trend: list<array<string,mixed>>, meta: array<string,mixed>}
     */
    private function getPkrTrend(int $reqTahun, int $reqBulan, string $mainFilter, array $mainBindings, ?string $restoreDatabase): array
    {
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
        $activePeriod = $this->getCurrentPeriodInternal();
        $currentYear = (int) $activePeriod['year'];
        $currentMonth = (int) $activePeriod['month'];
        $lastMonth = max(1, min(12, $reqBulan));
        $trend = [];
        $available = 0;
        $missing = 0;

        try {
            for ($month = 1; $month <= $lastMonth; $month++) {
                $database = $this->resolveMonthlySnapshotDatabase($reqTahun, $month);
                $isCurrentRuntime = $database === null && $reqTahun === $currentYear && $month === $currentMonth;

                if ($database !== null) {
                    app(MciConnectionService::class)->switchToDatabase($database);
                } elseif ($isCurrentRuntime && $restoreDatabase !== null) {
                    app(MciConnectionService::class)->switchToDatabase($restoreDatabase);
                    $database = $restoreDatabase;
                } else {
                    $missing++;
                    $trend[] = [
                        'tahun' => $reqTahun,
                        'bulan' => $month,
                        'label' => $monthNames[$month] ?? (string) $month,
                        'source_database' => null,
                        'available' => false,
                        'message' => 'Database snapshot belum tersedia',
                    ];
                    continue;
                }

                $metrics = $this->getPkrMetrics('TOFLMB', false, $mainFilter, $mainBindings, $reqTahun, $month);
                $summary = $metrics['summary'] ?? [];
                $available++;

                $trend[] = [
                    'tahun' => $reqTahun,
                    'bulan' => $month,
                    'label' => $monthNames[$month] ?? (string) $month,
                    'source_database' => $database,
                    'available' => (bool) ($summary['available'] ?? false),
                    'pkr_ratio' => (float) ($summary['pkr_ratio'] ?? 0),
                    'pkr_os' => (float) ($summary['pkr_os'] ?? 0),
                    'pkr_noa' => (int) ($summary['pkr_noa'] ?? 0),
                    'total_scope_os' => (float) ($summary['total_scope_os'] ?? 0),
                    'restructured_lancar_os' => (float) ($summary['restructured_lancar_os'] ?? 0),
                    'restructured_lancar_noa' => (int) ($summary['restructured_lancar_noa'] ?? 0),
                    'pkr_non_lancar_os' => (float) ($summary['pkr_non_lancar_os'] ?? 0),
                    'pkr_non_lancar_noa' => (int) ($summary['pkr_non_lancar_noa'] ?? 0),
                    'watch_kol2_os' => (float) ($summary['watch_kol2_os'] ?? 0),
                    'watch_kol2_ratio' => (float) ($summary['watch_kol2_ratio'] ?? 0),
                ];
            }
        } finally {
            if ($restoreDatabase !== null) {
                app(MciConnectionService::class)->switchToDatabase($restoreDatabase);
            }
        }

        return [
            'trend' => $this->appendPkrTrendDeltas($trend),
            'meta' => [
                'requested_year' => $reqTahun,
                'requested_month' => $reqBulan,
                'available_months' => $available,
                'missing_months' => $missing,
                'source_policy' => 'Trend PKR memakai snapshot database bulanan yang tersedia; bulan tanpa database ditandai unavailable.',
            ],
        ];
    }

    /**
     * @param  list<array<string,mixed>>  $trend
     * @return list<array<string,mixed>>
     */
    private function appendPkrTrendDeltas(array $trend): array
    {
        $previous = null;
        foreach ($trend as $index => $row) {
            if (! ($row['available'] ?? false)) {
                $trend[$index]['pkr_delta'] = null;
                $trend[$index]['pkr_os_delta'] = null;
                continue;
            }

            if ($previous === null) {
                $trend[$index]['pkr_delta'] = 0;
                $trend[$index]['pkr_os_delta'] = 0;
            } else {
                $trend[$index]['pkr_delta'] = (float) ($row['pkr_ratio'] ?? 0) - (float) ($previous['pkr_ratio'] ?? 0);
                $trend[$index]['pkr_os_delta'] = (float) ($row['pkr_os'] ?? 0) - (float) ($previous['pkr_os'] ?? 0);
            }

            $previous = $row;
        }

        return $trend;
    }

    /**
     * @param  array<string,mixed>  $kapMetrics
     * @return array{rows: list<array<string,mixed>>, summary: array<string,mixed>}
     */
    private function buildKapWorksheetReconciliation(array $kapMetrics): array
    {
        $breakdown = $kapMetrics['breakdown'] ?? [];
        $summary = $kapMetrics['summary'] ?? [];
        $rows = [];
        $rates = ['1' => '0%', '2' => '25%', '3' => '50%', '4' => '75%', '5' => '100%'];

        foreach ($breakdown as $item) {
            $col = (string) ($item->colbaru ?? '');
            $rows[] = [
                'section' => 'Pembiayaan',
                'kolektibilitas' => $col,
                'label' => $this->collectibilityName($col),
                'noa' => (int) ($item->noa ?? 0),
                'baki_debet' => (float) ($item->os_pokok ?? 0),
                'agunan_dikuasai' => (float) ($item->agunan_berbobot ?? 0),
                'jumlah_setelah_agunan' => (float) ($item->net_exposure_agunan ?? 0),
                'tarif_ppap_wd' => $rates[$col] ?? '-',
                'ppap_wajib_dibentuk' => (float) ($item->ppap_wajib_dibentuk ?? 0),
                'ppap_system' => (float) ($item->ppap_system ?? 0),
                'apyd' => in_array($col, ['3','4','5'], true) ? (float) ($item->apyd ?? 0) : 0,
                'row_type' => 'detail',
            ];
        }

        $rows[] = [
            'section' => 'Pembiayaan',
            'kolektibilitas' => 'subtotal',
            'label' => 'Subtotal Pembiayaan',
            'noa' => (int) ($summary['total_noa'] ?? 0),
            'baki_debet' => (float) ($summary['total_pembiayaan'] ?? 0),
            'agunan_dikuasai' => (float) ($summary['agunan_berbobot'] ?? 0),
            'jumlah_setelah_agunan' => (float) ($summary['net_exposure_agunan'] ?? 0),
            'tarif_ppap_wd' => '-',
            'ppap_wajib_dibentuk' => (float) ($summary['ppap_wajib_dibentuk_financing'] ?? 0),
            'ppap_system' => (float) ($summary['ppap_system'] ?? 0),
            'apyd' => (float) ($summary['apyd_financing_tks'] ?? 0),
            'row_type' => 'subtotal',
        ];

        $rows[] = [
            'section' => 'Antar Bank Aktiva',
            'kolektibilitas' => 'aba',
            'label' => 'ABA Non-Macet',
            'noa' => null,
            'baki_debet' => (float) ($summary['antar_bank_aktiva_lancar'] ?? 0),
            'agunan_dikuasai' => 0,
            'jumlah_setelah_agunan' => (float) ($summary['antar_bank_aktiva_lancar'] ?? 0),
            'tarif_ppap_wd' => '-',
            'ppap_wajib_dibentuk' => 0,
            'ppap_system' => 0,
            'apyd' => (float) ($summary['antar_bank_aktiva_apyd'] ?? 0),
            'row_type' => 'detail',
        ];

        if ((float) ($summary['antar_bank_aktiva_macet'] ?? 0) > 0) {
            $rows[] = [
                'section' => 'Antar Bank Aktiva',
                'kolektibilitas' => 'aba-macet',
                'label' => 'ABA Macet Dikecualikan',
                'noa' => null,
                'baki_debet' => (float) ($summary['antar_bank_aktiva_macet'] ?? 0),
                'agunan_dikuasai' => 0,
                'jumlah_setelah_agunan' => 0,
                'tarif_ppap_wd' => '-',
                'ppap_wajib_dibentuk' => 0,
                'ppap_system' => 0,
                'apyd' => 0,
                'row_type' => 'adjustment',
            ];
        }

        $rows[] = [
            'section' => 'Total Worksheet',
            'kolektibilitas' => 'total',
            'label' => 'Total Aktiva Produktif',
            'noa' => (int) ($summary['total_noa'] ?? 0),
            'baki_debet' => (float) ($summary['total_aktiva_produktif'] ?? 0),
            'agunan_dikuasai' => (float) ($summary['agunan_berbobot'] ?? 0),
            'jumlah_setelah_agunan' => (float) ($summary['net_exposure_agunan'] ?? 0) + (float) ($summary['antar_bank_aktiva_lancar'] ?? 0),
            'tarif_ppap_wd' => '-',
            'ppap_wajib_dibentuk' => (float) ($summary['ppap_wajib_dibentuk'] ?? 0),
            'ppap_system' => (float) ($summary['ppap_system'] ?? 0),
            'apyd' => (float) ($summary['apyd'] ?? 0),
            'row_type' => 'total',
        ];

        return [
            'rows' => $rows,
            'summary' => [
                'row_count' => count($rows),
                'policy' => 'Rekonsiliasi worksheet menyatukan pembiayaan per kolektibilitas, ABA non-macet, pengecualian ABA macet, dan total Aktiva Produktif.',
            ],
        ];
    }

    private function collectibilityName(string $col): string
    {
        return [
            '1' => 'Kol 1 - Lancar',
            '2' => 'Kol 2 - DPK',
            '3' => 'Kol 3 - Kurang Lancar',
            '4' => 'Kol 4 - Diragukan',
            '5' => 'Kol 5 - Macet',
        ][$col] ?? 'Kol '.$col;
    }

    private function interpretMiapbRatio(float $ratio, float $denominator): string
    {
        if ($denominator <= 0) {
            return 'Aset bermasalah sudah tertutup PPKA bermasalah pada denominator MIAPB; tetap validasi kualitas PPKA per debitur besar.';
        }

        if ($ratio >= 200) {
            return 'Modal inti sangat kuat terhadap aset bermasalah neto setelah PPKA; risiko penyerapan kerugian relatif terkendali.';
        }

        if ($ratio >= 100) {
            return 'Modal inti masih menutup aset bermasalah neto, namun perlu pemantauan bila NPF atau gap PPKA meningkat.';
        }

        return 'Modal inti lebih rendah dari aset bermasalah neto; perlu rencana penyehatan kualitas, penguatan cadangan, dan kontrol ekspansi berisiko.';
    }

    private function interpretAydaRatio(float $ratio, float $amount): string
    {
        if ($amount <= 0) {
            return 'Tidak ada AYDA terbaca pada saldo GL berbasis mapping saat ini.';
        }

        if ($ratio <= 1) {
            return 'AYDA masih kecil terhadap total pembiayaan, tetapi perlu tetap dipantau sebagai aset bermasalah non-pembiayaan.';
        }

        if ($ratio <= 3) {
            return 'AYDA mulai material; pastikan rencana penyelesaian, valuasi, dan umur kepemilikan termonitor.';
        }

        return 'AYDA tinggi terhadap portofolio pembiayaan; perlu action plan penyelesaian aset dan eskalasi manajemen risiko.';
    }

    private function interpretPkrRatio(float $ratio, float $kol2Os): string
    {
        if ($ratio <= 10) {
            return 'PKR relatif rendah; fokus utama menjaga Kol 2 agar tidak bermigrasi ke NPF.';
        }

        if ($ratio <= 25) {
            return 'PKR berada pada area perhatian; Kol 2 dan Kol 3 perlu daftar kerja remedial mingguan.';
        }

        return 'PKR tinggi; perlu konsolidasi collection, restrukturisasi selektif, dan pembatasan ekspansi pada segmen berisiko.';
    }

    /**
     * Trend prudential bulanan berbasis snapshot database yang tersedia.
     *
     * @param  list<mixed>  $mainBindings
     * @return array{trend: list<array<string,mixed>>, meta: array<string,mixed>}
     */
    private function getKapPrudentialTrend(int $reqTahun, int $reqBulan, string $mainFilter, array $mainBindings, ?string $restoreDatabase): array
    {
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
        $activePeriod = $this->getCurrentPeriodInternal();
        $currentYear = (int) $activePeriod['year'];
        $currentMonth = (int) $activePeriod['month'];
        $lastMonth = max(1, min(12, $reqBulan));
        $trend = [];
        $available = 0;
        $missing = 0;

        try {
            for ($month = 1; $month <= $lastMonth; $month++) {
                $database = $this->resolveMonthlySnapshotDatabase($reqTahun, $month);
                $isCurrentRuntime = $database === null && $reqTahun === $currentYear && $month === $currentMonth;

                if ($database !== null) {
                    app(MciConnectionService::class)->switchToDatabase($database);
                } elseif ($isCurrentRuntime && $restoreDatabase !== null) {
                    app(MciConnectionService::class)->switchToDatabase($restoreDatabase);
                    $database = $restoreDatabase;
                } else {
                    $missing++;
                    $trend[] = [
                        'tahun' => $reqTahun,
                        'bulan' => $month,
                        'label' => $monthNames[$month] ?? (string) $month,
                        'source_database' => null,
                        'available' => false,
                        'message' => 'Database snapshot belum tersedia',
                    ];
                    continue;
                }

                $metrics = $this->getKapRiskMetrics('TOFLMB', false, $mainFilter, $mainBindings, $reqTahun, $month, false);
                $summary = $metrics['summary'] ?? [];
                $available++;

                $trend[] = [
                    'tahun' => $reqTahun,
                    'bulan' => $month,
                    'label' => $monthNames[$month] ?? (string) $month,
                    'source_database' => $database,
                    'available' => true,
                    'kap_ratio' => (float) ($summary['kap_ratio'] ?? 0),
                    'apyd' => (float) ($summary['apyd'] ?? 0),
                    'apyd_ratio' => (float) ($summary['apyd_ratio'] ?? 0),
                    'ppap_wajib_dibentuk' => (float) ($summary['ppap_wajib_dibentuk'] ?? 0),
                    'ppap_system' => (float) ($summary['ppap_system'] ?? 0),
                    'ppap_rekap_current' => (float) ($summary['ppap_rekap_current'] ?? 0),
                    'total_ppap_bulanan' => (float) ($summary['ppap_rekap_current'] ?? 0),
                    'ppap_gap' => (float) ($summary['ppap_gap'] ?? 0),
                    'ppap_coverage_to_wd' => (float) ($summary['ppap_coverage_to_wd'] ?? 0),
                    'net_exposure_agunan' => (float) ($summary['net_exposure_agunan'] ?? 0),
                    'net_exposure_ratio' => (float) ($summary['net_exposure_ratio'] ?? 0),
                    'total_aktiva_produktif' => (float) ($summary['total_aktiva_produktif'] ?? 0),
                    'total_pembiayaan' => (float) ($summary['total_pembiayaan'] ?? 0),
                    'aba_macet' => (float) ($summary['antar_bank_aktiva_macet'] ?? 0),
                    'aba_unmapped' => (float) ($summary['antar_bank_aktiva_unmapped'] ?? 0),
                    'shortfall_count' => count($metrics['ppap_shortfall_accounts'] ?? []),
                    'recommendation_count' => count($metrics['recommendations'] ?? []),
                ];
            }
        } finally {
            if ($restoreDatabase !== null) {
                app(MciConnectionService::class)->switchToDatabase($restoreDatabase);
            }
        }

        return [
            'trend' => $this->appendKapTrendDeltas($trend),
            'meta' => [
                'requested_year' => $reqTahun,
                'requested_month' => $reqBulan,
                'available_months' => $available,
                'missing_months' => $missing,
                'source_policy' => 'Trend memakai snapshot database bulanan yang tersedia. Bulan tanpa database ditandai unavailable dan tidak dipaksakan dari data lain.',
            ],
        ];
    }

    /**
     * @param  list<array<string,mixed>>  $trend
     * @return list<array<string,mixed>>
     */
    private function appendKapTrendDeltas(array $trend): array
    {
        $previous = null;
        foreach ($trend as $index => $row) {
            if (! ($row['available'] ?? false)) {
                $trend[$index]['kap_delta'] = null;
                $trend[$index]['apyd_delta'] = null;
                $trend[$index]['ppap_gap_delta'] = null;
                $trend[$index]['net_exposure_delta'] = null;
                continue;
            }

            if ($previous === null) {
                $trend[$index]['kap_delta'] = 0;
                $trend[$index]['apyd_delta'] = 0;
                $trend[$index]['ppap_gap_delta'] = 0;
                $trend[$index]['net_exposure_delta'] = 0;
            } else {
                $trend[$index]['kap_delta'] = (float) ($row['kap_ratio'] ?? 0) - (float) ($previous['kap_ratio'] ?? 0);
                $trend[$index]['apyd_delta'] = (float) ($row['apyd'] ?? 0) - (float) ($previous['apyd'] ?? 0);
                $trend[$index]['ppap_gap_delta'] = (float) ($row['ppap_gap'] ?? 0) - (float) ($previous['ppap_gap'] ?? 0);
                $trend[$index]['net_exposure_delta'] = (float) ($row['net_exposure_agunan'] ?? 0) - (float) ($previous['net_exposure_agunan'] ?? 0);
            }

            $previous = $row;
        }

        return $trend;
    }

    /**
     * @param  array<string,mixed>  $kapMetrics
     * @param  list<array<string,mixed>>  $trend
     * @param  array<string,mixed>  $trendMeta
     * @return array{items: list<array<string,mixed>>, summary: array<string,mixed>}
     */
    private function buildKapAnomalyDetector(array $kapMetrics, array $trend, array $trendMeta): array
    {
        $summary = $kapMetrics['summary'] ?? [];
        $items = [];
        $availableTrend = array_values(array_filter($trend, fn ($row) => (bool) ($row['available'] ?? false)));
        $last = ! empty($availableTrend) ? $availableTrend[count($availableTrend) - 1] : null;

        if ((int) ($trendMeta['missing_months'] ?? 0) > 0) {
            $items[] = [
                'severity' => 'warning',
                'title' => 'Snapshot Bulanan Belum Lengkap',
                'metric' => 'Data Trend',
                'value' => (int) ($trendMeta['missing_months'] ?? 0),
                'message' => 'Ada bulan dalam rentang filter yang belum memiliki database snapshot, sehingga trend menampilkan gap secara eksplisit.',
                'action' => 'Pastikan database bulan tersebut sudah tersedia dan mapping environment MCI_DB_* sudah terisi.',
            ];
        }

        if ($last !== null && (float) ($last['kap_delta'] ?? 0) < -1) {
            $items[] = [
                'severity' => (float) ($last['kap_delta'] ?? 0) < -3 ? 'danger' : 'warning',
                'title' => 'Rasio KAP Menurun',
                'metric' => 'KAP MoM',
                'value' => (float) ($last['kap_delta'] ?? 0),
                'message' => 'Rasio KAP turun dibanding bulan snapshot sebelumnya, mengindikasikan tekanan kualitas aktiva produktif.',
                'action' => 'Review contributor APYD terbesar, migrasi Kol 2 ke Kol 3-5, dan validitas agunan akun besar.',
            ];
        }

        if ($last !== null && (float) ($last['apyd_delta'] ?? 0) > 0) {
            $items[] = [
                'severity' => (float) ($last['apyd_ratio'] ?? 0) >= 7.5 ? 'danger' : 'warning',
                'title' => 'APYD Meningkat',
                'metric' => 'APYD MoM',
                'value' => (float) ($last['apyd_delta'] ?? 0),
                'message' => 'APYD naik dibanding bulan sebelumnya, artinya portofolio yang diberi bobot risiko meningkat.',
                'action' => 'Tetapkan daftar kerja remedial untuk debitur Kol 3-5 dan pantau aging tunggakan mingguan.',
            ];
        }

        if ((float) ($summary['ppap_system_vs_wd_gap'] ?? 0) < 0) {
            $items[] = [
                'severity' => 'danger',
                'title' => 'PPKA Sistem di Bawah PPKA WD',
                'metric' => 'Gap Sistem vs WD',
                'value' => (float) ($summary['ppap_system_vs_wd_gap'] ?? 0),
                'message' => 'PPKA sistem belum menutup PPKA wajib dibentuk secara prudential pada posisi bulan aktif.',
                'action' => 'Lakukan rekonsiliasi kontrak shortfall, validasi agunan, dan siapkan adjustment cadangan bila diperlukan.',
            ];
        }

        if ((bool) ($summary['ppap_rekap_previous_available'] ?? false) && abs((float) ($summary['ppap_gap'] ?? 0)) > 0) {
            $items[] = [
                'severity' => (float) ($summary['ppap_gap'] ?? 0) > 0 ? 'warning' : 'safe',
                'title' => (float) ($summary['ppap_gap'] ?? 0) > 0 ? 'PPKA Bulanan Meningkat' : 'PPKA Bulanan Menurun',
                'metric' => 'Gap PPKA Bulanan',
                'value' => (float) ($summary['ppap_gap'] ?? 0),
                'message' => (float) ($summary['ppap_gap'] ?? 0) > 0
                    ? 'Kebutuhan PPKA template bulan berjalan lebih tinggi dibanding snapshot bulan sebelumnya.'
                    : 'Kebutuhan PPKA template bulan berjalan lebih rendah dibanding snapshot bulan sebelumnya.',
                'action' => (float) ($summary['ppap_gap'] ?? 0) > 0
                    ? 'Validasi kontrak penyumbang kenaikan, perubahan kolektibilitas, dan nilai likuidasi agunan.'
                    : 'Pastikan penurunan didukung pelunasan, perbaikan kolektibilitas, atau update agunan yang terdokumentasi.',
            ];
        }

        if ((float) ($summary['net_exposure_agunan'] ?? 0) > 0 && (float) ($summary['net_exposure_ratio'] ?? 0) >= 25) {
            $items[] = [
                'severity' => (float) ($summary['net_exposure_ratio'] ?? 0) >= 50 ? 'danger' : 'warning',
                'title' => 'Net Exposure Setelah Agunan Tinggi',
                'metric' => 'Net Exposure Ratio',
                'value' => (float) ($summary['net_exposure_ratio'] ?? 0),
                'message' => 'Jumlah setelah agunan masih besar terhadap Aktiva Produktif, sehingga kualitas agunan dan pengikatan perlu dipastikan.',
                'action' => 'Prioritaskan update nilai taksasi, status pengikatan, dan kelengkapan dokumen agunan debitur eksposur besar.',
            ];
        }

        if ((float) ($summary['antar_bank_aktiva_macet'] ?? 0) > 0 || (float) ($summary['antar_bank_aktiva_unmapped'] ?? 0) > 0) {
            $items[] = [
                'severity' => 'warning',
                'title' => 'Anomali Klasifikasi ABA',
                'metric' => 'ABA',
                'value' => (float) ($summary['antar_bank_aktiva_macet'] ?? 0) + (float) ($summary['antar_bank_aktiva_unmapped'] ?? 0),
                'message' => 'Terdapat ABA macet atau belum terpetakan kolektibilitasnya dari subledger.',
                'action' => 'Rekonsiliasi MGL akun 50113 dengan TOFABA dan lengkapi mapping kolektibilitas per rekening.',
            ];
        }

        if (empty($items)) {
            $items[] = [
                'severity' => 'safe',
                'title' => 'Tidak Ada Anomali Material',
                'metric' => 'Prudential Check',
                'value' => 0,
                'message' => 'Tidak terdeteksi tekanan material dari KAP, APYD, PPKA gap, net exposure, maupun ABA pada filter aktif.',
                'action' => 'Pertahankan monitoring bulanan dan gunakan trend sebagai baseline early warning periode berikutnya.',
            ];
        }

        $severityRank = ['danger' => 3, 'warning' => 2, 'safe' => 1];
        usort($items, fn ($a, $b) => ($severityRank[$b['severity']] ?? 0) <=> ($severityRank[$a['severity']] ?? 0));

        return [
            'items' => $items,
            'summary' => [
                'danger_count' => count(array_filter($items, fn ($item) => ($item['severity'] ?? '') === 'danger')),
                'warning_count' => count(array_filter($items, fn ($item) => ($item['severity'] ?? '') === 'warning')),
                'safe_count' => count(array_filter($items, fn ($item) => ($item['severity'] ?? '') === 'safe')),
                'available_trend_months' => count($availableTrend),
                'missing_trend_months' => (int) ($trendMeta['missing_months'] ?? 0),
            ],
        ];
    }

    /**
     * Model CKPN berbasis rujukan PSAK 414/CKPN Best Practice.
     *
     * Prinsip:
     * - Produk yang dihitung: Murabahah, Qard, Multijasa/Hawalah sebagai piutang.
     * - Produk yang dikecualikan: Mudharabah, Musyarakah/MMQ karena bukan debt-type financial asset.
     * - EAD = Outstanding pokok - margin ditangguhkan.
     * - Individual = debitur rank 25 besar dan minimal Kol 2 (indikasi penurunan nilai/SICR).
     * - CKPN Individual = max(EAD - agunan neto setelah biaya penjualan 5%, 0).
     * - CKPN Kolektif = PD x LGD x EAD kolektif.
     *
     * @param  list<mixed>  $mainBindings
     * @return array<string,mixed>
     */
    private function getCkpnModelAnalytics(
        string $tableName,
        bool $isHistoris,
        string $mainFilter,
        array $mainBindings,
        int $reqTahun,
        int $reqBulan
    ): array {
        $eligibleProductCondition = "
            (
                p.kdcol IN ('10', '30')
                OR UPPER(ISNULL(p.ket, '')) LIKE '%MURABAHAH%'
                OR UPPER(ISNULL(p.ket, '')) LIKE '%QORD%'
                OR UPPER(ISNULL(p.ket, '')) LIKE '%QARD%'
                OR UPPER(ISNULL(p.ket, '')) LIKE '%MULTIJASA%'
                OR UPPER(ISNULL(p.ket, '')) LIKE '%HAWALAH%'
                OR UPPER(ISNULL(p.ket, '')) LIKE '%ISTISHNA%'
            )
            AND UPPER(ISNULL(p.ket, '')) NOT LIKE '%MUSYARAKAH%'
            AND UPPER(ISNULL(p.ket, '')) NOT LIKE '%MUDHARABAH%'
        ";

        $period = sprintf('%04d%02d', $reqTahun, $reqBulan);
        $collateralCte = $isHistoris
            ? "
                SELECT
                    a.nokontrak,
                    CAST(ISNULL(a.htgagun, 0) AS DECIMAL(38,6)) * 0.95 AS collateral_net,
                    CAST(ISNULL(a.htgagun, 0) AS DECIMAL(38,6)) AS collateral_gross
                FROM {$tableName} a
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            "
            : "
                SELECT
                    j.nokontrak,
                    SUM(
                        CASE
                            WHEN sj.stsbobotjam = 'H' THEN CAST(ISNULL(j.nilaiagunbi, 0) AS DECIMAL(38,6)) * (CAST(ISNULL(sj.bobotjam, 0) AS DECIMAL(18,6)) / 100.0)
                            WHEN sj.stsbobotjam = 'P' THEN CAST(ISNULL(j.nompasar, 0) AS DECIMAL(38,6)) * (CAST(ISNULL(sj.bobotjam, 0) AS DECIMAL(18,6)) / 100.0)
                            WHEN sj.stsbobotjam = 'T' THEN CAST(ISNULL(j.nomtaksasi, 0) AS DECIMAL(38,6)) * (CAST(ISNULL(sj.bobotjam, 0) AS DECIMAL(18,6)) / 100.0)
                            ELSE CAST(ISNULL(j.nilaiagunbi, 0) AS DECIMAL(38,6))
                        END
                    ) * 0.95 AS collateral_net,
                    SUM(CAST(ISNULL(j.nilaiagunbi, 0) AS DECIMAL(38,6))) AS collateral_gross
                FROM TOFJAMIN j
                LEFT JOIN SETUPJAM sj ON j.jnsjamin = sj.kdjam
                WHERE j.stsrec = 'A'
                GROUP BY j.nokontrak
            ";
        $bindingsWithCollateral = $isHistoris ? array_merge($mainBindings, $mainBindings) : $mainBindings;

        $currentCkpnRows = DB::connection($this->connection)->select("
            WITH collateral AS ({$collateralCte}),
            base AS (
                SELECT
                    a.nokontrak,
                    a.nocif,
                    LTRIM(RTRIM(a.nama)) AS nama,
                    a.kdprd,
                    ISNULL(p.ket, 'Tanpa Produk') AS produk,
                    ISNULL(p.kdcol, '') AS kdcol,
                    a.colbaru,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok,
                    ISNULL(CAST(a.osmgnc AS DECIMAL(38,6)), 0) AS margin_ditangguhkan,
                    CASE
                        WHEN ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) - ISNULL(CAST(a.osmgnc AS DECIMAL(38,6)), 0) > 0
                        THEN ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) - ISNULL(CAST(a.osmgnc AS DECIMAL(38,6)), 0)
                        ELSE 0
                    END AS ead,
                    ISNULL(CAST(a.ppap AS DECIMAL(38,6)), 0) AS ppap_system,
                    ISNULL(CAST(a.ckpn AS DECIMAL(38,6)), 0) AS ckpn_system,
                    ISNULL(c.collateral_gross, ISNULL(CAST(a.htgagun AS DECIMAL(38,6)), 0)) AS collateral_gross,
                    ISNULL(c.collateral_net, ISNULL(CAST(a.htgagun AS DECIMAL(38,6)), 0) * 0.95) AS collateral_net,
                    CASE WHEN {$eligibleProductCondition} THEN 1 ELSE 0 END AS is_eligible,
                    ROW_NUMBER() OVER (ORDER BY ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) DESC) AS exposure_rank
                FROM {$tableName} a
                LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                LEFT JOIN collateral c ON a.nokontrak = c.nokontrak
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            ),
            classified AS (
                SELECT *,
                    CASE
                        WHEN is_eligible = 1 AND exposure_rank <= 25 AND colbaru IN ('2','3','4','5') THEN 'individual'
                        WHEN is_eligible = 1 THEN 'collective'
                        ELSE 'excluded'
                    END AS ckpn_bucket,
                    CASE
                        WHEN is_eligible = 1 AND exposure_rank <= 25 AND colbaru IN ('2','3','4','5')
                        THEN CASE WHEN ead - collateral_net > 0 THEN ead - collateral_net ELSE 0 END
                        ELSE 0
                    END AS individual_ckpn
                FROM base
            )
            SELECT
                SUM(CASE WHEN is_eligible = 1 THEN os_pokok ELSE 0 END) AS eligible_os,
                SUM(CASE WHEN is_eligible = 0 THEN os_pokok ELSE 0 END) AS excluded_os,
                SUM(CASE WHEN is_eligible = 1 THEN ead ELSE 0 END) AS eligible_ead,
                SUM(CASE WHEN ckpn_bucket = 'individual' THEN ead ELSE 0 END) AS individual_ead,
                SUM(CASE WHEN ckpn_bucket = 'collective' THEN ead ELSE 0 END) AS collective_ead,
                SUM(individual_ckpn) AS individual_ckpn,
                SUM(CASE WHEN is_eligible = 1 THEN ppap_system ELSE 0 END) AS eligible_ppap_system,
                SUM(ppap_system) AS total_ppap_system,
                SUM(ckpn_system) AS total_ckpn_system,
                SUM(CASE WHEN is_eligible = 1 THEN 1 ELSE 0 END) AS eligible_noa,
                SUM(CASE WHEN is_eligible = 0 THEN 1 ELSE 0 END) AS excluded_noa,
                SUM(CASE WHEN ckpn_bucket = 'individual' THEN 1 ELSE 0 END) AS individual_noa,
                SUM(CASE WHEN ckpn_bucket = 'collective' THEN 1 ELSE 0 END) AS collective_noa,
                SUM(CASE WHEN is_eligible = 1 THEN CASE WHEN ead - collateral_net > 0 THEN ead - collateral_net ELSE 0 END ELSE 0 END) AS collateral_shortfall,
                SUM(CASE WHEN is_eligible = 1 THEN collateral_net ELSE 0 END) AS eligible_collateral_net
            FROM classified
        ", $bindingsWithCollateral);

        $current = $currentCkpnRows[0] ?? (object) [];

        $lgdCollateralShortfall = (float) ($current->eligible_ead ?? 0) > 0
            ? ((float) ($current->collateral_shortfall ?? 0) / (float) ($current->eligible_ead ?? 0))
            : 0.0;

        $pdRows = DB::connection($this->connection)->select("
            WITH hist AS (
                SELECT
                    periode,
                    nokontrak,
                    kdprd,
                    colbaru,
                    CAST(osmdlc AS DECIMAL(38,6)) AS osmdlc,
                    LAG(colbaru) OVER (PARTITION BY nokontrak ORDER BY periode) AS prev_col,
                    LAG(CAST(osmdlc AS DECIMAL(38,6))) OVER (PARTITION BY nokontrak ORDER BY periode) AS prev_os
                FROM TOFLMBEOM
                WHERE periode <= ?
                  AND periode >= CONVERT(VARCHAR(6), DATEADD(MONTH, -12, CONVERT(date, ? + '01')), 112)
                  AND stsrec IN ('A', 'N') AND stsacc <> 'W'
                  AND kdprd IN ('50','51','56','58','59','64')
            ),
            paired AS (
                SELECT * FROM hist WHERE prev_col IS NOT NULL AND prev_os IS NOT NULL AND prev_os > 0
            )
            SELECT
                CASE WHEN SUM(CASE WHEN prev_col IN ('1','2') THEN prev_os ELSE 0 END) > 0
                    THEN SUM(CASE WHEN prev_col IN ('1','2') AND colbaru IN ('3','4','5') THEN osmdlc ELSE 0 END)
                         / NULLIF(SUM(CASE WHEN prev_col IN ('1','2') THEN prev_os ELSE 0 END), 0)
                    ELSE 0 END AS pd_net_flow,
                CASE WHEN SUM(CASE WHEN prev_col IN ('1','2','3','4') THEN prev_os ELSE 0 END) > 0
                    THEN SUM(CASE WHEN prev_col IN ('1','2','3','4') AND colbaru = '5' THEN osmdlc ELSE 0 END)
                         / NULLIF(SUM(CASE WHEN prev_col IN ('1','2','3','4') THEN prev_os ELSE 0 END), 0)
                    ELSE 0 END AS pd_migration,
                COUNT(*) AS transition_count,
                MIN(periode) AS observation_start,
                MAX(periode) AS observation_end
            FROM paired
        ", [$period, $period]);

        $pd = $pdRows[0] ?? (object) [];
        $pdNetFlow = (float) ($pd->pd_net_flow ?? 0);
        $pdMigration = (float) ($pd->pd_migration ?? 0);
        $selectedPd = max($pdNetFlow, $pdMigration);

        $collectiveCkpn = (float) ($current->collective_ead ?? 0) * $selectedPd * $lgdCollateralShortfall;
        $modelCkpn = (float) ($current->individual_ckpn ?? 0) + $collectiveCkpn;
        $systemBaseline = (float) ($current->eligible_ppap_system ?? 0);

        $stageRows = DB::connection($this->connection)->select("
            WITH base AS (
                SELECT
                    CASE
                        WHEN a.colbaru = '1' THEN 'Stage 1'
                        WHEN a.colbaru = '2' THEN 'Stage 2'
                        WHEN a.colbaru IN ('3','4','5') THEN 'Stage 3'
                        ELSE 'Unmapped'
                    END AS stage_name,
                    a.colbaru,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok,
                    CASE
                        WHEN ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) - ISNULL(CAST(a.osmgnc AS DECIMAL(38,6)), 0) > 0
                        THEN ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) - ISNULL(CAST(a.osmgnc AS DECIMAL(38,6)), 0)
                        ELSE 0
                    END AS ead,
                    ISNULL(CAST(a.ppap AS DECIMAL(38,6)), 0) AS ppap_system
                FROM {$tableName} a
                LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
                  AND {$eligibleProductCondition}
            )
            SELECT stage_name, colbaru, COUNT(*) AS noa, SUM(os_pokok) AS os_pokok, SUM(ead) AS ead, SUM(ppap_system) AS ppap_system
            FROM base
            GROUP BY stage_name, colbaru
            ORDER BY colbaru
        ", $mainBindings);

        $individualRows = DB::connection($this->connection)->select("
            WITH collateral AS ({$collateralCte}),
            base AS (
                SELECT
                    a.nokontrak, a.nocif, LTRIM(RTRIM(a.nama)) AS nama, a.colbaru,
                    ISNULL(p.ket, 'Tanpa Produk') AS produk,
                    ISNULL(cab.nama, '(Tanpa Cabang)') AS cabang,
                    ISNULL(ao.nmao, '(Tanpa AO)') AS nama_ao,
                    ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) AS os_pokok,
                    ISNULL(CAST(a.osmgnc AS DECIMAL(38,6)), 0) AS margin_ditangguhkan,
                    CASE
                        WHEN ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) - ISNULL(CAST(a.osmgnc AS DECIMAL(38,6)), 0) > 0
                        THEN ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) - ISNULL(CAST(a.osmgnc AS DECIMAL(38,6)), 0)
                        ELSE 0
                    END AS ead,
                    ISNULL(c.collateral_net, ISNULL(CAST(a.htgagun AS DECIMAL(38,6)), 0) * 0.95) AS collateral_net,
                    ISNULL(CAST(a.ppap AS DECIMAL(38,6)), 0) AS ppap_system,
                    CASE WHEN {$eligibleProductCondition} THEN 1 ELSE 0 END AS is_eligible,
                    ROW_NUMBER() OVER (ORDER BY ISNULL(CAST(a.osmdlc AS DECIMAL(38,6)), 0) DESC) AS exposure_rank
                FROM {$tableName} a
                LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
                LEFT JOIN collateral c ON a.nokontrak = c.nokontrak
                LEFT JOIN CABANG cab ON a.kdloc = cab.kdloc
                LEFT JOIN AO ao ON a.kdaoh = ao.kdao
                WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            )
            SELECT TOP 25 *,
                CASE WHEN ead - collateral_net > 0 THEN ead - collateral_net ELSE 0 END AS ckpn_model
            FROM base
            WHERE exposure_rank <= 25
              AND colbaru IN ('2','3','4','5')
              AND is_eligible = 1
            ORDER BY exposure_rank
        ", $bindingsWithCollateral);

        $productRows = DB::connection($this->connection)->select("
            SELECT
                ISNULL(p.ket, 'Tanpa Produk') AS produk,
                CASE WHEN {$eligibleProductCondition} THEN 'eligible' ELSE 'excluded' END AS ckpn_scope,
                COUNT(*) AS noa,
                SUM(CAST(a.osmdlc AS DECIMAL(38,6))) AS os_pokok,
                SUM(CAST(a.ppap AS DECIMAL(38,6))) AS ppap_system
            FROM {$tableName} a
            LEFT JOIN SETUPLOAN p ON a.kdprd = p.kdprd
            WHERE a.stsrec IN ('A', 'N') AND a.stsacc <> 'W' {$mainFilter}
            GROUP BY p.ket, p.kdcol
            ORDER BY ckpn_scope, os_pokok DESC
        ", $mainBindings);

        return [
            'methodology' => [
                'standard' => 'PSAK 414 / CKPN Best Practice — debt-type syariah financial assets',
                'ead_formula' => 'EAD = Outstanding Pokok - Margin Ditangguhkan',
                'individual_rule' => 'Rank 25 besar dan Kol 2-5',
                'individual_formula' => 'max(EAD - Agunan Neto, 0)',
                'collective_formula' => 'PD x LGD x EAD Kolektif',
                'selling_cost_pct' => 5,
                'eligible_products' => 'Murabahah, Qard/Qord, Piutang Multijasa/Hawalah, Istishna',
                'excluded_products' => 'Mudharabah, Musyarakah/MMQ, Ijarah non-piutang',
            ],
            'parameters' => [
                'pd_net_flow' => $pdNetFlow * 100,
                'pd_migration' => $pdMigration * 100,
                'selected_pd' => $selectedPd * 100,
                'lgd_collateral_shortfall' => $lgdCollateralShortfall * 100,
                'weighted_lgd' => $lgdCollateralShortfall * 100,
                'transition_count' => (int) ($pd->transition_count ?? 0),
                'observation_start' => $pd->observation_start ?? null,
                'observation_end' => $pd->observation_end ?? null,
            ],
            'summary' => [
                'eligible_noa' => (int) ($current->eligible_noa ?? 0),
                'excluded_noa' => (int) ($current->excluded_noa ?? 0),
                'individual_noa' => (int) ($current->individual_noa ?? 0),
                'collective_noa' => (int) ($current->collective_noa ?? 0),
                'eligible_os' => (float) ($current->eligible_os ?? 0),
                'excluded_os' => (float) ($current->excluded_os ?? 0),
                'eligible_ead' => (float) ($current->eligible_ead ?? 0),
                'individual_ead' => (float) ($current->individual_ead ?? 0),
                'collective_ead' => (float) ($current->collective_ead ?? 0),
                'individual_ckpn' => (float) ($current->individual_ckpn ?? 0),
                'collective_ckpn' => $collectiveCkpn,
                'model_ckpn' => $modelCkpn,
                'system_ppap' => $systemBaseline,
                'total_ppap_system' => (float) ($current->total_ppap_system ?? 0),
                'gap_vs_system' => $modelCkpn - $systemBaseline,
                'coverage_model_to_ead' => (float) ($current->eligible_ead ?? 0) > 0 ? ($modelCkpn / (float) ($current->eligible_ead ?? 0)) * 100 : 0,
                'system_coverage_to_ead' => (float) ($current->eligible_ead ?? 0) > 0 ? ($systemBaseline / (float) ($current->eligible_ead ?? 0)) * 100 : 0,
                'eligible_collateral_net' => (float) ($current->eligible_collateral_net ?? 0),
                'collateral_shortfall' => (float) ($current->collateral_shortfall ?? 0),
            ],
            'stage_breakdown' => $stageRows,
            'individual_debtors' => $individualRows,
            'product_scope' => $productRows,
        ];
    }

    /**
     * Rasio FDR berbasis komponen pembiayaan, DPK, modal inti, dan kewajiban bank lain:
     * - FDR    = Total Pembiayaan / (DPK + Modal Inti + Kewajiban Bank Lain)
     * - FDR v2 = Total Pembiayaan / DPK
     *
     * Mapping utama:
     * - Total Pembiayaan: TOFLMB/TOFLMBEOM.osmdlc aktif.
     * - DPK: Tabungan Wadiah + Tabungan Mudharabah + Deposito Mudharabah.
     * - Kewajiban Bank Lain: KBL + KBL Tab Mudharabah + KBL Dep <= 3 Bulan + KBL Dep > 3 Bulan.
     * - Modal Inti: modal dasar/cadangan + laba tahun lalu + laba berjalan yang diperhitungkan - AYDA.
     *
     * @return array{fdr: float, fdr_v2: float, components: array<string, float>}
     */
    private function getTksFdrMetrics(string $tableName, ?string $periode): array
    {
        $periodeFilter = '';
        $bindings = [];

        if ($periode !== null) {
            $periodeFilter = ' AND periode = ?';
            $bindings[] = $periode;
        }

        $rows = DB::connection($this->connection)->select("
            WITH pembiayaan AS (
                SELECT SUM(CAST(osmdlc AS DECIMAL(38,6))) AS total_pembiayaan
                FROM {$tableName}
                WHERE stsrec IN ('A', 'N') AND stsacc <> 'W' {$periodeFilter}
            ),
            gl AS (
                SELECT
                    SUM(CASE
                        WHEN nobb IN ('5012200000', '5012310000')
                        THEN CAST(sahirrp AS DECIMAL(38,6)) ELSE 0
                    END) AS tabungan,
                    SUM(CASE
                        WHEN nobb = '5012320000'
                        THEN CAST(sahirrp AS DECIMAL(38,6)) ELSE 0
                    END) AS deposito,
                    SUM(CASE
                        WHEN nobb IN ('5012500000', '5012510000', '5012521000', '5012522000')
                        THEN CAST(sahirrp AS DECIMAL(38,6)) ELSE 0
                    END) AS kewajiban_bank_lain,
                    SUM(CASE
                        WHEN nosbb IN ('5013100001', '5013100002', '5013100003', '5013100005', '5013100006', '5013300001', '5013300002')
                        THEN CAST(sahirrp AS DECIMAL(38,6)) ELSE 0
                    END) AS modal_dasar_dan_cadangan,
                    SUM(CASE
                        WHEN nobb = '5013510000'
                        THEN CAST(sahirrp AS DECIMAL(38,6)) ELSE 0
                    END) AS laba_tahun_lalu,
                    SUM(CASE
                        WHEN nobb = '5013520000'
                        THEN CAST(sahirrp AS DECIMAL(38,6)) ELSE 0
                    END) AS laba_tahun_berjalan,
                    SUM(CASE
                        WHEN nmsbb LIKE '%AYDA%'
                        THEN CAST(sahirrp AS DECIMAL(38,6)) ELSE 0
                    END) AS ayda_pengurang
                FROM MGL
            ),
            components AS (
                SELECT
                    p.total_pembiayaan,
                    g.tabungan,
                    g.deposito,
                    (g.tabungan + g.deposito) AS dpk,
                    g.kewajiban_bank_lain,
                    g.ayda_pengurang,
                    (
                        g.modal_dasar_dan_cadangan
                        + g.laba_tahun_lalu
                        + CASE
                            WHEN g.laba_tahun_berjalan > 0 THEN g.laba_tahun_berjalan * 0.5
                            ELSE g.laba_tahun_berjalan
                          END
                        - g.ayda_pengurang
                    ) AS modal_inti
                FROM pembiayaan p CROSS JOIN gl g
            )
            SELECT
                total_pembiayaan,
                tabungan,
                deposito,
                dpk,
                kewajiban_bank_lain,
                ayda_pengurang,
                modal_inti,
                CASE
                    WHEN (dpk + modal_inti + kewajiban_bank_lain) <> 0
                    THEN total_pembiayaan / NULLIF(dpk + modal_inti + kewajiban_bank_lain, 0) * 100
                    ELSE 0
                END AS fdr,
                CASE
                    WHEN dpk <> 0 THEN total_pembiayaan / NULLIF(dpk, 0) * 100
                    ELSE 0
                END AS fdr_v2
            FROM components
        ", $bindings);

        $row = $rows[0] ?? null;

        return [
            'fdr' => (float) ($row->fdr ?? 0),
            'fdr_v2' => (float) ($row->fdr_v2 ?? 0),
            'components' => [
                'total_pembiayaan' => (float) ($row->total_pembiayaan ?? 0),
                'tabungan' => (float) ($row->tabungan ?? 0),
                'deposito' => (float) ($row->deposito ?? 0),
                'dpk' => (float) ($row->dpk ?? 0),
                'kewajiban_bank_lain' => (float) ($row->kewajiban_bank_lain ?? 0),
                'ayda_pengurang' => (float) ($row->ayda_pengurang ?? 0),
                'modal_inti' => (float) ($row->modal_inti ?? 0),
            ],
        ];
    }

    /**
     * Dapatkan daftar pembiayaan yang sudah atau akan jatuh tempo (bulan ini / lewat).
     */
    public function getJatuhTempo(array $filters = [], int $perPage = 50): Paginator
    {
        $query = Toflmb::query()
            ->with([
                'ao:kdao,nmao',
                'cif:nocif,tgllhr,alamat,hp',
                'cabang:kdloc,nama',
            ])
            ->whereIn('stsrec', ['A', 'N'])
            ->where('stsacc', '<>', 'W')
            // Filter: Jatuh tempo <= Akhir bulan ini
            ->whereRaw('tglexp <= EOMONTH(GETDATE())')
            ->whereRaw("tglexp > '1900-01-01'");

        if (! empty($filters['cabang'])) {
            $query->where('kdloc', $filters['cabang']);
        }

        if (! empty($filters['ao'])) {
            $query->where('kdaoh', $filters['ao']);
        }

        // Urutkan dari yang paling lama menunggak / lewat waktu
        $query->orderBy('tglexp', 'asc')
            ->orderBy('nokontrak', 'asc');

        // H5 Fix: Gunakan paginate() untuk fetch all (bypass 500 limit cursorPaginate)
        if ($perPage >= 100000) {
            return $query->paginate($perPage);
        }

        return $query->cursorPaginate($perPage);
    }
}
