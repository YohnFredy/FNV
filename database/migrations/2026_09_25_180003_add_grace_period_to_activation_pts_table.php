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
        Schema::table('activation_pts', function (Blueprint $table) {
            $table->boolean('first_activation_grace_period_enabled')->default(true)->after('min_pts_monthly');
            $table->unsignedTinyInteger('grace_period_months')->default(1)->after('first_activation_grace_period_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activation_pts', function (Blueprint $table) {
            $table->dropColumn([
                'first_activation_grace_period_enabled',
                'grace_period_months',
            ]);
        });
    }
};
