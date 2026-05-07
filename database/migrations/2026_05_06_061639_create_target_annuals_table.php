<?php

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
        Schema::create('target_annuals', function (Blueprint $table) {
            $table->id();
            $table->string('dimension_type'); // 'ao', 'cabang', 'produk'
            $table->string('dimension_id');   // kdao, kdloc, kdprd
            $table->integer('target_year');
            $table->decimal('total_nominal', 20, 2);
            $table->timestamps();

            $table->unique(['dimension_type', 'dimension_id', 'target_year'], 'target_annual_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_annuals');
    }
};
