<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Models\Mci\Financing\Toflmb;
use App\Models\Mci\Financing\Tofrs;
use App\Models\Mci\Marketing\Ao;
use App\Repositories\Interfaces\FinancingRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancingRepository extends MciBaseRepository implements FinancingRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'TOFLMB';
    }

    /**
     * Dapatkan daftar data nominatif nasabah pembiayaan dengan Eager Loading.
     * Tidak lagi melooping data di PHP untuk kalkulasi sisa jadwal.
     *
     * @param  array<string, mixed>  $filters
     * @param  int  $perPage
     * @return CursorPaginator
     */
    public function getNominative(array $filters = [], int $perPage = 50): CursorPaginator
    {
        $query = Toflmb::query()
            ->with([
                'ao:kdao,nmao',
                'cif:nocif,tgllhr,alamat',
                'tabunganPokok:notab,saldoblok,sahirrp',
                'cabang:kdloc,nama',
                'wilayah:kodewil,ket',
                'segmenPasar:kdseg,ket',
                'produk:kdprd,ket',
            ])
            ->where('stsrec', 'A')
            ->where('stsacc', '<>', 'W')
            ->select([
                'TOFLMB.*',
                // Raw SQL untuk menghitung total_bayar langsung di query tanpa N+1 join TOFRS massal
                DB::raw("(SELECT COUNT(*) FROM TOFRS WHERE TOFRS.nokontrak = TOFLMB.nokontrak AND TOFRS.stsbyr IN ('L', 'LUNAS')) as total_bayar")
            ]);

        // Filter: Pencarian Nama atau No Kontrak
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nokontrak', 'like', "%{$search}%");
            });
        }

        // Filter: Cabang
        if (!empty($filters['cabang'])) {
            $query->where('kdloc', $filters['cabang']);
        }

        // Filter: AO
        if (!empty($filters['ao'])) {
            $query->where('kdaoh', $filters['ao']);
        }

        // Urutkan default (Sama dengan lama: nmao asc, colbaru desc, nama asc)
        $query->orderBy('kdaoh', 'asc') // Sort by kdaoh for cursor safety
              ->orderBy('colbaru', 'desc')
              ->orderBy('nokontrak', 'asc'); // Primary/unique key at the end for cursor pagination

        return $query->cursorPaginate($perPage);
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
                'nama'        => ucwords(strtolower((string) $header->nama)),
                'nocif'       => $header->nocif,
                'nokontrak'   => $header->nokontrak,
                'noakad'      => $header->noakad,
                'alamat'      => optional($header->cif)->alamat,
                'produk'      => optional($header->produk)->ket,
                'colbaru'     => $header->colbaru,
                'tenor'       => $header->jw,
                'mdlawal'     => $header->mdlawal,
                'mgn_awal'    => $header->mgnawal,
                'osmdlc'      => $header->osmdlc,
                'osmgnc'      => $header->osmgnc,
                'saldo_netto' => $saldo_netto,
                'tglakadn'    => $this->formatSafeDate((string) $header->tglakadn),
                'tglexp'      => $this->formatSafeDate((string) $header->tglexp),
            ],
            'details' => $details->map(function ($item) {
                return [
                    'tgltagih'  => $this->formatSafeDate((string) $item->tgltagih),
                    'tagmdl'    => (float) $item->tagmdl,
                    'tagmgn'    => (float) $item->tagmgn,
                    'byrmdl'    => (float) $item->byrmdl,
                    'byrmgn'    => (float) $item->byrmgn,
                    'stsbyr'    => (trim((string) $item->stsbyr) === 'L' || trim((string) $item->stsbyr) === 'LUNAS') ? 'LUNAS' : '-',
                    'ratiobh'   => number_format((float) $item->ratiobh, 2),
                    'tglbyrmdl' => $this->formatSafeDate((string) $item->tglbyrmdl),
                    'tglbyrmgn' => $this->formatSafeDate((string) $item->tglbyrmgn),
                ];
            })->toArray()
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
            return \Carbon\Carbon::parse($date)->format('d M Y');
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Dapatkan data rekapitulasi pembiayaan secara dinamis berdasarkan group_by.
     * Menerapkan Rule #6: Cache Strategy (60 detik via Redis/File)
     */
    public function getRekapitulasi(string $groupBy): Collection
    {
        $cacheKey = "financing_rekapitulasi_{$groupBy}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($groupBy) {
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
                DB::raw('ROUND(AVG(TOFLMB.rateflat) / 12, 2) AS avg_rate')
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
                                        'TOFLMB.colbaru as id'
                                    ], $aggregates))
                                  ->groupBy('TOFLMB.colbaru')
                                  ->orderBy('TOFLMB.colbaru', 'asc'),

                default => throw new \InvalidArgumentException("Invalid group_by parameter")
            };

            return $query->get();
        });
    }

    /**
     * Dapatkan daftar pembiayaan yang sudah atau akan jatuh tempo (bulan ini / lewat).
     */
    public function getJatuhTempo(array $filters = [], int $perPage = 50): CursorPaginator
    {
        $query = Toflmb::query()
            ->with([
                'ao:kdao,nmao',
                'cif:nocif,tgllhr,alamat,notelp',
                'cabang:kdloc,nama',
            ])
            ->where('stsrec', 'A')
            ->where('stsacc', '<>', 'W')
            // Filter: Jatuh tempo <= Akhir bulan ini
            ->whereRaw("tglexp <= EOMONTH(GETDATE())")
            ->whereRaw("tglexp > '1900-01-01'");

        if (!empty($filters['cabang'])) {
            $query->where('kdloc', $filters['cabang']);
        }

        if (!empty($filters['ao'])) {
            $query->where('kdaoh', $filters['ao']);
        }

        // Urutkan dari yang paling lama menunggak / lewat waktu
        $query->orderBy('tglexp', 'asc')
              ->orderBy('nokontrak', 'asc');

        return $query->cursorPaginate($perPage);
    }
}
