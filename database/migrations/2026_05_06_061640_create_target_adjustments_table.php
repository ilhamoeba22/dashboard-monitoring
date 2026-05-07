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
        Schema::create('target_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('dimension_type')->default('ao');
            $table->string('from_dimension_id');
            $table->string('to_dimension_id');
            $table->integer('effective_month');
            $table->integer('target_year');
            $table->decimal('nominal_transferred', 20, 2);
            $table->text('reason')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_adjustments');
    }
};
