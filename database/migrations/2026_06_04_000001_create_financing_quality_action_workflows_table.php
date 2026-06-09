<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financing_quality_action_workflows', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->string('action_key', 160);
            $table->string('nokontrak', 50)->nullable();
            $table->string('nama', 180)->nullable();
            $table->string('source', 60)->nullable();
            $table->json('signals')->nullable();
            $table->string('severity', 20)->nullable();
            $table->unsignedSmallInteger('score')->default(0);
            $table->decimal('exposure', 20, 2)->default(0);
            $table->string('status', 30)->default('open');
            $table->string('owner', 120)->nullable();
            $table->date('due_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('reviewed_by', 120)->nullable();
            $table->timestamps();

            $table->unique(['period_year', 'period_month', 'action_key'], 'fqaw_period_action_unique');
            $table->index(['period_year', 'period_month', 'status'], 'fqaw_period_status_idx');
            $table->index('nokontrak', 'fqaw_nokontrak_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financing_quality_action_workflows');
    }
};
