<?php

use App\Enums\MlmStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mlm_status', 30)->default(MlmStatus::WAITING_ROOM->value)->after('password');
            $table->foreignId('sponsor_id')->nullable()->after('mlm_status')->constrained('users')->nullOnDelete();
            $table->char('preferred_leg', 1)->nullable()->after('sponsor_id');
            $table->timestamp('placed_at')->nullable()->after('preferred_leg');

            $table->index(['mlm_status', 'sponsor_id']);
        });

        // Actualizar usuarios existentes que ya poseen nodo en el árbol binario a 'active_affiliate'
        if (Schema::hasTable('binary_nodes')) {
            $placedUserIds = DB::table('binary_nodes')->pluck('user_id');
            if ($placedUserIds->isNotEmpty()) {
                DB::table('users')
                    ->whereIn('id', $placedUserIds)
                    ->update([
                        'mlm_status' => MlmStatus::ACTIVE_AFFILIATE->value,
                        'placed_at' => now(),
                    ]);
            }
        }

        // Sincronizar sponsor_id de usuarios existentes que ya tengan nodo en el árbol unilevel
        if (Schema::hasTable('unilevel_nodes')) {
            $unilevelNodes = DB::table('unilevel_nodes')->whereNotNull('sponsor_id')->get(['user_id', 'sponsor_id']);
            foreach ($unilevelNodes as $node) {
                DB::table('users')
                    ->where('id', $node->user_id)
                    ->update(['sponsor_id' => $node->sponsor_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sponsor_id']);
            $table->dropIndex(['mlm_status', 'sponsor_id']);
            $table->dropColumn(['mlm_status', 'sponsor_id', 'preferred_leg', 'placed_at']);
        });
    }
};
