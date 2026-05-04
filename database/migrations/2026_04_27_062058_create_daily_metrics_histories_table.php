<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_metrics_histories', function (Blueprint $table) {
            $table->id();
            // Tanggal snapshot (Unik)
            $table->date('tgl_snapshot')->unique()->index();

            // Financing (Pembiayaan)
            $table->decimal('financing_os', 20, 2)->default(0);
            $table->decimal('financing_npf', 20, 2)->default(0);
            $table->integer('financing_noa')->default(0);

            // Saving (Tabungan)
            $table->decimal('saving_saldo', 20, 2)->default(0);
            $table->integer('saving_noa')->default(0);

            // Deposito
            $table->decimal('deposito_saldo', 20, 2)->default(0);
            $table->decimal('deposito_baghas', 20, 2)->default(0);
            $table->integer('deposito_noa')->default(0);

            // Metadata
            $table->string('source_database')->nullable()->comment('Nama database MCI asal snapshot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_metrics_histories');
    }
};
