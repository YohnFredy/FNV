<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para los periodos/ciclos mensuales MLM.
     */
    public function up(): void
    {
        Schema::create('mlm_periods', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 60); // ej: "Septiembre 2026"
            $table->string('code', 10)->unique(); // ej: "2026-09"
            $table->date('starts_at');
            $table->date('ends_at');
            $table->decimal('min_activation_pts', 10, 2)->default(1.80);
            $table->enum('status', ['upcoming', 'active', 'closing', 'settled'])->default('upcoming');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at'], 'idx_mlm_periods_status_dates');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mlm_periods');
    }
};
