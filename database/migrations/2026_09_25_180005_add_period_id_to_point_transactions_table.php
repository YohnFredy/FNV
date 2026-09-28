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
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->unsignedSmallInteger('period_id')->nullable()->after('user_id');
            $table->index(['period_id', 'user_id'], 'idx_pt_period_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_pt_period_user');
            $table->dropColumn('period_id');
        });
    }
};
