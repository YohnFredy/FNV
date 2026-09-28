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
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'totalPts') && ! Schema::hasColumn('order_items', 'total_pts')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->renameColumn('totalPts', 'total_pts');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'total_pts') && ! Schema::hasColumn('order_items', 'totalPts')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->renameColumn('total_pts', 'totalPts');
            });
        }
    }
};
