<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel untuk menyimpan snapshot data pembiayaan per bulan.
     * Digunakan untuk trend analysis dan historical comparison.
     *
     * Data diambil dari SQL Server CBS MCI (TOFLMB) per database bulanan.
     */
    public function up(): void
    {
        Schema::create('financing_monthly_snapshots', function (Blueprint $table) {
            $table->id();

            // Period identifier (format: YYYY-MM, contoh: "2026-03")
            $table->string('tahun_bulan', 7);

            // Source database identifier
            $table->string('source_db', 50)->nullable();

            // Grouping dimensions (ALL = aggregate semua)
            $table->string('kdseg', 20)->default('ALL');
            $table->string('kdaoh', 20)->default('ALL');
            $table->string('kdprd', 20)->default('ALL');
            $table->string('kdloc', 20)->default('ALL');
            $table->string('kdwil', 20)->default('ALL');
            $table->string('colbarU', 5)->default('ALL'); // Kolektibilitas: 1-5 atau ALL

            // === AGGREGATE METRICS ===

            // Count metrics
            $table->unsignedInteger('total_noa')->default(0);           // Number of Accounts
            $table->unsignedInteger('total_noa_lancar')->default(0);   // Kol 1
            $table->unsignedInteger('total_noa_dpk')->default(0);     // Kol 2
            $table->unsignedInteger('total_noa_kurang_lancar')->default(0); // Kol 3
            $table->unsignedInteger('total_noa_diragukan')->default(0); // Kol 4
            $table->unsignedInteger('total_noa_macat')->default(0);    // Kol 5

            // Outstanding metrics (dalam Rupiah)
            $table->decimal('total_osmdlc', 20, 2)->default(0);       // Outstanding Modal
            $table->decimal('total_osmgnc', 20, 2)->default(0);        // Outstanding Margin
            $table->decimal('total_os_total', 20, 2)->default(0);       // Total O/S (mdlc + mgnc)
            $table->decimal('total_mdlawal', 20, 2)->default(0);       // Modal Awal/Pokok Pinjaman
            $table->decimal('total_tgkmdl', 20, 2)->default(0);         // Tunggakan Modal
            $table->decimal('total_tgkmgn', 20, 2)->default(0);        // Tunggakan Margin

            // NPF metrics
            $table->unsignedInteger('npf_noa')->default(0);           // NPF Count (Kol 3,4,5)
            $table->decimal('npf_osmdlc', 20, 2)->default(0);          // NPF Outstanding
            $table->decimal('npf_persen', 5, 2)->default(0);            // NPF Ratio %

            // PPKA metrics
            $table->decimal('total_ppak', 20, 2)->default(0);          // Penyisihan Aktiva PNBN
            $table->decimal('ppka_persen', 5, 2)->default(0);          // PPKA Ratio %

            // Jatuh tempo
            $table->unsignedInteger('total_jatuhtempo')->default(0);

            // Average metrics
            $table->decimal('avg_kolek', 4, 2)->default(0);            // Avg Kolektibilitas (1.0 - 5.0)
            $table->decimal('avg_rate', 8, 4)->default(0);            // Avg Suku Bunga
            $table->decimal('avg_plafond', 18, 2)->default(0);        // Avg Plafond

            // Totals untuk rata-rata
            $table->decimal('sum_kolek', 8, 2)->default(0);           // Sum kolektibilitas (untuk avg)
            $table->decimal('sum_rate', 12, 4)->default(0);          // Sum rate (untuk avg)

            // Timestamps
            $table->timestamps();

            // === INDEXES ===

            // Primary query pattern: WHERE tahun_bulan = ? AND kdseg = ?
            $table->index(['tahun_bulan', 'kdseg', 'kdaoh', 'kdprd', 'kdloc'], 'idx_snapshot_grouping');

            // Unique constraint: satu record per combination
            $table->unique(
                ['tahun_bulan', 'kdseg', 'kdaoh', 'kdprd', 'kdloc', 'kdwil', 'colbarU', 'source_db'],
                'unique_snapshot_combo',
            );

            // For getting all records of a specific month
            $table->index('tahun_bulan', 'idx_tahun_bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financing_monthly_snapshots');
    }
};
