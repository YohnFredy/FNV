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
        Schema::table('user_activations', function (Blueprint $table) {
            $table->string('activation_type', 30)->default('none')->after('is_active');
            $table->foreignId('activated_by_admin_id')->nullable()->after('activation_type')->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable()->after('activated_by_admin_id');
            $table->timestamp('first_activated_at')->nullable()->after('admin_notes');
            $table->boolean('has_used_grace_period')->default(false)->after('first_activated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_activations', function (Blueprint $table) {
            $table->dropForeign(['activated_by_admin_id']);
            $table->dropColumn([
                'activation_type',
                'activated_by_admin_id',
                'admin_notes',
                'first_activated_at',
                'has_used_grace_period',
            ]);
        });
    }
};
