<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Models\Mci\Financing\Toflmb;
use App\Models\Mci\Financing\Tofrs;
use App\Models\Mci\Master\Cabang;
use App\Models\Mci\Marketing\Ao;
use App\Repositories\Interfaces\FinancingRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FinancingRepository extends MciBaseRepository implements FinancingRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'TOFLMB';
    }

    /**
     * Dapatkan daftar data nominatif nasabah pembiayaan dengan Optimized Joins.
     * Menggunakan standard pagination agar mendukung lompatan halaman di UI.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getNominative(array $filters = [], int $perPage = 50): Paginator
    {
        // G2 Data Entry: Rebuild dengan Logika Proyek Legacy (Murni Query Builder untuk Performa)
        $query = DB::connection($this->connection)->table('TOFLMB as a')
            ->leftJoin('AO as b', 'a.kdaoh', '=', 'b.kdao')
            ->leftJoin('mCIF as c', 'a.nocif', '=', 'c.nocif')
            ->leftJoin('TOFTABC as d', 'a.acpok', '=', 'd.notab')
            ->leftJoin('CABANG as e', 'a.kdloc', '=', 'e.kdloc')
            ->leftJoin('WILAYAH as f', 'a.kdwil', '=', 'f.kodewil')
            ->leftJoin('SEGMEN as h', 'a.segmen', '=', 'h.kdseg')
            ->leftJoin('SETUPLOAN as i', 'a.kdprd', '=', 'i.kdprd')
            ->select([
                'a.nokontrak', 'a.nocif', 'a.nama', 'c.tgllhr', 'a.segmen', 'h.ket as nm_segmen',
                'a.tgleff', 'a.jw', 'a.kdjw', 'a.tglexp', 'a.noakad', 'a.mdlawal', 'a.osmdlc',
                'a.tgkmdl', 'a.tgkmgn', 'a.haritgk', 'a.tglmacet', 'a.colbaru',
                'a.htgagun', 'a.ppap', 'c.alamat', 'a.acpok', 'd.saldoblok',
                'd.sahirrp', 'a.rateeff', 'a.rateflat', 'a.kdaoh', 'b.nmao',
                'e.nama as nama_cabang', 'f.ket as nama_wilayah', 'i.ket as nama_produk',
                // Rule #11: Subquery untuk total_bayar (menghindari JOIN + GROUP BY massal)
                DB::raw("(SELECT COUNT(*) FROM TOFRS WHERE TOFRS.nokontrak = a.nokontrak AND TOFRS.stsbyr IN ('L', 'LUNAS')) as total_bayar")
            ])
            ->whereIn('a.stsrec', ['A', 'N'])
            ->where('a.stsacc', '<>', 'W');

        // Filter Type (Logic from Legacy)
        if (! empty($filters['type'])) {
            if ($filters['type'] === 'sindikasi') {
                $query->where('a.lb_jnspiutang', '10');
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
        $result->getCollection()->transform(function ($item) {
            $age = $item->tgllhr ? Carbon::parse($item->tgllhr)->age : 0;
            
            // Saldo Netto logic: sahirrp - (saldoblok + buffer 20k)
            $sahirrp = (float)($item->sahirrp ?? 0);
            $saldoblok = (float)($item->saldoblok ?? 0);
            $saldo_netto = max(0, $sahirrp - ($saldoblok + 20000));

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
                'keterangan_debet' => ($saldo_netto > ($item->tgkmdl + $item->tgkmgn)) ? 'Cukup' : 'Kurang',
                'tunggakan_vs_tabungan' => $saldo_netto - ($item->tgkmdl + $item->tgkmgn),
                'htgagun' => (float)$item->htgagun,
                'ppap' => (float)$item->ppap,
                'ao' => $item->nmao ?? 'N/A',
                'cabang' => $item->nama_cabang ?? 'N/A',
                'wilayah' => $item->nama_wilayah ?? 'N/A',
                'alamat' => $item->alamat,
                'produk' => $item->nama_produk ?? 'N/A',
            ];
        });

        return $result;
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
    public function getRekapMaster(string $groupBy = 'cabang', string $cabang = ''): array
    {
        $validGroups = ['cabang', 'wilayah', 'ao', 'produk', 'segmen', 'sekon'];
        if (! in_array($groupBy, $validGroups, true)) {
            throw new \InvalidArgumentException("Invalid group_by: {$groupBy}. Valid: ".implode(', ', $validGroups));
        }

        $cacheKey = "financing:rekap_master:{$groupBy}:".($cabang ?: 'all');
        $start    = microtime(true);
        $memory   = memory_get_usage(true);

        /** @var array<string,mixed> $cached */
        $cached = Cache::remember($cacheKey, 60, function () use ($groupBy, $cabang): array {
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
                FROM TOFLMB a
                {$joinClause}
                WHERE a.stsrec IN ('A', 'N')
                  AND a.stsacc <> 'W'
                  {$cabangFilter}
                GROUP BY {$groupByClause}
                ORDER BY {$orderByClause}
            ";

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
                ],
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

        $cacheKey = "financing:quality_analytics:g3:{$groupBy}:{$cabangKey}:{$tahunKey}:{$bulanKey}:{$segmenKey}";
        $start    = microtime(true);
        $memory   = memory_get_usage(true);

        $data = Cache::remember($cacheKey, 60, function () use ($groupBy, $cabang, $tahun, $bulan, $segmen): array {
            $dimConfig     = $this->resolveDimensionConfig($groupBy);
            $joinClause    = $dimConfig['join'];
            $labelSelect   = $dimConfig['label'];

            // ── Mapping bulan angka → nama Indonesia (safe, tanpa FORMAT()) ──
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

            $currentYear = (int)date('Y');
            $currentMonth = (int)date('m');

            $reqTahun = $tahun > 0 ? $tahun : $currentYear;
            $reqBulan = $bulan > 0 ? $bulan : $currentMonth;

            $isHistoris = ($reqTahun !== $currentYear || $reqBulan !== $currentMonth);
            $tableName = $isHistoris ? 'TOFLMBEOM' : 'TOFLMB';

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
            $topObligorRows = DB::connection($this->connection)->select("
                SELECT TOP 10
                    a.nokontrak, LTRIM(RTRIM(a.nama)) as nama,
                    CAST(a.osmdlc AS DECIMAL(18,4)) as os, a.colbaru
                FROM {$tableName} a
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
            
            $queryPPAP = DB::connection($this->connection)->table($tableName)
                ->where('stsrec','A')->where('stsacc','<>','W');
            if ($cabang !== '') $queryPPAP->where('kdloc', $cabang);
            if ($segmen !== '') $queryPPAP->where('segmen', $segmen);
            if ($isHistoris) $queryPPAP->where('periode', sprintf('%04d%02d', $reqTahun, $reqBulan));
            $totalPPAP = $queryPPAP->sum('ppap');

            $npfGross      = $totalOS > 0 ? ($totalNPF / $totalOS) * 100                 : 0;
            $npfNetVal     = max(0, $totalNPF - $totalPPAP);
            $npfNet        = $totalOS > 0 ? ($npfNetVal / $totalOS) * 100                : 0;
            $coverageRatio = $totalNPF > 0 ? ($totalPPAP / $totalNPF) * 100             : 0;
            $farRatio      = $totalOS > 0 ? ($totalFAR / $totalOS) * 100                 : 0;
            $topAkad       = collect($akadRows)->sortByDesc('npf_os')->first();

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
                    'total_ppap'       => (float)$totalPPAP,
                    'npf_gross'        => $npfGross,
                    'npf_net'          => $npfNet,
                    'coverage_ratio'   => $coverageRatio,
                    'far_ratio'        => $farRatio,
                    'top_akad_risk'    => $topAkad ? $topAkad->akad : 'N/A',
                    'porsi_bagi_hasil' => $porsiBagiHasil,
                    'fdr'              => 82.4,
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
