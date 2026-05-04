<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * FinancingMonthlySnapshot Model
 *
 * Menyimpan data agregat pembiayaan per bulan untuk trend analysis.
 * Data berasal dari SQL Server CBS MCI yang di-aggregate via artisan command.
 *
 * @property int $id
 * @property string $tahun_bulan Format: YYYY-MM
 * @property string|null $source_db Source database name
 * @property string $kdseg Segmen (ALL = semua)
 * @property string $kdaoh AO code (ALL = semua)
 * @property string $kdprd Product code (ALL = semua)
 * @property string $kdloc Branch code (ALL = semua)
 * @property string $kdwil Wilayah code (ALL = semua)
 * @property string $colbarU Kolektibilitas (ALL = semua, 1-5 = spesifik)
 * @property int $total_noa Total Number of Accounts
 * @property float $total_osmdlc Total Outstanding Modal
 * @property float $total_osmgnc Total Outstanding Margin
 * @property float $total_os_total Total O/S
 * @property float $total_mdlawal Total Modal Awal
 * @property int $npf_noa NPF Number of Accounts
 * @property float $npf_osmdlc NPF Outstanding
 * @property float $npf_persen NPF Percentage
 * @property float $avg_kolek Average Kolektibilitas
 * @property float $avg_rate Average Interest Rate
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class FinancingMonthlySnapshot extends Model
{
    use HasFactory;

    protected $table = 'financing_monthly_snapshots';

    protected $fillable = [
        'tahun_bulan',
        'source_db',
        'kdseg',
        'kdaoh',
        'kdprd',
        'kdloc',
        'kdwil',
        'colbarU',
        'total_noa',
        'total_osmdlc',
        'total_osmgnc',
        'total_os_total',
        'total_mdlawal',
        'total_tgkmdl',
        'total_tgkmgn',
        'npf_noa',
        'npf_osmdlc',
        'npf_persen',
        'total_ppak',
        'ppka_persen',
        'total_jatuhtempo',
        'avg_kolek',
        'avg_rate',
        'avg_plafond',
        'sum_kolek',
        'sum_rate',
    ];

    protected $casts = [
        'total_noa' => 'integer',
        'total_osmdlc' => 'decimal:2',
        'total_osmgnc' => 'decimal:2',
        'total_os_total' => 'decimal:2',
        'total_mdlawal' => 'decimal:2',
        'total_tgkmdl' => 'decimal:2',
        'total_tgkmgn' => 'decimal:2',
        'npf_noa' => 'integer',
        'npf_osmdlc' => 'decimal:2',
        'npf_persen' => 'decimal:2',
        'total_ppak' => 'decimal:2',
        'ppka_persen' => 'decimal:2',
        'total_jatuhtempo' => 'integer',
        'avg_kolek' => 'decimal:2',
        'avg_rate' => 'decimal:4',
        'avg_plafond' => 'decimal:2',
        'sum_kolek' => 'decimal:2',
        'sum_rate' => 'decimal:4',
    ];

    /**
     * Scope: Get only overall snapshots (ALL dimensions)
     */
    public function scopeOverall($query)
    {
        return $query->where('kdseg', 'ALL')
            ->where('kdaoh', 'ALL')
            ->where('kdprd', 'ALL')
            ->where('kdloc', 'ALL')
            ->where('kdwil', 'ALL')
            ->where('colbarU', 'ALL');
    }

    /**
     * Scope: Get snapshots for specific period
     */
    public function scopeForPeriod($query, string $tahunBulan)
    {
        return $query->where('tahun_bulan', $tahunBulan);
    }

    /**
     * Scope: Get snapshots in date range
     */
    public function scopeInRange($query, string $start, string $end)
    {
        return $query->whereBetween('tahun_bulan', [$start, $end]);
    }

    /**
     * Scope: Get only Kol 1 (Lancar)
     */
    public function scopeKol1($query)
    {
        return $query->where('colbarU', '1');
    }

    /**
     * Scope: Get only NPF accounts (Kol 3, 4, 5)
     */
    public function scopeNpf($query)
    {
        return $query->whereIn('colbarU', ['3', '4', '5']);
    }

    /**
     * Get formatted tahun_bulan label
     */
    public function getPeriodeLabelAttribute(): string
    {
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        if (preg_match('/^(\d{4})-(\d{2})$/', $this->tahun_bulan, $matches)) {
            $year = $matches[1];
            $month = $matches[2];

            return $monthNames[$month].' '.$year;
        }

        return $this->tahun_bulan;
    }

    /**
     * Get Kolektibilitas label
     */
    public function getKolekLabelAttribute(): string
    {
        return match ($this->colbarU) {
            '1' => 'Lancar',
            '2' => 'DPK',
            '3' => 'Kurang Lancar',
            '4' => 'Diragukan',
            '5' => 'Macet',
            default => 'Semua',
        };
    }
}
