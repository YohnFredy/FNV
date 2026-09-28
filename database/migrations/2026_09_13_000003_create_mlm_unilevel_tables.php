<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para el sistema escalonado (Unilevel) MLM.
     */
    public function up(): void
    {
        // 1. Tabla de nodos del árbol escalonado / patrocinio directo
        Schema::create('unilevel_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // Patrocinador directo (Sponsor). Es NULL únicamente para el Nodo Maestro Raíz.
            $table->foreignId('sponsor_id')->nullable()->constrained('users')->nullOnDelete();

            // Nivel de profundidad genealógica (Raíz = 1, Directos del Raíz = 2, etc.)
            $table->unsignedInteger('level')->default(1);

            // Ruta materializada para jerarquía directa (ej: '/1/4/9/21/')
            $table->string('path', 500)->nullable();

            $table->timestamps();

            $table->index('sponsor_id');
            $table->index('path');
        });

        // 2. Tabla de Cierre (Closure Table) para el árbol escalonado.
        // Permite consultar toda la red descendente de un usuario en un solo query sin recursión.
        Schema::create('unilevel_paths', function (Blueprint $table) {
            $table->foreignId('ancestor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('descendant_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('depth'); // Distancia en generaciones (0 = mismo usuario, 1 = directo, 2 = segundo nivel...)

            $table->primary(['ancestor_id', 'descendant_id']);
            $table->index('descendant_id');
            $table->index(['ancestor_id', 'depth'], 'idx_unilevel_paths_ancestor_depth');
        });

        // 3. Resumen y contadores de la red escalonada
        Schema::create('unilevel_summaries', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();

            // Cantidad de patrocinados directos (frontales en nivel 1)
            $table->unsignedBigInteger('direct_sponsors_count')->default(0);

            // Cantidad total de afiliados en toda su organización descendente (todos los niveles)
            $table->unsignedBigInteger('total_network_members')->default(0);

            // Volumen de puntos personales
            $table->decimal('personal_points', 18, 4)->default(0);

            // Volumen de puntos grupales de toda la organización
            $table->decimal('group_points', 18, 4)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones del sistema escalonado.
     */
    public function down(): void
    {
        Schema::dropIfExists('unilevel_summaries');
        Schema::dropIfExists('unilevel_paths');
        Schema::dropIfExists('unilevel_nodes');
    }
};
