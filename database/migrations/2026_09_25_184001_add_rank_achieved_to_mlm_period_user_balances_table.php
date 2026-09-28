<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para añadir el rango alcanzado por el afiliado en el periodo.
     */
    public function up(): void
    {
        Schema::table('mlm_period_user_balances', function (Blueprint $table) {
            $table->string('rank_achieved', 60)->default('Afiliado')->after('activation_type');
            $table->unsignedInteger('rank_level')->default(0)->after('rank_achieved');
            $table->index(['period_id', 'rank_level'], 'idx_period_rank_level');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mlm_period_user_balances', function (Blueprint $table) {
            $table->dropIndex('idx_period_rank_level');
            $table->dropColumn(['rank_achieved', 'rank_level']);
        });
    }
};
