<?php

declare(strict_types=1);

namespace App\Models\Mci;

use App\Models\Mci\Traits\HasDynamicConnection;
use App\Models\Mci\Traits\HasMciCasts;
use App\Models\Mci\Traits\ReadOnlyModel;
use Illuminate\Database\Eloquent\Model;

/**
 * MciBaseModel
 * --------------------------------------------------------------------------
 * Base model abstract untuk SEMUA 510 model yang merepresentasikan tabel
 * di database SQL Server MCI (MCI_MAR26_01042026 / MCI_APR26_xxxx / dst).
 *
 * Semua model MCI HARUS extend class ini agar:
 *   1. Otomatis pakai koneksi `dashboard_data` (SQL Server, dinamis per bulan).
 *   2. Bersifat READ-ONLY (tidak boleh insert/update/delete).
 *   3. Bisa di-switch ke database history bulan sebelumnya saat runtime.
 *   4. Otomatis handle casting tipe data SQL Server yang sering varchar
 *      padahal semestinya tanggal/numeric.
 *
 * OPTIMIZATION RULES APPLIED:
 *  - RULE #8: Lazy Loading OFF — semua relasi HARUS via with()
 *
 * PENTING:
 *  - JANGAN override `$connection` di child model. Biarkan default `dashboard_data`.
 *  - JANGAN tambahkan `$timestamps = true`. Tabel MCI tidak punya created_at/updated_at.
 *  - Primary key default non-incrementing & string karena hampir semua tabel MCI
 *    pakai composite key / string key (seperti nocif, noacc, dll).
 */
abstract class MciBaseModel extends Model
{
    use HasDynamicConnection;
    use HasMciCasts;
    use ReadOnlyModel;

    /**
     * Koneksi default: SQL Server MCI (akan dinamis saat runtime
     * melalui MciConnectionService).
     */
    protected $connection = 'dashboard_data';

    /**
     * Tabel MCI legacy tidak punya kolom created_at / updated_at.
     */
    public $timestamps = false;

    /**
     * Default: tidak auto-increment (hampir semua tabel MCI pakai string PK
     * atau composite key). Override di model child jika berbeda.
     */
    public $incrementing = false;

    /**
     * Default key type string. Override di model child jika integer.
     */
    protected $keyType = 'string';

    /**
     * RULE #8: Matikan lazy loading — semua relasi harus eksplisit via with().
     * Mencegah N+1 query yang tidak disadari.
     */
    protected bool $lazyLoading = false;

    /**
     * Guarded kosong + fillable kosong = tabel MCI tidak menerima mass assignment
     * karena memang read-only. Child model boleh override $fillable untuk
     * dokumentasi/IDE helper saja.
     */
    protected $guarded = [];
}
