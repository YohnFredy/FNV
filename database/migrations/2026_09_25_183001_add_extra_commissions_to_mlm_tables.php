<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para añadir los tipos de comisiones:
     * - Socio Estratégico
     * - Rango
     * - Otros
     * tanto a los balances individuales de usuario como a los totales del periodo.
     */
    public function up(): void
    {
        // 1. Ampliar mlm_period_user_balances
        Schema::table('mlm_period_user_balances', function (Blueprint $table) {
            $table->decimal('commission_strategic_partner', 14, 2)->default(0)->after('commission_unilevel');
            $table->decimal('commission_rank', 14, 2)->default(0)->after('commission_strategic_partner');
            $table->decimal('commission_other', 14, 2)->default(0)->after('commission_rank');
            $table->text('commission_notes')->nullable()->after('total_commission');
        });

        // 2. Ampliar mlm_periods con totales consolidados del corte
        Schema::table('mlm_periods', function (Blueprint $table) {
            $table->decimal('total_commission_binary', 16, 2)->default(0)->after('min_activation_pts');
            $table->decimal('total_commission_unilevel', 16, 2)->default(0)->after('total_commission_binary');
            $table->decimal('total_commission_strategic_partner', 16, 2)->default(0)->after('total_commission_unilevel');
            $table->decimal('total_commission_rank', 16, 2)->default(0)->after('total_commission_strategic_partner');
            $table->decimal('total_commission_other', 16, 2)->default(0)->after('total_commission_rank');
            $table->decimal('total_payout', 16, 2)->default(0)->after('total_commission_other');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mlm_period_user_balances', function (Blueprint $table) {
            $table->dropColumn([
                'commission_strategic_partner',
                'commission_rank',
                'commission_other',
                'commission_notes',
            ]);
        });

        Schema::table('mlm_periods', function (Blueprint $table) {
            $table->dropColumn([
                'total_commission_binary',
                'total_commission_unilevel',
                'total_commission_strategic_partner',
                'total_commission_rank',
                'total_commission_other',
                'total_payout',
            ]);
        });
    }
};
