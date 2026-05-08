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
            ->where('a.stsrec', 'A')
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
     * Dapatkan rincian angsuran per kontrak.
     */
    public function getDetailAngsuran(string $nokontrak): array
    {
        // 1. Ambil Header Pembiayaan
        $header = Toflmb::query()
            ->with(['cif:nocif,alamat', 'produk:kdprd,ket', 'tabunganPokok:notab,saldoblok,sahirrp'])
            ->where('nokontrak', $nokontrak)
            ->where('stsrec', 'A')
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
        $cacheKey = "financing_rekapitulasi_{$groupBy}";

        return Cache::remember($cacheKey, 60, function () use ($groupBy) {
            $query = Toflmb::query()
                ->where('TOFLMB.stsrec', 'A')
                ->where('TOFLMB.stsacc', '<>', 'W');

            // Seleksi kolom agregat standar
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

            // Tentukan Join dan Grouping secara cerdas
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

            return $query->get();
        });
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
                WHERE a.stsrec = 'A'
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
                $totals['npf_ratio'] = round(($totals['npf_os'] / $totals['total_os']) * 100, 2);
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
     * Analisis Kualitas Aset & Risiko (Aging, Risk Concentration, Coverage).
     * Single-hit analytics untuk dashboard Quality & Risk.
     */
    public function getQualityAnalytics(string $groupBy = 'cabang', string $cabang = ''): array
    {
        $validGroups = ['cabang', 'produk', 'ao'];
        if (! in_array($groupBy, $validGroups, true)) {
            $groupBy = 'cabang';
        }

        $cacheKey = "financing:quality_analytics:{$groupBy}:".($cabang ?: 'all');
        $start    = microtime(true);
        $memory   = memory_get_usage(true);

        $data = Cache::remember($cacheKey, 60, function () use ($groupBy, $cabang): array {
            $dimConfig = $this->resolveDimensionConfig($groupBy);
            $joinClause = $dimConfig['join'];
            $labelSelect = $dimConfig['label'];
            $groupByClause = $dimConfig['group_by'];

            $cabangFilter = '';
            $bindings = [];
            if ($cabang !== '') {
                $cabangFilter = "AND a.kdloc = ?";
                $bindings[] = $cabang;
            }

            // 1. Aging Buckets Aggregation
            $sqlAging = "
                SELECT 
                    {$labelSelect} AS label,
                    SUM(CASE WHEN a.haritgk = 0 THEN a.osmdlc ELSE 0 END) AS aging_0,
                    SUM(CASE WHEN a.haritgk BETWEEN 1 AND 30 THEN a.osmdlc ELSE 0 END) AS aging_1_30,
                    SUM(CASE WHEN a.haritgk BETWEEN 31 AND 60 THEN a.osmdlc ELSE 0 END) AS aging_31_60,
                    SUM(CASE WHEN a.haritgk BETWEEN 61 AND 90 THEN a.osmdlc ELSE 0 END) AS aging_61_90,
                    SUM(CASE WHEN a.haritgk > 90 THEN a.osmdlc ELSE 0 END) AS aging_npf,
                    SUM(a.osmdlc) AS total_os
                FROM TOFLMB a
                {$joinClause}
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {$cabangFilter}
                GROUP BY {$groupByClause}
                HAVING SUM(a.osmdlc) > 0
                ORDER BY total_os DESC
            ";

            // 2. Risk Concentration (Sektor Ekonomi)
            $sqlSektor = "
                SELECT 
                    ISNULL(a.sekon, 'Lainnya') AS label,
                    SUM(a.osmdlc) AS total_os,
                    SUM(CASE WHEN a.colbaru IN ('3','4','5') THEN a.osmdlc ELSE 0 END) AS npf_os
                FROM TOFLMB a
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' {$cabangFilter}
                GROUP BY a.sekon
                ORDER BY total_os DESC
            ";

            // 3. Top 10 Risk Alert (Nasabah NPF / Tunggakan Tinggi)
            $sqlAlert = "
                SELECT TOP 10
                    a.nokontrak, a.nama, a.osmdlc, a.tgkmdl, a.haritgk, a.colbaru as coll
                FROM TOFLMB a
                WHERE a.stsrec = 'A' AND a.stsacc <> 'W' AND a.colbaru IN ('2','3','4','5')
                {$cabangFilter}
                ORDER BY a.tgkmdl DESC
            ";

            $agingRows = DB::connection($this->connection)->select($sqlAging, $bindings);
            $sektorRows = DB::connection($this->connection)->select($sqlSektor, $bindings);
            $alertRows = DB::connection($this->connection)->select($sqlAlert, $bindings);

            // Calculate Global Metrics
            $totalOS = collect($agingRows)->sum('total_os');
            $totalNPF = collect($sektorRows)->sum('npf_os');
            $totalPPAP = DB::connection($this->connection)->table('TOFLMB')
                ->where('stsrec', 'A')->where('stsacc', '<>', 'W')
                ->when($cabang !== '', fn($q) => $query->where('kdloc', $cabang))
                ->sum('ppap');

            return [
                'aging' => $agingRows,
                'sektor' => $sektorRows,
                'alerts' => $alertRows,
                'summary' => [
                    'total_os' => (float) $totalOS,
                    'total_npf' => (float) $totalNPF,
                    'total_ppap' => (float) $totalPPAP,
                    'npf_ratio' => $totalOS > 0 ? round(($totalNPF / $totalOS) * 100, 2) : 0,
                    'coverage_ratio' => $totalNPF > 0 ? round(($totalPPAP / $totalNPF) * 100, 2) : 0,
                ]
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
            ->where('stsrec', 'A')
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
