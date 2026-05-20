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
        Schema::create('manual_ppap_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('nokontrak', 50)->unique();
            $table->decimal('nominal_ppap', 18, 2);
            $table->text('alasan')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_ppap_adjustments');
    }
};
