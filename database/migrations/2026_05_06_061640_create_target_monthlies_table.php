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
        Schema::create('target_monthlies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annual_target_id')
                ->constrained('target_annuals')
                ->onDelete('cascade');
            $table->integer('month'); // 1-12
            $table->decimal('nominal_target', 20, 2);
            $table->timestamps();

            $table->unique(['annual_target_id', 'month'], 'target_monthly_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_monthlies');
    }
};
