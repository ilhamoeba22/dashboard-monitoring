<?php

declare(strict_types=1);

namespace App\Models\Mci;

use Illuminate\Database\Eloquent\Model;

class DailyMetricsHistory extends Model
{
    /**
     * Pastikan tabel berada di koneksi MySQL (bukan SQL Server)
     */
    protected $connection = 'mysql';

    protected $fillable = [
        'tgl_snapshot',
        'financing_os',
        'financing_npf',
        'financing_noa',
        'saving_saldo',
        'saving_noa',
        'deposito_saldo',
        'deposito_baghas',
        'deposito_noa',
        'source_database',
    ];

    protected $casts = [
        'tgl_snapshot' => 'date',
        'financing_os' => 'float',
        'financing_npf' => 'float',
        'financing_noa' => 'integer',
        'saving_saldo' => 'float',
        'saving_noa' => 'integer',
        'deposito_saldo' => 'float',
        'deposito_baghas' => 'float',
        'deposito_noa' => 'integer',
    ];
}
