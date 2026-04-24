<?php

namespace App\Models\Mci\Traits;

use RuntimeException;

/**
 * ReadOnlyModel
 * --------------------------------------------------------------------------
 * Memastikan semua model yang memakai trait ini TIDAK BISA menulis ke
 * database (insert / update / delete). Database MCI adalah snapshot yang
 * di-generate oleh sistem lain; aplikasi dashboard ini hanya konsumen.
 *
 * Jika ada kode yang mencoba memanggil save/update/delete akan dilempar
 * RuntimeException supaya cepat ketahuan saat development.
 */
trait ReadOnlyModel
{
    /**
     * Boot hook untuk block semua event penulisan.
     */
    public static function bootReadOnlyModel(): void
    {
        static::creating(fn () => self::denyWrite('creating'));
        static::updating(fn () => self::denyWrite('updating'));
        static::deleting(fn () => self::denyWrite('deleting'));
        static::saving(fn () => self::denyWrite('saving'));
    }

    /**
     * Lempar exception dengan pesan jelas.
     */
    protected static function denyWrite(string $event): void
    {
        throw new RuntimeException(sprintf(
            '[MCI Read-Only] Operasi "%s" ditolak pada model %s. Database MCI bersifat read-only.',
            $event,
            static::class
        ));
    }

    /**
     * Override save untuk jaga-jaga (event saving di atas seharusnya sudah
     * cukup, tapi ini double-safety).
     */
    public function save(array $options = [])
    {
        self::denyWrite('save');

        return false; // tidak akan tercapai
    }

    /**
     * Override delete untuk jaga-jaga.
     */
    public function delete()
    {
        self::denyWrite('delete');

        return false;
    }
}
