<?php

namespace App\Models\Mci\Traits;

/**
 * HasMciCasts
 * --------------------------------------------------------------------------
 * Helper casting untuk database MCI (SQL Server). Kadang tabel MCI menyimpan:
 *   - Tanggal sebagai varchar(8) dengan format YYYYMMDD
 *   - Tanggal + jam sebagai varchar(14) dengan format YYYYMMDDHHMMSS
 *   - Numeric(18,5) untuk uang → lebih aman dicast ke string atau float
 *
 * Trait ini memberikan helper method untuk parsing / formatting kolom
 * legacy tersebut tanpa harus mengubah struktur database.
 *
 * CATATAN:
 *   - Trait ini TIDAK auto-register cast di property $casts (karena setiap
 *     tabel punya kolom berbeda). Child model wajib set $casts sendiri
 *     sesuai kolom yang ada di database.
 *   - Method helper di sini dipakai manual di accessor model bila perlu.
 */
trait HasMciCasts
{
    /**
     * Konversi string "YYYYMMDD" → Carbon.
     * Return null kalau kosong / tidak valid.
     */
    public function parseMciDate(?string $value): ?\Carbon\Carbon
    {
        if ($value === null || trim($value) === '' || strlen($value) < 8) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('Ymd', substr($value, 0, 8));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Konversi string "YYYYMMDDHHMMSS" → Carbon.
     */
    public function parseMciDatetime(?string $value): ?\Carbon\Carbon
    {
        if ($value === null || trim($value) === '' || strlen($value) < 14) {
            return $this->parseMciDate($value);
        }

        try {
            return \Carbon\Carbon::createFromFormat('YmdHis', substr($value, 0, 14));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Format Carbon → string YYYYMMDD (untuk where clause di query MCI).
     */
    public static function formatMciDate(\DateTimeInterface $date): string
    {
        return $date->format('Ymd');
    }

    /**
     * Format Carbon → string YYYYMMDDHHMMSS.
     */
    public static function formatMciDatetime(\DateTimeInterface $date): string
    {
        return $date->format('YmdHis');
    }
}
