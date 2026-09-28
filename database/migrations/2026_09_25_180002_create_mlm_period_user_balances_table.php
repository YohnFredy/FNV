<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para las instantáneas y balances mensuales de usuarios.
     * Almacena una sola fila inmutable por usuario por periodo para auditoría y reportes O(1).
     */
    public function up(): void
    {
        Schema::create('mlm_period_user_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('period_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Puntos acumulados en el ciclo mensual
            $table->decimal('personal_points', 12, 2)->default(0);
            $table->decimal('binary_left_points', 12, 2)->default(0);
            $table->decimal('binary_right_points', 12, 2)->default(0);
            $table->decimal('binary_points_matched', 12, 2)->default(0);
            $table->decimal('unilevel_group_points', 12, 2)->default(0);

            // Estado de actividad en el periodo
            $table->boolean('is_active')->default(false);
            $table->string('activation_type', 30)->default('none'); // 'points', 'admin', 'grace_period', 'none'

            // Comisiones financieras calculadas
            $table->decimal('commission_binary', 14, 2)->default(0);
            $table->decimal('commission_unilevel', 14, 2)->default(0);
            $table->decimal('total_commission', 14, 2)->default(0);

            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            // Relaciones e Índices de Alto Rendimiento
            $table->foreign('period_id')->references('id')->on('mlm_periods')->cascadeOnDelete();
            $table->unique(['user_id', 'period_id'], 'uq_user_period_balance');
            $table->index(['period_id', 'is_active'], 'idx_period_is_active');
            $table->index(['period_id', 'settled_at'], 'idx_period_settled_at');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mlm_period_user_balances');
    }
};
