<?php

declare(strict_types=1);

namespace App\Models\Mci\Traits;

/**
 * HasDynamicConnection
 * --------------------------------------------------------------------------
 * Trait untuk memungkinkan model MCI di-switch koneksi database-nya
 * secara runtime (antara database bulan ini / bulan-bulan sebelumnya).
 *
 * Cara pakai:
 *   - Default: pakai koneksi `dashboard_data` yang poin ke database terbaru.
 *   - Untuk query ke database history:
 *        App\Services\Mci\MciConnectionService::using(
 *            'MCI_FEB26_01032026',
 *            fn() => App\Models\Mci\Cif\Mcif::count()
 *        );
 *
 * Mekanisme override:
 *   - `MciConnectionService::useDatabase($dbName)` akan menyimpan nama db
 *     yang aktif di container (singleton). `getConnectionName()` di bawah
 *     akan membaca override tersebut saat eksekusi query.
 */
trait HasDynamicConnection
{
    /**
     * Override connection name resolver.
     * Jika ada override runtime (dari MciConnectionService), pakai itu.
     * Jika tidak, pakai $this->connection default.
     */
    public function getConnectionName(): ?string
    {
        if (app()->bound('mci.connection')) {
            $runtime = app('mci.connection');
            if (is_string($runtime) && $runtime !== '') {
                return $runtime;
            }
        }

        return $this->connection;
    }
}
