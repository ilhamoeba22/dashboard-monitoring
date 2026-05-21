<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

interface FinancingRepositoryInterface
{
    /**
     * Dapatkan daftar data nominatif nasabah pembiayaan.
     * Menggunakan pagination untuk mendukung lompatan halaman di UI.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getNominative(array $filters = [], int $perPage = 50): Paginator|CursorPaginator;

    /**
     * Dapatkan daftar nama AO yang unik.
     */
    public function getUniqueAos(): Collection;

    /**
     * Dapatkan daftar cabang yang unik.
     */
    public function getUniqueCabangs(): Collection; public function getUniqueSegmens(): Collection;

    /**
     * Dapatkan detail jadwal angsuran berdasarkan nomor kontrak.
     *
     * @return array<string, mixed>
     */
    public function getDetailAngsuran(string $nokontrak): array;

    /**
     * Dapatkan data rekapitulasi pembiayaan secara dinamis.
     *
     * @param  string  $groupBy  (cabang|wilayah|ao|produk|segmen|sekon|kolektibilitas)
     */
    public function getRekapitulasi(string $groupBy): Collection;

    /**
     * Dapatkan data rekapitulasi master dengan breakdown kolektibilitas Kol1-Kol5 lengkap.
     * Single-hit query menggunakan Conditional Aggregation.
     * Mendukung 6 dimensi: cabang|wilayah|ao|produk|segmen|sekon.
     *
     * @param  string  $groupBy  Dimensi analisis
     * @param  string  $cabang   Filter kode cabang (opsional, kosong = semua)
     * @return array{rows: Collection, totals: array<string,mixed>, meta: array<string,mixed>}
     */
    public function getRekapMaster(string $groupBy = 'cabang', string $cabang = ''): array;

    /**
     * Analisis Kualitas Aset & Risiko (Aging, Risk Concentration, Coverage).
     * Single-hit analytics untuk dashboard Quality & Risk.
     *
     * @param  string  $groupBy  Dimensi analisis (cabang|produk|ao)
     * @param  string  $cabang   Filter kode cabang (opsional)
     * @return array<string, mixed>
     */
    public function getQualityAnalytics(string $groupBy = 'cabang', string $cabang = '', int $tahun = 0, int $bulan = 0, string $segmen = ''): array;

    /**
     * Dapatkan daftar pembiayaan yang sudah atau akan jatuh tempo.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getJatuhTempo(array $filters = [], int $perPage = 50): Paginator|CursorPaginator;
}
