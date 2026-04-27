<?php

declare(strict_types=1);

namespace App\Repositories\Mci;

use App\Repositories\Interfaces\ReportingRepositoryInterface;
use App\Services\Mci\MciBaseRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportingRepository extends MciBaseRepository implements ReportingRepositoryInterface
{
    protected function getTableName(): string
    {
        return 'SBBOT'; // Tabel GL/Neraca dasar
    }

    public function getReport(string $jenisLaporan, array $filters = []): array
    {
        // Cache laporan berat selama 5 menit (300 detik)
        $cacheKey = "reporting_{$jenisLaporan}_" . md5(json_encode($filters));

        return Cache::remember($cacheKey, 300, function () use ($jenisLaporan, $filters) {
            
            // Dispatch ke metode spesifik berdasarkan jenis laporan
            // (DRY Principle: Menghindari pembuatan puluhan controller)
            return match ($jenisLaporan) {
                'neraca'    => $this->generateNeraca($filters),
                'labarugi'  => $this->generateLabaRugi($filters),
                'aruskas'   => $this->generateArusKas($filters),
                'jamkrida'  => $this->generateJamkrida($filters),
                default     => throw new \InvalidArgumentException("Jenis laporan {$jenisLaporan} belum didukung.")
            };
        });
    }

    private function generateNeraca(array $filters): array
    {
        // Mocking/Stub: Di real case, ini akan men-query tabel SBBOT / GL Neraca
        // Sesuai rules: query berat di-push ke Repository
        return [
            'judul' => 'Laporan Neraca',
            'periode' => $filters['periode'] ?? date('Y-m'),
            'aktiva' => [
                'kas' => 1500000000,
                'pembiayaan' => 45000000000,
            ],
            'pasiva' => [
                'tabungan' => 20000000000,
                'deposito' => 15000000000,
                'modal' => 11500000000,
            ],
            'total_aktiva' => 46500000000,
            'total_pasiva' => 46500000000,
        ];
    }

    private function generateLabaRugi(array $filters): array
    {
        return [
            'judul' => 'Laporan Laba Rugi',
            'periode' => $filters['periode'] ?? date('Y-m'),
            'pendapatan' => [
                'margin_pembiayaan' => 1200000000,
                'fee_based' => 50000000,
            ],
            'beban' => [
                'bagi_hasil' => 400000000,
                'operasional' => 300000000,
            ],
            'laba_bersih' => 550000000,
        ];
    }

    private function generateArusKas(array $filters): array
    {
        return [
            'judul' => 'Laporan Arus Kas',
            'periode' => $filters['periode'] ?? date('Y-m'),
            'arus_kas_operasional' => 250000000,
            'arus_kas_investasi' => -50000000,
            'arus_kas_pendanaan' => 100000000,
            'kenaikan_kas' => 300000000,
        ];
    }

    private function generateJamkrida(array $filters): array
    {
        return [
            'judul' => 'Laporan Penjaminan Jamkrida',
            'periode' => $filters['periode'] ?? date('Y-m'),
            'total_dijamin' => 8500000000,
            'jumlah_nasabah' => 125,
        ];
    }
}
